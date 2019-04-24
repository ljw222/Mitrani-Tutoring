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
      <p>Curious what our students and parents think of Mitrani Tutoring? Explore their testimonials below to find out.</p>
    </div>
  </div>

  <div class="body-div">
    <form id="sortby-form" action="testimonials.php" method="GET">
      <p>Sort by:</p>
      <select name="date">
        <?php
        // SQL QUERY FOR DATES
        // will save records as $all_dates, but temporary fake list
        $all_dates = [2012, 2013, 2014];
        echo "<option selected disabled>Date</option>";
        foreach ($all_dates as $date) {
          echo "<option value='" . $date . "'>" . $date . "</option>";
        }
        ?>
      </select>
      <select name="grade">
        <?php
        // SQL QUERY FOR GRADES
        $all_grades = [4, 5, 6];
        echo "<option selected disabled>Grade </option>";
        foreach ($all_grades as $grade) {
          echo "<option value='" . $grade . "'>" . $grade . "</option>";
        }
        ?>
      </select>
      <select name="rating">
        <?php
        // SQL QUERY FOR RATINGS
        $all_ratings = [1, 2, 3, 4, 5];
        echo "<option selected disabled>Rating </option>";
        foreach ($all_ratings as $rating) {
          echo "<option value='" . $rating . "'>" . $rating . "</option>";
        }
        ?>
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
