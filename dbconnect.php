<?php
try {
    $db = new PDO('mysql:dbname=phpkadai;host=localhost;charset=utf8', 'root', 'oK3MoGxi9tMuvSG01');
}   catch (PDOException $e) {
    echo "データベース接続エラー".$e->getMessage();
}
?>