<?php
try {
    $db = new PDO('mysql:dbname=phpkadai;host=localhost;charset=utf8', 'root', 'oK3MoGxi9tMuvSG01');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
}   catch (PDOException $e) {
    echo "データベース接続エラー".$e->getMessage();
}
?>