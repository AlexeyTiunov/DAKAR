<?php

class ItemsFromBasket
{
    static public $statusWord = array(    0=>"В работе",
                                    1=>"Выкуплен",
                                    2=>"Отказ",
                                    3=>"Склад",
                                    4=>"Отгружен",
                                    5=>"В пути");
    static public $statusNum = array(    
                                    "В работе"    =>0,
                                    "Выкуплен"    =>1,
                                    "Отказ"        =>2,
                                    "Склад"        =>3,
                                    "Отгружен"    =>4,
                                    "В пути"    =>5);
    private $params = array();
    private $sumItemsOpen = array();
    private $sumItemsAll = array();
    function __construct($params)
    {
        global $DB;
        $this->params = $params;

        foreach (self::$statusWord as $codeStatus=>$valueStatus) 
        {
            $sqlSelectItemsFromBasketOpen = "SELECT SUM(`PRICE`*`QUANTITY`) AS ITEMS_SUM 
                                            FROM `b_sale_basket` 
                                                WHERE     `ID`         IN 
                                                            (SELECT `BASKET_ID`     FROM `b_sale_basket_props` 
                                                                WHERE `CODE`='ItemStatus' AND `VALUE`='{$codeStatus}')
                                                    AND `ORDER_ID`     IN 
                                                            (SELECT `ID`             FROM `b_sale_order` 
                                                                WHERE `STATUS_ID` IN('A') 
                                                                                    AND `ADDITIONAL_INFO`    ='{$this->params['agreement']}' 
                                                                                    AND `USER_ID`            ='{$this->params['user']}')";
            $sqlSelectItemsFromBasketAll = "SELECT SUM(`PRICE`*`QUANTITY`) AS ITEMS_SUM 
                                            FROM `b_sale_basket` 
                                                WHERE     `ID`         IN 
                                                            (SELECT `BASKET_ID`     FROM `b_sale_basket_props` 
                                                                WHERE `CODE`='ItemStatus' AND `VALUE`='{$codeStatus}')
                                                    AND `ORDER_ID`     IN 
                                                            (SELECT `ID`             FROM `b_sale_order` 
                                                                WHERE `STATUS_ID` IN('A','B') 
                                                                                    AND `ADDITIONAL_INFO`    ='{$this->params['agreement']}' 
                                                                                    AND `USER_ID`            ='{$this->params['user']}')";
            //echo "{$sqlSelectItemsFromBasket}<br/>";
            $sqlItemsFromBasketOpen = $DB->Query($sqlSelectItemsFromBasketOpen);
            $resItemsFromBasketOpen = $sqlItemsFromBasketOpen->Fetch();
            $sqlItemsFromBasketAll = $DB->Query($sqlSelectItemsFromBasketAll);
            $resItemsFromBasketAll = $sqlItemsFromBasketAll->Fetch();            
            $this->sumItemsOpen[$valueStatus] = $resItemsFromBasketOpen['ITEMS_SUM'];
            $this->sumItemsAll[$valueStatus] = $resItemsFromBasketAll['ITEMS_SUM'];
        }
    }
    public function getOpenPosition()
    {
        return array('open'=>$this->sumItemsOpen,'all'=>$this->sumItemsAll);
    }
}
?>