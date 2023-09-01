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
$params = [];
$membersPerPage=10;

if(!empty($_POST['search'])){

    // IDに基づく検索条件
    if(!empty($_POST['search_id'])){
        $conditions[]="id=?";
        $params[]=$_POST['search_id'];
    }

    // 性別に基づく検索条件
    if(!empty($_POST['search_male']) && empty($_POST['search_female'])){
        $conditions[]="gender=1";
    } elseif(!empty($_POST['search_female']) && empty($_POST['search_male'])){
        $conditions[]="gender=2";
    }elseif(!empty($_POST['search_female']) && !empty($_POST['search_male'])){
        $conditions[]="(gender=1 OR gender=2)";
    }

    // 都道府県に基づく検索条件
    if(!empty($_POST['search_prefecture'])){
        $conditions[]="pref_name=?";
        $params[]=$_POST['search_prefecture'];
    }

    // フリーワードに基づく検索条件
    if(!empty($_POST['search_word'])){
        $conditions[]="(name_sei LIKE ? OR name_mei LIKE ? OR email LIKE ? )";
        $searchWord="%". $_POST['search_word'] ."%";
        $params[] = $searchWord;
        $params[] = $searchWord;
        $params[] = $searchWord;
    }

    // ページを移動しても検索条件を保持できるように
    $_SESSION['conditions']=$conditions;
}

if(!empty($_SESSION['conditions'])){
    $conditions=$_SESSION['conditions'];
}


$andconditions=implode(" AND ",$conditions);
    $countSql="SELECT COUNT(*) FROM members WHERE deleted_at IS NULL ";

    if(!empty($conditions)){
        $countSql.=" AND ". $andconditions;
    }

$stmt=$db->prepare($countSql);
$stmt->execute($params);
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

// デフォル表示順序
$orderBy='id';
$order='DESC';

if(!empty($_GET['orderBy'])){
    $orderBy=$_GET['orderBy'];
}
if(!empty($_GET['order'])){
    $order=$_GET['order'];
}

$sqlOrder=" ORDER BY $orderBy $order";


if(!empty($conditions)){
    $sql="SELECT * FROM members WHERE deleted_at IS NULL AND ". $andconditions. $sqlOrder. " LIMIT $membersPerPage  OFFSET $offset ";
    $stmt=$db->prepare($sql);
    $stmt->execute($params);
}else{
    $sql="SELECT * FROM members WHERE deleted_at IS NULL ". $sqlOrder. " LIMIT $membersPerPage  OFFSET $offset ";
    $stmt=$db->prepare($sql);
    $stmt->execute();
}
$members=$stmt->fetchAll(PDO::FETCH_ASSOC);
echo('<pre>');
var_dump($members);
echo('</pre>');

// 登録日時を指定の形式に直す
foreach($members as &$member){
    $timestamp=strtotime($member['created_at']);
    $member['created_at']=date("Y/n/j",$timestamp);
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
                    <tr><th>ID</th><td><input type="text"  name="search_id" value=""></td></tr>
                    <tr><th>性別</th><td><input type="checkbox"  name="search_male"  value="1">男性<input type="checkbox"  name="search_female"  value="2">女性</td></tr>
                    <?php 
						$towns=array('北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県', '石川県', '福井県', '山梨県','長野県','岐阜県','静岡県','愛知県', '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県', '和歌山県','鳥取県','島根県','岡山県', '広島県','山口県', '徳島県','香川県', '愛媛県','高知県', '福岡県','佐賀県', '長崎県', '熊本県', '大分県', '宮崎県','鹿児島県','沖縄県');
					?>
                    <tr><th>都道府県</th><td><select name="search_prefecture" ><option value=""></option>
                        <?php 
						foreach($towns as $town){
							if(!empty($error) && ($town===$_SESSION['prefecture'])){echo "<option value='{$_SESSION['prefecture']}' selected>{$town}</option>";}
						else{echo "<option value='{$town}'>{$town}</option>";}
						}?></select>
                    </td></tr>
                    <tr><th>フリーワード</th><td><input type="text"  name="search_word" value=""></td></tr>
                </table>
                <input type="submit" name="search" class="search_btn" value="検索する">
                </form>

                <table border="2" width="200">
                    <tr>
                        <th>ID <?php if($membersCount>=2 ):?><a href="?orderBy=id&order=<?php if($orderBy==='id' && $order==='DESC'){echo'ASC';}else{echo 'DESC';}?>">▼</a></th><?php endif;?>
                        <th>氏名</th>
                        <th>性別</th>
                        <th>住所</th>
                        <th>登録日時<?php if($membersCount>=2 ):?><a href="?orderBy=created_at&order=<?php if($orderBy==='created_at' && $order==='DESC'){echo 'ASC';}else{echo 'DESC';}?>">▼</a></th><?php endif;?>
                    </tr>

                    <!-- 一覧表示 -->
                    <?php echo('<pre>');
                    var_dump($members);
                    echo('</pre>');?>
                    <?php if (!isset($_POST['search'])):?>
                        
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($member['name_sei'], ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['name_mei'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php if($member['gender']==1){echo "男性";}else{echo "女性";} ?></td>
                                <td><?php echo htmlspecialchars($member['pref_name'],ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['address'],ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $member['created_at'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- 検索結果表示 -->
                    <?php if (isset($_POST['search']) ):?> 
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($member['name_sei'], ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['name_mei'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php if($member['gender']==1){echo "男性";}else{echo "女性";} ?></td>
                                <td><?php echo htmlspecialchars($member['pref_name'],ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['address'],ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $member['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?> 
                    <?php endif; ?> 

                </table>
                
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
                        <a href="?page=<?php echo $page + 1;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>" ?>次へ</a>
                    <?php endif; ?>
                </div>
            
            
            </main>
        </div>
    </body>

</html>