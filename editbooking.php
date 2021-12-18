<?php
include "header.php";
include "menu.php";
include "checksession.php";

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>
<head>
  <title>Edit booking</title>
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
    include "config.php";
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

  //function to clean input but not validate type and content
  function cleanInput($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }
  
    //checking connection success
    if (mysqli_connect_errno()) {
      echo "Error: Unable to connect to MySQL. \n" . mysqli_connect_errno() . "=" . mysqli_connect_error();
      exit;
    };

    //retrieve the bookingID from the URL
    if($_SERVER["REQUEST_METHOD"] == "GET"){
      $id = $_GET['id'];
      if(empty($id) or !is_numeric($id)){
        echo "<h2>Invalid bookingID</h2>";
        exit;
      }
    }
    /* the data was sent using a form therefore we use the $_POST instead of $_GET
   check if we are saving data first by checking if the submit button exists in
   the array */
   if (isset($_POST['submit']) and !empty($_POST['submit'])
   and ($_POST['submit'] == 'Update')){
    $error = 0; //clear our error flag
    $msg = 'Error: ';

    //bookingID is a string not a number -- needs type conversion
    if(isset($_POST['id']) and !empty($_POST['id'])
    and is_integer(intval($_POST['id']))){
      $id = cleanInput($_POST['id']);
    } else {
      $error++;
      $msg .= 'Invalid booking ID ';
      $id = 0;
    }

   //checkin
    
   if (
    isset($_POST['checkin']) and !empty($_POST['checkin'])
    and is_string($_POST['checkin'])
  ) {
    $checkin = cleanInput($_POST['checkin']);
  } else {
    $error++; //bump the error flag
    $msg .= 'Invalid check in date '; // append error message
    $checkin = '';
  }
  
    //checkout
    
    if (
      isset($_POST['checkout']) and !empty($_POST['checkout'])
      and is_string($_POST['checkout'])
    ) {
      $checkout = cleanInput($_POST['checkout']);
    } else {
      $error++; //bump the error flag
      $msg .= 'Invalid check out date '; // append error message
      $checkout = '';
    }
  //email
  if (
    isset($_POST['email']) and !empty($_POST['email'])
    and is_string($_POST['email'])
  ) {
    $email = cleanInput($_POST['email']);
  } else {
    $error++;
    $msg .= 'Invalid email ';
    $email = '';
  }

  //contactnumber
  if (
    isset($_POST['contactnumber']) and !empty($_POST['contactnumber'])
    and is_string($_POST['contactnumber'])
  ) {
    $contactnumber = cleanInput($_POST['contactnumber']);
  } else {
    $error++;
    $msg .= 'Invalid contact number ';
    $contactnumber = '';
  }

  //bookingextras
  if (
    isset($_POST['bookingextras']) and !empty($_POST['bookingextras'])
    and is_string($_POST['bookingextras'])
  ) {
    $bookingextras = cleanInput($_POST['bookingextras']);
  } else {
    $error++;
    $msg .= 'Invalid booking extras ';
    $bookingextras = '';
  }

  
  //roomreview
  if (
    isset($_POST['roomreview']) and !empty($_POST['roomreview'])
    and is_string($_POST['roomreview'])
  ) {
    $roomreview = cleanInput($_POST['roomreview']);
  } else {
    $error++;
    $msg .= 'Invalid room review ';
    $roomreview = '';
  }

    //save the booking data if the error flag is still clear
    if ($error == 0 and $id > 0) {
      $query = "UPDATE booking SET roomname=?,checkin=?,checkout=?,contactnumber=?,bookingextras=?,roomreview=? WHERE bookingID=?";
      $stmt = mysqli_prepare($DBC, $query); //prepare the query
      mysqli_stmt_bind_param($stmt, 'sssssi', $firstname, $lastname, $email, $username, $roomreview, $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      echo "<h2>Booking details updated.</h2>";
    } else {
      echo "<h2>$msg</h2>" . PHP_EOL;
    }
  }
    

    //this query pulls the room names, types and number of beds for the drop down selection of room
  $mysqli = new mysqli('localhost', 'root', '', 'bnb');
  $resultset = $mysqli->query("SELECT roomname, roomtype, beds FROM room")
  ?>
  <h1>Booking registration</h1>
  <h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>

  <form method="POST" action="createbooking.php">
    <!--Room selector-->
    <p>
      <label for="rooms">Room (name,type,beds): </label>
      <select name="rooms">
        <?php
        while ($rows = $resultset->fetch_assoc()) {
          $roomname = $rows['roomname'];
          $roomtype = $rows['roomtype'];
          $roombeds = $rows['beds'];
          echo "<option>$roomname, $roomtype, $roombeds</option>";
        }
        ?>
    </p>
    </select>

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
    
    <!--Room review-->
    <p>
      <label for="roomreview">Room review: </label>
      <textarea name="roomreview" id="roomreview" rows="8" cols="30"></textarea>
    </p>
    <input type="submit" name="submit" value="Update">
  </form>
  <?php
echo '</div></div>';
require_once "footer.php";
?>
