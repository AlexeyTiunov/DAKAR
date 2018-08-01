<?php
// Объявления констант для доработок Автодок

       //  Код инфоблока "Регионы"
define("CAD_IB_REGIONS_NUM", "17");

   //  Код инфоблока "Бренды"
define("CAD_IB_BRANDS_NUM", "14");

   //  Код инфоблока "Товарный каталог Автодок"
define("CAD_IB_ITEMS_NUM", "20");

   //  Код инфоблока "Цены"
define("CAD_IB_PRICES_NUM", "18");


   //  Код инфоблока "Платежи"
define("CAD_IB_PAYMENTS_NUM", "24");

   //  Код инфоблока "Отгрузки"
define("CAD_IB_SHIPPINGS_NUM", "25");

//  Код инфоблока "Строки отгрузок"
define("CAD_IB_SHIPPING_LINES_NUM", "26");


    //  Инфоблок "Бренды"

    // имя поля таблицы b_iblock_element_prop_sN, где физически храниться "Код бренда" (к примеру,ТY)
define("CAD_TBL_IP_SHORT_BRAND_CODE", "PROPERTY_71");
	// имя поля таблицы b_iblock_element_prop_sN, где физически храниться "Код бренда" (к примеру,999)
define("CAD_TBL_IP_INT_BRAND_CODE", "PROPERTY_72");


    //  Имена и описания свойств товарного каталога Автодок

    //  Свойство "Код бренда"
define("CAD_IP_BRAND_CODE", "IP_PROP120");
define("CAD_IP_BRAND_CODE_DESC", "Бренд");
    // имя поля таблицы b_iblock_element_prop_sN, где физически храниться "Код бренда"
define("CAD_TBL_IP_BRAND_CODE", "PROPERTY_120");

    //  Свойство "Вес"
define("CAD_IP_WEIGHT", "IP_PROP135");
define("CAD_IP_WEIGHT_DESC", "Вес");

    //  Свойство "Код товара по каталогу бренда"
define("CAD_IP_ITEM_CODE", "IP_PROP136");
define("CAD_IP_ITEM_CODE_DESC", "Код товара по каталогу бренда");
    // имя поля таблицы b_iblock_element_prop_sN, где физически храниться "Код товара по каталогу бренда"
define("CAD_TBL_IP_ITEM_CODE", "PROPERTY_136");


    // Колонка с ценой
define("CAD_IP_PRICE", "CAD_IP_PRICE");
define("CAD_IP_PRICE_DESC", "Цена");

    //  Поле "Название"
define("CAD_IE_NAME", "IE_NAME");
define("CAD_IE_NAME_DESC", "Название");


    //   Свойства инфоблока "Регионы"

             // срок доставки
define("CAD_IP_DELIVERY_TERM", "PROPERTY_95");
             // исмвольный код валюты ( aka USD )
define("CAD_IP_CURRENCY_CODE", "PROPERTY_189");
            // короткое имя региона
define("CAD_IP_SHORT_NAME", "PROPERTY_93");
            // код региона
define("CAD_IP_CODE", "PROPERTY_92");

      //  Значение "выбрать все регионы" для выпадающих списков
define ("ALL_REGIONS", '0');
      //  Значение "выбрать все регионы" для выпадающих списков
define ("ALL_BRANDS", '0');

      //  Номер региона "Собственный склад"
define ("OWN_WAREHOUSE", '1');
      //  Лимит строк во временной таблице-кэше результатов поиска
define ("CACHE_LINES_LIMIT", '200');





    //   Свойства инфоблока "Цены"

    //  Имена полей

define("CAD_IP_TBLPRICE_BRAND_CODE", "PROPERTY_101");
define("CAD_IP_TBLPRICE_ITEM_CODE", "PROPERTY_102");
define("CAD_IP_TBLPRICE_REGION_CODE", "PROPERTY_103");
define("CAD_IP_TBLPRICE_PRICE", "PROPERTY_104");
define("CAD_IP_TBLPRICE_PRESENCE", "PROPERTY_105");
define("CAD_IP_TBLPRICE_EXTITEMID", "PROPERTY_188");

         //  колонка прайса, в которой розничная цена живет
define("RETAIL_PRICE_COL", 10);

        //  по сколько примерно строк резать загружаемые прайсы
define( "MAX_LINES", 300000);


function detect_line_ending($file)
{
    $s = file_get_contents($file, false, NULL,0,10000);
    if( empty($s) ) return null;

    $cntRN = substr_count( $s,  "\r\n" );
    $cntR = substr_count( $s,  "\r" );
    $cntN = substr_count( $s,  "\n" );

    if( ($cntRN >= $cntR) && ($cntRN >= $cntN) )
       return '\r\n'; //Win

    if( ($cntR >= $cntRN) && ($cntR >= $cntN) )
       return '\r'; //Mac

    return '\n'; //Unix
}

     //  Возвращает id brandcode по его буквенному представлению  ( i.e. "TY" )

function  GetBrandCodeByCHR( $brand )
{
   global $DB;

   if( $brand == "ACURA" )
     $brand = "0A";


   $sql = "SELECT IBLOCK_ELEMENT_ID FROM  b_iblock_element_prop_s".CAD_IB_BRANDS_NUM;
   $sql .= " WHERE ".CAD_TBL_IP_SHORT_BRAND_CODE." = '".$brand."'";


   $rRes = $DB->Query($sql);

   if ( $arRes = $rRes->Fetch() )
     return $arRes["IBLOCK_ELEMENT_ID"];
   else
     return false;
}

     //  Возвращает  буквенное представление бренда  ( i.e. "TY" ) по ИД

function  GetCHRBrandCodeByID( $ID )
{
   global $DB;

   $sql = "SELECT ".CAD_TBL_IP_SHORT_BRAND_CODE." as CHR_BRAND_CODE FROM  b_iblock_element_prop_s".CAD_IB_BRANDS_NUM;
   $sql .= " WHERE IBLOCK_ELEMENT_ID = '".$ID."'";


   $rRes = $DB->Query($sql);

   if ( $arRes = $rRes->Fetch() )
     return $arRes["CHR_BRAND_CODE"];
   else
     return false;
}


     //  Возвращает массив брендов

function  GetAllBrandsProperties()
{
   global $DB;

   $sql = "SELECT IBLOCK_ELEMENT_ID AS ID,".CAD_TBL_IP_SHORT_BRAND_CODE." as CHR_BRAND_CODE FROM  b_iblock_element_prop_s".CAD_IB_BRANDS_NUM;



   $res = $DB->Query( $sql );

   $flag = false;
   while( $arRes = $res->Fetch()  )
   {
      $resArr[strtoupper($arRes["CHR_BRAND_CODE"])] = $arRes["ID"];
      $flag = true;

   }

    if( $flag ) return $resArr;
    else return false;
}
function  GetAllBrandsPropertiesFromFullName()
{
   global $DB;

   $sql = "SELECT IBLOCK_ELEMENT_ID AS ID,".CAD_TBL_IP_INT_BRAND_CODE." as CHR_BRAND_CODE FROM  b_iblock_element_prop_s".CAD_IB_BRANDS_NUM;



   $res = $DB->Query( $sql );

   $flag = false;
   while( $arRes = $res->Fetch()  )
   {
      $resArr[strtoupper($arRes["CHR_BRAND_CODE"])] = $arRes["ID"];
      $flag = true;

   }

    if( $flag ) return $resArr;
    else return false;
}
function  GetAllBrandsNameFromID()
{
   global $DB;

   $sql = "SELECT IBLOCK_ELEMENT_ID AS ID,".CAD_TBL_IP_INT_BRAND_CODE." as CHR_BRAND_CODE FROM  b_iblock_element_prop_s".CAD_IB_BRANDS_NUM;



   $res = $DB->Query($sql);

   $flag = false;
   while( $arRes = $res->Fetch()  )
   {
      $resArr[$arRes["ID"]] = $arRes["CHR_BRAND_CODE"];
      $flag = true;

   }

    if( $flag ) return $resArr;
    else return false;
}


      //  Возвращает наименование бренда по его текстовому коду
function GetBrandCaptionByCHR( $brandCHR )
{


  return $brandCHR;
}

   //  Возвращает массив наименование бренда ID
function GetBrandCaptionsArray( )
{

        $arBrands = Array();

        $MY_IBLOCK_ID = CAD_IB_BRANDS_NUM;   //  Код инфоблока "Бренды"
        $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));
        while($arItem = $items->GetNext())
          {
             $arBrands[$arItem["ID"]] = $arItem["NAME"];
          }

       return $arBrands;
}



            //  Возвращает двумерный массив значений свойств всех регионов в виде
            //  array[region_code][code] , где code - chrCurrencyCode, DeliveryDays, ShortName, Caption, chrCurrencyCode

function GetAllRegionsProperties()
{
    global $DB;

    $MY_IBLOCK_ID = CAD_IB_REGIONS_NUM;   //  Код инфоблока "Регионы"

    $items = GetIBlockElementList($MY_IBLOCK_ID,false, Array("NAME"=>"ASC"));



    while($arItem = $items->GetNext())
      {
         $elem = GetIBlockElement($arItem["ID"]);

               //  заполняем массив свойств каждого региона
         $arRegions[ $elem['PROPERTIES']['Code']['VALUE'] ]["chrCurrencyCode"] = $elem['PROPERTIES']['chrCurrencyCode']['VALUE'];
         $arRegions[ $elem['PROPERTIES']['Code']['VALUE'] ]["DeliveryDays"] = $elem['PROPERTIES']['OrderDays']['VALUE'];
         $arRegions[ $elem['PROPERTIES']['Code']['VALUE'] ]["ShortName"] = $elem['PROPERTIES']['ShortName']['VALUE'];
         $arRegions[ $elem['PROPERTIES']['Code']['VALUE'] ]["Caption"] = $elem['PROPERTIES']['Caption']['VALUE'];
      }

    if ( isSet($arRegions) )
      return $arRegions;
    else
      return false;
}



            //  Возвращает двумерный массив значений свойств всех регионов в виде
            //  array[region_code][code] , где code - chrCurrencyCode, DeliveryDays, ShortName, Caption, chrCurrencyCode

function GetAllRegionsProperties2()
{
    global $DB;

    $MY_IBLOCK_ID = CAD_IB_REGIONS_NUM;   //  Код инфоблока "Регионы"

    $sql = "SELECT * FROM b_iblock_element_prop_s".$MY_IBLOCK_ID;

    $rRes = $DB->Query( $sql );

    while( $arRes = $rRes->Fetch() )
      {
                        //  заполняем массив свойств каждого региона
         $arRegions[ $arRes['PROPERTY_92'] ]["chrCurrencyCode"] = $arRes['PROPERTY_189'];
         $arRegions[ $arRes['PROPERTY_92'] ]["DeliveryDays"] = $arRes['PROPERTY_95'];
         $arRegions[ $arRes['PROPERTY_92'] ]["ShortName"] = $arRes['PROPERTY_93'];
         $arRegions[ $arRes['PROPERTY_92'] ]["Caption"] = $arRes['PROPERTY_94'];

      }


    if ( isSet($arRegions) )
      return $arRegions;
    else
      return false;
}




    //  Возвращает наименование товара по его ICode, BCode

function GetItemCaption( $ICode, $BCode )
{
   global $DB;
       //  Сначала ищем в товарах с собственного склада
   $sql = "SELECT Caption FROM b_autodoc_items_1c_m ";
   $sql .= " WHERE ItemCode ='".$ICode."' AND BrandCode = '".$BCode."'";

   $rRes = $DB->Query($sql);

   if ( $arRes = $rRes->Fetch() )
     return $arRes["Caption"];
   else
     {
        $sql = "SELECT Caption FROM b_autodoc_items_m";
        $sql .= " WHERE ItemCode ='".$ICode."' AND BrandCode = '".$BCode."'";

        $rRes = $DB->Query($sql);
		   if ( $arRes = $rRes->Fetch() )
		     return $arRes["Caption"];
		   else
             return "";
     }
}

    //  Возвращает вес товара по его ICode, BCode

function GetItemWeight( $ICode, $BCode )
{
   global $DB;
       //  Сначала ищем в товарах с собственного склада
   $sql = "SELECT Weight FROM b_autodoc_items_1c_m ";
   $sql .= " WHERE ItemCode ='".$ICode."' AND BrandCode = '".$BCode."'";

   $rRes = $DB->Query($sql);

   if ( $arRes = $rRes->Fetch() )
     return $arRes["Weight"];
   else
     {
        $sql = "SELECT Weight FROM b_autodoc_items_m";
        $sql .= " WHERE ItemCode ='".$ICode."' AND BrandCode = '".$BCode."'";

        $rRes = $DB->Query($sql);
		   if ( $arRes = $rRes->Fetch() )
		     return $arRes["Weight"];
		   else
             return 0;
     }
}



        // возвращает коэффициент цен для текущего юзера и заданного региона
function GetPriceCoeff4Region( $regionID )
{

  if( $regionID == OWN_WAREHOUSE )
    return 1;

  global $DB;
  global $USER;

  if( $USER->IsAuthorized() )
    {
	  $UID = $USER->GetID();
	  $ID_1C = GetUserID_1CByID( $UID );
    }
  else
    $ID_1C = '00001';


	  $sql = "SELECT K FROM b_autodoc_clients_k ";
	  $sql .= " WHERE ID_1C='".$ID_1C."'";
	  $sql .= " AND RegionCode='".$regionID."'";

	  $rRes = $DB->Query($sql);

	   if ( $arRes = $rRes->Fetch() )
	     return $arRes["K"];
	   else      //  если нет коэффициента цен для текущего клиента
	     {
	             //  если юзер - не розничный покупатель, то превращаем его в розничного
	       if( $ID_1C != '00001' )
	         $ID_1C = '00001';

           $sql = "SELECT K FROM b_autodoc_clients_k ";
		   $sql .= " WHERE ID_1C='".$ID_1C."'";
		   $sql .= " AND RegionCode='".$regionID."'";

		   $rRes = $DB->Query($sql);

              if ( $arRes = $rRes->Fetch() )
             return $arRes["K"];


         }
}
       // Обновляем в таблице подтверждений выгрузок на сайт $tag, $Value и перегенирируем файл confirm.xml
function UpdateConfirms( $Tag, $Value )
{
   global  $DB;
      //  Ищем тэг, если его нет - создаем

   $sql = " SELECT COUNT(id) as cnt FROM b_autodoc_confirms";
   $sql .= " WHERE Tag='".$Tag."'";

   $res = $DB->Query($sql);

   $arRes = $res->Fetch();

   if( $arRes["cnt"] == 0 )
     {
       $sql = "INSERT INTO `b_autodoc_confirms` (`Tag`, `Value`, `Date`)";
       $sql .= "VALUES( '".$Tag."', ".intval( $Value ).", Now())";
       $res = $DB->Query($sql);

     }
   else
     {
        $sql = "UPDATE `b_autodoc_confirms`";
        $sql .= " SET Value='".intval( $Value )."', Date = Now() ";
        $sql .= " WHERE Tag='".$Tag."'";
        $res = $DB->Query($sql);
     }

   $str = "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">\n";;

   $sql = " SELECT * FROM b_autodoc_confirms ORDER BY Tag ASC";
   $res = $DB->Query($sql);
   $str .= "<Документ>"."\n";
   while( $arRes = $res->Fetch())
    {
       $str .= "  <".$arRes["Tag"].">".$arRes["Value"]."</".$arRes["Tag"].">"."\n";
    }

   $str .= "</Документ>";

   $str = iconv("UTF-8", "WINDOWS-1251" , $str);
   file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/confirm.xml',$str);

}

      // функция выгружает массив в файл - для тестирования
function my_logger($ar)
{
  $sstr = print_r($ar,true);


  $fp = fopen ($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/log.tmp', "a+");
  fwrite ($fp, $sstr);
  fclose ($fp);


}

    //  Возвращает ID для данного айди юзера из 1С
function GetUserIDByID_1C( $ID_1C )
{

  global $DB;
  $sql = "SELECT ID FROM b_user WHERE ID_1C='".$ID_1C."'";

  $res = $DB->Query( $sql );

  if( $arRes = $res->Fetch() )
    return $arRes["ID"];
  else
    return false;
}

    //  Возвращает айди юзера из 1С для данного ID
function GetUserID_1CByID( $ID )
{

  global $DB;
  $sql = "SELECT ID_1C FROM b_user WHERE ID='".$ID."'";

  $res = $DB->Query( $sql );

  if( $arRes = $res->Fetch() )
    return $arRes["ID_1C"];
  else
    return false;
}

     // Есть ли юзер с данным айди из 1С в базе сайта

function IsUserPresent( $ID_1C )
{
  global $DB;

  $sql = "SELECT count(*) as CNT FROM b_user ";
  $sql .= " WHERE ID_1C='".$ID_1C."'";

  $res = $DB->Query( $sql );
  $arRes = $res->Fetch();

  if( $arRes["CNT"] > 0 )
    return true;
  else
    return false;
}
       // Возвращает список договоров текущего юзера
function GetAgreementsList()
{
   global $USER, $DB;

   $UID = GetUserID_1CByID( $USER->GetID() );

   $sql = "SELECT * FROM b_autodoc_agreements";
   $sql .= " WHERE ClientCode='".$UID."'";

   $res = $DB->Query( $sql );


   $i = 0;
   while( $arRes = $res->Fetch() )
     {
     	$arAgr[$i] = $arRes;

     	$i++;
     }

  if( isset( $arAgr ) )
    return $arAgr;
  else
    return false;

}
      //  Возвращает общее количество записей во временной таблице текущего юзера
function GetTotalTmpRecs()
{
   global $DB, $USER;

   $UID = GetUserID_1CByID( $USER->GetID() );

   $sql = "SELECT COUNT(*) AS CNT FROM b_autodoc_tmp_".$UID;
   $res = $DB->Query( $sql );

   if( $arRes = $res->Fetch()  )
     return $arRes["CNT"];
   else
     return false;

}
        //  Возвращает обработанный ICode ( верхний регистр, - лишние "левые" символы )
function PrepareICode( $icode )
{
  global $DB;

  $sql = "SELECT prepare_icode('".$icode."') AS icode";
   $res = $DB->Query( $sql );

   if( $arRes = $res->Fetch()  )
     return $arRes["icode"];
   else
     return false;


}

	                   // меняем цены в соответствии с выбранной юзером валютой и коэффициентом цен для данного юзера

function RecalcPrices( $tmpTblName )
{
  global $DB;

	       $sql = " SELECT ID,ItemCode, BrandCode, Price, RegionCode, Currency FROM ".$tmpTblName;

	       $tmpRes = $DB->Query($sql);

	       while( $arRes = $tmpRes->Fetch())
	         {

	            if( $arRes["RegionCode"] != OWN_WAREHOUSE )
	              $userPrice = $arRes["Price"] * GetPriceCoeff4Region($arRes["RegionCode"]);
	            else
	              $userPrice = $arRes["Price"];
	                  //  Пересчитываем в выбранную юзером валюту

	            $userPrice = CCurrencyRates::ConvertCurrency( $userPrice, $arRes["Currency"], $_REQUEST["CURRENCY"] );

	            $tSql = "UPDATE ".$tmpTblName;
	            $tSql .= " SET UserPrice='".$userPrice."'";
	            $tSql .= " WHERE ID='".$arRes["ID"]."'";

	         	$tmpRes2 = $DB->Query($tSql);

	         }

}

     //  Возвращает номер колонки цен собственного склада для текущего юзера
     //  если номер колонки для данного юзера не задан или юзер не залогинен,
     //   возвращает колонку 10 - розничные цены

function GetPriceColNumber(  )
{   global $USER, $DB;

   if ( $USER->IsAuthorized())
     {
        $sql = "SELECT PriceColumn_1C FROM b_user";
        $sql .= " WHERE ID='".$USER->GetID()."'";

        $res = $DB->Query( $sql );
        $arRes = $res->Fetch();
        if( $arRes["PriceColumn_1C"] )
          return $arRes["PriceColumn_1C"];
     }

   return RETAIL_PRICE_COL;
}


         //  Возвращает код региона для заказа

function GetRegionCodeForOrder( $orderID )
{
   global $DB;

   $sql = "SELECT REGION_CODE FROM b_sale_order WHERE ID='".$orderID."'";

   $res = $DB->Query( $sql );

   if( $arRes = $res->Fetch()  )
     return $arRes["REGION_CODE"];
   else
     return false;

}
       //  Обрабатываем строку, дабы избежать SQL-injection
function PrepareForSQL( $str )
{
   if( get_magic_quotes_gpc() )
     $str = stripslashes( $str ) ;

   return mysql_real_escape_string( $str );


}


     //   удаляем дубли по ICode - Bcode во временной таблице
     //  оставляем жить товар с максимальной ценой
     //  возвращает количсетво удаленных записей


function RemoveDupRecs()
{    global $DB;



	      // вытаскиваем дубли
	 $sql = " SELECT count( * ) AS cnt, id , `BrandCode` , `ItemCode` , `RegionCode` ";
	 $sql .= " FROM `b_autodoc_import_temp` ";
     $sql .= " GROUP BY `BrandCode` , `ItemCode` , `RegionCode` ";
     $sql .= " HAVING (cnt) >1 ";

     $resDub = $DB->Query( $sql );
     $cnt = 0;

     while( $arDub = $resDub->Fetch() )
       {
	         // ищем среди дублирующихся эелемент с максимальной ценой

	     $maxPrice = 0;
	     $maxPriceID = 0;

       	 $sql = " SELECT * FROM `b_autodoc_import_temp` ";
       	 $sql .= " WHERE `BrandCode` = '".$arDub["BrandCode"]."' ";
       	 $sql .= " AND `ItemCode`='".$arDub["ItemCode"]."' ";
       	 $sql .= " AND `RegionCode` = '".$arDub["RegionCode"]."'";

         $resRec = $DB->Query( $sql );
         $arRec = $resRec->Fetch();

         if( $arRec["Price"] > $maxPrice )
           {
              $maxPrice = $arRec["Price"];
              $maxPriceID = $arRec["id"];
           }

               //  вырезаем все дублирующиеся записи кроме записи с максимальной ценой



		 if( $maxPriceID > 0 )
		   {		      $sql = " DELETE FROM `b_autodoc_import_temp` ";
		      $sql .= " WHERE `BrandCode` = '".$arDub["BrandCode"]."' ";
		      $sql .= " AND `ItemCode`='".$arDub["ItemCode"]."' ";
		      $sql .= " AND `RegionCode` = '".$arDub["RegionCode"]."'";
		      $sql .= " AND id <> ".$maxPriceID ;
              $DB->Query( $sql );

		      $cnt ++;
		   }
       }

     return $cnt;
}

     //  Получает список файлов, загруженных внешним флешовым загрузчиком
     //
     //   1. Если файл не зипованный - переносит его в папку /upload/data
     //   2. Если файл - зипованный, разархивируем его в /upload/data
     //

define("FLASH_UPLOADER_PATH",$_SERVER["DOCUMENT_ROOT"].'/autodoc/test_upload/upload/');
define("UPLOADER_PATH",$_SERVER["DOCUMENT_ROOT"].'/upload/data/');

function PreprocessLoadedFiles( $arFileNames )
{
    $arResFNames = Array();

    for( $i = 0; $i < count( $arFileNames ); $i++ )
      {      	 $ext = strtolower( pathinfo($arFileNames[$i], PATHINFO_EXTENSION) );
      	   if( $ext == "zip" )
      	     {                $za = new ZipArchive();
                $za->open( FLASH_UPLOADER_PATH.$arFileNames[$i] );
                for( $j = 0; $j < $za->numFiles; $j++ )
                  {

                     $finfo = Array();
                     $finfo = $za->statIndex($j);
                        //   size - Размер
                     $za->extractTo(UPLOADER_PATH, $finfo["name"]);

			         $lines = getLinesCount(UPLOADER_PATH . $finfo["name"]);
			           if( $lines > MAX_LINES )
			             {
			               $arResFNames = array_merge( $arResFNames, splitFile(UPLOADER_PATH . $finfo["name"] , MAX_LINES ) );
			               unlink( UPLOADER_PATH . $finfo["name"] );
			             }
			            else
                          $arResFNames[] = $finfo["name"];
                  }
                $za->close();
                unlink(FLASH_UPLOADER_PATH.$arFileNames[$i]);
      	     }
      	   else
      	     {                rename(FLASH_UPLOADER_PATH.$arFileNames[$i],UPLOADER_PATH.$arFileNames[$i]) ;

      	     	if( getLinesCount( UPLOADER_PATH.$arFileNames[$i] )  > MAX_LINES )
                  {

                    $arResFNames = array_merge( $arResFNames, splitFile( UPLOADER_PATH.$arFileNames[$i] , MAX_LINES ) );
			        unlink( UPLOADER_PATH.$arFileNames[$i] );

      	     	  }
      	     	else
      	     	  {
      	     	    $arResFNames[] = $arFileNames[$i];
                  }
      	     }
      }

    return $arResFNames;

}
    //  считаем количсетво строк в файле
function getLinesCount( $fpath )
{	return intval( exec( 'wc -l '. $fpath) );
}

     // режем текстовый файл на кусочки
     //  возвращает массив имен файлов-кусков
function splitFile( $fpath, $num_lines )
{

      //  корректируем количсетво строк разбиения
      // чтоб в каждом файле было примерно равное кол-во строк ( до 1.5 * $num_lines )

  $num_parts = round( getLinesCount( $fpath ) / $num_lines );
  $num_lines = intval( getLinesCount( $fpath )/$num_parts + 1 );




  $arFNames = Array();
  $prefix = pathinfo($fpath, PATHINFO_FILENAME);
  $ext = pathinfo($fpath, PATHINFO_EXTENSION);
  $wrkDir = pathinfo( $fpath, PATHINFO_DIRNAME ) . '/';

  $i = 0;
  $j = -1;

  $handle = @fopen($fpath, "r");
	if ($handle) {
	    while (!feof($handle))
	    {
	        $buffer = fgets($handle, 4096);

	        if( $i == 0 )
	          {
	            $j++;
	            if( isset( $handleOut ) )
	              fclose( $handleOut );

                $fname = $arFNames[] = $prefix.'.'.$j.'.'.$ext;
                $fname = $wrkDir . $fname;
	            if (!$handleOut = fopen( $fname, 'w'))
	             {
	               echo "Cannot open file ($fname)";
	               exit;
	             }
	          }


	        if( ++$i == $num_lines ) $i = 0;

	        if (fwrite($handleOut, $buffer) === FALSE) {
	        echo "Cannot write to file ($fname)";
	        exit;
	    }


	    }
	    fclose($handle);
	    return $arFNames;
	}
  return false;}
        //  Возвращает массив данных для проценки  ( название, цена для данного клиента и т.п. )
        //  если товар не найден, возвращается строка с описанием ошибки
function getItemData( $chrBrandName, $itemCode, $chrRegionCode  )
  {
    global $DB;
     $result = Array();

     $result['CHR_BRAND_CODE'] = $chrBrandName;
	 $result['CHR_ITEM_CODE'] = $itemCode;
	 $result['CHR_REGION_CODE'] = $chrRegionCode;


     $result['ITEM_CODE'] = $itemCode = PrepareICode( $itemCode);

     $brandCode = GetBrandCodeByCHR( $chrBrandName );
     if( !$brandCode )
       {
         $result['ERROR_MESSAGE'] = 'Не найден код бренла '.$chrBrandName;
         return $result;
       }

     $regionCode = -1;
     switch( strtoupper( trim( $chrRegionCode ) ) )
       {
          case 'US' :
          case 'USA' :            $regionCode = 4;
            break;
          case 'DE' :
          case 'EU' :
            $regionCode = 3;
            break;
          case 'AR' :
            $regionCode = 2;
            break;
          case 'UA' :
            $regionCode = OWN_WAREHOUSE;
            break;
       }

     if( $regionCode == -1 )
       {         $result['ERROR_MESSAGE'] = 'Не найден регион с кодом '.$chrRegionCode ;
         return $result;
       }


     $arRegions = GetAllRegionsProperties();

             //  результирующий массив



     // ищем название ( caption ) товара

     if( $regionCode == OWN_WAREHOUSE )
       {       	  $sql = " SELECT * FROM b_autodoc_items_1c_m ";
       	  $sql .= " WHERE `BrandCode` = ".$brandCode;
       	  $sql .= " AND itemcode='".$itemCode."'";
       	  $sql .= " AND Presence = 1 ";

       	  $res = $DB->Query( $sql );
       	  if( $arRes = $res->Fetch() )
       	    {               $result['CAPTION'] = $arRes['Caption'];

                       // Колонка складских цен для данного юзера

               $priceCol = GetPriceColNumber();

               $result['PRICE'] = 0;

               if( $priceCol != RETAIL_PRICE_COL )
                 {
                                   //  Если задана колонка цены для данного юзера, то сначала ставим цены из этой колонки
                                   // ( где возможно )

                 	$sql = "SELECT Price FROM b_autodoc_wh_prices_m ";
                 	$sql .= " WHERE ItemCode = '".$itemCode."'";
	                $sql .= " AND BrandCode = '".$brandCode."' ";
	                $sql .= " AND PriceCode = '".$priceCol."'";

                    $res = $DB->Query($sql);
                    if( $arRes = $res->Fetch() )
                      $result['PRICE'] = $arRes['Price'];
                 }

                            //  если цена все еще нулевая - берем розничную цену
                if( $result['PRICE'] == 0 )
                  {
	                $sql = "SELECT Price FROM b_autodoc_wh_prices_m ";
                 	$sql .= " WHERE ItemCode = '".$itemCode."'";
	                $sql .= " AND BrandCode = '".$brandCode."' ";
	                $sql .= " AND PriceCode = '".RETAIL_PRICE_COL."'";

	                $res = $DB->Query($sql);
                    if( $arRes = $res->Fetch() )
                      $result['PRICE'] = $arRes['Price'];
                  }

                if( $result['PRICE'] == 0 )
                  {                    $result['ERROR_MESSAGE'] = 'Для данного товара не найдена цена';
                    return $result;
                  }


			    $result['CHR_BRAND_CODE'] = $chrBrandName;
			    $result['BRAND_CODE'] = $brandCode;
			    $result['CHR_REGION_CODE'] = $chrRegionCode;
			    $result['REGION_CODE'] = $regionCode;
			    $result['REGION_CURRENCY_CODE'] = $arRegions[$regionCode]["chrCurrencyCode"];

                return $result;
       	    }
       	  else
            {
               $result['ERROR_MESSAGE'] = 'Данный артикул не найден на складе';
               return $result;
            }


       }
     else
       {                //  Товар - не с собственного склада

          $sql = " SELECT * FROM b_autodoc_items_m ";
       	  $sql .= " WHERE `BrandCode` = ".$brandCode;
       	  $sql .= " AND itemcode='".$itemCode."'";

       	  $res = $DB->Query( $sql );
       	  if( $arRes = $res->Fetch() )
       	    {
               $result['CAPTION'] = $arRes['Caption'];

                       // Колонка складских цен для данного юзера

               $priceCoeff = GetPriceCoeff4Region( $regionCode );

               $result['PRICE'] = 0;


               $sql = "SELECT Price FROM b_autodoc_prices_m ";
               $sql .= " WHERE ItemCode = '".$itemCode."'";
	           $sql .= " AND BrandCode = '".$brandCode."' ";
	           $sql .= " AND RegionCode = '".$regionCode."'";

               $res = $DB->Query($sql);

               if( $arRes = $res->Fetch() )
                  $result['PRICE'] = $arRes['Price'] * $priceCoeff;

                if( $result['PRICE'] == 0 )
                  {
                    $result['ERROR_MESSAGE'] = 'Для данного товара не найдена цена';
                    return $result;
                  }

			    $result['CHR_BRAND_CODE'] = $chrBrandName;
			    $result['BRAND_CODE'] = $brandCode;
			    $result['CHR_REGION_CODE'] = $chrRegionCode;
			    $result['REGION_CODE'] = $regionCode;
			    $result['REGION_CURRENCY_CODE'] = $arRegions[$regionCode]["chrCurrencyCode"];

                return $result;

            }
          else
            {
               $result['ERROR_MESSAGE'] = 'Данный артикул не найден';
               return $result;
            }
       }
  }


?>