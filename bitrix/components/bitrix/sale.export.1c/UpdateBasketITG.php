<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
class UpdateBasketITG
{
    public static $statusOrder             = array(0=>"N",
                                                1=>"A",
                                                2=>"B",
                                                3=>"C",
                                                4=>"F");
    public static $statusOrderName        = array("N"=>"Заказ рассматривается",
                                                "A"=>"Заказ подтвержден",
                                                "B"=>"Заказ отложен",
                                                "C"=>"Заказ закрыт",
                                                "F"=>"Заказ выполнен");
    /*
     * N - рассматривается
     * A - подтвержден
     * B - отложен
     * C - закрыт
     * F - выполнен
     */
    public $user;
    public $dateInsert;
    public $status;
    public $currency;
    protected $changeOrder             = array("STATUS"=>false,"CURRENCY"=>false,"SUM"=>false);
    protected $changeItemsStatus     = array();
    protected $changeItemsStatusQuantity     = array();
    protected $changeItemsStatus2     = array();  
    //protected $changeItemsCurrency     = array();
    protected $changeItemsPrice     = array();
    protected $changeItemsDelete     = array();
    protected $changeItemsAdd         = array();
    protected $basket;
    protected $region;
    //protected $amount;

    
    
    function __construct($orderId,$items)
    {
        global $DB;
        //if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
        if (CModule::IncludeModule("sale"))
        {
            if (!($arOrder = CSaleOrder::GetByID($orderId)))
            {
                $sqlSelectUser = "SELECT `ID` FROM `b_user` WHERE `LOGIN`='{$items['LOGIN']}'";
                $resLog = $DB->Query($sqlSelectUser);
                if (($log = $resLog->Fetch()))
                {
                    if ($items["CURRENCY"] == "грн") $items["CURRENCY"] = "UAH";//исправляем валюту пришедшую с 1С
                    $userID = $log['ID'];
                    $sqlInsertOrder = "INSERT INTO `b_sale_order` (
                                                                    `ID`,
                                                                    `LID`,
                                                                    `PERSON_TYPE_ID`,
                                                                    `PAYED`,
                                                                    `CANCELED`,
                                                                    `STATUS_ID`,
                                                                    `DATE_STATUS`,
                                                                    `PRICE_DELIVERY`,
                                                                    `PRICE`,
                                                                    `CURRENCY`,
                                                                    `DISCOUNT_VALUE`,
                                                                    `USER_ID`,
                                                                    `PAY_SYSTEM_ID`,
                                                                    `DATE_INSERT`,
                                                                    `DATE_UPDATE`,
                                                                    `USER_DESCRIPTION`,
                                                                    `ADDITIONAL_INFO`,
                                                                    `RECOUNT_FLAG`,
                                                                    `REGION_CODE`,
                                                                    `IS_MAIL_SENT`
                                                                    ) 
                                                                    VALUES 
                                                                    (
                                                                    '{$orderId}',
                                                                    's1',
                                                                    '1',
                                                                    'N',
                                                                    'N',
                                                                    'N',
                                                                    NOW(),
                                                                    '0.00',
                                                                    '".$items['AMOUNT']."',
                                                                    '".$items['CURRENCY']."',
                                                                    '0.00',
                                                                    '{$userID}',
                                                                    '1',
                                                                    NOW(),
                                                                    NOW(),
                                                                    'добавлено 1с',
                                                                    '".$items['AGREEMENT']."',
                                                                    'Y',
                                                                    '".$items['REGION']."',
                                                                    '1')";
                    $DB->Query($sqlInsertOrder);
                    $arOrder = CSaleOrder::GetByID($orderId);
                }
            }
            if (($arOrder = CSaleOrder::GetByID($orderId)))
            {
                $triggerToUpdateOrder = false;
                
                $this->basket         = $orderId;
                $this->user         = $arOrder["USER_ID"];
                $this->dateInsert     = $arOrder["DATE_INSERT_FORMAT"];
                $this->currency        = $items["CURRENCY"];
                //$this->amount        = $items["AMOUNT"];
                $this->status        = self::$statusOrderName[self::$statusOrder[$items["STATUS"]]];
                @$this->region        = $arOrder["REGION_CODE"];
                
                if (self::$statusOrder[$items["STATUS"]] != $arOrder["STATUS_ID"])//фиксируем изменения статуса
                {
                    $this->changeOrder["STATUS"] = array(    "OLD"    =>$arOrder["STATUS_ID"],
                                                            "NEW"    =>self::$statusOrder[$items["STATUS"]]);
                    $triggerToUpdateOrder = true;
                }
                if ($items["CURRENCY"] == "грн") $items["CURRENCY"] = "UAH";//исправляем валюту пришедшую с 1С
                $this->currency = $items["CURRENCY"];
                if ($items["CURRENCY"] != $arOrder["CURRENCY"])//фиксируем изменение валюты
                {
                    $this->changeOrder["CURRENCY"] = array(    "OLD"    =>$arOrder["CURRENCY"],
                                                            "NEW"    =>$items["CURRENCY"]);
                    $triggerToUpdateOrder = true;
                }
                if ($items["AMOUNT"] != $arOrder["PRICE"])//фиксируем изменение суммы заказа
                {
                    $this->changeOrder["SUM"] = array(    "OLD"    =>$arOrder["PRICE"],
                                                        "NEW"    =>$items["AMOUNT"]);
                    $triggerToUpdateOrder = true;
                }
                $triggerToUpdateOrder = true;//обновляемся в любом случае ВРЕМЕННО!!!!!!
                if ($triggerToUpdateOrder)//обновляем все данные если хотя бы одно свойство изменено
                {
                    $propsOrderFrom1C = array(    "PRICE"                =>$items["AMOUNT"],
                                                "CURRENCY"            =>$items["CURRENCY"],
                                                "STATUS_ID"            =>self::$statusOrder[$items["STATUS"]],
                                                "ADDITIONAL_INFO"    =>$items["AGREEMENT"]);//добавляем реквизит договора в счет
                    CSaleOrder::Update($orderId, $propsOrderFrom1C);
                }
                $tmpChangeItems = $this->compareItems($orderId, $items);
                $this->makeChange($tmpChangeItems);
            }
        }
        else
        {
            //echo "Модуль sale не найден";
        }
    }
    public function getChangesOrder()//выдаем изменения касающиеся заказа, если таковых нет то false
    {
        $retChanges = array();
        foreach ($this->changeOrder as $change)
        {
            if ($change)
            {
                //echo "getChangesOrder\n";
                //var_dump($this->changeOrder);
                return $this->changeOrder;
            }
        }
        //echo "1 getChangesOrder false\n";
        //var_dump($retChanges);
        return $retChanges;
    }
    public function getInsertPosition()//возвращем вставленные позиции
    {
        //echo "2 getInsertPosition\n";
        //var_dump($this->changeItemsAdd);
        return $this->changeItemsAdd;
    }
    public function getDeletePosition()//возвращаем удаленные позиции
    {
        //echo "3 getDeletePosition\n";
        //var_dump($this->changeItemsDelete);
        return $this->changeItemsDelete;
    }
    public function getStatusQuantityChangedPosition()//возвращаем позиции с измененным статусом
    {
        //echo "4 getStatusChangedPosition\n";
        //var_dump($this->changeItemsStatus);
        return $this->changeItemsStatusQuantity;
    }
    public function getStatus2ChangedPosition()//возвращаем позиции с измененным статусом
    {
        //echo "4 getStatusChangedPosition\n";
        //var_dump($this->changeItemsStatus);
        return $this->changeItemsStatus2;
    }
    public function getStatusChangedPosition()//возвращаем позиции с измененным статусом
    {
        //echo "4 getStatusChangedPosition\n";
        //var_dump($this->changeItemsStatus);
        return $this->changeItemsStatus;
    }
    public function getPriceChangedPosition()//возвращаем позиции с изменененными ценами
    {
        //echo "5 getPriceChangedPosition\n";
        //var_dump($this->changeItemsPrice);
        return $this->changeItemsPrice;
    }
    protected function getItemsFrom1C($items)
    {
        //echo "6 getItemsFrom1C\n";
        //$items = current($items["items"]);
        //var_dump($items["items"]);
        return $items["items"];
    }
    protected function getItemsFromBasket($orderId)
    {
        $dbBasketItems = CSaleBasket::GetList(    array(
                                                        "NAME" => "ASC",
                                                        "ID" => "ASC"
                                                    ),
                                                array(
                                                        "ORDER_ID" => $orderId
                                                    )
                                                );
        $i = 0;
        while ($arItems = $dbBasketItems->Fetch())//запись в массив строк заказа(также массив)
        {
            $arBasketItems[$i] = $arItems;
            $dbPropsItems = CSaleBasket::GetPropsList(
                                                array(
                                                        "SORT" => "ASC",
                                                        "NAME" => "ASC"
                                                    ),
                                                array("BASKET_ID" => $arItems["ID"])
                                            );
            $check=0;
            $checkstatus=0;                                
            while ($propsItem = $dbPropsItems->Fetch())//$propsItem ассоциативный массив с свойствами заказа
            {
                if ($propsItem["CODE"]=="ItemStatusQuantity") //ItemStatusQuantity
                {
                      $check=1;
                }
                if ($propsItem["CODE"]=="ItemStatus2")
                {
                     $checkstatus=1;
                }
                $arBasketItems[$i][$propsItem["CODE"]] = $propsItem["VALUE"];//добавляем свойства товара к общему массиву строк товара
                //if ($propsItem["CODE"] == "RegionCode" && $this->region == -1) $this->region = $propsItem["VALUE"];//добавляем свойство регион к объекту, если данное свойство не заполнено
                if ( $check==0)
                {
                    $arBasketItems[$i]["ItemStatusQuantity"] = " ";
                }
                if ($checkstatus==0)
                {
                        $arBasketItems[$i]["ItemStatus2"] = " ";  
                }
            }
            $i++;
        }
        //echo "7 getItemsFromBasket\n";
        //var_dump($arBasketItems);
        return $arBasketItems;
    }
    protected function compareItems($orderId,$items)
    {
        $itemsFrom1C = $this->getItemsFrom1C($items);
        $itemsFromSite = $this->getItemsFromBasket($orderId);
        $arBrands = GetAllBrandsPropertiesFromFullName();
        $arBrands2 = GetAllBrandsProperties();
        foreach ($itemsFrom1C as $key1C=>$item1C)
        {
            $item1C["BRAND"] = strtoupper(trim($item1C["BRAND"]));
            $brand1C = (strlen($item1C["BRAND"])>2)?$arBrands[$item1C["BRAND"]]:$arBrands2[$item1C["BRAND"]];//берем числовое значение бренда
            $tmpId1C = trim($item1C["ARTICLE"]).trim($brand1C);
            if (!isset($itemsFrom1C[$key1C]["ADDTOBASKET"])) $itemsFrom1C[$key1C]["ADDTOBASKET"] = true;//добавляем свойство если оно отсутствует
            unset($arProps,$arFields);
            $arFields = array();
            $arProps = array();
            foreach ($itemsFromSite as $keySite=>$itemSite)
            {
                #if (!isset($itemsFrom1C[$key1C]["OLDITEMCODE"]))$itemsFrom1C[$key1C]["OLDITEMCODE"]=$itemSite["ItemCode"];
                $tmpIdSite = trim($itemSite["ItemCode"]).$arBrands2[trim($itemSite["Brand"])];
                if (!isset($itemsFromSite[$keySite]["DELETEFROMBASKET"])) $itemsFromSite[$keySite]["DELETEFROMBASKET"] = true;//добавляем свойство если оно отсутствует
                if (!isset($itemsFromSite[$keySite]["UPDATEFROMBASKET"])) $itemsFrom1C[$keySite]["UPDATEFROMBASKET"] = false;//добавляем свойство если оно отсутствует
                $brandToIns = GetCHRBrandCodeByID($brand1C);
                $arTmp = Array();
                $arTmp['BCODE']     = $brandToIns;
                $arTmp['ICODE']     = $item1C["ARTICLE"];
                $arTmp['PRICE']     = $item1C["PRICE"];
                $arTmp['QTY']         = $item1C["QUANTITY"];
                $arTmp['CAPTION']     = $item1C["NAME"];
                $arTmp['STATUS']     = GetItemStatusDescription($item1C["ITEMSTATUS"]);
                $arTmp['STATUSQUANTITY']=$item1C["ITEMSTATUSQUANTITY"];
                $arTmp['STATUSF']     = $item1C["ITEMSTATUS"];
                $arTmp['STATUS2']     = GetItemStatusDescription($item1C["ITEMSTATUS2"]);
                $arTmp['STATUSF2']     = $item1C["ITEMSTATUS2"];
                $arTmp['STATUSQUANTITY2']=$item1C["ITEMSTATUSQUANTITY2"];
                $arTmp['WAREHOUSEDATE']=$item1C["WAREHOUSEDATE"];
                //готовим записи для обновления корзины
                if ($tmpId1C == $tmpIdSite)
                {
                    if (($item1C["WAREHOUSEDATE"] != $itemSite["WAREHOUSEDATE"]) || ($item1C["ITEMSTATUS"] != $itemSite["ItemStatus"]))
                    {
                        $this->changeItemsStatus[] = $arTmp;
                        $itemsFrom1C[$key1C]["UPDATEFROMBASKET"] = true;
                        $itemsFrom1C[$key1C]["UPDATEIDBASKET"] = $itemSite["ID"];
                    }
                     
                    
                    if ($item1C["ITEMSTATUS2"] != $itemSite["ItemStatus2"] )
                    {
                       $this->changeItemsStatus2[] = $arTmp;
                        $itemsFrom1C[$key1C]["UPDATEFROMBASKET"] = true;
                        $itemsFrom1C[$key1C]["UPDATEIDBASKET"] = $itemSite["ID"];// 
                    }
                    if ($item1C["ITEMSTATUSQUANTITY"] != $itemSite["ItemStatusQuantity"])
                    {
                        $this->changeItemsStatusQuantity[] = $arTmp;
                        $itemsFrom1C[$key1C]["UPDATEFROMBASKET"] = true;
                        $itemsFrom1C[$key1C]["UPDATEIDBASKET"] = $itemSite["ID"];//  
                    }
                   /* if ($item1C["ITEMSTATUS"] != $itemSite["ItemStatus"])
                    {
                        $this->changeItemsStatus[] = $arTmp;
                        $itemsFrom1C[$key1C]["UPDATEFROMBASKET"] = true;
                        $itemsFrom1C[$key1C]["UPDATEIDBASKET"] = $itemSite["ID"];//добавляем ID товара если нужно обновлять позицию
                    } */
                    if ($item1C["PRICE"] != $itemSite["PRICE"])
                    {
                        $this->changeItemsPrice[] = $arTmp;
                        $itemsFrom1C[$key1C]["UPDATEFROMBASKET"] = true;
                        $itemsFrom1C[$key1C]["UPDATEIDBASKET"] = $itemSite["ID"];//добавляем ID товара если нужно обновлять позицию
                    }
                    $itemsFrom1C[$key1C]["ADDTOBASKET"] = false;
                    $itemsFromSite[$keySite]["DELETEFROMBASKET"] = false;
                    continue 2;
                }
            }
        }
        //echo "8 itemsFrom1C\n";
        //var_dump($itemsFrom1C);
        //echo "9 itemsFromSite\n";
        //var_dump($itemsFromSite);
        return array($itemsFrom1C,$itemsFromSite);//результат массив содержащий помеченные на добавление и удаление позиции, а также изменения статусов и цен
    }
    protected function makeChange($tmpChangeItems)
    {
        global $DB;
        $itemsFrom1C = $tmpChangeItems[0];
        $itemsFromSite = $tmpChangeItems[1];
        $arBrands = GetAllBrandsPropertiesFromFullName();
        $arBrands2 = GetAllBrandsProperties();
        unset($add);
        $add = array();
        //обновляем или добавляем позиции, обход подготовленного массива с 1С
        foreach ($itemsFrom1C as $key1C=>$item1C)
        {
            //BEGIN значения для вставки в корзину*********************************************************************************
            unset($arProps,$arFields);
            $arFields = array();
            $arProps = array();
            $item1C["BRAND"] = strtoupper(trim($item1C["BRAND"]));
            $brand1C = (strlen($item1C["BRAND"])>2)?$arBrands[$item1C["BRAND"]]:$arBrands2[$item1C["BRAND"]];
            $brandToIns = GetCHRBrandCodeByID($brand1C);
            $productID = $this->getIdItemsWithInsert($brand1C, $item1C["ARTICLE"], $item1C["NAME"]);
            $arFields = array(  "PRODUCT_ID" => $productID,    
                                "PRODUCT_PRICE_ID" => 0,    
                                "PRICE" => $item1C["PRICE"],    
                                "CURRENCY" => $this->currency,   
                                "QUANTITY" => $item1C["QUANTITY"],    
                                "LID" => 's1',    
                                "DELAY" => "N",    
                                "CAN_BUY" => "Y",    
                                "NAME" => $item1C["NAME"],     
                                "MODULE" => "AUTODOC");
            
            $arProps[] = array("NAME"=>"Артикул",                    "CODE"=>"ItemCode",        "VALUE"=>$item1C["ARTICLE"]);
            $arProps[] = array("NAME"=>"Валюта",                    "CODE"=>"Currency",        "VALUE"=>$this->currency);
            $arProps[] = array("NAME"=>"Код бренда",                "CODE"=>"Brand",        "VALUE"=>$brandToIns);
            $arProps[] = array("NAME"=>"Код региона",                "CODE"=>"RegionCode",    "VALUE"=>$this->region);
            $arProps[] = array("NAME"=>"Статус строки заказа",        "CODE"=>"ItemStatus",    "VALUE"=>$item1C["ITEMSTATUS"]);
            $arProps[] = array("NAME"=>"Статус строки заказа Количество",        "CODE"=>"ItemStatusQuantity",    "VALUE"=>$item1C["ITEMSTATUSQUANTITY"]);
            $arProps[] = array("NAME"=>"Статус строки заказа 2",        "CODE"=>"ItemStatus2",    "VALUE"=>$item1C["ITEMSTATUS2"]);
            $arProps[] = array("NAME"=>"Статус строки заказа Количество 2",        "CODE"=>"ItemStatusQuantity2",    "VALUE"=>$item1C["ITEMSTATUSQUANTITY2"]);
            #if ($item1C["WAREHOUSEDATE"]!="")
            #{
            $arProps[] = array("NAME"=>"ДатаОжидания",                    "CODE"=>"WAREHOUSEDATE",        "VALUE"=>$item1C["WAREHOUSEDATE"]);
           # } 
            if ($item1C["ADDTOBASKET"])
            {
                 $arProps[] = array("NAME"=>"АртикулБыло",  "CODE"=>"ItemCodeChange",  "VALUE"=>$item1C["ARTICLEWAS"]);
            } 
           
            $arFields["PROPS"] = $arProps;
            //echo "VALUE TO INSERT arFields\n";
            //var_dump($arFields);
            //END значения для вставки в корзину*********************************************************************************
            if ($item1C["ADDTOBASKET"])
            {

                $add = CSaleBasket::Add($arFields);
                $resAdd[$add] = CSaleBasket::OrderBasket($this->basket, $this->user, 's1');
                //дополнительны запрос т.к. при добавлении выполняется запрос не адекватно
                $DB->Query("UPDATE `b_sale_basket` SET `ORDER_ID`='{$this->basket}' WHERE `ID`={$add}");
                $arTmp = Array();
                $arTmp['BCODE']     = $brandToIns;
                $arTmp['ICODE']     = $item1C["ARTICLE"];
                $arTmp['PRICE']     = $item1C["PRICE"];
                $arTmp['QTY']         = $item1C["QUANTITY"];
                $arTmp['CAPTION']     = $item1C["NAME"];
                $arTmp['STATUS']     = GetItemStatusDescription($item1C["ITEMSTATUS"]);
                $arTmp['STATUSQUANTITY']=$item1C["ITEMSTATUSQUANTITY"];
                $arTmp['STATUSF']     = $item1C["ITEMSTATUS"]; 
                $arTmp['STATUS2']     = GetItemStatusDescription($item1C["ITEMSTATUS2"]);
                $arTmp['STATUSF2']     = $item1C["ITEMSTATUS2"];
                $arTmp['STATUSQUANTITY2']=$item1C["ITEMSTATUSQUANTITY2"];  
                $this->changeItemsAdd[] = $arTmp;
            }
            if ($item1C["UPDATEFROMBASKET"])
            {
                //echo "обновляем корзину, поле -  {$item1C["UPDATEIDBASKET"]} \n";
                //var_dump($arFields);
                CSaleBasket::Update($item1C["UPDATEIDBASKET"],$arFields);
                //дополнительный запрос, так как не обновляются поля которые включены в запрос
                $DB->Query("UPDATE `b_sale_basket` SET `PRICE`='{$item1C['PRICE']}',`CURRENCY`='{$this->currency}',`QUANTITY`='{$item1C['QUANTITY']}' WHERE `ID`={$item1C['UPDATEIDBASKET']}");
                $DB->Query("UPDATE `b_sale_basket_props` SET `VALUE`='{$item1C['ITEMSTATUS']}' WHERE `BASKET_ID`='{$item1C['UPDATEIDBASKET']}' AND `CODE`='ItemStatus'");
                $DB->Query("UPDATE `b_sale_basket_props` SET `VALUE`='{$item1C['ITEMSTATUSQUANTITY']}' WHERE `BASKET_ID`='{$item1C['UPDATEIDBASKET']}' AND `CODE`='ItemStatusQuantity'"); 
                $DB->Query("UPDATE `b_sale_basket_props` SET `VALUE`='{$item1C['ITEMSTATUS2']}' WHERE `BASKET_ID`='{$item1C['UPDATEIDBASKET']}' AND `CODE`='ItemStatus2'"); 
                $DB->Query("UPDATE `b_sale_basket_props` SET `VALUE`='{$item1C['ITEMSTATUSQUANTITY2']}' WHERE `BASKET_ID`='{$item1C['UPDATEIDBASKET']}' AND `CODE`='ItemStatusQuantity2'");
                $DB->Query("UPDATE `b_sale_basket_props` SET `VALUE`='{$item1C['WAREHOUSEDATE']}' WHERE `BASKET_ID`='{$item1C['UPDATEIDBASKET']}' AND `CODE`='WAREHOUSEDATE'");
            }
        }
        if (count($this->changeItemsAdd)>0)
        {
            //echo "добавить в заказ позиции:\n";
            
            //var_dump($resAdd);
            //echo "add\n";
            //var_dump($add);
            //echo "basket, user\n";
            //var_dump($this->basket,$this->user);
            //echo "добавлено!\n";
        }
        foreach ($itemsFromSite as $keySite=>$itemSite)
        {
            if ($itemSite["DELETEFROMBASKET"])
            {
                $idToDel = $itemSite["ID"];
                CSaleBasket::Delete($idToDel);
                
                $arTmp2 = Array();
                $arTmp2['BCODE']     = $itemSite["Brand"];
                $arTmp2['ICODE']     = $itemSite["ItemCode"];
                $arTmp2['PRICE']     = $itemSite["PRICE"];
                $arTmp2['QTY']         = $itemSite["QUANTITY"];
                $arTmp2['CAPTION']     = $itemSite["NAME"];
                $arTmp2['STATUS']     = GetItemStatusDescription($itemSite["ItemStatus"]);
                $arTmp2['STATUSQUANTITY']=$itemSite["ITEMSTATUSQUANTITY"];
                $arTmp2['STATUSF']     = $itemSite["ITEMSTATUS"];  
                $this->changeItemsDelete[] = $arTmp2;
            }
        }
    }
    protected  function getIdItemsWithInsert($brand,$article,$name)
    {
        global $DB;
        $where = " WHERE ItemCode = '{$article}' AND BrandCode = '{$brand}'";
        $sqlExist = "SELECT `id` FROM `b_autodoc_items_m`".$where." UNION SELECT `id` FROM `b_autodoc_items_1c_m`".$where;
        $resExist = $DB->Query($sqlExist,true);
        $resExistRow = intval($resExist->SelectedRowsCount());
        if ($resExistRow == 0)//обавляем товар если он отсутствует в таблицах
        {
            if ($brand > 0 && strlen($article) > 0 && strlen($name) > 0)
            {
                $sqlExistInsert = "INSERT INTO b_autodoc_items_m (
                                                                `id`,
                                                                `BrandCode`, 
                                                                `ItemCode`, 
                                                                `Caption`,
                                                                `1CRef`) 
                                                            VALUES(
                                                                NULL,
                                                                '{$brand}',
                                                                '{$article}',
                                                                '{$name}',
                                                                '1');";
                $DB->Query($sqlExistInsert);
                $product_ID = $DB->LastID();
            }
            else 
            {
                $product_ID = -1;
            }
        }
        else 
        {
            $resTmp = $resExist->Fetch();
            $product_ID = $resTmp['id'];
        }
        //echo "9 {$product_ID}\n";
        return $product_ID;
    }
}
?>