<?php
	$session3rd=$_GET['session3rd'];
	include("../conn.php");
	$time=date("Y-m-d H:i:s");
	
	$res=mysqli_query($conn,"select * from auth where session3rd ='{$session3rd}' AND expiresTime >'{$time}'");
	echo mysqli_fetch_array($res)?"true":"false";

?>