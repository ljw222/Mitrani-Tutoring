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
  <title>About Me</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
  <div class="top-page-div" id="prek5-div">
    <h1>Pre-K - 5th Grade</h1>
    <div>
      <h2>What we offer</h2>
      <p>We use various electronic and print materials, consult with teachers and school administrators, provide data-driven instruction, and aid in standardized test prep.</p>
    </div>
    <!-- <p class="source">Source: <a href="https://www.pexels.com/photo/girls-on-desk-looking-at-notebook-159823/">Pexels</a></p> -->
    <p class="source">Source: <a href="https://www.pexels.com/photo/girl-in-red-short-sleeve-dress-and-flower-headband-holding-pen-and-writing-on-paper-on-table-159782/">Pexels</a></p>
  </div>

  <div class="body-div">
    <h2>Our services</h2>
    <ul>
      <li>Reading: decoding and comprehension</li>
      <li>Math Skills: computation and word problems</li>
      <li>Writing Skills</li>
      <li>Organizational skills</li>
      <li>Study skills (upper elementary)</li>
      <li>Homework assistance</li>
      <li>Standardized Test prep</li>
    </ul>
    <p>Our resources include: <em>Explode the Code; Recipe for Reading; Lindamood-Bell programs for reading, decoding, and reading comprehension; Project-Based Learning;</em> and <em>Writing Workshop</em>, among others.
  </div>

  <div class="body-div">
    <?php
    if (is_user_logged_in()) {
      echo "<p class='link-testimony'><em>Check out the <a href='testimonials.php?" . http_build_query(array('grade_filter' => array(0, 1, 2, 3, 4, 5))) . "#testimonial_table'>testimonials from Pre-K - 5th grade </a>students and parents, or submit your own below!</em></p>";
      include("includes/testimonial_form.php");
    } else {
      echo "<h2>Curious what other tutees have to say?</h2>";
      echo "<p class='link-testimony'><a href='testimonials.php?" . http_build_query(array('grade_filter' => array(0, 1, 2, 3, 4, 5))) . "#testimonial_table'>Check out the testimonials from Pre-K - 5th grade students and parents.</a></p>";
    }; ?>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
