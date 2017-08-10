<?php
require_once("databaseoop.php");
require_once("deviceoop.php");
require_once(__DIR__."/../../..".'/wp-config.php');
require_once(__DIR__."/../../..".'/wp-includes/pluggable.php');
// require_once(__DIR__.'\vendor\autoload.php') ;
class misysnews_tool_function{
  static function record_request_to_db(){
    if(self::isNonEmptyGetParameter($_GET['version'])&&self::isNonEmptyGetParameter($_GET['serialnumber'])){
      $db = new misysnews_dbconnection();
      $ip = self::get_client_ip();
      if(self::isIPExist($ip)){
        $stmt =$db->getConnection()->prepare("update ".$db->getTableName()["accessTable"]." set accesstime = NOW() , version = ? , serialnumber = ?  where deviceip =?");
      }else {
        $stmt = $db->getConnection()->prepare("insert into ".$db->getTableName()["accessTable"]." (accesstime, version,serialnumber, deviceip) values( NOW(),?,? ,?)");
      }
      $version = $_GET['version'];
      $serialnumber = (int)$_GET["serialnumber"];
      $stmt->bind_param("sis",$version,$serialnumber,$ip);
      $stmt->execute();
      $stmt->close();
      $db->releaseConnection;
    }
  }

  // Function to get the client IP address
 static  function get_client_ip() {
      $ipaddress = '';
      if (isset($_SERVER['HTTP_CLIENT_IP']))
          $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_X_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if(isset($_SERVER['REMOTE_ADDR']))
          $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
          $ipaddress = 'UNKNOWN';
      return $ipaddress;
  }
static  function isIPExist($ip){
  	$db = new misysnews_dbconnection();

  	$stmt = $db->getConnection()->prepare("select count(*) as number  from ".$db->getTableName()["accessTable"]." where deviceip =?");
    $stmt->bind_param("s",$ip);
    $stmt->execute();
    $stmt->bind_result($number);
    $stmt->fetch();
    $stmt->close();
  	if($number>0){
  		return true;
  	}else {
  		return false;
  	}
  }
  static function checkVersion($version,$serialnumber){
    if(get_option("misys_news_version")==$version && get_option("misysnews_runnable_serial_number")==$serialnumber){
      return true;
    }else{
      return false;
    }
  }
  static function isNonEmptyGetParameter($variable){
    return (isset($variable)&&(!empty($variable)));
  }
  static function checkIfDevicesDown(){
    if(misysnews_tool_function::isDeviceUpdateEmailToSend()){
      $emailObj = new misysnews_mail();
      $emailObj->sendEmailWithDownDevices();
      //send email to notice the admian user
      // $targetEmail = "ping.yuan@efrei.net";
      // $emailObj = new misysnews_mail($targetEmail,"test Subject","test Content","test template");
      // $result = wp_mail("ping.yuan@efrei.net","test subject","hello test content ");
    }
  }

  static function isDeviceUpdateEmailToSend(){

    $numberOfDownDevices = misysnews_tool_function::getDownDeviceNumberFromLog();
    if(!get_option("misysnews_number_of_down_device")){
      add_option("misysnews_number_of_down_device");
      update_option("misysnews_number_of_down_device",0);
    }
    if($numberOfDownDevices != get_option("misysnews_number_of_down_device")){
      update_option("misysnews_number_of_down_device",$numberOfDownDevices);
      return true;
    }else{
      return false;
    }

  }
  static function getDownDeviceNumberFromLog(){
    // only check the registered devices
    $misysnews_deviceMG_obj = new misysnews_deviceManagement();
    $registeredDevices = $misysnews_deviceMG_obj->listRegisteredDevice();
    // echo "the number of the devices that is registed :".count($registeredDevices)."<br/>";
    $numberOfDownDevices = 0;
    for($i = 0 ; $i<count($registeredDevices);$i++){
      // echo "the status of $i is ".$registeredDevices[$i]->getStatus()."<br/>";
      if($registeredDevices[$i]->isDeviceDown()){
        $numberOfDownDevices++;
      }
    }
    return $numberOfDownDevices;
  }
}

class misysnews_mail{
  private $from = "ping.yuan@misys.com";
  private $fromName = "";
  private $to = "ping.yuan@efrei.net";
  private $subject = "";
  private $body = "";
  private $contentTemplate = "";
  private $header= "";
  function __construct(){
    $this->headers .= "MIME-Version: 1.0" . "\r\n";
    $this->headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  }
  // function __construct($to, $subject, $body,$contentTemplate){
  //   $this->to = $to ;
  //   $this->subject = $subject ;
  //   $this->body = $body;
  // }
  function sendTemplateEmail(){
    wp_mail("ping.yuan@finastra.com",
      $this->getSubject(),
      $this->getAllDownDevcieEmailBody(),
      $this->headers);
  }
  function sendEmailWithDownDevices(){
    wp_mail($this->getAllAdminEmail(),
      $this->getSubject(),
      $this->getAllDownDevcieEmailBody(),
      $this->headers);
  }
  private function getAllAdminEmail(){
    $emails = [];
    $misysnews_users = get_users('role=Administrator');
    foreach ($misysnews_users as $user) {
      array_push($emails, $user->user_email);
    }
    return $emails;
  }
  //test method here
  private function  getAllDownDevcieEmailBody(){
    // return "hello i just test this come on boy";
    return $this->getContent();
  }
  private function getSubject(){
    $subject = "Warning: Finastra notification";

    return $subject;
  }
  private function getContent(){
    $content = "<html>";
      $content .="<head>";
        $content.=$this->getEmailHeader();
      $content.="</head>";
      $content.="<body>";
        $content.=$this->getSummaryContent();

        $content.=$this->getDeviceDetailsTable();
        $content.=$this->getEmailFooter();
      $content.="</body>";
    $content.="</html>";
    return $content;
  }

  private function getEmailHeader(){
    $emailHeader = "Dear Admin:\r\n";

    return $emailHeader;
  }
  private function getSummaryContent(){
    $deviceSummary = "";
    $deviceSummary.="<h4>";
    $deviceSummary.="Finastra Devices update: <br/>";
    $deviceSummary.="</h4>";
    $deviceSummary.="<p>";
    if($this->getDownDeviceNumber() > 0){
      $deviceSummary.="Unforturnately,".$this->getDownDeviceNumber();
      if($this->getDownDeviceNumber == 1){
        $deviceSummary.=" device is";
      }else{
        $deviceSummary.=" devices are";
      }
      $deviceSummary.=" down. <br/>";
    }else{
      $deviceSummary.="Congratulations! All the devices are running well. <br/>";
    }
    $deviceSummary.="Please find the details below : <br/>";
    $deviceSummary.="</p>";
    return $deviceSummary;
  }

  private function getDeviceDetailsTable(){
    $deviceTable = "";
    $deviceTable .= "
      <table width='350' cellpadding='5' border='1'
        style='  text-align: left;border-collapse: collapse;'
        >
        <tr>
          <th>Device Name</th>
          <th>Device IP</th>
          <th>Last Access</th>
        </tr>".
          $this->getRegisteredDeviceDetails()
        ."</table>";
    return $deviceTable;
  }

  private  function getRegisteredDeviceDetails(){
    $details = "";
    $misysnews_deviceMG_obj = new misysnews_deviceManagement();
    $registeredDevices = $misysnews_deviceMG_obj->listRegisteredDevice();
    for($i = 0 ; $i < count($registeredDevices) ; $i++){
      $details.="<tr>";
      // style=color:".$registeredDevices[$i]->isDeviceDown()?'red':'green'."
        $details .="<td >"
        // $details .= '<td style="color:"."'
          .$registeredDevices[$i]->getName()."</td>";
        $details .="<td>".$registeredDevices[$i]->getIP()."</td>";
        $details .="<td>".$registeredDevices[$i]->getLatestRequest()."</td>";
      $details .="</tr>";
    }
    return $details;
  }

  private function getEmailFooter(){
    $emailFooter = "";
    $emailFooter .="<p>";
    $emailFooter .= "Best Regard <br/>";
    $emailFooter .="Finastra News Support";
    $emailFooter .="</p>";
    return $emailFooter;
  }
  private function getDownDeviceNumber(){
    return misysnews_tool_function::getDownDeviceNumberFromLog();
  }
}



?>
