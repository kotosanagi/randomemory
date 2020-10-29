<?php

/*  It's for local environment
require_once("define.php");
//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);
*/

// It's for deploy environment
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
define("DB_HOST",$url["host"]);
define("DB_USER",$url["user"]);
define("DB_PASS",$url["pass"]);
define("DB_NAME",substr($url["path"]),1);

session_start();
//変数の初期化
$error_message = array();
$clean = array();
$message_array = array();
$num_train = 0;

//dbへデータを送信する
if(!empty($_POST['btn_submit'])){//register押されたかどうか

  //  card_frontのバリデーション(空白のチェック)
  if(empty($_POST['card_front'])) {
    $error_message[] = "please fill in the front of the card!";
  }else{//  view_nameのサニタイズ(無害化)
    $clean['card_front'] = htmlspecialchars($_POST['card_front'],ENT_QUOTES);
  }

  //  card_backのバリデーション(空白のチェック)
  if(empty($_POST['card_back'])) {
    $error_message[] = "please fill in the back of the card!";
  }else{//  view_nameのサニタイズ(無害化)
    $clean['card_back'] = htmlspecialchars($_POST['card_back'],ENT_QUOTES);
  }


  if(empty($error_message)) {
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    if($mysqli->connect_errno){
      $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
    }else{
      $mysqli->set_charset('utf8');

      $sql = "INSERT INTO cards(card_front,card_back) VALUES('$clean[card_front]','$clean[card_back]')";
      $res = $mysqli->query($sql);

    
      if($res) {
        $_SESSION['success_message'] = 'new card is registred!!';
      }else{
        $error_message[] = 'fail to register...';
      }
      $mysqli->close();
    }
    header("Location: ./");
  }
}elseif(!empty($_GET['go_to_training_btn'])){//go to training押されたかどうか
  $num_train = (int)$_GET['num_train'];
  if($num_train === 0){
    $error_message[] = "please put some numbers...";
  }else{
    $_SESSION['question_array'] = array();
    $_SESSION['is_front_array'] = array();
    $_SESSION['first_answer_date'] = "";
    $_SESSION['final_answer_date'] = "";
    // $_SESSION['finish_train'] = "";
    header("Location: ./training.php?num_train=".$num_train);
  }
}

//dbからデータを受け取る
$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if($mysqli->connect_errno){
  $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
}else{
  $sql = "SELECT card_front,card_back FROM cards order by card_id asc";
  $res = $mysqli->query($sql);

  echo 'var_dump(res) : ';
  var_dump($res);

  if($res){
    $card_array = $res->fetch_all(MYSQLI_ASSOC);
  }

  echo 'var_dump(card_array) : ';
  var_dump($card_array);

  $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>randoMemory</title>
<link rel="stylesheet" href="style.css" >
</head>

<body>

<h1>randoMemory</h1>


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


<form class="register_cards" method="post">
  <p class="fill_in_the_contents">fill in the contents what you want to memorise...</p>
  
  <div class="cards_area clearfix">
    <!-- view_name input -->
    <div class="input-card_front">
      <label for="card_front">front of the card</label>
      <br>
      <textarea id="card_front" name="card_front"></textarea>
    </div>
    <!-- message_id input -->
    <div class="input-card_back">
      <label for="card_back">back of the card</label>
      <br>
      <textarea id="card_back" name="card_back"></textarea>
    </div>
  </div>


  <!-- btn_submit input -->
  <input class="input-btn_submit" type="submit" name="btn_submit" value="register!" >
</form>
<hr>

<!-- go to training -->
<div class='go_to_training'>
  <p>How many times do you train?</p>

  <form class="num_train" method="get">
    <div class='num_train_times'>
      <input type="text" name="num_train" value="" class="num_train_text">
      <div class='times'>times</div>
    </div>
    <input class="go_to_training_btn" type="submit" name="go_to_training_btn" value="let's training!">
  </form>
</div>

<hr>

<section>

<article>
<p class="cards_list">cards list</p>
<?php $card_i = 1; ?>
<?php foreach ($card_array as $value): ?>
  <div class="card_view">
    <div class="card_i">
      <?php echo $card_i; ?>
    </div>
    <div class="card_front">
      <?php echo $value['card_front']; ?>
    </div>
    <div class="card_back">
      <?php echo $value['card_back']; ?>
    </div>
  </div>
  <?php $card_i += 1; ?>
<?php endforeach ?>
</article>
</section>
</body>
</html>
