<?php

// require_once("define.php");
require_once("function.php");

//db接続情報の定義
// define("DB_HOST",$define["host"]);
// define("DB_USER",$define["user"]);
// define("DB_PASS",$define["pass"]);
// define("DB_NAME",$define["name"]);

session_start();

//come from index.php
if(!empty($_GET['num_train'])){
  $_SESSION['question_num'] = 0;
  $_SESSION['num_train'] = (int)$_GET['num_train'];
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

//go to top page
if(!empty($_GET['exit'])){
  header("Location: ./");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>randoMemory(training mode)</title>
<link rel="stylesheet" href="style.css" >
</head>

<body>
<h1>randoMemory(training mode)</h1>

<?php
  //set array 
  if(empty($_SESSION['question_array'])){
    for($i = 0; $i < $_SESSION['num_train']; $i++){
      $question_array[] = rand(1,count($card_array));
      $is_front_array[] = rand(0,1);
    }
    $_SESSION['question_array'] = $question_array;
    $_SESSION['is_front_array'] = $is_front_array;
  }


  
  // echo "card_array<br>";
  // foreach($card_array as $value){
  //   var_dump($value);
  //   echo "<br>";
  // }
?>

<!-- <br>
<?php //echo "_SESSION['question_array']"."<br>"; ?>
<?php //var_dump($_SESSION['question_ppparray']) ?>
<br>
<br>
<?php //echo "_SESSION['is_front_array']"."<br>"; ?>
<?php //var_dump($_SESSION['is_front_array']) ?>
<br>
<br>
<?php //echo "_SESSION['num_train']"."<br>"; ?>
<?php //var_dump($_SESSION['num_train']) ?>
<br>
<br> -->

<!-- success_messageのチェック(掲示板の名残) -->
<!-- <?php //if(empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])): ?>
  <ul class="success_message">
    <li>・<?php //echo $_SESSION['success_message'];?></li>
    <?php //unset($_SESSION['success_message']) ?>
  </ul>
<?php //endif ?> -->

<!-- $error_messageのチェック -->
<?php if(!empty($error_message)): ?>
  <ul class="error_message">
    <?php foreach($error_message as $value): ?>
      <li>・<?php echo $value."<br>"; ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>


<?php
  //when result button is pushed
  if(!empty($_GET['result_ok'])){
    insert_result($card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_id'],1,$_SESSION['is_front_array'][$_SESSION['question_num']]);
    $_SESSION['question_num'] += 1;
  }else if(!empty($_GET['result_ng'])){
    insert_result($card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_id'],0,$_SESSION['is_front_array'][$_SESSION['question_num']]);
    $_SESSION['question_num'] += 1;
  }
?>

<!-- show cards -->
<?php if($_SESSION['question_num'] < $_SESSION['num_train']): ?>
  <?php show_train_question($card_array); ?>
<?php endif ?>



<?php if(empty($_GET['answer']) && ($_SESSION['question_num'] !== $_SESSION['num_train'])): ?>
<!-- answer button -->
<form class="question" method="get">
  <div class="answer">
    <input type="submit" name="answer" value="answer">
  </div>
</form>
<?php elseif(!empty($_GET['answer'])): ?>
<!-- result button -->
<form class="question" method="get">
  <div class="result">
    <input type="submit" name="result_ok" value="result OK">
    <input type="submit" name="result_ng" value="result NG">
  </div>
</form>
<?php endif ?>

<!-- show results -->
<?php if($_SESSION['question_num'] === $_SESSION['num_train']): ?>
  <?php $result_array = show_results(); ?>
<?php endif ?>

<?php // echo "<br><br>_SESSION['first_answer_date']<br>" ?>
<?php //var_dump($_SESSION['first_answer_date']) ?>
<?php //echo "<br>_SESSION['final_answer_date']<br>" ?>
<?php //var_dump($_SESSION['final_answer_date']) ?>

<!-- exit button -->
<form method="get">
  <div class="exit">
    <input type="submit" name="exit" value="exit">
  </div>
</form>


<section>
<article>
</article>
</section>
</body>
</html>