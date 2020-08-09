<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = 'UTF-8'>
        <title>投稿内容確認</title>
    </head>
    <body>
        <?php
            #テーブル名を取得する
            
            #投稿内容を表示する
            $selected_date = $_SESSION['selected_date'];
            if(empty($selected_date)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            $post_text = $_SESSION['post_text'];
            $up_file = $_SESSION['up_file'];
            $filename = $_SESSION['filename'];
            $table = $_SESSION['table'];
            if(empty($table)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            echo "■以下の内容で登録します<br>";
            echo "1. 実験実施日<br>"; 
            echo $selected_date. "<br>";
            echo "<br>";
            echo "2. 実験内容<br>";
            echo $post_text. "<br>";
            echo "<br>";
            echo "3. 実験結果等のファイル<br>";
            if(!empty($filename)){
                echo $filename;
            }else{
                echo "ファイルなし";
            }
             
            #今回はユーザーから取得するデータはないが、確認ボタンを動作させるために、隠しテキストボックスを作成する
            echo "<form action = '' method = 'post'>";
            echo "<input type ='hidden' name = 'hidden' value = 'a'>";
            echo "<input type = 'submit' name = 'submit' value = '実験データを登録'>";
            echo "</form>";
            
            #確認ボタンが押されたら(=隠しボックスからの情報を受け取ったら)データベースに書き込む
            if(!empty($_POST['hidden'])){
                $hidden = $_POST['hidden'];
            }
            if(!empty($hidden)){
                $postdate = date("Y-m-d(D) H:i:s");
                #データベースの接続
                $dsn = 'mysql:dbname=tb220143db;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => ERRMODE_WARNING));
            
                #テーブルへの書き込み
                #テーブル名の引継ぎが必要
                $sql = $pdo -> prepare("INSERT INTO $table(date, method, file, filename, postdate) VALUES(:date, :method, :file, :filename, :postdate)");
                $sql -> bindParam(":date", $selected_date, PDO::PARAM_STR);
                $sql -> bindParam(":method", $post_text, PDO::PARAM_STR);
                $sql -> bindParam(":file", $up_file);
                $sql -> bindParam(":filename", $filename, PDO::PARAM_STR);
                $sql -> bindParam(":postdate", $postdate, PDO::PARAM_STR);
                $sql -> execute();
                #投稿できたら投稿完了画面に遷移
                header('Location: https://tb-220143.tech-base.net/Mission6-2/Mission6-2-NEWPOST-complete.php');
            }
        ?>
    </body>
</html>