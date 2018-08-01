<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG2.php";
abstract class MainImport
{
    protected $domxml;
    protected $xpath;
    protected $queryToControllProcess;
    protected $pathDesired;
    protected $nodesChecked;
    protected $item = array();
    protected $params = array();
    protected $typeItems = array(''=>0,'Масло'=>1,'Радиатор'=>2);
    protected $timeInterval = array();
    /*
     * $params['node'] = 'Товар';
     * $params['item'] = array('Масло','Радиаторы');
     * $params['allUpdate'] = false;
     * $params['file'] = import.xml;
    */
    function __construct($params)
    {
        global $DB;
        //$this->queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                                                //NULL, 
                                                                //NOW(),'условие1')";
        
        $this->params = $params;
        if (!self::getProcessList())
        {
            $this->domxml = new DOMDocument;
            $this->domxml->substituteEntities = true;
            $this->domxml->preserveWhiteSpace = false;
            if (isset($this->params['path']))
            {
                $this->domxml->load($_SERVER["DOCUMENT_ROOT"]."/upload/".$this->params['path']."/{$this->params['file']}");
            }
            else 
            {
                $this->domxml->load($_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/{$this->params['file']}");
            }
            $this->xpath = new DOMXPath($this->domxml);
            
            #echo "progress\n XML Document is load\n";
            
            $this->getDesiredNodes();
            $this->updateMain();
        }
        //$this->dumpProcess();

    }
    function __destruct()
    {
        global $DB;
        if ($this->params['UpdateConfirms'] && $this->params['numberSend'])
        {
            $sql = "UPDATE `ITG_exchange_1c` SET `process`='success' WHERE `nameFile`='{$this->params['file']}'";
            $DB->Query($sql,true);
            UpdateConfirms($this->params['UpdateConfirms'], $this->params['numberSend']);
        }
    }
    abstract protected function getDesiredNodes();
    protected  function updateMain()
    {
        foreach ($this->nodesChecked as $checked)
        {
            $nodes = $checked->childNodes;

            $this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($nodes)."')";
            //$DB->Query($queryToControllProcess);
            foreach ($nodes as $node)
            {
                $nodeValue = $node->nodeValue;
                $nodeName = $node->nodeName;

                $this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
                $this->changeSameItemForImport($nodeName,$nodeValue,$node);
            }
            $this->updateForImport();
            unset($this->item);
        }
    }

    abstract protected function changeSameItemForImport($nodeName,$nodeValue,$node = null);
    abstract protected function updateForImport();
    
    protected function getExistId($dataBase,$where,$id='id')
    {
        global $DB;
        $sqlTmp = "SELECT `{$id}` FROM `{$dataBase}` ".$where;
        //echo $sqlTmp."\n";
        $resTmp = $DB->Query($sqlTmp,true);
        $countReturnedRow = intval($resTmp->SelectedRowsCount());
        $arTmp[$id] = -1;
        $reterned = $countReturnedRow == 0 ? $arTmp[$id] : $arTmp = $resTmp->Fetch();
        return $arTmp[$id];
    }
    protected function idFromMainIB($query)
    {
        global $DB;
        $sqlInsert = "INSERT INTO `b_iblock_element` SET ";
        $numProps = count($query);
        $i = 0;
        //$ignoreFieldsForeSelect = array('TIMESTAMP_X','DATE_CREATE','MODIFIED_BY','CREATED_BY','ACTIVE','SORT','PREVIEW_TEXT_TYPE','WF_STATUS_ID','IN_SECTIONS');
        foreach ($query as $name=>$value)
        {
            $subquery = "`{$name}`={$value}";
            $sqlInsert .= $subquery;
            if (++$i < $numProps)
            {
                $sqlInsert .= ",";
            }
        }
        //echo "запрос - {$sqlInsert}<br/>";
        $DB->Query($sqlInsert);
        $retID = $DB->LastID();
        $DB->Query("UPDATE `b_iblock_element` SET `XML_ID`='{$retID}' WHERE `ID`='{$retID}'");

        return $retID;
    }
    protected function deleteFromMainIB($selectToDelete)
    {
        global $DB;
        $sqlDelete = "DELETE FROM `b_iblock_element` WHERE `ID` IN(".$selectToDelete.")";
        $DB->Query($sqlDelete);
    }
    protected function saveImage($namePicture)
    {
        $from = imageCreateFromJPEG($this->item['pathImage']);
        $info = @getimagesize($this->item['pathImage']);
        $x = $info[0];
        $y = $info[1];
                
        $to = imageCreateTrueColor($x, $y);
        imageCopy($to,$from,0,0,0,0,$x,$y);
        imageJPEG($to,$_SERVER["DOCUMENT_ROOT"]."/autodoc/imagesItems/{$namePicture}.jpg");
        imageDestroy($to);
        imageDestroy($from);
    }
    protected function dumpProcess()//записываем в спец БД результаты работы скрипта
    {
        global $DB;
        //echo $this->queryToControllProcess;
        $DB->Query($this->queryToControllProcess);
    }
    /*protected function sendProcessInterval()
    {
        if (!isset($this->timeInterval['lastTime']))
        {
            $this->timeInterval['lastTime'] = $this->timeInterval['startPoint'];
            echo "progress\n XML Document have been load\n";
        }
        $this->timeInterval['currentTime'] = new DateTime('now');
        $interval = $this->timeInterval['startPoint']->diff($this->timeInterval['currentTime']);
        if (($interval->format('%m') % 5) == 0 && $this->timeInterval['lastTime']->format('%m') != $this->timeInterval['currentTime']->format('%m'))
        {
            $this->timeInterval['lastTime'] = $this->timeInterval['currentTime'];
            $this->timeInterval['loadTime'] = $interval->format('%H hour %m minutes %s seconds');
            echo "progress\n Data loading: {$this->timeInterval['loadTime']}\n";
        }
    }*/
    public static function getProcessList()
    {
        $DB = Search_ITG::manualConnect();
        $sql = "SELECT `process`,`extMessage`,`lastTime`,`startTime` FROM `ITG_exchange_1c` WHERE `nameFile`='{$_GET['filename']}' ORDER BY `id` DESC LIMIT 1";
        $res = $DB->query($sql);
        $process = $res->fetch_assoc();
        $timeUpdate = new DateTime($process['lastTime']);
        $timeStart = new DateTime($process['startTime']);
        $timeNow = new DateTime('now');
        $timeNow->sub(new DateInterval('PT30M'));
        if ($process['process'] == 'progress' && $timeNow < $timeStart)
        {
            #$timeInterval['lastTime'] = new DateTime($process['lastTime']);
            $timeInterval['startPoint'] = new DateTime($process['startTime']);
            $timeInterval['currentTime'] = new DateTime('now');
            $interval = $timeInterval['startPoint']->diff($timeInterval['currentTime']);
            $timeInterval['loadTime'] = $interval->format('%H hour %i minutes %s seconds');                
            
            $sql = "UPDATE `ITG_exchange_1c` SET `extMessage`='Data loading: {$timeInterval['loadTime']}',`lastTime`=NOW() WHERE `nameFile`='{$_GET['filename']}' AND `startTime`='{$process['startTime']}'";
            $DB->query($sql);
            //echo "process\n {$process['extMessage']}\n";
            return true;
        }
        else
        {

            $timeNow = new DateTime('now');
            $timeNow->sub(new DateInterval('PT10M'));//отнимаем 10 минут
            if ($timeNow < $timeUpdate && $res->num_rows > 0)
            {
                echo "success\n last exchange was less than 10 minutes ago... try later...it's fucking 1C programmer!!!";
                return true;
            }
            //echo "process\n XML Document has been loaded, begin to parse data \n";
            $sql = "INSERT INTO `ITG_exchange_1c` (`process`,`extMessage`,`lastTime`,`startTime`,`nameFile`) VALUES('progress','Data loading...',NOW(),NOW(),'{$_GET['filename']}')";
            $DB->query($sql);
            return false;
        }
    }
}
?>