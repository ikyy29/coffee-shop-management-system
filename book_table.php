<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:login.php');
   exit();
}

if (isset($_POST['book_table'])) {

   $guests = $_POST['guests'];
   $guests = filter_var($guests, FILTER_SANITIZE_STRING);
   $reserve_date = $_POST['reserve_date'];
   $reserve_date = filter_var($reserve_date, FILTER_SANITIZE_STRING);
   $reserve_time = $_POST['reserve_time'];
   $reserve_time = filter_var($reserve_time, FILTER_SANITIZE_STRING);

   // Get user name
   $select_user = $conn->prepare("SELECT name FROM `users` WHERE id = ?");
   $select_user->execute([$user_id]);
   $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
   $name = $fetch_user['name'];

   $insert_reservation = $conn->prepare("INSERT INTO `reservations`(user_id, name, guests, reserve_date, reserve_time) VALUES(?,?,?,?,?)");
   $insert_reservation->execute([$user_id, $name, $guests, $reserve_date, $reserve_time]);

   $message[] = 'Reservasi meja berhasil dikirim! Silakan tunggu konfirmasi admin.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reservasi Meja</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Reservasi Meja</h3>
   <p><a href="home.php">home</a> <span> / reservasi</span></p>
</div>

<section class="about">
   <div class="row">
      <div class="content" style="text-align: center; width: 100%;">
         <h3>Reservasi Anda Sedang Diproses</h3>
         <br>
         <p>Terima kasih <b><?= $name; ?></b>. Permintaan pemesanan meja untuk <b><?= $guests; ?> orang</b> pada tanggal <b><?= $reserve_date; ?></b> jam <b><?= $reserve_time; ?></b> telah kami terima.</p>
         <br>
         <a href="home.php" class="btn">Kembali ke Beranda</a>
      </div>
   </div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
