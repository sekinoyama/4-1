<?php

 $dsn='データベース名';//データベースへの接続
 $user='ユーザー名';
 $password='パスワード';
 $pdo=new PDO($dsn,$user,$password);

 $sql="CREATE TABLE no"//3-2の内容、コメントの後ろに時間とパスワードの項目も作った方がいい？
 ."("
 ."id int auto_increment primary key,"//idになる
 ."name char(32),"//名前。(32)は文字数の指定？
 ."comment TEXT,"//コメント。文章になれそう
 ."date datetime,"//日時のつもり。
 ."password varchar(10)"//パスワード。10文字指定にしてみたつもり。
 .");";
 $stmt=$pdo->query($sql);

//新規投稿から受け取る
 $name=$_POST['name'];//名前
 $comment=$_POST['comment'];//コメント
 $password=$_POST['password'];//新規投稿時のパスワード

 $e_number=$_POST['number'];//編集番号とパスワードがあってた時に出る

 $id='NULL';//まずゼロ?

//先に編集コード書いてからの新規投稿のコード
 if(!empty($name) && !empty($comment) && !empty($e_number)){//編集時のコード
  $sql='SELECT id,name,comment,date FROM no';//データの取得
  $sql="update no set name='$name', comment='$comment', date=NOW() where id='$e_number'";//編集
  $result=$pdo->query($sql);
 }elseif(!empty($name) && !empty($comment) && !empty($password)){//後で編集番号が入っているときとそうでないときで分岐
  $sql=$pdo->prepare("INSERT INTO no(id,name,comment,date,password) VALUE(:id,:name,:comment,NOW(),:password)");
  $sql->bindValue(':id',$id,PDO::PARAM_INT);
  $sql->bindParam(':name',$name,PDO::PARAM_STR);
  $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
  $sql->bindParam(':password',$password,PDO::PARAM_STR);
  $sql->execute();
  }

//削除から受け取る
 $delete=$_POST['d_number'];//削除番号
 $dlt_pass=$_POST['d_pass'];//パスワード、指定した番号の投稿についたパスワードと比較する

//削除時のコード
 if(!empty($delete) && !empty($dlt_pass)){
  $sql='SELECT id,password FROM no';//データの取得ができるらしい。idを取得して削除番号と比較できないか？
  $results=$pdo->query($sql);
  foreach($results as $row){
   if($row['id']==$delete && $row['password']==$dlt_pass){//削除番号とパスワードの両方が一致したとき
    $sql="delete from no where id=$delete";//削除
    $result=$pdo->query($sql);
   }elseif($row['id']==$delete && $row['password']!=$dlt_pass){
     echo "パスワード違います";
    }
  }//foreachかっこ
 }

//編集から受け取る
 $edit_number=$_POST['e_number'];
 $edit_pass=$_POST['e_pass'];

//編集番号が入った時、フォームに再表示
 if(!empty($edit_number) && !empty($edit_pass)){
  $sql='SELECT id,name,comment,password FROM no';
  $results=$pdo->query($sql);
  foreach($results as $row){
   if($row['id']==$edit_number && $row['password']==$edit_pass){
    $edit_i=$row['id'];
    $edit_n=$row['name'];
    $edit_c=$row['comment'];
   }elseif($row['id']==$edit_number && $row['password']!=$edit_pass){
     echo "パスワード違います";
    }
  }
 }

?>

<!DOCTYPE html>
 <html>
  <head>
   <meta charset="utf-8">
   <title>mission_4-1</title>
  </head>
  <body>
   <form action="mission_4-1.php" method="post">
    <p><input type="text" name="name" placeholder="お名前" value="<?php echo $edit_n;?>"></p>
    <p><input type="text" name="comment" placeholder="コメント" value="<?php echo $edit_c;?>"></p>
    <p><input type="password" name="password" placeholder="パスワード"></p>
    <p><input type="hidden" name="number" value="<?php echo $edit_i;?>"></p><!--あとでvalueで編集時の投稿を表示-->
    <p><input type="submit" value="送信"></p>

    <p><input type="number" name="d_number" placeholder="削除番号"></p><!--削除用フォーム-->
    <p><input type="password" name="d_pass" placeholder="パスワード"></p>
    <p><input type="submit" value="削除"></p>

    <p><input type="number" name="e_number" placeholder="編集番号"></p><!--編集用フォーム-->
    <p><input type="password" name="e_pass" placeholder="パスワード"></p>
    <p><input type="submit" value="編集"></p>
   </form>
  </body>
 </html>


<?php
 $sql='SELECT*FROM no ORDER BY id ASC';//*は全部のカラムを取得するときに使えるらしい。order...でidを順番に取得
 $results=$pdo->query($sql);
 foreach($results as $row){
  echo $row['id'].' ';
  echo $row['name'].' ';
  echo $row['comment'].' ';
  echo $row['date'].'<br>';
 }
?>
