<?php 

// 载入配置文件
require_once '../config.php';

// 给用户找一个箱子（如果你之前有就用之前的，没有给个新的）
session_start();
// 判断是否是post请求
function login () {
// 1.接收并校验
// 2.持久化
// 3.响应
  if(empty($_POST['email'])){
    $GLOBALS['message'] = '请填写邮箱';
    return;
  }
  if(empty($_POST['password'])){
    $GLOBALS['message'] = '请填写密码';
    return;
  }


  $email = $_POST['email'];
  $password = $_POST['password'];

  // 当客户端提交过来的完整的表单信息就应该开始对其进行数据校验
  $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
  if (!$conn) {
    exit('<h1>连接数据库失败</h1>');
  }

  $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");

  if (!$query) {
    $GLOBALS['message'] = '登录失败，请重试！';
    return;
  }

  // 获取登录用户
  $user = mysqli_fetch_assoc($query);

  if (!$user) {
    // 用户名不存在
    $GLOBALS['message'] = '邮箱与密码不匹配';
    return;
  }

  // 一般密码是加密存储的
  if ($user['password'] !== md5($password)) {
    // 密码不正确
    $GLOBALS['message'] = '邮箱与密码不匹配';
    return;
  }

  // 存一个登录标识
  // $_SESSION['is_logged_in'] = true;
  //为了后续可以直接获取当前登陆用户的信息，直接将用户信息放到session中
  $_SESSION['current_login_user'] = $user;

  // 一切OK 可以跳转
  header('Location: /admin/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action'] === 'logout'){
  //删除登陆标识
  unset($_SESSION['current_login_user']);
}

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <!-- 可以通过在form上添加novalidate取消浏览器自带的校验功能 -->
  <!-- autocomplete="off" 关闭客户端的自动完成功能-->
  <div class="login">
    <form class="login-wrap<?php echo isset($message) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script>
  $(function($){
    //单独作用域
    //确保页面加载过后执行
    //在用户输入自己的邮箱过后，在页面上展示这个邮箱对应的头像（邮箱文本框失去焦点并且里面有数据）
    //里面的数据符合正则表达式检验才获取

    var emailFormat =/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/

    $('#email').on('blur',function(){
      var value = $(this).val()
      if(!value || !emailFormat.test(value)) return
      


      //获取这个邮箱对应的头像地址，展示到上面的img元素上
      //因为客户端的js 无法直接操作数据库，应该通过JS发送Ajax请求，让这个接口帮助客户端获取头像地址
      $.get('/admin/api/avatar.php',{email : value}, function(res){
        //希望res=>这个邮箱对应的头像地址
        if(!res) return
          //展示到上面的img元素上
        // $('.avatar').fadeOut().attr('src',res).fadeIn()
        $('.avatar').fadeOut(function(){
          $(this).on('load',function(){
            $(this).fadeIn()
          }).attr('src',res)
        })
      })
    })
  })
</script>
</body>
</html>
