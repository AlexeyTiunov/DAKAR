			</div>
		
		</div>

		<div id="space-for-footer"></div>
	</div>
	
	<div id="footer">
	
		<div id="copyright">
<?
$APPLICATION->IncludeFile(
	SITE_DIR."include/copyright.php",
	Array(),
	Array("MODE"=>"html")
);
?>
		</div>
		
<?
$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
	"ROOT_MENU_TYPE" => "bottom",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);
?>
		
	</div>
</body>
</html>