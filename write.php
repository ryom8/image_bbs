<?php
    date_default_timezone_set('Asia/Tokyo');
    $wtime = date('Y-m-d H-i-s');
    $imgnm = date('YmdHis');

    $dir = './image/';
    $name = '';
    $email = '';
    $title = '';
    $genre = '';
    $message = '';
    $pass = '';
    $errorflg = false;

    // 画像最大サイズ (最大2MB)
    $imgMaxbyte = 1000 * 1000 * 2;

    $imgname = '';
    $imgtype = '';
    $imgtemp = $_FILES['image']['tmp_name'];
    $imgerror = false;
    
    //　画像関連の処理
    if($imgtemp != ''){        

        //画像ファイルの容量チェック
        if($_FILES['image']['size'] > $imgMaxbyte){
            $imgerror = true;
        }

        //画像ファイルのアップロードエラーチェック
        if($_FILES['image']['error'] != UPLOAD_ERR_OK){
            $imgerror = true;
        }

        //画像ファイルの形式チェック
        switch($_FILES['image']['type']){
            case 'image/jpeg':
                $imgtype = '.jpg';
                break;
            case 'image/gif':
                $imgtype = '.gif';
                break;
            case 'image/png':
                $imgtype = '.png';
                break;
            default:
                $errorflg = true;
        }

        if($imgerror == false){
            $imgpath = $dir . $imgnm . $imgtype;
            $imgs = $imgnm . $imgtype;

            if(move_uploaded_file($imgtemp,$imgpath) == false){
                $imgerror = true;
            }            
        }        

    }

    if(isset($_POST['name']) == true){
        $name = $_POST['name'];
        $name = htmlspecialchars($name);
    }else{
        $errorflg = true;
    }

    if(isset($_POST['email']) == true){
        $email = $_POST['email'];
        $email = htmlspecialchars($email);
    }else{
        $errorflg = true;
    }

    if(isset($_POST['title']) == true){
        $title = $_POST['title'];
        $title = htmlspecialchars($title);
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
        $message = htmlspecialchars($message);
        $message = str_replace(PHP_EOL, '<br/>',$message);
    }else{
        $errorflg = true;
    }

    if(isset($_POST['pass']) == true){
        $pass = $_POST['pass'];
    }else{
        $errorflg = true;
    }
/*
    function displayImg($files,$id){

        $imginfo = @getimagesize($files);
        $width = $imginfo[0];
        $height = $imginfo[1];
        $proportion = $width / $height;
    
        if($proportion > 1){
            $per = ceil($width / 200);
        }else{
            $per = ceil($height / 200);
        }
        
        $imgd = '<a href="' .$files;
        $imgd.= '" data-lightbox="' .$id;    
        $imgd.= '"><img src="' .$files;
        $imgd.= '" width="' .$width/$per;
        $imgd.= '" height="' .$height/$per;
        $imgd.= '"></a>';
    
        print($imgd);
    }
    */
?>

<!document html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<link href="index.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="css/lightbox.min.css">
    <link href="https://fonts.googleapis.com/css?family=Noto+Serif+JP&display=swap" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="bbs.js"></script>
    <script src="js/lightbox.min.js"></script>

	<title>画像ＵＰ掲示板</title>
</head>
<body>
    <header>
        <h1>画像ＵＰ掲示板</h1>
    </header>

    <div class="center checks">

    <?php

        if($imgerror == true || $errorflg == true){
            echo '<p class="ready">';
            if($imgerror == true)   echo 'アップロードエラー！<br>';
            if($errorflg == true)   echo '書き込みエラー！<br>';
            echo '下記より掲示板に戻ります</p>';
            echo '<input type="button" id="return" class="btn" onClick="history.go(-1);" value="戻る">';
        }else{

            echo <<< EOM

            <div class="imgbbsw">
            <h3>書き込みの確認</h3>
            <table>
                <tr>
                    <td>名前</td>
                    <td>{$name}</td>
                </tr>
                <tr>
                    <td>メールアドレス</td>
                    <td>{$email}<br>※書き込み後、メールが送信されます</td>
                </tr>
                <tr>
                    <td>タイトル</td>
                    <td>{$title}</td>
                </tr>
                <tr>
                    <td>ジャンル</td>
                    <td>{$genre}</td>
                </tr>
                <tr>
                    <td>記事</td>
                    <td>{$message}</td>
                </tr>
                <tr>
                    <td>画像</td>
                    <td>
                    <a href="{$imgpath}" data-lightbox="thumb"><img src="{$imgpath}"></a> 
                    </td>
                </tr>
            </table>
            <p class="ready">この内容で書き込みを行います</p>
            <form action="middle.php" method="POST">
                <input type="hidden" name="time" value="{$wtime}">
                <input type="hidden" name="name" value="{$name}">
                <input type="hidden" name="email" value="{$email}">
                <input type="hidden" name="title" value="{$title}">
                <input type="hidden" name="genre" value="{$genre}">
                <input type="hidden" name="message" value="{$message}">
                <input type="hidden" name="imgpath" value="{$imgs}">
                <input type="hidden" name="pass" value="{$pass}">
                <input type="submit" id="submit" class="btn" value="送信">
                <input type="button" id="return" class="btn" onClick="history.go(-1);" value="戻る">
            </form>
            
        </div>
EOM;
        }
    ?>
       
    </div>

    <footer>
        <p>画像UP掲示板 Ver.0.1</p>
    </footer>
</body>
</html>