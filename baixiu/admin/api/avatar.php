<?php 
 /**
 *根据用户邮箱获取用户头像
 */
 
require_once '../../config.php';

 //接收传递过来的邮箱
if(empty($_GET['email'])){
	exit('缺少必要参数');
}
$email = $_GET['email'];


 //查询对应的头像地址
$conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
if(!$conn){
	exit('连接数据库失败');
}

$res = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1;");
if(!$res){
	exit('查询失败');
}

$row = mysqli_fetch_assoc($res);

//echo输出}
echo $row['avatar'];