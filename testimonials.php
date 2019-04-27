<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

if (isset($_POST["submit-sortby"])) {
  $ready_to_show = FALSE;
  $sort_values = array();
  $given_sorts = array();

  // date
  if (isset($_POST["date"])) {
    $date = filter_input(INPUT_POST, "date", FILTER_VALIDATE_INT);
    array_push($sort_values, "testimonials.date = :date");
    $given_sorts[':date'] = $date;
  }
  // grade
  if (isset($_POST["grade"])) {
    $grade = filter_input(INPUT_POST, "grade", FILTER_VALIDATE_INT);
    array_push($sort_values, "users.grade = :grade");
    $given_sorts[':grade'] = $grade;
  }
  // rating
  if (isset($_POST["rating"])) {
    $rating = filter_input(INPUT_POST, "rating", FILTER_VALIDATE_INT);
    array_push($sort_values, "testimonials.rating = :rating");
    $given_sorts[':rating'] = $rating;
  }
  // role
  if (isset($_POST["role"])) {
    $role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_STRING);
    array_push($sort_values, "testimonials.role = :role");
    $given_sorts[':role'] = $role;
  }

  if ($given_sorts != []) { // if specified sort by
    $sort_values_str = implode(" AND ", $sort_values); // make string from values
    // https://php.net/manual/en/function.implode.php
  }

  if (isset($sort_values_str)) { // specified WHERE ...
    $sql = "SELECT testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id WHERE $sort_values_str";
    $result = exec_sql_query($db, $sql, $params = $given_sorts);
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are records
        $ready_to_show = TRUE;
      } else {
        $ready_to_show = FALSE;
      }
    }
  } else { // show all
    $sql = "SELECT testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id";
    $result = exec_sql_query($db, $sql, $params = array());
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are records
        $ready_to_show = TRUE;
      } else {
        $ready_to_show = FALSE;
      }
    }
  }
}

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
    <form id="sortby-form" action="testimonials.php" method="POST">
      <p>Sort by:</p>
      <select name="date">
        <?php
        // SQL QUERY FOR DATES
        $sql = "SELECT DISTINCT date FROM testimonials";
        $result = exec_sql_query($db, $sql, $params = array());
        $all_dates = $result->fetchAll();
        echo "<option selected disabled>Date</option>";
        foreach ($all_dates as $date) {
          echo "<option value='" . $date["date"] . "'>" . $date["date"] . "</option>";
        }
        ?>
      </select>
      <select name="grade">
        <?php
        // SQL QUERY FOR GRADES
        $sql = "SELECT DISTINCT grade FROM users JOIN testimonials ON users.id = testimonials.user_id";
        $result = exec_sql_query($db, $sql, $params = array());
        $all_grades = $result->fetchAll();
        echo "<option selected disabled>Grade </option>";
        foreach ($all_grades as $grade) {
          echo "<option value='" . $grade["grade"] . "'>" . $grade["grade"] . "</option>";
        }
        ?>
      </select>
      <select name="rating">
        <?php
        // SQL QUERY FOR RATINGS
        $result = exec_sql_query($db, "SELECT DISTINCT rating FROM testimonials", $params = array());
        $all_ratings = $result->fetchAll();
        echo "<option selected disabled>Rating </option>";
        foreach ($all_ratings as $rating) {
          echo "<option value='" . $rating["rating"] . "'>" . $rating["rating"] . "</option>";
        }
        ?>
      </select>
      <select name="role">
        <?php
        // SQL QUERY FOR ROLES
        $result = exec_sql_query($db, "SELECT DISTINCT role FROM testimonials", $params = array());
        $all_roles = $result->fetchAll();
        echo "<option selected disabled>Role</option>";
        foreach ($all_roles as $role) {
          echo "<option value='" . $role["role"] . "'>" . $role["role"] . "</option>";
        }
        ?>
      </select>
      <button type="submit" name="submit-sortby">Go</button>
    </form>

    <p>WILL INSERT TABLE HERE</p>
    <?php
    if (isset($ready_to_show) && $ready_to_show) {
      ?>
      <div class="table-div">
        <table>
          <tr>
            <th>Testimonial</th>
            <th>Rating</th>
            <th>Grade</th>
            <th>Date</th>
            <th>Role</th>
          </tr>
          <?php
          foreach ($records as $record) {
            print_record($record);
          }
          ?>
        </table>
      </div>
    <?php
  } else {
    $sql = "SELECT testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id";
    $result = exec_sql_query($db, $sql, $params = array());
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are records
        ?>
          <div class="table-div">
            <table>
              <tr>
                <th>Testimonial</th>
                <th>Rating</th>
                <th>Grade</th>
                <th>Date</th>
                <th>Role</th>
              </tr>
              <?php
              foreach ($records as $record) {
                print_record($record);
              }
              ?>
            </table>
          </div>
        <?php
      }
    }
  }
  ?>
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
