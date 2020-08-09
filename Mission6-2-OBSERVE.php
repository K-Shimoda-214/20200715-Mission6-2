<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = 'UTF-8'>
        <title>WEB実験ノート-閲覧</title>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート-実験内容の閲覧<br></b></span>
        <?php
            #データベースに接続して研究室メンバーの情報を取得
            $dsn = 'mysql:dbname=tb220143db;host=localhost';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            #研究室IDの引きつぎ
            $lab = $_SESSION['lab'];
            if(empty($lab)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            #メンバーの情報を取得する
            $sql = "SELECT * FROM $lab";
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            #プルダウンメニューの作成の準備
            echo "閲覧したいメンバーを選択してください";
            echo "<form action = '' method = 'post'>";
            echo "<select name = 'labmember'>";
            foreach($results as $result){
                $labmembers = $result['personalid'];
                #プルダウンメニューの作成
                echo "<option value = $labmembers>";
                echo $labmembers;
                echo "</option>";
            }
            echo "</select>";
            echo "<input type = 'submit' name = 'submit' value = '閲覧'>";
            echo "</form>";
            
            #選択されたメンバーを取得
            if(!empty($_POST['labmember'])){
                $sel_member = $_POST['labmember'];
                echo "【". $sel_member. " さんの実験履歴】";
                $membertable = $lab. "_". $sel_member;
                $_SESSION['usertable'] = $membertable;
                #テーブルの内容を取得
                $sql = "SELECT * FROM $membertable ORDER BY date DESC";
                #テーブルが存在しない場合のエラー回避
                $sql2 = "SHOW TABLES";
                $tables = $pdo -> query($sql2);
                foreach($tables as $table){
                    if($table[0] == $membertable){
                        $stmt = $pdo -> query($sql);
                        $results = $stmt -> fetchAll();
                            if(!empty($results)){
                                #表の作成
                                echo "<table border = 1>";
                                echo "<tr>";
                                echo "<th>実験日</th>";
                                echo "<th width = '1000'>実験内容</th>";
                                echo "<th>データファイル等</th>";
                                echo "<th>更新日時</th>";
                                echo "</tr>";
                                foreach($results as $result){
                                    echo "<tr>";
                                    echo "<td>". $result['date']. "</td>";
                                    echo "<td>". $result['method']. "</td>";
                                    if(!empty($result['filename'])){
                                        $filename = $result['filename'];
                                    }
                                    if(empty($result['filename'])){
                                    $filename = 'ファイルなし';
                                    echo "<td>". $filename. "</td>";
                                    }
                                    #ファイルをダウンロードできるようにする
                                    else{
                                        $id = $result['id'];
                                        echo "<td>". $filename;
                                        echo "<form action = '' method = 'post'>";
                                        echo "<input type = 'hidden' name = 'hidden_id' value = $id>";
                                        echo "<input type = 'hidden' name = 'hidden_name' value = $membertable>";
                                        echo "<input type = 'submit' name = 'submit' value = 'ダウンロード'>";
                                        echo "</form>";
                                        echo "</td>";
                                    }
                                    echo "<td>". $result['postdate']. "</td>";
                                    echo "</tr>";
                                }
                            }
                    }
                }
                echo "</table>";
                
            }
            if(!empty($_POST['hidden_id']) && !empty($_POST['hidden_name'])){
                $id2 = $_POST['hidden_id'];
                $membertable2 = $_POST['hidden_name'];
                $sql = "SELECT * FROM $membertable2";
                $stmt = $pdo -> query($sql);
                $lines = $stmt -> fetchAll();
                foreach($lines as $line){
                    $dbid = $line['id'];
                    $dbfile = $line['file'];
                    if($id2 == $dbid){
                        $new_filename = 'ダウンロード.pdf';
                        header("Content-Type: Application/PDF");
                        header("Content-Length: ". filesize($dbfile));
                        header('Content-Disposition: attachment; filename="'. $new_filename. '"');
                        echo $dbfile;
                        #なんでreadfileじゃだめなんだろう・・・（多分すでにreadfileしたものをカラムに取り込んでいるから?）
                    }
                }
                
                
                
                #ファイルを取得して隠しテキストボックスとIDが一致した時のファイルをダウンロード
                
                
                
                
                
            }
        ?>
        <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-Mypage.php'>マイページ</a>
    </body>
</html>