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

// コメントの総数を取得
$count_stmt = $db->prepare('SELECT COUNT(*) FROM comments WHERE thread_id = ? ');
$count_stmt->execute([$id]);
$comment_count = $count_stmt->fetchColumn();


// 全てのコメントデータを取得
$stmt = $db->prepare('SELECT * FROM comments WHERE thread_id = ? ORDER BY created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 現在のページ番号を取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) {
    $page = 1;
}
// $page = 1 の場合、$offset は (1-1) * 5 = 0 になり、データベースからの取得を最初のレコードから開始することを示す。
// $page = 2 の場合、$offset は (2-1) * 5 = 5 になり、データベースからの取得を6番目のレコードから開始することを示す。
$commentsPerPage = 5;
$offset = ($page - 1) * $commentsPerPage; 

// コメントを指定の数だけ取得&「いいね」の情報を取得
$stmt = $db->prepare('
    SELECT 
        comments.*, 
        CONCAT(members.name_sei,\' \',members.name_mei) as member_name, 
        (CASE WHEN likes.id IS NULL THEN 0 ELSE 1 END) as is_liked
    FROM comments 
    LEFT JOIN members ON comments.member_id = members.id 
    LEFT JOIN likes AS likes ON comments.id = likes.comment_id AND likes.member_id = ?
    WHERE comments.thread_id = ? 
    ORDER BY comments.created_at ASC
    LIMIT ? OFFSET ?
');
$stmt->bindParam(1, $_SESSION['id'], PDO::PARAM_INT);
$stmt->bindParam(2, $id, PDO::PARAM_INT);
$stmt->bindParam(3, $commentsPerPage, PDO::PARAM_INT);
$stmt->bindParam(4, $offset, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 各コメントの作成日時を指定の形式に変更
foreach ($comments as $key => $comment) {
    $dateFromDB2 = $comment['created_at'];
    $datetime2 = new DateTime($dateFromDB2);
    $comments[$key]['formatted_date'] = $datetime2->format('Y.m.d H:i');
}

// いいね機能
if(isset($_POST['likes_id']) && isset($_SESSION['id'])) {
    $likesId = $_POST['likes_id'];
    $memberId = $_SESSION['id'];

    // 既にいいねしているか確認
    $likeCheckStmt = $db->prepare("SELECT * FROM likes WHERE comment_id = ? AND member_id = ?");
    $likeCheckStmt->execute([$likesId, $memberId]);
    $likeExists = $likeCheckStmt->fetch(PDO::FETCH_ASSOC);

    if(!$likeExists) {
        // いいね情報をデータベースに保存
        $likeStmt = $db->prepare("INSERT INTO likes (comment_id, member_id) VALUES (?, ?)");
        $likeStmt->execute([$likesId, $memberId]);
    } else {
        // いいねを取り消す
        $unlikeStmt = $db->prepare("DELETE FROM likes WHERE comment_id = ? AND member_id = ?");
        $unlikeStmt->execute([$likesId, $memberId]);
    }

    exit();
}

// 各コメントに対する「いいね」の数をカウントするための配列を作成
$like_counts = [];

foreach($comments as $comment) {
    $count_liked = $db->prepare('SELECT COUNT(*) FROM likes WHERE comment_id = ?');
    $count_liked->execute([$comment['id']]);
    $like_counts[$comment['id']] = $count_liked->fetchColumn();
}

?>

<!-- いいねがクリックされたときの処理-->
<script>
    // ログイン状態の確認
    const isLoggedIn = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;
    document.addEventListener( "DOMContentLoaded",function() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!isLoggedIn) {
                    window.location.href = "member_regist.php";
                    return;
                }

            const commentId = this.dataset.commentId;
            
            fetch('?', {
                method: 'POST',
                body: new URLSearchParams({ 'likes_id': commentId }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
            })
            .then(response => response.text())
            .then(() => {
                // ここで「いいね」の数を更新するなどの処理を行う
                location.reload();
                });
            });
        });
    });
</script>


<!DOCTYPE html>
<html>
    <head>
     <meta charset="utf-8">
     <title>スレッド詳細</title>
     <link rel="stylesheet" href="stylesheet.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>
    <body>
        <div class="wrapper">
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
                                <?php echo $comment['comment'];?>
                                
                                <!-- いいね機能 -->
                                <button class="like-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                <span class="fa-solid fa-heart <?php echo $comment['is_liked'] ? 'liked' : ''; ?>"></span>
                                <span><?php echo $like_counts[$comment['id']];?></span>
                                </button>
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
        </div>
    </body>
</html>