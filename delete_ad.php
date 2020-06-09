<?php
    $did = '';

    $dir = './image/';
   
    $errorflg = false;
    $info = '';

    if(isset($_POST['did']) == false){
        $errorflg = true;
    }else{
        $did = $_POST['did'];
    }

    // エラー時はCookieにエラーログを書き込む
    if($errorflg == true || $did == ''){
        $info = 'エラー！削除に失敗しました';
        goto end;
    }

//    $id = explode(',',$did);

    try{
        require_once('./DBInfo.php');
        $pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);
                        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // データベースより画像のイメージを取得する
        $sql1 = 'SELECT id,img FROM bbs';
        $sql2 = 'DELETE FROM bbs WHERE';
        $sql3 = 'DELETE FROM res WHERE';

        $st1 = $pdo->prepare($sql1);
        $st1->execute();

        $roops = false;
        $sflg = false;

        while($row = $st1->fetch()){      

            // 該当記事の画像削除とSQL分作成
            //foreach($id as $n){
                foreach($did as $n){
                if($n == $row[0]){
                    $img = $dir.$row[1];

                    // 選択記事が複数あれば、複数選択
                    if($roops == true){
                        $sql2 .= ' OR';
                        $sql3 .= ' OR';
                    }
                    $sql2 .= ' id = ' .$row[0];
                    $sql3 .= ' id = ' .$row[0];
                    $roops = true;

                    // 画像の削除

                    if(!unlink($img)){
                        $info .= '記事' .$row[0]. 'の画像削除に失敗しました<br>';
                    }

                    break;
                }
            }
            $sflg = true;
        }

        // 該当記事・レスの削除
        $pdo->beginTransaction();

        $pdo->exec($sql2);
        $pdo->exec($sql3);

        $pdo->commit();

        $info .= '記事を正常に削除できました';
    
    }catch(PDOException $e){
        if(isset($pdo) == true && $pdo->inTransaction() == true){
            $pdo->rollBack();
            $info = 'データベース読み込みエラー！';
            setcookie('info',$info);
        }
    }
    end:

    echo $info;

