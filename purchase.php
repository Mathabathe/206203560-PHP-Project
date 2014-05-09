<?php
include ('book_sc_fns.php');
// The shopping cart needs sessions, so start one
session_start();
do_html_header("Checkout");
// create short variable names
$name = $_POST['name'];
$address = $_POST['address'];
$city = $_POST['city'];
$zip = $_POST['zip'];
$country = $_POST['country'];
// if filled out
if (($_SESSION['cart']) && ($name) && ($address) && ($city)
&& ($zip) && ($country)) {
// able to insert into database
if(insert_order($_POST) != false ) {
//display cart, not allowing changes and without pictures
display_cart($_SESSION['cart'], false, 0);
display_shipping(calculate_shipping_cost());
//get credit card details
display_card_form($name);
display_button("show_cart.php", "continue-shopping", "Continue Shopping");
} else {
echo "<p>Could not store data, please try again.</p>";
display_button('checkout.php', 'back', 'Back');
}
} else {
echo "<p>You did not fill in all the fields, please try again.</p><hr />";
display_button('checkout.php', 'back', 'Back');
}
do_html_footer();

<?php
function process_card($card_details) {
// connect to payment gateway or
// use gpg to encrypt and mail or
// store in DB if you really want to
return true;
}
function insert_order($order_details) {
// extract order_details out as variables
extract($order_details);
// set shipping address same as address
if((!$ship_name) && (!$ship_address) && (!$ship_city)
&& (!$ship_state) && (!$ship_zip) && (!$ship_country)) {
$ship_name = $name;
$ship_address = $address;
$ship_city = $city;
$ship_state = $state;
$ship_zip = $zip;
$ship_country = $country;
}
$conn = db_connect();
// we want to insert the order as a transaction
// start one by turning off auto commit
$conn->autocommit(FALSE);
// insert customer address
$query = "select customerid from customers where
name = '".$name."' and address = '".$address."'
and city = '".$city."' and state = '".$state."'
and zip = '".$zip."' and country = '".$country."'";
$result = $conn->query($query);
if($result->num_rows>0) {
$customer = $result->fetch_object();
$customerid = $customer->customerid;
} else {
$query = "insert into customers values
('', '".$name."','".$address."','".$city."',
'".$state."','".$zip."','".$country."')";
$result = $conn->query($query);
if (!$result) {
return false;
}
}
$customerid = $conn->insert_id;
$date = date("Y-m-d");
$query = "insert into orders values
('', '".$customerid."', '".$_SESSION['total_price']."',
'".$date."', '".PARTIAL."', '".$ship_name."',
'".$ship_address."', '".$ship_city."',
'".$ship_state."', '".$ship_zip."',
'".$ship_country."')";
$result = $conn->query($query);
if (!$result) {
return false;
}
$query = "select orderid from orders where
customerid = '".$userid."' and
amount > (".$_SESSION['total_price']."-.001) and
amount < (".$_SESSION['total_price']."+.001) and
date = '".$date."' and
order_status = 'PARTIAL' and
ship_name = '".$ship_name."' and
ship_address = '".$ship_address."' and
ship_city = '".$ship_city."' and
ship_state = '".$ship_state."' and
ship_zip = '".$ship_zip."' and
ship_country = '".$ship_country."'";
$result = $conn->query($query);
if($result->num_rows>0) {
$order = $result->fetch_objectreturn false;
}
// insert each chair
foreach($_SESSION['cart'] as $serialcode => $quantity) {
$detail = get_chair_details($serialcode);
$query = "delete from order_items where
orderid = '".$orderid."' and isbn = '".$serialcode."'";
$result = $conn->query($query);
$query = "insert into order_items values
('".$orderid."', '".$serialcode."', ".$detail['price'].", $quantity)";
$result = $conn->query($query);
if(!$result) {
return false;
}
}
// end transaction
$conn->commit();
$conn->autocommit(TRUE);
return $orderid;
}
?>


