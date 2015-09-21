<?php
require_once('parsecsv.lib.php');
# create new parseCSV object.
$csv = new parseCSV();
# Parse '_books.csv' using automatic delimiter detection...
$csv->auto('BYU-Radio.csv');
 ?>
 <?php $grand = array(); $parent = array(); $counter = 0; ?>
 <table> 
 <?php foreach ($csv->data as $key => $row): ?>

		<?php $merg_vars = array('FNAME' => $row['first_name'] ,
								 'LNAME' => $row['last_name'] , 
								 'MMERGE6' => $row['address_1'] ,
								// 'MMERGE3' => $row['create_date']
								 ); ?>
		<?php 
		 $mail_obj = (object) array('email' => $row['email_address'], 'euid' => '111'.$counter , 'leid' => 'byu456'.$counter);
		 $grand['email'] = $mail_obj ;
		 $grand['email_type'] = 'html' ;
		 $grand['merge_vars'] = $merg_vars ;
		 $parent[$counter] =  $grand;
		 $counter++; 
		  ?>
	
	<?php endforeach; ?>
 <table>
<pre><?php  // print_r( $parent); ?></pre>
<?php	
include('MailChimp.php');
 $new = new  MailChimp('733573ee36a84e29009352cc5f064f08-us7');
 $listId = '46041df925';
 $api = new MailChimp('733573ee36a84e29009352cc5f064f08-us7');
 $array  = array();   $index = 1;
 	$x=1; $y = 2; $blabla = '<br><br><br><br>====================================================<br><br><br><br>';
  for ( $x ; $x <= $y && $x < 11 ; $x++ ) {	

   		$chunkes[$x]  = $parent[$x];
   		//print_r($chunkes);
  		$index++;
		if($x == $y)
		{  

			echo '=== Array start <pre>'; print_r($chunkes); echo '</pre> Array end ===<br /><br />';
			$returnz = $api->lists->batchSubscribe($listId, $chunkes , true, false, true);
			if(isset($api->errorCode))  {echo $api->errorMessage;} else  {  
				echo  'Subscribed<br> === Response start <pre>'; print_r($returnz); echo '</pre>Response end===<br /><br />';
				 }
			echo $x;
			flush();
			
			 echo '<br>';
		$y = $x+1;
		$chunkes = [];
		}  	
  }   
?>

