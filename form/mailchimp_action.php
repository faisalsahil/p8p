<?php
include 'conn.php';

$double = 'false'; // Enter true or false for Double Opt-in
$welcome = 'true'; // Enter true or false to send the Final Welcome Message

$apikey = 'db45276ffd03b999ff07a4d514953198-us6';
$apiUrl = 'http://api.mailchimp.com/1.3/';
$listId = 'b1550099aa'; 


require_once 'MCAPI.class.php';
$api = new MCAPI($apikey);

//------------------------------------------------------------------------------//

// EDIT BELOW THIS LINE AT YOUR OWN RISK

ob_start();
echo "Your request is being processed...";

$name = $_POST["fname"];
$lname = $_POST["lname"];
$email = $_POST["email"];
$format = "html";


if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	// $options = array();
	// $result = $api->listMergeVarAdd($listId, 'COUPON','COUPON', $options);

	// $options1 = array();
	// $result1 = $api->listMergeVarAdd($listId, 'USED','USED', $options1);

	$coupon = getCoupon();
	if($coupon)
	{
		$merge_vars = array(
			'FNAME'=>$name,
			'LNAME'=>$lname,
			'COUPON'=>$coupon);
		$retval = $api->listSubscribe( $listId, $email, $merge_vars, 'html', false, true);

		if ($api->errorCode){
			$error = "Unable to load listSubscribe()!\n";
			$error .= "\tCode=".$api->errorCode."\n";
			$error .= "\tMsg=".$api->errorMessage."\n";
		//var_dump($error);
			header ("Location: html.html?msg=mc_error");
                //wh_log($error);
		} else {
			$success = "Subscribed - look for the confirmation email!\n".$retval;
			updateCoupon($coupon,$email);
			header ("Location: html.html?msg=success");

		}
	}
	else{
		header ("Location: html.html?msg=coupon_error");
	}	

}else{
	header ("Location: html.html?msg=email_error");
}







/***********************************************
    Helper Functions
    ***********************************************/
    function wh_log($msg){
    	$logfile = 'webhook.log';
    	file_put_contents($logfile,$msg."\n",FILE_APPEND);
    }
    function checkEmail($email,$user){
    	$query = mysql_query("SELECT * FROM contacts  WHERE EMAIL = '".$email."' AND userid ='". $user."'" ) or die(mysql_error());
    	$count = mysql_num_rows($query);
    	if($count == 0){
    		return 0;
    	}else{
    		return 1;
    	}
    }

    function getCoupon(){
    	$query = mysql_query("SELECT coupon_code FROM coupon WHERE used = '0' AND subscriber_email = '' LIMIT 1")or die(mysql_error());
    	//$query = mysql_query("SELECT coupon_code FROM coupon") or die(mysql_error());
    	$coupon = mysql_fetch_row($query);
    	return $coupon[0];
    }

    function updateCoupon($code, $email){
    	$query = "UPDATE coupon SET subscriber_email = '".$email."' WHERE coupon_code = '".$code."'";
    //wh_log($query);
    	$sql = mysql_query($query)or die(mysql_error());
    	if($query){
    		return 1;
    	}
    }

    function updateMailChimp($lsit, $code, $email){
    	$merge_vars = array('COUPON'=>$code);
    	$retval = $api->listSubscribe( $lsit, $email, $merge_vars, 'html', false, true);
    	if ($api->errorCode){
    		$error = "Unable to load listSubscribe()!\n";
    		$error .= "\tCode=".$api->errorCode."\n";
    		$error .= "\tMsg=".$api->errorMessage."\n";
    		return $error;
    	} else {
    		$success = "Subscribed - look for the confirmation email!\n";
    		return $success;
    	}
    }




    ?>
