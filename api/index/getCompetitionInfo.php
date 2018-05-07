<?php	
/*
 * 返回竞赛信息  表 competitioninfo 
 */
	include("../conn.php");	
	$result=mysqli_query($conn,"select * from competitioninfo");
	$jarr = array();
	while ($rows=mysqli_fetch_array($result)){
	    $count=count($rows);//不能在循环语句中，由于每次删除 row数组长度都减小  
	    for($i=0;$i<$count;$i++){  
	        unset($rows[$i]);//删除冗余数据  
	    }
	    array_push($jarr,$rows);
	}	
	echo $str=json_encode($jarr,JSON_UNESCAPED_UNICODE);//将数组进行json编码

?>