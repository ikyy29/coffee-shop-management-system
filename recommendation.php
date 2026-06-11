<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

include 'components/add_cart.php';

$recommended_product = null;

if (isset($_POST['get_recommendation'])) {
   $category = $_POST['category'];
   $taste = $_POST['taste'];

   $search_keyword = '';

   // Ambil kata kunci dari database berdasarkan kategori dan rasa
   $get_rule = $conn->prepare("SELECT keyword FROM `recommendation_rules` WHERE category = ? AND taste = ?");
   $get_rule->execute([$category, $taste]);
   
   if ($get_rule->rowCount() > 0) {
      $fetch_rule = $get_rule->fetch(PDO::FETCH_ASSOC);
      $search_keyword = $fetch_rule['keyword'];
   } else {
      // Fallback keyword jika tidak ada aturan yang cocok
      $search_keyword = 'tidak_ditemukan'; 
   }

   // Pertama, kita coba cari berdasarkan keyword spesifik, tetapi HANYA dalam kategori yang dipilih
   $get_cheap = $conn->prepare("SELECT * FROM `products` WHERE category = ? AND name LIKE ? ORDER BY price ASC LIMIT 1");
   $get_cheap->execute([$category, "%$search_keyword%"]);
   $cheap_product = $get_cheap->fetch(PDO::FETCH_ASSOC);

   $get_exp = $conn->prepare("SELECT * FROM `products` WHERE category = ? AND name LIKE ? ORDER BY price DESC LIMIT 1");
   $get_exp->execute([$category, "%$search_keyword%"]);
   $exp_product = $get_exp->fetch(PDO::FETCH_ASSOC);

   // Jika keyword tidak ditemukan, kita jadikan fallback untuk merekomendasikan 
   // Termurah & Termahal dari keseluruhan kategori tersebut
   if (!$cheap_product) {
       $get_cheap = $conn->prepare("SELECT * FROM `products` WHERE category = ? ORDER BY price ASC LIMIT 1");
       $get_cheap->execute([$category]);
       $cheap_product = $get_cheap->fetch(PDO::FETCH_ASSOC);

       $get_exp = $conn->prepare("SELECT * FROM `products` WHERE category = ? ORDER BY price DESC LIMIT 1");
       $get_exp->execute([$category]);
       $exp_product = $get_exp->fetch(PDO::FETCH_ASSOC);
   }

   if (!$cheap_product) {
      // Jika masih kosong, berarti data kategori tersebut memang belum ada di DB
      $recommended_products = 'not_found';
   } else {
      $recommended_products = [];
      $recommended_products[] = ['label' => 'Rekomendasi Paling Hemat', 'data' => $cheap_product];
      
      // Jika produk termahal berbeda dengan yang termurah, masukkan juga ke daftar
      if ($exp_product['id'] != $cheap_product['id']) {
         $recommended_products[] = ['label' => 'Rekomendasi Paling Premium', 'data' => $exp_product];
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Rekomendasi Cerdas</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .quiz-container { max-width: 600px; margin: 0 auto; padding: 2rem; background: var(--white); border-radius: .5rem; box-shadow: var(--box-shadow); text-align: center; }
      .quiz-container select { width: 100%; padding: 1.2rem; margin: 1rem 0; font-size: 1.8rem; border: var(--border); border-radius: .5rem; }
      .rec-result { margin-top: 3rem; padding: 2rem; border: 2px dashed var(--main-color); border-radius: .5rem; }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Rekomendasi Menu</h3>
   <p><a href="home.php">home</a> <span> / rekomendasi</span></p>
</div>

<section class="about">
   <div class="quiz-container">
      <h3 style="font-size: 2.5rem; color: var(--black);">Bingung Mau Pesan Apa?</h3>
      <p style="font-size: 1.5rem; color: var(--light-color); margin-bottom: 2rem;">Jawab 2 pertanyaan ini dan kami akan mencarikan kopi yang paling pas untuk *mood* Anda hari ini!</p>
      
      <form action="" method="POST">
         <select name="category" id="category_select" required>
            <option value="" disabled selected>Pilih Kategori Menu</option>
            <option value="coffee">Coffee</option>
            <option value="fast food">Fast Food</option>
            <option value="drinks">Drinks</option>
            <option value="desserts">Desserts</option>
         </select>
         
         <select name="taste" id="taste_select" required>
            <option value="" disabled selected>Pilih Nuansa Rasa Kesukaan Anda (Pilih Kategori Dulu)</option>
            <!-- Opsi akan diisi otomatis oleh Javascript -->
         </select>
         
         <input type="submit" name="get_recommendation" value="Cari Rekomendasi" class="btn" style="width: 100%;">
      </form>

      <?php 
         if (isset($recommended_products)): 
            if ($recommended_products === 'not_found'): 
      ?>
      <div class="rec-result">
         <h3 style="font-size: 2rem; color: var(--red); margin-bottom: 1rem;">Oops!</h3>
         <p style="font-size: 1.5rem;">Maaf, menu dengan kriteria tersebut belum tersedia di katalog kafe saat ini.</p>
         <p style="font-size: 1.3rem; margin-top: 1rem; color: var(--light-color);">(Sistem mencari berdasarkan template kata kunci yang telah dirancang. Silakan coba kombinasi lain atau periksa kembali nanti jika menu sudah diperbarui).</p>
      </div>
      <?php else: ?>
         
         <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap; margin-top: 3rem;">
         <?php foreach ($recommended_products as $rec): $product = $rec['data']; ?>
            <div class="rec-result" style="flex: 1; min-width: 250px; margin-top: 0;">
               <h3 style="font-size: 2rem; color: var(--main-color); margin-bottom: 1.5rem; border-bottom: 2px dashed var(--main-color); padding-bottom: 1rem;"><?= $rec['label']; ?></h3>
               <img src="uploaded_img/<?= $product['image']; ?>" alt="" style="height: 15rem; margin-bottom: 1rem;">
               <h2 style="font-size: 2.5rem;"><?= $product['name']; ?></h2>
               <p style="font-size: 1.8rem; font-weight: bold; color: var(--red);">Rp. <?= $product['price']; ?></p>
               
               <form action="" method="post" style="margin-top: 1.5rem;">
                  <input type="hidden" name="pid" value="<?= $product['id']; ?>">
                  <input type="hidden" name="name" value="<?= $product['name']; ?>">
                  <input type="hidden" name="price" value="<?= $product['price']; ?>">
                  <input type="hidden" name="image" value="<?= $product['image']; ?>">
                  <input type="hidden" name="qty" value="1">
                  <button type="submit" class="btn" name="add_to_cart">Add to Cart</button>
               </form>
            </div>
         <?php endforeach; ?>
         </div>

      <?php 
            endif;
         endif; 
      ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      const categorySelect = document.getElementById('category_select');
      const tasteSelect = document.getElementById('taste_select');

      const optionsMap = {
         'coffee': [
            {value: 'sweet', text: 'Manis / Creamy (Mocha, dll)'},
            {value: 'strong', text: 'Kuat / Pahit Bikin Melek (Espresso)'},
            {value: 'balanced', text: 'Seimbang (Latte / Cappuccino)'}
         ],
         'fast food': [
            {value: 'strong', text: 'Gurih / Asin (Savory Dish)'},
            {value: 'sweet', text: 'Manis (Sweet Dish)'}
         ],
         'drinks': [
            {value: 'sweet', text: 'Manis / Susu (Milkshake)'},
            {value: 'fresh', text: 'Segar / Asam (Lemon/Fruity)'}
         ],
         'desserts': [
            {value: 'sweet', text: 'Sangat Manis (Cake/Chocolate)'},
            {value: 'fresh', text: 'Ringan / Segar (Pudding/Jelly)'}
         ]
      };

      categorySelect.addEventListener('change', function() {
         const selectedCategory = this.value;
         const options = optionsMap[selectedCategory] || [];
         
         // Kosongkan opsi yang ada
         tasteSelect.innerHTML = '<option value="" disabled selected>Pilih Nuansa Rasa Kesukaan Anda</option>';
         
         // Tambahkan opsi baru sesuai kategori
         options.forEach(opt => {
            const optionElement = document.createElement('option');
            optionElement.value = opt.value;
            optionElement.textContent = opt.text;
            tasteSelect.appendChild(optionElement);
         });
      });
   });
</script>

</body>
</html>
