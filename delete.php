<?php
    $did = '';
    $dpass = '';
    $img = '';
    $email = '';

    $dir = './image/';
    
    $errorflg = false;
    $info = '';

    if(isset($_POST['did']) == false){
        $errorflg = true;
    }else{
        $did = $_POST['did'];
    }

    if(isset($_POST['dpass']) == false){
        $errorflg = true;
    }else{
        $dpass = $_POST['dpass'];
    }

    if(isset($_POST['img']) == false){
        $errorflg = true;
    }else{
        $img = $_POST['img'];
        $img = $dir.$img;
    }

    // エラー時はCookieにエラーログを書き込む
    if($errorflg == true){
        $info = 'エラー！削除に失敗しました';
        goto end;
    }


    try{
        require_once('./DBInfo.php');
        $pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);
                        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // パスワードの照合
        $sql0 = 'SELECT delpass FROM bbs where id =' .$did;

        $st0 = $pdo->query($sql0);

        foreach($st0 as $val){
            $apass = $val['delpass'];
        }

        if($dpass == $apass){

            $sql2 = 'DELETE FROM bbs WHERE id = ' .$did;
            $sql3 = 'DELETE FROM res WHERE parent = ' .$did;

            $pdo->beginTransaction();

            $pdo->exec($sql2);
            $pdo->exec($sql3);

            $pdo->commit();
            
            // 画像を削除する
            if(!unlink($img)){
                $info .= '画像が削除されませんでした<br>';
            }

            $info .= '記事を正常に削除できました';

        }else{
            $info = 'パスワードが違います！再度入力してください';

        }
    
    }catch(PDOException $e){
        if(isset($pdo) == true && $pdo->inTransaction() == true){
            $pdo->rollBack();
            $info .= 'データベース読み込みエラー！';
            setcookie('info',$info);
        }
    }
    end:

    echo $info;

