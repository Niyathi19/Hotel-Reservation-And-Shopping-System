<?php
// Start session
session_start();

// Function to check if user is logged in
function check_login() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    // Check login credentials (hardcoded for this example)
    $valid_username = "admin";
    $valid_password = "admin123";

    if ($_POST['username'] === $valid_username && $_POST['password'] === $valid_password) {
        $_SESSION['loggedin'] = true;
        header("Location: admindashboard.php");
        exit;
    } else {
        die("Invalid username or password.");
    }
}

// If not logged in, redirect to login page
if (!check_login()) {
    header("Location: adminlogin.html");
    exit;
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "order";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $update_sql = "UPDATE orders SET status='$status' WHERE id=$order_id";
    $conn->query($update_sql);
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $delete_sql = "DELETE FROM orders WHERE id=$order_id";
    $conn->query($delete_sql);
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $delete_sql = "DELETE FROM reservations WHERE id=$order_id";
    $conn->query($delete_sql);
}


// Fetch order data
$order_sql = "SELECT id, name, email, contact, address, product, quantity, status, reg_date FROM orders";
$order_result = $conn->query($order_sql);

// Fetch reservation data
$reservation_sql = "SELECT id, name, number_of_people, reg_date, time, additional_notes, reg_date FROM reservations";
$reservation_result = $conn->query($reservation_sql);

$contact_sql = "SELECT id, name, phone_number, reg_date FROM contacts";
$contact_result = $conn->query($contact_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('flat-lay-composition-mexican-food-with-copyspace.jpg');
            background-size: cover;
            color: white;
            text-align: center;
            padding: 50px;
        }
        table {
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid white;
            padding: 10px;
            text-align: left;
        }
        th {
            background: rgba(0, 0, 0, 0.7);
        }
        td {
            background: rgba(0, 0, 0, 0.5);
        }
        .status-pending {
            color: red;
        }
        .status-completed {
            color: green;
        }
        nav {
            padding: 10px;
            text-align: left;
        }
        .form-container {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<nav>
        <a href="index.html" style="color:white; text-decoration:none; float:right;">Logout</a>
    </nav>
    <h2>Admin Dashboard</h2>

    <h3>Reserved Tables</h3>
    <table>
        <tr>
            <th>SNO</th>
            <th>Name</th>
            <th>Number of People</th>
            <th>Date</th>
            <th>Time</th>
            <th>Additional Notes</th>
            <th>Actions</th>
        </tr>
        <?php
         $sno = 1;
        if ($reservation_result->num_rows > 0) {
            while ($row = $reservation_result->fetch_assoc()) {
                echo "<tr>
                 <td>{$sno}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['number_of_people']}</td>
                    <td>{$row['reg_date']}</td>
                    <td>{$row['time']}</td>
                    <td>{$row['additional_notes']}</td>
                    <td>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='order_id' value='{$row['id']}'>
                        <input type='submit' name='delete_order' value='Delete'>
                    </form>
                </td>
                </tr>";
                $sno++;
            }
        } else {
            echo "<tr><td colspan='6'>No reservations found</td></tr>";
        }
        ?>
    </table>

    <h3>Order List</h3>
    <table>
        <tr>
        <th>SNO</th>
            <th>Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php
        $sno = 1;
        if ($order_result->num_rows > 0) {
            while ($row = $order_result->fetch_assoc()) {
                $status_class = ($row['status'] == 'Completed') ? 'status-completed' : 'status-pending';
                echo "<tr>
                <td>{$sno}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['contact']}</td>
                    <td>{$row['address']}</td>
                    <td>{$row['product']}</td>
                    <td>{$row['quantity']}</td>
                    <td class='{$status_class}'>{$row['status']}</td>
                    <td>{$row['reg_date']}</td>
                    <td>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='order_id' value='{$row['id']}'>
                            <select name='status'>
                                <option value='Pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                <option value='Completed' " . ($row['status'] == 'Completed' ? 'selected' : '') . ">Completed</option>
                            </select>
                            <input type='submit' name='update_status' value='Update'>
                        </form>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='order_id' value='{$row['id']}'>
                            <input type='submit' name='delete_order' value='Delete'>
                        </form>
                    </td>
                </tr>";
                $sno++;
            }
        } else {
            echo "<tr><td colspan='10'>No orders found</td></tr>";
        }
        ?>
    </table>
    <h3>Contact Submissions</h3>
<table>
    <tr>
        <th>S.No</th>
        <th>Name</th>
        <th>Phone Number</th>
        <th>Date</th>
    </tr>
    <?php
    $sno = 1;
    if ($contact_result->num_rows > 0) {
        while ($row = $contact_result->fetch_assoc()) {
            echo "<tr>
                <td>{$sno}</td>
                <td>{$row['name']}</td>
                <td>{$row['phone_number']}</td>
                <td>{$row['reg_date']}</td>
            </tr>";
            $sno++;
        }
    } else {
        echo "<tr><td colspan='4'>No contacts found</td></tr>";
    }
    ?>
</table>
    

</body>
</html>

<?php
$conn->close();
?>
