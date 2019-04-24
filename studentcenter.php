<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
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
    <form id="login_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <fieldset>
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
      </fieldset>
    </form>
  <?php
} else { ?>
    <p>Existing appointments:</p>
    <!-- TEMP -->
    <a href="single_appointment.php" target="_blank">single_appointment</a>

    <form id="signup_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <fieldset>
        <legend>Schedule an appointment!</legend>
        <ul>
          <!-- <li>
                      <label for="firstname" class="text_label">Student First Name:</label>
                      <input id="firstname" type="text" name="firstname" />
                    </li>
                    <li>
                      <label for="lastname" class="text_label">Student Last Name:</label>
                      <input id="lastname" type="text" name="lastname" />
                    </li> -->
          <li>
            <label for="date" class="text_label">Date:</label>
            <input id="date" type="date" name="date" />
          </li>
          <li>
            <label for="time" class="text_label">Time:</label>
            <input type="time" id="time" name="time" min="9:00" max="17:00">
          </li>
          <li>
            <label class="text_label">Subject:</label>
            <p class="subject"><input type="checkbox" name="math" value="math"> Math</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> Reading</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Writing</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> History</p>
            <p class="subject"><input type="checkbox" name="vehicle2" value="Car"> Science</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Organizational Skills</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Study Skills</p>
            <p class="subject"><input type="checkbox" name="vehicle3" value="Boat"> Standardized Test Preparation</p>
            <p class="subject"><input type="checkbox" value="other" name="other_check">Other:
              <input type="text" name="other_tag"></p>
          </li>
          <li>
            <label for="duration" class="text_label">Duration:</label>
            <select name="duration">
              <option value="30">30 minutes</option>
              <option value="45">45 minutes</option>
              <option value="60">60 minutes</option>
            </select>
          </li>
          <li>
            <button name="submit" type="submit">Submit</button>
          </li>
        </ul>
      </fieldset>
    </form>
  <?php
} ?>
  <legend>

    <?php include("includes/footer.php"); ?>
</body>
