<?php
    // DB接続設定
    //データベース名
    $dsn = 'mysql:dbname=********;host=localhost';
    //ユーザー名
    $user = '**********';
    //パスワード
    $password = 'PASSWORD';
    
    //データベースに接続する
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    print('接続に成功しました。<br>');
    
    //もしまだテーブル名"mission5"が存在しないならテーブル"mission5"を作成する
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    //自動で登録されているナンバリング
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    //名前を入れる。32文字まで
    . "name char(32),"
    //コメントを入れる。80文字まで
    . "comment TEXT,"
    //日時を入れる
    . "now datetime,"
    //パスワードを入れる
    . "pass char(32)"
    .");";
    $stmt = $pdo->query($sql);
//編集実行機能
//編集番号が指定済みなら以下の処理を実行
if(!empty($_POST["editnum"])){
    //入力フォームに文字があるとき下記の処理を実行  
    if(!empty($_POST["name"]) || !empty($_POST["comment"]) || !empty($_POST["pass"])){
        //指定した編集番号を代入
        $id      = $_POST["editnum"];
        //入力された文字を変数に代入
        $name    = $_POST["name"];
        $comment = $_POST["comment"]; 
        $now     = date('Y/m/d H:i:s');
        $pass    = $_POST["pass"];
        //指定した投稿を書き換える
        $sql_u  = 'UPDATE mission5 SET name=:name,comment=:comment,now=:now,pass=:pass WHERE id=:id';
        $stmt_u = $pdo->prepare($sql_u);
        $stmt_u -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_u -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt_u -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt_u -> bindParam(':now', $now, PDO::PARAM_STR);
        $stmt_u -> bindParam(':pass', $pass, PDO::PARAM_STR);
        
        $stmt_u->execute();
    
    }
}
//新規投稿機能
//入力フォームに文字があるとき下記の処理を実行  
elseif(!empty($_POST["name"]) || !empty($_POST["comment"]) || !empty($_POST["pass"])){
    //入力された文字を変数に代入
    $name = $_POST["name"];
    $comment = $_POST["comment"]; 
    $now     = date('Y/m/d H:i:s');
    $pass    = $_POST["pass"];
    //テーブルに入力内容を書き込む
    $sql_w = $pdo -> prepare('INSERT INTO mission5 (name,comment,now,pass) VALUES (:name,:comment,:now,:pass)');
    $sql_w -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql_w -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql_w -> bindParam(':now', $now, PDO::PARAM_STR);
    $sql_w -> bindParam(':pass', $pass, PDO::PARAM_STR);
        
    $sql_w->execute();
    
}
//削除フォームに文字があるとき以下の処理を実行
elseif(!empty($_POST["del"]) || !empty($_POST["pass_del"])){
    //$delidに削除番号を代入
    $delid = $_POST["del"];
    //$delpassにパスワードを代入
    $delpass = $_POST["pass_del"];
    //削除番号と一致する投稿番号の投稿を削除 
    $sql_d = 'delete from mission5 where id=:delid and pass=:delpass';
    $stmt_d = $pdo->prepare($sql_d);
    $stmt_d->bindParam(':delid', $delid, PDO::PARAM_INT);
    $stmt_d->bindParam(':delpass', $delpass, PDO::PARAM_STR);
    $stmt_d->execute();
    
}
//編集選択機能
//編集フォームに文字があるとき以下の処理を実行
elseif(!empty($_POST["edit"]) || !empty($_POST["pass_edt"])){
    //$edtidに削除番号を代入
    $edtid   = $_POST["edit"];
    //$delpassにパスワードを代入
    $edtpass = $_POST["pass_edt"];
    //編集番号と一致する投稿番号の投稿を抽出
    $sql_s  ='SELECT * FROM mission5 where id=:edtid and pass=:edtpass';
    $stmt_s = $pdo->prepare($sql_s);
    $stmt_s->bindParam(':edtid', $edtid, PDO::PARAM_INT);
    $stmt_s->bindParam(':edtpass', $edtpass, PDO::PARAM_STR);
    $stmt_s->execute();
    //配列の形に整える
    $results_s = $stmt_s->fetchAll();
    foreach ($results_s as $row_s){
    //抽出したデータを変数に代入
        $name_edt    = $row_s['name'];
        $comment_edt = $row_s['comment'];
        $pass_edt    = $row_s['pass'];
    }
    
}

?>

<!DOCTYPE html>
<html> 
<head> 
<meta charset="utf-8" /> 
</head>
<body> 
<!-- 入力フォームを作る -->
<!-- method属性を指定 -->
<form action="" method="post">
    <!-- type属性でテキストボックスを指定、name属性で変数を指定-->
    <input type="text" name="name" placeholder="名前"
        value="<?php if(!empty($name_edt)){//編集番号指定後、指定した投稿の名前を表示
                        echo $name_edt;
                        }
                        else{
                        }?>"><br>
    <input type="text" name="comment" placeholder="コメント"
        value="<?php if(!empty($comment_edt)){//編集番号指定後、指定した投稿のコメントを表示
                        echo $comment_edt;
                        }
                        else{
                        }?>"><br>
    <!-- パスワード入力フォーム作製 -->
    <input type="text" name="pass" placeholder="パスワード"
        value="<?php if(!empty($pass_edt)){//編集番号指定後、指定した投稿のパスワードを表示
                        echo $pass_edt;
                        }
                        else{
                        }?>">
    <!-- 編集対象番号を非表示 -->
    <input type="hidden" name="editnum" placeholder="編集対象番号" 
        value="<?php if(!empty($_POST["edit"])){//編集番号が空でなければ編集番号を表示
                        echo $_POST["edit"];
                        }
                        else{
                        }?>">
    <!-- type属性にsubmitを指定して送信ボタンを作成 -->
    <input type="submit" value="送信"><br><br>
</from>
<!-- 削除フォーム作製 -->
<from action="" method="post">
    <!-- type属性でテキストボックスを指定、name属性で変数を指定-->
    <input type="text" name="del" placeholder="削除対象番号"><br>
    <!-- パスワード入力フォーム作製 -->
    <input type="text" name="pass_del" placeholder="パスワード">
    <!-- type属性にsubmitを指定して送信ボタンを作成 -->
    <input type="submit" value="削除"><br>
</from>
<!-- 削除番号指定フォーム完成 -->
<!-- 編集フォーム作製 -->
<from action="" method="post">
    <!-- type属性でテキストボックスを指定、name属性で変数を指定-->
    <input type="text" name="edit" placeholder="編集対象番号"><br>
    <!-- パスワード入力フォーム作製 -->
    <input type="text" name="pass_edt" placeholder="パスワード">
    <!-- type属性にsubmitを指定して送信ボタンを作成 -->
    <input type="submit" value="編集"><br>
</from>
<!-- 編集フォーム完成 -->
</body>
</html>
<?php
//入力したデータレコードを抽出し、表示する
    $sql = 'SELECT * FROM mission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['now'].',';
    echo "<hr>";
    }
    
$pdo = null;

?>