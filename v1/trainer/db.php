<?php
    class DB{
        private static $writeDb;
        private static $readDb;
       
    public static function connectWriteDb(){
        if(self::$writeDb === null){
            self::$writeDb = new PDO('mysql:host=localhost;dbname=myfoodee;charset=utf8','root','');
            self::$writeDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$writeDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        return self::$writeDb;
    }

    public static function connectReadDb(){
        if(self::$readDb === null){
            self::$readDb = new PDO('mysql:host=localhost;dbname=myfoodee;charset=utf8','root','');
            self::$readDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$readDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        return self::$readDb;
    }
}
