<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>WEB実験ノート-研究室登録</title>
    </head>
    <body>
    <span style = "font-size: 30px;"><b>WEB実験ノート-研究室登録</b></span>
    <form action = "" method = "post">
        研究室IDを設定　　　<input type = "text", name = "kenkyu_room", placeholder = "研究室IDを設定"><br>
        ※研究室IDにスペースを含めないでください。ログインできなくなります。<br>
        <br>
        パスワードを設定　　<input type = "password", name = "password", placeholder = "パスワードを設定"><br>
        確認用にもう一度入力<input type = "password", name = "password_confirm", placeholder = "確認">
        <input type = "submit", name = "submit", value = "登録">
    </form>
    <?php
        #データベース上にテーブルを作って、入力された研究室名とパスワードを格納する
        #データベースへの接続
        $dsn = 'mysql:dbname=tb220143db;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        #テーブルの作成
        $sql = 'CREATE TABLE IF NOT EXISTS tblabname'
        ."("
        ."labname TEXT,"
        ."labpass TEXT"
        .");";
        $stmt = $pdo -> query($sql);
        
        #研究室名の重複を避けるため、テーブル内の研究室名を取得し、入力された研究室名と比較する
        #エラー回避
        if(!empty($_POST['kenkyu_room']) && !empty($_POST['password']) && !empty($_POST['password_confirm'])){
            $input_labname = $_POST['kenkyu_room'];
            $input_password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
        }
        $sql = 'SELECT * FROM tblabname';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchAll();
        foreach($results as $result){
            $dblabname = $result['labname'];
            if(!empty($input_labname) && $input_labname == $dblabname){
                echo "<br>そのIDはすでに使われています";
                #「入力IDとテーブルに登録されているIDを比較して、そのすべてと一致しないとき」はif文では実現できなさそう。
                #したがって一致するIDがあった時点で、以降のphpの動作をストップ
                exit; 
            }    
        }
        #データベースに書き込む
        if(!empty($input_labname) && !empty($input_password) && $input_password == $password_confirm){
            $sql = $pdo -> prepare('INSERT INTO tblabname (labname, labpass) VALUES (:labname, :labpass)');
            $sql -> bindParam(':labname', $input_labname, PDO::PARAM_STR);
            $sql -> bindParam(':labpass', $input_password, PDO::PARAM_STR);
            $sql -> execute();
            echo "<br>研究室登録が完了しました";
        }
        #入力されたパスワードが一致しない場合の対応
        
        if(!empty($input_password) && !empty($password_confirm) && $input_password != $password_confirm){
            echo "<br>パスワードが一致しません";
        }
    ?>
    <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-login_01.php'>ログインはこちらから</a>
    </body>
</html>