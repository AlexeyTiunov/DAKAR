<?php

class ProductProps
{
    public static $filtrOfproduct = array('VendorCode','ModelRange','DateBegin');
    protected $table;
    protected $mysqli;
    protected $props = array();
    protected $dependent = array();
    protected $notExist = array();
    
    function __construct($table,$table2,$extProp)
    {
        $this->mysqli = new mysqli("localhost","bitrix","a251d851","bitrix","31006");
        $this->mysqli->set_charset("utf8");
        $this->notExist = $extProp;
        $sqlAll = "SELECT * FROM `{$table2}`,`{$table}` WHERE {$table}.autodocId={$table2}.id ORDER BY {$table}.ModelRange";
        //echo $sqlAll."<br>";
        $propsKey = array();
        $propsVal = array();
        if ($resAll = $this->mysqli->query($sqlAll))
        {
            //$i = 0;
            $j = 0;
            while ($rowAll = $resAll->fetch_assoc())
            {
                //echo var_dump($rowAll)."<br>";
                foreach ($rowAll as $key=>$val)
                {
                    $tableCashe[$j][$key] = $val;
                }
                $j++;
                
            }
            $this->props = array_unique($propsVal);//записываем уникальные значения полей
            //echo "<br><pre>*************1:";
            //echo var_dump($this->props)."</pre>"."<br>";
            //echo "<br><pre>*************1:";
            //echo var_dump($tableCashe)."</pre>"."<br>";
            $this->dependent = $tableCashe;
        }
    }
    public function getDependents()
    {
        return $this->dependent;
    }
    static public function getProperty($arSess,$namePropExt)
    {
        foreach ($arSess as $numProduct=>$propuctProp)//$val - строка позиции, $key - номер строки
        {
            foreach ($propuctProp as $nameProp=>$valueProp)
            {
                if ($nameProp == $namePropExt) $retProp[] = $valueProp;
            }
        }
        $retProp = array_unique($retProp);
        return $retProp;
    }
    static public function getPropertyWithCondition($arSess,$props)
    {
        //echo "<pre>";
        //echo var_dump($arSess)."</pre>";
        //echo "<pre>";
        //echo var_dump($props)."</pre>";
        foreach ($props as $nameExtProp=>$valueExtProp)
        {
            if ($valueExtProp == '') unset($props[$nameExtProp]);//исключаем пустые фильтры
        }
        $numExtProps = count($props);
        foreach ($arSess as $numProduct=>$propuctProp)
        {
            $j = 0;//счетчик совпавших свойств
            foreach ($props as $nameExtProp=>$valueExtProp)
            {    
                foreach ($propuctProp as $nameProp=>$valueProp)
                {
                    if ($nameExtProp == $nameProp && $valueExtProp == $valueProp && $nameExtProp != 'DateBegin')
                    {
                        //echo "{$props[$nameProp]}:{$valueProp}:{$nameProp}:". $propsFlip[$nameProp]."<br/>";
                        $j++;
                    }
                    elseif ($nameExtProp == $nameProp && $nameExtProp == 'DateBegin')
                    {
                        $extDate = intval($valueExtProp);
                        //echo "data ext ".$extDate."<br />";
                        //echo "data min ".intval($valueProp)."<br />";
                        //echo "data max ".intval($propuctProp['DateEnd'])."<br />";
                        if (intval($valueProp) <= $extDate && $extDate <= intval($propuctProp['DateEnd']))
                        {
                            $j++;
                            echo "is date <br />";
                        }
                        elseif (intval($valueProp) <= $extDate && $propuctProp['DateEnd'] == '')
                        {
                            $j++;
                            echo "is date <br />";
                        }
                    }
                }
            }
            //echo $j."count iteration \n";
            if ($j == $numExtProps)
            {
                $retTmpPropList[] = $propuctProp;
                $retTmpProp[self::$filtrOfproduct[0]][] = $propuctProp[self::$filtrOfproduct[0]];
                $retTmpProp[self::$filtrOfproduct[1]][] = $propuctProp[self::$filtrOfproduct[1]];
                $retTmpProp[self::$filtrOfproduct[2]][] = $propuctProp[self::$filtrOfproduct[2]];
            }
        }
        //echo "<pre>";
        //echo var_dump($retTmpPropList)."</pre>";        
        $retTmpProp[self::$filtrOfproduct[0]] = array_unique($retTmpProp[self::$filtrOfproduct[0]]);
        $retTmpProp[self::$filtrOfproduct[1]] = array_unique($retTmpProp[self::$filtrOfproduct[1]]);
        $retTmpProp[self::$filtrOfproduct[2]] = array_unique($retTmpProp[self::$filtrOfproduct[2]]);
        //echo "<pre>";
        //echo var_dump($retTmpProp)."</pre>";    
        return array($retTmpProp,$retTmpPropList);
    }
    static public function getItemFromId($arSess,$id)
    {
        foreach ($arSess as $numProduct=>$propuctProp)
        {
            if ($propuctProp['idRad'] == $id)
            {    # echo $id;
               $sqlpic="SELECT Base64 FROM b_autodoc_items_m WHERE id=".$propuctProp['id']." LIMIT 1";
                 #$sqlpic="SELECT Base64 FROM b_autodoc_items_m WHERE id=".$id." LIMIT 1";
                 
                $mysql=new mysqli("localhost","bitrix","a251d851","bitrix","31006");
                 $resultpic=$mysql->query($sqlpic); 
                while ($pictext=$resultpic->fetch_assoc())
                {
                    $base64=$pictext['Base64'];
                }
                 if (  $base64!="")
                 {
                   $data = base64_decode($base64);

                   $im = imagecreatefromstring($data);
                   if ($im != false) {
                       $handle = fopen("/media/Vol/www/priceld/".$propuctProp['idRad'].".png", "w+");
                       Fclose($handle);
                       imagepng($im,'/media/Vol/www/priceld/'.$propuctProp['idRad'].'.png') ;
                         
                          $propuctProp['image']="http://".$_SERVER["SERVER_NAME"]."/priceld/".$propuctProp['idRad'].".png" ;
                          
                       return $propuctProp  ;
                    } else
                    {
                     $localPath = "/autodoc/imagesItems/{$propuctProp['id']}.jpg";
                      $imageRealPath = "http://".$_SERVER["SERVER_NAME"].$localPath;
                      $imageNonePath = "http://".$_SERVER["SERVER_NAME"]."/autodoc/imagesItems/expected.png";
                      $propuctProp['image'] = is_file($_SERVER["DOCUMENT_ROOT"].$localPath)?$imageRealPath:$imageNonePath;
                       return $propuctProp;   
                    } 
                 
                 } else{
                 
                $localPath = "/autodoc/imagesItems/{$propuctProp['id']}.jpg";
                $imageRealPath = "http://".$_SERVER["SERVER_NAME"].$localPath;
                $imageNonePath = "http://".$_SERVER["SERVER_NAME"]."/autodoc/imagesItems/expected.png";
                $propuctProp['image'] = is_file($_SERVER["DOCUMENT_ROOT"].$localPath)?$imageRealPath:$imageNonePath;
                return $propuctProp;
                 }
            }
        }
    }
    static public function getIdRadFromItem($arSess,$ItemCode)
    {
        foreach ($arSess as $numProduct=>$propuctProp)
        {
            if ($propuctProp['ItemCode'] == $ItemCode)
            { 
                   return $propuctProp['idRad'];
                   break;
            } 
              
         }
         return false;
    }
}
?>