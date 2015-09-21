<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class settings extends CI_Controller{
	private $mckey = '';
	private $getShop = '';
	private $getApi = '';
	private $getSecret = '';
	private $token = '';
	private $password = '';
	private $mcList = '';
	function __construct(){
		parent::__construct();
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
	}
	public function index(){
		$this->load->helper('url');
		$data = '';

		if($this->mckey && $this->mcList){
			$config1 = array(
		    	'apikey' => $this->mckey,      // Insert your api key
	            'secure' => FALSE   // Optional (defaults to FALSE)
			);
			$this->load->library('MCAPI', $config1, 'mail_chimp1');
			$retval = $this->mail_chimp1->lists();
			foreach ($retval['data'] as $list) {
				if($list['id'] == $this->mcList){
					$listname = $list['name'];
				}
			}
		}else{
			$listname = '';
		}
		if($listname){
			$data['McList'] = $listname;
		}else{
			$data['McList'] = '';
		}
		// GET MAILCHIMP API
		if($this->mckey == '0'){
			$data['MCkey'] = '';
		}else{
			$data['MCkey'] = $this->mckey;
		}
		$data['getShop'] = '';
		$data['getApi'] = '';
		$data['getSecret'] = '';
		$data['getToken'] = '';
		$data['getPass'] = '';
		// GET SHOPIFY DATA
		if($this->getShop){
			$data['getShop'] = $this->getShop;
		}
		if($this->getApi){
			$data['getApi'] = $this->getApi;
		}
		if($this->getSecret){
			$data['getSecret'] = $this->getSecret;
		}
		if($this->token){
			$data['getToken'] = $this->token;
		}
		if($this->password){
			$data['getPass'] = $this->password;
		}

		$data['heading'] = 'P8P | Settings';
		$data['actionMC'] =	site_url('common/settings/MailchimpKey');
		$data['actionShopify'] = site_url('common/settings/InsertShopify');
		$data['webhook'] = base_url()."webhooks/webhooks.php";

		$this->load->view('common/header',$data);
		$this->load->view('common/nav',$data);
		$this->load->view('common/settings_view',$data);
		$this->load->view('common/footer',$data);

	}

	public function MailchimpKey(){
		
		$this->load->model('SettingsModel');
		header('Content-Type: application/json');

		if($_POST['key']){
			$config = array('apikey' =>$_POST['key'], 'secure' => FALSE);
			$this->load->library('MCAPI', $config, 'mail_chimp');
			$lists = $this->mail_chimp->lists();
			if($lists){
				$this->SettingsModel->InsertMC($_POST);
				echo "true";
			}else{
				echo "false";
			}
		}else{
			echo "null";	
		}
		
	}

	public function InsertShopify(){
		$this->load->model('SettingsModel');
		//header('Content-Type: application/json');
		if($_POST){
			$check = $this->ChkAuth($_POST);
			if($_POST['shopName'] == $this->getShop && $_POST['shopifyApi'] == $this->getApi && $_POST['shopifySecret'] == $this->getSecret && $_POST['shopifyToken'] != ''){
				/////////////  YES/NO dropdown value insert here //////////////////////////
				// echo("=======================if===================================================");
				// echo($_POST['re_subscribe']);
				if(isset($_POST['re_subscribe']))
			    {
					$query = $this->db->get('resubscribe');
					if($query->num_rows != 0)
					{
						foreach($query->result() as $row)
						{
							$data = array('re_subscribe' => $_POST['re_subscribe'] );
							$this->db->where('id', $row->id);
							$this->db->update('resubscribe', $data); 
	 					}	
					}else{
						$data = array('re_subscribe' => $_POST['re_subscribe'] );
						$this->db->insert('resubscribe', $data); 
					}
			    }

				redirect('common/settings', 'refresh');	
				
			}elseif($_POST['shopName'] != $this->getShop || $_POST['shopifyApi'] != $this->getApi | $_POST['shopifySecret'] != $this->getSecret || $_POST['shopifyToken'] == ''){
				$api = $_POST['shopifyApi'];
				$siteURL = $_POST['shopName'];
				$scope =  "write_products,read_orders";
				////////////// YES/NO value insert here //////////////////////////
				// echo("=======================else===================================================");
				// echo($_POST['re_subscribe']);
				if(isset($_POST['re_subscribe']))
			    {
					$query = $this->db->get('resubscribe');
					if($query->num_rows != 0)
					{
						foreach($query->result() as $row)
						{
							$data = array('re_subscribe' => $_POST['re_subscribe'] );
							$this->db->where('id', $row->id);
							$this->db->update('resubscribe', $data); 
	 					}	
					}else{
						$data = array('re_subscribe' => $_POST['re_subscribe'] );
						$this->db->insert('resubscribe', $data); 
					}
			    }
				$isnert = $this->SettingsModel->InsertSHInfo($_POST);
				//header("Location: https://".$siteURL."/admin/oauth/authorize?client_id=".$api."&scope=".$scope."&redirect_uri=http://localhost/codeigniter212/index.php/common/settings/authorize");
			}
		}
	}

	public function authorize(){
		$this->load->helper('url');
		IF($_REQUEST){
			$data['client_id'] = $this->getApi;
			$data['siteURL'] = $this->getShop;
			$data['client_secret'] = $this->getSecret;
			$data['code'] = $_REQUEST['code'];
			$data['url'] = "https://".$this->getShop."/admin/oauth/access_token";
			$url = "https://".$this->getShop."/admin/oauth/access_token";

			// $this->load->view('common/header',$data);
			// $this->load->view('common/settings_form',$data);

			$accessToken = $this->post_to_url($url, $data);
			$array = json_decode($accessToken);
			foreach ($array as $key => $value) {
				if($key == 'error'){

					redirect('common/settings', 'refresh');
				}else{
					
					$insertToken = $this->SettingsModel->insertToken($value);
					redirect('common/settings', 'refresh');
				}
			}
		}
	}
	function post_to_url($url, $data) {

		$postText = http_build_query($data);
		$body = 'client_id='.$data['client_id'].'&client_secret='.$data['client_id'].'&code='.$data['client_id'];
		$c = curl_init ($url);
		curl_setopt ($c, CURLOPT_POST, true);
		curl_setopt ($c, CURLOPT_POSTFIELDS, $postText);
		curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($c, CURLOPT_SSL_VERIFYHOST, false);
		$page = curl_exec ($c);
		curl_close ($c);
		return 	$page;	
	}

	public function ChkAuth($data){

		$queryShop = $this->db->get_where('tbl_key', array('value' => $data['shopName']));
		$countShop = $queryShop->num_rows();

		$queryAPI = $this->db->get_where('tbl_key', array('value' => $data['shopifyApi']));
		$countAPI = $queryAPI->num_rows();

		$querySecret = $this->db->get_where('tbl_key', array('value' => $data['shopifySecret']));
		$countSecret = $querySecret->num_rows();

		$queryToken = $this->db->get_where('tbl_key', array('value' => $data['shopifySecret']));
		$countToken = $queryToken->num_rows();


	}
	public function getList(){
		$key =$_POST['key'];
		$config1 = array(
	    	'apikey' => $key,      // Insert your api key
            'secure' => FALSE   // Optional (defaults to FALSE)
		);
		
		$this->load->library('MCAPI', $config1, 'mail_chimp1');

		$retval = $this->mail_chimp1->lists();
		$list1 = array();
		if($retval){
			foreach ($retval['data'] as $lists) {
				$list1[] = array($lists['id'] => $lists['name']);	
			}
		}
		if($list1){
			header('Content-Type: application/json');
			echo $json = json_encode($list1);	
		}else{
			echo "0";
		}
		
		//echo $json;
		//return $json;
	}
	private function check_isvalidated(){
		$this->load->helper('url');
		if(! $this->session->userdata('validated')){
			redirect('login/login', 'refresh');
		}
	}
}
?>