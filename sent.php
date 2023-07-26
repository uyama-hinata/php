<!DOCTYPE>
<html>
    <head>
      <meta charset="utf-8">
	  <title>会員登録フォーム</title>
	  <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="main">
            <form action="thank.php" method="post">
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
                 <?php echo 'セキュリティのため非表示';?>
                 </div>

                 <div class="form-item">メールアドレス
                 <?php echo $_POST['email'];?>
                 </div>

                </div>
                
                <input type="submit" class="btn next" value="登録完了">
                
            </form>
            
            <button type="button" class="btn back" onclick=history.back()>前に戻る</button>

        </div>
    </body>
</html>