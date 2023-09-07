<?php 
require("../dbconnect.php");
session_start();

if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

// 「編集」から飛んできた人のデータを取得
if(!empty($_GET['id'])){
    $id=$_GET['id'];
    
    $sql="SELECT * FROM members WHERE id= :id ";
    $stmt=$db->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    $members=$stmt->fetch();
}

// バリデーション
if(!empty($_POST)){
    // 氏
    if($_POST['family-name']===""){
        $error['family-name-blank']='氏名(性)を入力してください';
    }
    elseif(mb_strlen($_POST['family-name']) > 20 ){
        $error['family-name-length']='20文字以内で入力してください';
    }

    // 名
    if($_POST['first-name']===""){
        $error['first-name-blank']='氏名(名)を入力してください';
    }
    elseif(mb_strlen($_POST['first-name']) > 20 ){
        $error['first-name-length']='20文字以内で入力してください';
    }

    // 性別
    if(!isset($_POST['gender']) || empty($_POST['gender'])){
        $error['gender-blank']='性別を選択してください';
    }
    elseif($_POST['gender']!=="1" && $_POST['gender']!=="2"){
        $error['gender-correct']='性別は 男性 か 女性 で入力してください';
    }

    // 都道府県
    $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
	
    if ($_POST['prefecture']===""){
		$error['prefecture-blank']='都道府県を選択してください';
	}
    elseif(!in_array($_POST['prefecture'],$towns)){
        $error['prefecture-correct']='住所は正しく選択してください';
    }

    //住所　それ以降
	if (mb_strlen($_POST['address']) > 100) {
        $error['address-length'] = '100文字以内で入力してください';
    }


    // パスワード1
    if($_POST['password1']==="" && empty($_GET['id'])){
        $error['password1-blank']='パスワードを入力してください';
    }
    elseif(!empty($_POST['password1']) && (mb_strlen($_POST['password1'])<8 || mb_strlen($_POST['password1']) > 20 )){
        $error['password1-length']='8～20文字以内で入力してください';
    }
    elseif (!empty($_POST['password1']) && !preg_match( '/^[0-9a-zA-Z]+$/', $_POST['password1']) ) {
        $error['password1-format'] = '半角英数字で入力してください';
    }

    // パスワード2
    if($_POST['password2']==="" && empty($_GET['id'])){
        $error['password2-blank']='パスワードを入力してください';
    }
    elseif (($_POST['password1']!= $_POST['password2'])){
        $error['password2-difference'] = '正しく入力してください';
    }
    elseif(!empty($_POST['password2']) && (mb_strlen($_POST['password2'])<8 || mb_strlen($_POST['password2']) > 20 )){
        $error['password2-length']='8～20文字以内で入力してください';
    }
    elseif (!empty($_POST['password2']) && !preg_match( '/^[0-9a-zA-Z]+$/', $_POST['password2']) ) {
        $error['password2-format'] = '半角英数字で入力してください';
    }
    

    // メールアドレス
    // 登録済みメールアドレスではないか
    $sqlEmail=" SELECT email FROM members " ;
	$stmt = $db->prepare($sqlEmail);
	$stmt->execute();
	$allEmailsRow = $stmt->fetchAll();
    $allEmails=array_column($allEmailsRow,'email');

    if (empty($_POST['email'])) {
        $error['email-blank'] = 'メールアドレスを入力してください';
    } 
	elseif (mb_strlen($_POST['email']) > 200) {
        $error['email-length']= '200文字以内で入力してください';
    }
    elseif (!preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $_POST['email']) ) {
        $error['email-correct'] = 'メール形式を正しく入力してください';
    }
    elseif (empty($_GET['id']) && in_array($_POST['email'],$allEmails)) {
		$error['email-duplicate']= 'すでに登録済みのメールアドレスです';
	}
    elseif(!empty($_GET['id']) && !empty($_POST['email'])){
        $email=$members['email'];
        $newemail=$_POST['email'];
        
        // 編集しているメールアドレスを除外
        $index=array_search($email, $allEmails);
        if($index!==false){
            unset($allEmails[$index]);
        }
        if(in_array($newemail, $allEmails)) {
            $error['email-duplicate'] = 'すでに使われているメールアドレスです';
        }
    }

    $_SESSION['family-name']=$_POST['family-name'];
    $_SESSION['first-name']=$_POST['first-name'];
    if(isset($_POST['gender'])){
        $_SESSION['gender']=$_POST['gender'];
    }
    $_SESSION['prefecture']=$_POST['prefecture'];
    $_SESSION['address']=$_POST['address'];
    $_SESSION['password1']=$_POST['password1'];
    $_SESSION['password2']=$_POST['password2'];
    $_SESSION['email']=$_POST['email'];

    if(!isset($error)){
        $_SESSION['id']=$_GET['id'];
        header('Location:confirm.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>会員登録（管理）</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <span class="header-title"><?php if(!empty($_GET['id'])){echo "会員編集";}else{echo "会員登録";}?></span>
                <span class="header-menu">
                    <a href="member.php">一覧へ戻る</a>
                </span>

            </header>
            <main>
                <form action="" method="post">
                    <div class="form-item"> 

                        <p>ID <span><?php if(!empty($_GET['id'])){echo $_GET['id'];}else{echo "登録後に自動採番";}?></span></p>

                        <p>氏名
                            <span>性</span>
                            <input type="text" name="family-name" value="<?php if(isset($_SESSION['family-name'])){echo $_SESSION['family-name'];}elseif(!empty($members['name_sei'])){echo $members['name_sei'];} ?>">
                            <span>名</span>
                            <input type="text" name="first-name" value="<?php if(isset($_SESSION['first-name'])){echo $_SESSION['first-name'];}elseif(!empty($members['name_mei'])){echo $members['name_mei'];} ?>"> 
                        </p> 

                        <!-- 氏名(性)エラー文表示 -->
                        <div class="error">
                            <?php if(!empty($error['family-name-blank'])){echo $error['family-name-blank'];}?>
                            <?php if(!empty($error['family-name-length'])){echo $error['family-name-length'];}?>
                        </div>


                        <!-- 氏名(名)エラー文表示 -->
                        <div class="error">
                            <?php if(!empty($error['first-name-blank'])){echo $error['first-name-blank'];}?>
                            <?php if(!empty($error['first-name-length'])){echo $error['first-name-length'];}?>
                        </div>


                    </div>

                    <div class="form-item">性別
                        <label><input type="radio" name="gender" value="1" <?php if(isset($_SESSION['gender']) && $_SESSION['gender']=="1"){echo 'checked';}elseif(empty($_SESSION['gender']) && (isset($members['gender']) && $members['gender']=="1")){echo 'checked';}?>>男性</label>
                        <label><input type="radio" name="gender" value="2" <?php if(isset($_SESSION['gender']) && $_SESSION['gender']=="2"){echo 'checked';}elseif(empty($_SESSION['gender']) && (isset($members['gender']) && $members['gender']=="2")){echo 'checked';}?>>女性</label>

                        <!-- 性別エラー文表示 -->
                        <div class="error">
                            <?php if(!empty($error['gender-blank'])){echo $error['gender-blank'];}?>
                            <?php if(!empty($error['gender-correct'])){echo $error['gender-correct'];}?>
                        </div>

                    </div>

                    <div class="form-item"> 
                        <p>住所
                        <span>都道府県</span>
                        <?php 
                        $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
                        ?>

                        <select name="prefecture">
                        <option value=""> 選択してください</option>
                            <?php 
                            foreach($towns as $town){
                                if($town===$_SESSION['prefecture']){echo "<option value='{$_SESSION['prefecture']}' selected>{$town}</option>";}
                                elseif(empty($_SESSION['prefecture']) && $town===$members['pref_name']){echo "<option value='{$members['pref_name']}' selected>{$town}</option>";}
                                else{echo "<option value='{$town}'>{$town}</option>";}
                            }
                            
                            ?>
                        </select>
                        </p>

                        <!--住所エラー文表示  -->
                        <div class="error">
                            <?php if(!empty($error['prefecture-blank'])){echo $error['prefecture-blank'];}?>
                            <?php if(!empty($error['prefecture-correct'])){echo $error['prefecture-correct'];}?>
                        </div>

                    </div>

                    <div class="form-item">それ以降の住所
                        <input type="text" name="address" maxlength="100" value="<?php if(isset($_SESSION['address'])){echo $_SESSION['address'];}elseif(!empty($members['address'])){echo $members['address'];} ?>">
                        
                        <!--パスワードエラー文表示  -->
                        <div class="error">
                            <?php if(!empty($error['address-length'])){echo $error['address-length'];}?>
                        </div>

                        </div>

                    <div class="form-item">パスワード
                        <input type="password" name="password1" value="">

                        <!--パスワードエラー文表示  -->
                        <div class="error">
                            <?php if(!empty($error['password1-blank'])){echo $error['password1-blank'];}?>
                            <?php if(!empty($error['password1-length'])){echo $error['password1-length'];}?>
                            <?php if(!empty($error['password1-format'])){echo $error['password1-format'];}?>
                        </div>

                    </div>

                    <div class="form-item">パスワード確認
                        <input type="password" name="password2" value="">

                        <!--パスワード確認エラー文表示  -->
                        <div class="error">
                            <?php if(!empty($error['password2-blank'])){echo $error['password2-blank'];}?>
                            <?php if(!empty($error['password1-length'])){echo $error['password1-length'];}?>
                            <?php if(!empty($error['password1-format'])){echo $error['password1-format'];}?>
                            <?php if(!empty($error['password2-difference'])){echo $error['password2-difference'];}?>
                        </div>

                    </div>

                    <div class="form-item">メールアドレス
                        <input type="text" name="email" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}elseif(!empty($members['email'])){echo $members['email'];} ?>" >

                        <!--メールアドレスエラー文表示  -->
                        <div class="error">
                            <?php if(!empty($error['email-blank'])){echo $error['email-blank'];}?>
                            <?php if(!empty($error['email-length'])){echo $error['email-length'];}?>
                            <?php if(!empty($error['email-correct'])){echo $error['email-correct'];}?>
                            <?php if(!empty($error['email-duplicate'])){echo $error['email-duplicate'];}?>
                            
                        </div>

                    </div>

                    <input type="submit" class="btn_next" value="確認画面へ" >
                    
                </form>
            </main>
        </div>
    </body>

</html>