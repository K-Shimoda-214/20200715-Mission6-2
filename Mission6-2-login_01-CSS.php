<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = 'ja'>
    <head>
        <meta charset = "UTF-8">
        <title>WEB実験ノート-ログイン画面</title> 
        <style>
        body {
            background: #b0c4de;
        }
        </style>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート ログイン画面①-研究室情報の入力</b></span>
        <form action = "", method = "post">
            研究室ID　　　　<input type = "text", name = "user", placeholder = "研究室ID"><br>
            研究室パスワード<input type = "password", name = "password", placeholder = "研究室パスワード">
            <input type = "submit", name = "submit", value = "確認"><br>
        </form>
        <?php
            #入力内容の取得
            if(!empty($_POST['user'])){
                $input_labuser = $_POST['user'];
            }
            if(!empty($_POST['password'])){
                $input_labpass = $_POST['password'];
            }
            #入力された研究室ID、研究室パスワードがデータベースに登録されたものと一致するかどうかを確認する
            #データベースへの接続
            $dsn = 'mysql:dbname=tb220143db;host=localhost';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            #研究室ID、パスワードを取得
            $sql = 'SELECT * FROM tblabname';
            #tblabnameが存在しない時のエラー回避
            $sql2 = 'SHOW TABLES';
            $lines = $pdo -> query($sql2);
            foreach($lines as $line){
                $tblabname = $line[0];
                if($tblabname == 'tblabname'){
                    $stmt = $pdo -> query($sql);
                    $results = $stmt -> fetchAll();
                    foreach($results as $result){
                        $dblabname = $result['labname'];
                        $dblabpass = $result['labpass'];
                        #どうやら二段階のフォーム入力に問題がありそうな感じがするので、研究室ID、パスワードが一致した時点で、別ページに飛ばすやり方を考えてみる。
                
                        if(!empty($input_labuser) && !empty($input_labpass) && $dblabname == $input_labuser && $dblabpass == $input_labpass){
                            #次のページに飛ばす
                            #ページに飛ばす前に、データをサーバー側に保存
                            #$checkを使って、研究室IDが登録されていない場合の警告文を表示する
                            $check = 0;
                            $_SESSION['labname'] = $input_labuser;
                            header('Location: https://tb-220143.tech-base.net/Mission6-2/Mission6-2-login_02.php');
                            exit;
                        }
                        if(!empty($input_labuser) && !empty($input_labpass) && $dblabname == $input_labuser && $dblabpass != $input_labpass){
                            $check = 0;
                            echo 'パスワードが違います';
                            break;
                        }
                        if(!empty($input_labuser) && $dblabname == $input_labuser && empty($_POST['password'])){
                            $check = 0;
                            echo 'パスワードを入力して下さい';
                            break;
                        }
                        if(!empty($input_labuser) && $dblabname != $input_labuser){
                            $check = 1;                            
                        }
                    }
                }
            }
            if(!empty($check) && $check == 1){
                echo "登録されていない研究室IDです";
            }
        ?>
    </body>
    <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-LABregist.php'>研究室登録はこちら</a>
    <br>
    <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-New%20Registration.php'>個人ユーザー登録はこちら</a>
</html>