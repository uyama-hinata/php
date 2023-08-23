<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <main>
            <form action="thread_confirm.php" method="post">
             <div class="form-title">スレッド作成フォーム</div>

             <div class="form-item">
                 <span>スレッドタイトル</span>
                 <input type="text" name="thread-title" >
             </div>

             <div  class="form-item">
                 <span>コメント</span>
                 <textarea type="text" name="comment" ></textarea>
             </div>

             <input type="submit" class="btn next" value="確認画面へ">

            </form>
            <a href="top.php"  class="btn back">トップへ戻る</a>
        </main>
    </body>
</html>