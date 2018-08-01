

<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");
    echo "<div style = 'border-bottom:solid 1px white;width:600px; opacity:0.9;filter:alpha(opacity=90);padding:10px;'>";
 ?>
 
 <?
        $itg_more_info=preg_replace("/[^0-9]*/i","",$_REQUEST['itg_more_info']); 
        $product = new itgProduct($itg_more_info);
        $props = $product->getProductProperties();    
    echo "<a  class=\"catalogdetaila\" href=\"#\"><img src={$props['image']}></a>";


        /*foreach ($props as $prop)
        {
            echo $prop."<br/>";
        }*/
            echo "<div style = ';display:block; margin:10px 0px 30px 100px;'><div>";
                echo "<span style = 'font-style:italic;font-size:12px;'>Наименование:</span>";
                echo "<div style = 'margin-left:50px;color:#353536;font-weight: bolder;'>{$props['Caption']}</div>";
            echo "</div>";
            echo "<div>";
                echo "<span style = 'font-style:italic;font-size:12px;'>Код:</span>";

                       $objTStr = new TemplatedString($props['ItemCode']);
                       $objTStr->SetTemplate($props["BrandCode"] );
                //$objTStr->SetColor("#EDAF1F");
                $objTStr->SetSelection($props['ItemCode']);
                echo "<div style = 'margin-left:50px;color:#353536;'>{$objTStr->GetTemplated()}</div>";
            echo "</div></div>";
            echo "<div>";
                echo "<span style = 'font-style:italic;font-size:12px;'>Описание:</span>";
                echo "<div>".$props['definition']."</div>";
            echo "</div>";
           # echo "<div style = 'float:right;position:relative; top:-10px;'> <a style ='text-decoration:none;color:#252525;' href='http://".$_SERVER["SERVER_NAME"]."/autodoc/search.php?ICODE=".$props['ItemCode']."&BCODE=".$props['BrandCode']."&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25'>купить &raquo;</a>
             echo "<div style = 'float:right;position:relative; top:-10px;'>";
            
             echo " <a id=\"OfferA\" style=' font-size:12px;color:#8a0101; font-style:italic; font-weight:bold;' href='#AcSearch'>
          <input type=\"hidden\" name=\"ItemCode\" value=\"".$props['ItemCode']."\">

         
         Купить</a></div>";
            //http://parts.avtodok.com.ua/autodoc/search.php?ICODE=0888080365&BCODE=916&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25                        
        echo "</div>";
?>