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
  define("DB_NAME",substr($url["path"],1));

  //when you select results, insert result to mysql
  function insert_result($result_card_id,$result,$is_front){
    date_default_timezone_set('Asia/Tokyo');
    $trained_date = date("Y/m/d H:i:s");
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    $mysqli->set_charset('utf8');/////////////////////////////////////

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
    $mysqli->set_charset('utf8');/////////////////////////////////////
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

    $num = 1;
    echo '<div class="result_row">';

      echo '<div class="result_no" style="background-color:white">No</div>';
      echo '<div class="result_question" style="background-color:white">Question</div>';
      echo '<div class="result_answer" style="background-color:white">Answer</div>';
      echo '<div class="result_okng" style="background-color:white">result</div>';

      foreach($result_array as $value){
        if($value['is_front']==="1"){ // if it is card front
          if($value['result']==="1"){ // if the answer is correct
            echo '<div class="result_no ok_color">'.$num.'</div>';
            echo '<div class="result_question ok_color">'.$value['card_front'].'</div><div class="result_answer ok_color">'.$value['card_back'].'</div>';
            echo '<div class="result_okng ok_color">OK</div>';
          }else{
            echo '<div class="result_no ng_color">'.$num.'</div>';
            echo '<div class="result_question ng_color">'.$value['card_front'].'</div><div class="result_answer ng_color">'.$value['card_back'].'</div>';
            echo '<div class="result_okng ng_color">NG</div>';
          }
        }else{
          if($value['result']==="1"){ 
            echo '<div class="result_no ok_color">'.$num.'</div>';
            echo '<div class="result_question ok_color">'.$value['card_back'].'</div><div class="result_answer ok_color">'.$value['card_front'].'</div>';
            echo '<div class="result_okng ok_color">OK</div>';
          }else{
            echo '<div class="result_no ng_color">'.$num.'</div>';
            echo '<div class="result_question ng_color">'.$value['card_back'].'</div><div class="result_answer ng_color">'.$value['card_front'].'</div>';
            echo '<div class="result_okng ng_color">NG</div>';
          }
        }
        $num += 1;
      }
    echo '</div>';
    return $result_array;
  }
?>