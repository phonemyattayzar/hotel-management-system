<?php
require '../config.php';

// Fetch bookings with JOIN
$bookings = $pdo->query("
    SELECT 
        b.id,
        c.name AS customer_name,
        r.room_number AS room_number,
        r.type AS room_type,
        b.check_in,
        b.check_out,
        b.status
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>All Bookings</h2>
        <a href="booking_create.php" class="btn btn-success mb-3">Create New Booking</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Room Type</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings): ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?php echo $b['id']; ?></td>
                            <td><?php echo $b['customer_name']; ?></td>
                            <td><?php echo $b['room_number']; ?></td>
                            <td><?php echo $b['room_type']; ?></td>
                            <td><?php echo $b['check_in']; ?></td>
                            <td><?php echo $b['check_out']; ?></td>
                            <td><?php echo ucfirst($b['status']); ?></td>
                            <td>
                                <a href="booking_update.php?id=<?php echo $b['id']; ?>"
                                    class="btn btn-warning btn-sm">Update</a>
                                <a href="booking_delete.php?id=<?php echo $b['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>