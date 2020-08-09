<!--研究室IDでログインが成功したあとの個人ID入力画面-->
<!--研究室IDは前回のログイン画面から引き継ぐ-->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = 'UTF-8'>
        <title>WEB研究ノート-個人ID入力画面</title>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート ログイン画面②-個人IDの入力</b></span>
        <?php
            #フォームにプルダウンを作成するために、テーブルから情報を取得
            #データベースに接続
            $dsn = 'mysql:dbname=tb220143db;host=localhost';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            #テーブルからグループに格納されている個人IDを取得する
            $labname = $_SESSION['labname'];
            $sql = "SELECT * FROM $labname";
            #テーブルが存在しない時のエラー回避
            $sql2 = "SHOW TABLES";
            $tables = $pdo -> query($sql2);
            foreach($tables as $table){
                $tablename = $table[0];
                if($tablename == $labname){
                    $stmt = $pdo -> query($sql);
                    $results = $stmt -> fetchAll();
                    #プルダウンメニューを含んだフォームの作成
                    echo "<form action = '' method = 'post'>";
                    echo "<select name = 'pulldown'>";
                    foreach($results as $result){
                        $p_id = $result['personalid'];
                        echo "<option value = $p_id>";
                        echo $p_id;
                        echo "</option>";
                    }
                    echo "</select>";
                    echo "<br>";
                    echo "<input type = 'password' name = 'password' placeholder = '個人パスワードを入力'>";
                    echo "<br>";
                    echo "<input type = 'submit' name = 'submit' value = 'login'>";
                    echo "</form>";
            
                    #フォームに入力された情報を取得
                    if(!empty($_POST['pulldown']) && !empty($_POST['password'])){
                        $input_id = $_POST['pulldown'];
                        $input_pass = $_POST['password'];
            
                        #データベースの情報と比較する
                        #データベースに保存されている個人IDとパスワードを取得する
                        $sql = "SELECT * FROM $labname";
                        $stmt = $pdo -> query($sql);
                        $results = $stmt -> fetchAll();
                        foreach ($results as $result){
                            $dbid = $result['personalid'];
                            $dbpass = $result['personalpass'];
                            if($input_id == $dbid && $input_pass == $dbpass){
                                #マイページに研究室IDとユーザーIDを引き継ぐ
                                $_SESSION['labid'] = $labname;
                                $_SESSION['personalid'] = $input_id;
                                header("Location:https://tb-220143.tech-base.net/Mission6-2/Mission6-2-Mypage.php");
                                exit;
                            }
                            if($input_id == $dbid && $input_pass != $dbpass){
                                echo "パスワードが間違っています";
                            }
                        }
                    }
                }
            }    
        ?>
    </body>
</html>