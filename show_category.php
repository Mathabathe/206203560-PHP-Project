<?php
include ('chair.php');

session_start();

$catid = $_GET['catid'];
$name = get_category_name($catid);
do_html_header($name);

$book_array = get_chair($catid);
display_chair($chair_array);

if(isset($_SESSION['admin_user']))
 {
display_button("Home.php", "continue", "Continue Shopping");
display_button("admin.php", "admin-menu", "Admin Menu");
display_button("edit_category_form.php?catid=".$catid,
"edit-category", "Edit Category");
} 
else
 {
display_button("index.php", "continue-shopping", "Continue Shopping");
}
do_html_footer();


?>