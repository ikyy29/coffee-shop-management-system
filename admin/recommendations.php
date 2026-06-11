<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
};

if (isset($_POST['add_rule'])) {
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $taste = $_POST['taste'];
   $taste = filter_var($taste, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $keyword = $_POST['keyword'];
   $keyword = filter_var($keyword, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $select_rules = $conn->prepare("SELECT * FROM `recommendation_rules` WHERE category = ? AND taste = ?");
   $select_rules->execute([$category, $taste]);

   if ($select_rules->rowCount() > 0) {
      $message[] = 'Aturan untuk kategori dan rasa ini sudah ada!';
   } else {
      $insert_rule = $conn->prepare("INSERT INTO `recommendation_rules`(category, taste, keyword) VALUES(?,?,?)");
      $insert_rule->execute([$category, $taste, $keyword]);
      $message[] = 'Aturan rekomendasi baru berhasil ditambahkan!';
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_rule = $conn->prepare("DELETE FROM `recommendation_rules` WHERE id = ?");
   $delete_rule->execute([$delete_id]);
   header('location:recommendations.php');
}

if (isset($_POST['update_rule'])) {
   $update_id = $_POST['update_id'];
   $keyword = $_POST['update_keyword'];
   $keyword = filter_var($keyword, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $update_rule = $conn->prepare("UPDATE `recommendation_rules` SET keyword = ? WHERE id = ?");
   $update_rule->execute([$keyword, $update_id]);
   $message[] = 'Kata kunci berhasil diperbarui!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Kelola Rekomendasi Menu</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/dashboard_style.css">
   <link rel="stylesheet" href="../css/table.css">
   
   <style>
       .update-form {
           display: flex;
           gap: 10px;
       }
       .update-form input[type="text"] {
           padding: 5px;
           border: 1px solid #ccc;
           border-radius: 4px;
       }
       .update-form button {
           padding: 5px 10px;
           background: var(--main-color, #2980b9);
           color: white;
           border: none;
           border-radius: 4px;
           cursor: pointer;
       }
   </style>
</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <!-- add rule section starts  -->

   <section class="add-products">

      <form action="" method="POST">
         <h3>Tambah Aturan Rekomendasi</h3>
         <select name="category" class="box" required>
            <option value="" disabled selected>Pilih Kategori --</option>
            <option value="coffee">Coffee</option>
            <option value="fast food">Fast Food</option>
            <option value="drinks">Drinks</option>
            <option value="desserts">Desserts</option>
         </select>
         <input type="text" required placeholder="Masukkan Rasa (contoh: sweet, strong, fresh)" name="taste" maxlength="100" class="box">
         <input type="text" required placeholder="Masukkan Kata Kunci Produk (contoh: mocha)" name="keyword" maxlength="100" class="box">
         <input type="submit" value="Tambah Aturan" name="add_rule" class="btn">
      </form>

   </section>

   <!-- add rule section ends -->

   <!-- show rules section starts  -->

   <section class="show-products" style="padding-top: 0;">

      <div class="table_header">
         <p>Daftar Aturan Rekomendasi</p>
      </div>

      <div>
         <table class="table">
            <thead>
               <tr>
                  <th>Kategori</th>
                  <th>Rasa (Taste)</th>
                  <th>Kata Kunci (Keyword)</th>
                  <th>Ubah Kata Kunci</th>
                  <th>Hapus</th>
               </tr>
            </thead>
            <tbody>
               <?php
               $show_rules = $conn->prepare("SELECT * FROM `recommendation_rules` ORDER BY category ASC, taste ASC");
               $show_rules->execute();
               if ($show_rules->rowCount() > 0) {
                  while ($fetch_rules = $show_rules->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <tr>
                        <td><?= $fetch_rules['category']; ?></td>
                        <td><?= $fetch_rules['taste']; ?></td>
                        <td><?= $fetch_rules['keyword']; ?></td>
                        <td>
                            <form action="" method="POST" class="update-form">
                                <input type="hidden" name="update_id" value="<?= $fetch_rules['id']; ?>">
                                <input type="text" name="update_keyword" value="<?= $fetch_rules['keyword']; ?>" required>
                                <button type="submit" name="update_rule"><i class="fa-solid fa-save"></i> Simpan</button>
                            </form>
                        </td>
                        <td>
                           <a href="recommendations.php?delete=<?= $fetch_rules['id']; ?>" onclick="return confirm('Hapus aturan ini?');"><button><i class="fa-solid fa-trash"></i></button></a>
                        </td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<tr><td colspan="5" class="empty">Belum ada aturan yang ditambahkan!</td></tr>';
               }
               ?>
            </tbody>
         </table>
      </div>

   </section>

   <!-- show rules section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>
