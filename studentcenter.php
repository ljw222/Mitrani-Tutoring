<?php
   // DO NOT REMOVE!
   include("includes/init.php");
   // DO NOT REMOVE!
//Delete appointment
$deleted_appt = FALSE;
if (isset($_POST['cancel_appointment'])) {
  $appt_to_delete = intval($_GET['appt_to_delete']);
  //Modify times table to show the time is now available
    //get id of time slot
  $sql = "SELECT time_id FROM appointments WHERE id = :appt_to_delete;";
  $params = array(
    ':appt_to_delete' => $appt_to_delete
  );
  $result = exec_sql_query($db, $sql, $params)->fetchAll();
  $time_id = $result[0][0];
  $sql = "UPDATE times SET available = 1 WHERE id = :time_id";
  $params = array(
    ':time_id' => $time_id
  );
  $result = exec_sql_query($db, $sql, $params);
  //Delete from appointmnets table
  $sql = "DELETE FROM appointments WHERE id = :appt_to_delete;";
  $params = array(
    ':appt_to_delete' => $appt_to_delete
  );
  $result = exec_sql_query($db, $sql, $params);
  //Delete from appointment_subjects table
  $sql = "DELETE FROM appointment_subjects WHERE appointment_id = :appt_to_delete;";
  $params = array(
    ':appt_to_delete' => $appt_to_delete
  );
  $result = exec_sql_query($db, $sql, $params);
  //cancel appt complete
  $deleted_appt = TRUE;
}
// //re-format date input
// function format_date($date) {
//   $pieces = explode("-", $date);
//   return ($pieces[1] . '/' . $pieces[2] . '/' . $pieces[0]);
// }
if (isset($_POST['submit_testimony'])) {
   echo testimonial_php();
}
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <link href="styles/all.css" type="text/css" rel="stylesheet">
      <title>Student Center</title>
   </head>
   <body>
      <?php include("includes/header.php"); ?>
      <div class="top-page-div" id="studentcenter-div">
         <h1>Student Center</h1>
         <div>
            <?php
               if (is_user_logged_in()) {
                 echo "<h2>Welcome Back, " . htmlspecialchars($current_user['first_name']) . " " . htmlspecialchars($current_user['last_name']) . "!</h2>";
                 echo "<p>In the Student Center, you can view existing appointments, edit appointments, schedule a new appointment, or cancel an appointment.</p>";
               } else {
                 echo "<h2>What is the Student Center?</h2>";
                 echo "<p>The Mitrani Tutoring Student Center is a place for students and parents to track and schedule tutoring sessions with Laurie. Sign in to access these exclusive tools!</p>";
               }
               ?>
         </div>
         <p class="source">Source: <a href="https://www.pexels.com/photo/desk-office-pen-ruler-2097/">Pexels</a></p>
      </div>
  <?php
  if (!is_user_logged_in()) { ?>
    <div class="body-div">
      <form id="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#login_form" method="post">
      <?php
        foreach ($session_messages as $session_messages) {
          echo "<p class='error'>" . $session_messages . "</p>";
        }
      ?>
        <ul>
          <li>
            <label for="username" class="text_label">Username:</label>
            <input id="username" type="text" name="username" />
          </li>
          <li>
            <label for="password" class="text_label">Password:</label>
            <input id="password" type="password" name="password" />
          </li>
          <li>
            <button name="login" type="submit">Sign In</button>
          </li>
        </ul>
      </form>
    </div>
  <?php
} else {
  if (isset($_POST["submit"]) && is_user_logged_in()) {
    // filter input for upload
    $date = format_date($_POST["date"]);
    $time = $_POST['start_time']; //filter input
    //convert out of military time
    $start_time = date("h:i", strtotime($time));
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $end_time = date('h:i',strtotime('+1 hour',strtotime($time)));
    // check if given date + time overlaps with any other apptmt start or end time frames
    $sql = "SELECT * FROM appointments WHERE appointments.date = :date AND (:start_time <= appointments.time_start <= :end_time) OR (:start_time <= appointments.time_end <= :end_time) ";
    $params = array(
      ':date' => $date,
      ':start_time' => $start_time,
      ':end_time' => $end_time
    );
    $time_overlap = exec_sql_query($db, $sql, $params)->fetchAll();
    if (count($time_overlap) > 0) { // if overlap
      $time_is_available = FALSE;
    } else {
      $time_is_available = TRUE;
    }
    // $available = exec_sql_query($db, $sql, $params)->fetchAll();
    // $time_is_available = false;
    // var_dump(intval($available[0]));
    // if(intval($available[0]) == 1){
    //     $time_is_available = true;
    // }
    // var_dump($time_is_available);
    //validate form -- messages
    $valid_field = true;
    if ($date == NULL){
        $valid_field = false;
        $valid_date = false;
    }
    if ($time == NULL){
        $valid_field = false;
        $valid_time = false;
    }
    if ($all_subject == NULL){
        $valid_field = false;
        $valid_subject = false;
    }
    //Upload Time of Appointment
    if ($upload_info['error']== UPLOAD_ERR_OK && $time_is_available && $valid_field) {
      // get id for start time
      // $sql = "SELECT times.id FROM times WHERE times.time_start = '$start_time' AND times.date = '$date'";
      // $params = array();
      // $result = exec_sql_query($db, $sql, $params)->fetchAll();
      // $sql = "INSERT INTO 'appointments' (time_id, user_id, comment) VALUES (:time_id, :user_id, :comment);";
      $sql = "INSERT INTO appointments (date,time_start,time_end,half,comment,user_id) VALUES (:date,:time_start,:time_end,:half,:comment,:user_id)";
      $params = array(
        ':date' => $date,
        ':time_start' => $time_start,
        ':time_end' => $time_end,
        ':comment' => $comment,
        ':user_id' => $current_user['id']
        );
      $result = exec_sql_query($db, $sql, $params);
      // check for each subject that has been checked, insert respective subject id
      $new_id =intval($db->lastInsertId("id"));
      $all_subjects = array(1=>'reading',2=>'math',3=>'writing',4=>'organization',5=>'study',6=>'test',7=>'homework',8=>'project');
      foreach($all_subjects as $all_subject){
        $subj_id = array_search($all_subject, $all_subjects);
        if (isset($_POST[$all_subject])){
          $sql = "INSERT INTO 'appointment_subjects' (appointment_id, subject_id) VALUES ($new_id, $subj_id);";
          $params = array();
          $result = exec_sql_query($db, $sql, $params);
        }
        $sql = "UPDATE times SET available = 0 WHERE times.time_start = '$start_time' AND times.date = '$date'";
        $params = array();
        $result = exec_sql_query($db, $sql, $params);
      }
    }
    else{
        ?>
        <p class='error'> "This time slot is not available. Please make an appointment with an open time slot."</p>
    <?php
    }
  }
  ?>
    <div class="body-div" id="existing_appointments_div">
      <?php
        if ($deleted_appt) {
          echo "<p class='success'>Appointment successfully cancelled!</p>";
        }
      ?>
      <h2>Existing appointments</h2>
      <?php
      $sql = "SELECT DISTINCT appointments.id, appointments.date, appointments.time_start, appointments.half, appointments.comment FROM appointments
        JOIN appointment_subjects ON appointments.id = appointment_subjects.appointment_id
        JOIN subjects ON appointment_subjects.subject_id = subjects.id
        WHERE appointments.user_id = :user_id;";
      $params = array(
        ':user_id' => $current_user['id']
        );
      $result = exec_sql_query($db, $sql, $params);
      if ($result) {
        $records = $result->fetchAll();
        if (count($records) > 0) { // if there are records
          ?>
          <div class="table-div">
            <table>
               <tr>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Details</th>
               </tr>
               <?php
                  foreach ($records as $record) {
                    print_appt($record);
                  }
                  ?>
            </table>
         </div>
         <?php
        } else {
            echo "<p class='no_appt'>You do not have any scheduled appointments.</p>";
        }
            } ?>
      </div>
      <!-- Appointment Form -->
      <div class="body-div">
         <div class="form-div">
            <form id="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#signup_form" method="post">
               <h2>Schedule an Appointment</h2>
               <ul>
                  <li>
                     <div class="form_label">
                     <p class="error <?php if(!isset($valid_date)) { echo "hidden";} ?>">Please enter a valid date</p>
                        <p class="required">*</p>
                        <label for="date">Date:</label>
                     </div>
                     <input class="input_box" id="date" type="date" name="date" value="2019-04-29"/>
                  </li>
                  <li>
                     <div class="form_label">
                     <p class="error <?php if(!isset($valid_time)) { echo "hidden";} ?>">Please enter a valid time, between 9 AM and 7 PM</p>
                        <p class="required">*</p>
                        <label for="time">Start Time:</label>
                     </div>
                     <input class="input_box" type="time" id="time" name="start_time" min="9:00" max="19:00" value="15:00">
                  </li>
                  <!-- <li>
                     <div class="form_label">
                        <p class="required">*</p>
                        <label for="time">End Time:</label>
                     </div>
                     <input class="input_box" type="time" id="time" name="end_time" min="9:00" max="17:00">
                  </li> -->
                  <li>
                     <div class="form_label">
                     <p class="error <?php if(!isset($valid_subject)) { echo "hidden";} ?>">Please select a subject for your appointment</p>
                        <p class="required">*</p>
                        <label>Subject(s):</label>
                     </div>
                     <p class="subject"><input type="checkbox" name="math" value="math"> Math</p>
                     <p class="subject"><input type="checkbox" name="reading" value="reading"> Reading</p>
                     <p class="subject"><input type="checkbox" name="writing" value="writing"> Writing</p>
                     <p class="subject"><input type="checkbox" name="homework" value="homework"> Homework Help</p>
                     <p class="subject"><input type="checkbox" name="project" value="project"> Project Assistance</p>
                     <p class="subject"><input type="checkbox" name="organization" value="organization"> Organizational Skills</p>
                     <p class="subject"><input type="checkbox" name="study" value="study"> Study Skills</p>
                     <p class="subject"><input type="checkbox" name="test" value="test"> Standardized Test Preparation</p>
                  </li>
                  <div id="comment">
                     <div class="form_label">
                        <label for="comment">Comment:</label>
                     </div>
                     <textarea rows=5 cols=40 name="comment" id="comment" ></textarea>
                  </div>
                  <li>
                     <button name="submit" type="submit">Submit</button>
                  </li>
               </ul>
            </form>
         </div>
      </div>
      <div class="body-div">
         <?php include("includes/testimonial_form.php");?>
      </div>
      <?php
         } ?>
      <?php include("includes/footer.php"); ?>
   </body>
