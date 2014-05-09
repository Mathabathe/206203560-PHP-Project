<?php
// include function files for this application
require_once('chair.php');
session_start();
do_html_header("Adding a book");
if (check_admin_user()) {
if (filled_out($_POST)) {
$serialcode = $_POST['serialcode'];
$type = $_POST['type'];
$author = $_POST['color'];
$catid = $_POST['catid'];
$price = $_POST['price'];
$description = $_POST['description'];
if(insert_chair($serialcode, $type, $color, $catid, $price, $description)) {
echo "<p>Chair <em>".stripslashes($type)."</em> was added to the
database.</p>";
} else {
echo "<p>Chair <em>".stripslashes($type)."</em> could not be
added to the database.</p>";
}
} else {
echo "<p>You have not filled out the form. Please try again.</p>";
}
do_html_url("admin.php", "Back to administration menu");
} else {
echo "<p>You are not authorised to view this page.</p>";
}
do_html_footer();

function display_chair_form($chair = '') {
// This displays the book form.
// It is very similar to the category form.
// This form can be used for inserting or editing books.
// To insert, don't pass any parameters. This will set $edit
// to false, and the form will go to insert_book.php.
// To update, pass an array containing a book. The
// form will be displayed with the old data and point to update_book.php.
// It will also add a "Delete book" button.
// if passed an existing book, proceed in "edit mode"
$edit = is_array($chairs);
// most of the form is in plain HTML with some
// optional PHP bits throughout
?>
<form method="post"
action="<?php echo $edit ? 'edit_chair.php' : 'insert_chair.php';?>">
<table border="0">
<tr>
<td>Serial code:</td>
<td><input type="text" name="serialcode"
value="<?php echo $edit ? $book['serial_code'] : ''; ?>" /></td>
</tr>
<tr>
<td>Type:</td>
<td><input type="text" name="type"
value="<?php echo $edit ? $chair['type'] : ''; ?>" /></td>
</tr>
<tr>
<td>Colour:</td>
<td><input type="text" name="author"
value="<?php echo $edit ? $chair['color'] : ''; ?>" /></td>
</tr>
<tr>
<td>Category:</td>
<td><select name="catid">
<?php
// list of possible categories comes from database
$cat_array=get_categories();
foreach ($cat_array as $thiscat) {
echo "<option value=\"".$thiscat['catid']."\"";
// if existing book, put in current catgory
if (($edit) && ($thiscat['catid'] == $chair['catid'])) {
echo " selected";
}
echo ">".$thiscat['catname']."</option>";
}
?>
</select>
</td>
</tr>
<tr>
<td>Price:</td>
<td><input type="text" name="price"
value="<?php echo $edit ? $chair['price'] : ''; ?>" /></td>
</tr>
<tr>
<td>Description:</td>
<td><textarea rows="3" cols="50"
name="description">
<?php echo $edit ? $chair['description'] : ''; ?>
</textarea></td>
</tr>
<tr>
<td 
<?php 
if (!$edit) { echo "colspan=2"; }?> align="center">
<?php
if ($edit)
// we need the old serial_code to find book in database
// if the isbn is being updated
echo "<input type=\"hidden\" name=\"oldisbn\"
value=\"".$chair['serial_code']."\" />";
?>
<input type="submit"
value="<?php echo $edit ? 'Update' : 'Add'; ?> Chair" />
</form></td>
<?php
if ($edit) 
{
echo "<td>
<form method=\"post\" action=\"delete_book.php\">
<input type=\"hidden\" name=\"isbn\"
value=\"".$chair['serial_code']."\" />
<input type=\"submit\" value=\"Delete book\"/>
</form></td>";
}
?>
