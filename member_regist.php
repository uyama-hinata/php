<?php
require("./dbconnect.php");
session_start();
// var_dump($_SESSION);

if(isset($_SESSION['error'])){
	$error=$_SESSION['error'];
	// var_dump($error);
	// die();
} 

// エラーない時は次画面へ
if(!empty($_POST) && !isset($_SESSION['error'])){ 
	 header('Location:confirm.php');
	 exit();
}

?>


<!DOCTYPE html>
<html>
	<head>
      <meta charset="utf-8">
	  <title>会員登録フォーム</title>
	  <link rel="stylesheet" href="stylesheet.css">
	</head>
	<body>
	    <main>
		    <form action ="confirm.php" method ="POST" > 
			    <div class="form-title">会員登録フォーム </div>

			    <div class="form-item"> 

		            <p>氏名
			        <span>性</span>
			        <input type="text" name="family-name" value="<?php if(isset($_SESSION['family-name'])){echo $_SESSION['family-name'];} ?>">
				    <span>名</span>
			        <input type="text" name="first-name" value="<?php if(isset($_SESSION['first-name'])){echo $_SESSION['first-name'];} ?>"> 
                    </p> 

					<!-- 氏名(性)エラー文表示 -->
					<?php if(isset($error['family-name'])&& in_array("blank", $error['family-name'])):?>
					<p class="error">氏名(性)は必須です。</p>
					<?php endif;?>
					<?php if (isset($error['family-name'])&& in_array("length", $error['family-name'])):?>
					<p class="error">氏名(性)は20文字以内でお願いします。</p>
					<?php endif;?>
					

					<!-- 氏名(名)エラー文表示 -->
					<?php if (isset($error['first-name'])&& in_array("blank", $error['first-name'])):?>
					<p class="error">氏名(名)は必須です。</p>
					<?php endif;?>
					<?php if (isset($error['first-name'])&& in_array("length", $error['first-name'])):?>
					<p class="error">氏名(名)は20文字以内でお願いします。</p>
					<?php endif;?>
					

                </div>

				<div class="form-item">性別
					<label><input type="radio" name="gender" value="1" <?php if(!empty($error) && $_SESSION['gender']==="1"){echo 'checked';}?>>男性</label>
					<label><input type="radio" name="gender" value="2" <?php if(!empty($error) && $_SESSION['gender']==="2"){echo 'checked';}?>>女性</label>

					<!-- 性別エラー文表示 -->
					<?php if (isset($error['gender'])&& in_array("blank", $error['gender'])):?>
					<p class="error">性別は必須です。</p>
					<?php endif;?>
					<?php if (isset($error['gender'])&& in_array("correct", $error['gender'])):?>
					<p class="error">性別は 男性 か 女性 で入力してください。</p>
					<?php endif;?>

                </div>

                <div class="form-item"> 
					<p>住所
					<soan>都道府県</span>
					<?php 
					 $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
					?>
					
					<select name="prefecture">
					 <option value=""> 選択してください'</option>
						<?php 
						 foreach($towns as $town){
							if(!empty($error) && ($town===$_SESSION['prefecture'])){echo "<option value='{$_SESSION['prefecture']}' selected>{$town}</option>";}
						  else{echo "<option value='{$town}'>{$town}</option>";}
						 }
					
						?>
                    </select>
					</p>

					<!--住所エラー文表示  -->
					<?php if (isset($error['prefecture'])&& in_array("blank", $error['prefecture'])):?>
					<p class="error">住所は必須です。</p>
					<?php endif;?>
					<?php if (isset($error['prefecture'])&& in_array("correct", $error['prefecture'])):?>
					<p class="error">住所は正しく選択してください。</p>
					<?php endif;?>

				</div>

				<div class="form-item">それ以降の住所
					<input type="text" name="address" maxlength="100" value="<?php if(isset($_SESSION['address'])){echo $_SESSION['address'];} ?>">

					<!--住所エラー文表示 -->
					<?php if (isset($error['address'])&& in_array("length", $error['address'])):?>
					<p class="error">それ以降の住所は100文字以内でお願いします。</p>
					<?php endif;?>

				</div>

			    <div class="form-item">パスワード
			        <input type="password" name="password1" value="<?php if(isset($_SESSION['password1'])){echo $_SESSION['password1'];} ?>" minlength="8" maxlength="20">

					<!--パスワードエラー文表示  -->
					<?php if (isset($error['password1'])&& in_array("blank", $error['password1'])):?>
					<p class="error">パスワードは必須です。</p>
					<?php endif;?>
					<?php if (isset($error['password1'])&& in_array("length", $error['password1'])):?>
					<p class="error">パスワードは8~20文字以内でお願いします。</p>
					<?php endif;?>
					<?php if (isset($error['password1'])&& in_array("correct", $error['password1'])):?>
					<p class="error">パスワードは半角英数字で入力してください。</p>
					<?php endif;?>

				</div>

			    <div class="form-item">パスワード確認
			        <input type="password" name="password2" value="<?php if(isset($_SESSION['password2'])){echo $_SESSION['password2'];} ?>"minlength="8" maxlength="20">

					<!--パスワード確認エラー文表示  -->
					<?php if (isset($error['password2'])&& in_array("blank", $error['password2'])):?>
					<p class="error">パスワード確認は必須です。</p>
					<?php endif;?>
					<?php if (isset($error['password2'])&& in_array("length", $error['password2'])):?>
					<p class="error">パスワード確認は8~20文字以内でお願いします。</p>
					<?php endif;?>
					<?php if (isset($error['password2']) && in_array("difference", $error['password2'])): ?>
			        <p class="error"> パスワードが上記と違います</p>
			        <?php endif; ?>

				</div>

			    <div class="form-item">メールアドレス
			        <input type="text" name="email" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?>" maxlength="200">

					<!--メールアドレスエラー文表示  -->
					<?php if (isset($error['email']) && in_array("blank", $error['email'])): ?>
			        <p class="error">メールアドレスは必須です</p>
			        <?php endif; ?>

					<!-- <?php if (isset($error['email']) && in_array("duplicate", $error['email'])): ?> -->
		            <!-- <p class="error">すでにそのemailは登録されています。</p> -->
			        <!-- <?php endif; ?> -->

					<?php if (isset($error['email'])&& in_array("length", $error['email'])):?>
					<p class="error">メールアドレスは200文字以内でお願いします。</p>
					<?php endif;?>

					<?php if (isset($error['email'])&& in_array("correct", $error['email'])):?>
					<p class="error">メールアドレスは正しい形式で入力してください。</p>
					<?php endif;?>

					<?php if (isset($error['email'])&& in_array("duplicate", $error['email'])):?>
					<p class="error">登録済みのメールアドレスです。</p>
					<?php endif;?>

				</div>

		        <input type="submit" class="btn next" value="確認画面へ" >

	        </form>
			<a href="logout.php" class="btn back" >トップへ戻る</a>
			<?php session_destroy(); ?>
		</main>
	</body>
</html>