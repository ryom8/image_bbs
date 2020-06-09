<?php

    $errorflg = false;
    $info = '';

    if(isset($_POST['time']) == true){
        $time = $_POST['time'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['name']) == true){
        $name = $_POST['name'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['email']) == true){
        $email = $_POST['email'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['title']) == true){
        $title = $_POST['title'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['genre']) == true){
        $genre = $_POST['genre'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['message']) == true){
        $message = $_POST['message'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['imgpath']) == true){
        $imgpath = $_POST['imgpath'];
    }else{
        $errorflg = true;
    }

    if(isset($_POST['pass']) == true){
        $pass = $_POST['pass'];
    }else{
        $errorflg = true;
    }

    if($errorflg == true){
        $info = '書き込みエラー！<br>';
        setcookie('info',$info);
        goto end;
    }

/*

    mb_language('Japanese');
    mb_internal_encoding('UTF-8');

    $mailAdmin = 'hoge@hogehoge.com';
    $mailTitle = '投稿ありがとうございます';

    $mailContent = <<<EOM
{$name}　様
掲示板の書き込みありがとうございます！
記事を削除したい場合は、以下にアクセスして「削除パスワード」を入力してください。

====================

名前        ：　{$name}
タイトル    ：　{$title}
ジャンル    ：　{$genre}
削除パスワード  ：  セキュリティ保護のため表示されません
記事    ：
{$message}

====================

画像UP掲示板

EOM;

*/
    try{
        require_once('./DBInfo.php');
        $pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);
                        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);       
                        
        $sql = 'INSERT INTO bbs SET date = ?, name = ?, email = ?, title = ?, img = ?, category = ?, delpass = ?, message = ?';
        $statement = $pdo->prepare($sql);

        $statement->bindValue(1, $time);
        $statement->bindValue(2, $name);
        $statement->bindValue(3, $email);
        $statement->bindValue(4, $title);
        $statement->bindValue(5, $imgpath);
        $statement->bindValue(6, $genre);
        $statement->bindValue(7, $pass);
        $statement->bindValue(8, $message);

        $pdo->beginTransaction();
        
        $statement->execute();
        
        $pdo->commit();

        setcookie('name',$name,time()+ 60*60*24*7);
        setcookie('email',$email,time()+ 60*60*24*7);
        $info = 'レスの書き込みが完了しました！';
        setcookie('info',$info);

        /*
        // 確認メールの送信
        if(mb_send_mail($email,$mailTitle,$mailContent)){
            $info .= '<br>確認メールを送信しました';
        }else{
            $info .= '<br>確認メールが送信できませんでした';
        }

        */
    
    }catch(PDOException $e){
        if(isset($pdo) == true && $pdo->inTransaction() == true){
            $pdo->rollBack();
            $info = 'データベース読み込みエラー！';
            setcookie('info',$info);
        }
    }

    end:

    header('location:index.php');
