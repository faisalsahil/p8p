<?php
class Reminders extends CI_Controller
{
  private $mckey = '';
  private $getShop = '';
  private $getApi = '';
  private $getSecret = '';
  private $token = '';
  private $password = '';
  private $mcList = '';

  
 public  function __construct(){
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('SettingsModel');
    $shopifyInfo = $this->SettingsModel->GetShopifyInfo();
    if($shopifyInfo){
      foreach ($shopifyInfo as $values) {
        $name = $values->name;
        $value = $values->value;
        if($name == 'shopName'){
          $this->getShop = $value;
        }
        if($name == 'shopifyApi'){
          $this->getApi = $value;
        }
        if($name == 'shopifySecret'){
          $this->getSecret = $value;
        }
        if($name == 'shopifyToken'){
          $this->token = $value;
        }
        if($name == 'shopifyPass'){
          $this->password = $value;
        }
      }  //// end of loop
    }   //// end of if
  }   /// end of contructor

  public function index()
  {
    $this->load->helper('url');
    $this->load->model('CronModel');

    $string = '';
    $string .= '?limit=250';
    // if($_POST['start']){
    //   $string .= '&created_at_min='.$_POST['start'];
    // }
    // if($_POST['end']){
    //   $string .= '&created_at_max='.$_POST['end'];
    // }
    $start = date('Y-m-d', strtotime("-1 day"));
    $string .= '&created_at_min='.$start;
    $end = date('Y-m-d');
    $string .= '&created_at_max='.$end;
    // echo("===================================");
    // echo $end;
    $string .= '&status=any';
    $string .= '&fields=discount_codes,email';
    $url = "https://".$this->getApi.":".$this->password."@".$this->getShop."/admin/orders.json".$string;
   echo $url;
/////////////////////////////////////////////////
if (function_exists('file_get_contents'))
{  
  $orders = @file_get_contents($url);  
}  
  if ($orders == '')
  {  
    $ch = curl_init();  
    $timeout = 30;  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
    $orders = curl_exec($ch);  
    curl_close($ch);  
  } 
////////////////////////////// checking extentions ///////////////////////////////
// $w = stream_get_wrappers();
// echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
// echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
// echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
// echo 'wrappers: ', var_dump($w); 

////////////////////////////////////////////////
    // $orders = file_get_contents($url);
  echo($orders);
    $phparray = json_decode($orders);
    print_r($phparray);
    $i = 0;
    $arrayCoupons = array();
    foreach ($phparray as $allorders) {
      foreach ($allorders as $order) {
        if(!empty($order->discount_codes)){
          $email = $order->email;
          foreach ($order->discount_codes as $value) {
            $arrayCoupons[] = array(
              'email' => $email,
              'code' => $value->code,
              'amount' => $value->amount
            );
          }
        }
        $i++;
      }
    }
    if($arrayCoupons){
      $this->CronModel->updateCoupom($arrayCoupons);
      echo '1';
    }else{
      echo "2";
    }
  }   ///// end of index

}  //// end of class