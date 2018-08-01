<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//echo "<pre>"; print_r($arParams); print_r($arResult); echo "</pre>";

if (count($arResult["ERRORS"]) > 0)
{
	foreach ($arResult["ERRORS"] as $key => $error)
	{
		if (intval($key) <= 0) 
			$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);
	}

	ShowError(implode("<br />", $arResult["ERRORS"]));
}
elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y")
{
	?><p  align="center" style="color: green ;font-weight:bold;"><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p><?
}?>



<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
<?
if (strlen($arResult["BACKURL"]) > 0)
{
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
}
?>

<table>
	<thead>
		<tr>
			<td colspan="2"><b><?=GetMessage("AUTH_REGISTER")?></b></td>
		</tr>
	</thead>
	<tbody>
<?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
		<tr>
			<!-- <td><?#=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?#if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?> <span class="starrequired">*</span> <?#endif?></td> -->  
			<? 
            if ($FIELD=="NAME")
            {                       echo "<td><p  id=\"nametitle\" style=\"display: block;\">";
                echo  GetMessage("REGISTER_FIELD_".$FIELD);
                
                echo ":";
                 if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y")
                 {
                     echo "<span class=\"starrequired\">*</span>";
                 }
                 echo "</p>";
                echo "</td>";
                                
            }elseif($FIELD=="LAST_NAME") 
            {
                echo "<td><p  id=\"lastnametitle\" style=\"display: block;\">";
                echo  GetMessage("REGISTER_FIELD_".$FIELD);
                
                echo ":";
                 if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y")
                 {
                     echo "<span class=\"starrequired\">*</span>";
                 }
                 echo "</p>";
                echo "</td>";
                
            }
            
            elseif ($FIELD=="MONTH_TURNOVER")
            {
                echo "<td><p  id=\"monthturnovertitle\" style=\"display:  none;\">";
                echo  GetMessage("REGISTER_FIELD_".$FIELD);
                
                echo ":";
                 if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y")
                 {
                     echo "<span class=\"starrequired\">*</span>";
                 }
                 echo "</p>";
                echo "</td>";
            } else
            {
                echo "<td>";
                echo GetMessage("REGISTER_FIELD_".$FIELD);
                 echo ":";
                 if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y")
                 {
                     echo "<span class=\"starrequired\">*</span>";
                 }
                 echo"</td>";
                
            }
            
            
            ?> 
            <td>
            <?
	switch ($FIELD)
	{
        case  "LOGIN" :
               ?><input size="30" id="login" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /> <br>
                 <text id="rightLog" style="color:red;"></text>
                 <p id="loginkeycheck"></p>
               <?
        break;
		case "PASSWORD":
        ?><input size="30" id="password" type="password" name="REGISTER[<?=$FIELD?>]" /><br><?
        break;
		case "CONFIRM_PASSWORD":
			?><input size="30" id="passwordconfirm" type="password" name="REGISTER[<?=$FIELD?>]" /><br><?
		break;

		case "PERSONAL_GENDER":
			?><select name="REGISTER[<?=$FIELD?>]">
				<option value=""> <?=GetMessage("USER_DONT_KNOW")?></option>
				<option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
				<option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
			</select><?
		break;

		case "PERSONAL_COUNTRY":
			//echo "<pre>"; print_r($arResult["COUNTRIES"]); echo "</pre>";
			?><select name="REGISTER[<?=$FIELD?>]"><?
			foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value)
			{
				?><option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
			<?
			}
			?></select><?
		break;

		case "PERSONAL_PHOTO":
		case "WORK_LOGO":
			?><input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" /><?
		break;

		case "PERSONAL_NOTES":
		case "WORK_NOTES":
			?><textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea><?
		break;
        case "MONTH_TURNOVER" :
       ?> 
       <select id="monthturnover"  name="REGISTER[<?=$FIELD?>]" style="display: none;">
              <option value="onebuy">Единоразовая покупка </option>  
             <option value="toonethousand">до 1000 USD </option>
              <option value="fromonetotow">1000-2000 USD </option>
              <option value="fromtowtofive">2000-5000 USD </option>
              <option value="fromfive">от 5000 USD </option>     
          </select><br>
          <input size="10" id="pricetype" type="text" disabled="disabled" title=""  value="Розница" style="float:left ; display: none;" />
       <?
        ?><!--<input size="30"  type="text" disabled="disabled" title="" name="REGISTER[<?=$FIELD?>]" value="Единоразовая покупка" />--> <?
        break;
        case "PARTS_MYCAR" :
        ?><input size="30" id="buyerMyauto" type="radio" name="REGISTER[BUYER]" checked="checked" value="MyAvto" />
        <input size ="20" id="carmodel" type="text" name ="REGISTER[CARMODEL]"  value="Марка Авто?" style="display:block; float:right;">           
        <?
        break;
        
        case "PARTS_SELLER" :
        ?><input size="30" id="buyerSeller" type="radio" name="REGISTER[BUYER]" value="Seller" />
          <div  id="parts_seller_info"  style="display:none; ">
          <p style="float: right;">Частный Предприниматель</p><input size="30"  type="radio" name="REGISTER[SELLER]" id="businessman" value="businessman" style="float:right ; " /> <br> 
          <p style="float: right;">Авто-Магазин</p><input size="30"  type="radio" name="REGISTER[SELLER]"  value="AutoShop" style="float:right ; " /> <br>
          <p style="float: right;">Интернет-Магазин</p><input size="30" type="radio" name="REGISTER[SELLER]" value="Internet_shop"  style="float:right" />  <br>
          <p style="float: right;">Разборка</p><input size="30" type="radio" name="REGISTER[SELLER]" value="AutoDemount"  style="float:right" />   <br>
          <p style="float: right;">Сто</p><input size="30" type="radio" name="REGISTER[SELLER]" value="Station"  style="float:right" />  <br>
          </div>
        <?
        break;
        case "PERSONAL_STATE":
        ?>
             <select id="personalstate"  name="REGISTER[<?=$FIELD?>]"> 
        <?
           $sql="SELECT* FROM b_user_regions ORDER BY REGIONNAME ASC";
           $resultregion=$DB->QUERY($sql);
           while($regionarray=$resultregion->Fetch())
           {
               echo "<option value='{$regionarray['ID']}'>{$regionarray['REGIONNAME']}</option>";
           }
        ?>     
              
             </select>  
        <? 
        break;
        case "PERSONAL_STREET":
          ?>
        
        <p>ул.<input size="30" id="PERSONAL_STREET" type="text" name="REGISTER[<?=$FIELD?>]"/>
        №<input size="5" id="PERSONAL_HOUSE" type="text" name="REGISTER[PERSONAL_HOUSE]" /></p> 
        
        <?
        break; 
        case "WORK_COMPANY":
        ?>
        
        <input size="30" id="WORK_COMPANY" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><? 
        break;
        case "PERSONAL_PHONE" :
            ?>
         <p>(<input size="6" maxlength="6" id="PERSONAL_PHONECODE" type="text" name="REGISTER[PERSONAL_PHONECODE]"  />)
        <input size="15"  maxlength="15" id="PERSONAL_PHONE" type="text" name="REGISTER[<?=$FIELD?>]"  /></p> <? 
        break;
        case "NAME":
        ?> <p><p><input size="30"  type="radio" id="person" checked="checked" name="person"  value="Person" style="float:left ; " />Частное Лицо</p>
        <p><input size="30"  type="radio" id="firm" name="person"  value="Firm" style="float:left ; " /> Юр. Лицо
        </p></p><br> 
        <input size="30" id="NAME" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" style="float:left ; " /><? 
        break;
        case "LAST_NAME":
        ?><input size="30" id="LAST_NAME" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><? 
        break;
		default:
			if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
			?><input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
				if ($FIELD == "PERSONAL_BIRTHDAY")
					$APPLICATION->IncludeComponent(
						'bitrix:main.calendar',
						'',
						array(
							'SHOW_INPUT' => 'N',
							'FORM_NAME' => 'regform',
							'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
							'SHOW_TIME' => 'N'
						),
						null,
						array("HIDE_ICONS"=>"Y")
					);
				?><?
	}?></td>
		</tr>
        <tr>
        <td>-------</td>
        <td>-----</td> 
        </tr>
<?endforeach?>
<?// ********************* User properties ***************************************************?>
<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
	<tr><td colspan="2"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></td></tr>
	<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
	<tr><td><?=$arUserField["EDIT_FORM_LABEL"]?>:<?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?></td><td>
			<?$APPLICATION->IncludeComponent(
				"bitrix:system.field.edit",
				$arUserField["USER_TYPE"]["USER_TYPE_ID"],
				array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
	<?endforeach;?>
<?endif;?>
<?// ******************** /User properties ***************************************************?>
<?
/* CAPTCHA */
if ($arResult["USE_CAPTCHA"] == "Y")
{
	?>
		<tr>
			<td colspan="2"><b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("REGISTER_CAPTCHA_PROMT")?> <span class="starrequired">*</span>:</td>
			<td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
	<?
}
/* CAPTCHA */
?>
	</tbody>
	<tfoot>
        <tr>
        <td><p style="color: red;">Подтвердите, что вы <br>ознакомлены с  правилами работы<br> на сайте</p><br>
        <a href="/rules/" target="_blank" style="color: red;">Правила работы</a>
        </td> 
        <td><input type="checkbox" id="rules"  /></td> 
        </tr>
		<tr>
			<td></td>
			<td><input type="submit" id="register_submit_button" disabled="disabled" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" /></td>
		</tr>
	</tfoot>
</table>
<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
<p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>

</form>

<script>
$(function()
{
 $("#rules").click(function()
  {
      if( $(this).attr("checked")=="checked")
      {
        $('#register_submit_button').removeAttr("disabled");
           
      }
      else
      {
            $('#register_submit_button').attr("disabled","disabled");
      }
  }
 )   
 $("#person").click( function()
 {
    $("#nametitle").text('Имя.*');
    $("#LAST_NAME").css("display","block");
    $("#lastnametitle").css("display","block"); 
    $("#LAST_NAME").val(''); 
     
 }
 );   
$("#firm").click( function()
 {
    $("#nametitle").text('Название организации.*');
    $("#LAST_NAME").val('NOT_USED');
    $("#LAST_NAME").css("display","none");
     $("#lastnametitle").css("display","none"); 
    
     
 }
 );       
    
    
 $("#monthturnover").change(function()
 {
     if ($(this).val()=="onebuy" )
     {
         $(this).val("toonethousand");
        $("#pricetype").val("Мелкий опт"); 
     }
     if(($(this).val()=="toonethousand" ))
     {
         $("#pricetype").val("Мелкий опт"); 
     }
     if(($(this).val()=="fromonetotow" ))
     {
         $("#pricetype").val("Опт"); 
     }
     if(($(this).val()=="fromtowtofive" ))
     {
         $("#pricetype").val("Спец-цена"); 
     }
     
     
 }
 );
  $("#buyerSeller").click(function()
  {
       $("#carmodel").attr("disabled","disabled");       
      
       $("#parts_seller_info").css("display","block");
       $("#monthturnover").css("display","block");
        $("#monthturnovertitle").css("display","block");
       $("#monthturnover").val("toonethousand");
        $("#pricetype").val("Мелкий опт");
        $("#businessman").attr("checked","checked"); 
  });
  $("#buyerMyauto").click(function() 
  {
    $("#carmodel").removeAttr("disabled");
    
       $("#parts_seller_info").children().removeAttr("checked"); 
       $("#parts_seller_info").css("display","none");  
       $("#monthturnover").css("display","none");
        $("#monthturnovertitle").css("display","none"); 
       $("#monthturnover").val("onebuy");
       $("#pricetype").val("Розница"); 
  }
  );
  $("#carmodel").focus( function()
  {
      if ($(this).val()=="Марка Авто?")
      { 
      $(this).val('');
      }
  }
  
  );
  $("#carmodel").focusout( function()
  {
      if ($(this).val()=="")
      {
          $(this).val('Марка Авто?')
      }
  }
  
  );
  $('#login').focusout(function()
    {
        txt = $(this).val();
        //alert(txt);
        if(txt.length >= 6)
        {
            var params = {login: $('#login').val()}
         $.get("/auth/Registration/ajaxRequest.php", params, function(data)
         {
             triggerLogin = Number(data);
             //alert(trigger)
             //if (data == '0') return false;//такого логина нету значит можно оставить
             //else return true;
             
         });
            $.ajax({
               complete: function()
               {
                    if(triggerLogin == 1)
                    {
                        //alert('error');
                        $('#rightLog').show().text('Данный логин присутствует! выберите другой');
                           $('#rightLog').css("color","red");
                          /* $('#register_submit_button').attr("disabled","disabled");*/ 
                          $('#rules').attr("disabled","disabled");
                    }
                    else
                    {
                        $('#rightLog').show().text('Логин может быть использован.');
                        $('#rightLog').css("color","green");
                       /* $('#register_submit_button').removeAttr("disabled");*/ 
                       $('#rules').removeAttr("disabled"); 
                    }
               }
            });
        }
        else (txt.length < 6)
        {
            $('#rightLog').show().text('Логин должен содержать не менее 6 символов!');
             $('#rightLog').css("color","red");
             /*$('#register_submit_button').attr("disabled","disabled");*/ 
             $('#rules').attr("disabled","disabled");
        }
        
    });
  
  $('#login').keyup(function()
    {
        txt = $(this).val();
        //alert(txt);
        if(txt.length >= 6)
        {
            var params = {login: $('#login').val()}
         $.get("/auth/Registration/ajaxRequest.php", params, function(data)
         {
             triggerLogin = Number(data);
             //alert(trigger)
             //if (data == '0') return false;//такого логина нету значит можно оставить
             //else return true;
             
         });
            $.ajax({
               complete: function()
               {
                    if(triggerLogin == 1)
                    {
                        //alert('error');
                        $('#rightLog').show().text('Данный логин присутствует! выберите другой');
                           $('#rightLog').css("color","red");
                          /* $('#register_submit_button').attr("disabled","disabled"); */ 
                           $('#rules').attr("disabled","disabled"); 
                    }
                    else
                    {
                        $('#rightLog').show().text('Логин может быть использован.');
                        $('#rightLog').css("color","green");
                       /* $('#register_submit_button').removeAttr("disabled");*/  
                          $('#rules').removeAttr("disabled"); 
                    }
               }
            });
        }
        else (txt.length < 6)
        {
            $('#rightLog').show().text('Логин должен содержать не менее 6 символов!');
             $('#rightLog').css("color","red");
            /* $('#register_submit_button').attr("disabled","disabled");*/
             $('#rules').attr("disabled","disabled");  
        }
        
    });
    
    $("#login").keyup(function(event) {
        // Разрешаем нажатие клавиш backspace, del, tab и esc
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
             // Разрешаем выделение: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
             // Разрешаем клавиши навигации: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39) ||
            event.keyCode == 190
            ) {
                
                 return;
        }
        else {
            // Запрещаем всё, кроме клавиш цифр на основной клавиатуре, а также Num-клавиатуре
            if ((event.keyCode < 48 || event.keyCode > 90) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault();
              
                            
            } else
            {
                  
                  loginval=$("#login").val();
                  countcheck=0;
                  bval=0;
                   /*$("#loginkeycheck").text("код"+loginval.length);*/ 
                  if (loginval.length>0)
            { 
                
                  for (var i=0; i<loginval.length; i++    )
                  { 
                   bval=loginval.charCodeAt(i);
                   if (bval>31 && bval<128)
                   {
                        $("#loginkeycheck").text("код"+bval+loginval.length); 
                   }else
                   {
                        
                        countcheck++;
                         
                   }
                      
                  }
            }  
                  if (countcheck>0)
                  {
                    $("#loginkeycheck").text("Есть недопустимые символы!.Только латинский алфавит.") ;
                    $('#password').attr("disabled","disabled");
                    $('#password').val("");  
                  }else
                  {
                      $("#loginkeycheck").text("") ; 
                     $('#password').removeAttr("disabled");   
                  }
              
            } 
        }
    });
    
    
$(document).ready(function()                              
{
  $("#login").trigger("focusout");      
        
});

});
</script>
