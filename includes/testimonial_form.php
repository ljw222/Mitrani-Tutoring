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
                <p class="required">*</p><label for="testimonial">Testimonial:</label>
            </div>
            <textarea rows=10 cols=40 name="form_testimonial" id="testimonial" value="<?php if (isset($_POST['form_testimonial'])) {
                                                                                            echo $_POST['form_testimonial'];
                                                                                        }; ?>"></textarea>
        </div>
        <div>
            <div class="form_label">
                <p class="required">*</p><label for="rating">Rating:</label>
            </div>
            <select name="form_rating" id="rating" value="<?php if (isset($_POST['form_rating'])) {
                                                                echo $_POST['form_rating'];
                                                            }; ?>">
                <?php
                for ($i = 5; $i > 0; $i--) {
                    echo "<option value='" . $i . "'>" . $i . "</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <div class="form_label">
                <p class="required">*</p><label for="role">Role:</label>
            </div>
            <select name="form_role" id="role" value="<?php if (isset($_POST['form_role'])) {
                                                            echo $_POST['form_role'];
                                                        }; ?>">
                <option value="Student">Student</option>
                <option value="Parent">Parent</option>
            </select>
        </div>
        <button type="submit" name="submit_testimony">Submit</button>
    </form>
</div>
