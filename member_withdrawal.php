<?php
require("./dbconnect.php");
session_start();

if (!isset($_SESSION['name'])) {
    session_destroy();
    // ログアウトトップページにリダイレクト
    header('Location: logout.php');
    exit;
}


if(isset($_POST['withdrawal'])){
    // ソフトデリート処理
    $stmt = $db->prepare("UPDATE members SET deleted_at = NOW() WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();

    // ログアウト処理
    session_destroy();

    header('Location: logout.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>退会画面</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <ul>
                    <li><a href="top.php">トップへ戻る</a></li>
                </ul>
            </header>
            <main>
                <div class="form-title">退会</div>
                <div class="withdrawal_content">退会しますか？</div>

                <form action="" method="post">
                    <input type="hidden" name="withdrawal" value="ture">
                    <input type="submit" class="btn next" value="退会する">
                </form>

            </main>
        </div>
    </body>
</html>