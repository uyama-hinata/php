<?php
require("../dbconnect.php");
session_start();

if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

// ボタン連打による二重登録を防ぐ
// トークンの生成
if (!isset($_SESSION['token'])) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
} else {
    $token = $_SESSION['token'];
}

// 入力情報をデータベースに登録
if(!empty($_POST) ){
    if(empty($_SESSION['id'])){
        if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
            $hashedPassword = password_hash($_POST['password1'], PASSWORD_DEFAULT);
            $stmt=$db->prepare("INSERT INTO members SET name_sei=?, name_mei=?,  gender=?, pref_name=?, address=?, password=? ,email=?, created_at=NOW(), updated_at=NOW()");
            $stmt->execute(array(
                $_POST['family-name'],
                $_POST['first-name'],
                $_POST['gender'],
                $_POST['prefecture'],
                $_POST['address'],
                $hashedPassword,
                $_POST['email'],
            ));
        }
    }
    if(!empty($_SESSION['id'])){
        $id=$_SESSION['id'];
        if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
            if(!empty($_POST['password1'])){$hashedPassword = password_hash($_POST['password1'], PASSWORD_DEFAULT);}
            $stmt=$db->prepare("UPDATE members SET name_sei=?, name_mei=?,  gender=?, pref_name=?, address= ?, password=(CASE WHEN ? IS NOT NULL THEN ? ELSE password END) ,email=?, updated_at=NOW() WHERE id = ? ");
            $data=[
                $_POST['family-name'],
                $_POST['first-name'],
                $_POST['gender'],
                $_POST['prefecture'],
                $_POST['address'],
                $hashedPassword,
                $hashedPassword,
                $_POST['email'],
                $id,
            ];
            $stmt->execute($data);
        }
    }
    unset($_SESSION['token']);
    unset($_SESSION['family-name']);
    unset($_SESSION['first-name']);
    unset($_SESSION['gender']);
    unset($_SESSION['prefecture']);
    unset($_SESSION['address']);
    unset($_SESSION['password1']);
    unset($_SESSION['password2']);
    unset($_SESSION['email']);

    header('Location: member.php');
    exit();
}

?>
<!DOCTYPE>
<html>
    <head>
      <meta charset="utf-8">
	  <title>確認（管理）</title>
	  <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <main>
                <form action="" method="POST">
                    <div class="dislay-register">

                        <div class="confirm-item">
                        ID
                        <?php if(empty($_SESSION['id'])){echo "登録後に自動採番";}elseif(!empty($_SESSION['id'])){echo $_SESSION['id'];}?>
                        </div> 

                        <div class="confirm-item">
                        氏名
                        <?php echo $_SESSION['family-name'];?>
                        <?php echo $_SESSION['first-name'];?>
                        </div>

                        <div class="confirm-item">
                        性別
                        <?php if($_SESSION['gender']==="1"){echo "男性";}elseif($_SESSION['gender']==="2"){echo "女性";};?>
                        </div>

                        <div class="confirm-item">
                        住所
                        <?php echo $_SESSION['prefecture'];?>
                        <?php echo $_SESSION['address'];?>
                        </div>

                        <div class="confirm-item">
                        パスワード
                        セキュリティのため非表示
                        </div>

                        <div class="confirm-item">
                        メールアドレス
                        <?php echo $_SESSION['email'];?>
                        </div>
                    </div>


                    <!-- 隠しフィールドでデータを持ち越す -->
                    <input type="hidden" name="family-name" value="<?php echo $_SESSION['family-name'];?>">
                    <input type="hidden" name="first-name" value="<?php echo $_SESSION['first-name'];?>">
                    <input type="hidden" name="gender" value="<?php echo $_SESSION['gender'];?>">
                    <input type="hidden" name="prefecture" value="<?php echo $_SESSION['prefecture'];?>">
                    <input type="hidden" name="address" value="<?php echo $_SESSION['address'];?>">
                    <input type="hidden" name="password1" value="<?php echo $_SESSION['password1'];?>">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['email'];?>">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    
                    
                    <input type="submit" class="btn_next" value=<?php if(empty($_SESSION['id'])){echo "登録完了";}elseif(!empty($_SESSION['id'])){echo "編集完了";}?>>
                    
                </form>
                <?php if(empty($_SESSION['id'])):?>
                <div class="btn_back"><a href="member_regist.php"  >前へ戻る</a></div>
                <?php endif;?>
                <?php if(!empty($_SESSION['id'])):?>
                <div  class="btn_back"><a href="member_edit.php?id=<?php echo(int)$_SESSION['id'];?>" >前へ戻る</a></div>
                <?php endif;?>
            </main>
        </div>
    </body>
</html>