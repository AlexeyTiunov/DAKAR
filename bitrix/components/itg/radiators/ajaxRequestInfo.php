<style>
html,body { 
    height: 100%;
    background-color:#edeeef;
    
 } 
 a:hover img
 {
    width:500px;
    height :400px; 
 }
 img
 {
     float:left; width:280px; height:200px; margin-top:10px;margin-left: 10px; border:solid 1px black; border-top:solid white 1px;
                    border-left:solid white 1px;border-right:solid 1px #afb0b2; border-bottom:solid 1px #afb0b2 ;
 }
</style>
<?php
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/components/itg/radiators/ProductProps.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php"); 
session_start();
$params = array();
$arret = array();
$brands = GetAllBrandsNameFromID();
$idBrands = GetAllBrandsPropertiesFromFullName();
global $DB;

$radiator = new ProductProps("b_autodoc_radiator","b_autodoc_items_m",array("id","autodocId"));
    $_SESSION['itgPadiator'] = $radiator->getDependents();
 error_reporting(0) ;
//echo json_encode($params);



#$_GET['ItemCode']="PL062305" ;
if (isset($_GET['ItemCode']))
{   
    $ItemCode= preg_replace("/[^A-Za-z0-9]*/i", "", $_GET['ItemCode']);    
    $idRad=ProductProps::getIdRadFromItem( $_SESSION['itgPadiator'],$ItemCode);     
   # echo $_GET['item'];
    $item = ProductProps::getItemFromId($_SESSION['itgPadiator'], $idRad);
    
    ?>
   
   <div style="width:600; height:550px; border:solid 1px black; border-top:solid white 1px;
                border-left:solid white 1px; 
                border-right:solid 1px #afb0b2;
                border-bottom:solid 1px #afb0b2; background-color:#edeeef;"> 
                
                 <div style=" margin:10px;width:570px;height:100px; float: left; border:solid 1px black; border-top:solid white 1px;
                    border-left:solid white 1px; 
                    border-right:solid 1px #afb0b2;
                    border-bottom:solid 1px #afb0b2;overflow: auto;">
                    <p align="center"><?=$item['definition']?>
                    <table align="center">                       
                      <tr >
                        <td>Бренд</td>
                        <td><?=$brands[$item['BrandCode']]?></td> 
                      </tr>
                       <tr>
                         <td> Артикул</td> 
                         <td> <?=$item['ItemCode']?></td>
                       </tr>  
                    </table>  </p> 
                   
                 </div>
                 <a  href='#'> <img  src="<?=$item['image']?>"/>
                    
                     </a>
                    
                <div style="float: left; width:280px ;height: 200px; border:solid 1px black; border-top:solid white 1px;
                    border-left:solid white 1px; 
                    border-right:solid 1px #afb0b2;
                    border-bottom:solid 1px #afb0b2 ; overflow: auto; margin-top:10px; margin-left: 10px;" >
                    <p align="center"><strong>Является аналогом к:</strong></p>
                    <table align="center" style="width: 100%;">
                        <tr align="center">
                           <th>Бренд</th>
                           <th>Артикул </th>
                        </tr>
                    
                <?
                  $sqlSelectAnalog = "SELECT `B2Code`,`I2Code` FROM `b_autodoc_analogs_m` WHERE `B1Code`='{$item['BrandCode']}' AND `I1Code`='{$item['ItemCode']}'";
                  $result=$DB->Query($sqlSelectAnalog) ;
                  while($sqlAnalogArray=$result->Fetch())
                  {
                          echo "<tr>" ;
                          echo  "<td align=\"center\" style=\"border-bottom: solid 1px white;\"> {$brands[$sqlAnalogArray['B2Code']]}</td>
                                 <td  align=\"center\" style=\"border-bottom: solid 1px white;\"> {$sqlAnalogArray['I2Code']}         </td>
                          
                          ";
                          echo"</tr>";
                      
                  }
                  
                  
                 ?>    
                    
                   
                    </table>
                
                </div>    
                    
                   
                <div style="width:570px; height: 200px;  overflow: auto;  border:solid 1px black; border-top:solid white 1px;
                    border-left:solid white 1px; 
                    border-right:solid 1px #afb0b2;
                    border-bottom:solid 1px #afb0b2 ;  margin-top:10px; margin-left:10px; float: left; ">
                      <p align="center"><strong>Подходит к следующим моделям авто:</strong></p> 
                      <table>  
                           <tr align="center"> 
                               <th>Марка авто</th>
                               <th>Модель Авто</th>
                               <th>Год Начала Выпуска</th>
                               <th>Год Окончания Выпуска</th>
                               <th>Дополнительная Информация</th>
                           <tr>
                           
    <?                       
             foreach ($_SESSION['itgPadiator'] as $radiatorProps)
                {
                   if ($radiatorProps['autodocId'] == $item['autodocId'])
                      {   
                         echo "<tr align=\"center\">"  ;
                              echo " <td align=\"center\" style=\"border-bottom: solid 1px white;\" >{$brands[$radiatorProps['VendorCode']]}</td>
                             <td align=\"center\" style=\"border-bottom: solid 1px white;\">{$radiatorProps['ModelRange']}  </td>
                             <td align=\"center\" style=\"border-bottom: solid 1px white;\">".substr($radiatorProps['DateBegin'],0,4) ."</td>
                              <td align=\"center\" style=\"border-bottom: solid 1px white;\">".substr($radiatorProps['DateEnd'],0,4) .".</td>
                              <td align=\"center\" style=\"border-bottom: solid 1px white;\">{$radiatorProps['AddInfo']}.</td>
                             ";
                         echo"</tr>" ;
                      }
   
                   
                }       
                                         
                        
    ?>                     
                           
                           
                      </table>
                   
                  </div>
                
    
    <?
    
    
    
    
    
}
 ?>
  
    
    </div>
 <?


?>   