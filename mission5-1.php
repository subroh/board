<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        $sql = "CREATE TABLE IF NOT EXISTS dbtable"
            ."("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name char(32),"
                . "comment TEXT,"
                . "create_at char(32),"
                . "password char(32)"
            .");";
        $pdo->query($sql);
        
        if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])) {
            if (empty($_POST["flg"])) {
                //投稿モード
                $sql = "INSERT INTO dbtable (name, comment, create_at, password) VALUES (:name, :comment, :create_at, :password)";
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':name', $_POST["name"], PDO::PARAM_STR);
                $stmt -> bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
                $stmt -> bindParam(':create_at', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':password', $_POST["password"], PDO::PARAM_STR);
                $date = date ("Y/m/d H:i:s");
                $stmt -> execute();
            } else {
                //編集モード
                $sql = 'UPDATE dbtable SET name=:name,comment=:comment,create_at=:create_at,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $_POST["flg"], PDO::PARAM_INT);
                $stmt->bindParam(':name', $_POST["name"], PDO::PARAM_STR);
                $stmt->bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
                $stmt->bindParam(':create_at', $date, PDO::PARAM_STR);
                $stmt->bindParam(':password', $_POST["password"], PDO::PARAM_STR);
                $date = date ("Y/m/d H:i:s");
                $stmt->execute();
            }
        }
        
        if (!empty($_POST["delete"]) && !empty($_POST["password"])) {
            //対応する項目のパスワード取得
            $check = "";
            $sql = 'SELECT * FROM dbtable';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if ($row['id'] == $_POST["delete"]) $check = $row["password"];
            }
            if ($_POST["password"] == $check) { //パスワードOK
                //指定された投稿を削除する作業
                $sql = 'DELETE FROM dbtable WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $_POST['delete'], PDO::PARAM_INT);
                $stmt->execute();
            } elseif ($check == "") echo "対応する投稿がありません<br>";
            else echo "パスワードが違います<br>";
        }
        
        if (!empty($_POST["edit"]) && !empty($_POST["password"])) {
            $name = "";
            $comment = "";
            $flg = "";
            $password = "";
            //対応する項目のパスワード取得
            $check = "";
            $sql = 'SELECT * FROM dbtable WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $_POST["edit"], PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(); 
            foreach ($results as $row){
                if ($row['id'] == $_POST["edit"]) $check = $row["password"];
            }
            if ($_POST["password"] == $check) { //パスワードOK
                //名前、コメント、パスワードを投稿フォームに表示
                foreach ($results as $row){
                    if ($row['id'] == $_POST["edit"]){
                        $flg = $row["id"];
                        $name = $row["name"];
                        $comment = $row["comment"];
                        $password = $row["password"];
                    }
                }
            } elseif ($check == "") echo "対応する投稿がありません<br>";
            else echo "パスワードが違います<br>";
        }
        
        //idを連番に修正
        $sql = 'SELECT * FROM dbtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        $num = 1;
        foreach ($results as $row){
            $sql = 'UPDATE dbtable SET id=:newid WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':newid', $num, PDO::PARAM_INT);
            $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
            $stmt->execute();
            $num++;
        }
    ?>
    <form method="post" action="">投稿フォーム<br>
        <input type="text" name="name" placeholder="名前" value = "<?php if(!empty($_POST["edit"])) echo $name; ?>"><br>
    	<input type="text" name="comment" placeholder="コメント" value = "<?php if(!empty($_POST["edit"])) echo $comment; ?>"><br>
    	<input type="hidden" name="flg" value = "<?php if(!empty($_POST["edit"])) echo $flg; ?>">
    	<input type="text" name="password" placeholder="パスワード" value = "<?php if(!empty($_POST["edit"])) echo $password; ?>"><br>
    	<input type="submit" name="submit">
    </form>
    <form method="post" action="">削除フォーム<br>
    	<input type="number" name="delete" min=1 placeholder="削除対象番号"><br>
    	<input type="text" name="password" placeholder="パスワード"><br>
    	<input type="submit" name="submit">
    </form>
    <form method="post" action="">編集フォーム<br>
    	<input type="number" name="edit" min=1 placeholder="編集対象番号"><br>
    	<input type="text" name="password" placeholder="パスワード"><br>
    	<input type="submit" name="submit">
    </form>
    <?php
        $sql = 'SELECT * FROM dbtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['create_at'].'<br>';
        }  
    ?>
</body>
</html>