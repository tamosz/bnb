<?php
include "header.php";
include "menu.php";
include "checksession.php";

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>
<head>
  <title>Make a booking</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
    $(function() {
      $("#datepicker").datepicker();
      $("#datepicker2").datepicker();
    });
  </script>
</head>

<body>

  <?php
  //function to clean input but not validate type and content
  function cleanInput($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }

  //the data was sent using a form therefore we use the $_POST instead of $_GET
  //check if we are saving data first by checking if the submit button exists in the array
  if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
    //if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
    include "config.php"; //load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
      echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
      exit; //stop processing the page further
    };

    //validate incoming data
    $error = 0; //clear our error flag
    $msg = 'Error: ';

    //roomname does not need extensive checking or validation because the options are preset and taken from the database directly
    $roomname = filter_input(INPUT_POST, 'rooms', FILTER_SANITIZE_STRING);


    //dates have had text input disabled, limiting input to the datepicker widget -- this means there is little need for validation
    //just checking if the fields are empty
    //check in date
    if (isset($_POST['checkin']) and !empty($_POST['checkin'])) {
      $checkin = cleanInput($_POST['checkin']);
    } else {
      $error++; //bump the error flag
      $msg .= 'Enter a check in date '; //append eror message
    }
    
    //check out date
    if (isset($_POST['checkout']) and !empty($_POST['checkout'])) {
      $checkin = cleanInput($_POST['checkout']);
    } else {
      $error++; //bump the error flag
      $msg .= 'Enter a check out date '; //append eror message
    }
    
    //contact number
    if (isset($_POST['contactnumber']) and !empty($_POST['contactnumber'])) {
      $checkin = cleanInput($_POST['contactnumber']);
    } else {
      $error++; //bump the error flag
      $msg .= 'Enter a contact number '; //append eror message
    }

    //booking extras  
    if (isset($_POST['bookingextras']) and !empty($_POST['bookingextras'])) {
      $checkin = cleanInput($_POST['bookingextras']);
    }

    //save the customer data if the error flag is still clear
    if ($error == 0) {
      $query = "INSERT INTO booking (roomname,checkin,checkout,contactnumber,bookingextras) VALUES (?,?,?,?,?)";
      $stmt = mysqli_prepare($DBC, $query); //prepare the query		
      mysqli_stmt_bind_param($stmt, 'sssss', $roomname, $checkin, $checkout, $contactnumber, $bookingextras);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      echo "<h2>Booking saved</h2>";
    } else {
      echo "<h2>$msg</h2>" . PHP_EOL;
    }
    mysqli_close($DBC); //close the connection once done
  }
  ?>

  <h1>Make a booking</h1>
  <h2><a href='listbookings.php'>[Return to the Booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
  <form method="POST" action="createbooking.php">

    <!--Check in date-->
    <p>
      <label for="datepicker">Check in date: </label>
      <input type="text" name="checkin" id="datepicker" readonly="true" required>
    </p>

    <!--Check out date-->
    <p>
      <label for="datepicker2">Check out date:</label>
      <input type="text" name="checkout" id="datepicker2" readonly="true" required>
    </p>

    <!--Contact number-->
    <p>
      <label for="contactnumber">Contact number: </label>
      <input type="tel" name="contactnumber" id="contactnumber" pattern="^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$" required>
    </p>

    <!--Booking extras-->
    <p>
      <label for="bookingextras">Booking extras: </label>
      <textarea name="bookingextras" id="bookingextras" rows="8" cols="30"></textarea>
    </p>
    <input type="submit" name="submit" value="Add">
  </form>

  <?php
echo '</div></div>';
require_once "footer.php";
?>