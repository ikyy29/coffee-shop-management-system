<?php
include 'components/connect.php';

$dir = 'uploaded_img/';
$images = scandir($dir);

$added_count = 0;

foreach ($images as $image) {
    if ($image == '.' || $image == '..') continue;

    // Check if image is already in database
    $check = $conn->prepare("SELECT * FROM `products` WHERE image = ?");
    $check->execute([$image]);
    
    if ($check->rowCount() == 0) {
        // Derive name from image filename
        $name = str_replace(['.jpg', '.png', '.jpeg', ' edit', 'Recipe', 'Italian', '_'], ['', '', '', '', '', '', ' '], $image);
        $name = trim(preg_replace('/\s+/', ' ', $name)); // remove extra spaces
        
        // Guess category
        $name_lower = strtolower($name);
        $category = 'coffee';
        if (strpos($name_lower, 'tea') !== false || strpos($name_lower, 'matcha') !== false || strpos($name_lower, 'water') !== false || strpos($name_lower, 'tonic') !== false) {
            $category = 'drinks';
        } elseif (strpos($name_lower, 'cake') !== false || strpos($name_lower, 'cookie') !== false || strpos($name_lower, 'brownie') !== false || strpos($name_lower, 'roll') !== false || strpos($name_lower, 'cotta') !== false || strpos($name_lower, 'toast') !== false || strpos($name_lower, 'sando') !== false) {
            $category = 'desserts';
        } elseif (strpos($name_lower, 'sandwich') !== false || strpos($name_lower, 'melt') !== false || strpos($name_lower, 'puff') !== false || strpos($name_lower, 'bread') !== false || strpos($name_lower, 'sourdough') !== false) {
            $category = 'fast food';
        }
        
        // Default price
        $price = 35000;
        if ($category == 'drinks') $price = 25000;
        if ($category == 'coffee') $price = 30000;
        
        // Insert
        $insert = $conn->prepare("INSERT INTO `products` (name, category, price, image) VALUES (?, ?, ?, ?)");
        $insert->execute([$name, $category, $price, $image]);
        $added_count++;
        
        echo "Added: $name | Category: $category | Price: Rp. $price\n";
    }
}

echo "\nTotal added: $added_count products.";
?>
