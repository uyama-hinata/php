<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <main>
            <div class="form-title">スレッド作成確認画面</div>

            <div  class="form-item">スレッドタイトル
            <?php echo $_POST['thread-title'];?>
            </div>

            <div class="form-item">コメント
            <?php echo $_POST['comment'];?>
            </div>

            <a href="top.php"  class="btn next">スレッドを作成する</a>
            <a href="thread_regist.php"  class="btn back">前へ戻る</a>
        </main>
    </body>
</html>