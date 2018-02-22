<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$first_name = $last_name = $street = $city = $country = "";
$first_name_err = $last_name_err = $street_err = $city_err = $country_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate First name
    $input_first_name = trim($_POST["first-name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a First name.";
    } elseif(!filter_var(trim($_POST["first-name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $first_name_err = 'Please enter a valid First name.';
    } else{
        $first_name = $input_first_name;
    }

    // Validate Last name
    $input_last_name = trim($_POST["last-name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";
    } elseif(!filter_var(trim($_POST["last-name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $last_name_err = 'Please enter a valid Last name.';
    } else{
        $last_name = $input_last_name;
    }

    // Validate street/number
    $input_street = trim($_POST["street"]);
    if(empty($input_street)){
        $street_err = 'Please enter a street and number.';
    } else{
        $street = $input_street;
    }

    // Validate city
    $input_city = trim($_POST["city"]);
    if(empty($input_city)){
        $city_err = 'Please enter a city.';
    } else{
        $city = $input_city;
    }

    // Validate country
    $input_country = trim($_POST["country"]);
    if(empty($input_country)){
        $country_err = 'Please enter a country.';
    } else{
        $country = $input_country;
    }

    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($street_err) && empty($city_err) && empty($country_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO users (first_name, last_name, street, city, country) VALUES (:first_name, :last_name, :street, :city, :country)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':first_name', $param_first_name);
            $stmt->bindParam(':last_name', $param_last_name);
            $stmt->bindParam(':street', $param_street);
            $stmt->bindParam(':city', $param_city);
            $stmt->bindParam(':country', $param_country);

            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_street = $street;
            $param_city = $city;
            $param_country = $country;


            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: users_details.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form validation</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkhS15LF5JMTGf5uzzmPPzy7ndseLvMjI&libraries=places"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="js/jquery.geocomplete.min.js"></script>
</head>
<body>
<div id="wrapper">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="mapform" class="mapForm" onsubmit="return Validate()" name="vform">
        <div>
            <label>First name</label>
            <input type="text" name="first-name" class="textInput" value="<?php echo $first_name; ?>" placeholder="First name">
            <div id="first_name_error" class="val_error"></div>
        </div>
        <div>
            <label>Last name</label>
            <input type="text" name="last-name" class="textInput" value="<?php echo $last_name; ?>" placeholder="Last name">
            <div id="last_name_error" class="val_error"></div>
        </div>
        <div>
            <label>Street and number</label>
            <input type="text" name="street" class="textInput" id="inputmap" data-geo="formatted_address" value="<?php echo $street; ?>" placeholder="Street and number">
            <div id="street_error" class="val_error"></div>
        </div>
        <div>
            <label>City</label>
            <input type="text" name="city" class="textInput" value="<?php echo $city; ?>" placeholder="City">
            <div id="city_error" class="val_error"></div>
        </div>
        <div>
            <label>Country</label>
            <input type="text" name="country" class="textInput" data-geo="country" value="<?php echo $country; ?>" placeholder="Country">
            <div id="country_error" class="val_error"></div>
        </div>
        <div>
            <input type="submit" value="Register" class="button" name="register">
            <a href="users_details.php" class="button">User's Details</a>
        </div>
    </form>

    <div id="map"></div>
    <script>
//        Adding a map
        $('#inputmap').geocomplete({
            map: '#map',
            details: '#mapform',
            detailsAttribute: "data-geo"
        });
    </script>
</div>
<!-- adding javascript -->
<script type="text/javascript">
    //Getting all input text objects
    var first_name = document.forms["vform"]["first-name"];
    var last_name = document.forms["vform"]["last-name"];
    var street = document.forms["vform"]["street"];
    var city = document.forms["vform"]["city"];
    var country = document.forms["vform"]["country"];

    //    Getting all error display objects
    var first_name_error = document.getElementById("first_name_error");
    var last_name_error = document.getElementById("last_name_error");
    var street_error = document.getElementById("street_error");
    var city_error = document.getElementById("city_error");
    var country_error = document.getElementById("country_error");

    //    Setting all event listeners
    first_name.addEventListener("blur", first_nameVerify, true);
    last_name.addEventListener("blur", last_nameVerify, true);
    street.addEventListener("blur", streetVerify, true);
    city.addEventListener("blur", cityVerify, true);
    country.addEventListener("blur", countryVerify, true);

    //  Validation function
    function Validate() {
//     First name validation
        if (first_name.value == "") {
            first_name.style.border = "3px solid red";
            first_name_error.textContent = "First name is required";
            first_name.focus();
            return false;
        }
//     Last name validation
        if (last_name.value == "") {
            last_name.style.border = "3px solid red";
            last_name_error.textContent = "Last name is required";
            last_name.focus();
            return false;
        }
//     Street validation
        if (street.value == "") {
            street.style.border = "3px solid red";
            street_error.textContent = "Street and number are required";
            street.focus();
            return false;
        }
//     City validation
        if (city.value == "") {
            city.style.border = "3px solid red";
            city_error.textContent = "City is required";
            city.focus();
            return false;
        }
//     Country validation
        if (country.value == "") {
            country.style.border = "3px solid red";
            country_error.textContent = "Country is required";
            country.focus();
            return false;
        }
    }
    //  event handler functions
    function first_nameVerify() {
        if (first_name.value != "") {
            first_name.style.border = "1px solid #5e6e66";
            first_name_error.innerHTML = "";
            return true;
        }
    }
    function last_nameVerify() {
        if (last_name.value != "") {
            last_name.style.border = "1px solid #5e6e66";
            last_name_error.innerHTML = "";
            return true;
        }
    }
    function streetVerify() {
        if (street.value != "") {
            street.style.border = "1px solid #5e6e66";
            street_error.innerHTML = "";
            return true;
        }
    }
    function cityVerify() {
        if (city.value != "") {
            city.style.border = "1px solid #5e6e66";
            city_error.innerHTML = "";
            return true;
        }
    }
    function countryVerify() {
        if (country.value != "") {
            country.style.border = "1px solid #5e6e66";
            country_error.innerHTML = "";
            return true;
        }
    }
</script>
</body>
</html>
