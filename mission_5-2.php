<?php
    // DB接続設定
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-******';
	$password = 'Password';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//table作成
	$sql = "CREATE TABLE IF NOT EXISTS mission_5"
	." ("
	. "tb_id INT AUTO_INCREMENT PRIMARY KEY,"
	. "tb_name char(32),"
	. "tb_comment TEXT,"
    . "tb_password TEXT,"
    . "tb_date datetime"
	.");";
	$stmt = $pdo->query($sql);
?>

<?php
    //新規投稿機能
    //各種定義
    $tb_date = date("Y-m-d H:i:s");
    if (!empty($_POST['name'])&&($_POST['comment'])&&($_POST['pass-a'])){
        if (empty($_POST['secret'])){
            $tb_name = $_POST['name'];
            $tb_comment = $_POST['comment'];
            $tb_password = $_POST['pass-a'];
            echo "新規投稿しました<br>";
            //INSERT文：データを入力（データレコードの挿入）
            $sql = $pdo -> prepare("INSERT INTO mission_5 (tb_name, tb_comment, tb_password, tb_date) 
            VALUES (:tb_name, :tb_comment, :tb_password, :tb_date)");
        	$sql -> bindParam(':tb_name', $tb_name, PDO::PARAM_STR);
        	$sql -> bindParam(':tb_comment', $tb_comment, PDO::PARAM_STR);
            $sql -> bindParam(':tb_password', $tb_password, PDO::PARAM_STR);
            $sql -> bindParam(':tb_date', $tb_date, PDO::PARAM_STR);
        	$sql -> execute();
        }
    }else{
        echo "名前、コメント、パスワードを全て入力してください<br>";
    }
        
    //削除機能
    if(!empty($_POST["number"])
    && ($pass_b = $_POST['pass-b'])){
        //パスワードを取得
        $sql = 'SELECT * FROM mission_5';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){};
        if($pass_b == $row['tb_password']){
            $number = $_POST['number'];
            echo $number;
            echo "を削除<br>";
            $tb_id = $number;
            $sql = 'delete from mission_5 where tb_id=:tb_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tb_id', $tb_id, PDO::PARAM_INT);
            $stmt->execute(); 
        }else{
            echo "パスワードが違います";
        }
    }

    //編集機能   
    if (!empty($_POST['secret'])&&($pass_a = $_POST['pass-a'])&&($_POST['submit'])){
        //パスワードを取得
        $sql = 'SELECT * FROM mission_5';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){};
        if($pass_a == $row['tb_password']){
            $secret = $_POST['secret'];
            echo $secret."番を編集しました<br>";
            //データを抽出
            $sql = 'SELECT * FROM mission_5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //データを編集
            $tb_id = $secret;
            $tb_name = $_POST['name'];
            $tb_comment = $_POST['comment'];
            $tb_password = $_POST['pass-a'];
            $tb_date = date("Y-m-d H:i:s");
            $sql = 'UPDATE mission_5 SET tb_name=:tb_name,
            tb_comment=:tb_comment, tb_password=:tb_password, tb_date=:tb_date
            WHERE tb_id=:tb_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':tb_name', $tb_name, PDO::PARAM_STR);
            $stmt->bindParam(':tb_comment', $tb_comment, PDO::PARAM_STR);
            $stmt->bindParam(':tb_password', $tb_password, PDO::PARAM_STR);
            $stmt->bindParam(':tb_date', $tb_date, PDO::PARAM_STR);
            $stmt->bindParam(':tb_id', $tb_id, PDO::PARAM_INT);
            $stmt->execute();
        }else{
            echo "パスワードが違います";
        } 
    }
        
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <form action = "" method = "post">
        <!--名前とコメント入力欄-->
        <input type = "text" name = "name"
        placeholder = "名前"
        value = "<?php
        //編集するとき
        if (!empty($_POST['edit_number'])
        && ($pass_c = $_POST['pass-c'])){
            $edit_number = $_POST['edit_number'];
            //データコードを抽出
            $sql = 'SELECT * FROM mission_5';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        //名前を入力フォームに表示
	        foreach ($results as $row){
	            if ($row[0] == $edit_number && $row[3] == $pass_c){
                    echo $row[1];
	            }
            }
        } ?>"><br>
        
        <input type = "text" name = "comment"
        placeholder = "コメント"
        value = "<?php
        //編集するとき
        if (!empty($_POST['edit_number'])
        && ($pass_c = $_POST['pass-c'])){
            //データコードを抽出
            $sql = 'SELECT * FROM mission_5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //コメントを入力フォームに表示
            foreach ($results as $row){
                if ($row[0] == $edit_number && $row[3] == $pass_c){
                    echo $row[2];
                }
            }
        } ?>"><br>
        
        <input type = "text" name = "pass-a"
        placeholder = "パスワード"><br>
        
        <!--編集したい番号入力欄(後で隠す)-->
        <input type = "hidden" name = "secret"
        value = "<?php
        //編集するとき
        if (!empty($_POST["edit_number"])
        && ($pass_c = $_POST['pass-c'])){
            //データコードを抽出
            $sql = 'SELECT * FROM mission_5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //シークレットフォームに投稿番号を入力
            foreach ($results as $row){
                if ($row[0] == $edit_number  && $row[3] == $pass_c){
                    echo $row[0];
                }
            }
        } ?>">
        
        <input type = "submit" name = "submit"><br><br>
        <!--削除欄-->
        <input type = "number" name = "number"
        placeholder = "削除対象番号"><br>
        <input type = "text" name = "pass-b"
        placeholder = "パスワード">
        <input type = "submit" name = "delete" 
        value = "削除"><br>
        <!--編集欄-->
        <input type = "number" name = "edit_number"
        placeholder = "編集対象番号"><br>
        <input type = "text" name = "pass-c"
        placeholder = "パスワード">
        <input type = "submit" name = "edit_submit"
        value = "編集">
    </form>

    <?php
        //表示機能
        //$rowの添字（[ ]内）は、カラムの名称に併せる必要があります。
        $sql = 'SELECT * FROM mission_5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['tb_id'].',';
            echo $row['tb_name'].',';
            echo $row['tb_comment'].',';
            echo $row['tb_date'].'<br>';
            echo "<hr>";
        }
        //編集するとき、パスワードが誤っている場合のエラー表示
        if (!empty($_POST['pass-c']) && $_POST['pass-c'] != $row['tb_password']){
            echo "パスワードが違います";
        }    
    ?>
</body>
</html>