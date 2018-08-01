<style>
html,body { 
    height: 100%;
    background-color:#edeeef;
    
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


$radiator = new ProductProps("b_autodoc_radiator","b_autodoc_items_m",array("id","autodocId"));
    $_SESSION['itgPadiator'] = $radiator->getDependents();
 error_reporting(0) ;
//echo json_encode($params);




if (isset($_GET['ItemCode']))
{        
    $idRad=ProductProps::getIdRadFromItem( $_SESSION['itgPadiator'],$_GET['ItemCode']);     
   # echo $_GET['item'];
    $item = ProductProps::getItemFromId($_SESSION['itgPadiator'], $idRad);
   # echo "<div style='position:absolute;left:0px;top:0px;background-color:#edeeef; width:100%; height:100%; >";
    echo "<div style='border: ; margin:10px;widht:600px; height:600px ;background-color: #edeeef; border:solid 6px blue; border-top:solid white 1px;
    border-left:solid white 1px; 
    border-right:solid 1px #afb0b2;
    border-bottom:solid 1px #afb0b2 ; ' >";
    echo "<img style=' float:left; height:320px; margin:10px 10px 10px 10px; border:solid 1px black; border-top:solid white 1px;
    border-left:solid white 1px; 
    border-right:solid 1px #afb0b2;
    border-bottom:solid 1px #afb0b2 ;' src={$item['image']}>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Производитель радиатора:</span>";
        echo "<div style = 'margin-left:70px;color:#080589;font-weight: bolder;'>{$brands[$item['BrandCode']]}</div>";
    echo "</div>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Код радиатора:</span>";
        echo "<div style = 'margin-left:70px;color:#080589;font-weight: bolder;'>{$item['ItemCode']}</div>";
    echo "</div>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Описание:</span>";
        echo "<div style = 'margin-left:70px;color:#080589;'>".$item['definition']."</div>";
    echo "</div>";
    //-----------------------------------------------------------------------------------------------------------------
    $sqlSelectAnalog = "SELECT `B2Code`,`I2Code` FROM `b_autodoc_analogs_m` WHERE `B1Code`='{$item['BrandCode']}' AND `I1Code`='{$item['ItemCode']}'";
    //echo $sqlSelectAnalog;
    $mysqli = new mysqli("localhost","bitrix","a251d851","bitrix","31006");
    $mysqli->set_charset("utf8");
    if ($sqlAnalogRes = $mysqli->query($sqlSelectAnalog))
    {
        echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
            echo "<span style = 'font-style:italic;font-size:12px;'>Аналоги:</span>";
        while ($sqlAnalogArray = $sqlAnalogRes->fetch_assoc())
        {
            echo "<div style = 'margin-left:70px;color:#080589;'>{$brands[$sqlAnalogArray['B2Code']]}-{$sqlAnalogArray['I2Code']}</div>";
            //echo "<div style = 'margin-left:70px;color:#EDAF1F;font-weight: bolder;'></div>";
        }
        echo "</div><br/><br/>";
    }
    //------------------------------------------------------------------------------------------------------------------

    $tableB = "<table>";$tableE = "</table>";
    $tableTRB = "<tr align='left'>";$tableTRE = "</tr>";
    $tableTHB = "<th style = 'font-style:italic;font-size:12px;font-weight: 300;padding:3px 3px 3px 3px;border:1px dotted #797977;'>";$tableTHE = "</th>";
    $tableTDB = "<td style = 'color:#080589;padding:3px 7px 3px 7px;border:1px dotted #797977;'>";$tableTDE = "</td>";
    $tableStrArray = array();
    $tableStrArray['markaAvto'] = $tableTRB.$tableTHB."Марка авто".$tableTHE;
    $tableStrArray['modelAvto'] = $tableTRB.$tableTHB."Модель авто".$tableTHE;
    $tableStrArray['yearBegin'] = $tableTRB.$tableTHB."Год начала выпуска:".$tableTHE;
    $tableStrArray['yearEnd'] = $tableTRB.$tableTHB."Год окончания выпуска:".$tableTHE;
    $tableStrArray['addInfo'] = $tableTRB.$tableTHB."Дополнительная<br />информация:".$tableTHE;

    foreach ($_SESSION['itgPadiator'] as $radiatorProps)
    {
        if ($radiatorProps['autodocId'] == $item['autodocId'])
        {
            $tableStrArray['markaAvto'] .= $tableTDB.$brands[$radiatorProps['VendorCode']].$tableTDE;
            $tableStrArray['modelAvto'] .= $tableTDB.$radiatorProps['ModelRange'].$tableTDE;
            $tableStrArray['yearBegin'] .= $tableTDB.substr($radiatorProps['DateBegin'],0,4).$tableTDE;
            $tableStrArray['yearEnd'] .= $tableTDB.substr($radiatorProps['DateEnd'],0,4).$tableTDE;
            $tableStrArray['addInfo'] .= $tableTDB.$radiatorProps['AddInfo'].$tableTDE;

        }
    }
    $strToPrint =     $tableB.
                        $tableStrArray['markaAvto'].$tableTRE.
                        $tableStrArray['modelAvto'].$tableTRE.
                        $tableStrArray['yearBegin'].$tableTRE.
                        $tableStrArray['yearEnd'].$tableTRE.
                        $tableStrArray['addInfo'].$tableTRE.
                    $tableE;
    echo $strToPrint."<br/><br/>";
    
 echo "</div></div>"  ;
}

?>