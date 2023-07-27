<!DOCTYPE html>
<html>
	<head>
      <meta charset="utf-8">
	  <title>会員登録フォーム</title>
	  <link rel="stylesheet" href="stylesheet.css">
	</head>
	<body>
       <div class="main">
		    <form action ="sent.php" method ="POST"> 
			    <div class="form-title">会員登録フォーム </div>

			    <div class="form-item"> 
		            <p>氏名
			        <span>性</span>
			        <input type="text" name="family-name" required maxlength="20">
					<?php
                     if(empty($_POST['family_name'])){
                     echo "氏名（性）は必須入力です。";
                     }
                    ?>
				    <span>名</span>
			        <input type="text" name="first-name" required maxlength="20"> 
					</p>
					<?php
                     if(empty($_POST['first_name'])){
                     echo "氏名（名）は必須入力です。";
                     }
                    ?>
                </div>

				<div class="form-item">性別
					<label><input type="radio" name="gender" value="男性" required>男性</label>
					<label><input type="radio" name="gender" value="女性">女性</label>
                </div>

                <div class="form-item"> 
					<p>住所
					<soan>都道府県</span>
					<?php 
					 $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
					?>
					
					<select name="prefecture" required>
					 <option value="">選択してください</option>
						<?php 
						 foreach($towns as $town){
						  echo "<option value='{$town}'>{$town}</option>";
						 }	
						?>
                    </select required>
					</p>
				</div>

				<div class="form-item">それ以降の住所
					<input type="text" name="address" required maxlength="100">
				</div>

			    <div class="form-item">パスワード
			        <input type="password" name="password1" required minlength="8" maxlength="20">
				</div>

			    <div class="form-item">パスワード確認
			        <input type="password" name="password2" required minlength="8" maxlength="20">
				</div>

			    <div class="form-item">メールアドレス
			        <input type="text" name="email" required maxlength="200">
					<?php 
					$email=$_POST['email'];
					if(filter_var($email,FILTER_VALIDATE_EMAIL )){
						echo"$_POST['email']";
					}else{
						echo"";
					}
					?>
				</div>

			        
		        <input type="submit" class="btn next" value="確認画面へ">

	        </form>
           
	   </div>
	</body>
</html>