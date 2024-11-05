<?php
// Establish database connection
include '../koneksi.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize date filter (default to current date if not provided)
$dateFilter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Query to retrieve data from history_transaksi table with a date filter
$query = "SELECT * FROM history_transaksi WHERE DATE(created_at) = '$dateFilter' ORDER BY created_at DESC, id";
$result = $conn->query($query);

// Check if there are rows in the result set
if ($result->num_rows > 0) {
    echo "<h2>History Transaksi:</h2>";

    // Display the date filter form
    echo "<form method='get'>";
    echo "Select Date: <input type='date' name='date' value='$dateFilter'>";
    echo "<input type='submit' value='Filter'>";
    echo "</form>";

    echo "<table border='1'>";
    echo "<tr><th>No</th><th>Date</th><th>Total Belanja</th><th>Jumlah Uang</th><th>Kembalian</th><th>View Struk</th></tr>";

    // Variables to track the current date
    $currentDate = null;
    $idArray = [];
    $totalSales = 0; // Initialize total sales
    $totalModal = 0; // Initialize total modal
    $number = 1; // Initialize the number

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Fetch hbeli value from mstock table based on bara
        $mstockQuery = "SELECT hbeli FROM mstock WHERE bara = '{$row['bara']}'";
        $mstockResult = $conn->query($mstockQuery);

        if ($mstockResult->num_rows > 0) {
            $hbeliRow = $mstockResult->fetch_assoc();
            $hbeli = $hbeliRow['hbeli'];
        } else {
            $hbeli = 0; // Set default value if hbeli is not found
        }

        if ($currentDate != $row['created_at']) {
            // Output the row when the date changes
            echo "<tr>";
            echo "<td>" . $number . "</td>";
            // echo "<td>" . implode(", ", $idArray) . "</td>";
            echo "<td>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";

            echo "<td>Rp " . number_format($row['total_belanja'], 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($row['jumlah_uang'], 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($row['kembalian'], 0, ',', '.') . "</td>";

            // Add the View button with a link to view_transaksi.php and pass created_at as a parameter
            echo "<td><a href='view_transaksi.php?created_at=" . $row['created_at'] . "'>View</a></td>";

            echo "</tr>";

            // Reset the variables for the next group
            $currentDate = $row['created_at'];
            $idArray = [$row['id']];
            $totalSales += $row['total']; // Accumulate total sales
            $totalModal += $hbeli * $row['jumlah']; // Accumulate total modal
            $number++; // Increment the number
        } else {
            // Accumulate total sales and total modal for the same date
            $idArray[] = $row['id'];
            $totalSales += $row['total']; // Accumulate total sales
            $totalModal += $hbeli * $row['jumlah']; // Accumulate total modal
        }
    }
    echo "</table>";

    // Display total sales and total modal
    echo "<p>Total Sales: Rp " . number_format($totalSales, 0, ',', '.') . "</p>";
    echo "<p>Total Modal Penjualan: Rp " . number_format($totalModal, 0, ',', '.') . "</p>";

    // Back link to kasir.php
    echo '<a href="kasir.php">Back to Kasir</a>';
} else {
    echo "<h2>No records found in history_transaksi for the specified date</h2>";

    // Display the date filter form
    echo "<form method='get'>";
    echo "Select Date: <input type='date' name='date' value='$dateFilter'>";
    echo "<input type='submit' value='Filter'>";
    echo "</form>";

    // Back link to kasir.php
    echo '<a href="kasir.php">Back to Kasir</a>';
}

// Close the database connection
$conn->close();
