<?php
class Coupons extends CI_Controller{
	private $mckey = '';
	private $getShop = '';
	private $getApi = '';
	private $getSecret = '';
	private $token = '';
	private $password = '';
	private $mcList = '';

	function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->check_isvalidated();
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

	public function index(){
		redirect('coupons/add', 'refresh');
	}

	public function add(){
		$this->load->helper('url');
		$data = '';
		$data['heading'] = 'P8P | Add Coupons';
		$data['action'] = site_url('coupons/UploadCsv');

		$this->load->view('common/header',$data);
		$this->load->view('common/nav',$data);
		$this->load->view('coupons/addCoupon_view',$data);
		$this->load->view('common/footer',$data);
	}
	public function update(){
		$this->load->helper('url');
		$data = '';
		$data['heading'] = 'P8P | Update';
		$data['updateAction'] = site_url('coupons/updateCoupons');
		$this->load->view('common/header',$data);
		$this->load->view('common/nav',$data);
		$this->load->view('coupons/updateCoupon_view',$data);
		$this->load->view('common/footer',$data);
	}

	public function UploadCsv(){
		$this->load->model('couponsModel');
		header('Content-Type: application/json');

		////////////////////////////file upload code start here//////////////////////

		$UploadDirectory	= 'uploads/csv/'; //Upload Directory, ends with slash & make sure folder exist

		if (!@file_exists($UploadDirectory)) {
			//destination folder does not exist
			die("4");
		}

		if($_POST)
		{	
			
			if(!isset($_FILES['mFile']))
			{
				//required variables are empty
				die("2");
			}

			
			if($_FILES['mFile']['error'])
			{
				//File upload error encountered
				die(upload_errors($_FILES['mFile']['error']));
			}
			$time = time();
			$FileName			= strtolower($_FILES['mFile']['name']); //uploaded file name
			$ImageExt			= substr($FileName, strrpos($FileName, '.')); //file extension
			$FileType			= $_FILES['mFile']['type']; //file type
			$FileSize			= $_FILES['mFile']["size"]; //file size
			$RandNumber   		= rand(0, 9999999999); //Random number to make each filename unique.
			$uploaded_date		= date("Y-m-d H:i:s");
			
			$NewFileName = $time.$FileName;
		   //Rename and save uploded file to destination folder.
			if($ImageExt == '.csv'){
			   if(move_uploaded_file($_FILES['mFile']["tmp_name"], $UploadDirectory . $NewFileName ))
			   {	
			   		$newPath = $UploadDirectory.$NewFileName;
			   		$insert = $this->couponsModel->insertCoupons($newPath);	
					die('1');
			   }else{
			   		die('2');
			   }
			}else{
				echo "3";
			}
		}
	}

	public function updateCoupons(){
		//var_dump($_POST);
		$this->load->model('couponsModel');

		$string = '';
		$string .= '?limit=250';
		if($_POST['start']){
			$string .= '&created_at_min='.$_POST['start'];
		}
		if($_POST['end']){
			$string .= '&created_at_max='.$_POST['end'];
		}
		$string .= '&status=any';
		$string .= '&fields=discount_codes,email';
		$url = "https://".$this->getApi.":".$this->password."@".$this->getShop."/admin/orders.json".$string;
		$orders = file_get_contents($url);
		$phparray = json_decode($orders);
		$i = 0;
		$arrayCoupons = array();
		foreach ($phparray as $allorders) {
			//var_dump($allorders);
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
			$this->couponsModel->updateCoupom($arrayCoupons);
			echo '1';
		}else{
			echo "2";
		}
		//var_dump($arrayCoupons);
	}

	function post_to_url($url, $data) {

		$postText = http_build_query($data);
		//$body = 'client_id='.$data['client_id'].'&client_secret='.$data['client_id'].'&code='.$data['client_id'];
		$c = curl_init ($url);
		//curl_setopt ($c, CURLOPT_GET, true);
		curl_setopt ($c, CURLOPT_POSTFIELDS, $postText);
		curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($c, CURLOPT_SSL_VERIFYHOST, false);
		$page = curl_exec ($c);
		curl_close ($c);
		return 	$page;	
	}
	private function check_isvalidated(){
		$this->load->helper('url');
		if(! $this->session->userdata('validated')){
			redirect('login/login', 'refresh');
		}
	}

	public function listSubscribe(){
		$batch[] = array(
					'EMAIL' => 'scottlummes@hotmail.com'
					);
		// $batch[] = array(
		// 			'EMAIL' => 'scottlummes@hotmail.com'
		// 			);
		// $batch[] = array(
		// 			'EMAIL' => 'sl@hullabaloo.uk.com'
		// 			);
		// $batch[] = array(
		// 			'EMAIL' => 'huajin0316@hotmail.com'
		// 			);
		var_dump($batch);
		$this->mail_chimp->listBatchSubscribe($this->mcList, $batch, 'html', false, false);

	}
	public function remove(){
		$this->load->model('couponsmodel');
		$response = $this->couponsmodel->removecoupon();

		$data = '';
		$this->load->view('common/header',$data);
		$this->load->view('common/nav',$data);
		$this->load->view('coupons/coupon_view',$data);
		$this->load->view('common/footer',$data);
	}
}
?>