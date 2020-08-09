<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>WEB実験ノート-マイページ</title>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート-マイページ<br></b></span>
        <?php
            #マイページに遷移した時点でテーブルを作成する
            #まずはデータベースに接続
            $dsn = 'mysql:dbname=tb220143db;host=localhost';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            #テーブルの作成
            #テーブル名作成の為に、前のページから研究室IDと個人IDを引き継ぐ
            $p_id = $_SESSION['personalid'];
            #個人IDおよび研究室IDが引き継がれなくなったらセッションタイムアウトとしてテーブルを作らせない 
            if(empty($p_id)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            echo "ようこそ！　". $p_id. " さん<br>"; 
            $lab_id = $_SESSION['labid'];
            if(empty($lab_id)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            $usertable = $lab_id . "_". $p_id;
            $sql = "CREATE TABLE IF NOT EXISTS $usertable"
            . "("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "date DATE,"
            . "method TEXT,"
            . "filename TEXT,"
            . "file MEDIUMBLOB,"
            . "postdate TEXT"
            . ");";
            $stmt = $pdo -> query($sql);
            #新規投稿画面で使用するため、テーブル名を引き継ぐ
            $_SESSION['tablename'] = $usertable;
            
            #直近1週間の投稿を表示できるようにする
            #現在の日付を取得
            $date = new DateTime();
            $cur_date = $date -> format('Y-m-d');
            $cur_date2 = new DateTime($cur_date);
            #テーブルの情報を取得
            #取得する際に実験日で降順になるように取得
            $sql = "SELECT * FROM $usertable ORDER BY date DESC";
            $stmt = $pdo -> query($sql);
            $results = $stmt -> fetchAll();
            echo "<br>【直近1週間の実験内容】<br>";
            echo "<table border = 1>";
            echo "<tr>";
            echo "<th>実験日</th>";
            echo "<th width = '1000'>実験内容</th>";
            echo "<th>データファイル等</th>";
            echo "<th>更新日時</th>";
            echo "</tr>";
            foreach($results as $result){
                $dbdate = $result['date'];
                $dbmethod = $result['method'];
                if(!empty($result['filename'])){
                    $dbfilename = $result['filename'];
                }
                if(empty($result['filename'])){
                    $dbfilename = "ファイルなし";
                }
                $dbpostdate = $result['postdate'];
                $dbdate2 = new DateTime($dbdate);
                #現在の日付と実験実施日時を比較する
                #diffはオブジェクト同士の比較しかできないようなので上のように全てオブジェクト化した
                $diff = $cur_date2 -> diff($dbdate2);
                #日数差を総日数で取得
                $diff_date = $diff -> format('%a');
                if($diff_date <= 7){
                    echo "<tr>";  
                    echo "<td>$dbdate</td>";
                    echo "<td>$dbmethod</td>";
                    if(!empty($dbfilename)){
                        echo "<td>$dbfilename</td>";
                    }
                    echo "<td>$dbpostdate</td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
            #閲覧画面で使用するため、研究室IDの引継ぎ
            $_SESSION['lab'] = $lab_id;
            
            
        ?>
        <!--新規投稿画面へのハイパーリンク-->
        <br>
        <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-NEWPOST.php'>新規投稿</a>
        <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-OBSERVE.php'>実験履歴の閲覧</a>
        <a href = 'https://tb-220143.tech-base.net/Mission6-2/Mission6-2-login_01.php'>ログアウト</a>
    </body>
</html>