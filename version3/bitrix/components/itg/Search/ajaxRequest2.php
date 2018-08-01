<?php
#require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/connect.web/Connect_ITG2.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Appearance/Appearance.php";
session_start();
$arRegions = $_SESSION['arRegion_ITG'];
$arBrands = $_SESSION['arBrands_ITG'];
	if(isset($_GET["web"]))
    {
    	/*$itemsFromWeb['tehnomir'] = new Connect_ITG(array(
    											'user'=>$_GET['user'],
    											'client'=>'tehnomir',
    											'article'=>$_GET['icode'],
    											'currency'=>$_GET['currency']
    									));*/
    	$_GET['user'] = ($_GET['user'] == '')?'09999':$_GET['user'];
    	$itemsFromWebAll = new Connect_ITG(array(
    											'user'=>$_GET['user'],
    											#'client'=>'autopalma',
    											'article'=>$_GET['icode'],
    											'currency'=>$_GET['currency']
    									));
    	$i = $_GET['iterator'];
    	$itemsFromWeb = $itemsFromWebAll->getResult();
    	#echo "<pre>";
    	#print_r($arBrands);
    	#echo "</pre>";
    	echo "<pre>";
    	print_r($itemsFromWeb);
    	echo "</pre>";
    	if (is_array($itemsFromWeb) && count($itemsFromWeb)>0)
    	{
	    	foreach ($itemsFromWeb as $resConnect)
	    	{
	    		if (count($resConnect) == 0) continue;
	    		foreach ($resConnect as $product)
	    		{
	    			//
		    		$brand = $arBrands['ShortName'][strtoupper($product['Brand'])]['FullName'];
		    		if ($brand)
		    		{
		    			$bcode = $arBrands['ShortName'][strtoupper($product['Brand'])]['id'];
		    		}
		    		else 
		    		{
		    			$brand = isset($arBrands['FullName'][strtoupper($product['Brand'])]['ShortName'])?strtoupper($product['Brand']):"";
		    			$bcode = $arBrands['FullName'][strtoupper($product['Brand'])]['id'];
		    		}
		    		//
		    		#if (!$brand) continue;//
		    		++$i;
					echo "<tr id='{$brand}{$i}style='background-color:{$product[CS]}'>";
					echo "<td style='width:30px;'> <span class='counterFor'></span><input type='hidden' name='BrandShortCode' value='".$arBrands['id'][$bcode]['ShortName']."' /> </td>";
					echo "<td style='width:80px;'>".$brand."<input type='hidden' name='BrandCode' value='{$bcode}' /></td>";
					echo "<td style='width:150px;text-align:left;'> &nbsp;".$product['ICODE']." <input type='hidden' name='ItemCode' value='{$product['ICODE']}' /></td>";
					echo "<td style='width:150px;text-align:left;'> &nbsp;".$product['CAPTION']." <input type='hidden' name='Caption' value='{$product['CAPTION']}' /></td>";
					echo "<td style='width:80px;'>".$product['DELIVERY']."<input type='hidden' name='RegionCode' value='".$arRegions['ShortName'][$product['REGION']]['Code']."' /></td>";
					echo "<td style='width:80px;'>"/*.$product['REGIONR']*/"УКРАИНА"."<input type='hidden' name='DeliveryDays' value='".intval($product["DELIVERY"])."' /></td>";
					echo "<td style='width:50px;'>".$product['Weight']."<input type='hidden' name='Weight' value='{$product['Weight']}' /></td>";
					echo "<td style='width:80px;'>".Appearance_ITG::preparePrice($product['PRICEREGION'],$arRegions['ShortName'][$product['REGION']]['chrCurrencyCode'])."<input type='hidden' name='CurrencyCode' value='{$arRegions['ShortName'][$product['REGION']]['chrCurrencyCode']}' /></td>";
					echo "<td style='width:100px;'>".Appearance_ITG::preparePrice($product['PRICEREGIONINCURRENCY'],$_GET['currency'])."<input type='hidden' name='Price' value='{$product['PRICEREGIONINCURRENCY']}' /></td>";
					
					if($_GET['auth'])
					{
						echo"
						<td style='width:120px;'>&nbsp;
							<input name='Quantity' type='text' size='1' value='1' id='qty_{$i}_{$product['BrandCode']}'/> шт.&nbsp;&nbsp;
							<a 	class='itg-basket-style' style='border-width:0px;'
								href='/autodoc/add2cart.php?pid={$bcode}{$product['REGION']}&cur=".$arRegions['ShortName'][$product['REGION']]['chrCurrencyCode']."&qty=1&r=1&rg=".$arRegions['ShortName'][$product['REGION']]['Code']."' 
								><img style='width:30px;float:right;' title='Добавить в корзину' src=\"http://parts.avtodok.com.ua/bitrix/components/itg/Search/basket.png\" />
							</a>
							<span id='pic_{$i}_{$bcode}'>&nbsp;</span>
						</td>";
					}
					echo "</tr>";
	    		}
	    	}
    	}
    }
?>