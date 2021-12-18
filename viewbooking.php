<?php
include "header.php";
include "menu.php";
include "checksession.php";

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>
<title>View Booking</title> </head>
 <body>

<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Booking Details View</h1>
<h2><a href='listbookings.php'>[Return to the Booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
<?php

//makes sure we have the Room
if ($rowcount > 0) {  
   echo "<fieldset><legend>Booking detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
   echo "<dt>Check in date:</dt><dd>".$row['checkin']."</dd>".PHP_EOL;
   echo "<dt>Check out date:</dt><dd>".$row['checkout']."</dd>".PHP_EOL;
   echo "<dt>Contact number:</dt><dd>".$row['contactnumber']."</dd>".PHP_EOL;
   echo "<dt>Booking extras:</dt><dd>".$row['bookingextras']."</dd>".PHP_EOL; 
   echo "<dt>Room review:</dt><dd>".$row['roomreview']."</dd>".PHP_EOL; 
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No Booking found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>


<?php
echo '</div></div>';
require_once "footer.php";
?>

  