<?php
/**
* new database oop style
*/

require_once(__DIR__."/../../..".'/wp-config.php');

class misysnews_dbconnection{
  private $connection;
  private $tableName= ["misys_device","misysnews_access"];

  public function __construct(){
    $conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    if (mysqli_connect_errno() ) {
      $this->connection = null;
    }else {
      $this->connection = $conn;
    }
  }
  public function getTableName(){
    return [
      "deviceTable"=>"misys_device",
      "accessTable"=>"misysnews_access"
    ];
  }

  public function getConnection(){
    if(!is_null($this->connection)){
      return $this->connection;
    }else {
      return null;
    }
  }
  public function initTables(){
    $this->createTables();
  }
  public function createTables(){
    $query ;
    for ($i=0; $i < count($this->tableName) ; $i++) {
      if(!$this->isTableExist($this->tableName[$i])){
        // create table if not exist
        $query = $this->getCreateQueryByName($this->tableName[$i]);
        $this->connection->query($query);
      }
    }

  }
  public function getCreateQueryByName($table){

    $tableDefine= ["(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                     ipaddress varchar(50) NOT NULL UNIQUE,
                     devicename varchar(50) NOT NULL,
                     location varchar(100) NOT NULL,
                     addtime timestamp NOT NULL)",
                   "(accessid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                     accesstime timestamp NOT NULL,
                     deviceip varchar(50) NOT NULL UNIQUE,
                     version varchar(50) NOT NULL ,
                     serialnumber int(4) NOT NULL)"];

    $result  = 'create table if not exists '.$table;

    if(in_array($table,$this->tableName)){
      $result.=$tableDefine[array_search($table,$this->tableName)];
    }
    return $result;
  }
  public function resetTable($tableName){
    $stmt = $this->connection->query("TRUNCATE TABLE ".$tableName);
  }
  public function removeTables(){
    $this->deleteTables();
  }

  public function deleteTables(){

    /*
    for ($i=0; $i < count($this->tableName) ; $i++) {
        if($this->isTableExist($this->tableName[$i])){
          $this->resetTable($this->tableName[$i]);
          $this->connection->query("drop table if exists ".$this->tableName[$i]);
        }
    }
    */
    //just remove the access table keep the registered devices
    $this->resetTable($this->tableName[1]);
    $this->connection->query("drop table if exists ".$this->tableName[1]);
  }

  public function isTableExist($tableName){
      $result = $this->connection->query('select 1 from '.$tableName.' LIMIT 1');
      if($result!==false){
        return true;
      }else {
        //no such table
        return false;
      }
    }


  public function releaseConnection(){
    $this->connection->close();
  }


}

 ?>
