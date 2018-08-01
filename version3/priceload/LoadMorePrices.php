<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG4.php");
set_time_limit(0);
ini_set('memory_limit', '6048M'); 
error_reporting (0); 
#ini_set('post_max_size','60M');
#ini_set('upload_max_filesize','40M') ;

global $DB;
global $USER;

$idgr=$USER->GetUserGroupArray();
#var_dump($idgr);
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
#require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
   function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","dakar",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }  
 function tablestrload ($FileNameArray, $strld, $unstr,$DB)
 {    foreach($FileNameArray as $FileName)
     { 
             echo'<tr>
             <td ><div align="center"><input type="checkbox" checked="checked" name="load['.$FileName.']" value="yes" align="right" ></div></td>
             <td ><div align="center"> ';
            
              echo '<p style="color:black;">'.$FileName.'</p>' ;
             
             echo '</div></td>
             <td ><div align="center"><input type="checkbox"  name="delprev['.$FileName.']" value="yes" align="right" ></div></td> '; 
            
            /* echo'<td ><div align="center"><select name="supplier"><option value=""></option>';
             $options = $DB->Query("SELECT
                    IBLOCK_ELEMENT_ID AS SupplierCode, PROPERTY_94 AS SupplierName
                    FROM  b_iblock_element_prop_s17
                    WHERE PROPERTY_233 IS NOT NULL AND PROPERTY_233 > 0
                    ORDER BY PROPERTY_94 ASC");
                while($row = $options->Fetch())
                {
                    echo '<option value="' . $row['SupplierCode'] . '">' . $row['SupplierName'] . '</option>';
                }
             
             
              
            echo' </select></div></td>  '; */
            $FileNameWithoutExtArray=explode('.',$FileName);
            $FileNameWithoutExt=$FileNameWithoutExtArray[0];
            $FileNameSupplierArray=explode("-",$FileNameWithoutExt) ;
            $SupplierName=$FileNameSupplierArray[0];
            $sql="SELECT
                    IBLOCK_ELEMENT_ID AS SupplierCode
                    FROM  b_iblock_element_prop_s17
                    WHERE DESCRIPTION_94='{$SupplierName}'
                    LIMIT 1";
           $result=$DB->Query($sql);
           $ID=$result->Fetch();
           $SupplierCode=$ID['SupplierCode']; 
            if ($SupplierCode=="")
            {
               echo ' <td ><div align="center"> <input type="text" name="supplier['.$FileName.']" value="0"/><p> Не найден поставщик</p></td> ';  
            }
            else
            {
                     
             echo ' <td ><div align="center"> 
              <input type="text"  value="'.$SupplierCode.'"/>
             <input type="hidden" name="supplier['.$FileName.']" value="'.$SupplierCode.'"/>
             </td> ';
            }
           /* echo'  
             <td ><div align="center"><select name="curency"><option value="UAH">UAH</option> <option value="USD">USD</option> <option value="EUR">EUR</option> 
             </select></div></td>'; */
             if (is_array($strld))
             {
                  echo '<td ><div align="center"><p style="color:black;">'.$strld[$FileName][0].'</p></div></td> 
                 <td ><div align="center"><p style="color:black;">'.$strld[$FileName][3].'</p></div></td>'; 
                 
             } else
             {
                 echo '<td ><div align="center"><p style="color:black;">'.$strld.'</p></div></td> 
                 <td ><div align="center"><p style="color:black;">'.$unstr.'</p></div></td>'; 
             }
             echo '</tr>';  
             #echo   $strld[$FileName][4];
     }  
    /* echo '<tr>
                <td ><div align="center">Скидка<input type="text"  name="dis" size="5" maxlength="2" align="right" >%</div></td> 
                <td ><div align="center">Extra<input type="text"  name="extra" size="5" maxlength="2" align="right" >%</div></td> ';  ; 
     echo '</tr>'; */
     
     echo '<tr>
               <td ><div align="center"><p>Подключить класс</p><br><input type="checkbox" checked="checked" name="IncludeClass" value="yes" align="right" ></div></td> ';  
     echo '</tr>';  
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
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID,PROPERTY_288 AS FULLNAME FROM b_iblock_element_prop_s37 WHERE PROPERTY_288='".trim($BrandCode)."'";
           $result =$DB->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res;
            }else
            {
                return false;
            }   
         
     }
    /* elseif(strlen(trim($BrandCode))==2)
     {
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID,PROPERTY_72 AS FULLNAME FROM b_iblock_element_prop_s14 WHERE PROPERTY_71='".trim($BrandCode)."'";
           $result =$DB->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res;
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
     $FileNameArray=explode('.',$fln) ;
     echo '<div id="st'.$FileNameArray[0].'" style="display:none;">Не Загрузились<table style="border:2px solid white">' ;
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
  require_once($_SERVER["DOCUMENT_ROOT"]."/personal/suppload/ItemCodeCorrection.php"); 
  $ObjectItemCodeCheckAndChange= new ItemCodeCorrection();
  #$ArrayItemCodeCorrectionByBrand=$ObjectArrayItemCodeCorrectionByBrand->DefinePartenArray(); 
  $row = 0;
  $upline=0;
  $ErrorPriceQuantity=0;
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
               $strcsv['BrandCd']=$brandcodenum['ID'];
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
               $ItemCodeFirst=$ObjectItemCodeCheckAndChange->CheckAndChangeItemCode($ItemCodeFirst,$brandcodenum['FULLNAME']);
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
            
             if ($data[$c]<$strcsv['Quantity'])
             {
                 $ErrorPriceQuantity++; 
             }
             else
             {
               $ErrorPriceQuantity=0;  
             }
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
       if ( $ErrorPriceQuantity>1000)
       {
           exit("<p style='color:red'>Колонка Цены не Верна</p>");
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
        if ($first==false)
        {
             if (!in_array($data[0],$UnlodedStrArray))
                   {
                        #$UnlodedStrArray[]=$data[3] ;
                        $UnlodedStrArray[]=$data[0] ;
                   }
        }
    #fputcsv($importfile, $strcsv,',','"');
    $row++;
  }
  fclose($importfile);
  fclose($handle);
  
  $UnlodedBrands=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/UnlodedBrands.csv",'w'); 
   fputcsv($UnlodedBrands,$UnlodedStrArray,',','"');
   fclose($UnlodedBrands);
  #echo $upline;
  #echo $row;
  $returnarr[0]=$upline;
  $returnarr[1]=$row-$upline;  
  return $returnarr;   
 }  
 function tablecreate($flname, $strld, $unstr,$DB)
 {
   echo '<form method="post" action="LoadMorePrices.php" enctype="multipart/form-data" > ' ;
    echo '<div id="dv" style="display: block; border:4px double orange ; background:white;">
     <table>
      <tr>
         <th style="color: black;"> Загрузить</th>
         <th style="color: black;">Имя Файла</th> 
         <th style="color: black;">Удалить старый прайс </th>
         <th style="color: black;"> Поставщик </th>
         
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
    {  error_reporting (0); 
        #ini_set('memory_limit', '4048M'); 
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
<p style="color:red;" align="center">Загрузка файлов пакетом.</p>  
 <table>
   <tr>
 
     <td><div align="left"><form method="post" action="LoadMorePrices.php" enctype="multipart/form-data" >
    <input multiple=""  type="file" name="file[]"  style='width:250px;'/>  <br> 
     <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/> 
     </form></div>
     </td> 
     <td>
        <div align="left" ><form method="post" action="Loadprice.php" enctype="multipart/form-data" > 
        <select style="width:250px ;"> <option>Выберите папку </option>
         <?
         $dir = "/var/www/priceld/";

// Открыть заведомо существующий каталог и начать считывать его содержимое
  /* if (is_dir($dir))
    {
     if ($dh = opendir($dir)) 
     {
        # $filel = readdir($dh);
        while (false!==($filel = readdir($dh))) 
        {
            
           # if ($filel!='.' && $filel!='..' )
            #{     
                if (is_dir($filel))
                {
                      echo '<option>'.$filel.' </option>' ;
                }
                else
                {
                      
                } 
            #}
            #print "Файл: $file : тип: " . filetype($dir . $file) . "\n";
        }
        closedir($dh);
      }
   }
             */
         ?>
        
        </select> <br>
        <input type="submit" id="submit" name="submit" value="Загрузить" style="  font-weight:bold;"/> 
        </form></div>
     </td>
    </tr> 
 </table>
 <?
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
   if (is_array($_FILES["file"]["tmp_name"]) && count ($_FILES["file"]["tmp_name"]>0))
   { 
           $FileQuantity= count ($_FILES["file"]["tmp_name"]);
          for($i=0;$i<$FileQuantity;$i++)
          { 
           if (file_exists($_FILES["file"]["tmp_name"][$i])) /*&& $_FILES["file"]["type"]=="application/vnd.ms-excel")*/ 
             {
               echo $_FILES["file"]["type"][$i] ;
               if ($_FILES["file"]["type"][$i]=="application/vnd.ms-excel" || $_FILES["file"]["type"][$i]=="application/vnd.excel"||$_FILES["file"]["type"][$i]=="text/comma-separated-values" || $_FILES["file"]["type"][$i]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) 
                {  
                       #chmod($_SERVER["DOCUMENT_ROOT"]."/priceld/",0777);
                       copy($_FILES["file"]["tmp_name"][$i],$_SERVER["DOCUMENT_ROOT"]."/priceld/".($_FILES['file']['name'][$i]));
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
                   # tablecreate($_FILES["file"]["name"],0,0,$DB);
                   
                    #echo  $_FILES["file"]["type"];
                    #echo  $_FILES["file"]["name"];
            
                 } /*echo $_FILES["file"]["type"]; */
             } 
          } 
       tablecreate($_FILES["file"]["name"],0,0,$DB); 
       echo ini_get('post_max_size');          
   }        
           if (isset($_POST["check"]))
           {  
                $FilesArray=array();
                     #tablecreate($_SESSION["fln"]["name"],0,0,$DB);
                      
                 foreach($_SESSION['fln']['name'] as $FileName)
                 {
                      if (isset($_POST["load"][$FileName] ) && $_POST["load"][$FileName]=='yes')
                      {
                             
                              
                             if (isset($_POST['supplier'][$FileName] )&& $_POST['supplier'][$FileName]>0 )
                             {
                                         #echo'www';
                            
                                    if (isset($_POST["delprev"][$FileName])&& $_POST["delprev"][$FileName]=='yes')
                                  {
                                       # if (isset($_POST["supplier"]))
                                       #{
                                     $sql = 'DELETE FROM `b_autodoc_prices_suppUA` WHERE `SuppCode`='.$_POST['supplier'][$FileName]; 
                                     $DB-> Query($sql)  ;
                                      #}  
                                 
                                 
                                   }   
                             }  
                             else
                             { 
                                      
                                      #tablecreate($_SESSION["fln"]["name"],0,0,$DB); 
                                       #echo "Не указан поставщик";
                                     # die();
                             }        
                             $IncludeClass=(isset($_POST["IncludeClass"])&& $_POST["IncludeClass"]=='yes')? True:FALSE;           
                             $csvfile= mkscv($_SERVER["DOCUMENT_ROOT"]."/priceld/".$FileName ,$FileName ,$IncludeClass) ;
                            
                           # copy($csvfile,"/var/www/priceld/db.csv");
                            /* if (isset($_POST['dis']) )
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
                             }   */
                             if (!$IncludeClass)
                             {
                             $arr=filedeal($csvfile,$_POST['supplier'],$_POST['curency'],$discount,$extra,'w');
                             }elseif($IncludeClass)
                             {
                                 $arr=filedeal($csvfile->NewFileCSV,$_POST['supplier'][$FileName],$csvfile->Currency,$csvfile->Discount,$csvfile->Extra,'w');  
                             }
                             $FileNameArray=explode('.',$FileName) ;
                             $arr[3]='<a href="#"  id="'.$FileNameArray[0].'" style="color:red;" >'.$arr[1].'</a> 
                             <script>
                               $("#'.$FileNameArray[0].'").click(function()
                                   {
                                       if ( $("#st'.$FileNameArray[0].'").css("display")=="none" )
                                           { 
                                            $("#st'.$FileNameArray[0].'").css("display","block");
                                            $("#'.$FileNameArray[0].'").css("color","green") ;
                                           }
                                           else 
                                           { 
                                            $("#st'.$FileNameArray[0].'").css("display","none"); 
                                             $("#'.$FileNameArray[0].'").css("color","red") ;  
                                           }
   
                                    
                                   }
                               
                               );
                             </script>
                             ';
                              
                             #tablecreate($_SESSION["fln"]["name"],$arr[0],$arr[3],$DB);
                             echo "<br>Скидка-".$csvfile->Discount; 
                             echo "Extra-".$csvfile->Extra.$FileName."<br>";
                             ob_start();
                             unloadedstrcsv($FileName);
                             $Ununloadedstrcsv=ob_get_contents();
                             ob_end_clean();
                             $arr[4]= $Ununloadedstrcsv;
                             $FilesArray[$FileName]=$arr;
                             $w='"' ;
                             $sqlld="LOAD DATA INFILE '".$_SERVER["DOCUMENT_ROOT"]."/priceld/dbfile.csv' REPLACE INTO TABLE b_autodoc_prices_suppUA
                            FIELDS TERMINATED BY ',' ENCLOSED BY '".$w."'" ; 
                             $DBB=manualConnect();
                             #$DBB->Query("SET NAMES 'UTF8'"); 
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
                      
                 }
                 #global $DB;
                tablecreate($_SESSION["fln"]["name"],$FilesArray,0,$DB); 
                foreach ($FilesArray as $UnloadedDiv) 
                {
                    echo $UnloadedDiv[4];
                }
           }   
?>


<?
error_reporting(0);
global $DB;
#$DB=manualConnect();                                      
//echo "www" ;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
            
?>
