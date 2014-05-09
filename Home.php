<?php

include ('chairs.php');

session_start();

header("Welcome to Moagi Chairs");

echo "<p>Please choose a category:</p>";

$cat_array = get_categories();

display_categories($cat_array);

if(isset($_SESSION['admin_user']))
 {
display_button("admin.php", "admin-menu", "Admin Menu");
}
do_html_footer();

function get_categories() 
{
// query database for a list of categories
$conn = db_connect();
$query = "select catid, catname from categories";
$result = @$conn->query($query);
if (!$result) 
{
return false;
}
$num_cats = @$result->num_rows;
if ($num_cats == 0)
 {
return false;
}
$result = db_result_to_array($result);
return $result;
}

function db_result_to_array($result)
 {
$res_array = array();
for ($count=0; $row = $result->fetch_assoc(); $count++) 
{
$res_array[$count] = $row;
}
return $res_array;
}

function display_categories($cat_array) 
{
if (!is_array($cat_array)) {
echo "<p>No categories currently available</p>";
return;
}
echo "<ul>";
foreach ($cat_array as $row) {
$url = "show_cart.php?catid=".($row['catid']);
$type = $row['catname'];
echo "<li>";
do_html_url($url, $title);
echo "</li>";
}
echo "</ul>";
echo "<hr />";
}

?>