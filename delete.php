<?php
include('./set.php');

//変数の初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();
session_start();

if( !empty($_GET['message_id'])  && empty($_POST['message_id'])) {

	$message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);
	
	// データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
	 //文字コードの設定
            $mysqli->set_charset('utf8mb4');
            
		// データの読み込み
		$sql = "SELECT * FROM board WHERE id = $message_id";
		$res = $mysqli->query($sql);
		
		if( $res ) {
			$message_data = $res->fetch_assoc();
		} else {
		
			// データが読み込めなかったら一覧に戻る
			header("Location: ./admin.php");
		}
		
		$mysqli->close();
	}
}elseif( !empty($_POST['message_id']) ) {

	$message_id = (int)htmlspecialchars( $_POST['message_id'], ENT_QUOTES);
	
	// データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
	} else {
         //文字コードの設定
            $mysqli->set_charset('utf8mb4');
            
		$sql = "DELETE FROM board WHERE id = $message_id";
		$res = $mysqli->query($sql);
	}
	
	$mysqli->close();
	
	// 更新に成功したら一覧に戻る
	if( $res ) {
		header("Location: ./admin.php");
	}
	}


?>

<! doctype html>
    <html lang="ja">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="style.css">
        <title>掲示板</title>

    </head>

    <body>
        <h1>管理ページ(投稿の削除)</h1>
        <?php if(!empty($success_message)): ?>
        <p class="success_message"><?php echo $success_message; ?>
        </p>
        <?php endif; ?>

        　　　　　<?php if(!empty($error_message)): ?>
        <ul class="error_message">
            <?php foreach($error_message as $value ): ?>
            <li>·<?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        　　　　　　<p class="text-confirm">以下の投稿を削除します。<br>
            よろしければ「削除」ボタンを押してください。
        </p>
        <form method="post">
            <div>
                <label for="name">名前</label>
                <input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ){ echo $message_data['view_name']; } ?>" disabled>
            </div>
            <div>
                <label for="message">投稿内容</label>
                <textarea id="message" name="message" disabled><?php if( !empty($message_data['message']) ){ echo $message_data['message']; } ?></textarea>
            </div>
            <p>
                <a class="btn_cancel" href="admin.php">キャンセル</a>
                <input type="submit" name="post" class="a" value="削除">
                <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
            </p>
        </form>




        <hr>
        <hr>


    </body>

    </html>