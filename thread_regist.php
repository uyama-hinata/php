<?php 
require("./dbconnect.php");
session_start();

if (!isset($_SESSION['name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: logout.php');
    exit;
}

$threadtitle = '';
$comment = '';
$error= array();


// POST送信があるかないか判定
if (!empty($_POST)) {
    $threadtitle = $_POST['threadtitle'];
    $comment = $_POST['comment'];
    

    // バリデーション
    if ($threadtitle=== '') {
        $error['threadtitle-blank'] = 'スレッドタイトルを入力してください';
    }elseif (mb_strlen($threadtitle) > 101) {
        $error['threadtitle-length']= '100文字以内で入力してください';
    }
    
    if ($comment=== '') {
        $error['comment-blank'] = 'コメントを入力してください';
    }elseif (mb_strlen($comment) > 501) {
        $error['comment-length']= '500文字以内で入力してください';
    }

    if (empty($error)) {
        $_SESSION['threadtitle']=$threadtitle;
        $_SESSION['comment']=$comment;
        header('Location: thread_confirm.php');
        exit();
	}


}
?>
<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <main>
            <form action="" method="post">
             <div class="form-title">スレッド作成フォーム</div>

             <div class="form-item">
                 スレッドタイトル
                 <input type="text" name="threadtitle" value="<?php if(!empty($error)){echo $threadtitle;} elseif(empty($error) && isset($_SESSION['threadtitle'])){echo $_SESSION['threadtitle'];}?>">

                 <!-- エラー文表示 -->
                 <div class="error"><?php echo isset($error['threadtitle-blank']) ? $error['threadtitle-blank'] : ''; ?></div>
                 <div class="error"><?php echo isset($error['threadtitle-length']) ? $error['threadtitle-length'] : ''; ?></div>
             </div>

             <div  class="form-item">
                 コメント
                 <textarea type="text" name="comment" ><?php if(!empty($error)){echo$comment;} elseif(empty($error) && isset($_SESSION['comment'])){echo $_SESSION['comment'];}?></textarea>

                 <!-- エラー文表示 -->
                 <div class="error"><?php echo isset($error['comment-blank']) ? $error['comment-blank'] : ''; ?></div>
                 <div class="error"><?php echo isset($error['comment-length']) ? $error['comment-length'] : ''; ?></div>
             </div>

             <input type="submit" class="btn next" value="確認画面へ">

            </form>
            <a href="top.php"  class="btn back">トップへ戻る</a>
        </main>
    </body>
</html>