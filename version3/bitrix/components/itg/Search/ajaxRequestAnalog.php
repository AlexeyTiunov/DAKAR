<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG2.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Analogs.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Appearance/Appearance.php";
session_start();
$arRegions = $_SESSION['arRegion_ITG'];
$arBrands = $_SESSION['arBrands_ITG'];
$arUser = $_SESSION['arUser_ITG'];

if (isset($_GET['icode']) && isset($_GET['bcode']))
{
	$bcode = intval($_GET['bcode']);
	$icode = str_replace(array(' ','&','?','*','-','.',',','%','+','/','#','$','\'','\"'), '', $_GET['icode']);
	$oAnalogs = new Analogs(array('icode'=>$icode,'bcode'=>$bcode));
	$arAnalogs = $oAnalogs->getArrItems();
	foreach ($arAnalogs as $analog)
	{
		$sAnalogs = new Search_ITG(array(	
								'exactOnly'=>true,
								'user'=>$arUser['user'], 
 								'currency'=>$arUser["currency"], 
 								'icode'=>$analog['icode'], 
 								'bcode'=>$analog['bcode'], 
 								'page'=>0, 
 								'numPage'=>25, 
 								'arBrands'=>$arBrands['id'],
 								'region'=>$arRegions['Code'],
 								'regionID'=>$arRegions['id']));
		if ($sAnalogs->getNumRows() > 0)
		{
			$products = $sAnalogs->getArrItems();
            $infoArray=$sAnalogs->GetInfoFromMegaP() ;
			if (count($products) > 0)
			{
				foreach ($products as $product)
				{
		    		++$i;
		    		if ($product['RegionCode'] > 4) 
                    {
                            
                             #$idgr=$USER->GetUserGroupArray();
                                #print_r($idgr)    ;
                              foreach ($_SESSION['GLUSERMASS'] as $id=>$i)
                              {
   
                                  if ($i==4)
                                 {  #echo'www';
                                   $idgrch=true; 
                                    break;   
                                  } else
                                    {
                                      $idgrch=false ;
                                     }
                               }
                              if( $idgrch!=true) 
                               {  
                                 $product['RegionShortName'] = 'УКРАИНА';//по просьбе идиотов  
                               } else
                                {
                                $product['RegionShortName'] = "<a href='#' title='{$product['RegionFullName']}'>УКРАИНА<br>{$product['RegionFullName']}</a>";//по просьбе идиотов
                                $cssStyle = 'background-color:#eed5d5;';
                                }
                        }
                    if ($product['RegionCode'] ==1000) $product['RegionShortName'] = 'ДОНЕЦК';//
		    		$product['DeliveryDays'] = intval($product['DeliveryDays']);
		    		$cssStyle = '';
		    		switch ($product['RegionShortName'])
		    		{
		    			case 'УКРАИНА':
		    				$cssStyle = 'background-color:#eed5d5;';
		    				break;
                            case "<a href='#' title='{$product['RegionFullName']}'>УКРАИНА<br>{$product['RegionFullName']}</a>" :
                            $cssStyle = 'background-color:#eed5d5;';
                            break;
		    			case 'СКЛАД':
		    				$cssStyle = 'background-color:#fefedc;';
		    				break;
                            case 'ДОНЕЦК':
                                $cssStyle = 'background-color:#fefedc;';
                                break;
                            default:
                                $cssStyle = 'background-color:#e3e3e3;';
		    		}
		    		$delivery = ($product['DeliveryDays'] != 0)?"<strong>".$product['DeliveryDays']." </strong>":"<font style='font-size:10px;text-align:left;'><strong>Есть<br>в<br>нал-и</strong></font>";
		    		$printRow = '';
					$printRow .= "<tr id='{$brand}{$i}' style='{$cssStyle}'>";
					$printRow .= "<td style='width:30px;'> <span class='counterFor'></span><input type='hidden' name='BrandShortCode' value='".$arBrands['id'][$product['BrandCode']]['ShortName']."' /> </td>";
					$printRow .= "<td style='width:80px;'>";
                       $printRow .= "
                            <script>
                               function defPosition(event) {
                                var x = y = 0;
                                  if (document.attachEvent != null) {
                                    x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
                                    y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
                                        }
                                if (!document.attachEvent && document.addEventListener) {
                                 x = event.clientX + window.scrollX;
                                 y = event.clientY + window.scrollY;
                                }
                                 return {x:x, y:y};
      
                                 }
                             </script>  
                            ";
                           if  ($product['BrandName']=="KOYORAD")  $printRow .= $product['BrandName']."<br>
                            <a href=\"#\" onclick=\"window.open('/bitrix/components/itg/radiators/ajaxRequestForA.php?ItemCode=".$product["ItemCode"]."','".$product["ItemCode"]."','width=800,height=620,left='+defPosition(event).x+',top='+defPosition(event).y); return false\" >INFO</a>
                           " ;  
                           else $printRow .= $product['BrandName'];  
                    
                    $printRow .="<input type='hidden' name='BrandCode' value='{$product['BrandCode']}' /></td>";
					$printRow .= "<td style='width:90px;text-align:left; font-size:11px'><strong>".$product['ItemCode']."</strong> <input type='hidden' name='ItemCode' value='{$product['ItemCode']}' /></td>";
					$printRow .= "<td style='width:90px;text-align:left;'>".$product['Caption']." <input type='hidden' name='Caption' value='{$product['Caption']}' /></td>";
					$printRow .= "<td style='width:30px; font-size:10px;'>".$delivery." <input type='hidden' name='RegionCode' value='".$product['RegionCode']."' /></td>";
                    $printRow .= "<td style='width:80px;text-align:left;font-size:11px;'>";
                    if($product["RegionCode"] == 1 or $product["RegionCode"] == 1000 )  $printRow .= "<br><strong>".$product["Quantity"]." шт.</strong>" ;
                               elseif( $product["RegionCode"] == 3 )   $printRow .=  "<strong>>".$product["Quantity"]." шт.</strong>" ; 
                               elseif($product["RegionCode"] == 2  )   $printRow .=  "<strong>".$infoArray["quantity"]." шт.</strong>" ;
                               else    $printRow .= "<strong>".$product["Quantity"]." шт.</strong>" ;
                     $printRow .="</td>" ;
					$printRow .= "<td style='width:50px; font-size:10px;'><strong>".$product['RegionShortName']."</strong><input type='hidden' name='DeliveryDays' value='".intval($product["DeliveryDays"])."' /></td>";
					 $printRow .=  "<td style='width:20px; font-size:11px;'>";
                                      if($product["RegionCode"] == 1 or $product["RegionCode"] == 1000) $printRow .= "100%";
                                  elseif  ( $product["RegionCode"] == 3 ) $printRow .= "100%" ;
                                  elseif( $product["RegionCode"] == 2 ) $printRow .= $infoArray['persentsupped'].'%'  ;
                                  else $printRow .= $product['PercentSupp']."%";
                    $printRow .= "</td> " ;
                    $printRow .= "<td style='width:50px;font-size:11px;'>".$infoArray['weight']."<input type='hidden' name='Weight' value='{$infoArray['weight']}' /></td>";
					  
                    $printRow .= "<td style='width:80px;font-size:11px;text-align:center'>".Appearance_ITG::preparePrice($product['Price'],$product["Currency"])."<input type='hidden' name='CurrencyCode' value='{$product['Currency']}' /></td>";
					$printRow .= "<td style='width:80px;font-size:11px;text-align:center'>".Appearance_ITG::preparePrice($product['UserPrice'],$arUser["currency"])."<input type='hidden' name='Price' value='{$product['Price']}' /></td>";
					
					if($arUser["auth"] == 'yes')
					{
						$printRow .="
						<td style='width:120px;'>&nbsp;
							<input name='Quantity' type='text' size='1' value='1' id='qty_{$i}_{$product['BrandCode']}'/> шт.&nbsp;&nbsp;
							<a 	class='itg-basket-style' style='border-width:0px;'
								href='/autodoc/add2cart.php?pid={$product['id']}&cur={$arRegions['Code'][$product['RegionCode']]['chrCurrencyCode']}&qty=1&r=1&rg={$product['RegionCode']}' 
								><img style='width:30px;float:right;' title='Добавить в корзину' src=\"http://parts.avtodok.com.ua/bitrix/components/itg/Search/basket.png\" />
							</a>
							<span id='pic_{$i}_{$product['BrandCode']}'>&nbsp;</span>
						</td>";
					}
					$printRow .= "</tr>";
					if ($product['RegionCode'] == 1 || $product['RegionCode'] > 4) $j = 200+$i-100;
					else $j = 200+$i+100;
					//echo "<tr><td colspan='10'>".intval($product['RegionCode'].$j)."</td></tr>";
					$arPrintRow[intval($j)] = $printRow;
				}
			}
		}
	}
	//echo "<tr><td colspan='10' style='height:500px;'><pre>";
	//var_dump($arPrintRow);
	//echo "</pre></td></tr>";
	if (isset($arPrintRow) && count($arPrintRow) > 0)
	{
		ksort($arPrintRow, SORT_NUMERIC);
		echo implode('', $arPrintRow);
	}
}
?>