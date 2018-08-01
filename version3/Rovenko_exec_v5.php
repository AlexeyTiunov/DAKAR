<?php  
      exec("rm /var/DAKAR/priceld/mypipe5");
    exec("mkfifo /var/DAKAR/priceld/mypipe5");   
    exec("php -f /var/DAKAR/Rovenko2DB_V5.php >/var/DAKAR/priceld/mypipe5 &");
    //exec("rm /var/www/www/priceld/mypipe2");  
?>