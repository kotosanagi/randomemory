<?php

require_once("define.php");

//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);

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
        $_SESSION['success_message'] = 'success!!';
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
<title>randoMemory</title>

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
  padding-left: 60px;
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


<form method="post">
  <p class="fill_in_the_contents">fill in the contents what you want to memorise...</p>
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
  <!-- btn_submit input -->
  <input class="input-btn_submit" type="submit" name="btn_submit" value="register!" >
</form>
<hr>

<!-- 暗記練習モードへ -->

<form class="go_to_training" method="get">
  <div class="num_train"><!-- 回数指定 -->
    <label for="num_train">How many times do you train?</label>
    <input type="text" name="num_train" value="">
  </div>
  <div class="go_to_training_btn">
    <input type="submit" name="go_to_training_btn" value="go to training!">
  </div>
</form>


<!-- 暗記練習モードへ ここまで -->
<hr>

<section>


<article>
<p class="cards_list">cards list</p>
<?php foreach ($card_array as $value): ?>

  <div class="card_view">
    <div class="card_front">
      <?php echo $value['card_front']."<br>"; ?>
    </div>
    <div class="card_back">
      <?php echo $value['card_back']; ?>
    </div>
  </div>

<?php endforeach ?>
</article>
</section>
</body>
</html>
