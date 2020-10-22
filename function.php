<?php

require_once("define.php");
//db接続情報の定義
define("DB_HOST",$define["host"]);
define("DB_USER",$define["user"]);
define("DB_PASS",$define["pass"]);
define("DB_NAME",$define["name"]);


//when you select results, insert result to mysql
function insert_result($result_card_id,$result,$is_front){
  date_default_timezone_set('Asia/Tokyo');
  $trained_date = date("Y/m/d H:i:s");
  $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

  if($mysqli->connect_errno){
    $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
  }else{
    $mysqli->set_charset('utf8');

    $sql = "INSERT INTO results(result_card_id,result,is_front,trained_date) VALUES('$result_card_id','$result','$is_front','$trained_date')";
    $res = $mysqli->query($sql);

    if($res) {
      if(empty($_SESSION['first_answer_date'])){
        $_SESSION['first_answer_date'] = $trained_date;
      }
      if($_SESSION['question_num'] === $_SESSION['num_train'] - 1){
        $_SESSION['final_answer_date'] = $trained_date;
      }
    }else{
      $error_message[] = 'something is wrong...';
    }
    $mysqli->close();
  }
}


//show training cards
function show_train_question($card_array){
  echo 'Q '.($_SESSION['question_num']+1)."/".$_SESSION['num_train']."<br>";
  if($_SESSION['is_front_array'][$_SESSION['question_num']]){ //if it is card front
    echo $card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_front']."<br>";
  }else{//if it is card back
    echo $card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_back']."<br>";
  }
  var_dump($_SESSION['question_array'][$_SESSION['question_num']]);
  echo "<br><br>";

  //when answer button is pushed
  if(!empty($_GET['answer'])){
    echo $card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_front']."<br>";
    echo $card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_back']."<br>";
  }
}

//fetch result table
//     SELECT * FROM `results` WHERE trained_date >= "$_SESSION['first_answer_date']" and trained_date <= "$_SESSION['final_answer_date']";

?>