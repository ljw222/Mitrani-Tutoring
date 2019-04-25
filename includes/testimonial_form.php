<h2>Submit a testimonial</h2>
<form id="testimonial_form" action="" method="POST">
    <div>
        <label for="testimonial">Testimonial:</label>
        <textarea rows=10 cols=50 name="textarea"></textarea>
    </div>
    <div>
        <label for="rating">Rating:</label>
        <select name="rating" id="rating">
            <?php
            for ($i = 1; $i <= 5; $i++) {
                echo "<option value='" . $i . "'>" . $i . "</option>";
            }
            ?>
        </select>
    </div>
    <div>
        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="parent">Parent</option>
            <option value="student">Student</option>
        </select>
    </div>
    <button type="submit" name="submit_testimony">Submit</button>
</form>
