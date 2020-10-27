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
<h1>randoMemory</h1>

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
  if(!empty($_GET['result_ok']) && ($_SESSION['question_num'] < $_SESSION['num_train'])){
    insert_result($card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_id'],1,$_SESSION['is_front_array'][$_SESSION['question_num']]);
    $_SESSION['question_num'] += 1;
  }else if(!empty($_GET['result_ng']) && ($_SESSION['question_num'] < $_SESSION['num_train'])){
    insert_result($card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_id'],0,$_SESSION['is_front_array'][$_SESSION['question_num']]);
    $_SESSION['question_num'] += 1;
  }
?>

<!-- show cards -->
<?php if($_SESSION['question_num'] < $_SESSION['num_train']): ?>
  <?php echo '<div class="question_num">Q '.($_SESSION['question_num']+1)."/".$_SESSION['num_train'].'<br></div>'; ?>
  <?php if(empty($_GET['answer'])):?>
    <div class='question_cards clearfix'>
      <?php show_train_question($card_array); ?>
    </div>
  <?php else: ?>
    <div class='question_cards clearfix'>
      <?php show_temp_answer($card_array); ?>
    </div>
  <?php endif ?>
<?php endif ?>



<!-- answer or result button -->
<?php if(empty($_GET['answer']) && ($_SESSION['question_num'] < $_SESSION['num_train'])): ?>
  <!-- answer button -->
  <div class='ans_res_button'>
  <p>Think of this pair of card...</p>
    <form class="question" method="get">
      <input class="ans_btn" type="submit" name="answer" value="answer">
    </form>
  </div>
<?php elseif(!empty($_GET['answer'])): ?>
  <div class='ans_res_button'>
    <p>Did you remenber the current card?</p>
    <!-- result button -->
    <form class="question" method="get">
      <div class="result">
        <input class="res_ok_btn" type="submit" name="result_ok" value="Yes!!">
        <input class="res_ng_btn" type="submit" name="result_ng" value="No...">
      </div>
    </form>
  </div>
<?php endif ?>

<!-- show results -->
<?php if($_SESSION['question_num'] === $_SESSION['num_train']): ?>
  <p style="text-align:center">your result is .....</p>
  <div class="show_results clearfix">
    <?php $result_array = show_results(); ?>
  </div>
<?php endif ?>

<!-- exit button -->
<div class="exit">
  <form method="get">
    <input class="exit_btn" type="submit" name="exit" value="exit">
  </form>
</div>


<section>
<article>
</article>
</section>
</body>
</html>