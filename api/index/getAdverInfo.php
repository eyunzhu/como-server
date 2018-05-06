<?php	
/*
 * 返回首页顶部轮转广告
 */
	include("../conn.php");
	mysqli_query($conn,"set names utf8");	
	$result=mysqli_query($conn,"select * from adverinfo");
	$jarr = array();
	while ($rows=mysqli_fetch_array($result)){
	    $count=count($rows);//不能在循环语句中，由于每次删除 row数组长度都减小  
	    for($i=0;$i<$count;$i++){  
	        unset($rows[$i]);//删除冗余数据  
	    }
	    array_push($jarr,$rows);
	}	
	echo $str=json_encode($jarr);//将数组进行json编码

?>