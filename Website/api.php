<?php

function getDatabaseConnection() {
	$db = new mysqli('localhost', 'gmorancr_phpuser', 'fcb5296', 'gmorancr_FoodTrucks');

	if ($db->connect_errno > 0) {
		 die('Unable to connect to database [' . $db->connect_error . ']');
	}

	return $db;
}


function getFoodTrucksAtLocation($locationID) {

	$db = getDatabaseConnection();
   	//$user = $db -> escape_string($user);
   	$sql = "SELECT locationID, truckName FROM FoodTruckLocation, FoodTruck where FoodTruckLocation.truckID = FoodTruck.truckID and '$locationID' = FoodTruckLocation.locationID";

   	if (!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
   	}

	$truckNames = array();

   	while($row = $result->fetch_assoc()) {
		$newTruck = $row['truckName'] . '<br />';
		array_push($truckNames, $newTruck);
   	}
   	$result->free();

	return $truckNames;

}

function getFoodTruckMenuItems($truckName){
	$db = getDatabaseConnection();
	//$user = $db -> escape_string($user);
	$sql = "SELECT menuItemName, menuItemPrice from Menu, FoodTruck where Menu.truckID = FoodTruck.truckID and FoodTruck.truckName = '$truckName'";

	if (!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
   	}



	while($row = $result->fetch_assoc()) {
		$newItem = $row['menuItemName'];
		$newPrice = $row['menuItemPrice'];

		echo " $ $newPrice : $newItem";
		echo "<br>";
		//echo $row['menuItemPrice'] . '<br />';

	}

	$result->free();

}

function getFoodTruckHours($truckName){
	$db = getDatabaseConnection();
	//$user = $db -> escape_string($user);
	$sql = 	"SELECT openingTime, closingTime, daysOpen from Hours, FoodTruck where Hours.hoursID = FoodTruck.hoursID and FoodTruck.truckName = '$truckName'";

	if (!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
   	}

	while($row = $result->fetch_assoc()) {
		echo $row['openingTime'];
		echo $row['closingTime'];
		echo $row['daysOpen']. '<br />';

	}

	$result->free();

}

function getFoodTruckRating($truckName){
	$db = getDatabaseConnection();
	//$user = $db -> escape_string($user);
	$sql = "SELECT ratingNumber, customerName from Rating, CustomerRating, Customer, FoodTruck where Rating.ratingID = CustomerRating.ratingID and Customer.customerID = CustomerRating.customerID and Rating.truckID = FoodTruck.truckID and FoodTruck.truckName = '$truckName'";

	if (!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
	}

	while($row = $result->fetch_assoc()) {
		echo $row['ratingNumber'];
		echo $row['customerName'] . '<br />';
	}
	$result->free();
}

function getFoodTruckContact($truckName){
	$db = getDatabaseConnection();
	//$user = $db -> escape_string($user);
	$sql = "SELECT truckPhone, truckEmail from FoodTruck where truckName = '$truckName'";

	if (!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
   	}

	while($row = $result->fetch_assoc()) {
		echo $row['truckPhone'];
		echo $row['truckEmail'] . '<br />';
	}
	$result->free();
}

function updateTruckHours($truckName, $openingTime, $closingTime, $daysOpen) {
	$db = getDatabaseConnection();

	$sql = "UPDATE Hours, FoodTruck SET Hours.openingTime = '$openingTime', Hours.closingTime = '$closingTime', Hours.daysOpen = '$daysOpen'
			WHERE FoodTruck.hoursID = Hours.hoursID and FoodTruck.truckName = '$truckName'";



	if ($db->query($sql) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $db->error;
	}
}

function addNewFoodTruck($openingTime, $closingTime, $daysOpen, $truckName, $truckPhone, $truckEmail) {

	$db = getDatabaseConnection();

	$openingTime = $db -> escape_string($openingTime);
	$closingTime = $db -> escape_string($closingTime);
	$daysOpen    = $db -> escape_string($daysOpen);

	$truckName  = $db -> escape_string($truckName);
	$truckPhone = $db -> escape_string($truckPhone);
	$truckEmail = $db -> escape_string($truckEmail);

	//insert into Hours values(hoursID, openingTime , closingTime, daysOpen);
	//insert into FoodTruck values(truckID, truckName, truckPhone, truckEmail, hoursID);
	//insert into Menu values(menuID, menuItemName, menuItemPrice, truckID);

	$sql = "SELECT truckName, truckPhone, truckEmail from FoodTruck where truckName = '$truckName'";

	if(!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
	}

	while($row = $result->fetch_assoc()) {
        $dbTruckName  = $row['truckName'];
        $dbTruckPhone = $row['truckPhone'];
        $dbTruckEmail = $row['truckEmail'];
    }

	if ($dbTruckName == $truckName) {
		echo "Error: This entry already exists";

	}

	else {

		$newHoursID = rand(0,99999);

		//Add Hours First

		$sql2 = "INSERT INTO Hours (hoursID, openingTime, closingTime, daysOpen) VALUES (?, ?, ?, ?)";
		$statement2 = $db->prepare($sql2);
		$statement2->bind_param('isss', $newHoursID, $openingTime, $closingTime, $daysOpen);

		if ($statement2->execute()) {
            //print 'Success! ID of last inserted record is : ' .$statement->insert_id .'<br />';
            //print "New Entry successfully submitted";
        }
        else {
            //die('Error : ('. $db->errno .') '. $db->error);
            echo "An error occurred while attempting to add an entry.";
        }

		$newID = null;
		$sql1 = "INSERT INTO FoodTruck (truckID, truckName, truckPhone, truckEmail, hoursID) VALUES (?, ?, ?, ?, ?)";
		$statement1 = $db->prepare($sql1);
		$statement1->bind_param('isssi', $newID, $truckName, $truckPhone, $truckEmail, $newHoursID);

		if ($statement1->execute()) {
            echo "Entry successfully submitted!";
        }
        else {
            //die('Error : ('. $db->errno .') '. $db->error);
            echo "An error occurred while attempting to add an entry.1";
			return;
        }
	}
}

function addNewRating($customerName, $customerEmail, $ratingNumber, $truckID) {

	$db = getDatabaseConnection();

	//Check if customer exists in database

	$sql = "SELECT customerName, customerEmail, customerID from Customer where customerName = '$customerName'";

	if(!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
	}

	while($row = $result->fetch_assoc()) {
        $dbCustomerName  = $row['customerName'];
        $dbCustomerEmail = $row['customerEmail'];
		$customerID = $row['customerID'];
    }

	if ($dbCustomerName != $customerName && $dbCustomerEmail != $customerEmail) {
		echo "Error: This account does not exist";

	}
	else { //Account exists

		//Insert rating into system

		$newRatingID = rand(0,999999999);

		$sql = "INSERT INTO Rating (ratingID, ratingNumber, truckID) VALUES (?, ?, ?)";
		$statement = $db->prepare($sql);
		$statement->bind_param('iss', $newRatingID, $ratingNumber, $truckID);

		if ($statement->execute()) {
			//print 'Success! ID of last inserted record is : ' .$statement->insert_id .'<br />';
			print "New Entry successfully submitted";
		}
		else {
			//die('Error : ('. $db->errno .') '. $db->error);
			echo "An error occurred while attempting to add an entry.";
		}

		//Insert rating into CustomerRating

		$sql = "INSERT INTO CustomerRating (customerID, ratingID) VALUES (?, ?)";
		$statement = $db->prepare($sql);
		$statement->bind_param('ii', $customerID, $newRatingID);

		if ($statement->execute()) {
			//print 'Success! ID of last inserted record is : ' .$statement->insert_id .'<br />';
			print "New Entry successfully submitted";
		}
		else {
			//die('Error : ('. $db->errno .') '. $db->error);
			echo "An error occurred while attempting to add an entry.";
		}
	}
}

function obfuscatePassword($password) {
    return hash('sha512', $password);
}

function registerNewCustomer($customerName, $customerEmail, $customerPassword) {

	$db = getDatabaseConnection();
	//Check if customer exists in database

	$sql = "SELECT customerName, customerEmail from Customer where customerName = '$customerName'";

	if(!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
	}

	while($row = $result->fetch_assoc()) {
		$dbCustomerEmail  = $row['customerEmail'];

	}

	if ($dbCustomerEmail == $customerEmail) {
		echo "Error: This email is already in use.";

	}

	else {

		$hashedPass = obfuscatePassword($customerPassword);

		$newID = null;
		$sql = "INSERT INTO Customer (customerID, customerName, customerEmail, customerPass) VALUES (?, ?, ?, ?)";
		$statement = $db->prepare($sql);
		$statement->bind_param('isss', $newID, $customerName, $customerEmail, $hashedPass);

		if ($statement->execute()) {
			//print 'Success! ID of last inserted record is : ' .$statement->insert_id .'<br />';
			print "New user has been registered";
		}
		else {
			//die('Error : ('. $db->errno .') '. $db->error);
			echo "An error occurred while attempting to add an entry.";
		}
	}
}

function logIn($email, $password) {

	$hashedPass = obfuscatePassword($password);

	$db = getDatabaseConnection();

	//Check if customer exists in database

	$sql = "SELECT customerEmail, customerPass from Customer where customerEmail = '$email'";

	if(!$result = $db->query($sql)) {
		die('There was an error running the query [' . $db->error . ']');
	}

	while($row = $result->fetch_assoc()) {
		$dbCustomerPassword  = $row['customerPass'];
	}

	if ($hashedPass == $dbCustomerPassword) {
		echo "Log in successful";
	}
	else {
		echo "Wrong username/password.";
	}
}

function deleteAccount($email, $password) {

	$db = getDatabaseConnection();
	$hashedPass = obfuscatePassword($customerPassword);

	$newID = null;
	$sql = "DELETE FROM Customer WHERE customerEmail = '$email' and customerPass = '$hashedPass'";
	$statement = $db->prepare($sql);
	//$statement->bind_param('isss', $newID, $customerName, $customerEmail, $hashedPass);

	if ($statement->execute()) {
		//print 'Success! ID of last inserted record is : ' .$statement->insert_id .'<br />';
		print "Account has been deleted.";
	}
	else {
		//die('Error : ('. $db->errno .') '. $db->error);
		echo "Error";
	}

}



//getFoodTrucksAtLocation("6th Street");
//
//getFoodTruckMenuItems("Tacos");

//getFoodTruckHours("Tacos");

//getFoodTruckRating("Tacos");
//getFoodTruckContact("Tacos");

//registerNewCustomer("John", "john@jmail.com", "password");

//logIn("john@jmail.com", "password");

//addNewFoodTruck($hoursID, $openingTime, $closingTime, $daysOpen, $truckID, $truckName, $truckPhone, $truckEmail, $menuID, $menuItemName, $menuItemPrice)




?>
