<?php
require '../config.php';

// Fetch customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();

// Fetch available rooms
$rooms = $pdo->query("SELECT * FROM rooms WHERE status='available' ORDER BY room_number ASC")->fetchAll();

// Initialize success/error messages
$error = '';
$success = '';

if(isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Basic validation
    if(strtotime($check_out) <= strtotime($check_in)){
        $error = "Check-out date must be after check-in date!";
    } else {
        try {
            // Insert booking
            $stmt = $pdo->prepare("
                INSERT INTO bookings (customer_id, room_id, check_in, check_out) 
                VALUES (:customer_id, :room_id, :check_in, :check_out)
            ");
            $stmt->execute([
                ':customer_id' => $customer_id,
                ':room_id' => $room_id,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);

            // Update room status to booked
            $pdo->prepare("UPDATE rooms SET status='booked' WHERE id=:id")->execute([':id'=>$room_id]);

            $success = "Booking created successfully!";
            header("Location: booking_read.php");
            exit();
            
            // Refresh available rooms
            $rooms = $pdo->query("SELECT * FROM rooms WHERE status='available' ORDER BY room_number ASC")->fetchAll();
            
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Create Booking</h2>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="form-select" required>
                <option value="">Select Customer</option>
                <?php foreach($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Room</label>
            <select name="room_id" class="form-select" required>
                <option value="">Select Room</option>
                <?php foreach($rooms as $room): ?>
                    <option value="<?php echo $room['id']; ?>">
                        <?php echo $room['room_number'] . " - " . $room['type']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Check-in Date</label>
            <input type="date" name="check_in" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Check-out Date</label>
            <input type="date" name="check_out" class="form-control" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Create Booking</button>
    </form>
</div>
</body>
</html>
