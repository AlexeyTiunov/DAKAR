<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/modules/main/classes/mysql/database.php";
class CitgTempTable extends CDatabase
{
    //global $DB;
    private $tmpNameTable;
    private $selectAll;
    //private $mysqli;
    private $host = "localhost:31006";
    private $db = "bitrix";
    private $login = "bitrix"; 
    private $password = "a251d851";
    //private static $mysqli = new mysqli(sqli(self::$host, self::db, self::login, self::password);
    function __construct($arHeaderFields, $drop = false)
    {
        $this->tmpNameTable = 'itgTempTable'.session_id();
        $this->selectAll = "SELECT * FROM {$this->tmpNameTable}";
        //$this->mysqli = new mysqli($this->host, $this->login, $this->password, $this->db);
        CDatabase::Connect($this->host, $this->db, $this->login, $this->password);
        if($drop)
        {
            $query = "DROP TABLE IF EXISTS {$this->tmpNameTable}";
            //$this->mysqli->query($query);
        }
        $query = "CREATE TEMPORARY TABLE IF NOT EXISTS {$this->tmpNameTable} (
                    `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,";
                    foreach ($arHeaderFields as $name=>$type)
                    {
                        $query .= "`{$name}` {$type},";
                    }
                    $query .= "PRIMARY KEY (`ID`));";
        self::TimeCallInsert();
        return CDatabase::Query($query);//$this->mysqli->query($query);
    }
    public function Insert($arInsertedValue)
    {
        $query = "INSERT INTO {$this->tmpNameTable} VALUES";
                    $countFields = count($arInsertedValue[0]);
                    $countRow = count($arInsertedValue);
                    $i1 = 0;
                    foreach ($arInsertedValue as $row)
                    {
                        $i1++;
                        $query .= "(NULL,";
                        $i2 = 0;
                        foreach ($row as $val)
                        {
                            $i2++;
                            $query .= "'".CDatabase::ForSql($val)."'";
                            if($i2 < $countFields) $query .= ",";
                        }
                        $query .= ")";
                        if($i1 < $countRow) $query .= ",";
                    }
        if(self::Show("itgTempTable".session_id())) return true;
        return CDatabase::Query($query);//$this->mysqli->query($query);
    }
    public function GetList($orderBy = array("ORDER_ID","DESC"))
    {
        $query = $this->selectAll." ORDER BY {$orderBy[0]} {$orderBy[1]}";
        return CDatabase::Query($query);
    }
    public function Filtr($arFiltr,$orderBy = array("DESC","ORDER_ID"))
    {
        $query = "{$this->selectAll} WHERE";
        $countFiltr = count($arFiltr);
        $i = 0;
        foreach ($arFiltr as $name=>$search)
        {
            $i++;
            $query .= " {$name} LIKE '{$search}'";
            if($countFiltr > $i) $query .= " AND ";
        }
        $query .= " ORDER BY {$orderBy[0]} {$orderBy[1]}";
        return CDatabase::Query($query);//$this->mysqli->query($query);
    }
    public function FiltrExclusive($arFiltr,$items)
    {
        $query = "{$this->selectAll} WHERE";
        $countFiltr = count($arFiltr);
        $i = 0;
        foreach ($arFiltr as $name=>$search)
        {
            $i++;
            $query .= " {$name} LIKE '{$search}'";
            if($countFiltr > $i) $query .= " AND ";
        }
        $query .= "GROUP BY {$items}";
        return mysql_query($query);
    }
    public function GetSessionTable()
    {
        return $this->tmpNameTable;
    }
	public function DropOldTable()
    {
    	$query = "SELECT * FROM session_control_table";
    	$queryToDrop = "DROP TABLE IF EXISTS ";
    	$queryToDelete = "DELETE FROM session_control_table WHERE `table`=";
    	$link = self::ConnectInternal();
    	$result = mysql_query($query,$link)or die(mysql_error());
    	while ($row = mysql_fetch_assoc($result))
    	{
    		$now = new DateTime("now");
    		$timeDropTable = new DateTime("{$row['drop_time']}");
    		if ($now > $timeDropTable && $row['table'] != $this->tmpNameTable)
    		{
    			@mysql_query($queryToDrop.$row['table'],$link);
    			@mysql_query($queryToDelete."'".$row['table']."'",$link);
    		}
    	}
    }
    static public function FiltrToAjax($search,$field,$table)
    {
    	$searchToSQL = str_replace(array("-",".",",",":","'","\""," "),"",$search);
        $query = "SELECT {$field} FROM {$table} WHERE {$field} LIKE '%{$searchToSQL}%' GROUP BY {$field}";
        $link = self::ConnectInternal();
        $result = mysql_query($query,$link) or die(mysql_error());
        $num_row = mysql_affected_rows();
        $html = '';
        $i = 0;
        while($row = mysql_fetch_row($result))
        {
        	$i++;
            $html .= "<a class='reultAjax' href='http://parts.avtodok.com.ua/personal/order/history_positions/index.php?itg_code=";
            foreach ($row as $val) $html .= $val."&nameTmpTable={$table}&filter=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA'><b>".str_ireplace($searchToSQL,"</b>".$search."<b>",$val)."</b></a><br>";
            if ($i > 19) break;
        }
        return ($i != $num_row) ? $html."<font style='color:red; font-size:12px;'>Показано $i совпадений из $num_row</font>" : $html;
    }
    static public function Show($nameTable)
    {
        $link = self::ConnectInternal();
        $result = mysql_query("SELECT * FROM {$nameTable}",$link);
        return mysql_num_rows($result);
    }
    static public function Drop($nameTable)
    {
        $query = "DROP TABLE IF EXISTS ".$nameTable;
        $link = self::ConnectInternal();
        return mysql_query($query,$link)or die(mysql_error());
    }
    private function ConnectInternal()
    {
        $link = mysql_connect('localhost:31006', 'bitrix', 'a251d851') or die(mysql_error());//self::$host, self::db, self::login, self::password
        $db_selected = mysql_select_db('bitrix', $link) or die(mysql_error());
        return $link;
    }
    private  function TimeCallInsert()
    {
    	$dropTime = new DateTime("tomorrow");
    	$queryUpdate = "SELECT * FROM session_control_table WHERE `table`='".$this->tmpNameTable."'";
    	$queryInsert = "INSERT INTO session_control_table VALUES (NULL, '{$dropTime->format('Y-m-d H:i:s')}','{$this->tmpNameTable}')";
    	//echo $queryUpdate.$queryInsert;
    	$link = self::ConnectInternal();
    	mysql_query($queryUpdate,$link);
    	if (mysql_affected_rows() == 0) mysql_query($queryInsert,$link);
    }
}
/*********************************************************************************************/
if($_GET['droped'])
{
    CitgTempTable::Drop($_GET['droped']);
}
if($_GET['search'] && $_GET['field'] && $_GET['table'])
{
    echo CitgTempTable::FiltrToAjax($_GET['search'],$_GET['field'],$_GET['table']);
}
