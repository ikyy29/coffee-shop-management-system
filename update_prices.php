<?php
include 'components/connect.php';

// 1. Mengalikan harga yang masih satuan dollar (di bawah 1000) menjadi puluhan ribu Rupiah
$update_price = $conn->prepare("UPDATE `products` SET price = price * 10000 WHERE price < 1000");
$update_price->execute();

// 2. Mengubah simbol $ menjadi Rp. di semua file PHP
$files = glob("*.php");
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace <span>$</span> with <span>Rp. </span>
    $new_content = str_replace('<span>$</span>', '<span>Rp. </span>', $content);
    
    // Replace specific formats like $10/- to Rp. 10
    $new_content = str_replace('$<?= $fetch_orders[\'total_price\']; ?>/-', 'Rp. <?= $fetch_orders[\'total_price\']; ?>', $new_content);
    $new_content = str_replace('$<?= $grand_total; ?>/-', 'Rp. <?= $grand_total; ?>', $new_content);
    $new_content = str_replace('$<?= $fetch_cart[\'price\']; ?>/-', 'Rp. <?= $fetch_cart[\'price\']; ?>', $new_content);
    
    if ($new_content !== $content) {
        file_put_contents($file, $new_content);
    }
}

echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
echo "<h1>Pembaruan Selesai! 🎉</h1>";
echo "<p>1. Harga di database telah diubah menjadi harga masuk akal (dikali 10.000).</p>";
echo "<p>2. Semua simbol Dollar ($) di layar telah diubah menjadi <strong>Rp.</strong></p>";
echo "<br><a href='home.php' style='padding:10px 20px; background:#e67e22; color:#fff; text-decoration:none; border-radius:5px;'>Kembali ke Beranda</a>";
echo "</div>";
?>
