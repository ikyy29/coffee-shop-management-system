<?php

include '../components/connect.php';

session_start();

$employee_id = $_SESSION['employee_id'];

if (!isset($employee_id)) {
   header('location:employee_login.php');
}

if (isset($_POST['update_status'])) {
   $reservation_id = $_POST['reservation_id'];
   $status = $_POST['status'];
   $update_status = $conn->prepare("UPDATE `reservations` SET status = ? WHERE id = ?");
   $update_status->execute([$status, $reservation_id]);
   $message[] = 'Status reservasi berhasil diupdate!';
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_reservation = $conn->prepare("DELETE FROM `reservations` WHERE id = ?");
   $delete_reservation->execute([$delete_id]);
   header('location:reservations.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Daftar Reservasi</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/dashboard_style.css">

</head>
<body>

<?php include '../components/employee_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders">

   <h1 class="heading">Daftar Reservasi Meja</h1>

   <div class="box-container">

   <?php
      $select_reservations = $conn->prepare("SELECT * FROM `reservations` ORDER BY reserve_date DESC, reserve_time DESC");
      $select_reservations->execute();
      if ($select_reservations->rowCount() > 0) {
         while ($fetch_reservations = $select_reservations->fetch(PDO::FETCH_ASSOC)) {
   ?>
   <div class="box">
      <p> user id : <span><?= $fetch_reservations['user_id']; ?></span> </p>
      <p> nama : <span><?= $fetch_reservations['name']; ?></span> </p>
      <p> jumlah tamu : <span><?= $fetch_reservations['guests']; ?> Orang</span> </p>
      <p> tanggal reservasi : <span><?= $fetch_reservations['reserve_date']; ?></span> </p>
      <p> waktu reservasi : <span><?= $fetch_reservations['reserve_time']; ?></span> </p>
      <p> status : <span style="color:<?php if($fetch_reservations['status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_reservations['status']; ?></span> </p>
      <form action="" method="POST">
         <input type="hidden" name="reservation_id" value="<?= $fetch_reservations['id']; ?>">
         <select name="status" class="drop-down">
            <option value="" selected disabled><?= $fetch_reservations['status']; ?></option>
            <option value="pending">pending</option>
            <option value="approved">approved</option>
            <option value="completed">completed</option>
            <option value="canceled">canceled</option>
         </select>
         <div class="flex-btn">
            <input type="submit" value="update" class="btn" name="update_status">
            <a href="reservations.php?delete=<?= $fetch_reservations['id']; ?>" class="delete-btn" onclick="return confirm('Hapus reservasi ini?');">delete</a>
         </div>
      </form>
   </div>
   <?php
      }
   } else {
      echo '<p class="empty">belum ada reservasi meja!</p>';
   }
   ?>

   </div>

</section>

<!-- placed orders section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
