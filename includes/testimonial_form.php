<div class="form-div">
   <h2>Submit a testimonial</h2>
   <?php
   global $testimonial_error_messages;
   global $testimonial_success_messages;
   if (isset($testimonial_error_messages) or isset($testimonial_success_messages)) {
      foreach ($testimonial_error_messages as $error_message) {
         echo "<p class='error'>" . $error_message . "</p>";
      }
      foreach ($testimonial_success_messages as $success_message) {
         echo "<p class='success'>" . $success_message . "</p>";
      }
   }
   ?>
   <form id="testimonial_form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>#testimonial_form" method="POST">
      <div id="testimonial-text">
         <div class="form_label">
            <p class="required">*</p>
            <label for="testimonial">Testimonial:</label>
         </div>
         <textarea rows=10 cols=40 name="form_testimonial" id="testimonial"><?php global $testimonial;
                                                                              if (isset($testimonial)) {
                                                                                 echo $testimonial;
                                                                              }; ?></textarea>
      </div>
      <div id="rating-whole-div">
         <div class="form_label">
            <p class="required">*</p>
            <label>Rating:</label>
         </div>
         <div class="form-rating-div">
            <?php
            for ($i = 5; $i > 0; $i--) {
               if (isset($_POST['form_rating']) && $_POST['form_rating'] == $i) {
                  $checked = "checked";
               } else {
                  $checked = "";
               }
               echo "<div class='form-rating-option'>";
               echo "<input type='radio' name='form_rating' value='" . $i . "' id='" . $i . "' " . $checked . "/>";
               echo "<label for='" . $i . "'>" . print_stars($i) . "</label>";
               echo "</div>";
            }
            ?>
         </div>
      </div>
      <div>
         <div class="form_label">
            <p class="required">*</p>
            <label for="role">Role:</label>
         </div>
         <select name="form_role" id="role">
            <?php
            $all_roles = ['Student', 'Parent'];
            foreach ($all_roles as $role) {
               if (isset($_POST['form_role']) && $_POST['form_role'] == $role) {
                  $selected = "selected = 'selected' class='selected-option'";
               } else {
                  $selected = "";
               }
               echo "<option value='" . $role . "' " . $selected . ">" . $role . "</option>";
            }
            ?>
         </select>
      </div>
      <button type="submit" name="submit_testimony">Submit</button>
   </form>
</div>
