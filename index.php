<!DOCTYPE html>
<html>
	<head>
      <meta charset="utf-8">
	  <title>会員登録フォーム</title>
	  <link rel="stylesheet" href="stylesheet.css">
	</head>
	<body>
       <div class="main">
		   <div class="register-form">
		       <div class="form-title">会員登録フォーム </div>
			   <form action ="sent.php" method ="post"> 
				
			        <div class="form-item"> 
		            <p>氏名
			        <span>性</span>
			        <input type="text" name="family-name">
				    <span>名</span>
			        <input type="text" name="first-name"> 
					</p>
                    </div>

					<div class="form-item">性別
					<input type="radio" name="gender" value="男性">男性
					<input type="radio" name="gender" value="女性">女性
					</div>

                    <div class="form-item"> 
					<p>住所
					<soan>都道府県</span>
					<?php 
					 $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
					?>
					<select name="prefecture">
						<option value="未選択">選択してください</option>
						<?php 
						 foreach($towns as $town){
						  echo "<option value='{$town}'>{$town}</option>";
						 }	
						?>
                    </select>
					</p>
				    </div>

					<div class="form-item">それ以降の住所
					<input type="text" name="address">
					</div>

			        <div class="form-item">パスワード
			        <input type="password" name="password1">
					</div>

			        <div class="form-item">パスワード確認
			        <input type="password" name="password2">
					</div>

			        <div class="form-item">メールアドレス
			        <input type="text" name="email">
					</div>

			        
		            <input type="submit" class="btn next" value="確認画面へ">

	          </form>
           </div>
	   </div>
	</body>
</html>