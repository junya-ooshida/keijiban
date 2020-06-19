<?php
include('./set.php');

//変数の初期化
$message_id = null;
$mysqli = null;
$now_date=null;
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
}else if( !empty($_POST['message_id']) ) {

	$message_id = (int)htmlspecialchars( $_POST['message_id'], ENT_QUOTES);
	
	if( empty($_POST['view_name']) ) {
		$error_message[] = '表示名を入力してください。';
	} else {
		$message_data['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
	}
	
	if( empty($_POST['message']) ) {
		$error_message[] = 'メッセージを入力してください。';
	} else {
		$message_data['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
	}

	if( empty($error_message) ) {
	
		// データベースに接続
		$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		// 接続エラーの確認
		if( $mysqli->connect_errno ) {
			$error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
		} else {
            
            //文字コードの設定
            $mysqli->set_charset('utf8mb4');
            
            $now_date = date("Y-m-d H:i:s");
            
			$sql = "UPDATE board set view_name = '$message_data[view_name]', message= '$message_data[message]',post_date='$now_date' WHERE id = $message_id";
            
			$res = $mysqli->query($sql);
		}
		
		$mysqli->close();
		
		// 更新に成功したら一覧に戻る
		if( $res ) {
			header("Location: admin.php");
		}
	}
}

?>

<! doctype html>
    <html lang="ja">

    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="style.css">
        <title>掲示板</title>

    </head>

    <body>
        <h1>掲示板</h1>
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

        <form method="post">
            <div>
                <label for="name">名前</label>
                <input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ){ echo $message_data['view_name']; } ?>">
            </div>
            <div>
                <label for="message">投稿内容</label>
                <textarea id="message" name="message"><?php if( !empty($message_data['message']) ){ echo $message_data['message']; } ?></textarea>
            </div>
            <p>
                <a class="btn_cancel" href="admin.php">キャンセル</a>
                <input type="submit" name="post" class="a" value="更新">
                <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
            </p>
        </form>

        <hr>
        <hr>


    </body>

    </html>
