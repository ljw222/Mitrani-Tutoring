<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

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
  <title>6th-12th</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
  <div class="top-page-div" id="gr612-div">
    <h1>6th - 12th Grade</h1>
    <div>
      <h2>What we offer</h2>
      <p>For middle through high school students, we provide report and research paper assistance, study skills and organizational strategies, and standardized test prep.</p>
    </div>
    <p class="source">Source: <a href="https://burst.shopify.com/photos/thoughtful-students-talk?q=education">Burst</a></p>
  </div>

  <div class="body-div">
    <h2>Our services</h2>
    <ul>
      <li>Reading: decoding and comprehension</li>
      <li>Math Skills</li>
      <li>Writing Skills</li>
      <li>Organizational skills</li>
      <li>Study Skills/Test Taking Skills</li>
      <li>Homework/Project Assistance</li>
    </ul>
  </div>

  <div class="body-div">
    <h2>Curious what other tutees have to say?</h2>
    <?php
      echo "<p class='link-testimony'><a href='testimonials.php?" . http_build_query(array('grade_filter' => array(6, 7, 8, 9, 10, 11, 12))) . "#testimonial_table'>Check out the testimonials from 6th - 12th grade students and parents.</a></p>";
    ?>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
