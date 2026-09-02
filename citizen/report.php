<?php

session_start();

/*
|--------------------------------------------------------------------------
| CHECK CITIZEN LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['citizen_id'])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| MONGODB CONNECTION
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

try {

    $client = new Client("mongodb://127.0.0.1:27017");

    $database = $client->waste_monitoring_system;

    $complaints = $database->complaints;

} catch (Exception $e) {

    die("Database connection failed: " . $e->getMessage());

}


/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$message = "";
$error = "";

$citizenId = $_SESSION['citizen_id'];

$citizenEmail = $_SESSION['citizen_email'] ?? "";


/*
|--------------------------------------------------------------------------
| SUBMIT COMPLAINT
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $reporterName = trim($_POST['reporter_name'] ?? "");

    $location = trim($_POST['location'] ?? "");

    $latitude = $_POST['latitude'] ?? "";

    $longitude = $_POST['longitude'] ?? "";

    $wasteType = trim($_POST['waste_type'] ?? "");

    $description = trim($_POST['description'] ?? "");


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($reporterName === "") {

        $error = "Please enter your name.";

    } elseif ($location === "") {

        $error = "Please select the waste location on the map.";

    } elseif ($latitude === "" || $longitude === "") {

        $error = "Please drop a pin on the map.";

    } elseif ($wasteType === "") {

        $error = "Please select the type of waste.";

    } elseif ($description === "") {

        $error = "Please enter a description.";

    } elseif (!isset($_FILES['waste_photo']) ||
              $_FILES['waste_photo']['error'] !== UPLOAD_ERR_OK) {

        $error = "Please upload a waste photograph.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        $uploadDirectory = __DIR__ . "/../uploads/";

        if (!is_dir($uploadDirectory)) {

            mkdir($uploadDirectory, 0777, true);

        }


        $fileName = $_FILES['waste_photo']['name'];

        $tmpName = $_FILES['waste_photo']['tmp_name'];

        $fileSize = $_FILES['waste_photo']['size'];

        $fileExtension = strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );


        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];


        if (!in_array($fileExtension, $allowedExtensions)) {

            $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

        } elseif ($fileSize > 5 * 1024 * 1024) {

            $error = "Image size must be less than 5 MB.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | CREATE UNIQUE IMAGE NAME
            |--------------------------------------------------------------------------
            */

            $newFileName =
                "waste_" .
                time() .
                "_" .
                uniqid() .
                "." .
                $fileExtension;


            $imagePath =
                $uploadDirectory .
                $newFileName;


            /*
            |--------------------------------------------------------------------------
            | MOVE IMAGE
            |--------------------------------------------------------------------------
            */

            if (move_uploaded_file($tmpName, $imagePath)) {


                /*
                |--------------------------------------------------------------------------
                | GENERATE COMPLAINT ID
                |--------------------------------------------------------------------------
                */

                $complaintId =
                    "WM" .
                    date("Ymd") .
                    rand(1000, 9999);


                /*
                |--------------------------------------------------------------------------
                | SAVE COMPLAINT
                |--------------------------------------------------------------------------
                */

                try {

             $complaints->insertOne([

                        "complaint_id" => $complaintId,

                        "citizen_id" => $citizenId,

                        "citizen_email" => $citizenEmail,

                        "reporter_name" => $reporterName,

                        "location" => $location,

                        "latitude" => (float)$latitude,

                        "longitude" => (float)$longitude,

                        "waste_type" => $wasteType,

                        "description" => $description,

                        "image" =>
                            "uploads/" . $newFileName,

                        "status" => "Pending",

                        "created_at" => new MongoDB\BSON\UTCDateTime()

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    $message =
                        "Complaint submitted successfully! Your Complaint ID is " .
                        $complaintId;


                } catch (Exception $e) {

                    $error =
                        "Unable to save complaint. Please try again.";

                }


            } else {

                $error =
                    "Unable to upload the image.";

            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Report Uncollected Waste | Waste Monitor
</title>


<!--
|--------------------------------------------------------------------------
| LEAFLET MAP CSS
|--------------------------------------------------------------------------
-->

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>


<style>

/*
|--------------------------------------------------------------------------
| GENERAL
|--------------------------------------------------------------------------
*/

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

    font-family: Arial, sans-serif;

}


body {

    background:
        linear-gradient(
            rgba(244,248,244,0.94),
            rgba(244,248,244,0.94)
        );

    color: #222;

    min-height: 100vh;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

header {

    background: #176b3a;

    color: white;

    padding: 17px 7%;

    display: flex;

    justify-content: space-between;

    align-items: center;

}


.logo {

    font-size: 21px;

    font-weight: bold;

}


.dashboard-link {

    color: white;

    text-decoration: none;

    font-size: 15px;

    font-weight: bold;

}


.dashboard-link:hover {

    opacity: 0.8;

}


/*
|--------------------------------------------------------------------------
| MAIN CONTAINER
|--------------------------------------------------------------------------
*/

.main-container {

    width: 92%;

    max-width: 850px;

    margin: 35px auto 60px;

}


/*
|--------------------------------------------------------------------------
| FORM CARD
|--------------------------------------------------------------------------
*/

.form-card {

    background: rgba(255,255,255,0.96);

    padding: 35px;

    border-radius: 15px;

    box-shadow:
        0 8px 30px rgba(0,0,0,0.10);

}


/*
|--------------------------------------------------------------------------
| HEADING
|--------------------------------------------------------------------------
*/

.form-card h1 {

    text-align: center;

    color: #176b3a;

    font-size: 30px;

    margin-bottom: 10px;

}


.subtitle {

    text-align: center;

    color: #666;

    margin-bottom: 30px;

}


/*
|--------------------------------------------------------------------------
| SUCCESS MESSAGE
|--------------------------------------------------------------------------
*/

.success {

    background: #e7f7ed;

    border-left: 5px solid #176b3a;

    color: #176b3a;

    padding: 15px;

    border-radius: 7px;

    margin-bottom: 20px;

    font-weight: bold;

}


/*
|--------------------------------------------------------------------------
| ERROR MESSAGE
|--------------------------------------------------------------------------
*/

.error {

    background: #fdeaea;

    border-left: 5px solid #d63031;

    color: #b71c1c;

    padding: 15px;

    border-radius: 7px;

    margin-bottom: 20px;

    font-weight: bold;

}


/*
|--------------------------------------------------------------------------
| LABEL
|--------------------------------------------------------------------------
*/

label {

    display: block;

    font-weight: bold;

    margin-bottom: 8px;

    color: #333;

}


.required {

    color: red;

}


/*
|--------------------------------------------------------------------------
| INPUTS
|--------------------------------------------------------------------------
*/

input,
select,
textarea {

    width: 100%;

    padding: 13px;

    border: 1px solid #ccc;

    border-radius: 8px;

    font-size: 15px;

    outline: none;

    margin-bottom: 20px;

}

input:focus,
select:focus,
textarea:focus {

    border-color: #176b3a;

    box-shadow:
        0 0 0 2px rgba(23,107,58,0.10);

}


textarea {

    resize: vertical;

    min-height: 120px;

}


/*
|--------------------------------------------------------------------------
| LOCATION SECTION
|--------------------------------------------------------------------------
*/

.location-heading {

    font-size: 18px;

    color: #176b3a;

    margin-top: 5px;

    margin-bottom: 8px;

}


.location-info {

    background: #eef7f1;

    border-radius: 8px;

    padding: 13px;

    margin-bottom: 15px;

    color: #444;

    line-height: 1.5;

}


/*
|--------------------------------------------------------------------------
| MAP
|--------------------------------------------------------------------------
*/

#map {

    width: 100%;

    height: 400px;

    border-radius: 12px;

    border: 2px solid #ddd;

    margin-bottom: 15px;

}


/*
|--------------------------------------------------------------------------
| PLACE NAME BOX
|--------------------------------------------------------------------------
*/

.place-box {

    background: #f4f8f4;

    border: 1px solid #d6e4d9;

    border-radius: 8px;

    padding: 15px;

    margin-bottom: 15px;

}


.place-title {

    font-weight: bold;

    color: #176b3a;

    margin-bottom: 7px;

}


.place-name {

    color: #444;

    line-height: 1.5;

}


/*
|--------------------------------------------------------------------------
| COORDINATES
|--------------------------------------------------------------------------
*/

.coordinates {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;

}


.coordinates input {

    background: #f8f8f8;

}


/*
|--------------------------------------------------------------------------
| BUTTON
|--------------------------------------------------------------------------
*/

.submit-btn {

    width: 100%;

    border: none;

    background: #176b3a;

    color: white;

    padding: 15px;

    border-radius: 8px;

    font-size: 17px;

    font-weight: bold;

    cursor: pointer;

    transition: 0.3s;

}


.submit-btn:hover {

    background: #0f542c;

    transform: translateY(-2px);

    box-shadow:
        0 6px 15px rgba(0,0,0,0.15);

}


/*
|--------------------------------------------------------------------------
| MAP INSTRUCTION
|--------------------------------------------------------------------------
*/

.map-instruction {

    background: #fff8e7;

    border-left: 5px solid #e59b55;

    padding: 13px;

    border-radius: 7px;

    margin-bottom: 15px;

    color: #555;

    line-height: 1.5;

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 700px) {

    header {

        padding: 15px 5%;

    }


    .main-container {

        width: 94%;

        margin-top: 25px;

    }


    .form-card {

        padding: 22px;

    }


    .form-card h1 {

        font-size: 25px;

    }


    #map {

        height: 350px;

    }


    .coordinates {

        grid-template-columns: 1fr;

        gap: 0;

    }

}

</style>

</head>


<body>


<!--
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
-->

<header>

    <div class="logo">

        🌱 Waste Monitor

    </div>


    <a
        href="dashboard.php"
        class="dashboard-link"
    >

        ← Dashboard

    </a>

</header>



<!--
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
-->

<div class="main-container">


<div class="form-card">


<h1>

    Report Uncollected Waste

</h1>


<p class="subtitle">

    Help us keep your community clean by reporting uncollected waste.

</p>



<?php if ($message !== ""): ?>

<div class="success">

    <?= htmlspecialchars($message) ?>

</div>

<?php endif; ?>



<?php if ($error !== ""): ?>

<div class="error">

    <?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>



<form
    method="POST"
    enctype="multipart/form-data"
>


<!--
|--------------------------------------------------------------------------
| NAME
|--------------------------------------------------------------------------
-->

<label>

    Name of Reporter
    <span class="required">*</span>

</label>


<input
    type="text"
    name="reporter_name"
    placeholder="Enter your name"
    required
    pattern="[A-Za-z ]+"
    title="Name should contain only letters and spaces."
>



<!--
|--------------------------------------------------------------------------
| LOCATION
|--------------------------------------------------------------------------
-->

<div class="location-heading">

    📍 Select Waste Location

</div>


<div class="location-info">

    Click anywhere on the map to drop the pin.
    You can also drag the pin to select the exact waste location.

</div>


<div class="map-instruction">

    <strong>📌 How to select location:</strong><br>

    1. Allow location access when your browser asks.<br>

    2. The map will open near your current location.<br>

    3. Click on the exact waste location to drop the pin.<br>

    4. Drag the pin if you want to adjust the location.

</div>


<!-- MAP -->

<div id="map"></div>



<!--
|--------------------------------------------------------------------------
| PLACE NAME
|--------------------------------------------------------------------------
-->

<div class="place-box">

    <div class="place-title">

        📍 Selected Place

    </div>


    <div
        class="place-name"
        id="placeName"
    >

        Select a location on the map...

    </div>

</div>



<!--
|--------------------------------------------------------------------------
| HIDDEN LOCATION FIELD
|--------------------------------------------------------------------------
-->

<input
    type="hidden"
    name="location"
    id="location"
>



<!--
|--------------------------------------------------------------------------
| COORDINATES
|--------------------------------------------------------------------------
-->

<div class="coordinates">


<div>

    <label>
        Latitude
    </label>


    <input
        type="text"
        name="latitude"
        id="latitude"
        readonly
        placeholder="Latitude"
    >

</div>



<div>

    <label>
        Longitude
    </label>


    <input
        type="text"
        name="longitude"
        id="longitude"
        readonly
        placeholder="Longitude"
    >

</div>


</div>



<!--
|--------------------------------------------------------------------------
| WASTE TYPE
|--------------------------------------------------------------------------
-->

<label>

    Type of Waste
    <span class="required">*</span>

</label>


<select
    name="waste_type"
    required
>


    <option value="">
        Select waste type
    </option>


    <option value="Household Waste">
        Household Waste
    </option>


    <option value="Plastic Waste">
        Plastic Waste
    </option>


    <option value="Food Waste">
        Food Waste
    </option>


    <option value="Construction Waste">
        Construction Waste
    </option>


    <option value="Medical Waste">
        Medical Waste
    </option>


    <option value="Electronic Waste">
        Electronic Waste
    </option>


    <option value="Other">
        Other
    </option>


</select>



<!--
|--------------------------------------------------------------------------
| DESCRIPTION
|--------------------------------------------------------------------------
-->

<label>

    Description
    <span class="required">*</span>

</label>


<textarea
    name="description"
    placeholder="Describe the uncollected waste..."
    required
></textarea>



<!--
|--------------------------------------------------------------------------
| PHOTO
|--------------------------------------------------------------------------
-->

<label>

    Upload Waste Photograph
    <span class="required">*</span>

</label>


<input
    type="file"
    name="waste_photo"
    accept="image/jpeg,image/png,image/webp"
    required
>



<!--
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
-->

<button
    type="submit"
    class="submit-btn"
>

    🚀 Submit Complaint

</button>


</form>


</div>

</div>



<!--
|--------------------------------------------------------------------------
| LEAFLET MAP JAVASCRIPT
|--------------------------------------------------------------------------
-->

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script>

/*
|--------------------------------------------------------------------------
| DEFAULT LOCATION
|--------------------------------------------------------------------------
*/

let defaultLatitude = 12.9716;

let defaultLongitude = 77.5946;


/*
|--------------------------------------------------------------------------
| CREATE MAP
|--------------------------------------------------------------------------
*/

let map = L.map("map").setView(
    [
        defaultLatitude,
        defaultLongitude
    ],
    13
);


/*
|--------------------------------------------------------------------------
| OPENSTREETMAP
|--------------------------------------------------------------------------
*/

L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {

        maxZoom: 19,

        attribution:
            '&copy; OpenStreetMap contributors'

    }
).addTo(map);



/*
|--------------------------------------------------------------------------
| MARKER
|--------------------------------------------------------------------------
*/

let marker = null;



/*
|--------------------------------------------------------------------------
| LOCATION ELEMENTS
|--------------------------------------------------------------------------
*/

const latitudeInput =
    document.getElementById("latitude");


const longitudeInput =
    document.getElementById("longitude");


const locationInput =
    document.getElementById("location");


const placeName =
    document.getElementById("placeName");



/*
|--------------------------------------------------------------------------
| GET PLACE NAME
|--------------------------------------------------------------------------
*/
async function getPlaceName(lat, lng) {

    placeName.innerHTML =
        "🔄 Finding place name...";


    try {

        const response =
            await fetch(
                "https://nominatim.openstreetmap.org/reverse?" +
                "format=json&lat=" +
                lat +
                "&lon=" +
                lng +
                "&zoom=18&addressdetails=1",
                {

                    headers: {

                        "Accept":
                            "application/json"

                    }

                }
            );


        const data =
            await response.json();


        if (data.display_name) {

            placeName.innerHTML =
                data.display_name;


            locationInput.value =
                data.display_name;

        } else {

            placeName.innerHTML =
                "Location selected";

            locationInput.value =
                "Latitude: " +
                lat +
                ", Longitude: " +
                lng;

        }


    } catch (error) {

        placeName.innerHTML =
            "Location selected at the marked point.";


        locationInput.value =
            "Latitude: " +
            lat +
            ", Longitude: " +
            lng;

    }

}



/*
|--------------------------------------------------------------------------
| PLACE MARKER
|--------------------------------------------------------------------------
*/

function setMarker(lat, lng) {


    /*
    |--------------------------------------------------------------------------
    | REMOVE OLD MARKER
    |--------------------------------------------------------------------------
    */

    if (marker !== null) {

        map.removeLayer(marker);

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE NEW MARKER
    |--------------------------------------------------------------------------
    */

    marker =
        L.marker(
            [lat, lng],
            {
                draggable: true
            }
        ).addTo(map);


    /*
    |--------------------------------------------------------------------------
    | POPUP
    |--------------------------------------------------------------------------
    */

    marker.bindPopup(
        "📍 <b>Waste Location</b>"
    ).openPopup();


    /*
    |--------------------------------------------------------------------------
    | SET COORDINATES
    |--------------------------------------------------------------------------
    */

    latitudeInput.value =
        lat.toFixed(6);


    longitudeInput.value =
        lng.toFixed(6);


    /*
    |--------------------------------------------------------------------------
    | FIND PLACE
    |--------------------------------------------------------------------------
    */

    getPlaceName(
        lat,
        lng
    );


    /*
    |--------------------------------------------------------------------------
    | DRAG MARKER
    |--------------------------------------------------------------------------
    */

    marker.on(
        "dragend",
        function(event) {

            const position =
                event.target.getLatLng();


            const newLat =
                position.lat;


            const newLng =
                position.lng;


            latitudeInput.value =
                newLat.toFixed(6);


            longitudeInput.value =
                newLng.toFixed(6);


            getPlaceName(
                newLat,
                newLng
            );


            marker
                .bindPopup(
                    "📍 <b>Waste Location</b>"
                )
                .openPopup();

        }
    );

}



/*
|--------------------------------------------------------------------------
| CLICK MAP
|--------------------------------------------------------------------------
*/

map.on(
    "click",
    function(event) {

        const lat =
            event.latlng.lat;


        const lng =
            event.latlng.lng;


        setMarker(
            lat,
            lng
        );

    }
);



/*
|--------------------------------------------------------------------------
| GET USER CURRENT LOCATION
|--------------------------------------------------------------------------
*/

if (navigator.geolocation) {

    navigator.geolocation.getCurrentPosition(

        function(position) {

            const lat =
                position.coords.latitude;


            const lng =
                position.coords.longitude;


            map.setView(
                [lat, lng],
                16
            );


            setMarker(
                lat,
                lng
            );

        },

        function(error) {

            /*
            |--------------------------------------------------------------------------
            | IF LOCATION ACCESS IS DENIED
            |--------------------------------------------------------------------------
            */

            map.setView(
                [
                    defaultLatitude,
                    defaultLongitude
                ],
                13
            );

        }

    );

}



/*
|--------------------------------------------------------------------------
| PREVENT FORM SUBMISSION WITHOUT LOCATION
|--------------------------------------------------------------------------
*/

document
    .querySelector("form")
    .addEventListener(
        "submit",
        function(event) {

            if (
                latitudeInput.value === "" ||
                longitudeInput.value === "" ||
                locationInput.value === ""
            ) {

                event.preventDefault();


                alert(
                    "Please select the waste location on the map before submitting."
                );


                return false;

            }

        }
    );

</script>


</body>

</html>


