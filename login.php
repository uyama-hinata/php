<?php
require("./dbconnect.php");
session_start();

$email = '';
$password = '';
$error= array();



// POST送信があるかないか判定
if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    

    // バリデーション
    if ($email === '') {
        $error['email'] = 'メールアドレス(ID)を入力してください';
    }
    if ($password === '') {
        $error['password'] = 'パスワードを入力してください';
    }

    // データベースと相違ないか判定
    $sql = "SELECT * FROM members WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $data=$stmt->fetch();

    if(empty($error)){
        if($data && $data['email'] === $email && password_verify($password, $data['password'])){
            $_SESSION['name'] = $data['name_sei'] . ' ' . $data['name_mei'];
            $_SESSION['id']=$data['id'];
            header('Location:top.php');
            exit;
        }else{
          $error['login'] = 'IDもしくはパスワードが間違っています';
        }
    }
}

?>



<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheet.css">
    </head>

    <body>
        <main>
         <form action="" method="post">
             <div class="form-title">ログイン</div>
    
             <div class="form-item"> 
                 <span>メールアドレス(ID)</span>
                 <input type="email" name="email" value="<?php if(!empty($error)){echo $email;}?>">

                 <!-- エラー文表示 -->
                 <div class="error"><?php echo isset($error['email']) ? $error['email'] : ''; ?></div>
             </div>

             <div class="form-item">
                 <span>パスワード</span>
                 <input type="password" name="password" >

                 <!-- エラー文表示 -->
                 <div class="error"><?php echo isset($error['password']) ? $error['password'] : ''; ?></div>
                 <div class="error"><?php echo isset($error['login']) ? $error['login'] : ''; ?></div>
             </div>

             <input type="submit" class="btn next" value="ログイン">
         </form>
         <a href="logout.php"  class="btn back">トップへ戻る</a>
        </main>

    </body>
</html>
