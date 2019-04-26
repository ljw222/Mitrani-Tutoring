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
  <title>Testimonials</title>
</head>

<body>
  <?php include("includes/header.php"); ?>

  <div class="top-page-div" id="testimonials-div">
    <h1>Testimonials</h1>
    <div>
      <h2>About the testimonials</h2>
      <p>These reviews were submitted by past and current students and parents of Mitrani Tutoring. Curious what they thought of their experiences? Explore below to find out.</p>
    </div>
    <p class="source">Source: <a href="https://unsplash.com/photos/_lhefRJtT0U">Unsplash</a></p>
  </div>

  <div class="body-div">
    <form id="sortby-form" action="testimonials.php" method="GET">
      <p>Sort by:</p>
      <select name="date">
        <?php
        // SQL QUERY FOR DATES
        $result = exec_sql_query($db, "SELECT date FROM testimonials", $params = array());
        $all_dates = $result->fetchAll();
        echo "<option selected disabled>Date</option>";
        foreach ($all_dates as $date) {
          echo "<option value='" . $date . "'>" . $date . "</option>";
        }
        ?>
      </select>
      <select name="grade">
        <?php
        // SQL QUERY FOR GRADES
        $sql = "SELECT grade FROM users JOIN testimonials ON users.id = testimonials.user_id";
        $result = exec_sql_query($db, $sql, $params = array());
        $all_grades = $result->fetchAll();
        echo "<option selected disabled>Grade </option>";
        foreach ($all_grades as $grade) {
          echo "<option value='" . $grade . "'>" . $grade . "</option>";
        }
        ?>
      </select>
      <select name="rating">
        <?php
        // SQL QUERY FOR RATINGS
        $result = exec_sql_query($db, "SELECT rating FROM testimonials", $params = array());
        $all_ratings = $result->fetchAll();
        echo "<option selected disabled>Rating </option>";
        foreach ($all_ratings as $rating) {
          echo "<option value='" . $rating . "'>" . $rating . "</option>";
        }
        ?>
      </select>
      <select name="role" id="role">
        <option selected disabled>Role</option>
        <option value="parent">Parent</option>
        <option value="student">Student</option>
      </select>
      <button type="submit" name="submit-sortby">Go</button>
    </form>

    <p>WILL INSERT TABLE HERE</p>
    <a href="single_testimony.php" target="_blank">single_testimony</a>
  </div>

  <div class="body-div">
    <?php
    if (is_user_logged_in()) {
      include("includes/testimonial_form.php");
    } else {
      echo "<h2>Want to submit your own testimony?</h2>";
      echo "<p><a href='studentcenter.php'>Please login to our Student Center.</a></p>";
    }; ?>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
