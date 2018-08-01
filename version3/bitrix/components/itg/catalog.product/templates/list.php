<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");
    $idItemForCatalog = itgCatalog::getListProduct($_REQUEST['itg_brend_code'],$_REQUEST['itg_name_catalog']);
    $idItemForCatalog->NavStart(10);
    $arNavigations["NAV_STRING"] = $idItemForCatalog->GetPageNavString('Масла');
    $arNavigations["CURRENT_PAGE"] = $APPLICATION->GetCurPage();
    if(strlen($arNavigations["NAV_STRING"]) > 0)
    {
        echo "<p>".$idItemForCatalog->GetPageNavString('Масла')."</p>";
    }
    while($arId = $idItemForCatalog->GetNext())
    {
        echo "<div style = 'width:600px; height:210px; border-bottom: solid 1px white;color:#353536; opacity:0.9;filter:alpha(opacity=90);padding:10px;'>";
        $product = new itgProduct($arId['id']);
        $props = $product->getProductProperties();
        echo "<img style = 'float:left; width:90px; margin:0px 10px 10px 2px;' src={$props['image']}>";

            echo "<div style = 'display:block; margin:10px 0px 30px 100px;'><div>";
                echo "<span style = 'font-style:italic;font-size:12px;'>Наименование:</span>";
                echo "<div style = 'margin-left:50px;font-weight: bold;'><a style ='text-decoration:none;color:#252525;'href='http://".$_SERVER["SERVER_NAME"]."/personal/order/catalog/index.php?itg_more_info={$arId['id']}'>{$props['Caption']}</a></div>";
            echo "</div>";
            echo "<div>";
                echo "<span style = 'font-style:italic;font-size:12px;'>Код:</span>";

                       $objTStr = new TemplatedString($props['ItemCode']);
                       $objTStr->SetTemplate($props["BrandCode"] );
                #$objTStr->SetColor("#353536");
                $objTStr->SetSelection($props['ItemCode']);
                echo "<div style = 'margin-left:50px;color:#353536;'>{$objTStr->GetTemplated()}</div>";
            echo "</div></div>";
            echo "<div>";
                echo "<span style = ' font-style:italic;font-size:15px;'>Описание:</span>";
                echo "<div>".substr($props['definition'],0,333)."...</div>";
            echo "</div>";
            echo "<div style = 'float:right;'><a style ='text-decoration:none;color:#252525;'href='http://".$_SERVER["SERVER_NAME"]."/autodoc/search.php?ICODE=".$props['ItemCode']."&BCODE=".$props['BrandCode']."&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25'>купить &raquo;</a></div>";
            //http://parts.avtodok.com.ua/autodoc/search.php?ICODE=0888080365&BCODE=916&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25                        
        echo "</div>";
    echo "<br/>";
    }
    if(strlen($arNavigations["NAV_STRING"]) > 0)
    {
        echo "<p>".$idItemForCatalog->GetPageNavString('Масла')."</p>";
    }
?>