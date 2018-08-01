<?
///var/www/bitrix/modules/catalog/load_import
include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/import_setup_templ.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");

$NUM_CATALOG_LEVELS = IntVal(COption::GetOptionString("catalog", "num_catalog_levels", 3));

$strCSVError = "";
     //  время выполнения скрипта - неограничено
set_time_limit(0);

//********************  ACTIONS  **************************************//
if ($STEP > 1)
{


    if( isset( $_REQUEST["FILEMODE"] ) && ( $_REQUEST["FILEMODE"] == "MULTI") )
      {



                 //  если пришел список файлов из внешнего флешевого загрузчика
        if( isset( $_REQUEST["fnames"]) )
           {           	   $arFNames = explode("|", $_REQUEST["fnames"] );
                               // разархивируем (если нужно) и переносим загруженные файлы
               $arFNames = PreprocessLoadedFiles( $arFNames );
               $_REQUEST["CUR_FILE_NUM"] = 0;
               $_REQUEST["CURFILE"] = $arFNames[0];
               $_REQUEST["FILE_NAMES"] = serialize($arFNames);
           }
        else
          {          	 $arFNames = Array();
          	 $arFNames = unserialize($_REQUEST["FILE_NAMES"]);
          }

        echo "<br><div align='center'><h3>Режим пакетной загрузки файлов</h3>";
        if( $STEP != 5 )
          echo "Загружается ";
        else
          echo "Загружен ";

        echo "файл: <strong>".$_REQUEST["CURFILE"]."</strong> ( # ".( $_REQUEST["CUR_FILE_NUM"] + 1 )." из ".count($arFNames)." )" ;
        echo "</div><br><br>" ;


        if( strlen( $_REQUEST["CURFILE"] ) > 1 )
          {
          $URL_DATA_FILE = '/upload/data/'.$_REQUEST["CURFILE"];
          $IBLOCK_ID = 20;  // заглушка
          }
      }


	if (strlen($URL_DATA_FILE) > 0 && file_exists($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && $APPLICATION->GetFileAccessPermission($URL_DATA_FILE)>="W")
		$DATA_FILE_NAME = $URL_DATA_FILE;

	if (strlen($DATA_FILE_NAME) <= 0)
		$strCSVError .= GetMessage("CATI_NO_DATA_FILE")."<br>";

	if (strlen($strCSVError) <= 0)
	{
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$arIBlockres = CIBlock::GetList(Array("sort"=>"asc"), Array("ID"=>IntVal($IBLOCK_ID), 'MIN_PERMISSION' => 'W'));
		$arIBlockres = new CIBlockResult($arIBlockres);
		if ($IBLOCK_ID <= 0 || !($arIBlock = $arIBlockres->GetNext()))
			$strCSVError .= GetMessage("CATI_NO_IBLOCK")."<br>";
	}

	if (strlen($strCSVError) <= 0)
	{
		$bIBlockIsCatalog = False;
		if (CCatalog::GetByID($IBLOCK_ID))
			$bIBlockIsCatalog = True;
	}

	if (strlen($strCSVError) > 0)
	{
		$STEP = 1;
	}
}

if ($STEP > 2)
{
	$csvFile = new CCSVData();
	$csvFile->LoadFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);

	if ($fields_type != "F" && $fields_type != "R")
		$strCSVError .= GetMessage("CATI_NO_FILE_FORMAT")."<br>";

	$arDataFileFields = array();
	if (strlen($strCSVError)<=0)
	{
		$fields_type = (($fields_type == "F") ? "F" : "R" );

		$csvFile->SetFieldsType($fields_type);

		if ($fields_type == "R")
		{
			$first_names_r = (($first_names_r=="Y") ? "Y" : "N" );
			$csvFile->SetFirstHeader(($first_names_r == "Y") ? true : false);

			$delimiter_r_char = "";
			switch ($delimiter_r)
			{
				case "TAB":
					$delimiter_r_char = "\t";
					break;
				case "ZPT":
					$delimiter_r_char = ",";
					break;
				case "SPS":
					$delimiter_r_char = " ";
					break;
				case "OTR":
					$delimiter_r_char = substr($delimiter_other_r, 0, 1);
					break;
				case "TZP":
					$delimiter_r_char = ";";
					break;
			}

			if (strlen($delimiter_r_char) != 1)
				$strCSVError .= GetMessage("CATI_NO_DELIMITER")."<br>";


			$enclosed_by_r_char = "";
			switch ($enclosed_by_r)
			{
				case "NOT":
					$enclosed_by_r_char = "\0";
					break;
				case "DBL":
					$enclosed_by_r_char = "\"";
					break;
				case "SNG":
					$enclosed_by_r_char = "\'";
					break;
				case "OTR":
					$enclosed_by_r_char = substr( $enclosed_by_other_r , 0, 1 );
					break;
			}

			if ( (strlen($enclosed_by_r_char) != 1) && ( $STEP < 4 ) )
				$strCSVError .= "Не определен обрамляющий символ<br>";




			if (strlen($strCSVError) <= 0)
			{
				$csvFile->SetDelimiter($delimiter_r_char);

			}
		}
		else
		{
			$first_names_f = (($first_names_f == "Y") ? "Y" : "N" );
			$csvFile->SetFirstHeader(($first_names_f == "Y") ? true : false);

			if (strlen($metki_f) <= 0)
				$strCSVError .= GetMessage("CATI_NO_METKI")."<br>";

			if (strlen($strCSVError) <= 0)
			{
				$arMetkiTmp = preg_split("/[\D]/i", $metki_f);

				$arMetki = array();
				for ($i = 0; $i < count($arMetkiTmp); $i++)
				{
					if (IntVal($arMetkiTmp[$i]) > 0)
					{
						$arMetki[] = IntVal($arMetkiTmp[$i]);
					}
				}

				if (!is_array($arMetki) || count($arMetki)<1)
					$strCSVError .= GetMessage("CATI_NO_METKI")."<br>";

				if (strlen($strCSVError)<=0)
				{
					$csvFile->SetWidthMap($arMetki);
				}
			}
		}

		if (strlen($strCSVError) <= 0)
		{
			$bFirstHeaderTmp = $csvFile->GetFirstHeader();
			$csvFile->SetFirstHeader(false);
			if ($arRes = $csvFile->Fetch())
			{
				for ($i = 0; $i < count($arRes); $i++)
				{
					$arDataFileFields[$i] = $arRes[$i];
				}
			}
			else
			{
				$strCSVError .= GetMessage("CATI_NO_DATA")."<br>";
			}
			$NUM_FIELDS = count($arDataFileFields);
		}
	}

	if ( strlen($strCSVError) > 0   )
	{

		$STEP = 2;
	}
}

if ($STEP == 4)
{
             //   Предзагрузка данных во временную таблицу


            // полный путь к загружаемому файлу

     $dataFName = $_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME;

    $strCSVError = "";

	if ($fields_type != "F" && $fields_type != "R")
		$strCSVError .= GetMessage("CATI_NO_FILE_FORMAT")."<br>";

	$arDataFileFields = array();


	if (strlen($strCSVError)<=0)
	{
		$fields_type = (($fields_type == "F") ? "F" : "R" );

             //   № поля, по которому будет идти коррекция цены на коэффициент
        $corrCoeffFieldNo = $_REQUEST["CORR_COEFF"];

             // переменная вида @что-то  , где живет поле, по которому идет корректировка цені

        $corrCoeffField = "";

		if ($fields_type == "R")
		{
			$first_names_r = (($first_names_r=="Y") ? "Y" : "N" );
			$csvFile->SetFirstHeader(($first_names_r == "Y") ? true : false);

			$delimiter_r_char = "";
			switch ($delimiter_r)
			{
				case "TAB":
					$delimiter_r_char = "\t";
					break;
				case "ZPT":
					$delimiter_r_char = ",";
					break;
				case "SPS":
					$delimiter_r_char = " ";
					break;
				case "OTR":
					$delimiter_r_char = substr($delimiter_other_r, 0, 1);
					break;
				case "TZP":
					$delimiter_r_char = ";";
					break;
			}

			if (strlen($delimiter_r_char) < 1)
				$strCSVError .= GetMessage("CATI_NO_DELIMITER")."<br>";

			$enclosed_by_r_char = "";
			switch ($enclosed_by_r)
			{

				case "NOT":
					$enclosed_by_r_char = "\0";
					break;
				case "DBL":
					$enclosed_by_r_char = "\"";
					break;
				case "SNG":
					$enclosed_by_r_char = "\'";
					break;
				case "OTR":
					$enclosed_by_r_char = substr($enclosed_by_other_r, 0, 1);
					break;
			}

			if (strlen($enclosed_by_r_char) != 1)
				$strCSVError .= GetMessage("Не определн обрамляющий символ")."<br>";



                  //  Считаем, сколько полей в первой строке файла данных
	       $f=fopen($dataFName,"r") or die("Ошибка открытия файла данных");

		   $NUM_FIELDS= count(fgetcsv($f, 1000, $delimiter_r_char) );
	       fclose($f);

	       $arTmpFieldNames = array();


                  // Создаем список полей в виде  column1, column2, @dummy, ...


            for ($i = 0; $i < $NUM_FIELDS; $i++)
              {
                if( strlen(${"field_".$i}) >= 0 )
                  {
                  	switch(${"field_".$i})
                  	  {
                  	  	case CAD_IE_NAME :
                          $tblFieldsList .= "@Caption";
                          if( $corrCoeffFieldNo == $i )
                            $corrCoeffField = "@Caption";
                          break;

                  	  	case CAD_IP_PRICE :
                          $tblFieldsList .= "@Price";
                          if( $corrCoeffFieldNo == $i )
                            $corrCoeffField = "@Price";
                          break;

                  	  	case CAD_IP_ITEM_CODE :
                          $tblFieldsList .= "@ItemCode";
                          if( $corrCoeffFieldNo == $i )
                            $corrCoeffField = "@ItemCode";
                          break;

                  	  	case CAD_IP_WEIGHT :
                          $tblFieldsList .= "@Weight";
                          if( $corrCoeffFieldNo == $i )
                            $corrCoeffField = "@Weight";

                          break;

                  	  	case CAD_IP_BRAND_CODE :
                          $tblFieldsList .= "@strBrandCode";
                          if( $corrCoeffFieldNo == $i )
                            $corrCoeffField = "@strBrandCode";

                          break;

                        case "ANALOG_I1CODE" :
                          $tblFieldsList .= "I1Code";
                          break;

                        case "ANALOG_I2CODE" :
                          $tblFieldsList .= "I2Code";
                          break;

                        case "ANALOG_B1CODE" :
                          $tblFieldsList .= "strB1Code";
                          break;

                        case "ANALOG_B2CODE" :
                          $tblFieldsList .= "strB2Code";
                          break;


                        default:
                          $tblFieldsList .= "@dummy_".$i;
                          if( $corrCoeffFieldNo == $i )
                             $corrCoeffField = "@dummy_".$i;

                          break;
                              //   @dummy - заглушка, если в конкретное поле таблицы не пишется ничего из файла
                	  }

                  }
                else
                  {
                    $tblFieldsList .="@dummy2";
                  }

                if ( $i != $NUM_FIELDS -1 )
                  $tblFieldsList .= ", ";

		      }



        }
        elseif($fields_type == "F")
        {        	 //  Режим импорта данных из файла с Fixed Field Length
           if( isset( $_REQUEST["metki_ser"] ) )
             {                $arTmp = unserialize(  $_REQUEST["metki_ser"]  );

                $arMetki = Array();
                $j = 0 ;

                for( ; $j < count( $arTmp ); $j++ )
                  {
                    $arMetki[$j]["end"] = $arTmp[$j];
                    if( $j != 0 )
                      $arMetki[$j]["start"] = $arTmp[$j-1]+1;
                    else
                      $arMetki[$j]["start"] = 1;
                  }

                $arMetki[$j]["start"] = $arMetki[$j-1]["end"] + 1;
                $arMetki[$j]["end"] = $arMetki[$j]["start"] + 50;

                for( $i = 0; $i < count( $arMetki ); $i++ )
                  {
		                                  // Заполняем параметры для будущего вызова sql substring

                      $arSQLFields[$i]["from"]= $arMetki[$i]["start"];
                      $arSQLFields[$i]["len"] = $arMetki[$i]["end"] - $arMetki[$i]["start"] + 1;
                                   //  dg :  24/01/2011

                         if( $corrCoeffFieldNo == $i )
                           {
                             $corrCoeff["len"] = $arSQLFields[$i]["len"];
                             $corrCoeff["from"] = $arSQLFields[$i]["from"];
                           }


		               if( strlen(${"field_".$i}) >= 0 )
		                  {
		                  	switch(${"field_".$i})
		                  	  {
		                  	  	case CAD_IE_NAME :
		                          $tblFieldsList .= "@Caption";
		                          $arSQLFields[$i]["field"] = "Caption";
		                          break;

		                  	  	case CAD_IP_PRICE :
		                          $tblFieldsList .= "@Price";
	                              $arSQLFields[$i]["field"] = "Price";
		                          break;

		                  	  	case CAD_IP_ITEM_CODE :
		                          $tblFieldsList .= "@ItemCode";
		                          $arSQLFields[$i]["field"] = "ItemCode";
		                          break;

		                  	  	case CAD_IP_WEIGHT :
		                          $tblFieldsList .= "@Weight";
                                  $arSQLFields[$i]["field"] = "Weight";
		                          break;

		                  	  	case CAD_IP_BRAND_CODE :
		                          $tblFieldsList .= "@strBrandCode";
                                  $arSQLFields[$i]["field"] = "strBrandCode";
		                          break;

		                        case "ANALOG_I1CODE" :
		                          $tblFieldsList .= "I1Code";
		                          break;

		                        case "ANALOG_I2CODE" :
		                          $tblFieldsList .= "I2Code";
		                          break;

		                        case "ANALOG_B1CODE" :
		                          $tblFieldsList .= "strB1Code";
		                          break;

		                        case "ANALOG_B2CODE" :
		                          $tblFieldsList .= "strB2Code";
		                          break;


		                        default:
		                          $tblFieldsList .= "@dummy_".$i;
                                  $arSQLFields[$i]["field"] = "";
		                          break;
		                              //   @dummy - заглушка, если в конкретное поле таблицы не пишется ничего из файла
		                	  }

		                  }
		                else
		                  {
		                    $tblFieldsList .="@dummy2";
		                  }

		                if ( $i != $NUM_FIELDS -1 )
		                  $tblFieldsList .= ", ";

                  }
             }
        }
                    //  Если режим загрузки = Загрузка аналогов
            if( substr_count($tblFieldsList,"I1Code") && substr_count($tblFieldsList,"I2Code") && substr_count($tblFieldsList,"strB1Code") && substr_count($tblFieldsList,"strB2Code"))
              { 	            $modeImport = "ANALOGS";
   	                   // создаем временную таблицу
	                   // если есть - пришибаем и создаем заново

	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_analogs_tmp_db.sql");
	             // --------------------------------------------
	             // заполняем временную таблицу данными из файла
	             // --------------------------------------------

	            $sql = "LOAD DATA LOCAL INFILE '".$dataFName."' ";
	            $sql .= " IGNORE";
	            $sql .= " INTO TABLE b_autodoc_analogs_temp ";
                $sql .= " FIELDS TERMINATED BY '".@mysql_escape_string($delimiter_r_char)."' ";
	            $sql .= " LINES TERMINATED BY '".detect_line_ending($dataFName)."' ";

	                   //  скипаем первую запись, если в ней - заголовки полей
	            if ($first_names_r == "Y")
	               $sql .= " IGNORE 1 LINES ";

	            $sql .= " ( ".$tblFieldsList." ) ";


	$startImportExecTime = getmicrotime();
	            $tmpRes = $DB->Query($sql);

	                    // готовим перечень доступных брендов из базы в формате "TY" => "916"
	                    // и обновляем соответствующие поля временной таблицы

		           $arBrands = Array();

		           $MY_IBLOCK_ID = CAD_IB_BRANDS_NUM;   //  Код инфоблока "Бренды"

		           $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));
		           while($arItem = $items->GetNext())
		             {
		               $res = CIBlockElement::GetProperty($MY_IBLOCK_ID, $arItem["ID"], array("sort" => "asc"), Array("CODE"=>"ShortName"));
		               $res2 = $res->Fetch();
		               $arBrands[$res2["VALUE"]]= $arItem["ID"];
		             }

	               foreach ($arBrands as $key => $value)
	                 {
	                 	$sql = "UPDATE b_autodoc_analogs_temp  ";
	                 	$sql .= " SET B1Code='".$value."' ";
	                 	$sql .= " WHERE strB1Code='".$key."' ";
	       	            $tmpRes = $DB->Query($sql);

   	                 	$sql = "UPDATE b_autodoc_analogs_temp  ";
	                 	$sql .= " SET B2Code='".$value."' ";
	                 	$sql .= " WHERE strB2Code='".$key."' ";
	       	            $tmpRes = $DB->Query($sql);
	                 }

     	                      // запускаем предобработку данных во временной таблице

	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_preprocess_analogs_tmp_db.0.sql");

	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_preprocess_analogs_tmp_db.1.sql");


	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_preprocess_analogs_tmp_db.2.sql");


	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_preprocess_analogs_tmp_db.3.sql");


              }
                   // Режим загрузки <> аналоги
            else
              {

	                   // создаем временную таблицу
	                   // если есть - пришибаем и создаем заново

	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_import_tmp_db.sql");

	             // заполняем временную таблицу данными из файла

	            $sql = "LOAD DATA LOCAL INFILE '".$dataFName."' ";
	            $sql .= " IGNORE";
	            $sql .= " INTO TABLE b_autodoc_import_temp ";
	                       //   LOAD DATA LOCAL по умолчанию считает разделителем табуляцию

                if($fields_type == "R")
	              $sql .= " FIELDS TERMINATED BY '".@mysql_escape_string($delimiter_r_char)."' ";
	            else
	              $sql .= " FIELDS TERMINATED BY '\0' ";   //  для fixed width fields mode

	            $sql .= " ENCLOSED BY '".$enclosed_by_r_char."' ";

	            $sql .= " LINES TERMINATED BY '".detect_line_ending($dataFName)."' ";

	                   //  скипаем первую запись, если в ней - заголовки полей
	            if ($first_names_r == "Y")
	               $sql .= " IGNORE 1 LINES ";


                if($fields_type == "R")    //  Режим импорта данных из файла с разделителями полей
                  {

	 	            $sql .= " ( ".$tblFieldsList." ) ";

		                       // добавляем код региона

		            $sql .= " SET RegionCode = '".$tab1_region."' ";


		                        // если выбран  конкретный бренд из выпадающего списка, а не пункт "Бренд в колонке"
		           	           // то используем конкретный бренд из выпадающего списка


		            if( $tab1_brands != "1" )
		              $sql .= " , BrandCode = '".$tab1_brands."' ";

		            if( substr_count($tblFieldsList,"Price") )
	//	              $sql .= " , Price = trim(Replace(@Price,',','.')) ";
	                  $sql .= " , Price = trim( prepare_prices(@Price) ) ";

		            if( substr_count( $tblFieldsList,"Weight") )
		              $sql .= " , Weight = trim(Replace(@Weight,',','.')) ";

		            if( substr_count( $tblFieldsList,"@Caption") )
		              $sql .= " , Caption = @Caption ";

		            if( substr_count( $tblFieldsList,"@ItemCode") )
		              $sql .= " , ItemCode = @ItemCode ";

		            if( substr_count( $tblFieldsList,"@strBrandCode") )
		              $sql .= " , strBrandCode = @strBrandCode ";

	                if( $corrCoeffField != "" )
	                   $sql .= " , strCorr = ".$corrCoeffField;

	//

                 }
               elseif($fields_type == "F")     //  Режим импорта данных из файла с Fixed Field Length
                 {                    $sql .= ' (@var) SET ';
                    for( $i = 0; $i < count( $arSQLFields ); $i++)
                      {
                         if( $arSQLFields[$i]["field"] != "" )
                           {	                      	 if( $arSQLFields[$i]["field"] == "ItemCode" )
		                       $sql .= "\n ".$arSQLFields[$i]["field"]." = prepare_icode( SubString(@var,".$arSQLFields[$i]["from"].",".$arSQLFields[$i]["len"]." )) ";
	                      	 elseif( $arSQLFields[$i]["field"] == "Price" )
		                       $sql .= "\n ".$arSQLFields[$i]["field"]." = prepare_prices( SubString(@var,".$arSQLFields[$i]["from"].",".$arSQLFields[$i]["len"]." )) ";
	                      	 else
		                       $sql .= "\n ".$arSQLFields[$i]["field"]."=Trim( SubString(@var,".$arSQLFields[$i]["from"].",".$arSQLFields[$i]["len"]." )) ";
	                         $sql .= ",";
	                       }
                      }


                    if( $sql[ strlen( $sql ) -1 ] == ',' )
                      $sql[ strlen( $sql ) -1 ] = " ";

                           // добавляем код региона

		            $sql .= " , RegionCode = '".$tab1_region."' ";


		                        // если выбран  конкретный бренд из выпадающего списка, а не пункт "Бренд в колонке"
		           	           // то используем конкретный бренд из выпадающего списка


		            if( $tab1_brands != "1" )
		              $sql .= " , BrandCode = '".$tab1_brands."' ";


	                if( isset( $corrCoeff["len"] ) )
	                   $sql .= " , strCorr = Trim( SubString(@var,".$corrCoeff["from"].",".$corrCoeff["len"]." )) ";

                 }

   	$startImportExecTime = getmicrotime();
	            $tmpRes = $DB->Query($sql);

	                     // Режим загрузки файла
	                     //  если не указана цена, но указано Наименование товара, код бренда и код товара => режим загрузки текстовок

	            if( substr_count($sql,"Price") && substr_count($sql,"BrandCode") && substr_count($sql,"ItemCode") )
	              $modeImport = "PRICE";
	            else
	              $modeImport = "TEXT";


                         //  умножаем цену на поправочный коэффициент, внесенный вручную в форме предыдущего шага
                 if( $modeImport == "PRICE" )
                   {

                               //   Добавляем во временную тадлицу ID юзера, грузящего прайс


                          $sql = " UPDATE b_autodoc_import_temp";
	                      $sql .= " SET userID = ".$USER->GetID();
                          $DB->Query($sql);



	                  $arStr = explode("\n", $_REQUEST["CORR_DATA"]);

	                  for( $i = 0; $i < count($arStr); $i++ )
	                   {

	                      $line = explode("-", $arStr[$i] );
	                      if( isset( $line[1] ) )
	                    	  {

	                    	  	 $line[0] = trim($line[0]);
	                    	  	 $line[1] = trim($line[1]);
	                    	  	 $line[1] = str_replace( ",",".", $line[1]);

	                    	  	 if( is_numeric( $line[1] ))
	                    	  	   {
	                                  $sql = " UPDATE b_autodoc_import_temp";
	                                  $sql .= " SET Price = Price * ".floatval( $line[1]);
	                                  $sql .= " WHERE strCorr LIKE '%".PrepareForSQL($line[0])."%'";
                                      $tmpRes = $DB->Query($sql);
	                    	  	   }

	                    	  }

	                   }


	                   $coeff = floatval( trim( str_replace( ",",".", $_REQUEST["CORR_COEFF_ALL"] ) ) );

	                   if( $coeff == 0 ) $coeff = 1;

                       if( $coeff != 1 )
                         {
	                       $sql = " UPDATE b_autodoc_import_temp";
	                       $sql .= " SET Price = Price * ".$coeff;
	                       $tmpRes = $DB->Query($sql);
                         }

                   }


	                    // если буквенный код бренда берется из файла данных,
	                    // а не выбран из списка, то готовим перечень доступных брендов из базы в формате "TY" => "916"
	                    // и обновляем соответствующие поля временной таблицы

	            if( $tab1_brands == "1" )
	            {
		           $arBrands = Array();

		           $MY_IBLOCK_ID = CAD_IB_BRANDS_NUM;   //  Код инфоблока "Бренды"

		           $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));
		           while($arItem = $items->GetNext())
		             {
		               $res = CIBlockElement::GetProperty($MY_IBLOCK_ID, $arItem["ID"], array("sort" => "asc"), Array("CODE"=>"ShortName"));
		               $res2 = $res->Fetch();
		               $arBrands[$res2["VALUE"]]= $arItem["ID"];
		             }

	               foreach ($arBrands as $key => $value)
	                 {	                 	$sql = "UPDATE b_autodoc_import_temp ";
	                 	$sql .= " SET BrandCode='".$value."' ";
	                 	$sql .= " WHERE strBrandCode='".$key."' ";
	       	            $tmpRes = $DB->Query($sql);	                 }

	             }

	                      // запускаем предобработку данных во временной таблице

	             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
	             "/bitrix/php_interface/include/autodoc_preprocess_tmp_db.sql");

                       //  удаляем дублирующиеся записи из временной таблицы

                 $removedDups = RemoveDupRecs();

            }

echo "Затрачено времени : ".(getmicrotime() - $startImportExecTime)." сек.<br>";





	}

	if (strlen($strCSVError) > 0)
	{
		$STEP = 2;
	}

}

if ($STEP == 5)
{
    //   Загрузка данных из временной таблицы в постоянную

    $modeImport = htmlspecialchars($_REQUEST["modeImport"]);
    $modifiedBy = $USER->GetID();

          //  Загрузка текстовок   [-]
    if ( $modeImport == "TEXT" )
      {         $tmpSql = "SELECT CurItemID, BrandCode, ItemCode, Caption FROM b_autodoc_import_temp WHERE CurItemID IS NOT NULL ";
         $tmpSql .= " AND ItemCode IS NOT NULL";
         $tmpSql .= " AND BrandCode IS NOT NULL";
         $tmpSql .= " AND Caption IS NOT NULL";
         $tmpRes = $DB->Query($tmpSql);

               // количество обработанных строк
         $cntProcessed = 0;
              // количество успешно обработанных строк
         $cntSuccess = 0;

              // массив для обновления полей элемента инфоблока

         $arLoadProductArray = Array(
				"MODIFIED_BY"		=>	$modifiedBy,
				"IBLOCK_ID"			=>	CAD_IB_ITEMS_NUM,
				"TMP_ID"				=> md5(uniqid("")),
				"NAME" => ""
				);

         $el = new CIBlockElement;

         while ( $arTmp = $tmpRes->Fetch() )
           {
              $cntProcessed ++;

              $arLoadProductArray["NAME"] = $arTmp["Caption"];
              if( $el->Update($arTmp["CurItemID"], $arLoadProductArray, false, false))
                 $cntSuccess++;


            }
      }

          //  Загрузка цен
    if ( $modeImport == "PRICE" )
      {

                               // запускаем обработку данных во временной таблице
$startImportExecTime = getmicrotime();
             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
             "/bitrix/php_interface/include/autodoc_process_tmp_db.sql");

echo "Обработка завершена за ".( getmicrotime() - $startImportExecTime   )." сек.";



          //  если установлен флажок "Помечать не найденные позиции"  [-]




        /*

               // количество обработанных строк
         $cntProcessed = 0;
              // количество успешно обработанных строк
         $cntSuccess = 0;
              // количество обновленных товаров
         $cntUpdatedItems = 0;
              // количество обновленных цен
         $cntUpdatedPrices = 0;
              // количество добавленных цен
         $cntAddedPrices = 0;
              // количество добавленных товаров
         $cntAddedItems = 0;


     */
      }

          //  Загрузка цен
    if ( $modeImport == "ANALOGS" )
      {

                               // запускаем обработку данных во временной таблице
$startImportExecTime = getmicrotime();

             $arrErrors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"].
             "/bitrix/php_interface/include/autodoc_process_analogs_tmp_db.sql");

echo "Обработка завершена за ".( getmicrotime() - $startImportExecTime   )." сек.";
     }


}
//********************  END ACTIONS  **********************************//

echo ShowError($strCSVError);
?>

<form method="POST" action="<?echo $sDocPath ?>?lang=<?echo LANG ?>" ENCTYPE="multipart/form-data" name="dataload">
<?=bitrix_sessid_post();?>
<?if ($STEP < 6):?>
	<table border="0" cellspacing="1" cellpadding="0" width="99%">
		<tr>
			<td align="left">
				<b><?= str_replace("#STEP#", $STEP, str_replace("#ALL#", 5, GetMessage("CATI_STEPPER_TITLE"))) ?></b><br>
			</td>
			<?if ($STEP!=5){?>
			<td align="right">
				<input type="submit" value="<? echo ($STEP==5) ? (($ACTION=="IMPORT") ? GetMessage("CATI_NEXT_STEP_F") : GetMessage("CICML_SAVE")) : GetMessage("CATI_NEXT_STEP")." &gt;&gt;" ?>" name="submit_btn">
			</td>          <?}?>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<?endif;?>

<table border="0" cellspacing="1" cellpadding="3" width="100%" class="list-table">
<?


if( isset( $_REQUEST["FILEMODE"] ) && ( $_REQUEST["FILEMODE"] == "MULTI") )
	if( ( $STEP > 1 )  && ( $STEP < 5 ) )
	  {


	    ?>	  	<input type="hidden" name="CUR_FILE_NUM" value="<?=$_REQUEST["CUR_FILE_NUM"]?>">
	  	<input type="hidden" name="CURFILE" value="<?=$_REQUEST["CURFILE"]?>">
	  	<input type="hidden" name="FILE_NAMES" value='<?=$_REQUEST["FILE_NAMES"]?>'>
	  	<input type="hidden" name="FILEMODE" value="<?=$_REQUEST["FILEMODE"]?>">

	    <?	  }





//*****************************************************************//
if ($STEP == 1):
//*****************************************************************//
?>
	<tr class="head">
		<td valign="middle" colspan="2" align="center" nowrap><b><?echo GetMessage("CATI_DATA_LOADING") ?></b></td>
	</tr>
	<tr>
		<td align="right" nowrap valign="top">
			<?echo GetMessage("CATI_DATA_FILE_SITE") ?>
		</td>
		<td align="left" nowrap>
			<input type="text" name="URL_DATA_FILE" size="40" value="<?= htmlspecialchars($URL_DATA_FILE) ?>">
			<input type="button" value="<?=GetMessage("CATI_BUTTON_CHOOSE")?>" OnClick="cmlBtnSelectClick()">
<?
CAdminFileDialog::ShowScript(
	array(
		"event" => "cmlBtnSelectClick",
		"arResultDest" => array("FORM_NAME" => "dataload", "FORM_ELEMENT_NAME" => "URL_DATA_FILE"),
		"arPath" => array("PATH" => "/upload/", "SITE" => SITE_ID),
		"select" => 'F',// F - file only, D - folder only, DF - files & dirs
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'csv,txt',
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);
?>
<input type="hidden" name="IBLOCK_ID" value="<?=CAD_IB_ITEMS_NUM?>">
		</td>
	</tr>
<?
//*****************************************************************//
elseif($STEP==2):
//*****************************************************************//
?>
	<tr class="head">
		<td valign="middle" colspan="2" align="center" nowrap>
			<b><?echo GetMessage("CATI_CHOOSE_APPR_FORMAT") ?></b>
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="2" align="left" nowrap>
			<SCRIPT LANGUAGE="JavaScript">
			function DeactivateAllExtra()
			{
				document.getElementById("table_r").disabled = true;
				document.getElementById("table_f").disabled = true;

				document.dataload.metki_f.disabled = true;
				document.dataload.first_names_f.disabled = true;

				var i;
				for (i = 0 ; i < document.dataload.delimiter_r.length; i++)
				{
					document.dataload.delimiter_r[i].disabled = true;
				}
				for (i = 0 ; i < document.dataload.enclosed_by_r.length; i++)
				{
					document.dataload.enclosed_by_r[i].disabled = true;
				}

				document.dataload.delimiter_other_r.disabled = true;
				document.dataload.enclosed_by_other_r.disabled = true;
				document.dataload.first_names_r.disabled = true;
			}

			function ChangeExtra()
			{
				if (document.dataload.fields_type[0].checked)
				{
					document.getElementById("table_r").disabled = false;
					document.getElementById("table_f").disabled = true;

					var i;
					for (i = 0 ; i < document.dataload.delimiter_r.length; i++)
					{
						document.dataload.delimiter_r[i].disabled = false;
					}

					for (i = 0 ; i < document.dataload.enclosed_by_r.length; i++)
					{
						document.dataload.enclosed_by_r[i].disabled = false;
					}


					document.dataload.delimiter_other_r.disabled = false;
					document.dataload.enclosed_by_other_r.disabled = false;
					document.dataload.first_names_r.disabled = false;

					document.dataload.metki_f.disabled = true;
					document.dataload.first_names_f.disabled = true;

					document.dataload.submit_btn.disabled = false;
				}
				else
				{
					if (document.dataload.fields_type[1].checked)
					{
						document.getElementById("table_r").disabled = true;
						document.getElementById("table_f").disabled = false;

						var i;
						for (i = 0 ; i < document.dataload.delimiter_r.length; i++)
						{
							document.dataload.delimiter_r[i].disabled = true;
						}

						for (i = 0 ; i < document.dataload.enclosed_by_r.length; i++)
						{
							document.dataload.enclosed_by_r[i].disabled = true;
						}

						document.dataload.delimiter_other_r.disabled = true;
						document.dataload.enclosed_by_other_r.disabled = true;
						document.dataload.first_names_r.disabled = true;

						document.dataload.metki_f.disabled = false;
						document.dataload.first_names_f.disabled = false;

						document.dataload.submit_btn.disabled = false;
					}
				}
			}



function getCaretPosition (ctrl) {

	var CaretPos = 0;
	// IE Support
	if (document.selection) {

		ctrl.focus ();
		var Sel = document.selection.createRange ();

		Sel.moveStart ('character', -ctrl.value.length);

		CaretPos = Sel.text.length;
	}
	// Firefox support
	else if (ctrl.selectionStart || ctrl.selectionStart == '0')
		CaretPos = ctrl.selectionStart;
        document.dataload.metki_f.value += CaretPos + "\r\n";
        //document.getElementById( 'metki_f' ).value += CaretPos + "\r\n";
	return (CaretPos);
}








			</SCRIPT>

			<input type="radio" name="fields_type" id="id_fields_type_r" value="R" <?if ($fields_type=="R" || strlen($fields_type)<=0) echo "checked";?> onClick="ChangeExtra()"><label for="id_fields_type_r"><?echo GetMessage("CATI_RAZDELITEL") ?></label><br>
			<input type="radio" name="fields_type" id="id_fields_type_f" value="F" <?if ($fields_type=="F") echo "checked";?> onClick="ChangeExtra()"><label for="id_fields_type_f"><?echo GetMessage("CATI_FIXED") ?></label>
		</td>
	</tr>

	<tr>
		<td valign="middle" colspan="2" align="center" nowrap>
			<table id="table_r" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td valign="middle" colspan="2" align="center" nowrap>
						<?echo GetMessage("CATI_RAZDEL1") ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%" align="right">
						<?echo GetMessage("CATI_RAZDEL_TYPE") ?>
					</td>
					<td valign="top" width="50%" align="left" nowrap>
						<input type="radio" name="delimiter_r" value="TZP" <?if ($delimiter_r=="TZP" || strlen($delimiter_r)<=0) echo "checked"?>><?echo GetMessage("CATI_TZP") ?><br>
						<input type="radio" name="delimiter_r" value="ZPT" <?if ($delimiter_r=="ZPT") echo "checked"?>><?echo GetMessage("CATI_ZPT") ?><br>
						<input type="radio" name="delimiter_r" value="TAB" <?if ($delimiter_r=="TAB") echo "checked"?>><?echo GetMessage("CATI_TAB") ?><br>
						<input type="radio" name="delimiter_r" value="SPS" <?if ($delimiter_r=="SPS") echo "checked"?>><?echo GetMessage("CATI_SPS") ?><br>
						<input type="radio" name="delimiter_r" value="OTR" <?if ($delimiter_r=="OTR") echo "checked"?>><?echo GetMessage("CATI_OTR") ?>
						<input type="text" name="delimiter_other_r" size="3" value="<?echo htmlspecialchars($delimiter_other_r) ?>">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%" align="right">
						Обрамляющий символ
					</td>
					<td valign="top" width="50%" align="left" nowrap>
						<input type="radio" name="enclosed_by_r" value="NOT" <?if ($enclosed_by_r == "NOT" || strlen($enclosed_by_r)<=0) echo "checked"?>>нет обрамляющего символа<br>
						<input type="radio" name="enclosed_by_r" value="DBL" <?if ($enclosed_by_r == "DBL" ) echo "checked"?>>двойные кавычки<br>
						<input type="radio" name="enclosed_by_r" value="SNG" <?if ($enclosed_by_r == "SNG") echo "checked"?>>одинарные кавычки<br>
						<input type="radio" name="enclosed_by_r" value="OTR" <?if ($enclosed_by_r == "OTR") echo "checked"?>><?echo GetMessage("CATI_OTR") ?>
						<input type="text" name="enclosed_by_other_r" size="3" value="<?echo htmlspecialchars($enclosed_by_other_r) ?>">
					</td>
				</tr>


				<tr>
					<td valign="top" align="right" width="50%">
						<?echo GetMessage("CATI_FIRST_NAMES") ?>
					</td>
					<td valign="top" align="left" width="50%">
                       <input type="checkbox" name="first_names_r" value="Y" >
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td valign="middle" colspan="2" align="center" nowrap>
			<table id="table_f" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td valign="middle" colspan="2" align="center" nowrap>
						<?echo GetMessage("CATI_FIX1") ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" width="50%">
						<?echo GetMessage("CATI_FIX_MET") ?><br>
						<small><?echo GetMessage("CATI_FIX_MET_DESCR") ?></small>
					</td>
					<td valign="top" align="left" width="50%">
						<textarea name="metki_f" rows="7" cols="3"><?echo htmlspecialchars($metki_f) ?></textarea>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" width="50%">
						<?echo GetMessage("CATI_FIRST_NAMES") ?>
					</td>
					<td valign="top" align="left" width="50%">
						<input type="checkbox" name="first_names_f" value="Y" <?if ($first_names_f=="Y") echo "checked"?>>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td valign="middle" colspan="2" align="center" nowrap>
			<table border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td valign="middle" align="center" nowrap>
						<?echo GetMessage("CATI_DATA_SAMPLES") ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="center" nowrap>
						<?
						$file_id = fopen($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, "rb");
						$sContent = fread($file_id, 10000);
						fclose($file_id);
						?>
						<textarea name="data" id="data" wrap="OFF" rows="7" cols="90"  onclick = 'getCaretPosition(this)' ><?echo htmlspecialchars($sContent) ?></textarea>
					</td>
				</tr>


			</table>
		</td>
	</tr>


       </table>
     </td>
</tr>


	<SCRIPT LANGUAGE="JavaScript">
		DeactivateAllExtra();
		ChangeExtra();
	</SCRIPT>
<?
//*****************************************************************//
elseif ($STEP==3):
//*****************************************************************//
?>
	<tr class="head">
		<td valign="middle" colspan="2" align="center" nowrap>
			<b><?echo GetMessage("CATI_FIELDS_SOOT") ?></b>

		</td>
	</tr>

	<tr>
		<td valign="middle" colspan="2" align="left" nowrap>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
			<?
			$arAvailFields = array();

			$strVal = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields.",".$defCatalogAvailPriceFields);
			$arVal = split(",", $strVal);
			$arCatalogAvailProdFields_tmp = array_merge($arCatalogAvailProdFields, $arCatalogAvailPriceFields);
			for ($i = 0; $i < count($arVal); $i++)
			{
				for ($j = 0; $j < count($arCatalogAvailProdFields_tmp); $j++)
				{
					if ($arVal[$i]==$arCatalogAvailProdFields_tmp[$j]["value"]
						&& $arVal[$i]!="IE_ID")
					{
						$arAvailFields[] = array("value"=>$arCatalogAvailProdFields_tmp[$j]["value"], "name"=>$arCatalogAvailProdFields_tmp[$j]["name"]);
						break;
					}
				}
			}

			$properties = CIBlockProperty::GetList(
					array("sort" => "asc", "name" => "asc"),
					array("ACTIVE" => "Y", "IBLOCK_ID" => $IBLOCK_ID)
				);

			while ($prop_fields = $properties->Fetch())
			{
				$arAvailFields[] = array("value"=>"IP_PROP".$prop_fields["ID"], "name"=>GetMessage("CATI_FI_PROPS")." \"".$prop_fields["NAME"]."\"");
			}

			for ($k = 0; $k < $NUM_CATALOG_LEVELS; $k++)
			{
				$strVal = COption::GetOptionString("catalog", "allowed_group_fields", $defCatalogAvailGroupFields);
				$arVal = split(",", $strVal);
				for ($i = 0; $i < count($arVal); $i++)
				{
					for ($j = 0; $j < count($arCatalogAvailGroupFields); $j++)
					{
						if ($arVal[$i]==$arCatalogAvailGroupFields[$j]["value"])
						{
							$arAvailFields[] = array("value"=>$arCatalogAvailGroupFields[$j]["value"].$k, "name"=>GetMessage("CATI_FI_GROUP_LEV")." ".($k+1).": ".$arCatalogAvailGroupFields[$j]["name"]);
							break;
						}
					}
				}
			}


			$arAvailFields[] = array("value"=>"CV_QUANTITY_FROM", "name"=>GetMessage("DIN_QUANTITY_FROM"));
			$arAvailFields[] = array("value"=>"CV_QUANTITY_TO", "name"=>GetMessage("DIN_QUANTITY_TO"));

			$strVal = COption::GetOptionString("catalog", "allowed_price_fields", $defCatalogAvailValueFields);
			$arVal = split(",", $strVal);
			$db_prgr = CCatalogGroup::GetList(array("NAME" => "ASC"), Array());
			while ($prgr = $db_prgr->Fetch())
			{
				for ($i = 0; $i < count($arVal); $i++)
				{
					for ($j = 0; $j < count($arCatalogAvailValueFields); $j++)
					{
						if ($arVal[$i]==$arCatalogAvailValueFields[$j]["value"])
						{
							$arAvailFields[] = array("value"=>$arCatalogAvailValueFields[$j]["value"]."_".$prgr["ID"], "name"=>str_replace("#NAME#", $prgr["NAME"], GetMessage("DIN_PRICE_TYPE")).": ".$arCatalogAvailValueFields[$j]["name"]);
							break;
						}
					}
				}
			}

			for ($i = 0; $i < count($arDataFileFields); $i++)
			{
				?>
				<tr>
					<td valign="top">
						<b>Колонка <?echo $i+1 ?></b> (<?echo htmlspecialchars(TruncateText($arDataFileFields[$i], 15));?>)
					</td>
					<td valign="top">
						<select name="field_<?echo $i ?>">
							<option value=""> - </option>
							<?

							for ($j = 0; $j < count($arAvailFields); $j++)
							{


							    switch($arAvailFields[$j]["value"] )
							      {
								   case CAD_IE_NAME :
         								?>
            								<option value="<?echo $arAvailFields[$j]["value"] ?>"  <?if (${"field_".$i}==$arAvailFields[$j]["value"] || !isset(${"field_".$i}) && $arAvailFields[$j]["value"]==$arDataFileFields[$i]) echo "selected" ?>>
								            <?echo CAD_IE_NAME_DESC ?></option>
								        <?
								        break;
								   case CAD_IP_BRAND_CODE :
         								?>
            								<option value="<?echo $arAvailFields[$j]["value"] ?>"  <?if (${"field_".$i}==$arAvailFields[$j]["value"] || !isset(${"field_".$i}) && $arAvailFields[$j]["value"]==$arDataFileFields[$i]) echo "selected" ?>>
								            <?echo CAD_IP_BRAND_CODE_DESC ?></option>
								        <?
								        break;

								   case CAD_IP_WEIGHT :
         								?>
            								<option value="<?echo $arAvailFields[$j]["value"] ?>"  <?if (${"field_".$i}==$arAvailFields[$j]["value"] || !isset(${"field_".$i}) && $arAvailFields[$j]["value"]==$arDataFileFields[$i]) echo "selected" ?>>
								            <?echo CAD_IP_WEIGHT_DESC ?></option>
								        <?
								        break;

								   case CAD_IP_ITEM_CODE :
         								?>
            								<option value="<?echo $arAvailFields[$j]["value"] ?>"  <?if (${"field_".$i}==$arAvailFields[$j]["value"] || !isset(${"field_".$i}) && $arAvailFields[$j]["value"]==$arDataFileFields[$i]) echo "selected" ?>>
								            <?echo CAD_IP_ITEM_CODE_DESC ?></option>
								        <?
								        break;

								  }
							}
?>
							<option value="CAD_IP_PRICE"  ><?echo CAD_IP_PRICE_DESC ?></option>

<?


				       if( 1 )
				         {
							?>

			   				<option value="ANALOG_B1CODE"  >Аналоги: Бренд 1</option>
	       					<option value="ANALOG_I1CODE"  >Аналоги: Код товара 1</option>
							<option value="ANALOG_B2CODE"  >Аналоги: Бренд 2</option>
							<option value="ANALOG_I2CODE"  >Аналоги: Код товара 2</option>

                           <?
                         }
                           ?>
						</select>
					</td>
				</tr>
				<?
			}
			?>
			</table>
		</td>
	</tr>
    <tr>



<tr>
		<td valign="middle" colspan="2" align="center" nowrap>

<? if(isset($arMetki))
     {
 ?>
		<input type="hidden" name="metki_ser" value="<?=serialize($arMetki);?>">
<?
     }

?>
	<input type="hidden" name="enclosed_by_r" value="<?=$enclosed_by_r;?>">
    <input type="hidden" name="enclosed_by_other_r" value="<?=$enclosed_by_other_r;?>">
    <table id="table_autodoc" border="0" cellspacing="0" cellpadding="3" width="100%">

	 <tr>
	 <td valign="top" width="50%" align="right" nowrap>Регион</td> <td><select name="tab1_region" gtbfieldid="76">


<?
           // выводим список регионов
	    $MY_IBLOCK_ID = CAD_IB_REGIONS_NUM;   //  Код инфоблока "Регионы"

        $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));
	        while($arItem = $items->GetNext())
	          {
                 $sql = "SELECT ".CAD_IP_CODE." as CODE FROM b_iblock_element_prop_s".CAD_IB_REGIONS_NUM;
                 $sql .= " WHERE IBLOCK_ELEMENT_ID='".$arItem["ID"]."'";
                 $tmpRes = $DB->Query($sql);
                 $arRes = $tmpRes->Fetch();

	             ?><option value='<? echo intval( $arRes["CODE"]); ?>'><?echo $arItem["NAME"];?></option>
	             <?
	          }
?>


            </select>
	  </td> </tr>

        <tr>
        <td valign="top" width="50%" align="right" nowrap>Бренд</td> <td>
        <select name="tab1_brands" gtbfieldid="77"> <option selected="selected" value="1">Бренд в колонке&hellip;</option>

<?
             // выводим список брендов

        $MY_IBLOCK_ID = CAD_IB_BRANDS_NUM;   //  Код инфоблока "Бренды"
        $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));
        while($arItem = $items->GetNext())
          {
             ?><option value="<?echo $arItem["ID"];?>"><?echo $arItem["NAME"];?></option><?

          }
 ?>

         </select></td> </tr>


        <tr> <td valign="top" width="50%" align="right" nowrap> Помечать не найденные позиции</td> <td><input type="checkbox" checked="checked" value="y" id="checkbox" name="chkbSelect404Pos" /> <label for="checkbox"></label></td> </tr>
</table>



    </tr>

	<?if ($ACTION=="IMPORT_SETUP"):?>
		<tr>
			<td valign="middle" colspan="2" align="center" nowrap>
				<b><?= GetMessage("CATI_IMPORT_SCHEME_NAME") ?></b>
			</td>
		</tr>
		<tr>
			<td valign="middle" align="right" nowrap>
				<?= GetMessage("CATI_IMPORT_SCHEME_NAME") ?>
			</td>
			<td valign="top" align="left" nowrap>
				<input type="text" name="SETUP_PROFILE_NAME" size="40" value="<?echo htmlspecialchars($SETUP_PROFILE_NAME)?>">
			</td>
		</tr>
	<?endif;?>

	<tr>
		<td valign="middle" colspan="2" align="center" nowrap>
			<table border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td valign="middle" align="center" nowrap>
						<?echo GetMessage("CATI_DATA_SAMPLES") ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="center" nowrap>

						<?
						$file_id = fopen($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, "rb");
						$sContent = fread($file_id, 10000);
						fclose($file_id);
						?>
						<textarea name="data" wrap="OFF" rows="7" cols="90"><?echo htmlspecialchars($sContent) ?></textarea>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td valign="middle" colspan="2" align="center" nowrap>
			<table border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td valign="middle" align="center">
						Коррекция цены
					</td>
				</tr>

				<tr>
					<td valign="middle" align="center">
						Общий коэффициент корректировки<br>( для всего загружаемого прайса )<br>
                        <input type="text" name="CORR_COEFF_ALL" size="4" value="1.0">
					</td>
				</tr>

				<tr>
					<td valign="top" align="center" nowrap>
						Частные коэффициенты корректировки базовой цены<br> Формат каждой строки: "Подстрока" - "Коэффициент"<br>Пример:  1100002А5 - 1.29<br>
						<textarea name="CORR_DATA" wrap="OFF" rows="7" cols="40"><?echo htmlspecialchars("") ?></textarea>
            <br>&nbsp;<br>
            Искать подстроку в колонке :
            <select name="CORR_COEFF">
            <option value="-1" selected="selected"> - </option>
<?
			for ($i = 0; $i < count($arDataFileFields); $i++)
			{
				?>
				<option value="<?=$i?>" >
				<?echo "Колонка ".($i+1)." : ".htmlspecialchars(TruncateText($arDataFileFields[$i], 15));?>
				</option>
<?
		       }
?>
					</select><br>&nbsp;<br></td>
				</tr>
			</table>
		</td>
	</tr>



<?
//*****************************************************************//
elseif ($STEP==4):
//*****************************************************************//
?>
	<tr class="head">
		<td valign="middle" colspan="2" align="center" nowrap>
			<b><?echo GetMessage("CAD_TMP_LOAD") ?></b>

		</td>
	</tr>

<? if( $modeImport == "PRICE")
    {	 ?>
	<tr>
		<td valign="middle" colspan="2" align="left" nowrap>
     		<table width="100%" border="0" cellspacing="0" cellpadding="3">
               <tr>
					<td valign="top" align="center">

     		<table width="100%" border="0" cellspacing="0" cellpadding="0">
               <tr>
					<td valign="top" align="right" width="50%">
				        Загружено строк:
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE 1";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>
               <tr>
					<td valign="top" align="right" width="50%">
				        В файле найдено корректных строк с ценами:
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ItemCode IS NOT NULL AND BrandCode IS NOT NULL AND Price IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>

               <tr>
					<td valign="top" align="right" width="50%">
				        Новых строк с ценами к уже имеющимся товарам :
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ItemCode IS NOT NULL AND BrandCode IS NOT NULL AND CurPriceID IS NULL AND CurItemID IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>
               <tr>
					<td valign="top" align="right" width="50%">
				        Будет добавлено новых товаров :
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ItemCode IS NOT NULL AND BrandCode IS NOT NULL AND CurPriceID IS NULL AND CurItemID IS NULL AND Price IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>


               <tr>
					<td valign="top" align="right" width="50%">
				        Перезаписывается уже сохраненных кодов:
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ItemCode IS NOT NULL AND CurItemID IS NOT NULL  AND CurPriceID IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>
               <tr>
					<td valign="top" align="right" width="50%">
				        Проигнорировано строк с неизвестным брендом или пустым кодом товара:
					</td>
					<td valign="top" align="left">
                       <b>
						<?

                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ItemCode IS NULL OR BrandCode IS NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];

						?>
                       </b> шт
					</td>
			  </tr>
<?
   if( $removedDups > 0 )
     {
?>       <tr>
           <td valign="top" align="right" width="50%">
				        Удалено дублирующихся строк:
					</td>
					<td valign="top" align="left"><strong><?=$removedDups;?></strong> шт

           </tr>
<?
     }
?>




             </table>



					</td>
                </tr>
             </table>

<? }
if ( $modeImport == "TEXT")
  {
?>

	<tr>
		<td valign="middle" colspan="2" align="left" nowrap>
     		<table width="100%" border="0" cellspacing="0" cellpadding="3">
               <tr>
					<td valign="top" align="center">

     		<table width="100%" border="0" cellspacing="0" cellpadding="0">
               <tr>
					<td valign="top" align="right" width="50%">
				        Загружено строк:
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE 1";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>
               <tr>
					<td valign="top" align="right" width="50%">
				        Загружено пар "название-код":
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ";
                          $tmpSql .= "BrandCode IS NOT NULL";
                          $tmpSql .= " AND ItemCode IS NOT NULL";
                          $tmpSql .= " AND Caption IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт
					</td>
                </tr>

               <tr>
					<td valign="top" align="right" width="50%">
				        Не найдено кодов:
					</td>
					<td valign="top" align="left">
                       <b>
						<?
                          $tmpSql = "SELECT count(id) as cnt FROM b_autodoc_import_temp WHERE ";
                          $tmpSql .= "CurItemID IS NULL";
                          $tmpSql .= " AND BrandCode IS NOT NULL";
                          $tmpSql .= " AND ItemCode IS NOT NULL";
                          $tmpSql .= " AND Caption IS NOT NULL";
                          $tmpRes = $DB->Query($tmpSql);
                          $arTmp = $tmpRes->Fetch();
                          echo $arTmp["cnt"];
						?>
                       </b> шт<br><br>
                       <textarea name="notFoundCodes" cols="20" rows="5"><?
                           $tmpSql = "SELECT ItemCode FROM b_autodoc_import_temp WHERE ";
                           $tmpSql .= "CurItemID IS NULL AND itemCode IS NOT NULL";
                           $tmpRes = $DB->Query($tmpSql);
                           while ($arTmp = $tmpRes->Fetch())
                             {
                               echo $arTmp["ItemCode"]."\n";
                             }

						?></textarea><br><br> Эти кода не будут загружены<br>
					</td>
                </tr>
           </table>
           </td>
           </tr>
           </table>
<?
}
?>
<input type="hidden" name="modeImport" value="<?echo $modeImport;?>">
<input type="hidden" name="chkbSelect404Pos" value="<?echo htmlspecialchars($_REQUEST["chkbSelect404Pos"]);?>">




<?
//*****************************************************************//
elseif ($STEP==5):
//*****************************************************************//



?>
	<tr class="head">
		<td valign="middle" colspan="2" align="center" nowrap>
			<b>Результаты загрузки</b>
		</td>
	</tr>

<? if ( $modeImport == "TEXT" )
     {
?>
	<tr>
	  <td>

     		<table width="100%" border="0" cellspacing="0" cellpadding="0">
               <tr>
					<td valign="top" align="right" width="50%">
				        Обработано кодов :
					</td>
					<td valign="top" align="left">
                       <b><? echo $cntProcessed;?><b> шт.
                    </td>
                 </tr>
               <tr>
					<td valign="top" align="right" width="50%">
				        Из них успешно :
					</td>
					<td valign="top" align="left">
                       <b><? echo $cntSuccess; ?><b> шт.
                    </td>
                 </tr>
            </table>
	  </td>
	</tr>
<?   }
   if ( $modeImport == "PRICE" )
     {
?>

               <tr>
					<td valign="top" align="center" colspan=2>

<?

 if( $arrErrors !== false )
  {
    echo "<br>Запросы к БД выполнены с ошибками<pre>";
    print_r( $arrErrors );
    echo "</pre>";
  }
  else
    {
      echo "<b><h3>Загрузка файла успешно завершена</h3>";


      if( isset( $_REQUEST["FILEMODE"] ) && ( $_REQUEST["FILEMODE"] == "MULTI") )
		  {
             $arFNames = unserialize($_REQUEST["FILE_NAMES"]);
             if( count($arFNames) > ( $_REQUEST["CUR_FILE_NUM"] + 1 ) )
               {

				    ?>
				    <input type="hidden" name="STEP" value="2">
				  	<input type="hidden" name="CUR_FILE_NUM" value="<?=($_REQUEST["CUR_FILE_NUM"]+1);?>">
				  	<input type="hidden" name="CURFILE" value="<?=$arFNames[ $_REQUEST["CUR_FILE_NUM"]+1 ]?>">
				  	<input type="hidden" name="FILE_NAMES" value='<?=$_REQUEST["FILE_NAMES"]?>'>
				  	<input type="hidden" name="FILEMODE" value="<?=$_REQUEST["FILEMODE"]?>">


				  	<input name="modeImport" value="PRICE" type="hidden">
					<input name="chkbSelect404Pos" value="y" type="hidden">
					<input name="lang" value="ru" type="hidden">
					<input name="ACT_FILE" value="csv_auotodoc" type="hidden">
					<input name="ACTION" value="IMPORT" type="hidden">
					<input name="IBLOCK_ID" value="20" type="hidden">

 					<input type="submit" value="Продолжить обработку" name="submit_btn">

				    <?
		       }
		     else
		       {		          echo "<h3>Пакетная обработка завершена</h3>";
		       }

		  }



    }


?>

					</td>
                 </tr>

<?
     }
?>
<?
//*****************************************************************//
elseif ($STEP==6):
//*****************************************************************//
	$FINITE = True;
//*****************************************************************//
endif;
//*****************************************************************//
?>
</table>

<?if ($STEP < 5):?>
	<table border="0" cellspacing="1" cellpadding="0" width="99%">
		<tr>
			<td align="right" nowrap colspan="2">
				<input type="hidden" name="STEP" value="<?echo $STEP + 1;?>">
				<input type="hidden" name="lang" value="<?echo htmlspecialchars($lang) ?>">
				<input type="hidden" name="ACT_FILE" value="<?echo htmlspecialchars($_REQUEST["ACT_FILE"]) ?>">
				<input type="hidden" name="ACTION" value="<?echo htmlspecialchars($ACTION) ?>">

				<?if ($STEP > 1):?>
					<input type="hidden" name="IBLOCK_ID" value="<?echo $IBLOCK_ID ?>">
					<input type="hidden" name="URL_DATA_FILE" value="<?echo htmlspecialchars($DATA_FILE_NAME) ?>">

				<?endif;?>


				<?
				if ($STEP > 2)
				{
					?>
					<input type="hidden" name="fields_type" value="<?echo htmlspecialchars($fields_type) ?>">
					<?if ($fields_type == "R"):?>
						<input type="hidden" name="delimiter_r" value="<?echo htmlspecialchars($delimiter_r) ?>">
						<input type="hidden" name="delimiter_other_r" value="<?echo htmlspecialchars($delimiter_other_r) ?>">
						<input type="hidden" name="first_names_r" value="<?echo htmlspecialchars($first_names_r) ?>">
					<?else:?>
						<input type="hidden" name="metki_f" value="<?echo htmlspecialchars($metki_f) ?>">
						<input type="hidden" name="first_names_f" value="<?echo htmlspecialchars($first_names_f) ?>">
					<?endif;?>
					<?
					$fieldsString = "";
					for ($i = 0; $i < count($arDataFileFields); $i++)
						$fieldsString .= ",field_".$i;
					?>
					<input type="hidden" name="SETUP_FIELDS_LIST" value="IBLOCK_ID,URL_DATA_FILE,fields_type,delimiter_r,delimiter_other_r,first_names_r,metki_f,first_names_f,PATH2IMAGE_FILES,outFileAction,inFileAction,max_execution_time<?= $fieldsString ?>">
					<?
				}
				?>


				<?if ($STEP > 1):?>
                              <br>&nbsp;
				<input type="submit" name="backButton" value="&lt;&lt; <?echo GetMessage("CATI_BACK") ?>">
				<?endif?>
				<input type="submit" value="<?echo ($STEP==5) ? (($ACTION=="IMPORT") ? GetMessage("CATI_NEXT_STEP_F") : GetMessage("CICML_SAVE")) : GetMessage("CATI_NEXT_STEP")." &gt;&gt;" ?>" name="submit_btn">

				</td>
		  </tr>
	</table>
<?endif;?>
</form>