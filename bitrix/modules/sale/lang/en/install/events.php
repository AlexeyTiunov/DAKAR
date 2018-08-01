<?
$MESS["SALE_NEW_ORDER_NAME"] = "New order";
$MESS["SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - Order ID
#ORDER_DATE# - Order date
#ORDER_USER# - User
#EMAIL# - User E-Mail
#BCC# - BCC E-Mail
#ORDER_LIST# - Order list
#SALE_EMAIL# - Sales department e-mail";
$MESS["SALE_NEW_ORDER_SUBJECT"] = "#SITE_NAME#: New order N#ORDER_ID#";
$MESS["SALE_NEW_ORDER_MESSAGE"] = "Order confirmation from #SITE_NAME#\n".
"------------------------------------------\n".
"\n".
"Dear #ORDER_USER#,\n".
"\n".
"Your order #ORDER_ID# from #ORDER_DATE# has been accepted.\n".
"\n".
"Order value: #PRICE#.\n".
"\n".
"Ordered items:\n".
"#ORDER_LIST#\n".
"\n".
"You can monitor processing of your order (view current status \n".
"of order) by entering your personal site section at  #SITE_NAME#.\n".
"Note that that you will need login and password for entering this\n".
"site section at #SITE_NAME#.\n".
"\n".
"To cancel your order please use special option available in your\n".
"personal section at #SITE_NAME#.\n".
"\n".
"Please note that you should specify your order ID:  #ORDER_ID#\n".
"when requesting any information from site administration at  #SITE_NAME#.\n".
"\n".
"Thanks for ordering!\n";

$MESS["SALE_ORDER_CANCEL_NAME"] = "Cancel order";
$MESS["SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - Order ID
#ORDER_DATE# - Order date
#EMAIL# - User E-Mail
#ORDER_CANCEL_DESCRIPTION# - Order cancel description
#SALE_EMAIL# - Sales department e-mail";
$MESS["SALE_ORDER_CANCEL_SUBJECT"] = "#SITE_NAME#: Order N#ORDER_ID# was canceled";
$MESS["SALE_ORDER_CANCEL_MESSAGE"] = "Informational message from #SITE_NAME#\n".
"------------------------------------------\n".
"\n".
"Order ##ORDER_ID# from #ORDER_DATE# is canceled.\n".
"\n".
"#ORDER_CANCEL_DESCRIPTION#\n".
"\n".
"#SITE_NAME#\n";

$MESS["SALE_ORDER_PAID_NAME"] = "Paid order";
$MESS["SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - Order ID
#ORDER_DATE# - Order date
#EMAIL# - User E-Mail
#SALE_EMAIL# - Sales department e-mail";
$MESS["SALE_ORDER_PAID_SUBJECT"] = "#SITE_NAME#: Order N#ORDER_ID# was paid";
$MESS["SALE_ORDER_PAID_MESSAGE"] = "Informational message from #SITE_NAME#\n".
"------------------------------------------\n".
"\n".
"Order ##ORDER_ID# from #ORDER_DATE# was paid.\n".
"\n".
"#SITE_NAME#\n";

$MESS["SALE_ORDER_DELIVERY_NAME"] = "Order delivery allowed";
$MESS["SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - Order ID
#ORDER_DATE# - Order date
#EMAIL# - User E-Mail";
$MESS["SALE_ORDER_DELIVERY_SUBJECT"] = "#SITE_NAME#: Delivery of order N#ORDER_ID# is allowed";
$MESS["SALE_ORDER_DELIVERY_MESSAGE"] = "Informational message from #SITE_NAME#\n".
"------------------------------------------\n".
"\n".
"Delivery of order ##ORDER_ID# from #ORDER_DATE# is allowed.\n".
"\n".
"#SITE_NAME#\n";

$MESS["SALE_RECURRING_CANCEL_NAME"] = "Recurring payment canceled";
$MESS["SALE_RECURRING_CANCEL_DESC"] = "#ORDER_ID# - Order ID
#ORDER_DATE# - Order date
#EMAIL# - User E-Mail
#CANCELED_REASON# - Reason
#SALE_EMAIL# - Sales department e-mail";
$MESS["SALE_RECURRING_CANCEL_SUBJECT"] = "#SITE_NAME#: Recurring payment was canceled";
$MESS["SALE_RECURRING_CANCEL_MESSAGE"] = "Informational message from #SITE_NAME#\n".
"------------------------------------------\n".
"\n".
"Recurring payment was canceled\n".
"\n".
"#CANCELED_REASON#".
"\n".
"#SITE_NAME#\n";

?>