<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!

//Delete testimonial
$deleted_test = FALSE;
if (isset($_POST['delete_testimonial'])) {
  $testimonial_to_delete = intval($_GET['testimonial_to_delete']);
  $sql = "DELETE FROM testimonials WHERE id = :testimonial_to_delete;";
  $params = array(
    ':testimonial_to_delete' => $testimonial_to_delete
  );
  $result = exec_sql_query($db, $sql, $params);
  $deleted_test = TRUE;
}


if (isset($_POST['submit_testimony'])) {
  echo testimonial_php();
}

$error_messages = array();
$filter_messages = array();

// filter by grades: Pre-K - 5 OR 6-12
if (isset($_GET['grade_filter'])) {
  $grade_filter = $_GET['grade_filter'];
  $grade_filter_list = array();
  foreach ($grade_filter as $grade) {
    array_push($grade_filter_list, "users.grade = $grade");
  }
  $grade_filter_str = implode(" OR ", $grade_filter_list);
  $sql = "SELECT testimonials.id, testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id WHERE $grade_filter_str";
  $result = exec_sql_query($db, $sql, $params = array());
  if ($result) {
    $records = $result->fetchAll();
    if (count($records) > 0) { // if there are records
      $ready_to_show = TRUE;
      if ($grade_filter == [0, 1, 2, 3, 4, 5]) {
        array_push($filter_messages, "Testimonials from Pre-K - 5th grades");
      } elseif ($grade_filter == [6, 7, 8, 9, 10, 11, 12]) {
        array_push($filter_messages, "Testimonials from 6th - 12th grades");
      }
    } else {
      $ready_to_show = FALSE;
    }
  }
}

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
    $sql = "SELECT testimonials.id, testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id WHERE $sort_values_str ORDER BY testimonials.date DESC";
    $result = exec_sql_query($db, $sql, $params = $given_sorts);
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are matching records
        $ready_to_show = TRUE;
      } else {
        $ready_to_show = FALSE;
        array_push($error_messages, "Sorry, none of the testimonials matched your sort-by request. Please adjust the constraints and try again.");
      }
    }
  } else { // show all
    $sql = "SELECT testimonials.id, testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id ORDER BY testimonials.date DESC";
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

if (isset($_POST['reset-sortby'])) {
  unset($_POST['date']);
  unset($_POST['grade']);
  unset($_POST['rating']);
  unset($_POST['role']);
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

  <div class="body-div" id="testimonial-table-div">
    <form id="sortby-form" action="testimonials.php#testimonial-table-div" method="POST">
      <p>Filter by:</p>
      <select name="date" <?php if (isset($_POST['date'])) {
                            echo "class = 'selected'";
                          } ?>>
        <?php
        // SQL QUERY FOR DATES
        $sql = "SELECT DISTINCT date FROM testimonials ORDER BY date DESC";
        $result = exec_sql_query($db, $sql, $params = array());
        $all_dates = $result->fetchAll();
        echo "<option selected disabled>Date</option>";
        foreach ($all_dates as $date) {
          if (isset($_POST['date']) && ($_POST['date'] == $date['date'])) {
            $selected = "selected = 'selected' class='selected-option'";
          } else {
            $selected = "";
          }
          echo "<option value='" . $date["date"] . "' " . $selected . ">" . $date["date"] . "</option>";
        }
        ?>
      </select>
      <select name="grade" <?php if (isset($_POST['grade'])) {
                              echo "class = 'selected'";
                            } ?>>
        <?php
        // SQL QUERY FOR GRADES
        $sql = "SELECT DISTINCT grade FROM users JOIN testimonials ON users.id = testimonials.user_id ORDER BY grade";
        $result = exec_sql_query($db, $sql, $params = array());
        $all_grades = $result->fetchAll();
        echo "<option selected disabled>Grade </option>";
        foreach ($all_grades as $grade) {
          if (isset($_POST['grade']) && $_POST['grade'] == $grade['grade']) {
            $selected = "selected = 'selected' class='selected-option'";
          } else {
            $selected = "";
          }
          if ($grade["grade"] == 0) { // Pre-K show text, not number
            echo "<option value='" . $grade["grade"] . "' " . $selected . ">Pre-K</option>";
          } else {
            echo "<option value='" . $grade["grade"] . "' " . $selected . ">" . $grade["grade"] . "</option>";
          }
        }
        ?>
      </select>
      <select name="rating" <?php if (isset($_POST['rating'])) {
                              echo "class = 'selected'";
                            } ?>>
        <?php
        // SQL QUERY FOR RATINGS
        $result = exec_sql_query($db, "SELECT DISTINCT rating FROM testimonials ORDER BY rating DESC", $params = array());
        $all_ratings = $result->fetchAll();
        echo "<option selected disabled>Rating </option>";
        foreach ($all_ratings as $rating) {
          if (isset($_POST['rating']) && $_POST['rating'] == $rating['rating']) {
            $selected = "selected = 'selected' class='selected-option'";
          } else {
            $selected = "";
          }
          echo "<option value='" . $rating["rating"] . "' " . $selected . ">" . $rating["rating"] . "</option>";
        }
        ?>
      </select>
      <select name="role" <?php if (isset($_POST['role'])) {
                            echo "class = 'selected'";
                          } ?>>
        <?php
        // SQL QUERY FOR ROLES
        $result = exec_sql_query($db, "SELECT DISTINCT role FROM testimonials", $params = array());
        $all_roles = $result->fetchAll();
        echo "<option selected disabled>Role</option>";
        foreach ($all_roles as $role) {
          if (isset($_POST['role']) && $_POST['role'] == $role['role']) {
            $selected = "selected = 'selected' class='selected-option'";
          } else {
            $selected = "";
          }
          echo "<option value='" . $role["role"] . "' " . $selected . ">" . $role["role"] . "</option>";
        }
        ?>
      </select>
      <button type="submit" name="submit-sortby">Go</button>
      <button type="submit" name="reset-sortby" id="reset-button">Reset</button>
    </form>

    <?php
    if ($deleted_test) {
      echo "<p class='success'>Testimonial successfully deleted!</p>";
    }

    foreach ($error_messages as $error_message) {
      echo "<p class='error'>" . $error_message . "</p>";
    }
    foreach ($filter_messages as $filter_message) {
      echo "<p class='filter_message'>" . $filter_message . "</p>";
    }

    if (isset($ready_to_show) && $ready_to_show) {
      ?>
      <div class="table-div" id="testimonial_table">
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
    $sql = "SELECT testimonials.id, testimonials.testimonial, testimonials.rating, users.grade, testimonials.date, testimonials.role FROM testimonials JOIN users ON testimonials.user_id = users.id ORDER BY testimonials.date DESC";
    $result = exec_sql_query($db, $sql, $params = array());
    if ($result) {
      $records = $result->fetchAll();
      if (count($records) > 0) { // if there are records
        ?>
          <div class="table-div" id="testimonial_table">
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
  </div>

  <div class="body-div">
    <?php
    if (is_user_logged_in()) {
      include("includes/testimonial_form.php");
    } else {
      echo "<h2>Want to submit your own testimony?</h2>";
      echo "<p class='link-testimony'><a href='studentcenter.php'>Please login to our Student Center.</a></p>";
    }; ?>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
