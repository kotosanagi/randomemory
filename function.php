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
    if($_SESSION['is_front_array'][$_SESSION['question_num']]){ //if it is card front
      echo '<div class="question_card">'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_front'].'</div>';
    }else{//if it is card back
      echo '<div class="question_card">'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_back']."</div>";
    }

    echo '<div class="question_card">???</div>';
    // var_dump($_SESSION['question_array'][$_SESSION['question_num']]);
    // echo "<br>";
    // var_dump($card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_id']);
    // echo "<br><br>";
  }

  //show temporary answer
  function show_temp_answer($card_array){
    // echo "it's 'show_temp_answer' function";

    //if is_front is 1 (it means it's front)
    if($_SESSION['is_front_array'][$_SESSION['question_num']]){ //if it is card front
      echo '<div class="question_card">'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_front'].'</div>';
      echo '<div class="question_card" style="color:#f55">'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_back']."</div>";
      // echo 'this card is front';
    }
    else{//if is_front is 0 (it means it's back)
      echo '<div class="question_card">'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_back']."</div>";
      echo '<div class="question_card" style="color:#f55" >'.$card_array[$_SESSION['question_array'][$_SESSION['question_num']]-1]['card_front'].'</div>';
      // echo 'this card is back';
    }
  }

  //show result table
  function show_results(){
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if($mysqli->connect_errno){
      $error_message[] = "error! ".$mysqli->connect_errno. " : ".$mysqli->connect_error;
    }else{
      $sql = "SELECT * FROM results INNER JOIN cards ON results.result_card_id = cards.card_id WHERE trained_date >='".$_SESSION['first_answer_date']."' and trained_date <= '".$_SESSION['final_answer_date']."'";
      $res = $mysqli->query($sql);
      if($res){
        $result_array = $res->fetch_all(MYSQLI_ASSOC);
      }
      $mysqli->close();
    }
    // var_dump($result_array);
    // echo "<br>";
    // echo "<br>";
    // echo "<br>"; 
    echo "your result is .....";
    echo "<br>";
    $num = 1;
    foreach($result_array as $value){
      echo $num." : ";
      if($value['is_front']==="1"){ // if it is card front
        echo $value['card_front']." -> ".$value['card_back'];
        if($value['result']==="1"){ // if the answer is correct
          echo "   ...   OK";
        }else{
          echo "   ...   NG";
        }
        echo "<br>";
      }else{
        echo $value['card_back']." -> ".$value['card_front'];
        if($value['result']==="1"){ 
          echo "   ...   OK";
        }else{
          echo "   ...   NG";
        }
        echo "<br>";
      }
      $num += 1;
    }
    return $result_array;
  }
?>