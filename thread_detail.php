<?php 
require("./dbconnect.php");
session_start();

// クエリパラメータからIDを取得
$id = $_GET['id'];

// スレッド詳細データを取得
$stmt = $db->prepare("SELECT threads.*, CONCAT(members.name_sei,' ',members.name_mei) as member_name FROM threads LEFT JOIN members ON threads.member_id = members.id WHERE threads.id = ?");
$stmt->execute([$id]);
$thread = $stmt->fetch(PDO::FETCH_ASSOC);

// 登録日時を指定の形式に直す
$dateFromDB=$thread['created_at'];
$datetime=new DateTime($dateFromDB);


// ボタン連打による二重登録を防ぐ
// トークンの生成
if (!isset($_SESSION['token'])) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;
} else {
    $token = $_SESSION['token'];
}

// バリデーション
if(!empty($_POST)){
    if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        if($_POST['comment'] === ""){
            $error['comment-blank'] = "コメントを入力してください";
        } elseif(mb_strlen($_POST['comment']) > 500){
            $error['comment-length'] = "500文字以内で入力してください";
        } else {
            // コメントをデータベースに登録
            $stmt = $db->prepare("INSERT INTO comments SET member_id=?, thread_id=?, comment=?, created_at=NOW(), updated_at=NOW()");
            $stmt->execute(array(
                $_SESSION['id'],
                $thread['id'],
                $_POST['comment'],
            ));
        
        $_POST['comment']="";
        
        // 使用したトークンを破棄
        unset($_SESSION['token']);

        // 新しいトークンを再生成
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
       }
    }
}

// まず、コメントの総数を取得
$count_stmt = $db->prepare('SELECT COUNT(*) FROM comments WHERE thread_id = ?');
$count_stmt->execute([$id]);
$comment_count = $count_stmt->fetchColumn();


// 次に、全てのコメントデータを取得
$stmt = $db->prepare('SELECT * FROM comments WHERE thread_id = ?');
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 現在のページ番号を取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) {
    $page = 1;
}

$commentsPerPage = 5;
$offset = ($page - 1) * $commentsPerPage;

// コメントを指定の数だけ取得
$stmt = $db->prepare('SELECT comments.*, CONCAT(members.name_sei,\' \',members.name_mei) as member_name FROM comments LEFT JOIN members ON comments.member_id = members.id WHERE comments.thread_id = ? LIMIT ? OFFSET ?');
$stmt->bindParam(1, $id, PDO::PARAM_INT);
$stmt->bindParam(2, $commentsPerPage, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 各コメントの作成日時を指定の形式に変更
foreach ($comments as $key => $comment) {
    $dateFromDB2 = $comment['created_at'];
    $datetime2 = new DateTime($dateFromDB2);
    $comments[$key]['formatted_date'] = $datetime2->format('Y.m.d H:i');
}

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
            <div class="top-wrapper">
                <!-- スレッドタイトル-->
                <div class="detail-title"><?php echo nl2br(htmlspecialchars($thread['title']));?></div>

                <!-- 総コメント数 -->
                <div class="detail-count"><?php echo $comment_count; ?>コメント</div>
            
                <!-- 作成日時 -->
                <div class="detail-created.at"><?php echo $datetime->format('n/j/y G:i');?></div>
            </div>

            <div class="pagination">
                 <!-- 前へのリンク -->
                 <?php if ($page > 1): ?>
                    <a href="?id=<?php echo $id; ?>&page=<?php echo $page - 1; ?>">＜前へ</a>
                 <?php else: ?>
                    <span class="disabled">＜前へ</span>
                 <?php endif; ?>

                 <!-- 次へのリンク -->
                 <?php if (count($comments) === $commentsPerPage): ?>
                    <a href="?id=<?php echo $id; ?>&page=<?php echo $page + 1;?>" ?>次へ＞</a>
                 <?php else: ?>
                    <span class="disabled">次へ＞</span>
                 <?php endif; ?>
            </div>
            
            <div class="middle-wrapper">
                <!-- スレッド内容 -->
                <div class="detail-content">
                    投稿者：<?php echo htmlspecialchars($thread['member_name']);?> <?php echo $datetime->format('Y.m.d H:i');?><br>
                    <?php echo nl2br(htmlspecialchars($thread['content']));?>
                </div>


                <!-- コメント -->
                <div class="comment-wrapper">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <?php echo $comment['id'];?>.<?php echo htmlspecialchars($comment['member_name']);?> <?php echo htmlspecialchars($comment['formatted_date'], ENT_QUOTES);?><br>
                            <?php echo $comment['comment'];?><br>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>


            <div class="bottm-wrapper">
                <?php if(isset($_SESSION['name'])): ?>
                    <form action="" method="post"> 
                        <div  class="form-item">
                            <textarea type="text" name="comment" ><?php if(!empty($error)){echo$_POST['comment'];} ?></textarea><br>

                            <!-- エラー文表示 -->
                            <div class="error"><?php echo isset($error['comment-blank']) ? $error['comment-blank'] : ''; ?></div>
                            <div class="error"><?php echo isset($error['comment-length']) ? $error['comment-length'] : ''; ?></div>

                        </div>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES); ?>">
                        <input type="submit" class="btn next" value="コメントする">
                    </form>
                <?php endif;?>
            </div>

        </main>
    </body>
</html>