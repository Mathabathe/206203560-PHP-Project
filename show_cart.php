<?php
include ('chair_sc_fns.php');
// The shopping cart needs sessions, so start one
session_start();
@$new = $_GET['new'];
if($new) {
//new item selected
if(!isset($_SESSION['cart'])) {
$_SESSION['cart'] = array();
$_SESSION['items'] = 0;
$_SESSION['total_price'] ='0.00';
}
if(isset($_SESSION['cart'][$new])) {
$_SESSION['cart'][$new]++;
} else {
$_SESSION['cart'][$new] = 1;
}
$_SESSION['total_price'] = calculate_price($_SESSION['cart']);
$_SESSION['items'] = calculate_items($_SESSION['cart']);
}
if(isset($_POST['save'])) {
foreach ($_SESSION['cart'] as $serial_code => $qty) {
if($_POST[$serial_code] == '0') {
unset($_SESSION['cart'][$isbn]);
} else {
$_SESSION['cart'][$isbn] = $_POST[$isbn];
}
}
$_SESSION['total_price'] = calculate_price($_SESSION['cart']);
$_SESSION['items'] = calculate_items($_SESSION['cart']);
}
do_html_header("Your shopping cart");
if(($_SESSION['cart']) && (array_count_values($_SESSION['cart']))) {
display_cart($_SESSION['cart']);
} else {
echo "<p>There are no items in your cart</p><hr/>";
}
$target = "index.php";
// if we have just added an item to the cart, continue shopping in that category
if($new) {
$details = get_chair_details($new);
if($details['catid']) {
$target = "show_cat.php?catid=".$details['catid'];
}
}
display_button($target, "continue-shopping", "Continue Shopping");
// use this if SSL is set up
// $path = $_SERVER['PHP_SELF'];
// $server = $_SERVER['SERVER_NAME'];
// $path = str_replace('show_cart.php', '', $path);
// display_button("https://".$server.$path."checkout.php",
// "go-to-checkout", "Go To Checkout");
// if no SSL use below code
display_button("checkout.php", "go-to-checkout", "Go To Checkout");
do_html_footer();

function display_cart($cart, $change = true, $images = 1) {
// display items in shopping cart
// optionally allow changes (true or false)
// optionally include images (1 - yes, 0 - no)
echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\">
<form action=\"show_cart.php\" method=\"post\">
<tr><th colspan=\"".(1 + $images)."\" bgcolor=\"#cccccc\">Item</th>
<th bgcolor=\"#cccccc\">Price</th>
<th bgcolor=\"#cccccc\">Quantity</th>
<th bgcolor=\"#cccccc\">Total</th>
</tr>";
//display each item as a table row
foreach ($cart as $isbn => $qty) {
$book = get_chair_details($isbn);
echo "<tr>";
if($images == true) {
echo "<td align=\"left\">";
if (file_exists("images/".$isbn.".jpg")) {
$size = GetImageSize("images/".$isbn.".jpg");
if(($size[0] > 0) && ($size[1] > 0)) {
echo "<img src=\"images/".$isbn.".jpg\"
style=\"border: 1px solid black\"
width=\"".($size[0]/3)."\"
height=\"".($size[1]/3)."\"/>";
}
} else {
echo "&nbsp;";
}
echo "</td>";
}
echo "<td align=\"left\">
<a href=\"show_book.php?isbn=".$serial_code."\">".$chair['type']."</a>
by ".$chair['colour']."</td>
<td align=\"center\">\$".number_format($chair['price'], 2)."</td>
<td align=\"center\">";
// if we allow changes, quantities are in text boxes
if ($change == true) {
echo "<input type=\"text\" name=\"".$serial_code."\" value=\"".$qty."\"
size=\"3\">";
} else {
echo $qty;
}
echo "</td>
<td align=\"center\">\$".number_format($chair['price']*$qty,2)."</td
</tr>\n";
}
// display total row
echo "<tr>
<th colspan=\"".(2+$images)."\" bgcolor=\"#cccccc\">&nbsp;</td>
<th align=\"center\" bgcolor=\"#cccccc\">".$_SESSION['items']."</th>
<th align=\"center\" bgcolor=\"#cccccc\">
\$".number_format($_SESSION['total_price'], 2)."
</th>
</tr>";
// display save change button
if($change == true) {
echo "<tr>
<td colspan=\"".(2+$images)."\">&nbsp;</td>
<td align=\"center\">
<input type=\"hidden\" name=\"save\" value=\"true\"/>
<input type=\"image\" src=\"images/save-changes.gif\"
border=\"0\" alt=\"Save Changes\"/>
</td>
<td>&nbsp;</td>
</tr>";
}
echo "</form></table>";
}

function calculate_price($cart) {
// sum total price for all items in shopping cart
$price = 0.0;
if(is_array($cart)) {
$conn = db_connect();
foreach($cart as $serial_code => $qty) {
$query = "select price from books where serialcode='".$serial_code."'";
$result = $conn->query($query);
if ($result) 
{
$item = $result->fetch_object();
$item_price = $item->price;
$price +=$item_price*$qty;
}
}
}
return $price;
}

function calculate_items($cart) {
// sum total items in shopping cart
$items = 0;
if(is_array($cart)) {
foreach($cart as $serialcode=> $qty) {
$items += $qty;
}
}
return $items;
}
?>