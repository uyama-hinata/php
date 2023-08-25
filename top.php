<?php

session_start();
if (!isset($_SESSION['name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: logout.php');
    exit;
}

unset($_SESSION['threadtitle']);
unset($_SESSION['comment']);

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>トップ（ログイン）</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <header>
            <div class="header-title">ようこそ <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?> 様</div>
            <div class="header-list">
                <ul>
                    <li><a href="logout.php">ログアウト</a></li>
                    <li><a href="thread_regist.php">新規スレッド作成</a></li>
                    <li><a href="thread.php">スレッド一覧</a></li>
                </ul>
            </div>

        </header>
        <main>
        
        </main>
        </div>
    </body>

</html>