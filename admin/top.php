<?php
session_start();
if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

if(!empty($_POST['logout'])){
    session_start();
    // すべてのセッション変数をアンセットする
    $_SESSION = array();
    // セッションを破棄する
    session_destroy();
    header('Location:login.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>トップ（管理）</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <span class="header-title">掲示板管理画面メインメニュー</span>
                <span class="header-messege">ようこそ <?php echo htmlspecialchars($_SESSION['admin_name'], ENT_QUOTES, 'UTF-8'); ?> 様</span>
                <span class="header-menu">
                    <form action="" method="post">
                        <input type="hidden" name="logout" value="ture">
                        <input type="submit" class="logout_btn" value="ログアウト">
                    </form>
                </span>

            </header>
            <main>
            
            
            </main>
            </div>
        </div>
    </body>

</html>