<html><head>	
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-custom.css">
</head>
<body>
<?php 
include('MailChimp.php');
$api = '63713f59c1fb39cfb4e58665cf1c381c-us6';
$MailChimp = new MailChimp('63713f59c1fb39cfb4e58665cf1c381c-us6');

?>
<div class="span12">
<div class="span3">

	<Table class="table table-bordered">
		<tr>
			<h4>
Lists :
</h4>
		</tr>
		<tr>
			<th>
			List Name
			</th>
			<th>
			List ID
			</th>
			<th>
			Subscribers
			</th>
			<th>
			Groupings
			</th>
			<th>
			Groups
			</th>
		</tr>	
		
			<?php $list_all = $MailChimp->lists->getList(array(), $start=0, $limit=25, 'created', 'DESC');?>
				<?php  foreach ($list_all['data'] as $data) { ?>
				<tr>
					<td><a><?php echo   $data['name']; ?></a></a></td>
					<td><a><?php echo   $data['id']; ?></a></td>
					<Td><a><?php echo   $data['stats']['member_count']; ?></a></td>
					<Td><a><?php echo   $data['stats']['grouping_count']; ?></a></td>
					<Td><a><?php echo   $data['stats']['group_count']; ?></a></td>
				<tr>
			<?php } ?>
	</table>



</div>

</div>
</body></html>