<?php
require("./dbconnect.php");
session_start();
// 入力情報をデータベースに登録
// 初回時の登録


if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
    $hashedPassword = password_hash($_POST['password1'], PASSWORD_DEFAULT);
    $stmt=$db->prepare("INSERT INTO members SET name_sei=?, name_mei=?, gender=?, pref_name=?, address=?, password=? ,email=?, created_at=NOW(), updated_at=NOW()");
    $stmt->execute(array(
        $_POST['family-name'],
        $_POST['first-name'],
        $_POST['gender'],
        $_POST['prefecture'],
        $_POST['address'],
        $hashedPassword,// ハッシュ化されたパスワードを保存
        $_POST['email'],
    ));
}

unset($_SESSION['token']);

?>


<!DOCTYPE>
<html>
    <head>
      <meta charset="utf-8">
	  <title>会員登録完了</title>
	  <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <main>
            <h1>会員登録完了</h1>
            <p>会員登録が完了しました。</p>
            <a href="top.php" class="btn back">トップへ戻る</a>
            </main>
        </div>
    </body>
</html>