<?php
require("./dbconnect.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    // 氏名(性)
    if ($_POST['family-name']=="") {
        $error['family-name'][] = 'blank';
    }
	elseif (mb_strlen($_POST['family-name']) > 20) {
        $error['family-name'][] = 'length';
    }

    //氏名（名）
	if ($_POST['first-name']==""){
		$error['first-name'][]='blank';
	}
	elseif (mb_strlen($_POST['first-name']) > 20) {
        $error['first-name'][] = 'length';
    }

    // 性別
    if ($_POST['gender']=="") {
        $error['gender'][] = 'blank';
    }
    elseif($_POST['gender']!=="1" && $_POST['gender']!=="2"){
        $error['gender'][]='correct';
    }

	//住所　都道府県　
	if ($_POST['prefecture']==""){
		$error['prefecture'][]='blank';
	}
    
	$towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
	
    if(!in_array($_POST['prefecture'],$towns)){
        $error['prefecture'][]='correct';
    }

	//住所　それ以降
	if (mb_strlen($_POST['address']) > 100) {
        $error['address'][] = 'length';
    }

	//パスワード
	if (empty($_POST['password1'])){
		$error['password1'][]='blank';
	}
	elseif (mb_strlen($_POST['password1'])<8 || mb_strlen($_POST['password1']) > 20) {
        $error['password1'][] = 'length';
    }
    elseif(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['password1'])){
        $error['password1'][]='correct';
    }
    
    //パスワード確認
	if (empty($_POST['password2'])){
		$error['password2'][]='blank';
	}
	elseif (($_POST['password1']!= $_POST['password2'])&&($_POST['password2']!="") ){
        $error['password2'][] = 'difference';
    }

	// メールアドレス
    // 登録済みメールアドレスではないか
	$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
	$member->execute(array($_POST['email']));
	$record = $member->fetch();

    if (empty($_POST['email'])) {
        $error['email'][] = 'blank';
    } 
	elseif (mb_strlen($_POST['email']) > 200) {
        $error['email'] []= 'length';
    }
    elseif (!preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $_POST['email']) ) {
        $error['email'] []= 'correct';
    }
    elseif ($record['cnt'] > 0) {
		$error['email'] []= 'duplicate';
	}


    $_SESSION['family-name']=$_POST['family-name'];
    $_SESSION['first-name']=$_POST['first-name'];
    $_SESSION['gender']=$_POST['gender'];
    $_SESSION['prefecture']=$_POST['prefecture'];
    $_SESSION['address']=$_POST['address'];
    $_SESSION['password1']=$_POST['password1'];
    $_SESSION['password2']=$_POST['password2'];
    $_SESSION['email']=$_POST['email'];

	if (!empty($error)) {
        $_SESSION['error']= $error;
        header('Location: member_regist.php');
        exit();
	}
}

$token = bin2hex(random_bytes(32));
$_SESSION['token'] = $token;

?>


<!DOCTYPE>
<html>
    <head>
      <meta charset="utf-8">
	  <title>会員登録フォーム</title>
	  <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="main">
            <form action="thank.php" method="POST">
              <div class="dislay-register">
                 <div class="form-title">会員情報確認画面</div> 

                 <div class="form-item">氏名
                 <?php echo $_POST['family-name'];?>
                 <?php echo $_POST['first-name'];?>
                 </div>

                 <div class="form-item">性別
                 <?php if($_POST['gender']==="1"){echo "男性";}elseif($_POST['gender']==="2"){echo "女性";};?>
                 </div>

                 <div class="form-item">住所
                 <?php echo $_POST['prefecture'];?>
                 <?php echo $_POST['address'];?>
                 </div>

                 <div class="form-item">パスワード
                 セキュリティのため非表示
                 </div>

                 <div class="form-item">メールアドレス
                 <?php echo $_POST['email'];?>
                 </div>

                </div>

                 <!-- 隠しフィールドでデータを持ち越す -->
                 <input type="hidden" name="family-name" value="<?php echo $_POST['family-name'];?>">
                 <input type="hidden" name="first-name" value="<?php echo $_POST['first-name'];?>">
                 <input type="hidden" name="gender" value="<?php echo $_POST['gender'];?>">
                 <input type="hidden" name="prefecture" value="<?php echo $_POST['prefecture'];?>">
                 <input type="hidden" name="address" value="<?php echo $_POST['address'];?>">
                 <input type="hidden" name="password1" value="<?php echo $_POST['password1'];?>">
                 <input type="hidden" name="email" value="<?php echo $_POST['email'];?>">
                 <input type="hidden" name="token" value="<?php echo $token; ?>">
                

                <input type="submit" class="btn_next" value="登録完了">
                
            </form>
            
            <input type="submit" class="btn_back" value="前へ戻る" onclick=history.back()>

        </div>
    </body>
</html>