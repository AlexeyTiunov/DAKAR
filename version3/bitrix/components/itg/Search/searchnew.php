<? 

 require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");   
 require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/timer.php"); 
 require ("/media/Vol/www/bitrix/components/itg/Search/Search_ITG4.php"); 
 require ("/media/Vol/www/bitrix/components/itg/IB.property/IBPropertyAdvanced1.php");
 global $USER;
 $APPLICATION->SetTitle("запчасти для иномарок");
 $APPLICATION->SetPageProperty("keywords", "Запчасти поиск Киев Украина"); 
 $APPLICATION->SetPageProperty("description", "Поиск запчастей для всех иномарок");      
 $timer = new timer(); 
 $timer->start_timer();

 
 $regionID = 17;
            $brandID = 14;
            $oRegion = new IBPropertyAdvanced_ITG(array('IB'=>$regionID));
            $arRegions = $oRegion->getArray();
            $oBrand = new IBPropertyAdvanced_ITG(array('IB'=>$brandID));
            $arBrands = $oBrand->getArray();
            $_SESSION['arRegion_ITG'] = $arRegions;
            $_SESSION['arBrands_ITG'] = $arBrands;
            $_SESSION['CountScript'] =0;
            
            
            function CheckCondConfirm($USERID)
    {
        global $DB;
        $sql="SELECT CondConfirm FROM b_user WHERE ID='".$USERID."' LIMIT 1";
        $result = $DB->Query( $sql );
        
        if ($CondConfirm=$result->Fetch()) 
        {
            if ($CondConfirm['CondConfirm']==1 )
            {
               return true;
            }
            else
            {
               return false; 
            }
            
        } else
        {
               return false;
        }
        
        
        
    }  
    $CondConfirm=CheckCondConfirm($USER->GetID());
         if (!$CondConfirm && $USER->IsAuthorized())
         {
           ?>
           <p align="center" style="color: red;">Ув.Клиент.</p>
           <p align="center" style="color: red;">Для дальнейшей работы с системой поиска и подбора </p> 
           <p align="center" style="color: red;">деталей, вам необходимо ознакомится </p> 
           <p align="center" style="color: red;"> <strong>с условиями работы и подтвердить свое согласие.</strong> </p>
            <p align="center" style="color: red;"> Для этого перейдите на <a href="/docc/">страницу с условиями работы</a>.
             </p>  
           <?   
           exit;  
         }   
      ?>   
    <div  id="HeadOfSearch"> 
      <table border="0" cellspacing="1" cellpadding="3" width="100%" class="list-table" >
    <tr class="head">
        <td valign="middle" colspan="2" align="center" nowrap><div id="search_name" style="font-size:20px;">Поиск по коду Детали </div></td>
    </tr>
    <tr class="head">
        <td valign="middle" colspan="2" align="center" nowrap><div id="search_name" style="font-size:14px;">
       <div style="width:100%"> Ув. Клиенты и Гости нашего сайта.<br>
        Для качественного поиска запчастей MERCEDES-BENZ  просим вас  обращать внимание<br>
        на наличие буквы "A" в начале номера детали. <br>
         Т.е. Если  маркировка запчасти указанна без буквы "A",<br>
         то в 99% случаев будет правильно осушествлять  поиск подставив вперед  эту букву .<br>
        Пример: A1663200325  = 1663200325.
        </div>
        </td>
    </tr> 
    <tr>
        <td align="right" nowrap valign="top">
            Код (или часть кода) запчасти:
        </td>
        <td align="left" nowrap>
           <input  id="cod" type="text" name="ICODE" size="40" value="<? if( isSet( $_REQUEST["ICODE"] ))  echo htmlspecialchars($_REQUEST["ICODE"]);?>">
        </td>
     </tr>
     
<?
         // заполняем массив "код бренда" -> Название бренда
        //$arBrands = GetBrandCaptionsArray();
       # if (/*Appearance_ITG::isFirstEnter()*/true)
       # {
            
            
            
           # $regionID = 17;
           # $brandID = 14;
           # $oRegion = new IBPropertyAdvanced_ITG(array('IB'=>$regionID));
            #$arRegions = $oRegion->getArray();
           # $oBrand = new IBPropertyAdvanced_ITG(array('IB'=>$brandID));
           # $arBrands = $oBrand->getArray();
            #$_SESSION['arRegion_ITG'] = $arRegions;
           # $_SESSION['arBrands_ITG'] = $arBrands;
       # }
       # else 
      #  {
            $arRegions = $_SESSION['arRegion_ITG'];
            $arBrands = $_SESSION['arBrands_ITG'];
       # }
        
        #echo "<pre>";
        #print_r($arRegions['Code']);
        #echo "</pre>"; 
        #$UID = GetUserID_1CByID($USER->GetID());
        
        $user = ($UID)?$UID:0;
        $pg = isset($_GET["pg"])?$_GET["pg"]:0;
        if(isSet($_GET["pg"]))
        {?>
               <input type="hidden" name="BCODE"  value="<?=$_REQUEST["BCODE"];?>" />
<?        }
        else
        {
            if( isset( $_REQUEST["BCODE"] ) )//&&  ( $lastICode == $_REQUEST["ICODE"] ) && ( $lastBCode == $_REQUEST["BCODE"] )  )
            {?>
                <input type="hidden" name="BCODE"  value="<?=$_REQUEST["BCODE"];?>" >
<?            }
            else
            {?>
                <input type="hidden" name="BCODE"  value="<?=ALL_BRANDS?>" >
<?            }
        }?>
        <input type="hidden" name="REGION" value="<?=ALL_REGIONS?>">
        <input type="hidden" name="USER"  value="<?=$user;?>" />
        <input type="hidden" name="pg"  value="<?=$pg;?>" />

    <tr>
        <td align="right" nowrap valign="top">
            Сортировка результата:
        </td>
        <td align="left" nowrap>
            <select name="CMB_SORT" gtbfieldid="78">
<?  if( !isSet($_REQUEST["CMB_SORT"])) $_REQUEST["CMB_SORT"] = "PRICE";?>
            <option value="REGION" <? if( $_REQUEST["CMB_SORT"] == "REGION" ) echo "selected='selected'"; ?> >По региону</option>
            <option value="PRICE" <? if( $_REQUEST["CMB_SORT"] == "PRICE" ) echo "selected='selected'";?>>По цене</option>
        </td>
    </tr>
    <tr>
        <td align="right" nowrap valign="top">
            Строк на странице:
        </td>
        <td align="left" nowrap>
            <select name="NUM_PAG" gtbfieldid="78">
<?  if( !isSet($_REQUEST["NUM_PAG"])) $_REQUEST["NUM_PAG"] = "25";?>
            <option value="10" <? if( $_REQUEST["NUM_PAG"] == "10" ) echo "selected='selected'"; ?> >10</option>
            <option value="25" <? if( $_REQUEST["NUM_PAG"] == "25" ) echo "selected='selected'";?>>25</option>
            <option value="50" <? if( $_REQUEST["NUM_PAG"] == "50" ) echo "selected='selected'";?>>50</option>
        </td>
    </tr>
    <tr>
        <td align="right" nowrap valign="top">Валюта пересчета:</td>
        <td><select name="CURRENCY">
<?            #$lcur = CCurrency::GetList(($b="name"), ($order1="asc"), LANGUAGE_ID);
            #if(!isSet( $_REQUEST["CURRENCY"]))  
            $_REQUEST["CURRENCY"] = "USD";

            #while($lcur_res = $lcur->Fetch())
            #{
               # $cur = $lcur_res["CURRENCY"];
               # $curName = $cur;//." - ". CCurrencyRates::ConvertCurrency( 1, $cur, "UAH" ) . " грн. ";    // курс
?>
                <option value="USD" <? if( $_REQUEST["CURRENCY"]) echo 'selected="selected"'; ?>>USD</option>
<?            #}?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" nowrap valign="top">
            &nbsp;
        </td>
        <td align="left" nowrap>   
          
           
        </td>
    </tr>
</table>
 </div>
      <?      
 
/* require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG4.php"); 
 #global $DB;
 
         $port=31006;
        $DBB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DBB->set_charset("utf8");
              $DBB->query("SET NAMES 'utf8'");
             $DBS=&$DBB ;
       $customer= new Customer($DBS) ;
       $result= $customer->getSQLres()  ;
        #print_r($result);  
        foreach ($result as $res=>$key)
        {
            foreach($key as $ar=>$ky)
            {
                 echo $ky;
            }
            
        }  */
       # $time =$timer->end_timer();
   
          
        echo '<div align="center"><span > <input   id="bt" class="btt" type="submit" value="Искать" name="submit_btn"></span></div> '     ;
       # echo "<a id='but'>qqq</a>";
      
       # echo "<br> время".$time;
        /* echo " <script>
             $('#bt').click(function() 
             { 
                 var p=$('#but').html();
                  if (p=='qqq')
                  {
                $('#but').html('www') ;
                  }
                  else
                  {
                   $('#but').html('qqq') ; 
                  }
              }
             )
             </script>"; */
           
           
         
           
           
             
             
             
             
             
        
 require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");  
?>