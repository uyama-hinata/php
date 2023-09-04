<?php
require("../dbconnect.php");
session_start();

if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

$conditions = [];
$sql="SELECT * FROM members WHERE deleted_at IS NULL";

if(!empty($_POST['search'])){

    // IDに基づく検索条件
    if(!empty($_POST['search_id'])){
        $conditions['search_id']=$_POST['search_id'];
        $sql.=" AND id= {$conditions['search_id']}";
    }

    // 性別に基づく検索条件
    if(!empty($_POST['search_male']) && empty($_POST['search_female'])){
        $conditions['search_gender']=$_POST['search_male'];
        $sql.=" AND gender={$conditions['search_gender']}";
    }
    elseif(empty($_POST['search_male']) && !empty($_POST['search_female'])){
        $conditions['search_gender']=$_POST['search_female'];
        $sql.=" AND gender={$conditions['search_gender']}";
    } 

    // 都道府県に基づく検索条件
    if(!empty($_POST['search_prefecture'])){
         $conditions['search_prefecture']=$_POST['search_prefecture'];
         $sql.=" AND pref_name='{$conditions['search_prefecture']}'";
    }

     // フリーワードに基づく検索条件
     if(!empty($_POST['search_word'])){
         $conditions['search_word']=$_POST['search_word'];
         $searchWord='%'.$conditions['search_word'].'%';
         $sql.= " AND (name_sei like (:name_sei) )";
    }

    //  ページを移動しても検索条件を保持できるように
     $_SESSION['conditions']=$conditions;
}

if(!empty($_SESSION['conditions'])){
    $conditions=$_SESSION['conditions'];
}

if(!empty($searchWord)){
    $stmt=$db->prepare($sql);
    var_dump($sql);
    var_dump($searchWord);
    $stmt->bindValue(':name_sei','%uyama%',PDO::PARAM_STR);
    // $stmt->bindParam(':name_mei',$searchWord,PDO::PARAM_STR);
    // $stmt->bindParam(':email',$searchWord,PDO::PARAM_STR);
    var_dump($sql);
    var_dump($searchWord);
    $stmt->execute();
    $members=$stmt->fetchAll();
}

$stmt=$db->prepare($sql);
$stmt->execute();
$members=$stmt->fetchAll();

$orderBy='id';
$order='DESC';

if(!empty($_POST['orderby'])&&!empty($_POST['order'])){
    $orderBy=$_POST['orderby'];
    $order=$_POST['order'];
}

$sqlOrder=" ORDER BY $orderBy $order";
$sql.=$sqlOrder;
$stmt=$db->prepare($sql);
$stmt->execute();
$members=$stmt->fetchAll();


$countSql="SELECT COUNT(*) FROM members WHERE deleted_at IS NULL ";
$stmt=$db->prepare($countSql);
$stmt->execute();

$membersPerPage=10;
$membersCount=$stmt->fetchColumn();
$totalPages = ceil($membersCount / $membersPerPage);

// 現在のページ番号を取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) {
    $page = 1;
}
// $page = 1 の場合、$offset は (1-1) * 10 = 0 になり、データベースからの取得を最初から開始することを示す。
// $page = 2 の場合、$offset は (2-1) * 10 = 10 になり、データベースからの取得を11番目から開始することを示す。
$offset = ($page - 1) * $membersPerPage; 

$sql.=" LIMIT $membersPerPage OFFSET $offset";
var_dump($sql);
$stmt=$db->prepare($sql);
$stmt->execute();
$members=$stmt->fetchAll();


// 登録日時を指定の形式に直す
foreach($members as $key=>$member){
    $dateFromDB=$member['created_at'];
    $datetime=new Datetime($dateFromDB);
    $members[$key]['created_at']=$datetime->format('Y/n/j');
}

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>会員一覧（管理）</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <span class="header-title">会員一覧</span>
                <span class="header-menu">
                    <a href="top.php">トップへ戻る</a>
                </span>
            </header>

            <main>
                <form  action="" method="post">
                    <table border="2" width="200">
                        <tr>
                            <th>ID</th>
                            <td><input type="text"  name="serach_id" value="<?php if(!empty ($conditions['search_id'])){echo $conditions['search_id'];} ?>"></td>
                        </tr>
                        <tr>
                            <th>性別</th>
                            <td>
                                <input type="checkbox"  name="search_male"  value="1" <?php if(!empty($conditions['search_male'])){echo 'checked';}?>>男性
                                <input type="checkbox"  name="search_female"  value="2" <?php if(!empty($conditions['search_female'])){echo 'checked';}?>>女性
                            </td>
                        </tr>
                        <?php 
                            $towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
                        ?>
                        <tr>
                            <th>都道府県</th>
                            <td>
                                <select name="search_prefecture" >
                                    <option value=""></option>
                                    <?php 
                                    foreach($towns as $town){
                                        if(!empty($error) && ($town===$conditions['search_prefecture'])){echo "<option value='{$conditions['search_prefecture']}' selected>{$town}</option>";}
                                        else{echo "<option value='{$town}'>{$town}</option>";}
                                    }?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>フリーワード</th>
                            <td><input type="text"  name="search_word" value="<?php if(!empty($conditions['search_word'])){echo $conditions['search_word'];} ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" name="search" class="search_btn" value="検索する">
                </form>

                <form  action="" method="post">
                    <table border="2" width="200">
                        <tr>
                            <th>
                                ID 
                                <?php if (($orderBy==='id' && $order==='ASC') || ($orderBy==='created_at' && $order==='ASC')):?>
                                <input type="hidden" name="order" value="DESC">
                                <button type="submit" name="orderby" class="switch_btn" value="id">▼</button>
                                <?php else: ?>
                                <input type="hidden" name="order" value="ASC">
                                <button type="submit" name="orderby" class="switch_btn" value="id">▲</button>
                                <?php endif;?>
                            </th>
                            <th>氏名</th>
                            <th>性別</th>
                            <th>住所</th>
                            <th>
                                登録日時
                                <?php if (($orderBy==='id' && $order==='ASC') || ($orderBy==='created_at' && $order==='ASC')):?>
                                <input type="hidden" name="order" value="DESC">
                                <button type="submit" name="orderby" class="switch_btn" value="created_at">▼</button>
                                <?php else: ?>
                                <input type="hidden" name="order" value="ASC">
                                <button type="submit" name="orderby" class="switch_btn" value="created_at">▲</button>
                                <?php endif;?>
                            </th>
                        </tr>

                        <!-- 一覧表示と検索結果表示 --> 
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($member['name_sei'], ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['name_mei'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php if($member['gender']==1){echo "男性";}else{echo "女性";} ?></td>
                                <td><?php echo htmlspecialchars($member['pref_name'],ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['address'],ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $member['created_at'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </form>
                

                <div class="pagination">
                    <!-- 前へのリンク -->
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>">前へ</a>
                    <?php endif; ?>

                    <?php
                    $startPage=max(1,$page-1);
                    $endPage=min($totalPages,$page+1);
                    for($i=$startPage;$i<=$endPage;$i++):
                    ?>
                        <?php if($i==$page):?>
                            <span class="current_page"><?php echo $i;?></span>
                        <?php else:?>
                            <a href="?page=<?php echo $i;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>"><?php echo $i;?></a>
                        <?php endif;?>
                    <?php endfor; ?>

                    <!-- 次へのリンク -->
                    <?php if ($page<$totalPages && count($members) === $membersPerPage): ?>
                        <a href="?page=<?php echo $page + 1;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>">次へ</a>
                    <?php endif; ?>
                </div>
            
            
            </main>
        </div>
    </body>
</html>