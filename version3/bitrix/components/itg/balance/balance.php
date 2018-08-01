<?php

class Balance
{
        
    const     NAME_TABLE_PAY         = "b_iblock_element_prop_s24", 
            TYPE_DOCUMENT_PAY    = "PROPERTY_190",
            NUM_DOCUMENT_PAY     = "PROPERTY_192", 
            DATE_DOCUMENT_PAY     = "PROPERTY_191",
            CLIENT_CODE_PAY     = "PROPERTY_193",
            AGREEMENT_CODE_PAY    = "PROPERTY_194",
            CURRENCY_CODE_PAY    = "PROPERTY_195",
            SUMM_PAY            = "PROPERTY_196";
    
    const     NAME_TABLE_SHIPMENT        = "b_iblock_element_prop_s25",
            NUM_DOCUMENT_SHIPMENT    = "PROPERTY_197",
            CLIENT_CODE_SHIPMENT    = "PROPERTY_199",
            AGREEMENT_CODE_SHIPMENT    = "PROPERTY_200",
            DATE_DOCUMENT_SHIPMENT    = "PROPERTY_201",
            CURRENCY_CODE_SHIPMENT    = "PROPERTY_202",
            SUMM_SHIPMENT            = "PROPERTY_203",
            TYPE_DOCUMENT_SHIPMENT    = "PROPERTY_205";
    
    const     NAME_TABLE_ITEM     = "b_iblock_element_prop_s26",
            ID_SHIPMENT_ITEM    = "PROPERTY_206",
            BRAND_ITEM            = "PROPERTY_207",
            ARTICLE_ITEM        = "PROPERTY_208",
            QUANTITY_ITEM        = "PROPERTY_210",
            SUM_ITEM            = "PROPERTY_211",
            PRICE_ITEM            = "PROPERTY_212",
            NAME_ITEM            = "PROPERTY_213";
    private $params = array();
    private $balance = array();
    private $pointOfReference = array();

    function __construct($params)
    {
        global $DB;
        $this->params = $params;
        $this->params['dateBegin'] = isset($params['dateBegin']) ? intval($params['dateBegin']): false;
        $this->params['dateEnd'] = intval($this->params['dateEnd']);

        $sqlSelectPointOfReference = "SELECT `BalanceOnDate`, CAST(`DateBeginningReference` as SIGNED) as Begin FROM `b_autodoc_agreements`
                                            WHERE
                                                    `Code`='".$this->params['agreement']."' 
                                                AND `ClientCode`='".$this->params['user']."'";

        $resSelectPointOfReference = $DB->Query($sqlSelectPointOfReference);
        
        $selectPointOfReference = $resSelectPointOfReference->Fetch();
        $this->pointOfReference = $selectPointOfReference;
        //----------------------------------------------------------------------------------------------------------------------------------------
        if ($this->params['dateBegin'])
        {
            $sqlSelectExpencesBegin = "SELECT IF('{$selectPointOfReference['Begin']}' < '{$this->params['dateBegin']}',
                                            (SELECT 0-SUM(`".self::SUMM_SHIPMENT."`)  
                                            FROM `".self::NAME_TABLE_SHIPMENT."` 
                                                    WHERE  
                                                            `".self::AGREEMENT_CODE_SHIPMENT."`='".$this->params['agreement']."' 
                                                        AND `".self::CLIENT_CODE_SHIPMENT."`='".$this->params['user']."' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)>'{$selectPointOfReference['Begin']}' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)<'{$this->params['dateBegin']}'),
                                            (SELECT SUM(`".self::SUMM_SHIPMENT."`) 
                                            FROM `".self::NAME_TABLE_SHIPMENT."` 
                                                    WHERE
                                                            `".self::AGREEMENT_CODE_SHIPMENT."`='".$this->params['agreement']."' 
                                                        AND `".self::CLIENT_CODE_SHIPMENT."`='".$this->params['user']."' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)<='{$selectPointOfReference['Begin']}' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)>'{$this->params['dateBegin']}')
                                            )";
            $resExpencesBegin = $DB->Query($sqlSelectExpencesBegin);
            $expencesBegin = $resExpencesBegin->Fetch();
        }
        $sqlSelectExpencesEnd = "SELECT IF('{$selectPointOfReference['Begin']}' < '{$this->params['dateEnd']}',
                                        (SELECT 0-SUM(`".self::SUMM_SHIPMENT."`)  
                                        FROM `".self::NAME_TABLE_SHIPMENT."` 
                                                WHERE  
                                                        `".self::AGREEMENT_CODE_SHIPMENT."`='".$this->params['agreement']."' 
                                                    AND `".self::CLIENT_CODE_SHIPMENT."`='".$this->params['user']."' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)>'{$selectPointOfReference['Begin']}' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)<='{$this->params['dateEnd']}'),
                                        (SELECT SUM(`".self::SUMM_SHIPMENT."`) 
                                        FROM `".self::NAME_TABLE_SHIPMENT."` 
                                                WHERE
                                                        `".self::AGREEMENT_CODE_SHIPMENT."`='".$this->params['agreement']."' 
                                                    AND `".self::CLIENT_CODE_SHIPMENT."`='".$this->params['user']."' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)<='{$selectPointOfReference['Begin']}' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_SHIPMENT."` as DATE) as SIGNED)>='{$this->params['dateEnd']}')
                                        )";
        $resExpencesEnd = $DB->Query($sqlSelectExpencesEnd);
        $expencesEnd = $resExpencesEnd->Fetch();

        //------------------------------------------------------------------------------------------------------------------------------------------
        if ($this->params['dateBegin'])
        {
            $sqlSelectParishesBegin = "SELECT IF('{$selectPointOfReference['Begin']}' < '{$this->params['dateBegin']}',
                                            (SELECT '{$selectPointOfReference['BalanceOnDate']}'+SUM(`".self::SUMM_PAY."`) 
                                            FROM `".self::NAME_TABLE_PAY."` 
                                                    WHERE  
                                                            `".self::AGREEMENT_CODE_PAY."`='".$this->params['agreement']."' 
                                                        AND `".self::CLIENT_CODE_PAY."`='".$this->params['user']."' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)>'{$selectPointOfReference['Begin']}' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)<'{$this->params['dateBegin']}'),
                                            (SELECT '{$selectPointOfReference['BalanceOnDate']}'-SUM(`".self::SUMM_PAY."`) 
                                            FROM `".self::NAME_TABLE_PAY."` 
                                                    WHERE
                                                            `".self::AGREEMENT_CODE_PAY."`='".$this->params['agreement']."' 
                                                        AND `".self::CLIENT_CODE_PAY."`='".$this->params['user']."' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)<='{$selectPointOfReference['Begin']}' 
                                                        AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)>'{$this->params['dateBegin']}')
                                            )";
            $resParishesBegin = $DB->Query($sqlSelectParishesBegin);
            $parishesBegin = $resParishesBegin->Fetch();
        }
        $sqlSelectParishesEnd = "SELECT IF('{$selectPointOfReference['Begin']}' < '{$this->params['dateEnd']}',
                                        (SELECT '{$selectPointOfReference['BalanceOnDate']}'+SUM(`".self::SUMM_PAY."`) 
                                        FROM `".self::NAME_TABLE_PAY."` 
                                                WHERE  
                                                        `".self::AGREEMENT_CODE_PAY."`='".$this->params['agreement']."' 
                                                    AND `".self::CLIENT_CODE_PAY."`='".$this->params['user']."' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)>'{$selectPointOfReference['Begin']}' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)<='{$this->params['dateEnd']}'),
                                        (SELECT '{$selectPointOfReference['BalanceOnDate']}'-SUM(`".self::SUMM_PAY."`) 
                                        FROM `".self::NAME_TABLE_PAY."` 
                                                WHERE
                                                        `".self::AGREEMENT_CODE_PAY."`='".$this->params['agreement']."' 
                                                    AND `".self::CLIENT_CODE_PAY."`='".$this->params['user']."' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)<='{$selectPointOfReference['Begin']}' 
                                                    AND CAST(CAST(`".self::DATE_DOCUMENT_PAY."` as DATE) as SIGNED)>='{$this->params['dateEnd']}')
                                        )";
        $resParishesEnd = $DB->Query($sqlSelectParishesEnd);
        $parishesEnd = $resParishesEnd->Fetch();

        sort($expencesBegin);
        sort($parishesBegin);
        sort($expencesEnd);
        sort($parishesEnd);
        //isset($params['dateBegin']) ? (list($expBegin) = $expencesBegin) : false;
        //isset($params['dateBegin']) ? (list($parBegin) = $parishesBegin) : false;
        @list($expBegin) = $expencesBegin;
        @list($parBegin) = $parishesBegin;
        list($expEnd) = $expencesEnd;
        list($parEnd) = $parishesEnd;
        //echo "ptp:{$selectPointOfReference['BalanceOnDate']}<br/>";
        //echo $sqlSelectExpencesBegin."1<br/>";
        //echo $sqlSelectExpencesEnd."2<br/>";
        //echo $sqlSelectParishesBegin."3<br/>";
        //echo $sqlSelectParishesEnd."4<br/>";
        if ($selectPointOfReference['Begin'] == $this->params['dateBegin'])
        {
            if (!$parBegin && !$expBegin)
            {
                $this->balance['begin'] = $selectPointOfReference['BalanceOnDate'];
                $this->balance['end'] = $parEnd + $expEnd;
            }
        }
        elseif ($selectPointOfReference['Begin'] == $this->params['dateEnd'])
        {
            if (!$parEnd && !$expEnd) 
            {
                $this->balance['end'] = $selectPointOfReference['BalanceOnDate'];
                $this->balance['begin'] = $parBegin + $expBegin;
            }
        }
        else 
        {
            $this->balance['begin'] = $parBegin + $expBegin;
            $this->balance['end'] = $parEnd + $expEnd;
        }

    }
    public function getBalanceBegin()
    {
        return @$this->balance['begin'];
    }
    public function getBalanceEnd()
    {
        return $this->balance['end'];
    }
    public function getPointOfReference()
    {
        return $this->pointOfReference;
    }
    public function getDebt()
    {
        $this->calculateDebt();
        return $this->balance['debt'];
    }
    public function getUpdateDebt()
    {
        $this->dateUpdateDebt();
        return $this->balance['debtDate'];
    }
    private function calculateDebt()
    {
        global $DB;
        $sql = "SELECT `CurrentDebt`,`CurrencyCode` FROM `b_autodoc_agreements` WHERE `ClientCode`='{$this->params['user']}' AND `Code`='{$this->params['agreement']}'";
        $resSql = $DB->Query($sql);
        $debtUSD = $resSql->Fetch();
        if ($debtUSD['CurrencyCode'] == 'USD')
        {
            $this->balance['debt'] = $debtUSD['CurrentDebt'];
        }
        else 
        {
            $sqlRate = ($debtUSD['CurrencyCode'] == 'UAH')?
                                                "SELECT `RATE` AS KOEF FROM `b_catalog_currency_rate` WHERE `CURRENCY`='USD' HAVING MAX(DATE_RATE) LIMIT 1"
                                                :
                                                "SELECT 1*((SELECT `RATE` FROM `b_catalog_currency_rate` WHERE `CURRENCY`='EUR' HAVING MAX(DATE_RATE) LIMIT 1)/
                                                (SELECT `RATE` FROM `b_catalog_currency_rate` WHERE `CURRENCY`='USD' HAVING MAX(DATE_RATE) LIMIT 1)) AS KOEF";
            $resSqlRate = $DB->Query($sqlRate);
            $rate = $resSqlRate->Fetch();
            $this->balance['debt'] = $debtUSD['CurrentDebt']*$rate['KOEF'];
        }
    }
    private function dateUpdateDebt()//
    {
        global $DB;
        $sql = "(SELECT `PROPERTY_191` AS DATE_UPDATE FROM `b_iblock_element_prop_s24` 
                                                WHERE `PROPERTY_193`='{$this->params['user']}' 
                                                    AND `PROPERTY_194`='{$this->params['agreement']}'
                                                ORDER BY `IBLOCK_ELEMENT_ID` DESC LIMIT 1)
                UNION
                (SELECT `PROPERTY_201` AS DATE_UPDATE FROM `b_iblock_element_prop_s25` 
                                                WHERE `PROPERTY_199`='{$this->params['user']}' 
                                                    AND `PROPERTY_200`='{$this->params['agreement']}'
                                                ORDER BY `IBLOCK_ELEMENT_ID` DESC LIMIT 1) ORDER BY DATE_UPDATE DESC LIMIT 1";
        $resSql = $DB->Query($sql);
        $dateSql = $resSql->Fetch();
        $this->balance['debtDate'] = $dateSql['DATE_UPDATE'];
    }
}
?>