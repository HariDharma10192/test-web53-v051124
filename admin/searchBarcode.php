<?php
// Include your database connection logic here
include 'koneksi.php';

// Retrieve the scanned barcode from the AJAX request
$barcode = $_POST['barcode'];

// Perform a search based on the barcode
$sql = "SELECT BARA, NAMA, HBELI, HJUAL FROM mstock WHERE BARA = '$barcode'";
$result = $conn->query($sql);

// Process the search results as needed
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Return the search results as JSON, or perform any other desired actions
    echo json_encode($row);
} else {
    echo "Barcode not found";
}

// Close the database connection
$conn->close();
