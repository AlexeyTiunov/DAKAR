<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG4.php"); 
#die("NO SERVICE");
set_time_limit(0);  
error_reporting (E_ERROR); 

global $DB;
global $USER;
$idgr=$USER->GetUserGroupArray();
#print_r($idgr)    ;
foreach ($idgr as $id=>$i)
{
   
    if ($i==1 || $i==7)
    {  #echo'www';
      $idgrch=true; 
      break;   
    } else
    {
          $idgrch=false ;
    }
} 
if(!$USER->IsAuthorized()|| $idgrch!=true) 
{
    $APPLICATION->SetTitle("Нет доступа");
    die();
} else
  {
      $APPLICATION->SetTitle("Загрузка прайса");
  }
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
   function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","dakar",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }
 function tablestrload ($flname, $strld, $unstr,$DB)
 {
     echo'<tr>
     <td ><div align="center"><input type="checkbox"  name="load" value="yes" align="right" ></div></td>
     <td ><div align="center"><p style="color:black;">'.$flname.'</p></div></td>
     <td ><div align="center"><input type="checkbox"  name="delprev" value="yes" align="right" ></div></td> '; 
 echo'  
     <td ><div align="center"><select name="supplier"><option value=""></option>';
     $options = $DB->Query("SELECT
            PROPERTY_92 AS SupplierCode, PROPERTY_94 AS SupplierName
            FROM  b_iblock_element_prop_s17
            WHERE PROPERTY_233 IS NOT NULL AND PROPERTY_233 > 0
            ORDER BY PROPERTY_94 ASC");
        while($row = $options->Fetch())
        {
            echo '<option value="' . $row['SupplierCode'] . '">' . $row['SupplierName'] . '</option>';
        }
     
     
      
    echo' </select></div></td>  ';
    echo'  
     <td ><div align="center"><select name="curency"><option value="UAH">UAH</option> <option value="USD">USD</option> <option value="EUR">EUR</option> 
     </select></div></td>'; 
     echo '<td ><div align="center"><p style="color:black;">'.$strld.'</p></div></td> 
     <td ><div align="center"><p style="color:black;">'.$unstr.'</p></div></td>'; 
     
     echo '</tr>';
     echo '<tr>
                <td ><div align="center">Скидка<input type="text"  name="dis" size="5" maxlength="2" align="right" >%</div></td> 
                <td ><div align="center">Extra<input type="text"  name="extra" size="5" maxlength="2" align="right" >%</div></td> ';  ; 
     echo '</tr>';
     
    /* echo '<tr>
               <td ><div align="center"><p>Подключить класс</p><br><input type="checkbox" checked="checked" name="IncludeClass" value="yes" align="right" ></div></td>   
    
               <td ><div align="center"><p>Запись в ДБ</p><br><input type="checkbox"  name="WriteNameToDB" value="yes" align="right" ></div></td> 
               <td ><div align="center"><p>CSV-файл</p><br><input type="checkbox"  name="ReadyCSV" value="yes" align="right" ></div></td> ';  
     echo '</tr>';*/ 
      
 }  
 function branddeal($BrandCode)
 {
    $arBrandToKey = GetAllBrandsPropertiesFromFullName();
    $arShortBrandToKey = GetAllBrandsProperties();
    if ($arBrandToKey[$BrandCode] != '') $BrandCode = $arBrandToKey[$BrandCode];
                elseif ($arShortBrandToKey[$BrandCode] != '') $BrandCode = $arShortBrandToKey[$BrandCode];
                else//пробный поиск на чать полного наименования
                {
                    //echo "бренд не найден".strtoupper($item[$id['BrandCodeCol']])."**".$nameBrand."<br />";
                    $trigger = false;
                    foreach($arBrandToKey as $nameBrand=>$keyBrand)
                    {
                        $find = strpos($nameBrand,strtoupper($BrandCode));
                        if ($find !== false)
                        {
                            $BrandCode = $keyBrand;
                            $trigger = true;
                            //echo "записуем!!!";
                            break;//находим бренд выходим из цикла поиска бренда

                        }
                    }
                    //echo "нифига не записываем";
                    if($trigger) 
                    {
                         return $BrandCode ;
                    } else return $trigger;
                }
 }
 function brandsearch($BrandCode)
 {
     global $DB;
     if (strlen(trim($BrandCode))>2)
     {
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID FROM b_iblock_element_prop_s37 WHERE PROPERTY_288='".trim($BrandCode)."'";
           $result =$DB->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res['ID'];
            }else
            {
                include "BrandCorrection.php";
                if (array_key_exists(trim($BrandCode),$brandCorrection))
                {
                    #var_dump($brandCorrection[trim($BrandCode)]);
                    $sql="SELECT IBLOCK_ELEMENT_ID AS ID FROM b_iblock_element_prop_s37 WHERE PROPERTY_288='{$brandCorrection[trim($BrandCode)]}'";
                    $result =$DB->Query ($sql) ;
                    $res=$result->Fetch(); 
                    if ($res['ID']>0)
                        {
                            return $res['ID'];
                        }else
                        {
                          return false;  
                        }
                    
                    
                }
                return false;
            }   
         
     }
     /*elseif(strlen(trim($BrandCode))==2)
     {
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID FROM b_iblock_element_prop_s14 WHERE PROPERTY_71='".trim($BrandCode)."'";
           $result =$DB->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res['ID'];
            }else
            {
                return false;
            }   
          
     } */
     else
     {
         return false;
     }
     
 }
 function unloadedstrcsv($fln)
 {
     echo '<div id="st" style=" display:none;">Не Загрузились<table style="border:2px solid white">' ;
       echo'<tr>' ;
             echo'<th style="border:2px solid white;">Бренд</th>';
             echo'<th style="border:2px solid white;">Артикул</th>';    
             echo'<th style="border:2px solid white;">Наименование</th>';
             echo'<th style="border:2px solid white;">Цена</th>';
             echo'<th style="border:2px solid white;">Ошибка/Файл</th>';            
       echo'</tr>' ;
    $importfileUn=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/dbfileUn.csv",'r'); 
     while (($data = fgetcsv($importfileUn)) !== FALSE) 
     {   
         echo'<tr>' ;
         $num = count($data); 
         echo'<td style="border:2px solid white;">'.$data[0].'</td>';
         echo'<td style="border:2px solid white;">'.$data[1].'</td>';
         echo  '<td style="width:400px;border:2px solid white">'.$data[2].'</td>';
         echo '<td style="border:2px solid white;width:70px">'.$data[4].'</td>';
         echo  '<td style="border:2px solid white;">'.$data[$num-1].'/'.$fln.'</td>';
          echo'</tr>' ;  
     }
     
     
     echo '</table></div>' ;
     fclose($importfileUn);
 }
 function filedeal($flname,$supplier,$currency,$discount,$extra,$unrwr)
 
 {
  $strcsvsh = array(
  'BrandCd'=>' ',
  'ItemCode'=>' ',
  'SuppCode'=>' ',
  'Caption'=>' ',
  'Quantity'=>' ',
  'Price'=>' ',
   'Curency'=>' ',
   'LastUpdate'=>' '
  
  
  ) ;  
  $row = 0;
  $upline=0;
  $handle = fopen($flname, "r");
  $importfile=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/dbfile.csv",'w');
  $importfileUn=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/dbfileUn.csv",$unrwr); 
  $UahToUsdKoef =Search_ITG::PriceKoef($currency,"USD");   
  while (($data = fgetcsv($handle)) !== FALSE) 
  {               
      $num = count($data);
   # echo "<p> $num полей в строке $row: <br /></p>\n";
    #$row++; 
    $strcsv=$strcsvsh;
    for ($c=0; $c < $num; $c++) 
    { 
           # $first=false;
           # $second=false;
          # $third=false; 
         if ($c==0)
         {
            $BrandCode = str_ireplace('"', '',$data[$c]);
            #$brandcodenum= branddeal($BrandCode) ;
              $brandcodenum= brandsearch($BrandCode) ;
            if($brandcodenum!= false)
            {
               $strcsv['BrandCd']=$brandcodenum;
               if (preg_match("/\//",$data[$c+1]))
               {
                   $ItemCodeArray=explode('/',$data[$c+1]);
                   
                   $ItemCodeFirst=$ItemCodeArray[0];
                   
               }
               elseif(preg_match("/\,/",$data[$c+1]))
               {
                   $ItemCodeArray=explode(',',$data[$c+1]);
                   
                   $ItemCodeFirst=$ItemCodeArray[0];
                   
               } 
               else
               {
                   $ItemCodeArray=array();
                  $ItemCodeFirst= $data[$c+1];
               }
               $strcsv['ItemCode']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeFirst );
               $strcsv['SuppCode']=$supplier;
               #$strcsv['Caption']= $data[$c+2];
             $strcsv['Caption']= preg_replace("/[\",\']*/","",$data[$c+2] );
               #$strcsv['Caption']='"'.$data[$c+2].'"'; 
                $first=true;
            }
            else
            {
                $data['checkbr']  ='brand'      ;
                $first=false;   
                break;
            } 
            
         }
         if ($c==3)
         {
            #$quantity=((preg_replace("/[^0-9,]*/i","",$data[$c] ))=='')? 0: intval( preg_replace("/[^0-9,]*/i", "",$data[$c]) ) ;
            # $quantity=((preg_replace("/[^0-9,\,,.]*/i","",$data[$c] ))=='')? 0: ( preg_replace("/[^0-9,\,,.]*/i", "",$data[$c]) )*1 ;  
             $quantity=((preg_replace("/[^0-9,\,,.]*/i","",$data[$c] ))=='')? 0: ( preg_replace("/[^0-9,\,,.]*/i", "",$data[$c]) ) ; 
            #$quantity=$data[$c]=str_replace(",",'.',$data[$c])+0.00; 
             $quantity=str_replace(",",'.',$quantity)+0.00;
             
            #$quantity+=$data[$c];
            #if  ($quantity==0 || $quantity=='0') $quantity=0; 
            if ($quantity > 0)
            {
                
                $strcsv['Quantity']=$quantity;  
                 $third=true;
            }
            else
            {
                   $data['checkpr']  ='quantity' ;
                  $third=false; 
                 break; 
            }
         }
         if($c==4)
         {   #  $data[$c]+=0.00;
             #$data[$c]=str_replace("\n",'',$data[$c]); 
             $data[$c]=((preg_replace("/[^0-9,\,,.]*/i","",$data[$c] ))=='')? 0: ( preg_replace("/[^0-9,\,,.]*/i", "",$data[$c]) ) ;
             $data[$c]=str_replace(",",'.',$data[$c])+0.00;
             $data[$c]=$data[$c]+($data[$c]*($extra/100)); 
             $data[$c]=$data[$c]-($data[$c]*($discount/100));
           # $mprice= preg_replace("/[^0-9,\,,.]*/i","",$data[$c] );
            #$mprice+=0.00;
             if ($data[$c]>0)
             {    
             if ($currency=="UAH")
                 {
                  $strcsv['Curency']="USD";
                  $strcsv['Price']=$data[$c]*$UahToUsdKoef;  // $UahToUsdKoef =Search_ITG::PriceKoef($currency,"USD");
                  $strcsv['LastUpdate']=  date("Y-m-d H:i:s");
                  $second=true;
                     
                 } else
                 {
                  $strcsv['Curency']=$currency;
                  $strcsv['Price']=$data[$c];
                  $strcsv['LastUpdate']=  date("Y-m-d H:i:s");
                  $second=true;
                 }
             }
             else
             {
                 $data['checkpr']  ='price' ;
                $second=false; 
                break; 
             }
         }
        #echo $data[$c] . "<br />\n";
        
         
    }
    
         if ($first==true && $second==true && $third==true)
        {
              $upline++;
              #echo $upline;
              #fputcsv($importfile, $strcsv,',','"');
              if (count($ItemCodeArray)>1)
              {
                  foreach($ItemCodeArray as $ItemCode )
                  {
                     if (StrLen($ItemCode)<1)continue;
                    $strcsv['ItemCode']=preg_replace("/[^a-z0-9]*/i", "",$ItemCode);;
                    fputcsv($importfile, $strcsv,',','"');  
                  }
                  
              }else
              {
                fputcsv($importfile, $strcsv,',','"');   
              } 
        } 
        else
        {
                   fputcsv($importfileUn, $data,',','"');
        }
    #fputcsv($importfile, $strcsv,',','"');
    $row++;
  }
  fclose($importfile);
  fclose($handle);
  #echo $upline;
  #echo $row;
  $returnarr[0]=$upline;
  $returnarr[1]=$row-$upline;  
  return $returnarr;   
 }  
 function tablecreate($flname, $strld, $unstr,$DB)
 {
   echo '<form method="post" action="Loadprice.php" enctype="multipart/form-data" > ' ;
    echo '<div id="dv" style="display: block; border:4px double orange ; background:white;">
     <table>
      <tr>
         <th style="color: black;"> Загрузить</th>
         <th style="color: black;">Имя Файла</th> 
         <th style="color: black;">Удалить старый прайс </th>
         <th style="color: black;">Выбрать Поставщика </th>
         <th style="color: black;"> Выбрать Валюту </th>
         <th id="str"  style="color: black;">Внесено позиций </th>
         <th id="str"  style="color: black;"> Не внесено позиций </th>   
         
      </tr>';
     tablestrload($flname,$strld,$unstr,$DB); 
     
    echo'</table>
  
    </div>
     <input type="hidden"  name="check" value="yes" /> 
    <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/>  
    </form> ';  
 } 
 function mkscv($oldFile,$filename,$IncludeClass)
{

   # if ($oldFile=="/var/www/priceld/ServiceKoreaMotorsKIA.xls") # ServiceKoreaMotorsKIA.
    if ($IncludeClass)
    {   error_reporting (0); 
        ini_set('memory_limit', '4048M'); 
        require_once $_SERVER["DOCUMENT_ROOT"]."/personal/suppload/ClassesToLoadPrices/PriceLoadClass.php";
        $mynewfile =new PriceToLoad ($oldFile,$filename) ;
        #var_dump($mynewfile);
        #var_dump($mynewfile->ReturArray());
       
            echo  $mynewfile->ReturnErrorDef();
        
        
        #exit;
       #return $mynewfile->NewFileCSV;
        return $mynewfile;
    }
   # ini_set('memory_limit', '2048M');
    require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/excel/Classes/PHPExcel/IOFactory.php";
    $objPHPExcel = PHPExcel_IOFactory::load($oldFile);  
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $newFile = $oldFile . '.csv';
    $objWriter->save($newFile);
    return $newFile;
}
function WriteSupplierFileNameToDB($FileNameWithE,$SuppCode,$DB)
{
    $FileNameWOutEArray=explode('.',$FileNameWithE);
    $FileNameWOutE=$FileNameWOutEArray[0];
    $FileNameWOutBrandArray=explode('-',$FileNameWOutE);
    $SupplierFileName=$FileNameWOutBrandArray[0];
    $sql="UPDATE b_iblock_element_prop_s17 SET DESCRIPTION_94='{$SupplierFileName}' WHERE IBLOCK_ELEMENT_ID={$SuppCode}"; 
    $DB->Query($sql);
    
    
}
?>
<script type="text/javascript">
function selector() 
{
   if ( $("#st").css("display")=="none" )
   { 
    $("#st").css("display","block"); 
   }
   else 
   { 
    $("#st").css("display","none"); 
   }
   
}
</script>
<p style="color:red;" align="center">Загрузка одного файла.</p>
<!--<script>
alert( document.cookie );
</script> -->
 <table>
   <tr>
 
     <td><div align="left"><form method="post" action="Loadprice.php" enctype="multipart/form-data" >
    <input   type="file" name="file"  style='width:250px;'/>  <br> 
     <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/> 
     </form></div>
     </td> 
     <td>
        <div align="left" ><form method="post" action="Loadprice.php" enctype="multipart/form-data" > 
        <select style="width:250px ;"> <option>Выберите папку </option>
         <?
         $dir = "/var/www/priceld/";

// Открыть заведомо существующий каталог и начать считывать его содержимое
   
         
         ?>
        
        </select> <br>
        <input type="submit" id="submit" name="submit" value="Загрузить" style="  font-weight:bold;"/> 
        </form></div>
     </td>
    </tr> 
 </table>
 <!--<a href="LoadMorePrices.php">Загрузка прайсов пакетом.</a>--><br>
  <a href="LoadMorePricesUserConfig.php">Загрузка прайсов пакетом-NEW</a><br> 
 <a href="LoadMoreCrosses.php">Загрузка кроссов.</a> <br>
 <a href="LoadInternationPrice.php">Загрузка Иностранных прайсов</a> <br>   
 <?
 //die("NO SERVICE");
 /*<div id="dv" style="display: block; border:4px double orange ; background:white;  ">  
     
  <table>
      <tr>
         <th style="color: black;"> Загрузить</th>
         <th style="color: black;">Имя Файла</th> 
         <th style="color: black;">Удалить старый прайс </th>
         <th style="color: black;">Выбрать Поставщика </th>
         <th style="color: black;"> Выбрать Валюту </th>
         <th id="str"  style="color: black;">Внесено позиций </th>
         <th id="str"  style="color: black;"> Не внесено позиций </th>   
         
      </tr>
      <tr>
     <td><div align="center"><input type="checkbox"  name="load" value="yes"/></div></td>
          
     <td ><div align="center"><input type="checkbox"  name="load" value="yes" align="right" ></div></td>
     <td ><div align="center" ><select><option></option> <option>346</option>
     </select></div></td>
     </tr>
  </table>
 </div>  */
 ?>

<?  
   if (file_exists($_FILES["file"]["tmp_name"]) && !isset($_POST["check"])) /*&& $_FILES["file"]["type"]=="application/vnd.ms-excel")*/ 
     {   #error_reporting (E_ALL); 
         echo $_FILES["file"]["type"];
       if ($_FILES["file"]["type"]=="application/vnd.ms-excel" || $_FILES["file"]["type"]=="application/vnd.excel"||$_FILES["file"]["type"]=="text/comma-separated-values" || $_FILES["file"]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) 
        {  
       copy($_FILES["file"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/priceld/".($_FILES['file']['name']));
       $_SESSION['fln']=$_FILES["file"] ;   
      /*echo '<form method="post" action="Loadprice.php" enctype="multipart/form-data" > ' ;
      echo '<div id="dv" style="display: block; border:4px double orange ; background:white;">
       <table>
       <tr>
         <th style="color: black;"> Загрузить</th>
         <th style="color: black;">Имя Файла</th> 
         <th style="color: black;">Удалить старый прайс </th>
         <th style="color: black;">Выбрать Поставщика </th>
         <th style="color: black;"> Выбрать Валюту </th>
         <th id="str"  style="color: black;">Внесено позиций </th>
         <th id="str"  style="color: black;"> Не внесено позиций </th>   
         
      </tr>';
     tablestrload($_FILES["file"]["name"],0,0,$DB); 
     
    echo'</table>
  
    </div>
     <input type="hidden"  name="check" value="yes" /> 
    <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/>  
    </form> '; */
    tablecreate($_FILES["file"]["name"],0,0,$DB);
   
    #echo  $_FILES["file"]["type"];
    #echo  $_FILES["file"]["name"];
     echo ini_get('post_max_size');
     } /*echo $_FILES["file"]["type"]; */
   }    
   if (isset($_POST["check"]))
   {        #error_reporting (E_ALL);
             #tablecreate($_SESSION["fln"]["name"],0,0,$DB);
             #var_dump($_POST['supplier']);
       if (isset($_POST["load"]) && $_POST["load"]=='yes')
      {
         if (isset($_POST['supplier'])&& $_POST['supplier']>0 )
         {
                     #echo'www';
        
                if (isset($_POST["delprev"])&& $_POST["delprev"]=='yes')
              {
                   # if (isset($_POST["supplier"]))
                   #{
                 $sql = 'DELETE FROM `b_autodoc_prices_suppUA` WHERE `SuppCode`='.$_POST['supplier']; 
                 $DB-> Query($sql)  ;
                  #}  
             
             
               }   
         }  
         else
         { 
                  
                  tablecreate($_SESSION["fln"]["name"],0,0,$DB); 
                   echo "Не указан поставщик";
                  die();
         }        
         $IncludeClass=(isset($_POST["IncludeClass"])&& $_POST["IncludeClass"]=='yes')? True:FALSE;
         
         $csvfile= mkscv($_SERVER["DOCUMENT_ROOT"]."/priceld/".$_SESSION["fln"]["name"],$_SESSION["fln"]["name"],$IncludeClass) ;
         
       # copy($csvfile,"/var/www/priceld/db.csv");
         if (isset($_POST['dis']) )
         {
          $discount =($_POST['dis']=='')?0:$_POST['dis']*1;
         }else
         {
            $discount=0; 
         }
         if (isset($_POST['extra']))
         {
             $extra=($_POST['extra']=='')?0:$_POST['extra']*1;
         }else
         {
             $extra=0;
         }
         if (!$IncludeClass)
         {
         $arr=filedeal($csvfile,$_POST['supplier'],$_POST['curency'],$discount,$extra,'w');
         }elseif($IncludeClass)
         {
             if (isset($_POST['WriteNameToDB']) && $_POST['WriteNameToDB']=="yes")
             {
              WriteSupplierFileNameToDB($_SESSION["fln"]["name"],$_POST['supplier'],$DB) ;
             } 
             $arr=filedeal($csvfile->NewFileCSV,$_POST['supplier'],$csvfile->Currency,$csvfile->Discount,$extra,'w');  
         }
         $arr[3]='<a href="#" style="color:red;" onclick="selector()">'.$arr[1].'</a>' ;
         tablecreate($_SESSION["fln"]["name"],$arr[0],$arr[3],$DB);
         echo "Скидка-".$discount; 
         echo "Extra-".$extra;
         unloadedstrcsv($_SESSION["fln"]["name"]);
         $w='"' ;
         
         $sqlld="LOAD DATA INFILE'".$_SERVER["DOCUMENT_ROOT"]."/priceld/dbfile.csv' REPLACE INTO TABLE b_autodoc_prices_suppUA
        FIELDS TERMINATED BY ',' ENCLOSED BY '".$w."'" ;   
         $DBB=manualConnect(); 
         $DBB->Query($sqlld);
         if ($DBB->errno>0)
         {
             echo "<br>".$DBB->error;
         }else              
         {
             #var_dump($DB) ;
             echo "<br>".$DBB->affected_rows;
             
         }
      }
      # var_dump($_POST['supplier']);
    # die(); 
   }   
?>


<? 
#error_reporting(0);
#global $DB;                                     
//echo "www" ;
#var_dump($_POST['supplier']); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
            
?>
