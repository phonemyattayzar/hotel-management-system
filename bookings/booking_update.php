<?php
require '../config.php';

if(!isset($_GET['id'])){
    header("Location: booking_read.php");
    exit();
}

$booking_id = $_GET['id'];

// Fetch booking info
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id=:id");
$stmt->execute([':id'=>$booking_id]);
$booking = $stmt->fetch();

if(!$booking){
    header("Location: booking_read.php");
    exit();
}

// Fetch customers
$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();

// Fetch rooms (all rooms + currently booked room for this booking)
$rooms = $pdo->query("SELECT * FROM rooms WHERE status='available' OR id=".$booking['room_id'])->fetchAll();

$error = '';
$success = '';

if(isset($_POST['submit'])){
    $customer_id = $_POST['customer_id'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    if(strtotime($check_out) <= strtotime($check_in)){
        $error = "Check-out date must be after check-in date!";
    } else {
        try {
            // If room changed, free old room & book new room
            if($room_id != $booking['room_id']){
                // Free old room
                $pdo->prepare("UPDATE rooms SET status='available' WHERE id=:id")->execute([':id'=>$booking['room_id']]);
                // Book new room
                $pdo->prepare("UPDATE rooms SET status='booked' WHERE id=:id")->execute([':id'=>$room_id]);
            }

            // Update booking
            $stmt = $pdo->prepare("
                UPDATE bookings SET customer_id=:customer_id, room_id=:room_id, check_in=:check_in, check_out=:check_out
                WHERE id=:id
            ");
            $stmt->execute([
                ':customer_id'=>$customer_id,
                ':room_id'=>$room_id,
                ':check_in'=>$check_in,
                ':check_out'=>$check_out,
                ':id'=>$booking_id
            ]);

            $success = "Booking updated successfully!";

            // Refresh booking info
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id=:id");
            $stmt->execute([':id'=>$booking_id]);
            $booking = $stmt->fetch();

        } catch (Exception $e){
            $error = "Error: ".$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Update Booking</h2>

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
                <?php foreach($customers as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$booking['customer_id']) echo 'selected'; ?>>
                        <?php echo $c['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Room</label>
            <select name="room_id" class="form-select" required>
                <?php foreach($rooms as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php if($r['id']==$booking['room_id']) echo 'selected'; ?>>
                        <?php echo $r['room_number'] . " - " . $r['type']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Check-in Date</label>
            <input type="date" name="check_in" class="form-control" value="<?php echo $booking['check_in']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Check-out Date</label>
            <input type="date" name="check_out" class="form-control" value="<?php echo $booking['check_out']; ?>" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update Booking</button>
        <a href="booking_read.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
