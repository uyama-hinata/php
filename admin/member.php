<?php
require("../dbconnect.php");
session_start();

if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

    unset($_SESSION['family-name']);
    unset($_SESSION['first-name']);
    unset($_SESSION['gender']);
    unset($_SESSION['prefecture']);
    unset($_SESSION['address']);
    unset($_SESSION['password1']);
    unset($_SESSION['password2']);
    unset($_SESSION['email']);


if(!empty($_POST['search_id'])){
    $_SESSION['search_id']=$_POST['search_id'];
}
if(!empty($_POST['search_male'])){
    $_SESSION['search_male']=$_POST['search_male'];
}
if(!empty($_POST['search_female'])){
    $_SESSION['search_female']=$_POST['search_female'];
}
if(!empty($_POST['search_prefecture'])){
    $_SESSION['search_prefecture']=$_POST['search_prefecture'];
}
if(!empty($_POST['search_word'])){
    $_SESSION['search_word']=$_POST['search_word'];
}

if(!empty($_POST['search']) && empty($_POST['search_id'])){
    unset($_SESSION['search_id']);
}
if(!empty($_POST['search']) && empty($_POST['search_male'])){
    unset($_SESSION['search_male']);
}
if(!empty($_POST['search']) && empty($_POST['search_female'])){
    unset($_SESSION['search_female']);
}
if(!empty($_POST['search']) && empty($_POST['search_prefecture'])){
    unset($_SESSION['search_prefecture']);
}
if(!empty($_POST['search']) && empty($_POST['search_word'])){
    unset($_SESSION['search_word']);
}

$conditions = [];
$orderBy='id';
$order='DESC';
$sql="SELECT * FROM members WHERE deleted_at IS NULL";

// ▼▲を押したときに検索条件を保持できるように
if(!empty($_SESSION['search_id'])){
    $sql.=" AND id= {$_SESSION['search_id']}";
}
if(!empty($_SESSION['search_male']) && empty($_SESSION['search_female'])){
    $sql.=" AND gender= {$_SESSION['search_male']}";
}
if(!empty($_SESSION['search_female']) && empty($_SESSION['search_male'])){
    $sql.=" AND gender= {$_SESSION['search_female']}";
}
if(!empty($_SESSION['search_prefecture'])){
    $sql.=" AND pref_name= '{$_SESSION['search_prefecture']}'";
}
if(!empty($_SESSION['search_word'])){
    $searchWord='%'.$_SESSION['search_word'].'%';
    $sql.= " AND (name_sei like (:name_sei) OR name_mei like (:name_mei) OR email like (:email))";
}


// IDに基づく検索条件
if(!empty($_POST['search_id'])){
    $conditions['search_id']=$_POST['search_id'];
    $sql.=" AND id= {$conditions['search_id']}";
}

// 性別に基づく検索条件
if(!empty($_POST['search_male']) && empty($_POST['search_female'])){
    $conditions['search_male']=$_POST['search_male'];
    $sql.=" AND gender={$conditions['search_male']}";
}
elseif(empty($_POST['search_male']) && !empty($_POST['search_female'])){
    $conditions['search_female']=$_POST['search_female'];
    $sql.=" AND gender={$conditions['search_female']}";
}
elseif(!empty($_POST['search_male']) && !empty($_POST['search_female'])){
    $conditions['search_male']=$_POST['search_male'];
    $conditions['search_female']=$_POST['search_female'];
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
    $sql.= " AND (name_sei like (:name_sei) OR name_mei like (:name_mei) OR email like (:email))";
}

if(!empty($_POST['orderby'])&&!empty($_POST['order'])){
    $_SESSION['orderby']=$_POST['orderby'];
    $_SESSION['order']=$_POST['order'];
    $orderBy=$_POST['orderby'];
    $order=$_POST['order'];
}
if(!empty($_SESSION['orderby'])&&!empty($_SESSION['order'])){
    $orderBy=$_SESSION['orderby'];
    $order=$_SESSION['order'];
}



$sql.=" ORDER BY $orderBy $order";

$membersPerPage=10;

// 現在のページ番号を取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) {
    $page = 1;
}
// $page = 1 の場合、$offset は (1-1) * 10 = 0 になり、データベースからの取得を最初から開始することを示す。
// $page = 2 の場合、$offset は (2-1) * 10 = 10 になり、データベースからの取得を11番目から開始することを示す。
$offset = ($page - 1) * $membersPerPage; 

$sql.=" LIMIT $membersPerPage OFFSET $offset";

$stmt=$db->prepare($sql);
if(!empty($_POST['search_word']) || !empty($_SESSION['search_word'])){
    $stmt->bindValue(':name_sei',$searchWord,PDO::PARAM_STR);
    $stmt->bindParam(':name_mei',$searchWord,PDO::PARAM_STR);
    $stmt->bindParam(':email',$searchWord,PDO::PARAM_STR);
}
$stmt->execute();
$members=$stmt->fetchAll();

$result=str_replace(" LIMIT $membersPerPage OFFSET $offset","",$sql);
$stmt = $db->prepare($result);
$stmt->execute();
$count = $stmt -> rowCount();

$totalPages = ceil($count / $membersPerPage);


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
                <div class="toRegist"><a href="member_regist.php">会員登録</a></div>
                <form  action="" method="post">
                    <table border="2" width="200">
                        <tr>
                            <th>ID</th>
                            <td><input type="text"  name="search_id" value="<?php if(!empty ($conditions['search_id'])){echo $conditions['search_id'];}elseif(!empty ($_SESSION['search_id'])){echo $_SESSION['search_id'];} ?>"></td>
                        </tr>
                        <tr>
                            <th>性別</th>
                            <td>
                                <input type="checkbox"  name="search_male"  value="1" <?php if(!empty($conditions['search_male'])&&$conditions['search_male']==='1'){echo 'checked';}elseif(!empty($_SESSION['search_male'])&&$_SESSION['search_male']==='1'){echo 'checked';}?>>男性
                                <input type="checkbox"  name="search_female"  value="2" <?php if(!empty($conditions['search_female'])&&$conditions['search_female']==='2'){echo 'checked';}elseif(!empty($_SESSION['search_female'])&&$_SESSION['search_female']==='2'){echo 'checked';}?>>女性
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
                                        if($town===$conditions['search_prefecture']){echo "<option value='{$conditions['search_prefecture']}' selected>{$town}</option>";}
                                        elseif($town===$_SESSION['search_prefecture']){echo "<option value='{$_SESSION['search_prefecture']}' selected>{$town}</option>";}
                                        else{echo "<option value='{$town}'>{$town}</option>";}
                                    }?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>フリーワード</th>
                            <td><input type="text"  name="search_word" value="<?php if(!empty($conditions['search_word'])){echo $conditions['search_word'];}elseif(!empty ($_SESSION['search_word'])){echo $_SESSION['search_word'];} ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" name="search" class="search_btn" value="検索する">
                </form>

                <form  action="" method="post">
                    <table border="2" width="200">
                        <tr>
                            <th>
                                ID 
                                <?php if($count>=2):?>
                                    <?php if (($orderBy==='id' && $order==='ASC') || ($orderBy==='created_at' && $order==='ASC')):?>
                                    <input type="hidden" name="order" value="DESC">
                                    <button type="submit" name="orderby" class="switch_btn" value="id">▼</button>
                                    <?php else: ?>
                                    <input type="hidden" name="order" value="ASC">
                                    <button type="submit" name="orderby" class="switch_btn" value="id">▲</button>
                                    <?php endif;?>
                                <?php endif;?>
                            </th>
                            <th>氏名</th>
                            <th>性別</th>
                            <th>住所</th>
                            <th>
                                登録日時
                                <?php if($count>=2):?>
                                    <?php if (($orderBy==='id' && $order==='ASC') || ($orderBy==='created_at' && $order==='ASC')):?>
                                    <input type="hidden" name="order" value="DESC">
                                    <input type="hidden" name="search" value="">
                                    <button type="submit" name="orderby" class="switch_btn" value="created_at">▼</button>
                                    <?php else: ?>
                                    <input type="hidden" name="order" value="ASC">
                                    <input type="hidden" name="search" value="">
                                    <button type="submit" name="orderby" class="switch_btn" value="created_at">▲</button>
                                    <?php endif;?>
                                <?php endif;?>
                            </th>
                            <th>編集</th>
                        </tr>

                        <!-- 一覧表示と検索結果表示 --> 
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($member['name_sei'], ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['name_mei'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php if($member['gender']==1){echo "男性";}else{echo "女性";} ?></td>
                                <td><?php echo htmlspecialchars($member['pref_name'],ENT_QUOTES, 'UTF-8'); ?><?php echo htmlspecialchars($member['address'],ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $member['created_at'];?></td>
                                <td><a href="member_edit.php?id=<?php echo(int)$member['id'];?>">編集</a></td>
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
                    if ($count <= 10) {
                        $endPage = 1;
                    }
                    for($i=$startPage;$i<=$endPage;$i++):
                    ?>
                        <?php if($i==$page):?>
                            <span class="current_page"><?php echo $i;?></span>
                        <?php else:?>
                            <a href="?page=<?php echo $i;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>"><?php echo $i;?></a>
                        <?php endif;?>
                    <?php endfor; ?>

                    <!-- 次へのリンク -->
                    <?php if ($page<$totalPages): ?>
                        <a href="?page=<?php echo $page + 1;?>&orderBy=<?php echo $orderBy;?>&order=<?php echo $order;?>">次へ</a>
                    <?php endif; ?>
                </div>
            
            
            </main>
        </div>
    </body>
</html>