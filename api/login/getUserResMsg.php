<?php
	$session3rd=$_GET['session3rd'];
   $sql=" SELECT user.* FROM auth,user WHERE auth.openId=user.openId AND auth.session3rd='{$session3rd}'";
	include("../conn.php");	
	$res=mysqli_query($conn,$sql);
	while($row=mysqli_fetch_array($res)){
	  $data=json_encode($row);
	     print($data . "\n");
	}
	

?>