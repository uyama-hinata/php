<?php 
require("./dbconnect.php");
session_start();

// クエリパラメータからIDを取得
$id = $_GET['id'];

// 詳細データを取得
$stmt = $db->prepare('SELECT * FROM threads WHERE id = ?');
$stmt->execute([$id]);
$thread = $stmt->fetch();

// 登録日時を指定の形式に直す
$dateFromDB=$thread['created_at'];
$datetime=new DateTime($dateFromDB);

?>
<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <title>スレッド詳細</title>
     <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <header>
            <ul>
                <li><a href="thread.php">スレッド一覧に戻る</a></li>
            </ul>
        </header>
        <main>
            <div class="detail-title"><?php echo nl2br(htmlspecialchars($thread['title']));?></div>
            <div class="detail-created.at"><?php echo $datetime->format('n/j/y G:i');?></div>
            <div class="detail-content"><?php echo nl2br(htmlspecialchars($thread['content']));?></div>

            <?php if(isset($_SESSION['name'])): ?>
                <!-- <form action="" method="get"> -->
                    <div  class="form-item">
                        <textarea type="text" name="comment" ></textarea><br>
                    </div>
                    <input type="submit" class="btn next" value="コメントする">
                <!-- </form> -->
            <?php endif;?>
        </main>
    </body>
</html>