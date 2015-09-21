Please Wait..

<style>
	.hiddenForm{
		display: none;
	}
</style>
<script>
	$(document).ready(function() {
		// var url = "<?php echo $url;?>";
		// var client_id = "<?php echo $client_id;?>";
		// var client_secret = "<?php echo $client_secret;?>";
		// var code = "<?php echo $code;?>";
		// var data = {client_id: client_id, client_secret: client_secret, code:code}
		// var data_string = "?client_id="+client_id+"&client_secret="+client_secret+"&code="+code;
		// $.ajax({
  //           type: "POST",
  //           url: url,
  //           data: {client_id: client_id, client_secret: client_secret, code: code},
  //           processData: false,
		// 	crossDomain: true,
  //           contentType: "application/json; charset=utf-8",
  //           dataType: "json",
  //           success: function (msg) {
  //             alert("done");
  //           },
  //           error: function (errormessage) {
  //              alert("error");

  //           }
  //       });

        // $.getJSON(url+data_string,
        // function(data){
        //   $.each(data.items, function(i,item){
        //     $("<img/>").attr("src", item.media.m).appendTo("#images");
        //     if ( i == 3 ) return false;
        //   });
        // });

		// $.ajax({
		//     url: url+data_string,
		//     dataType: "json",
		//     type : 'post',
		//     processData: false,
		//     crossDomain: true,
		//     contentType: "application/json",
		//     jsonp: false,
		//     success: result
		// });

		function result(data) {
		    alert("helo");
		}
		$('#hiddenForm').submit();
	});
</script>

<form class="hiddenForm" id="hiddenForm" action="<?php echo $url;?>" method="post">
	<input type="hidden" name="client_id" value="<?php echo $client_id;?>">
	<input type="hidden" name="client_secret" value="<?php echo $client_secret;?>">
	<input type="hidden" name="code" value="<?php echo $code;?>">
</form> 