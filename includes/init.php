<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}

// ^^^ DO NOT MODIFY/REMOVE ^^^

// You may place any of your code here.
/* Source: Kyle Harms, Lecture 17: lectures/17-sessions/session-walkthrough/login-logout.php*/

define('SESSION_COOKIE_DURATION', 60*60*3); //3hrs
$session_messages = array();
function log_in($username, $password) {
  global $db;
  global $current_user;
  global $session_messages;
  //if the username and password are set, aka if someone tries to login
  if ( isset($username) && isset($password) ) {
    $sql = "SELECT * FROM users WHERE username = :username;";
    $params = array(
        ':username' => $username
    );
    //gets the stored/registered user with the same username
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    //if there is a matching record
    if ($records) {
        $account = $records[0];
        //checks if the password is correct, if so, a new session is started
        if ( password_verify($password, $account['password']) ) {
            $session = session_create_id();
            $sql = "INSERT INTO sessions (user_id, session) VALUES (:user_id, :session);";
            $params = array(
                ':user_id' => $account['id'],
                ':session' => $session
        );
        //store session ID into database
        $result = exec_sql_query($db, $sql, $params);
        if ($result) {
            setcookie("session", $session, time() + SESSION_COOKIE_DURATION);
            $current_user = $account;
            return $current_user;
        } else {
            array_push($session_messages, "Log in failed.");
        }
        } else {
        array_push($session_messages, "Invalid username or password.");
        }
    } else {
        array_push($session_messages, "Invalid username or password.");
    }
  } else {
        array_push($session_messages, "No username or password given.");
  }
  $current_user = NULL;
  return NULL;
}

//finds the user with the given user id
function find_user($user_id) {
    global $db;
    $sql = "SELECT * FROM users WHERE id = :user_id;";
    $params = array(
        ':user_id' => $user_id
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
        return $records[0];
    }
    return 'Anonymous';
}

//finds the session with the given session id
function find_session($session) {
    global $db;
    if (isset($session)) {
        $sql = "SELECT * FROM sessions WHERE session = :session;";
        $params = array(
            ':session' => $session
        );
        $records = exec_sql_query($db, $sql, $params)->fetchAll();
        if ($records) {
            // sessions are unique, so there should only be 1 record
            return $records[0];
        }
    }
    return NULL;
}

//updates the session
function session_login() {
    global $db;
    global $current_user;
    //if a new cookie is set..
    if (isset($_COOKIE["session"])) {
        $session = $_COOKIE["session"];
        $session_record = find_session($session);
        //if there is an existing session, renew the cookie for 3 hrs
        if ( isset($session_record) ) {
            $current_user = find_user($session_record['user_id']);
            setcookie("session", $session, time() + SESSION_COOKIE_DURATION);
            return $current_user;
        }
    }
    $current_user = NULL;
    return NULL;
}

function is_user_logged_in() {
    global $current_user;
    return ($current_user != NULL);
}

//log the user out by going back in time to force the cookie session expire
function log_out() {
    global $current_user;
    setcookie('session', '', time() - SESSION_COOKIE_DURATION);
    $current_user = NULL;
}

$db = open_or_init_sqlite_db('secure/gallery.sqlite', 'secure/init.sql');


//check to see if user should be logged in, if the username and password is entered, try to login. If not, check if already logged in via a cookie
if ( isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password']) ) {
    $username = trim( $_POST['username'] );
    $password = trim( $_POST['password'] );
    log_in($username, $password);
} else {
    session_login();
}

//if there is currently someone signed in and they want to sign out, they can logout
if ( isset($current_user) && ( isset($_GET['logout']) || isset($_POST['logout']) ) ) {
    log_out();
}

function print_stars($num_of_stars) {
  for ($star = 0; $star < $num_of_stars; $star++) {
    echo "<img class='rating_star' src='images/star.png' alt='rating star'/>";
  }
}

function print_record($record) {
  $categories = ["testimonial", "rating", "grade", "date", "role"];
  echo "<tr>";
  foreach ($categories as $category) {
    if ($category == "rating") {
      echo "<td class='rating-div'>";
      echo print_stars($record["rating"]);
      echo "</td>";
    } elseif ($category == "testimonial") {
      if( !$record['id'] == 0 ){
        echo "<td class='testimonial-div'><a href='single_testimony.php?" . http_build_query(array('id' => $record["id"]))."'>" . substr($record["testimonial"], 0, 90) . "...</a></td>";
      }
      else{
        echo "<td class='testimonial-div'><a href='single_testimony.php?" . http_build_query(array('id' => $record["id"]))."'>" . substr($record["testimonial"], 0, 90) . "...</a></td>";
      }

    } else {
      echo "<td>" . $record[$category] . "</td>";
    }
  }
  echo "</tr>";
}

function print_appt($record) {
  $categories = ["date", "time", "subjects"];
  echo "<tr>";
  foreach ($categories as $category) {
    if ($category == "date") {
      echo "<td class='rating-div'>";
      echo $record["date"];
      echo "</td>";
    } elseif ($category == "time") {
      echo "<td class='rating-div'>";
      echo $record["time_start"]. "-". $record["time_end"]. " ". $record['half'];
      echo "</td>";
    } else{
      echo "<td class='testimonial-div'><a href='single_appointment.php?" . http_build_query(array('time_id' => $record["id"], 'date' => $record["date"], 'start_time' => $record["time_start"], 'end_time' => $record["time_end"], 'half' => $record["half"]))."'>" . 'View Appointment' . "</a></td>";
    }
  }
  echo "</tr>";
}

$testimonial_error_messages = array();
$testimonial_success_messages = array();

function testimonial_php() {
  global $current_user;
  global $db;
  global $testimonial_error_messages;
  global $testimonial_success_messages;

  if (isset($_POST['submit_testimony'])) {
    // testimonial
    $valid_testimonial = FALSE;
    $valid_rating = FALSE;
    $valid_role = FALSE;

    if (isset($_POST['form_testimonial'])) {
      $testimonial = filter_input(INPUT_POST, "form_testimonial", FILTER_SANITIZE_STRING);
      if ($testimonial != '') { // if not empty
        $valid_testimonial = TRUE;
      } else {
        $valid_testimonial = FALSE;
      }
    } else {
      $valid_testimonial = FALSE;
    }
    // rating
    if (isset($_POST['form_rating'])) {
      $rating = filter_input(INPUT_POST, "form_rating", FILTER_VALIDATE_INT);
      $valid_rating = TRUE;
    } else {
      $valid_rating = FALSE;
    }
    // role
    if (isset($_POST['form_role'])) {
      $role = filter_input(INPUT_POST, "form_role", FILTER_SANITIZE_STRING);
      $valid_role = TRUE;
    } else {
      $valid_role = FALSE;
    }

    // name
    if( !isset($_POST['anonymous']) ){
      $user_id = $current_user['id'];
    }
    else{
      $user_id = 0;
    }
    if ($valid_testimonial && $valid_rating && $valid_role) { // all valid
      $date = date("Y");
      // insert into testimonials
      $sql = "INSERT INTO testimonials (testimonial, rating, date, role, user_id) VALUES (:testimonial, :rating, :date, :role, :user_id)";
      $params = array(
        ':testimonial' => $testimonial,
        ':rating' => $rating,
        ':date' => $date,
        ':role' => $role,
        ':user_id' => $user_id
      );
      $results = exec_sql_query($db, $sql, $params);
      if ($results) { // successful exec
        array_push($testimonial_success_messages, "Thank you for submitting your testimonial!");
      } else {
        array_push($testimonial_error_messages, "Something went wrong. Failed to submit testimonial.");
      }
    } else {
      array_push($testimonial_error_messages, "One of more of the fields was not filled in correctly. Failed to submit testimonial.");
    }
  }
}

?>
