<?php
require '../config.php';

// Check if form is submitted
if (isset($_POST['submit'])) {

    // Collect POST data
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Simple validation
    if (empty($room_number) || empty($type) || empty($price)) {
        $error = "Please fill all required fields.";
    } elseif (!is_numeric($price)) {
        $error = "Price must be a number.";
    } else {
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO rooms (room_number, type, price, status) VALUES (:room_number, :type, :price, :status)");

        // Execute statement with data
        $stmt->execute([
            ':room_number' => $room_number,
            ':type' => $type,
            ':price' => $price,
            ':status' => $status
        ]);

        $success = "Room created successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Create Room</h2>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <?php if (!empty($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>

    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">Room Number</label>
            <input type="text" name="room_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Room Type</label>
            <select name="type" class="form-select" required>
                <option value="">Select Type</option>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Suite">Suite</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="available">Available</option>
                <option value="booked">Booked</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Create Room</button>
    </form>
</div>
</body>
</html>

