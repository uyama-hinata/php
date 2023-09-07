<?php 
require("../dbconnect.php");
session_start();

if (empty($_SESSION['admin_name'])) {
    session_destroy();
    // ログインページにリダイレクト
    header('Location: login.php');
    exit;
}

// 「詳細」から飛んできた人のデータを取得
if(!empty($_GET['id'])){
    $id=$_GET['id'];
    
    $sql="SELECT * FROM members WHERE id= :id ";
    $stmt=$db->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    $members=$stmt->fetch();
}

if(isset($_POST['withdrawal'])){
    // ソフトデリート処理
    $stmt = $db->prepare("UPDATE members SET deleted_at = NOW() WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: member.php');
    exit;
}

?>
<!DOCTYPE>
<html>
    <head>
      <meta charset="utf-8">
	  <title>詳細（管理）</title>
	  <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <span class="header-title">会員詳細</span>
                <span class="header-menu">
                    <a href="member.php">一覧へ戻る</a>
                </span>

            </header>
            <main>
                <div class="dislay-register">

                    <div class="detail-item">
                    ID
                    <?php if(!empty($members['id'])){echo $members['id'];}?>
                    </div> 

                    <div class="detail-item">
                    氏名
                    <?php echo $members['name_sei'];?>
                    <?php echo $members['name_mei'];?>
                    </div>

                    <div class="detail-item">
                    性別
                    <?php if($members['gender']=="1"){echo "男性";}elseif($members['gender']=="2"){echo "女性";};?>
                    </div>

                    <div class="detail-item">
                    住所
                    <?php echo $members['pref_name'];?>
                    <?php echo $members['address'];?>
                    </div>

                    <div class="detail-item">
                    パスワード
                    セキュリティのため非表示
                    </div>

                    <div class="detail-item">
                    メールアドレス
                    <?php echo $members['email'];?>
                    </div>
                </div>

                <div  class="toRegist"><a href="member_edit.php?id=<?php echo(int)$members['id'];?>" >編集</a></div>
                <form action="" method="post">
                    <input type="hidden" name="withdrawal" value="ture">
                    <input type="submit" class="btn_next" value="削除">
                </form>
                
            </main>
        </div>
    </body>
</html>