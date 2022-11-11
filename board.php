<?php

session_start();
mb_internal_encoding("utf8");

if(!isset($_SESSION['id'])){
    header("Location:login.php");
}

//変数の初期化
$errors = array();

// POSTアクセス時処理
if($_SERVER["REQUEST_METHOD"]=="POST"){
    //エスケープ処理
    $input["title"] = htmlentities($_POST["title"]??"",ENT_QUOTES);
    $input["comments"] = htmlentities($_POST["comments"]??"",ENT_QUOTES);
    
    //バリデーションチェック
    if(strlen(trim($input["title"]??"")) == 0){ //入力されているかの確認
        $errors["title"] = "タイトルを入力してください";
    }
    //コメントのバリデーション
    if(strlen(trim($input["comments"]?? "")) == 0){ //入力されているかの確認
        $errors["comments"] = "コメントを入力してください";
    }
    //エラーが無ければ、DBに接続し投稿内容を格納
    if(empty($errors)){
        try{
            $pdo = new PDO("mysql:dbname=php_jissen;host=localhost;","root","");//DBに接続
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);//エラーモードを「警告」に設定
            $stmt = $pdo->prepare("INSERT INTO post(user_id,title,comments) VALUES(?,?,?) ");
            $stmt->execute(array($_SESSION["id"],$input["title"],$input["comments"]));
            $pdo = NULL; //DB切断
        } catch(PDOException $e){
            $e->getMessage();//例外発生時にエラーメッセージを出力
        }
    }
}

// アクセス時の処理（DBに接続し、投稿内容を取り出す）
try{
    $pdo = new PDO("mysql:dbname=php_jissen;host=hocalhost;","root","");//DBに接続
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);//エラーモード「警告に」設定
    $posts = $pdo->query("SELECT title,comments,name,posted_at FROM post INNER JOIN user ON post.user_id = user.id ORDER BY posted_at DESC");
    $pdo = NULL; //DB切断
} catch(PDOException $e){
    $e->getMessage(); //例外発生時にエラーメッセージを出力
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>php_jissen</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <header>
        <div class="logo">  
            <div class="logo_left">
                <img src="4eachblog_logo.jpg">
            </div>
            <div class="logo_right">
                <h3>こんにちは<?php echo $_SESSION["name"];?>さん</h3>
                <form action="logout.php">
                    <input type="submit" class="button1" value="ログアウト">
                </form>
            </div>
        </div>
        <ul class="menu">
            <li><a href = "#top">トップ</a></li>
            <li><a href = "#profile">プロフィール</a></li>
            <li>4eachについて</li>
            <li><a href = "#form">登録フォーム</a></li>
            <li><a href = "#inquiry">問い合わせ</a></li>
            <li><a href = "#others">その他</a></li>
        </ul>
    </header>
    <main>
        <div class="board_top">
            <div class="board_left">
                <h1>プログラミングに役立つ掲示板</h1>
                <div class="board_left_bg">
                    <h2 class="form_title">入力フォーム</h2>
                    <form method="POST" action="">
                        <div class="item">
                            <p>タイトル</p>
                            <div class="item1">
                                <input type="text" class="text" size="40" name="title" >
                                <?php if(!empty($errors["title"])):?>
                                <p class="err_message"><?php echo $errors["title"]??'';?></p>
                                <?php endif;?>
                            </div>
                            <div class="item1">
                                <p>コメント</p>
                                <textarea cols="65" rows="10" name="comments">></textarea>
                                <?php if(!empty($errors)): ?>
                                <p class="err_message"><?php echo $errors["comments"]??'';?></p>
                                <?php endif;?>
                            </div>
                            <div class="item1">
                                <input type="submit" class="submit" value="送信する">
                            </div>
                        </div>
                    </form>
                </div> 
                <?php foreach ($posts as $post) : ?>
                    <div class = 'toukou'>
                        <h3><?php echo $post["title"] ?></h3>
                        <div class='contents'><?php echo $post["comments"]?></div>
                        <div class='handlename'>投稿者：<?php echo $post["name"];?></div>
                        <div class='time'>投稿時間：
                            <?php
                                //日付のフォーマットの変更
                                $posted_at = new DateTime($post["psosted_at"]);
                                echo $posted_at->format('Y年m月d日 H:i');
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="board_right">
                <h2>人気の記事</h2>
                <ul>
                    <li>PHPオススメ本</li>
                    <li>PHP MyAdminの使い方</li>
                    <li>今人気のエディタ Top5</li>
                    <li>HTMLの基礎</li>
                </ul>
                <h2>オススメリンク</h2>
                <ul>
                    <li>インターノウス株式会社</li>
                    <li>XAMPPのダウンロード</li>
                    <li>Eclipseのダウンロード</li>
                    <li>Bracketsのダウンロード</li>
                </ul>
                <h2>カテゴリ</h2>
                <ul>
                    <li>HTML</li>
                    <li>PHP</li>
                    <li>MySQL</li>
                    <li>JavaScript</li>
                </ul>
            </div>
        </div>
    </main>
    <footer>copyright © internous | 4each blog the which provides A to Z about programming.</footer>
</body>
</html>