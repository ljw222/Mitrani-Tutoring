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
  <title>Home</title>
</head>

<body>
  <?php include("includes/header.php"); ?>

  <div class="top-page-div">
    <!-- <img src="images/academic-setup.jpg" alt="background" /> -->
    <!-- Photo by Element5 Digital on Unsplash -->
    <!-- <img src="images/elementary.jpg" alt="young girl studying" /> -->
    <!-- Photo by pan xiaozhen on Unsplash -->
    <!-- elementary children -->
    <!-- https://www.pexels.com/photo/girls-on-desk-looking-at-notebook-159823/ -->
    <!-- art materials: https://www.pexels.com/photo/art-art-materials-brush-color-207666/ -->
    <!-- office setup: https://www.pexels.com/photo/desk-office-pen-ruler-2097/ -->
    <!-- middle school: https://www.pexels.com/photo/homework-paper-pen-person-267491/ -->
    <!-- high school: https://burst.shopify.com/photos/thoughtful-students-talk?q=education -->
    <!-- elementary3: https://www.pexels.com/photo/girl-in-red-short-sleeve-dress-and-flower-headband-holding-pen-and-writing-on-paper-on-table-159782/ -->
    <h1>Mitrani Tutoring</h1>
    <div>
      <h2>Who we are</h2>
      <p>Mitrani Tutoring enables students from Pre-K through high school to excel in their academic life. Provided by certified educator Laurie Mitrani, Mitrani Tutoring offers services in a multitude of subjects, tailored to each student's individual needs and interests. Tutoring is also offered in various locations.</p>
    </div>
    <p class="source">Source: <a href="https://unsplash.com/photos/gqsY28obvH8">Unsplash</a></p>
  </div>

  <div class="body-div">
    <h2>What we offer</h2>
    <ul>
      <li>
        Remedial and enrichment services, individually created based on student’s needs and interests
      </li>
      <li>
        Integration of several areas of study, including STEM, writing, science, social studies, and more
      </li>
      <li>
        Many locations — in school, at home, in office, through FaceTime, and more
      </li>
      <li>
        Regular communication with parents and teachers if requested
      </li>
      <li>
        Competitive rates
      </li>
    </ul>
  </div>

  <div class="body-div">
    <h2>Why tutoring matters</h2>
    <p>[Insert excerpt from Laurie about why tutoring matters.]</p>
  </div>

  <div class="body-div">
    <?php
    if (is_user_logged_in()) {
      include("includes/testimonial_form.php");
    } else {
      echo "<h2>Don't just take our word for it!</h2>";
      echo "<p class='link-testimony'><a href='testimonials.php'>See what our students and parents have to say in their testimonials.</a></p>";
    }; ?>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
