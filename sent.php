<?php
session_start();

if (!empty($_POST)){
    // 氏名(性)
    if ($_POST['family-name']=="") {
        $error['family-name'][] = 'blank';
    }
	elseif (strlen($_POST['family-name']) > 20) {
        $error['family-name'][] = 'length';
    }

    //氏名（名）
	if ($_POST['first-name']==""){
		$error['first-name'][]='blank';
	}
	elseif (strlen($_POST['first-name']) > 20) {
        $error['first-name'][] = 'length';
    }

    // 性別
    if ($_POST['gender']=="") {
        $error['gender'][] = 'blank';
    }

	//住所　都道府県　
	if ($_POST['prefecture']==""){
		$error['prefecture'][]='blank';
	}

	//住所　それ以降
	if (strlen($_POST['address']) > 100) {
        $error['address'][] = 'length';
    }

	//パスワード
	if (empty($_POST['password1'])){
		$error['password1'][]='blank';
	}
	elseif (strlen($_POST['password1'])<8 || strlen($_POST['password1']) > 20) {
        $error['password1'][] = 'length';
    }
    
    //パスワード確認
	if (empty($_POST['password2'])){
		$error['password2'][]='blank';
	}
	elseif (($_POST['password1']!= $_POST['password2'])&&($_POST['password2']!="") ){
        $error['password2'][] = 'difference';
    }

	// メールアドレス
    if (empty($_POST['email'])) {
        $error['email'][] = 'blank';
    // } else {
	    // 登録済みメールアドレスではないか
		// $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		// $member->execute(array($_POST['email']));
		// $record = $member->fetch();
		// if ($record['cnt'] > 0) {
			// $error['email'] = 'duplicate';
	    // }
	}
	elseif (strlen($_POST['email']) > 200) {
        $error['email'] []= 'length';
    }
    elseif (!preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $_POST['email']) ) {
        $error['email'] []= 'correct';
    }


	if (!empty($error)) {
        // var_dump($error);
        $_SESSION['error']= $error;
        $_SESSION['family-name']=$_POST['family-name'];
        $_SESSION['first-name']=$_POST['first-name'];
        $_SESSION['gender']=$_POST['gender'];
        $_SESSION['prefecture']=$_POST['prefecture'];
        $_SESSION['address']=$_POST['address'];
        $_SESSION['password1']=$_POST['password1'];
        $_SESSION['password2']=$_POST['password2'];
        $_SESSION['email']=$_POST['email'];
        // var_dump($_SESSION);
        // die();
        header('Location: member_regist.php');
        exit();
	}


    // history.backで値を引き継げるように
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['form_data'] = $_POST; 
    }
    // フォームのデータを取得
    $form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
    // セッションのフォームデータを削除（必要に応じて）
    unset($_SESSION['form_data']);

}
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
                 <?php echo $_POST['gender'];?>
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
                

                <input type="submit" class="btn_next" value="登録完了">
                
            </form>
            
            <input type="button" class="btn_back" value="前へ戻る" onclick=history.back()>

        </div>
    </body>
</html>