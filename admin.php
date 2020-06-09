<?php

    $dir = './image/';
    $adid = '';
    $email = '';
    $adpass = '';
    $npass = '';
    $myPage = basename($_SERVER['PHP_SELF']);

    $errorflg = false;
    
    if(isset($_POST['aid']) == false){
        $errorflg = true;
    }else{
        $adid = $_POST['aid'];
    }

    if(isset($_POST['apass']) == false){
        $errorflg = true;
    }else{
        $adpass = $_POST['apass'];
    }

    if(isset($_POST['npass']) != false){
        $npass = $_POST['npass'];
    }

    ?>

<!document html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<link href="index.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="css/lightbox.min.css">
    <link href="https://fonts.googleapis.com/css?family=Noto+Serif+JP&display=swap" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="admin.js"></script>
    <script src="js/lightbox.min.js"></script>

	<title>管理者モード</title>
</head>
<body>
    <header>
        <h1>管理者モード</h1>
    </header>

    <div class="center checks">

    <?php

    try{
        require_once('./DBInfo.php');
        $pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);
                        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // パスワードの照合
        $sql0 = 'SELECT * FROM bbs_admin';

        $st0 = $pdo->prepare($sql0);
        $st0->execute();

        if($ro = $st0->fetch()){
            $ro[1] = $email;
        }

        $tflg = false;

        if($ro[0] == $adid && $ro[2] == $adpass){

            if($npass != ''){
                $sql2 = 'UPDATE bbs_admin SET adpass ="' .$npass. '" WHERE adid ="' .$adid. '"';

                $pdo->beginTransaction();
                $pdo->exec($sql2);        
                $pdo->commit();

                echo '<p class="ready">管理者パスワードを変更しました！</p>';
            }

            $sql1 = 'SELECT id,name,title FROM bbs ORDER BY id DESC';

            $statement = $pdo->prepare($sql1);
            $statement->execute();

            while($row = $statement->fetch()){
                if($tflg == false){
                    echo '<table class="ad_list">';
                    echo '<tr>';
                    echo '<td>CHECK</td><td>記事ID</td><td>投稿者名</td><td>タイトル</td></tr>';
                    $tflg = true;
                }
                echo '<tr>';
                echo '<td><input type="checkbox" name="delete" value="' .$row[0]. '"></td>';
                echo '<td>' .$row[0]. '</td>';
                echo '<td>' .$row[1]. '</td>';
                echo '<td><a href="index.php?id=' .$row[0]. '" target="_blank">' .$row[2]. '</a></td>';
                echo '</tr>';
            }

            if($tflg == true){
                echo '</table>';
                echo '<input type="button" id="nocheck" class="btn" value="チェックを外す">';
                echo '<input type="button" id="ad_del" class="btn" value="チェックした記事を削除">';
            }

        }else{
            echo '<p class="ready">管理者IDまたはパスワードが間違っています！</p>';
        }

    }catch(PDOException $e){
        if(isset($pdo) == true && $pdo->inTransaction() == true){
            $pdo->rollBack();
            $info = 'データベース読み込みエラー！';
            setcookie('info',$info);
        }
    }
        
    ?>

    <input type="button" id="return" class="btn" onClick="history.go(-1);" value="戻る">
       
    </div>

    <div id="admin">

<form action="<?php echo $myPage; ?>" method="POST">
    <input type="hidden" name="aid" value="<?php echo $adid; ?>">
    パスワードの変更　　
    現在PASS：<input type="password" name="apass" maxlength="8" required>　
    変更後PASS：<input type="password" name="npass" maxlength="8" required>　
    <input type="submit" value="送信" class="btn2">
</form>

</div>

    <footer>
        <p>画像UP掲示板 Ver.0.1</p>
    </footer>
</body>
</html>