<?php
require '../config.php';

// Check if ID exists
if (!isset($_GET['id'])) {
    die("Room ID not found.");
}

$id = $_GET['id'];

// Fetch existing room data
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
$stmt->execute([':id' => $id]);
$room = $stmt->fetch();

if (!$room) {
    die("Room not found.");
}

// Handle update submit
if (isset($_POST['submit'])) {

    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        UPDATE rooms 
        SET room_number=:room_number, type=:type, price=:price, status=:status 
        WHERE id=:id
    ");

    $stmt->execute([
        ':room_number' => $room_number,
        ':type' => $type,
        ':price' => $price,
        ':status' => $status,
        ':id' => $id
    ]);

    header("Location: read.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Room</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<h2>Update Room</h2>

<?php if (!empty($success)) { ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="post">

<div class="mb-3">
<label>Room Number</label>
<input type="text" name="room_number" class="form-control"
value="<?php echo $room['room_number']; ?>">
</div>

<div class="mb-3">
<label>Type</label>
<select name="type" class="form-select">
<option <?php if($room['type']=="Single") echo "selected"; ?>>Single</option>
<option <?php if($room['type']=="Double") echo "selected"; ?>>Double</option>
<option <?php if($room['type']=="Suite") echo "selected"; ?>>Suite</option>
</select>
</div>

<div class="mb-3">
<label>Price</label>
<input type="number" name="price" class="form-control"
value="<?php echo $room['price']; ?>">
</div>

<div class="mb-3">
<label>Status</label>
<select name="status" class="form-select">
<option value="available" <?php if($room['status']=="available") echo "selected"; ?>>Available</option>
<option value="booked" <?php if($room['status']=="booked") echo "selected"; ?>>Booked</option>
</select>
</div>

<button type="submit" name="submit" class="btn btn-primary">Update Room</button>

</form>
</div>

</body>
</html>
