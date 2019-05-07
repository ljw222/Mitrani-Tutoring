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
  <title>About Me</title>
</head>

<body>
  <?php include("includes/header.php"); ?>

  <div class="top-page-div" id="about-div">
    <h1>About Me</h1>
    <div>
      <h2>Laurie Mitrani, Founder of Mitrani Tutoring</h2>
      <p>Welcome to Mitrani Tutoring! I would love to inform you about who I am and my background in education. If you have any questions, please feel free to reach out.</p>
    </div>
    <p class="source">Source: <a href="https://unsplash.com/photos/gqsY28obvH8">Unsplash</a></p>
  </div>

  <div class="body-div">
    <div class="about_flex">
      <div class="headshot">
        <!-- Source: Laurie Mitrani -->
        <img src="images/headshot.jpg" alt="An image of Laurie">
      </div>
      <div class="about_me">
        <!-- <p class="quote">I make learning fun, with proven results!</p> -->
        <p>My name is Laurie Mitrani, and I am a <strong>compassionate, child-oriented tutor</strong> who makes learning fun. I have been a <strong>certified teacher</strong> for 30+ years, and have a wide variety of teaching experience. I graduated with a Masters in Education from the University of Virginia, and have experience teaching preschool through high school age students. I have been a elementary classroom teacher, assistant principal, and a principal. In addition, I have <strong>specialized training</strong> in reading. I have been trained in the Orton-Gillingham Approach to help students with reading impairments such as dyslexia. I have also been trained in the Lindamood Bell Approach to help students with reading and comprehension.</p>
        <div id="whole-about-contacts">
          <h2>Learn More</h2>
          <div id="about-contacts">
            <div>
              <a href="mailto:lmitrani114@gmail.com"><img class="contact-icon" src="images/email-black.png" alt="email" /></a>
              <p>lmitrani114@gmail.com</p>
            </div>
            <div>
              <a href="tel:305-926-5537"><img class="contact-icon" src="images/phone-black.png" alt="phone" /></a>
              <p>(305)-926-5537<p>
            </div>
            <div>
              <a href="https://www.linkedin.com/in/laurie-mitrani-a69b28157" target='_blank'><img class="contact-icon" src="images/linkedin.png" alt="linkedin" /></a>
              <p>Laurie Mitrani on LinkedIn</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
