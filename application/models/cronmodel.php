<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CronModel extends CI_Model{
	private $mckey = '';
	private $getShop = '';
	private $getApi = '';
	private $getSecret = '';
	private $token = '';
	private $password = '';
	private $mcList = '';

	function __construct(){
		parent::__construct();
		$this->load->model('SettingsModel');
		$this->mckey = $this->SettingsModel->GetMailchimpApi();
		$this->mcList = $this->SettingsModel->GetMailchimpList();
		$config = array(
	    		'apikey' => $this->mckey, // Insert your api key
            	'secure' => FALSE   // Optional (defaults to FALSE)
		);
		$this->load->library('MCAPI', $config, 'mail_chimp');
	}


	public function updateCoupom($coupons){
		foreach ($coupons as $coupon) {
			$code = $coupon['code'];	
			$email = $coupon['email'];
			$amount = $coupon['amount'];
			$query = mysql_query("SELECT * FROM coupon WHERE coupon_code = '".$code."' AND subscriber_email = '".$email."'") or die(mysql_error());
			$count = mysql_num_rows($query);
			if($count > 0){
				$queryUpdate = mysql_query("UPDATE coupon SET subscriber_email = '".$email."', used = '1' WHERE coupon_code = '".$code."'") or die(mysql_error());
				$this->UpdateMClist($email,$code);
			}else{
				
			}
		}
	}

	public function UpdateMClist($email, $code){
		$merge_vars = array(
				'COUPONS'=>	'', 
				'USED'=> $code 
			);	
		$this->mail_chimp->listSubscribe($this->mcList , $email, $merge_vars, 'html', false );
		if ($this->mail_chimp->errorCode){
			return 0;
		} else {
		    return 1;
		}
	}


}
?>