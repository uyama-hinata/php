<?php
require("../dbconnect.php");

session_start();
$error= array();

if(!empty($_POST)){
    // バリデーション
    if($_POST['id']===""){
        $error['id-blank']='ログインIDを入力してください';
    }
    elseif(mb_strlen($_POST['id'])<7 || mb_strlen($_POST['id']) > 10 ){
        $error['id-length']='7～10文字以内で入力してください';
    }
    elseif (!preg_match( '/^[0-9a-zA-Z]+$/', $_POST['id']) ) {
        $error['id-format'] = '半角英数字で入力してください';
    }

    if($_POST['password']===""){
        $error['password-blank']='パスワードを入力してください';
    }
    elseif(mb_strlen($_POST['password'])<8 || mb_strlen($_POST['password']) > 20 ){
        $error['password-length']='8～20文字以内で入力してください';
    }
    elseif (!preg_match( '/^[0-9a-zA-Z]+$/', $_POST['password']) ) {
        $error['password-format'] = '半角英数字で入力してください';
    }

    // データベースと相違ないか判定
    if(empty($error)){
        $stmt =$db->prepare("SELECT * FROM administers WHERE login_id=:login_id AND deleted_at IS NULL");
        $stmt->bindValue(':login_id', $_POST['id']);
        $stmt->execute();
        $data=$stmt->fetch();

        if($data && $data['login_id'] === $_POST['id'] && $_POST['password']===$data['password']){
            $_SESSION['admin_name'] = $data['name'] ;
            $_SESSION['admin_id']=$_POST['id'];
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
        <title>管理画面ログイン</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>

    <body>
        <div class="wrapper">
            <header>

            </header>
            <main>
                <div class="form-title">管理画面</div>
                <form action="" method="post">
                    <div class="form-item"> 
                        <span>ログインID</span>
                        <input type="text" name="id" value="<?php if(!empty($error)){echo $_POST['id'];}?>">

                        <!-- エラー文表示 -->
                        <div class="error">
                            <?php echo isset($error['id-blank']) ? $error['id-blank'] : ''; ?>
                            <?php echo isset($error['id-length']) ? $error['id-length'] : ''; ?>
                            <?php echo isset($error['id-format']) ? $error['id-format'] : ''; ?>
                        </div>
                    </div>

                    <div class="form-item">
                        <span>パスワード</span>
                        <input type="password" name="password" >

                        <!-- エラー文表示 -->
                        <div class="error">
                            <?php echo isset($error['password-blank']) ? $error['password-blank'] : ''; ?>
                            <?php echo isset($error['password-length']) ? $error['password-length'] : ''; ?>
                            <?php echo isset($error['password-format']) ? $error['password-format'] : ''; ?>
                            <?php echo isset($error['login']) ? $error['login'] : ''; ?>
                        </div>
                    </div>

                    <input type="submit" class="btn_login" value="ログイン">
                </form>
            </main>
            <footer>

            </footer>
        </div>
    </body>
</html>