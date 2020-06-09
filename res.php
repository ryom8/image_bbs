<?php
    $aid = '';
    $name = '';
    $res = '';
    
    $errorflg = false;
    $info = '';

    if(isset($_POST['aid']) == false){
        $errorflg = true;
    }else{
        $aid = $_POST['aid'];
    }

    if(isset($_POST['name']) == false){
        $errorflg = true;
    }else{
        $name = $_POST['name'];
        $name = htmlspecialchars($name);
    }

    if(isset($_POST['res']) == false){
        $errorflg = true;
    }else{
        $res = $_POST['res'];
        $res = htmlspecialchars($res);
    }

    // エラー時はCookieにエラーログを書き込む
    if($errorflg == true){
        $info = '書き込みエラー！<br>';
        setcookie('info',$info);
        echo 'e';
        goto end;
    }

    try{
        require_once('./DBInfo.php');
        $pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);
                        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);       
                        
        $sql = 'INSERT INTO res SET parent = ?, name = ?, message = ?';
        $statement = $pdo->prepare($sql);

        $statement->bindValue(1, $aid);
        $statement->bindValue(2, $name);
        $statement->bindValue(3, $res);

        $pdo->beginTransaction();
        
        $statement->execute();
        
        $pdo->commit();

        setcookie('name',$name,time()+ 60*60*24*7);
        $info = 'レスの書き込みが完了しました！';
        setcookie('info',$info);

    
    }catch(PDOException $e){
        if(isset($pdo) == true && $pdo->inTransaction() == true){
            $pdo->rollBack();
            $info = 'データベース読み込みエラー！';
            setcookie('info',$info);

        }
    }
    end:

    header('location:index.php');