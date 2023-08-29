<?php
require("./dbconnect.php");
session_start();

if (!isset($_SESSION['name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: logout.php');
    exit;
}

// ボタン連打による二重登録を防ぐ
// トークンの生成
if (!isset($_SESSION['token'])) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
} else {
    $token = $_SESSION['token'];
}


if(!empty($_POST)){
if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
    $stmt=$db->prepare("INSERT INTO threads SET member_id=?, title=?, content=?,  created_at=NOW(), updated_at=NOW()");
    $stmt->execute(array(
        $_SESSION['id'],
        $_POST['threadtitle'],
        $_POST['comment'],
    ));
    unset($_SESSION['token']);
    unset($_SESSION['threadtitle']);
    unset($_SESSION['comment']);
    
}

header('Location: thread.php');
exit();

}

?>

<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <title>スレッド確認</title>
     <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <main>
                <form action="" method="post">
                    <div class="form-title">スレッド作成確認画面</div>

                    <div  class="confirm-item">
                        <div class="confirm-label">スレッドタイトル</div>
                        <div class="confirm-content"><?php echo nl2br(htmlspecialchars($_SESSION['threadtitle'], ENT_QUOTES, 'UTF-8'));?></div>
                    </div>

                    <div class="confirm-item">
                        <div class="confirm-label">コメント</div>
                        <span class="confirm-content"><?php echo nl2br(htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'));?></span>
                    </div>

                    <!-- 隠しフィールドでデータを持ち越す -->
                    <input type="hidden" name="threadtitle" value="<?php echo $_SESSION['threadtitle'];?>">
                    <input type="hidden" name="comment" value="<?php echo $_SESSION['comment'];?>">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">

                    <input type="submit" class="btn next" value="スレッドを作成する" >

                </form>
                <a href="thread_regist.php"  class="btn back">前へ戻る</a>
            </main>
        </div>
    </body>
</html>