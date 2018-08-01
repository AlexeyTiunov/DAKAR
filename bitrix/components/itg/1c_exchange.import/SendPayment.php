<?php
@ini_set('memory_limit','512M');
@set_time_limit(0);
class SendPayment
{
	private $paymentIB = 25;
	private $itemsIB = 26;
	function __construct()
	{
		global $DB;
		if(CModule::IncludeModule("iblock"))
		{
			$arSelect = array("ID", "NAME", "PROPERTY_Code", "PROPERTY_DocumentDate", "PROPERTY_AgreementCode", "PROPERTY_CurrencyCode", "PROPERTY_ClientCode");
			$arFilter = array("IBLOCK_ID"=>$this->paymentIB,"ACTIVE"=>"Y","!PROPERTY_Email"=>'1',">=PROPERTY_Summ"=>0,"PROPERTY_Status"=>"Отобрано");//получаем не отосланные накладные
			$res = CIBlockElement::GetList(array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"),$arFilter,false,false,$arSelect);
			while ($arFieldIds = $res->GetNext())
			{
				$arSelectItems = array("ID", "NAME", "PROPERTY_BCode", "PROPERTY_ICode", "PROPERTY_Quantity", "PROPERTY_Price", "PROPERTY_Summ", "PROPERTY_Caption", "PROPERTY_Order");
				$arFilterItems = array("IBLOCK_ID"=>26,"ACTIVE"=>"Y","PROPERTY_ShippingID"=>$arFieldIds['ID']);
				$resItems = CIBlockElement::GetList(array(),$arFilterItems,false,false,$arSelectItems);//получаем список строк по накладной
				unset($rowXLS);
				$rowXLS = array();
				while ($arFieldItem = $resItems->GetNext())
				{
					$rowXLS[] = $arFieldItem;
				}
				require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/excel/Classes/PHPExcel/IOFactory.php";//подключаем класс EXCEL
				$objReader = PHPExcel_IOFactory::createReader('Excel5');
				$objPHPExcel = $objReader->load("templates/31template.xls");
				
				$objWorksheet = $objPHPExcel->getActiveSheet();
				$objWorksheet->getCell('B2')->setValue("Расходная накладная №{$arFieldIds['PROPERTY_CODE_VALUE']} от {$arFieldIds['PROPERTY_DOCUMENTDATE_VALUE']}");
				
				$sqlSelectAgreement = $DB->Query("SELECT `Caption` FROM `b_autodoc_agreements` 
																		WHERE 	`Code`='{$arFieldIds['PROPERTY_AGREEMENTCODE_VALUE']}'
																			AND 
																				`ClientCode`='{$arFieldIds['PROPERTY_CLIENTCODE_VALUE']}'");
				$sqlAgreement = $sqlSelectAgreement->Fetch();
				$objWorksheet->getCell('B4')->setValue("Договор: {$sqlAgreement['Caption']}");
				$objWorksheet->getCell('B5')->setValue("Валюта: {$arFieldIds['PROPERTY_CURRENCYCODE_VALUE']}");
				
				$sqlSelectEmail = $DB->Query("SELECT `EMAIL` FROM `b_user` WHERE `ID_1C`='{$arFieldIds['PROPERTY_CLIENTCODE_VALUE']}'");
				$sqlEmail = $sqlSelectEmail->Fetch();
				
				$baseRow = 11;
				foreach($rowXLS as $r => $dataRow) {
					$row = $baseRow + $r;
					$objWorksheet->insertNewRowBefore($row,1);
				
					$objWorksheet->setCellValue('B'.$row, $r+1);
					$objWorksheet->setCellValue('C'.$row, $dataRow['PROPERTY_ICODE_VALUE']);
					$objWorksheet->setCellValue('D'.$row, $dataRow['PROPERTY_CAPTION_VALUE']);
					$objWorksheet->setCellValue('E'.$row, $dataRow['PROPERTY_QUANTITY_VALUE']);
					$objWorksheet->setCellValue('F'.$row, $dataRow['PROPERTY_PRICE_VALUE']);
					$objWorksheet->setCellValue('G'.$row, $dataRow['PROPERTY_SUMM_VALUE']);
					$objWorksheet->setCellValue('H'.$row, $dataRow['PROPERTY_ORDER_VALUE']);
				}
				$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
				//echo date('H:i:s') . " Write to Excel5 format\n";
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$theFile = "sheepment{$arFieldIds['PROPERTY_CODE_VALUE']}.xls";
				$objWriter->save($theFile);

				$files = array($theFile);
				//echo $sqlEmail['EMAIL']."<br />";
				$to = "{$sqlEmail['EMAIL']}, gurkovsky@itekgold.com";
				$sendermail = "avtodok";
				//$this->multi_attach_mail($to, $files, $sendermail);
				
				ini_set('include_path',$_SERVER["DOCUMENT_ROOT"]."/PEAR".PATH_SEPARATOR.ini_get('include_path'));
				require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Mail/Mail.php";
				require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Mail_Mime/mime.php";
				$text = 'Сформирована расходная накладная';
				$html = '<html><body>Сформирована расходная накладная</body></html>';
				//$crlf = "\r\n";
				$hdrs = array(
				              'From'    => 'parts@parts.avtodok.com.ua',
				              'Subject' => 'Расходная накладная'
				              );
				$params['head_charset'] = 'utf-8';
				$params['text_charset'] = 'utf-8';
				$params['html_charset'] = 'utf-8';
				$mime = new Mail_mime($params);

				$mime->setTXTBody($text);
				$mime->setHTMLBody($html);
				$mime->addAttachment($theFile,'application/octet-stream',$theFile,true);

				// do not ever try to call these lines in reverse order
				// when using versions older than 1.6.0
				$body = $mime->get();
				// or in 1.6.0 and newer
				// $body = $mime->getMessageBody();
				
				$hdrs = $mime->txtHeaders($hdrs);
				$boundary = $mime->getParam('boundary');
				$_headers        =   array(
										  'From'         => 'parts@parts.avtodok.com.ua',
										  'To'           => 'gurkovsky@itekgold.com',
										  'Subject'      => "АВТОДОК ПАРТС Расходная накладная №{$arFieldIds['PROPERTY_CODE_VALUE']}",
										  'Content-Type' => "multipart/related; boundary=\"{$boundary}\"",
										  'MIME-Version' => '1.0'); 
				$mail =&Mail::factory('mail');
				$m = $mail->send($to, $_headers, $body);
				//echo "<pre> hdrs";
				//print_r($body);
				//echo "</pre>";
				unlink($theFile);
				if ($m)
				{
					$sqlUpdate = "UPDATE `b_iblock_element_prop_s25` SET `PROPERTY_229`='1' WHERE `IBLOCK_ELEMENT_ID`='{$arFieldIds['ID']}'";//записываем факт отправки по почте
					$DB->Query($sqlUpdate);
				}
			}
		}
	}
	private function multi_attach_mail($to, $files, $sendermail)
	{
		// email fields: to, from, subject, and so on
		$from = "АВТОДОК ПАРТС <info@parts.avtodok.com.ua>";
		$subject = "Расходная накладная";
		$message = "Если Вы получили это сообщение, значит Вы должны ящик пива Тохе\r\n<br />";
		$message .= "Открыв вложенный файл Вы подтверждаете что до конца недели выставите Тохе ПИВО";
		$headers = "From: $from";
		
		// boundary
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
				 
				    // headers for attachment
		$headers .= "\r\nMIME-Version: 1.0\r\n" . "Content-Type: multipart/mixed;\r\n" . " boundary=\"{$mime_boundary}\"";
				 
		// multipart boundary
		$message = "--{$mime_boundary}\r\n" . "Content-Type: text/html; charset=\"utf-8\"\r\n" .
				   "Content-Transfer-Encoding: 8bit\n\r\n" . $message . "\n\r\n";
				 
		// preparing attachments
		for($i=0;$i<count($files);$i++)
		{
	        if(is_file($files[$i]))
	        {
	            $message .= "--{$mime_boundary}\r\n";
	            $fp = @fopen($files[$i],"rb");
	        	$data = @fread($fp,filesize($files[$i]));
	            @fclose($fp);
	            $data = chunk_split(base64_encode($data));
	            $message .= "Content-Type: application/vnd.ms-excel; name=\"".basename($files[$i])."\"\r\n" .
	            "Content-Description: ".basename($files[$i])."\n" .
	            "Content-Disposition: attachment;\r\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\r\n" .
	            "Content-Transfer-Encoding: base64\n\r\n" . $data . "\n\r\n";
            }
        }
		$message .= "--{$mime_boundary}--";
    	$returnpath = "-f" . $sendermail;
    	$ok = mail($to, $subject, $message, $headers, $returnpath);
    	if($ok){ return $i; } else { return 0; }
	}
}
?>