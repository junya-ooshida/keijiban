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




if( !empty($_POST['post']) ){

    if( !empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD ) {
		$_SESSION['admin_login'] = true;
	} else {
		$error_message[] = 'ログインに失敗しました。';
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
            
    $sql = "SELECT id,view_name,message,post_date FROM board ORDER BY post_date DESC";
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
        <title>管理ページ</title>
        <link rel="stylesheet" href="style.css">
        <style>
        </style>
    </head>

    <body>
        <div class="centering_parent">
            <div class="centering_item">
                <h1>管理ページ</h1>
                <a href="keijiban.php" class="cp_btn">掲示板</a>

                <?php if(!empty($error_message)): ?>
                <ul class="error_message">
                    <?php foreach($error_message as $value ): ?>
                    <li>·<?php echo $value; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <h4>投稿内容をここに表示</h4>
                <section>
                    <?php if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>

                    <?php if(!empty($message_array)): ?>
                    <?php foreach($message_array as $value): ?>
                    <article>
                        <div class="info">
                            <h2><?php echo $value['view_name']; ?></h2>
                            <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                            <a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>
                            <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a>
                        </div>
                        <p><?php echo nl2br($value['message']); ?></p>
                    </article>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <?php else: ?>
                    <form method="post">
                        <div>

                            <label for="admin_password">ログインパスワード</label>
                            <input id="admin_password" type="password" name="admin_password" value="">
                        </div>
                        <input type="submit" name="post" value="ログイン">
                    </form>
                    <?php endif; ?>

                </section>

            </div>
        </div>
    </body>

    </html>
