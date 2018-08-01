<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);
if(strlen($arParams["SITE_LIST"])<=0)
	$arParams["SITE_LIST"] = "";
   // dg
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_xml_collector.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");

$arParams["USE_ZIP"] = "Y";

@set_time_limit(0);

$bUSER_HAVE_ACCESS = false;
if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();
if(!$bDesignMode)
{
	$APPLICATION->RestartBuffer();
	header("Pragma: no-cache");
}

ob_start();

$curPage = substr($APPLICATION -> GetCurPage(), 0, 22);
if($_GET["mode"] == "checkauth"  && $USER->IsAuthorized())
{
	echo "success\n";
	echo session_name()."\n";
	echo session_id() ."\n";

	COption::SetOptionString("sale", "export_session_name_".$curPage, session_name());
	COption::SetOptionString("sale", "export_session_id_".$curPage, session_id());
}
elseif(!$USER->IsAuthorized())
{
	echo "failure\n : 1",GetMessage("CC_BSC1_ERROR_AUTHORIZE");
}
elseif(!$bUSER_HAVE_ACCESS)
{
	echo "failure\n : 2",GetMessage("CC_BSC1_PERMISSION_DENIED");
}
elseif(!(CModule::IncludeModule('sale') && CModule::IncludeModule('catalog')))
{
	echo "failure\n :3",GetMessage("CC_BSC1_ERROR_MODULE");
}
else
{


   if($_GET["mode"]=="init")
	{
		$DIR_NAME = "/".COption::GetOptionString("main", "upload_dir")."/1c_exchange/";
		DeleteDirFilesEx($DIR_NAME);
	 	CheckDirPath($_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/");
		if(!is_dir($_SERVER["DOCUMENT_ROOT"].$DIR_NAME))
		{
			echo "failure\n :4",GetMessage("CC_BSC1_ERROR_INIT");
		}
		else
		{
			$ht_name = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/.htaccess";
			if(!file_exists($ht_name))
			{
				$fp = fopen($ht_name, "w");
				if($fp)
				{
					fwrite($fp, "Deny from All");
					fclose($fp);
					@chmod($ht_name, BX_FILE_PERMISSIONS);
				}
			}

			$_SESSION["BX_CML2_EXPORT"]["zip"] = $arParams["USE_ZIP"] && function_exists("zip_open");
			echo "zip=".($_SESSION["BX_CML2_EXPORT"]["zip"]? "yes": "no")."\n";
			echo "file_limit=0\n";
		}
	}
	elseif($_GET["mode"] == "file")
	{
		$DIR_NAME = "/".COption::GetOptionString("main", "upload_dir")."/1c_exchange/";
		$ABS_FILE_NAME = false;
		$WORK_DIR_NAME = false;

		if(isset($_GET["filename"]) && (strlen($_GET["filename"])>0))
		{
			$filename = trim(str_replace("\\", "/", trim($_GET["filename"])), "/");
			$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"].$DIR_NAME, "/".$filename);
			if((strlen($FILE_NAME) > 1) && ($FILE_NAME === "/".$filename))
			{
				$ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME.$FILE_NAME;
				$WORK_DIR_NAME = substr($ABS_FILE_NAME, 0, strrpos($ABS_FILE_NAME, "/")+1);
			}
		}

		if($ABS_FILE_NAME)
		{
			if(function_exists("file_get_contents"))
				$DATA = file_get_contents("php://input");
			elseif(isset($GLOBALS["HTTP_RAW_POST_DATA"]))
				$DATA = &$GLOBALS["HTTP_RAW_POST_DATA"];
				else
					$DATA = false;
			if($DATA !== false)
			{
				CheckDirPath($ABS_FILE_NAME);
				if($fp = fopen($ABS_FILE_NAME, "ab"))
				{
					$result = fwrite($fp, $DATA);
					if($result === (function_exists("mb_strlen")? mb_strlen($DATA, 'latin1'): strlen($DATA)))
					{
						if($_SESSION["BX_CML2_EXPORT"]["zip"])
							$_SESSION["BX_CML2_EXPORT"]["zip"] = $ABS_FILE_NAME;
					}
					else
					{
						echo "failure\n : 5",GetMessage("CC_BSC1_ERROR_FILE_WRITE", array("#FILE_NAME#"=>$FILE_NAME));
					}
					fclose($fp);
				}
				else
				{
					echo "failure\n : 6",GetMessage("CC_BSC1_ERROR_FILE_OPEN", array("#FILE_NAME#"=>$FILE_NAME));
				}
			}
			else
			{
				echo "failure\n : 7",GetMessage("CC_BSC1_ERROR_HTTP_READ");
			}

			if(strlen($_SESSION["BX_CML2_EXPORT"]["zip"])>0)
			{
				$file_name = $_SESSION["BX_CML2_EXPORT"]["zip"];

				if(function_exists("zip_open"))
				{
					$dir_name = substr($file_name, 0, strrpos($file_name, "/")+1);
					if(strlen($dir_name) <= strlen($_SERVER["DOCUMENT_ROOT"]))
						return false;

					$hZip = zip_open($file_name);
					if($hZip)
					{
						while($entry = zip_read($hZip))
						{
							$entry_name = zip_entry_name($entry);
							//Check for directory
							if(zip_entry_filesize($entry))
							{
								$ABS_FILE_NAME = $dir_name.$entry_name;
								$file_name = $dir_name.$entry_name;
								CheckDirPath($file_name);
								$fout = fopen($file_name, "wb");
								if($fout)
								{
									while($data = zip_entry_read($entry, 102400))
									{
										$result = fwrite($fout, $data);
										if($result !== (function_exists("mb_strlen")? mb_strlen($data, 'latin1'): strlen($data)))
											return false;
									}
								}
							}
							zip_entry_close($entry);
						}
						zip_close($hZip);
					}

					echo "success\n";
				}
				else
					echo "error\n".GetMessage("CC_BSC1_UNZIP_ERROR");
			}








		}
	}

	elseif(($_GET["mode"] == "import") &&  isset( $_GET["filename"] ) )//$ABS_FILE_NAME)
	{
	  //  Втягиваем справочники по одному



         $NS = &$_SESSION["BX_CML2_IMPORT"]["NS"];

           //  [-] TODO: запись параметров для файла результат.xml
           // Обработка всех файлов-справчников  в куче


		 if( $_GET["filename"] == "currency.xml" )
		   {
		 	  //   ---------  Тянем курс валют


	           if( ( $NS["LOAD_CURRENCY"] != "DONE" ) && ( $NS["LOAD_CURRENCY"] != "PROGRESS" ) )
	         	 {
		         	$sResult = "";
		         	$NS["LOAD_CURRENCY"] = "PROGRESS";

				    $new_file_name = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/currency.xml";
					$f = fopen($new_file_name, "rb");
					$sResult = fread($f, filesize($new_file_name));
					fclose($f);

					if(strlen($sResult)>0)
					  {
							if( strtoupper(LANG_CHARSET) != "UTF-8")
								$sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

							$objXML = new CDataXML();
							$objXML->LoadString($sResult);
							$arResult = $objXML->GetArray();

					        $objCurr = new CurrencyXMLItems();

					        foreach($arResult["КоммерческаяИнформация"]["#"]["Валюты"] as $value)
					          foreach( $value["#"]["Валюта"] as $v)
					            $objCurr->AddItem($v);

					        if( $objCurr->GetCount() > 0 )
					          if (CModule::IncludeModule("sale") )
					            $objCurr->UpdateCurrRates();

					        UpdateConfirms("Валюты", $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"][0]["#"]);

				      }

                    $NS["LOAD_CURRENCY"] = "DONE";
                    echo "success\n";
                 }
               elseif( $NS["LOAD_CURRENCY"] == "PROGRESS" )
                 echo "progress\n";
               elseif( $NS["LOAD_CURRENCY"] == "DONE" )
                 echo "success\n";

	       }
	     elseif( $_GET["filename"] == "offers.xml" )
	       {
	         if( ( $NS["LOAD_OFFERS"] != "DONE" ) && ( $NS["LOAD_OFFERS"] != "PROGRESS" ) )
	         	 {
		         	$NS["LOAD_OFFERS"] = "PROGRESS";


					$reader = new XMLReader();

					$reader->open($_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/offers.xml");

					$arBrands = GetAllBrandsProperties();

					// TODO [-] Отработка тэга <СодержитТолькоИзменения>

					//  Ставим наличие в 0

					$sql = "UPDATE b_autodoc_items_1c_m SET Presence=0";
					$DB->Query( $sql );



					while ($reader->read()) {
					   switch ($reader->nodeType) {
					       case (XMLREADER::ELEMENT):
					       if ($reader->localName == "ТипЦены")
					       {
					           $cnt++;


				               $node = $reader->expand();
				               $dom = new DomDocument();
				               $n = $dom->importNode($node,true);
				               $dom->appendChild($n);
				               $xp = new DomXpath($dom);

				               $res = $xp->query("/ТипЦены/Код");
				               $code = intval( $res->item(0)->nodeValue );

				               $res = $xp->query("/ТипЦены/Наименование");
				               $caption = trim( $res->item(0)->nodeValue );

				               $res = $xp->query("/ТипЦены/Валюта");
				               $currencyCode = trim( $res->item(0)->nodeValue );

				               $sql = "SELECT COUNT(*) as CNT FROM b_autodoc_wh_price_types_m ";
				               $sql .= " WHERE Code = '".$code."'";

				               $res = $DB->Query( $sql );

				               $arRes = $res->Fetch();

				               if( $arRes["CNT"] > 0  )   // есть в базе - обновляем
				                 {
					                $sql = "UPDATE b_autodoc_wh_price_types_m";
					                $sql .= " SET Caption = '".$caption."', CurrencyCode = '".$currencyCode."', DATE_UPDATED=Now()";
					                $sql .= " WHERE Code = '".$code."'";
					                $res = $DB->Query( $sql );
					             }
				               else                      // нет в базе - добавляем
				                 {
				                 	$sql = "INSERT INTO b_autodoc_wh_price_types_m";
					                $sql .= " ( Caption, Code, CurrencyCode, DATE_UPDATED ) Values ('".$caption."', '".$code."' , '".$currencyCode."', Now())";
					                $res = $DB->Query( $sql );

				                 }
					       }

					       if ($reader->localName == "Предложение")
					       {
				       	       $node = $reader->expand();
				               $dom = new DomDocument();
				               $n = $dom->importNode($node,true);
				               $dom->appendChild($n);
				               $xp = new DomXpath($dom);

				               $res = $xp->query("/Предложение/Артикул");
				               $itemCode = PrepareICode(trim( $res->item(0)->nodeValue ));

				               $res = $xp->query("/Предложение/Бренд");
				               if( $res->item(0)->nodeValue == "ACURA" ) $res->item(0)->nodeValue = "0A";

				               $brandCode = $arBrands[$res->item(0)->nodeValue];

				               $res = $xp->query("/Предложение/Количество");
				               $quantity = intval( $res->item(0)->nodeValue );


				               $res = $xp->query("/Предложение/Цены/Цена/ИдТипаЦены");
				               $res2 = $xp->query("/Предложение/Цены/Цена/ЦенаЗаЕдиницу");

				               for( $i = 0; $i < $res->length; $i++ )
				               {
				                 $priceCode = intval( $res->item($i)->nodeValue );
				                 $price =  $res2->item($i)->nodeValue ;

					             $sql = "SELECT * FROM b_autodoc_wh_prices_m";
					             $sql .= " WHERE BrandCode='".$brandCode."' AND ItemCode = '".$itemCode."' AND PriceCode = '".$priceCode."' LIMIT 1";

					             $resSql = $DB->Query( $sql );

					                 if( $brandCode )
					                   {
						                  if(  $arRes = $resSql->Fetch()  )
						                   {
		                                      $sql = "UPDATE b_autodoc_wh_prices_m ";
						                      $sql .= " SET Price = '".$price."'";
						                      $sql .= " WHERE BrandCode='".$brandCode."' AND ItemCode = '". $itemCode ."' AND PriceCode = '".$priceCode."'";

						                      $DB->Query( $sql );
						                   }
						                 else
						                   {
						                      $sql = " INSERT INTO b_autodoc_wh_prices_m ";
						                      $sql .= " (BrandCode,ItemCode,PriceCode,Price,DATE_UPDATED) VALUES ('".$brandCode."','".$itemCode."','".$priceCode."','".$price."',NOW() ) ";


						                      $DB->Query( $sql );
						                   }
                                       }
				               }

				                      //  Обновляем количество

				            if( intval( $quantity ) > 0 )
				              {
				                $sql = "UPDATE b_autodoc_items_1c_m SET Presence = 1, DATE_UPDATED=Now()";
				                $sql .= " WHERE BrandCode='".$brandCode."' AND ItemCode = '".$itemCode."'";
				                $DB->Query( $sql );
				              }
					       }
					   }
					}




     	//	TODO[-]			        UpdateConfirms("Цены", $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"][0]["#"]);



                    $NS["LOAD_OFFERS"] = "DONE";
                    echo "success\n";
                 }
               elseif( $NS["LOAD_OFFERS"] == "PROGRESS" )
                 echo "progress\n";
               elseif( $NS["LOAD_OFFERS"] == "DONE" )
                 echo "success\n";

	       }
	     elseif( $_GET["filename"] == "import.xml" )
	       {
	       	  //  ....
	       	  echo "success\n";

	       }
	     elseif( $_GET["filename"] == "regions.xml" )
	       {
	       	  //  ....
	       	  echo "success\n";

	       }
	     elseif( $_GET["filename"] == "agreements.xml" )
	       {


 	        if( ( $NS["LOAD_AGREEMENTS"] != "DONE" ) && ( $NS["LOAD_AGREEMENTS"] != "PROGRESS" ) )
	          {
            	$NS["LOAD_AGREEMENTS"] = "PROGRESS";

	       	  	$sResult = "";

			    $new_file_name = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/agreements.xml";

				$f = fopen($new_file_name, "rb");
				$sResult = fread($f, filesize($new_file_name));
				fclose($f);

				if(strlen($sResult)>0)
				  {
					if(toUpper(LANG_CHARSET) != "UTF-8")
						$sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

					$objXML = new CDataXML();
					$objXML->LoadString($sResult);
					$arResult = $objXML->GetArray();


			        foreach($arResult["КоммерческаяИнформация"]["#"]["Договора"] as $value)
			          foreach( $value["#"]["Договор"] as $v)
			            {
					       $name = trim( $v["#"]["Наименование"]["0"]["#"] );
					       $code = trim(  $v["#"]["Код"]["0"]["#"] );
					       $userCode = trim( $v["#"]["КодКонтрагента"]["0"]["#"] );
					       $priceColCode = intval(trim( $v["#"]["Цена"]["0"]["#"] ));
					       $creditSum = trim( $v["#"]["СуммаКредита"]["0"]["#"] );
					       $minPercent = trim( $v["#"]["ПроцентПредоплаты"]["0"]["#"] );


					       $currency = trim( $v["#"]["Валюта"]["0"]["#"] );
					         if( $currency == "грн" ) $currency = "UAH";


			               $rsUser = CUser::GetByID( GetUserIDByID_1C( $userCode ) );

					       if ( $arUser = $rsUser->Fetch() )
					         {
					               //  Если юзер есть в базе, то добавляем-обновляем его договора

			                   $sql = "SELECT COUNT(*) as CNT FROM b_autodoc_agreements ";
			                   $sql .= " WHERE Code='".$code."' AND ClientCode='".$userCode."'"  ;

			                   $res = $DB->Query( $sql );
			                   if( $arRes = $res->Fetch()  )
			                     {
			                        if( $arRes["CNT"] > 0 )   //  Есть в базе
			                          {
			                          	 $sql = "UPDATE b_autodoc_agreements ";
			                          	 $sql .= " SET Caption='".$name."',";
			                          	 $sql .= " MinPercent = '".$minPercent."',";
			                          	 $sql .= " CreditSum = '".$creditSum."',";
			                          	 $sql .= " PriceColCode = '".$priceColCode."'";

			                          	 // $sql .= " ";      TODO[-]  остальные параметры договора
			                             $sql .= " WHERE Code='".$code."' AND ClientCode='".$userCode."'"  ;
			                             $res = $DB->Query( $sql );
			                          }
			                        else                           //  Нет в базе
			                          {
			                             $sql = "INSERT INTO b_autodoc_agreements ";
			                             $sql .= " ( Code, ClientCode, CurrencyCode, Caption, MinPercent, CreditSum, PriceColCode ) ";
			                             // $sql .= " ";      TODO[-]  остальные параметры договора

			                             $sql .= " VALUES ( '".$code."','".$userCode."','".$currency."','".$name."','".$minPercent."','".$creditSum."','".$priceColCode."' ) ";
			                             $res = $DB->Query( $sql );

			                          }
			                     }
					         }
			            }
			     }

// TODO[-]                 UpdateConfirms("Договоры", $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"][0]["#"]);

                 $NS["LOAD_AGREEMENTS"] = "DONE";
                 echo "success\n";
              }
            elseif( $NS["LOAD_CURRENCY"] == "PROGRESS" )
              echo "progress\n";
            elseif( $NS["LOAD_CURRENCY"] == "DONE" )
              echo "success\n";

	       }
	    elseif( $_GET["filename"] == "clients.xml" )
	       {
	    $now77 = new DateTime("now");
        $link77 = mysql_connect('localhost:31006', 'bitrix', 'a251d851') or die(mysql_error());//self::$host, self::db, self::login, self::password
        $db_selected = mysql_select_db('bitrix', $link77) or die(mysql_error());
        $query77= "INSERT INTO session_control_table VALUES (NULL, '{$now77->format('Y-m-d H:i:s')}','условие5')";
        mysql_query($query77,$link77);
	           if( ( $NS["LOAD_CLIENTS"] != "DONE" ) && ( $NS["LOAD_CLIENTS"] != "PROGRESS" ) )
	         	 {

						$sResult = "";
						$NS["LOAD_CLIENTS"] = "PROGRESS";

					    $new_file_name = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/clients.xml";

						$f = fopen($new_file_name, "rb");
						$sResult = fread($f, filesize($new_file_name));
						fclose($f);

						if( strlen($sResult) > 0 )
						{
        $query77= "INSERT INTO session_control_table VALUES (NULL, '{$now77->format('Y-m-d H:i:s')}','условие6')";
        mysql_query($query77,$link77);
							if(toUpper(LANG_CHARSET) != "UTF-8")
								$sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

							$objXML = new CDataXML();
							$objXML->LoadString($sResult);
							$arResult = $objXML->GetArray();

					        global $DB;

					        foreach($arResult["КоммерческаяИнформация"]["#"]["Контрагенты"] as $value)
					          foreach( $value["#"]["Контрагент"] as $v)
					            {
        $query77= "INSERT INTO session_control_table VALUES (NULL, '{$now77->format('Y-m-d H:i:s')}','".print_r($v)."')";
        mysql_query($query77,$link77);
							        $name = trim( $v["#"]["Наименование"]["0"]["#"] );
							        $login = trim(  $v["#"]["Логин"]["0"]["#"] );
							        $id1C = trim(  $v["#"]["Код"]["0"]["#"] );
							        $email = trim( $v["#"]["Email"]["0"]["#"] );
							        $agr = trim( $v["#"]["Договор"]["0"]["#"] );

					                    // Проверяем, есть ли юзер с таким 1С ИД
					                $sql = "SELECT count(*) as CNT FROM b_user WHERE ID_1C='".$id1C."'";

							        $tmpRes = $DB->Query($sql);
								    $arRes = $tmpRes->Fetch();



							        if( $arRes["CNT"] < 1 )
							          {
							          	 //  Если нет такого пользователя

										 if( strlen( $login ) < 3 )
										   $login = $id1C;

										 $user = new CUser;
											$arFields = Array(
											  "NAME"              => $name,
											  "EMAIL"             => $email,
											  "LOGIN"             => $login,
											  "LID"               => "s1",      //  сайт №1
											  "ACTIVE"            => "Y",
											  "GROUP_ID"          => array(3),      //  в группу зарегистрированных пользователей
											  "PASSWORD"          => "Aa_1234567",
											  "CONFIRM_PASSWORD"  => "Aa_1234567",
											);

												$ID = $user->Add($arFields);
												if (intval($ID) > 0)
												  {

												    $sql = "UPDATE b_user SET ID_1C='".$id1C."' WHERE ID='".$ID."'";
												    $DB->Query($sql);

					  								//echo "Пользователь ID: ".$ID." успешно добавлен.";

												  }
												else
												    //echo $user->LAST_ERROR;
		                                            ;
							          }
							        else
							          {
							              // есть такой юзер - обновляем логин-емейл-название

							              $sql = "UPDATE b_user ";
							              $sql .= " SET NAME='".$name."',";
							              $sql .= " EMAIL='".$email."'";
							                       //  логин больше = 3 символов
							              if( strlen( $login ) > 2 )
							                $sql .= " ,LOGIN='".$login."'";
							              else
							                $sql .= " ,LOGIN='".$id1C."'";


							              $sql .= " WHERE ID_1C='".$id1C."'";

							              $DB->Query($sql);

							              //echo "USER ".$id1C." UPDATED<br><br>";

							          }

							         //  обновляем данные по договору в таблице юзеров

                                     if( strlen( $agr ) > 2 )
                                       {

							                  //   Ищем договор в таблице и забираем код колонки цены

							              $sql = " SELECT PriceColCode FROM `b_autodoc_agreements` ";
							              $sql .= " WHERE Code='".$agr."'";

							              $res = $DB->Query( $sql );
							              $arRes = $res->Fetch();

							              $priceCol = $arRes["PriceColCode"];

							                    // обновляем код колонки цены и код договора для текущего юзера


							               $sql = "UPDATE b_user ";
							               $sql .= " SET PriceColumn_1C='".$priceCol."'";

                                           if( strlen($agr) > 2 )
                                             $sql .= " , Agreement_1C = '".$agr."'";;

							               $sql .= " WHERE ID_1C='".$id1C."'";
							               $DB->Query( $sql );

                                       }

					            }
					    }









		              UpdateConfirms("Контрагенты", $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"][0]["#"]);



                     $NS["LOAD_CLIENTS"] = "DONE";
                     echo "success\n";
                 }
               elseif( $NS["LOAD_CLIENTS"] == "PROGRESS" )
                 echo "progress\n";
               elseif( $NS["LOAD_CLIENTS"] == "DONE" )
                 echo "success\n";



	       }
	    elseif( $_GET["filename"] == "payment.xml" )
	       {


	           if( ( $NS["LOAD_PAYMENT"] != "DONE" ) && ( $NS["LOAD_PAYMENT"] != "PROGRESS" ) )
	         	 {
                        // грузим оплаты



                    $NS["LOAD_PAYMENT"] = "PROGRESS";
					$reader = new XMLReader();

					$reader->open($_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/payment.xml");


					while ($reader->read())
					  {
					   switch ($reader->nodeType)
					     {
					       case (XMLREADER::ELEMENT):
					       if ($reader->localName == "Оплата")
					         {

					               $node = $reader->expand();
					               $dom = new DomDocument();
					               $n = $dom->importNode($node,true);
					               $dom->appendChild($n);
					               $xp = new DomXpath($dom);


					               $arData = Array();

					               $res = $xp->query("/Оплата/ТипДокумента");
					               $arData["ТипДокумента"] = trim( $res->item(0)->nodeValue );

					               $res = $xp->query("/Оплата/Дата");
					               $arData["Дата"] = $oldDate = trim( $res->item(0)->nodeValue );

					                            //  Преобразуем формат даты  "2010-03-31" ->  "31.03.2010"
					               $arrDate = explode('-',$arData["Дата"]);
					               $arData["Дата"] = $arrDate[2].".".$arrDate[1].".".$arrDate[0];

					               $res = $xp->query("/Оплата/Номер");
					               $arData["Номер"] = trim( $res->item(0)->nodeValue );

					               $res = $xp->query("/Оплата/Контрагент");
					               $arData["Контрагент"] = trim( $res->item(0)->nodeValue );

					               $res = $xp->query("/Оплата/ДоговорКод");
					               $arData["ДоговорКод"] = trim( $res->item(0)->nodeValue );

					               $res = $xp->query("/Оплата/Валюта");
					               $arData["Валюта"] = trim( $res->item(0)->nodeValue );

					               $res = $xp->query("/Оплата/Сумма");
					               $arData["Сумма"] = trim( $res->item(0)->nodeValue );

					               $arSelect = Array("ID", "NAME");

						           $arFilter = Array(
						                "IBLOCK_ID" => CAD_IB_PAYMENTS_NUM,
						                "ACTIVE"=>"Y",
						                "PROPERTY_Code" => $arData["Номер"],
					  	                "PROPERTY_DocumentDate" => $oldDate." 00:00:00",
						                "PROPERTY_DocumentType" => $arData["ТипДокумента"],
						                "PROPERTY_Summ" => $arData["Сумма"]

						              );

					            CModule::IncludeModule("iblock");

					            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);



					            if( $ob = $res->GetNextElement())
					              {
					                 $arFields = $ob->GetFields();
					                 //echo "<br>Найден ".$arFields["ID"]." ".$arFields["NAME"];
					                 //echo "<br/>";
					              }
					            else
					              {
					              	//echo "<br>Не найден  "; XML_ID



					              	if( IsUserPresent( $arData["Контрагент"] ) )
					              	  {

					              	     $el = new CIBlockElement;
					              	     $PROP = array();
					              	     $PROP["Code"] = $arData["Номер"];
					              	     $PROP["ClientCode"] = $arData["Контрагент"];
					              	     $PROP["DocumentType"] = $arData["ТипДокумента"];
					              	     $PROP["DocumentDate"] = $arData["Дата"];
					                     $PROP["AgreementCode"] = $arData["ДоговорКод"];
					                     $PROP["CurrencyCode"] = $arData["Валюта"];
					                     $PROP["Summ"] = $arData["Сумма"];

					              	     $arLoadProductArray = Array(  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
					              	     "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
					              	     "IBLOCK_ID"      => CAD_IB_PAYMENTS_NUM,
					              	     "PROPERTY_VALUES"=> $PROP,
					              	     "NAME"           => "Платеж ".$arData["Номер"],
					              	     "ACTIVE"         => "Y",            // активен
					              	     );



					              	     $PRODUCT_ID = $el->Add( $arLoadProductArray );

					              	  }

					              }







					         }

					        if ($reader->localName == "НомерОтправленного")
					         {

					               $node = $reader->expand();
					               $dom = new DomDocument();
					               $n = $dom->importNode($node,true);
					               $dom->appendChild($n);
					               $xp = new DomXpath($dom);

					               $res = $xp->query("/НомерОтправленного");
					               $confirmNo = trim( $res->item(0)->nodeValue );

					               UpdateConfirms("Оплата", $confirmNo );

					         }


					     }



					  }

	       	       $NS["LOAD_PAYMENT"] = "DONE";
                     echo "success\n";
                 }
               elseif( $NS["LOAD_PAYMENT"] == "PROGRESS" )
                 echo "progress\n";
               elseif( $NS["LOAD_PAYMENT"] == "DONE" )
                 echo "success\n";

	       }
	    elseif( $_GET["filename"] == "add_prices.xml" )
	       {
	       	  //  обработка наценок

				$sResult = "";

			    $new_file_name = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/add_prices.xml";
				$f = fopen($new_file_name, "rb");
				$sResult = fread($f, filesize($new_file_name));
				fclose($f);

				if(strlen($sResult)>0)
				{
					if(toUpper(LANG_CHARSET) != "UTF-8")
						$sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

					$objXML = new CDataXML();
					$objXML->LoadString($sResult);
					$arResult = $objXML->GetArray();

			        $objCoeff = new AddPricesXMLItems();

			        foreach($arResult["КоммерческаяИнформация"]["#"]["Наценки"] as $value)
			          foreach( $value["#"]["Наценка"] as $v)
			            $objCoeff->AddItem($v);

			        if( $objCoeff->GetCount() > 0 )
			          if (CModule::IncludeModule("sale") )
			            $objCoeff->UpdateUserCoeffs();

			        UpdateConfirms("Наценки", $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"][0]["#"]);

			    }


	       	  echo "success\n";

	       }
         else
	       {
     		 echo "failure\n :8 Неизвестная команда";
  	 		 print_r($_REQUEST);
	       }

	}
	else
	{
		echo "failure\n :9 Неизвестная команда";
		print_r($_REQUEST);
	}
}

$contents = ob_get_contents();
ob_end_clean();








if(!$bDesignMode)
{
	if(toUpper(LANG_CHARSET) != "WINDOWS-1251")
		$contents = $APPLICATION->ConvertCharset($contents, LANG_CHARSET, "windows-1251");
	$str = (function_exists("mb_strlen")? mb_strlen($contents, 'latin1'): strlen($contents));
    header("Content-Type: text/html; charset=windows-1251");

	echo $contents;
	die();
}
else
{
	$this->IncludeComponentLang(".parameters.php");
	$arStatuses = Array("" => GetMessage("CP_BCI1_NO"));
	CModule::IncludeModule("sale");
	$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
	while ($arStatus = $dbStatus->GetNext())
	{
		$arStatuses[$arStatus["ID"]] = "[".$arStatus["ID"]."] ".$arStatus["NAME"];
	}

	?><table class="data-table">
	<tr><td><?echo GetMessage("CP_BCI1_SITE_LIST")?></td><td><?echo $arParams["SITE_LIST"]?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_EXPORT_PAYED_ORDERS")?></td><td><?echo $arParams["EXPORT_PAYED_ORDERS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_EXPORT_ALLOW_DELIVERY_ORDERS")?></td><td><?echo $arParams["EXPORT_ALLOW_DELIVERY_ORDERS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_EXPORT_FINAL_ORDERS")?></td><td><?echo $arStatuses[$arParams["EXPORT_FINAL_ORDERS"]]?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_FINAL_STATUS_ON_DELIVERY")?></td><td><?echo $arStatuses[$arParams["FINAL_STATUS_ON_DELIVERY"]]?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_REPLACE_CURRENCY")?></td><td><?echo $arParams["REPLACE_CURRENCY"]?></td></tr>
	<tr><td><?echo GetMessage("CP_BCI1_USE_ZIP")?></td><td><?echo $arParams["USE_ZIP"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	</table>
	<?
}
?>
