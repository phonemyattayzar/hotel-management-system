<?php
require '../config.php';

if(isset($_GET['id'])){
    $booking_id = $_GET['id'];

    // Get the room_id for this booking
    $stmt = $pdo->prepare("SELECT room_id FROM bookings WHERE id=:id");
    $stmt->execute([':id'=>$booking_id]);
    $booking = $stmt->fetch();

    if($booking){
        $room_id = $booking['room_id'];

        // Delete the booking
        $pdo->prepare("DELETE FROM bookings WHERE id=:id")->execute([':id'=>$booking_id]);

        // Update room status back to available
        $pdo->prepare("UPDATE rooms SET status='available' WHERE id=:room_id")->execute([':room_id'=>$room_id]);

        // Redirect back to booking list
        header("Location: booking_read.php");
        exit();
    }
}
?>
