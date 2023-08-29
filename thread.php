<?php
require("./dbconnect.php");
session_start();

// SQLを実行してデータを取得
$stmt = $db->query("SELECT * FROM threads ORDER BY created_at DESC");
$threads = $stmt->fetchAll(PDO::FETCH_ASSOC);

$searchKeyword = '';
$results = [];

if (isset($_GET['search'])) {
    $searchKeyword = $_GET['search'];
    if($searchKeyword !==""){
    $stmt = $db->prepare("SELECT * FROM threads WHERE title LIKE :search OR content LIKE :search ORDER BY created_at DESC");
    $stmt->bindParam(':search', $searchValue);
    $stmt->execute([':search' => '%' . $searchKeyword . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
   }else{
    $error['search']='検索ワードを入力してください';
   }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>スレッド一覧</title>
        <link rel="stylesheet" href="stylesheet.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="wrapper">
            <header>

                <?php if(isset($_SESSION['name'])): ?>
                <ul>
                    <li><a href="thread_regist.php">新規スレッド作成</a></li>
                </ul>
                <?php endif;?>

            </header>
            <main>
                <form  method="get">
                <input type="text"  size="40" name="search" placeholder="" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                <input type="submit"  value="スレッド検索">
                </form>
                
                <!-- エラー文表示 -->
                <div class="error"><?php if (isset($_GET['search'])&& $searchKeyword ===""){echo $error['search'];}?></div>

                
                
                
                <div class="table-title">スレッド一覧</div>
                <table border="1">
                    <tbody>

                    <!-- 一覧表示 -->
                    <?php if (!isset($_GET['search']) || $searchKeyword ===""):?>
                        <?php foreach ($threads as $thread): ?>
                            <tr>
                                <td>ID：<?php echo htmlspecialchars($thread['member_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="thread_detail.php?id=<?php echo $thread['id']; ?>"><?php echo htmlspecialchars($thread['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($thread['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- 検索結果表示 -->
                    <?php if (isset($_GET['search']) && $searchKeyword !==""):?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td>ID：<?php echo htmlspecialchars($row['member_id']); ?></td>
                                <td><a href="thread_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    </tbody>
                </table>

                <!-- ログイン時とログアウト時で「トップに戻る」の遷移先を変える -->
                <?php if(isset($_SESSION['name'])): ?>
                <a href="top.php"  class="btn back">トップへ戻る</a>
                <?php endif;?>

                <?php if(!isset($_SESSION['name'])): ?>
                <a href="logout.php"  class="btn back">トップへ戻る</a>
                <?php endif;?>

            
            </main>
        </div>
    </body>

</html>