<?php
include 'conn.php';
require_once 'MCAPI.class.php';
require_once 'config.inc.php'; //contains apikey

/***********************************************
Sample code for handling MailChimp Webhooks - to write the logfile, your webserver must be able to write 
to the file in the wh_log() function below.

This also assumes you use a key to secure the script and configure a URL like this in MailChimp:

http://www.mydomain.com/webhook.php?key=EnterAKey!

***********************************************/
$queryApi = mysql_query("SELECT value FROM tbl_key WHERE type = 'MC' AND name = 'key'") or die(mysql_error());
$key = mysql_fetch_row($queryApi);
$mcKey = $key['0'];

$querylsit = mysql_query("SELECT value FROM tbl_key WHERE type = 'MC' AND name = 'list'") or die(mysql_error());
$lsit = mysql_fetch_row($querylsit);
$mcList = $lsit['0'];

$api = new MCAPI($mcKey);

    $options = array();
    $result = $api->listMergeVarAdd($listId, 'COUPON','COUPON', $options);

    $options1 = array();
    $result1 = $api->listMergeVarAdd($listId, 'USED','USED', $options1);

    $email = $_REQUEST['data']['email'];
    if($email){
        $response = EmailChecking($email);
        if($response == 0)      ///// if email id does not exist in DB then we assign a coupon
        {
            $coupon = getCoupon();
            wh_log($email."-->".$coupon );
            $merge_vars = array('COUPON'=>$coupon);
            $retval = $api->listSubscribe( $mcList, $email, $merge_vars, 'html', false, true);
            if ($api->errorCode){
                $error = "Unable to load listSubscribe()!\n";
                $error .= "\tCode=".$api->errorCode."\n";
                $error .= "\tMsg=".$api->errorMessage."\n";
                //wh_log($error);
            } else {
                $success = "Subscribed - look for the confirmation email!\n";
                //wh_log($success);
            }
            updateCoupon($coupon,$email);
        }else{
            //////////////// if email id already exist in DB then we first check admin permission then take action accordingly.
             $query = $this->db->get('resubscribe');
            foreach ($query->result() as $rr)
            {
                if($rr->re_subscribe == 1)   //// means allow from admin to assign another coupon
                {
                    {
                        $coupon = getCoupon();
                        wh_log($email."-->".$coupon );
                        $merge_vars = array('COUPON'=>$coupon);
                        $retval = $api->listSubscribe( $mcList, $email, $merge_vars, 'html', false, true);
                        if ($api->errorCode){
                            $error = "Unable to load listSubscribe()!\n";
                            $error .= "\tCode=".$api->errorCode."\n";
                            $error .= "\tMsg=".$api->errorMessage."\n";
                            //wh_log($error);
                        } else {
                            $success = "Subscribed - look for the confirmation email!\n";
                            //wh_log($success);
                        }
                        updateCoupon($coupon,$email);
                    }
                }else{                      //// means dnt allow another coupon to that email id which already have a coupon.
                        ///Do Nothing
                }
            }
        }
    }    
//}

// foreach ($_REQUEST['merges'] as $data) {
//         $coupon = getCoupon();
//         $mergs = $data['merges'];
//         $email = $mergs['EMAIL'];

//         wh_log($coupon." --> ". $email);
//         //updateCoupon($coupon,$email);

//         // mailchimp update member
//         $merge_vars = array('COUPON'=>$code);
//         $retval = $api->listSubscribe( $mcList, $email, $merge_vars, 'html', false, true);
//         if ($api->errorCode){
//             $error = "Unable to load listSubscribe()!\n";
//             $error .= "\tCode=".$api->errorCode."\n";
//             $error .= "\tMsg=".$api->errorMessage."\n";
//             //wh_log($error);
//         } else {
//             $success = "Subscribed - look for the confirmation email!\n";
//             //wh_log($success);
//         }


//         //wh_log($updateMC."\n");
// }

/***********************************************
    Helper Functions
***********************************************/
function wh_log($msg){
    $logfile = 'webhook.log';
    file_put_contents($logfile,$msg."\n",FILE_APPEND);
}
// function checkEmail($email,$user){
//     $query = mysql_query("SELECT * FROM contacts  WHERE EMAIL = '".$email."' AND userid ='". $user."'" ) or die(mysql_error());
//     $count = mysql_num_rows($query);
//     if($count == 0){
//         return 0;
//     }else{
//         return 1;
//     }
// }
function EmailChecking($email){
    $query = mysql_query("SELECT * FROM coupon  WHERE subscriber_email = '".$email."' " ) or die(mysql_error());
    $count = mysql_num_rows($query);
    if($count == 0){
        return 0;
    }else{
        return 1;
    }
}
function getCoupon(){
    $query = mysql_query("SELECT coupon_code FROM coupon WHERE used = '0' AND subscriber_email = '' LIMIT 1")or die(mysql_error());
    $coupon = mysql_fetch_row($query);
    wh_log(print_r($coupon,true));
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