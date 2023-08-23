<?php
require("./dbconnect.php");
session_start();
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <header>
            <div class="header-title">ようこそ<?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>様</div>
            <div class="header-list">
                <ul>
                    <li><a href="logout.php">ログアウト</a></li>
                </ul>
            </div>

        </header>
        <main>
        
        </main>
        </div>
    </body>

</html>