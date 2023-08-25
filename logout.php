<?php

session_start();
// すべてのセッション変数をアンセットする
$_SESSION = array();
// セッションを破棄する
session_destroy();

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <header>
            <div class="header-list">
                <ul>
                    <li><a href="login.php">ログイン</a></li>
                    <li><a href="member_regist.php">新規会員登録</a></li>
                    <li><a href="thread.php">スレッド一覧</a></li>
                </ul>
            </div>

        </header>
        <main>
        
        </main>
        </div>
    </body>

</html>