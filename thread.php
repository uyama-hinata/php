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
        <link rel="stylesheet" href="stylesheet.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>スレッド一覧</title>
    </head>
    <body>
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

            <!-- 検索結果表示 -->
            <div class="table-title"><?php if (isset($_GET['search'])&& $searchKeyword !==""){echo "検索結果";}?></div>
            <table border="1">
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td>ID：<?php echo htmlspecialchars($row['member_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

             <!-- 一覧表示 -->
            <div class="table-title">スレッド一覧</div>
            <table border="1">
                <tbody>
                <?php foreach ($threads as $thread): ?>
                    <tr>
                        <td>ID：<?php echo htmlspecialchars($thread['member_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($thread['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($thread['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
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