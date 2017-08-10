<?php
require_once(__DIR__."/databaseoop.php");

class misysnews_device{
  private $ip;
  private $name;
  private $location;
  private $status;
  private $latestRequest;
  private $serialNumber;
  private $version;

  public function __construct(){
    date_default_timezone_set('Europe/Paris');
  }

  public function getIP(){return $this->ip;}
  public function getName(){return $this->name;}
  public function getLocation(){return $this->location;}
  public function getStatus(){return $this->status;}
  public function getLatestRequest(){return $this->latestRequest;}
  public function getSerialNumber(){return $this->serialNumber;}
  public function getVersion(){return $this->version;}
  //unless the stauts is green then the device is down.
  public function isDeviceDown(){
    if($this->getStatus() != "green" ){
      return true;
    }else{
      return false;
    }
  }
  public function setData($ip,$name,$latestRequest,$serialNumber,$version){
    $this->ip = $ip;
    $this->name = $name;
    $this->latestRequest = $this->getHumanTime($latestRequest);
    $this->status = $this->setDeviceStatusByTime($latestRequest);
    $this->serialNumber = $serialNumber;
    $this->version = $version;
  }
  public function setRegisterInformation($ip,$name){
    $this->ip = $ip;
    $this->name = $name;
  }
  //green => ok
  //yellow => warning
  //red => wrong
  private function setDeviceStatusByTime($latestTime){
    $greenUpperLimit = 60*10;
    $yellowUpperLimit = 60*30;

    $timeDuring;
    if((time()-strtotime($latestTime)) < 0){
      $timeDuring = (time() - strtotime($latestTime))+3600;
    }else{
      $timeDuring = (time() - strtotime($latestTime));
    }

    if($timeDuring < $greenUpperLimit){
      return "green";
    }else if($timeDuring < yellowUpperLimit){
      return "yellow";
    }else{
      return "red";
    }
  }
  private function getHumanTime($time){
    $latestRequest;
    //format the time
    if((time()-strtotime($time)) < 0){
      $latestRequest =  (strtotime($time) - 3600);
    }else {
      $latestRequest = strtotime($time);
    }
    $humanTime = human_time_diff($latestRequest,time());
    return $humanTime;
  }
}
class misysnews_deviceManagement{
  private $connection;
  private $tableName;
  public function __construct(){
    $db = new misysnews_dbconnection();
    $this->connection = $db->getConnection();
    $this->tableName = $db->getTableName();
  }
  /**
  * list all the registered devices
  */
  public function listRegisteredDevice(){
    $registeredDevicesWithStatus = [];
    $stmt = $this->connection->prepare("select * from ".$this->tableName["deviceTable"]);
    $stmt->execute();
    $stmt->bind_result($deviceID,$deviceIP,$deviceName,$deviceLocation,$deviceAddTime);


    while($stmt->fetch()){
      $device = new misysnews_device();
      $dbConnetion = new misysnews_dbconnection();
      $stmtJoin=$dbConnetion->getConnection()->prepare("select * from ".$this->tableName["accessTable"]." where deviceip = ?");
      $stmtJoin->bind_param("s",$deviceIP);
      $stmtJoin->execute();

      $stmtJoin->bind_result($accessid,$accesstime,$accessip,$version,$serialNumber);

      $stmtJoin->fetch();
      $stmtJoin->close();

      $device->setData($deviceIP,$deviceName, $accesstime,$serialNumber,$version);

      array_push($registeredDevicesWithStatus,$device);
    }
    $stmt->close();

    return $registeredDevicesWithStatus;
  }
  /**
  *list all unrecognized devices from various resources
  */
  public function listUnrecognizedDevices(){

    $allUnrecognizedDevicesWithStatus= [];

    $stmt = $this->connection->prepare( "select * from ".$this->tableName["accessTable"]."
      where deviceip not in (select ipaddress from ".$this->tableName["deviceTable"]." )");
    $stmt->execute();
    $stmt->bind_result($accessid,$accessTime,$accessip,$version,$serialNumber);

    while($stmt->fetch()){
      $device = new misysnews_device();
      $device->setData($accessip,$accessip,$accessTime,$serialNumber,$version);
      array_push($allUnrecognizedDevicesWithStatus,$device);
    }
    $stmt->close();
    return $allUnrecognizedDevicesWithStatus;

  }
  //not use for now, in the future this can be removed
  public function registerOneDevice($device){
    $stmt = $this->connection->prepare("insert into ".$this->tableName["deviceTable"]." (ipaddress,devicename,addtime,version,serialnumber) values(?,?,NOW())");
    $stmt->bind_param("ss",$device->getIP(),$device->getName());
    $stmt->execute();

  }
  public function clearAllUnactiveDevice(){
    $unregisteredDevice = $this->listUnrecognizedDevices();
    for ($i=0; $i < count($unregisteredDevice) ; $i++) {
      if($unregisteredDevice[$i]->getStatus() === 'red'){
        $this->removeUnregisteredDeviceById($unregisteredDevice[$i]->getIP());
      }
    }
  }
  public function removeUnregisteredDeviceById($ip){
    $stmt = $this->connection->prepare("delete from ".$this->tableName["accessTable"]." where deviceip = ?");
    $stmt->bind_param("s",$ip);
    $stmt->execute();
  }


}
?>
