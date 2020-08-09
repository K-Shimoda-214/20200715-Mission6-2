<?php
    session_start();
?>
<!DOCTYPE html>
<html lang = 'ja'>
    <head>
        <meta charset = 'UTF-8'>
        <title>WEB研究ノート-新規投稿</title>
    </head>
    <body>
        <span style = "font-size: 30px;"><b>WEB実験ノート-新規投稿</b></span>
        <form action = "" method = "post" enctype = "multipart/form-data">
            1. 実験実施日を入力<br>
            <input type = "date" name = "postdate" placeholder = "実験実施日"><br>
            <br>2. 実験内容を入力<br>
            <textarea name = "text" placeholder = "実験内容を入力" rows = "30" cols = "50"></textarea><br>
            <br>3. 実験結果等のファイルがあればアップロードできます（PDFファイルのみ）<br>
            <input type = "file" name = "file"><br>
            <br><input type = "submit" name = "submit" value = "確認">
        </form>
        <?php
            #テーブル名を次に引き継ぐため、前ページからテーブル名を取得
            $tablename = $_SESSION['tablename'];
            if(empty($tablename)){
                echo "セッションタイムアウト。ログインしなおしてください";
                exit;
            }
            #実施日と実験内容の入力が確認出来たら、確認画面へ遷移する
            if(!empty($_FILES['file'])){
                $filename = $_FILES['file']['name'];
                if(!empty($_FILES['file']['tmp_name'])){
                    $file = file_get_contents($_FILES['file']['tmp_name']);
                }
                if(!empty(pathinfo($filename)['extension'])){
                    $extension = pathinfo($filename)['extension'];
                }
                if(!empty($extension) && $extension != 'pdf'){
                    echo "このファイルはPDFファイルではありません";
                    exit;
                }
                
                
            }
            if(!empty($_POST['postdate']) && !empty($_POST['text'])){
                $postdate = $_POST['postdate'];
                $text = $_POST['text'];
                $_SESSION['selected_date'] = $postdate;
                $_SESSION['post_text'] = $text;
                $_SESSION['up_file'] = $file;
                $_SESSION['filename'] = $filename;
                $_SESSION['table'] = $tablename;
                header('Location: https://tb-220143.tech-base.net/Mission6-2/Mission6-2-NEWPOST-confirm.php');
            }
            if(!empty($_POST['postdate']) && empty($_POST['text'])){
                echo "必要事項を入力して下さい";
            }
            if(empty($_POST['postdate']) && !empty($_POST['text'])){
                echo "必要事項を入力して下さい";
            }
        ?>
    </body>
</html>