<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class couponsModel extends CI_Model{
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
		$shopifyInfo = $this->SettingsModel->GetShopifyInfo();
		//var_dump($shopifyInfo);
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
			}
		}
		$config = array(
	    		'apikey' => $this->mckey, // Insert your api key
            	'secure' => FALSE   // Optional (defaults to FALSE)
		);
		$this->load->library('MCAPI', $config, 'mail_chimp');
	}

	public function insertCoupons($file){

		$date = date("Y-m-d H:i:s");
		$row = 1;
		if (($handle = fopen($file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        	$code = $data[0];
		        	if(isset($data[1])){
		        		$used = $data[1];	
		        	}else{
		        		$used = '0';
		        	}
		        	
		        	if(isset($data[2])){
		        		$subscriber = $data[2];
		        	}else{
		        		$subscriber = '';
		        	}
		        
		        	$query = $this->db->get_where('coupon', array('coupon_code' => $code));
		        	$rowcount = $query->num_rows();
		        	if($rowcount == 0){
			        	$data = array(
			        		'coupon_code' => $code,
			        		'used' => $used,
			        		'subscriber_email' => $subscriber,
			        		'date_added' => $date
			        		);
			        	$this->db->insert('coupon', $data);
		        	}elseif($rowcount == 1){
		        		$ret = $query->row();
		        		$id = $ret->id;
		        		$data = array(
						            'coupon_code' => $code,
			        				'used' => $used,
			        				'subscriber_email' => $subscriber,
			        				'date_added' => $date
						            );

						$this->db->where('id', $id);
						$this->db->update('coupon', $data); 	
		        	}
		    }
	    fclose($handle);
		}
	}

	public function updateCoupom($coupons){
		foreach ($coupons as $coupon) {
			$code = $coupon['code'];	
			$email = $coupon['email'];
			$amount = $coupon['amount'];
			$query = mysql_query("SELECT * FROM coupon WHERE coupon_code = '".$code."'") or die(mysql_error());
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

	public function removecoupon(){
		$query = $this->db->get('coupon');

		foreach ($query->result() as $row)
		{
		    if(!$row->subscriber_email)
		    {
		    	$this->db->delete('coupon', array('id' => $row->id)); 
		    }
		}
	}
}
?>