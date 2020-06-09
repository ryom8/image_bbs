<?php
    $dir = './image/';
    $myPage = basename($_SERVER['PHP_SELF']);
    $threadDisplay = 5;
    $f_info = false;
    $genreList = array("動物","漫画・アニメ","ゲーム","風景","その他");
    $displayList = array("-","2","5","10","20","50");

    $genre = '';
    $search = '';
    $article = 0;
    $sflg = false;
    
    // 名前をCookieから取得
    if(isset($_COOKIE['name'])){
        $name = $_COOKIE['name'];
    }else{
        $name = '';
    }

    // エラー等のコメントを取得
    if(isset($_COOKIE['info'])){
        $info = $_COOKIE['info'];
        $f_info = true;
    }else{
        $info = '';
    }

    // 記事IDを取得（メール等で使用）
    if(isset($_GET['id'])){
        $aid = $_GET['id'];
    }else{
        $aid = 0;
    }
    
    // 表示件数
    if(isset($_GET['display'])){
        $display = $_GET['display'];
    }else{
        $display = 5;
    }

        // ページ数と記事の処理用
    if(isset($_GET['page'])){
        $page = $_GET['page'];
        $pages = ($page - 1) * $display;
    }else{
        $page = 1;
        $pages = 0;

    }

    // ジャンルの読み込み
    if(isset($_GET['genre'])){
        $genre = $_GET['genre'];
        $sflg = true;
    }

    // 検索文字の読み込み
    if(isset($_GET['search'])){
        $search = $_GET['search'];
        $search = htmlspecialchars($search);
        $sflg = true;
    }

	
	if(isset($_POST['searchMessage']) == true){

		$searchMessage = $_POST['searchMessage'];
    }

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

    function displayPages($pg,$m,$dis){
        if($m <= 10){
            $f = true;
        }else{
            $f = false;
        }

        $d = '&display=' .$dis;

        echo '<div class="pages center">';
        echo '<ul>';
            if($f != false){
                for($i=1;$i <= $m;$i++){
                    echo '<li>';
                    if($i == $pg){
                        echo '<b>' .$i. '</b>';
                    }else{
                        echo '<a href="' .$myPage. '?page=' .$i.$d. '">' .$i. '</a>';
                    }
                    echo '</li>';
                }
            }else{
                if($pg-4 < 0){
                    $j = 1;
                }else if($pg +5 > $m){
                    $j = $m - 9;                    
                }
                if($pg-4 >= 0)   echo '<li><a href="' .$myPage. '"?page=1' .$d. '>First</a></li>';
                for($i=0;$i<10;$i++){                    
                    echo '<li>';
                    if($j == $pg){
                        echo '<b>' .$j. '</b>';
                    }else{
                        echo '<a href="' .$myPage. '?page=' .$j.$d. '">' .$j. '</a>';
                    }
                    echo '</li>';                    
                    $j++;
                }
                if($pg +5 < $m)    echo '<li><a href="' .$myPage. '?page=' .$m.$d. '">Last</a></li>';
        }
        echo '</ul>';        
    echo '</div>';

    }

    function escapeString($s){
		return "%" . mb_ereg_replace('([_%#])', '#\1', $s) . "%";
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
    <script type="text/javascript" src="bbs.js"></script>
    <script src="js/lightbox.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

	<title>画像ＵＰ掲示板</title>
</head>
<body>
    <header>
        <h1><a href=" <?php echo $myPage; ?>">画像ＵＰ掲示板</a></h1>
    </header>

    <div class="search center">
        <div class="up">
            <a href="#write">▼アップロードする</a>
        </div>
        <div class="sc">
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
              記事の検索：<input type="text" name="search">
            　<select name="genre" required>
                <?php
                    foreach($genreList as $value){
                        echo '<option value="' .$value. '">' .$value. '</option>';
                    }
                ?>
            </select>
            <input type="submit" value="検索">
           </form>
       </div>
       <div class="nu">
            表示件数：
                <select name="display" class="display" required>
                <?php
                    foreach($displayList as $value){
                        echo '<option value="' .$value. '">' .$value. '</option>';
                    }
                ?>
                </select>件
        </div>
        
    </div>

<?php
    if($sflg == true){
        echo '<div class="search2 center">';
        echo '<p><b>＜検索結果＞　';
        if($genre != '')    echo 'ジャンル：' .$genre. '　';
        if($search != '')   echo 'タイトル名：' .$search. '　';
        echo 'を含む記事を表示</b>';
        echo '</div></p>';
    }

    try{

        $searchf = false;

        require_once('./DBInfo.php');
		$pdo = new PDO(DBInfo::DSN, DBInfo::USER, DBInfo::PASSWORD);					
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL分の作成
        $sql1 = 'SELECT * FROM bbs';

        // 記事数のカウント
        $st2 = $pdo->query($sql1);
        $users = $st2->fetchall();
        
        $article = count($users);
        
        $max = ceil($article / $display);

        displayPages($page,$max,$display);

        // ジャンル選択時、該当する記事を出力
        if($genre != ''){
            $sql1 .= ' WHERE category = "' .$genre. '"';          
        }

        // タイトルで該当する記事を出力
        if($search != ''){

            if($genre != ''){
                $sql1 .= ' AND';
            }
            $sql1 .= ' title';

           $sql1 .= ' LIKE ?';
           $sql1 .= ' ESCAPE "#"';
        }

        // 記事を新しいものから順に表示
        if($aid == 0){
            $sql1 .= ' ORDER BY id DESC LIMIT ' .$pages. ',' .$display;
        }else{
            $sql1 .= ' WHERE id =' .$aid;
        }

        $statement = $pdo->prepare($sql1);

        if($search != ''){
            $statement->bindValue(1, escapeString($search));
        }

        $statement->execute();

        while($row = $statement->fetch()){
            $searchf = true;
            // 記事IDより、レス記事を読み込む
            $articleID = $row[0];
            $resf = false;

            // 画像のパスを作成
            $disimg = $dir . $row[5];

            // 記事の表示
            echo '<div id="thread" class="center">';
            echo '<div class="imgbbs">';
                echo '<h3>【' .$row[6]. '】'.$row[4]. '</h3>';
                echo '<div class="bbs">';
                    echo '<div class="title">' .$row[4]. '　投稿者：' .$row[2]. '　Date：' .$row[1]. '</div>';
                    echo '<div class="del" data-article="' .$articleID. '" data-img="' .$row[5]. '">削除</div>';
                    echo '<div class="bbsimg">';

                    displayImg($disimg,$articleID);
                    
                    echo '</div>';
                    echo '<div class="article">' .$row[8]. '</div>';
                    echo '</div>';


                // レス用記事の読み込み
                $sql2 = 'SELECT * FROM res WHERE parent =' .$articleID;
                $st3 = $pdo->prepare($sql2);
                $st3->execute();

                while($ro = $st3->fetch()){   
                    if($resf == false){                 
                        echo '<div class="re">';
                        echo '<table>';
                        $resf = true;
                    }
                echo '<tr>';
                    echo '<td><span class="bd">' .$ro[2]. '： </span></td>';
                    echo '<td>' .$ro[3]. '</td></tr>';
                }

                if($resf == true){
                    echo '</table></div>';
                }

                echo <<<EOM

                <div class="resw">
                    <form action="res.php" method="POST">
                       <input type="hidden" name="aid" value="{$articleID}">
                        名前：<input type="text" name="name" size="16" value="{$name}" required>
                        　記事：<input type="text" name="res" size="50" maxlength="50" required>
                        　<input type="submit" value="レス" class="btn2">
                    </form>
                </div>
            </div>
        </div>

EOM;

        }

        if($searchf == false){
            echo '<p class="ready">該当記事がありません！</p>';
        }    

    }catch(PDOException $e){
        $code = $e->getCode();
        $message = $e->getMessage();
        print("{$code}/{$message}<br/>");        
    }

    $pdo = null;

    displayPages($page,$max,$display);

    ?>

    <div id="write" class="center">
        <h3>画像のアップロード</h3>
        <form action="write.php" method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <td>名前</td>
                <td><input type="text" name="name" value="<?php echo $name; ?>"required></td>
            </tr>
            <tr>
                <td>メールアドレス</td>
                <td><input type="email" name="email"></td>
            </tr>
            <tr>
                <td>タイトル</td>
                <td><input type="text" name="title" maxlength="20" size="40" required></td>
            </tr>
            <tr>
                <td>ジャンル</td>
                <td>
                    <select name="genre" required>
                    <?php
                    foreach($genreList as $value){
                        echo '<option value="' .$value. '">' .$value. '</option>';
                    }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>画像</td>
                <td><input type="file" name="image" accept="image/jpeg, image/png, image/gif" required><br>
                ※2MB以内、jpg、png、gif形式のみアップロード可</td>
            </tr>
            <tr>
                <td>削除パスワード</td>
                <td><input type="password" name="pass" maxlength="8" required>
                ※8文字以内</td>
            </tr>
            <tr>
                <td>記事</td>
                <td><textarea name="message" cols="50" rows="5" maxlength="250"></textarea><br>
                <input type="submit" value="送信" class="btn2"></td>
            </tr>
        </table>
        </form>
        
    </div>

    <div id="admin">

        <form action="admin.php" method="POST">
            管理者ID：<input type="text" name="aid" size="16" required>　
            PASS：<input type="password" name="apass" maxlength="8" required>　
            <input type="submit" value="送信" class="btn2">
        </form>

    </div>
    <footer>
        <p>画像UP掲示板 Ver.1.0</p>
    </footer>

</body>
</html>