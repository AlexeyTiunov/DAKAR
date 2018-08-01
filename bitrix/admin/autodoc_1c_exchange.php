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
//require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/import.php';//подключение класса импорта


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
				else echo "error\n".GetMessage("CC_BSC1_UNZIP_ERROR");
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
				require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/ImportOffers.php';//подключение класса импорта цен
				$params['file'] = $_GET["filename"];
				$offers = new ImportOffers($params);
				unset($offers);	         	 	
				$NS["LOAD_OFFERS"] = "DONE";
				echo "success\n";
			}
			elseif( $NS["LOAD_OFFERS"] == "PROGRESS" ) echo "progress\n";
			elseif( $NS["LOAD_OFFERS"] == "DONE" ) echo "success\n";
		}
		elseif( $_GET["filename"] == "import.xml" )/*************************************import*******************************************************/
		{
	       	//подготавливаем параметры для импорта товаров
	       	require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/ImportItemsBrand.php';//подключение класса импорта товаров и брендов
	       	$params['node'] = 'Товар';
	 		$params['item'] = array('Масло','Радиатор');
	 		$params['allUpdate'] = false;//в данном случае обрабатывается файл вне зависимости с изменениями ли он или без(установки 1С)
	 		$params['file'] = $_GET["filename"];
	 		
	 		$product = new ImportItemsBrand($params);
	 		unset($product);
			echo "success\n";			
		}
		elseif( $_GET["filename"] == "regions.xml" )
		{
	       	  //  ....
			require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/ImportRegions.php';
			$params['file'] = $_GET["filename"];
			$regions = new ImportRegions($params);
			unset($regions);
			echo "success\n";

		}
		elseif( $_GET["filename"] == "agreements.xml" )
		{
			$params['file'] = "agreements.xml";
			require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/ImportAgreement.php';
			$agreem = new ImportAgreement($params); 
			unset($agreem);
			echo "success\n";
		}
		elseif( $_GET["filename"] == "clients.xml" )
	       {

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
							if(toUpper(LANG_CHARSET) != "UTF-8")
								$sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

							$objXML = new CDataXML();
							$objXML->LoadString($sResult);
							$arResult = $objXML->GetArray();

					        global $DB;

					        foreach($arResult["КоммерческаяИнформация"]["#"]["Контрагенты"] as $value)
					          foreach( $value["#"]["Контрагент"] as $v)
					            {
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
											  "PASSWORD"          => "Aa_123456",
											  "CONFIRM_PASSWORD"  => "Aa_123456", //zdM4R5gMde71f7391e3590f9224d827817a70f78    DOK  -e2GK7/fdf178306a0ce8ed4e3d3d3c49891707e
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
							              $sql .= " WHERE Code='".$agr."' AND ClientCode='{$id1C}'";

							              $res = $DB->Query( $sql );
							              $arRes = $res->Fetch();

							              $priceCol = $arRes["PriceColCode"];

							                    // обновляем код колонки цены и код договора для текущего юзера


							               $sql = "UPDATE b_user ";
							               $sql .= " SET PriceColumn_1C='".$priceCol."'";

                                           #if( strlen($agr) > 2 )
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
		elseif($_GET["filename"] == "payment.xml")
		{
			$params['file'] = $_GET["filename"];
			require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/ImportPayment.php';
	 		$payment = new ImportPayment($params);
	 		//require_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/itg/1c_exchange.import/SendPayment.php';
			//new SendPayment();
			unset($payment);
            echo "success\n";
		}
	    elseif($_GET["filename"] == "add_prices.xml")
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
