<? 
ini_set('memory_limit', '6048M');       
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
#set_time_limit(0);

     
 global $DB; 
function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","dakar",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    } 

function tablecreate($flname, $strld, $unstr,$DB)
 {
   echo '<form method="post" action="LoadMoreCrosses.php" enctype="multipart/form-data" > ' ;
    echo '<div id="dv" style="display: block; border:4px double orange ; background:white;">
     <table>
      <tr>
         <th style="color: black;"> Загрузить</th>
         <th style="color: black;">Имя Файла</th> 
         <th style="color: black;">Кросс по имени файла</th>
         <th style="color: black;"> Кросс Бренд</th>
         
         <th id="str"  style="color: black;">Внесено позиций </th>
         <th id="str"  style="color: black;"> Не внесено позиций </th>   
         
      </tr>';
     #------------------------------------------------
     foreach($flname as $FileName)
     { 
             echo'<tr>
             <td ><div align="center"><input type="checkbox" checked="checked" name="load['.$FileName.']" value="yes" align="right" ></div></td>
             <td ><div align="center"> ';
            
              echo '<p style="color:black;">'.$FileName.'</p>' ;
             
             echo '</div></td>
             <td ><div align="center"><input type="checkbox"  name="SelectCrossBrandFromName['.$FileName.']" value="yes" align="right" ></div></td> '; 
             
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
            $BrandName=strtolower($FileNameSupplierArray[0]);
            $sql="SELECT
                    IBLOCK_ELEMENT_ID AS BrandId,PROPERTY_287 AS BrandName
                    FROM  b_iblock_element_prop_s37
                    WHERE LOWER(PROPERTY_287)='{$BrandName}'
                    LIMIT 1";
                   
           $result=$DB->Query($sql);
           $ID=$result->Fetch();
           $BrandId=$ID['BrandId'];
           $BrandName=$ID['BrandName']; 
            if ($BrandId=="")
            {
               echo ' <td ><div align="center"> <input type="text" name="BrandId['.$FileName.']" value="0"/><p> Не найден бренд </p></td> ';  
            }
            else
            {
                     
             echo ' <td ><div align="center"> 
              <input type="text"  value="'.$BrandName.'"/>
             <input type="hidden" name="BrandId['.$FileName.']" value="'.$BrandId.'"/>
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
               <td ><div align="center"><p>Подключить класс</p><br><input type="checkbox"  name="IncludeClass" value="yes" align="right" ></div></td> ';  
     echo '</tr>';
     #------------------------------------------------
    echo'</table>
  
    </div>
     <input type="hidden"  name="check" value="yes" /> 
    <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/>  
    </form> ';  
 }       
 
  function filedeal($flname,$BrandCrossSelect,$BrandId,$unrwr)
 
 {
  $strcsvsh = array(
  'ID'=>'',
  'BrandCodeCross'=>' ',
  'ItemCodeCross'=>' ',
  'BrandCodeOriginal'=>' ',
  'ItemCodeOriginal'=>' ', 
   'in1c'=>'0'
  
  
  ) ; 
  $UnlodedStrArray=Array();
  $UnlodedStrArray[]=$flname;
  $row = 0;
  $upline=0;
  $handle = fopen($flname, "r");
  $importfile=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/dbfile.csv",'w');
  $importfileUn=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/dbfileUn.csv",$unrwr);  
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
              $ItemCodeCrossPosition=$c+1;
              $BranCodeCrossPosition=$c;
               if ($BrandCrossSelect)
               {
                    $brandcodenum=$BrandId;
               } else
               {
                   #$BranCodeCrossPosition=$c+1; 
                $BrandCode = str_ireplace('"', '',$data[$BranCodeCrossPosition]);
                #$brandcodenum= branddeal($BrandCode) ;
                 $brandcodenum= brandsearch($BrandCode) ;
               }
            if($brandcodenum!= false)
            {
               $strcsv['BrandCodeCross']=$brandcodenum;
               if (preg_match("/\//",$data[$ItemCodeCrossPosition]))
               {
                   $ItemCodeArrayCross=explode('/',$data[$ItemCodeCrossPosition]);
                   
                   $ItemCodeFirst=$ItemCodeArrayCross[0];
                   
               }
               elseif(preg_match("/\,/",$data[$ItemCodeCrossPosition]))
               {
                   $ItemCodeArrayCross=explode(',',$data[$ItemCodeCrossPosition]);
                   
                   $ItemCodeFirst=$ItemCodeArrayCross[0];
                   
               } 
               else
               {
                   $ItemCodeArrayCross=array();
                  $ItemCodeFirst= $data[$ItemCodeCrossPosition];
               }
               $strcsv['ItemCodeCross']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeFirst );
               #$strcsv['SuppCode']=$supplier;
               #$strcsv['Caption']= $data[$c+2];
               #$strcsv['Caption']= preg_replace("/[\",\']*/","",$data[$c+2] );
               #$strcsv['Caption']='"'.$data[$c+2].'"'; 
                $first=true;
            }
            else
            {
                $data['checkbr']  ='BrandCross';
                $first=false;   
                break;
            } 
            
         }
         if ($c==2)
         {
              $ItemCodePosition=$c;
              $BranCodePosition=$c+1;
            $BrandCode = str_ireplace('"', '',$data[$BranCodePosition]);
            #$brandcodenum= branddeal($BrandCode) ;
              $brandcodenum= brandsearch($BrandCode) ;
            if($brandcodenum!= false)
            {
               $strcsv['BrandCodeOriginal']=$brandcodenum;
               if (preg_match("/\//",$data[$ItemCodePosition]))
               {
                   $ItemCodeArrayOriginal=explode('/',$data[$ItemCodePosition]);
                   
                   $ItemCodeFirst=$ItemCodeArrayOriginal[0];
                   
               }
               elseif(preg_match("/\,/",$data[$ItemCodePosition]))
               {
                   $ItemCodeArrayOriginal=explode(',',$data[$ItemCodePosition]);
                   
                   $ItemCodeFirst=$ItemCodeArrayOriginal[0];
                   
               } 
               else
               {
                   $ItemCodeArrayOriginal=array();
                  $ItemCodeFirst= $data[$ItemCodePosition];
               }
               $strcsv['ItemCodeOriginal']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeFirst );
               #$strcsv['SuppCode']=$supplier;
               #$strcsv['Caption']= $data[$c+2];
               #$strcsv['Caption']= preg_replace("/[\",\']*/","",$data[$c+2] );
               #$strcsv['Caption']='"'.$data[$c+2].'"'; 
                $second=true;
            }
            else
            {
                $data['checkbr']  ='BrandOriginal'      ;
                $second=false;   
                break;
            } 
            
         }
         
        #echo $data[$c] . "<br />\n";
        
         
    }
    
         if ($first==true && $second==true)
        {
              $upline++;
              #echo $upline;
              #fputcsv($importfile, $strcsv,',','"');
              if (count($ItemCodeArrayCross)>1  )
              {
                  $StrCsvForCross=$strcsv;
                  foreach($ItemCodeArrayCross as $ItemCodeCross )
                  {
                     if (StrLen($ItemCodeCross)<1)continue;
                     if (count($ItemCodeArrayOriginal)>1)
                     {
                         foreach($ItemCodeArrayOriginal as $ItemCodeOriginal)
                         {
                         if (StrLen($ItemCodeOriginal)<1)continue;      
                         $StrCsvForCross['ItemCodeCross']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeCross);
                         $StrCsvForCross['ItemCodeOriginal']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeOriginal);
                         fputcsv($importfile, $StrCsvForCross,',','"');
                         fputcsv($importfile, invers($StrCsvForCross),',','"'); 
                         }
                     } 
                     else
                     {
                      $StrCsvForCross['ItemCodeCross']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeCross);                        
                       fputcsv($importfile, $StrCsvForCross,',','"');
                       fputcsv($importfile, invers($StrCsvForCross),',','"');
                     }  
                  }
                  
              }elseif (count($ItemCodeArrayOriginal)==0)
              {
                   $StrCsvForCross=$strcsv;
                  if (count($ItemCodeArrayOriginal)>1)
                     {
                         foreach($ItemCodeArrayOriginal as $ItemCodeOriginal)
                         {
                         if (StrLen($ItemCodeOriginal)<1)continue;      
                        
                         $StrCsvForCross['ItemCodeOriginal']=preg_replace("/[^a-z0-9]*/i", "",$ItemCodeOriginal);
                         fputcsv($importfile, $StrCsvForCross,',','"');
                         fputcsv($importfile, invers($StrCsvForCross),',','"');
                         }
                     } 
                     else
                     {
                                              
                       fputcsv($importfile, $StrCsvForCross,',','"');
                       fputcsv($importfile, invers($StrCsvForCross),',','"');
                     }  
                   
                  
                  
              }
              else
              {
                  fputcsv($importfile, $strcsv,',','"');
                  fputcsv($importfile, invers($strcsv),',','"');   
              } 
        } 
        else
        {
                   fputcsv($importfileUn, $data,',','"');
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
  $UnlodedBrands=fopen($_SERVER["DOCUMENT_ROOT"]."/priceld/UnlodedBrands.csv",'a+'); 
   fputcsv($UnlodedBrands,$UnlodedStrArray,',','"');
   fclose($UnlodedBrands); 
  
  #echo $upline;
  #echo $row;
  $returnarr[0]=$upline;
  $returnarr[1]=$row-$upline;  
  return $returnarr;   
 }     
 
 function brandsearch($BrandCode)
 {
     
     #SSANGYONG
     #FORD AUSTRALIA
     #FORD USA
     
     #RENAULT TRUCKS
     # CITROEN/PEUGEOT
     
     #FIAT / LANCIA
     #SSANGYONG
     #VW/SEAT
     #RENAULT TRUCKS
     #ALFAROME/FIAT/LANCI
     #FIAT / LANCIA

     #GREAT WALL  Hyundai/Kia
      #DAEWOO/CHEVROLET

     
     $BrandArrayToCheck["MERCEDES-BENZ"]="MB";
     $BrandArrayToCheck["ROVER"]="LAND ROVER";
     $BrandArrayToCheck["FORD USA"]="FORD";
     $BrandArrayToCheck["FORD AUSTRALIA"]="FORD";
     $BrandArrayToCheck['CITROEN/PEUGEOT']="PEUGEOT";
     $BrandArrayToCheck['FIAT / LANCIA']="FIAT";
     $BrandArrayToCheck['SSANGYONG']="Ssang Yong";
     $BrandArrayToCheck['VW/SEAT']="VW";
     $BrandArrayToCheck['RENAULT TRUCKS']="RENAULT";
     $BrandArrayToCheck['ALFAROME/FIAT/LANCI']="FIAT";
     $BrandArrayToCheck['GREAT WALL']="GREATWALL";
     $BrandArrayToCheck['Hyundai/Kia']="Hyundai"; 
    # $BrandArrayToCheck['']=;
    #$BrandArrayToCheck['']=;
     #$BrandArrayToCheck['']=;
     $BrandArrayToCheck['MERCEDES BENZ']="MB";
     $BrandArrayToCheck['DAEWOO/CHEVROLET']="CHEVROLET";
     $BrandArrayToCheck['HYUNDAI/KIA']="HYUNDAI";
     
     if (array_key_exists($BrandCode,$BrandArrayToCheck))
     {
         $BrandCode= $BrandArrayToCheck[$BrandCode];
         
     }
     global $DB;
     if (strlen(trim($BrandCode))>2)
     {
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID FROM b_iblock_element_prop_s37 WHERE PROPERTY_287='".trim($BrandCode)."'";
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
     elseif(strlen(trim($BrandCode))==2)
     {
         $sql="SELECT IBLOCK_ELEMENT_ID AS ID FROM b_iblock_element_prop_s37 WHERE PROPERTY_287='".trim($BrandCode)."'";
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
     else
     {
         return 33548111;
     }  
 }   
  
  function mkscv($oldFile,$filename,$IncludeClass)
{

   # if ($oldFile=="/var/www/priceld/ServiceKoreaMotorsKIA.xls") # ServiceKoreaMotorsKIA.
    if ($IncludeClass)
    {   error_reporting (0); 
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
 function unloadedstrcsv($fln)
 {
     $FileNameArray=explode('.',$fln) ;
     echo '<div id="st'.$FileNameArray[0].'" style="display:none;">Не Загрузились<table style="border:2px solid white">' ;
       echo'<tr>' ;
             echo'<th style="border:2px solid white;">Бренд Кросс</th>';
             echo'<th style="border:2px solid white;">Артикул Кросс</th>';    
             echo'<th style="border:2px solid white;">Бренд Оригинал</th>';
             echo'<th style="border:2px solid white;">Артикул Оригинал</th>';
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
         echo '<td style="border:2px solid white;width:70px">'.$data[3].'</td>';
         echo  '<td style="border:2px solid white;">'.$data[$num-1].'/'.$fln.'</td>';
          echo'</tr>' ;  
     }
     
     
     echo '</table></div>' ;
     fclose($importfileUn);
    
 }   
 function invers($CsvStrArray)
 {
     if (is_array($CsvStrArray))
     {
      $CsvStrArrayCopy=$CsvStrArray;
      $CsvStrArrayCopy["BrandCodeCross"]=$CsvStrArray['BrandCodeOriginal'];
      $CsvStrArrayCopy["BrandCodeOriginal"]=$CsvStrArray['BrandCodeCross']; 
      $CsvStrArrayCopy["ItemCodeCross"]=$CsvStrArray['ItemCodeOriginal']; 
      $CsvStrArrayCopy["ItemCodeOriginal"]=$CsvStrArray['ItemCodeCross'];
      return $CsvStrArrayCopy; 
     } 
     else
     {
         return $CsvStrArray;
     }
 }                                  
 ?>
  <p style="color:red;" align="center">Загрузка кроссов пакетом.</p>  
 <table>
   <tr>
 
     <td><div align="left"><form method="post" action="LoadMoreCrosses.php" enctype="multipart/form-data" >
    <input multiple=""  type="file" name="file[]"  style='width:250px;'/>  <br> 
     <input type="submit" id="submit" name="submit" value="Загрузить" style=" padding:4px; font-weight:bold;"/> 
     </form></div>
     </td> 
    </tr>
 </table>  
 
 
 
  
  
 <?
 if (is_array($_FILES["file"]["tmp_name"]) && count ($_FILES["file"]["tmp_name"]>0)) 
 {
     $FileQuantity= count ($_FILES["file"]["tmp_name"]);
          for($i=0;$i<$FileQuantity;$i++)
          {
              if (file_exists($_FILES["file"]["tmp_name"][$i])) /*&& $_FILES["file"]["type"]=="application/vnd.ms-excel")*/ 
             {
                  if ($_FILES["file"]["type"][$i]=="application/vnd.ms-excel" || $_FILES["file"]["type"][$i]=="application/vnd.excel"||$_FILES["file"]["type"][$i]=="text/comma-separated-values" || $_FILES["file"]["type"][$i]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) 
                {  
                       copy($_FILES["file"]["tmp_name"][$i],$_SERVER["DOCUMENT_ROOT"]."/priceld/".($_FILES['file']['name'][$i]));
                       $_SESSION['fln']=$_FILES["file"] ;   
                } 
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
                           $IncludeClass=(isset($_POST["IncludeClass"])&& $_POST["IncludeClass"]=='yes')? True:FALSE;           
                             $csvfile= mkscv($_SERVER["DOCUMENT_ROOT"]."/priceld/".$FileName ,$FileName ,$IncludeClass) ;
                             if (!$IncludeClass)
                             {
                                 if ($_POST['SelectCrossBrandFromName'][$FileName])
                                 $arr=filedeal($csvfile,true,$_POST['BrandId'][$FileName],'w');
                                 else
                                 $arr=filedeal($csvfile,false,"",'w'); 
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
                              ob_start();
                               unloadedstrcsv($FileName);
                               $Ununloadedstrcsv=ob_get_contents();
                              ob_end_clean();
                             $arr[4]= $Ununloadedstrcsv;
                             $FilesArray[$FileName]=$arr;
                              $w='"' ;
                             $sqlld="LOAD DATA INFILE '{$_SERVER["DOCUMENT_ROOT"]}/priceld/dbfile.csv' REPLACE INTO TABLE b_dakar_analogs_m 
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
                      
   
                 }
                 tablecreate($_SESSION["fln"]["name"],$FilesArray,0,$DB); 
                foreach ($FilesArray as $UnloadedDiv) 
                {
                    echo $UnloadedDiv[4];
                }
           }




require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); 
    
    
    
?>