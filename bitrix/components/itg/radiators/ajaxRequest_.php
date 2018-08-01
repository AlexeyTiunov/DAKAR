<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/components/itg/radiators/ProductProps.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");

session_start();
$params = array();
$arret = array();
$brands = $_SESSION['itgBrandsRadiators'];
$idBrands = $_SESSION['itgBrandsRadiatorsId'];
$triggerNumFiltr = 0;
if (isset($_GET['brand']) && $_GET['brand'] != '')
{
    $params['VendorCode'] = $_GET['brand'];
    $triggerNumFiltr++;
}
if (isset($_GET['model']) && $_GET['model'] != '')
{
    $params['ModelRange'] = $_GET['model'];
    $triggerNumFiltr++;
}
if (isset($_GET['dateb']) && $_GET['dateb'] != '')
{
    $params['DateBegin'] = $_GET['dateb'];
    $triggerNumFiltr++;
}

//echo json_encode($params);
$ret = ProductProps::getPropertyWithCondition($_SESSION['itgPadiator'],$params);
$numret = count($ret[1]);
if (isset($_GET['item']))
{
    //echo $_GET['item'];
    $item = ProductProps::getItemFromId($_SESSION['itgPadiator'], $_GET['item']);
    echo "<img style = 'float:left; height:320px; margin:0px 10px 10px 2px;' src={$item['image']}>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Производитель радиатора:</span>";
        echo "<div style = 'margin-left:70px;color:#EDAF1F;font-weight: bolder;'>{$brands[$item['BrandCode']]}</div>";
    echo "</div>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Код радиатора:</span>";
        echo "<div style = 'margin-left:70px;color:#EDAF1F;font-weight: bolder;'>{$item['ItemCode']}</div>";
    echo "</div>";
    echo "<div style = 'display:block; margin:10px 0px 30px 250px;'>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Описание:</span>";
        echo "<div style = 'margin-left:70px;color:#EDAF1F;'>".$item['definition']."</div>";
    echo "</div><br/><br/><br/><br/><br/><br/><br/>";
    //begin---------------выводим аналоги-----------------------------------------------------------------------------------------------------------------
    $sqlSelectAnalog = "SELECT `B2Code`,`I2Code` FROM `b_autodoc_analogs_m` WHERE `B1Code`='{$item['BrandCode']}' AND `I1Code`='{$item['ItemCode']}'";
    //echo $sqlSelectAnalog;
    $mysqli = new mysqli("localhost","bitrix","a251d851","bitrix","31006");
    $mysqli->set_charset("utf8");
	if ($sqlAnalogRes = $mysqli->query($sqlSelectAnalog))//выводим аналоги
	{
	    echo "<div style='position:absolute;left:250px;'>";
	    	echo "<span style = 'font-style:italic;font-size:12px;'>Аналоги:</span>";
	    while ($sqlAnalogArray = $sqlAnalogRes->fetch_assoc())
	    {
	    	echo "<div style = 'margin-left:20px;color:#EDAF1F;font-weight: bolder;'>{$brands[$sqlAnalogArray['B2Code']]}-{$sqlAnalogArray['I2Code']}</div>";
	    	//echo "<div style = 'margin-left:70px;color:#EDAF1F;font-weight: bolder;'></div>";
	    }
		echo "</div>";
	}
	//end----------------выводим аналоги------------------------------------------------------------------------------------------------------------------
	//begin--------------выводим модели-------------------------------------------------------------------------------------------------------------------
	echo "<div style='position:absolute;left:450px;'>";
		echo "<span style = 'font-style:italic;font-size:12px;'>Модели:</span>";
	foreach ($_SESSION['itgPadiator'] as $radiatorProps)
	{
		if ($radiatorProps['autodocId'] == $item['autodocId']/* && $radiatorProps['idRad'] != $item['idRad']*/)
		{
			echo "<div style = 'margin-left:20px;color:#EDAF1F;font-weight: bolder;'>{$brands[$radiatorProps['VendorCode']]} {$radiatorProps['ModelRange']}</div>";
		}
	}
	//end----------------выводим модели--------------------------------------------------------------------------------------------------------------------
	echo "</div>";
    echo "<div>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Марка автомобиля:</span>";
        echo "<div style = 'margin-left:50px;color:#EDAF1F;font-weight: bolder;'>{$brands[$item['VendorCode']]}</div>";
    echo "</div>";
    echo "<div>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Модель автомобиля:</span>";
        echo "<div style = 'margin-left:50px;color:#EDAF1F;font-weight: bolder;'>{$item['ModelRange']}</div>";
    echo "</div>";
    echo "<div>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Год начала выпуска:</span>";
        echo "<div style = 'margin-left:50px;color:#EDAF1F;font-weight: bolder;'>".substr($item['DateBegin'],0,4)."</div>";
    echo "</div>";
    echo "<div>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Год окончания выпуска:</span>";
        echo "<div style = 'margin-left:50px;color:#EDAF1F;font-weight: bolder;'>".substr($item['DateEnd'],0,4)."</div>";
    echo "</div>";
    echo "<div>";
        echo "<span style = 'font-style:italic;font-size:12px;'>Дополнительная<br />информация:</span>";
        echo "<div style = 'margin-left:50px;color:#EDAF1F;'>{$item['AddInfo']}</div>";
    echo "</div><br/><br/>";
    echo "<div>";
        echo "<div style = 'color:#EDAF1F;font-weight: bolder;float:right;'>
        <a href='http://".$_SERVER["SERVER_NAME"]."/autodoc/search.php?ICODE=".$item['ItemCode']."&BCODE=".$item['BrandCode']."&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25'>купить &raquo;</a>
        </div>";
    echo "</div>";
    /*foreach ($item as $prop)
    {
        echo "<p>{$prop}</p>";
    }*/
}
elseif ($triggerNumFiltr == 0 || $numret == 0)//при отсутствии фильтров возвращаем полные значения фильтров
{
    echo "#BrandCode<option></option>";
    unset($vendorsId,$vendors);
    $vendorsId = ProductProps::getProperty($_SESSION['itgPadiator'],"VendorCode");
    foreach ($vendorsId as $vendorId)
    {
        $vendors[]=$brands[$vendorId];
    }
    sort($vendors);
    foreach($vendors as $property)
    {
        echo "<option value='".$idBrands[strtoupper($property)]."'>{$property}</option>";
    }
    echo "#ModelRange<option></option>";
    foreach(ProductProps::getProperty($_SESSION['itgPadiator'],"ModelRange") as $property)
    {
        echo "<option value='$property'>{$property}</option>";
    }
    echo "#DateBegin<option></option>";
    /*foreach(ProductProps::getProperty($_SESSION['itgPadiator'],"DateBegin") as $property)
    {
        echo "<option value='$property'>{$property}</option>";
    }*/
    $arDate = ProductProps::getProperty($_SESSION['itgPadiator'],"DateBegin");
    $minDate = min($arDate);
    $minYear = new DateTime(substr($minDate,0,4));
    $fmin = $minYear->format('Y');
    $currentYear = new DateTime("now");
    $fcur = $currentYear->format('Y');
    for ($i=intval($fmin); $i<=intval($fcur); $i++)
    {
        $j = strval($i)."-00-00";
        echo "<option value='$j'>{$i}</option>";
    }
        
    if ($triggerNumFiltr == 0) echo "#ListProduct";
    if ($numret == 0) echo "#ListProduct<table style='width:250px;background-color: rgb(249,250,251);border-collapse: collapse;border-spacing: 2px;border-color: gray;'>
                <tbody>
                <tr>
                    <th colspan='2' style='background-image: url(images/table_head.gif);background-repeat: repeat-x;text-align: left;color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>Результаты поиска:</th>
                </tr>
                <tr>
                    <td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>По Вашему запросу ничего не найдено...<br>
                    </td>
                </tr>
             </table>";
}
else //сли есть значения фильтров
{
    if (!isset($params['VendorCode']))
    {
        //$arret['VendorCode'] = $ret['VendorCode'];
        echo "#BrandCode<option></option>";
        foreach ($ret[0]['VendorCode'] as $brand)
        {
            //$arret['BrandName'][] = $brands[$brand];
            echo "<option value='{$brand}'>{$brands[$brand]}</option>";
        }
    }
    if (!isset($params['ModelRange']))
    {
        //$arret['ModelRange'] = $ret['ModelRange'];
        echo "#ModelRange<option></option>";
        foreach ($ret[0]['ModelRange'] as $model)
        {
            echo "<option value='{$model}'>{$model}</option>";
        }
    }
    if (!isset($params['DateBegin']))
    {
        //$arret['DateBegin'] = $ret['DateBegin'];
        echo "#DateBegin<option></option>";
        $dates = $ret[0]['DateBegin'];
        sort($dates);
        $minDate = $dates[0];
        $minYear = substr($minDate,0,4);
        $fmin = intval($minYear);
        //var_dump($fmin);
        $currentYear = new DateTime("now");
        $fcur = $currentYear->format('Y');
        for ($i=$fmin; $i<=intval($fcur); $i++)
        {
            $j = strval($i)."-00-00";
            echo "<option value='$j'>{$i}</option>";
        }
        /*foreach ($ret[0]['DateBegin'] as $date)
        {
            echo "<option value='{$date}'>{$date}</option>";
        }*/
    }
    //здесь мы формируем список товаров если какой либо фильтр выбран
    if ($triggerNumFiltr < 4 && count($ret[1]) <= 50 && $numret > 0)
    {
        echo "#ListProduct<table style='width: 100%;background-color: rgb(249,250,251);border-collapse: collapse;border-spacing: 2px;border-color: gray;'>";
        echo "<tr style='background-image: url(images/table_head.gif);background-repeat: repeat-x;text-align: left;color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>
                <th>№</th>
                <!--<th>Марка авто</th>-->
                <th>Модель авто</th>
                <th>Годы выпуска</th>
                <th>Описание</th>
                <th>Артикул радиатора</th>
                <th>Наименование</th>
                <th>Производитель</th>
                <th>Заказ</th>
            </tr>";
        foreach ($ret[1] as $i=>$tr)
        {
            //var_dump($tr)."<br/>";
            echo "<tr id='".$tr['idRad']."' class='popup'><td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>".($i+1)."</td>";//первый столбик номер строки
            /*foreach ($tr as $name=>$td)
            {
                $exclude = array("id","Weight","autodocId","Model","Engine","Analogues","DateEnd","1CRef","type_item","definition","Caption","idRad","Reffer1C");
                if (in_array($name, $exclude)) continue;
                if ($name == 'VendorCode' || $name == 'BrandCode') $td = $brands[$td];
                if ($name == 'DateBegin') $td = substr($td,0,4);
                echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$td}</td>";
            }*/
            $emptyDate = "...";
            $dateEnd = (strlen($tr['DateEnd'])>4) ? substr($tr['DateEnd'],0,4) : $emptyDate;
            $dateStr = substr($tr['DateBegin'],0,4)." - ".$dateEnd;
            //echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$brands[$tr['VendorCode']]}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$tr['ModelRange']}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$dateStr}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$tr['AddInfo']}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$tr['ItemCode']}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$tr['Caption']}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>{$brands[$tr['BrandCode']]}</td>";
            echo "<td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>
                    <a href='http://".$_SERVER["SERVER_NAME"]."/autodoc/search.php?ICODE=".$tr['ItemCode']."&BCODE=".$tr['BrandCode']."&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25'>купить &raquo;</a>
                    </td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    elseif ($numret == 0)
    {
        echo "#ListProduct<table style='width:250px;background-color: rgb(249,250,251);border-collapse: collapse;border-spacing: 2px;border-color: gray;'>
                <tbody>
                <tr>
                    <th colspan='2' style='background-image: url(images/table_head.gif);background-repeat: repeat-x;text-align: left;color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>Результаты поиска:</th>
                </tr>
                <tr>
                    <td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>По Вашему запросу ничего не найдено...<br>
                    </td>
                </tr>
             </table>";
    }
    elseif ($numret > 50)
    {
        echo "#ListProduct<table style='width:250px;background-color: rgb(249,250,251);border-collapse: collapse;border-spacing: 2px;border-color: gray;'>
                <tbody>
                <tr>
                    <th colspan='2' style='background-image: url(images/table_head.gif);background-repeat: repeat-x;text-align: left;color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>Результаты поиска:</th>
                </tr>
                <tr>
                    <td style='color: rgb(37,99,154);border: 1px solid rgb(173,195,213);padding: 3px 5px;vertical-align: top;'>Уточните поиск, найдено товаров {$numret}...<br>
                    </td>
                </tr>
             </table>";
    }
}
//echo var_dump($ret);
//echo json_encode($arret);
?>