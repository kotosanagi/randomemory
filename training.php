<?php

require_once("define.php");
require_once("function.php");

//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);

session_start();

if(empty($_GET['answer'])){
  $num_train = (int)$_GET['num_train'];
}

//dbからデータを受け取る
$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if($mysqli->connect_errno){
  $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
}else{
  $sql = "SELECT card_id,card_front,card_back FROM cards order by card_id asc";
  $res = $mysqli->query($sql);

  if($res){
    $card_array = $res->fetch_all(MYSQLI_ASSOC);
  }

  $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>randoMemory(training mode)</title>

<style>

body{
  background-color: #dcffeb;
}


h1{
  font-size: 30px;
  text-align: center;
}

/***********************
アクセスカウンタ
************************/
.counter{
  text-align: center;
  width: 200px;
  margin: 0 auto 30px auto ;
  font-weight: 20px;
}

/***********************
結果表示部分
************************/
.success_message{
  border:solid 1px;
  border-color: blue;
  padding: 15px;
  margin: 0 90px;
  border-radius: 8px;
  list-style-type: none;
}

.error_message{
  border:solid 1px;
  border-color: red;
  padding: 15px;
  margin: 0 90px;
  border-radius: 8px;
  list-style-type: none;
}

/***********************
入力部分
************************/

form{
  background-color: white;
  margin: 15px 90px;
  padding-bottom: 15px;
}


textarea{
  height: 100px;
  width: 300px;
  border-radius: 5px;
  margin-right: 20px;
}
.fill_in_the_contents{
  margin:10px 20px;
  padding-top: 13px;
}

.input-card_front,
.input-card_back{
  margin-bottom: 20px;
}

.input-card_front{
  padding-left: 20px;
  width:400px;
  float:left;
}

.input-card_back{
  padding-left: 20px;
  width:400px
  float:left;
}

.input-btn_submit{
  margin: 0 15px;
  background: #96ddff;
  padding:8px;
  border-radius: 10px;
}

.input-btn_submit:hover{
  background-color: #40b4df;
  cursor: pointer;
  border-radius: 10px;
}

hr{
  margin: 30px;
}
/***********************
表示部分
************************/

.cards_list{
  text-align: center;
  font-weight: bold;
}

.card_view{
  background-color: #fff;
  margin: 15px 90px;
  padding : 2px 0px 0px 0px;
}

.card_front{
  margin: 0 15px 5px 15px;
  float :left;
}

.card_back{

}

.comment_message{
  margin:5px 15px 0 15px;
  padding-bottom: 5px;
  clear:left;
}




</style>
</head>

<body>
<h1>randoMemory(training mode)</h1>

<?php
  if(empty($_SESSION['question_array'])){
    for($i = 0; $i < $num_train; $i++){//$_GET['answer']的なのと「あってた」「間違ってた」とかが空っぽだったら。
      $question_array[] = rand(1,count($card_array));
      $front_back_array[] = rand(0,1);
    }
    for($i = 0; $i < $num_train; $i++){
      echo "No.".(string)((int)$i+1)." : ".$front_back_array[$i]." : ".$question_array[$i]."<br>";
    }
    $_SESSION['question_array'] = $question_array;
    $_SESSION['front_back_array'] = $front_back_array;
  }


  // var_dump($card_array);
  // var_dump(count($card_array));
  count($card_array);
  echo "<br>";
  foreach($card_array as $value){
    var_dump($value);
    echo "<br>";
  }
?>

<!-- success_messageのチェック -->

<?php if(empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])): ?>
  <ul class="success_message">
    <li>・<?php echo $_SESSION['success_message'];?></li>
    <?php unset($_SESSION['success_message']) ?>
  </ul>
<?php endif ?>

<!-- $error_messageのチェック -->
<?php if(!empty($error_message)): ?>
  <ul class="error_message">
    <?php foreach($error_message as $value): ?>
      <li>・<?php echo $value."<br>"; ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>


<form class="question" method="get">
  <div class="answer">
    <input type="submit" name="answer" value="answer">
  </div>
</form>



<?php var_dump($_SESSION['question_array']) ?>
<br>
<?php var_dump($_SESSION['front_back_array']) ?>
<section>
<article>
</article>
</section>
</body>
</html>
