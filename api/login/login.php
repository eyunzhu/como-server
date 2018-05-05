<?php  
	
	//小程序传过来的code,通过wx.login获取
    $js_code=$_GET['code'];
	//小程序传过来的encryptedData,通过getUserInfo获取
	$encryptedData=$_GET['encryptedData'];
	//小程序传过来的iv,通过getUserInfo获取
	$iv=$_GET['iv'];
	//小程序appid
    $appid="xxxxxxxx";
	//小程序AppSecret
    $secret="xxxxxxxx";
    $grant_type="authorization_code";
	//接口地址
    $url = 'https://api.weixin.qq.com/sns/jscode2session';  
    $data = array('appid'=>$appid,'secret'=>$secret,'js_code'=>$js_code,'grant_type'=>$grant_type);  
	$header = array();
  
	$response = curl_https($url, $data, $header, 5);  
	//echo $response;
    $json = json_decode($response);//对json数据解码  
    $arr = get_object_vars($json);
	//print_r($arr);
    /* 获取了$arr数组内容，包含：
    *	session_key，expires_in，openid
          Array
      (
          [session_key] => l/7hXfTozrXYBNKa5cbEtQ==
          [expires_in] => 7200
          [openid] => oyzTt0A2x-Mc_iMYhCoJt2CxUork
      )
    */

	$sessionKey=$arr['session_key'];

	//接下来对encryptedData解密
	include_once "wxBizDataCrypt.php";
    $pc = new WXBizDataCrypt($appid, $sessionKey);
    $errCode = $pc->decryptData($encryptedData, $iv, $data );
	//echo $data;
	$json1 = json_decode($data);//对json数据解码  
    $arr1 = get_object_vars($json1);
	//print_r($arr1);
	$data=$arr1;
	
	//echo($data) ;
	//$json="'".$data."'";
	//echo $json;
	//var_dump(json_decode($json));
	//var_dump(json_decode($json, true));
	
	/*
	//echo($data) ;
		$str="'".$data."'";
	//echo "****".$data."****";
	$data=json_encode($data);
	//$data=json_decode($data);
		$data=json_decode($data,true);
		//$data=json_decode($str,true);
		var_dump($data) ;
	*/

	/*用户信息存入数据库*/
	include("../conn.php");
	//判断是否已经存在用户
	$isSetUser=mysqli_query($conn,"select * from user where openId ='{$data['openId']}' ");
	if(!mysqli_fetch_array($isSetUser)){
     	$time=date("Y-m-d H:i:s");
    	$sql ="insert into user(openId,nickName,gender,language,city,province,country,avatarUrl,createDate) values('{$data['openId']}','{$data['nickName']}','{$data['gender']}','{$data['language']}','{$data['city']}','{$data['province']}','{$data['country']}','{$data['avatarUrl']}','{$time}')";
    	mysqli_query($conn,$sql);
    }

	//生成session3rd
	$session3rd=`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168`;
	//将生成的session3rd存入数据库
	//判断此用户是否存在session3rd
	$isSetSession3rd=mysqli_query($conn,"select * from auth where openId ='{$data['openId']}' ");
	if(mysqli_fetch_array($isSetSession3rd)){//如果此用户存在session3rd则先删除
    	$sql ="delete from auth where openId ='{$data['openId']}'";
    	mysqli_query($conn,$sql);
    }

	//一周过期时间
	$expiresTime=date('Y-m-d H:i:s',strtotime('+7 day'));
	mysqli_query($conn,"insert into auth(session3rd,openId,expiresTime) values('{$session3rd}','{$data['openId']}','{$expiresTime}')");

    $data=$data+Array('session3rd' => $session3rd);

	/*$arr=Array('session3rd' => $session3rd);
	$data=array_merge($data,$arr);*/
    $data=json_encode($data);
    print($data . "\n");


/** 函数 curl_https  curl 获取 https 请求 
* @param String $url        请求的url 
* @param Array  $data       要发送的数据 
* @param Array  $header     请求时发送的header 
* @param int    $timeout    超时时间，默认30s 
*/  
function curl_https($url, $data=array(), $header=array(), $timeout=30){  
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);  
  
    $response = curl_exec($ch);  
  
    if($error=curl_error($ch)){  
        die($error);  
    }  
  
    curl_close($ch);  
  
    return $response;  
  
}  
 
?>