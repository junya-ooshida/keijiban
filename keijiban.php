<?php
include('./set.php');

//変数の初期化
$now_date = null;
$data =null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

session_start();

if(!empty($_POST['post'])){
    
    //名前の入力チェック
    if(empty($_POST['view_name'])){
        $error_message[] = '名前を入力してください';
    }else{
$clean['view_name'] = htmlspecialchars(
$_POST['view_name'], ENT_QUOTES);
        
        //セッションに名前を保存
        $_SESSION['view_name'] = $clean['view_name'];
        
        $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/','<br>',$clean['view_name']);
    }
    
    //投稿内容のチェック
    if(empty($_POST['message'])){
        $error_message[] = '投稿内容を入力してください';
    }else{
        $clean['message'] = htmlspecialchars($_POST['message'],ENT_QUOTES);
    }

    if(empty($error_message)){
    
    
        
            //データベースに接続
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        //接続エラーの確認
        if( $mysqli->connect_errno ){
            $error_message[] = '書き込みに失敗しました。　エラー番号　'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
        }else{
            
            //文字コードの設定
            $mysqli->set_charset('utf8mb4');
            
            //書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");
            
            //データを登録するSQL作成
            $sql = "INSERT INTO board (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";
            
            //データを登録
            $res = $mysqli->query($sql);
            
            if( $res ){
                $success_message = 'メッセージを書き込みました';
            }else{
                $error_message[] = '書き込みに失敗しました';
            }
            
            //データベースの接続を閉じる
            $mysqli->close();
        }
}
}



// データベースに接続
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
	
     //文字コードの設定
            $mysqli->set_charset('utf8mb4');
            
    $sql = "SELECT view_name,message,post_date FROM board ORDER BY post_date DESC";
    $res = $mysqli->query($sql);
    
    if($res){
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    
    $mysqli->close();
}
?>


<! doctype html>
    <html lang="ja">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>掲示板</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="centering_parent">
            <div class="centering_item">
                <h1>
                    掲示板
                </h1>
                <a href="admin.php" class="cp_btn">管理ページ</a>

                <?php if(!empty($success_message)): ?>
                <p class="success_message">
                    <?php echo $success_message; ?>
                </p>
                <?php endif; ?>

                　　　　　<?php if(!empty($error_message)): ?>
                <ul class="error_message">
                    <?php foreach($error_message as $value ): ?>
                    <li>·<?php echo $value; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <form method="post">
                    <div>
                        <label for="name">名前</label>
                        <input id="view_name" type="text" name="view_name" value="<?php if(!empty($_SESSION['view_name'])){
    echo $_SESSION['view_name']; 
} ?>">
                    </div>
                    <div>
                        <label for="message">投稿内容</label>
                        <textarea id="message" name="message"></textarea>
                    </div>
                    <p>
                        <input type="submit" name="post" value="投稿" class="b">
                    </p>
                </form>

                <hr>
                <hr>


                投稿内容をここに表示
                <section>
                    <?php if(!empty($message_array)): ?>
                    <?php foreach($message_array as $value): ?>
                    <article>
                        <div class="info">
                            <h2><?php echo $value['view_name']; ?></h2>
                            <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                        </div>
                        <p><?php echo nl2br($value['message']); ?></p>
                    </article>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </body>

    </html>
