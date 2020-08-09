<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>WEB実験ノート-新規登録</title>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート-個人ユーザー登録</b></span>
        <form action = "" method = "post">
            所属研究室の情報を入力して下さい<br>
            <input type = 'text' name ='input_labname' placeholder = '研究室IDを入力'><br>
            <input type = 'password' name = 'input_labpass' placeholder = 'パスワードを入力'>
            <input type = 'submit' name = 'submit'>
        </form>
        <?php
            if(!empty($_POST['input_labname']) && !empty($_POST['input_labpass'])){
                $labname = $_POST['input_labname'];
                $labpass = $_POST['input_labpass'];
            }
            #まず入力された研究室ID、パスワードが登録済みか調べる
            #データベースに接続
            $dsn = 'mysql:dbname=tb220143db;host=localhost';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            #テーブルに登録された研究室ID、パスワードを取得
            $sql = 'SELECT * FROM tblabname';
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            foreach($results as $result){
                $dblabname = $result['labname'];
                $dblabpass = $result['labpass'];
                #データベース内の情報と入力した情報が一致したら、新しいテーブル（研究室ごと）を作り、フォームを表示してループを止める（テーブル内には重複するIDはないので、breakのような処理は不要。）
                if(!empty($labname) && $labname == $dblabname && $labpass == $dblabpass){
                    #テーブル名を変数で指定
                    $sql = "CREATE TABLE IF NOT EXISTS $labname" 
                    ."("
                    ."personalid TEXT,"
                    ."personalpass TEXT"
                    .");";
                    $stmt = $pdo -> query($sql);
                    #フォームを表示
                    echo "<br>■個人用ユーザー登録ができます(※ユーザー名にスペースを含めないでください。ログインできなくなります。)<br>";
                    echo "<form action = '' method = 'post'>";
                    echo "<input type = 'text' name = 'username' placeholder = '個人ユーザー名を設定'>";
                    echo "<input type = 'password' name = 'p_pass' placeholder = 'パスワードを設定'>";
                    echo "<input type = 'password' name = 'conf_p_pass' placeholder = 'パスワード確認用'>";
                    echo "<input type = 'hidden' name = 'hidden' value = $labname>";
                    echo "<input type = 'submit' name = 'p_submit' value = '登録'>";
                }
            }
            #投稿内容が入力されたら、それらを取得する。
            if(!empty($_POST['username']) && !empty($_POST['p_pass']) && !empty($_POST['conf_p_pass']) &&!empty($_POST['hidden'])){
                $p_user = $_POST['username'];
                $p_pass = $_POST['p_pass'];
                $conf_p_pass = $_POST['conf_p_pass'];
                $hidden = $_POST['hidden'];
            }
            #ユーザー名の重複回避
            #データベース内のユーザー名を取得
            if(!empty($p_user)){
                $sql = "SELECT * FROM $hidden";
                $stmt = $pdo -> query($sql);
                $results = $stmt -> fetchAll();
                foreach($results as $result){
                    $dbid = $result['personalid'];
                    #入力されたIDと比較し、一致するものがあれば以降の動作を行わない
                    if($p_user == $dbid){
                        echo 'すでに使われているユーザー名です';
                        exit;
                    }
                }
            }
            #パスワードが一致した時は、テーブルに書き込む
            if(!empty($p_user) && $p_pass == $conf_p_pass){
                $sql = $pdo -> prepare("INSERT INTO $hidden(personalid, personalpass) VALUES(:personalid, :personalpass)");
                $sql -> bindParam(':personalid', $p_user, PDO::PARAM_STR);
                $sql -> bindParam(':personalpass', $p_pass, PDO::PARAM_STR);
                $sql -> execute();
                echo "個人データ登録が完了しました";
            }
            if(!empty($p_user) && $p_pass != $conf_p_pass){
                echo "確認用パスワードが一致しません";
            }
        ?>
    <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-login_01.php'>ログインはこちらから</a>
    </body>
</html>