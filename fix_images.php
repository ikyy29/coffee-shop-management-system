<?php
include 'components/connect.php';

$fixes = [
    'Cinnamon Rolls With Heavy Cream (Extra Soft &amp; Gooey) &ndash; Cookin&#039; with Mima.jpg' => 'Cinnamon Rolls Extra Soft.jpg',
    'How much water should you drink each day_ &nbsp;Depends on your body size&hellip;_ #wellness #health #chiropractic.jpg' => 'Mineral Water Wellness.jpg',
    'Korean Garlic &amp; Cheese Sourdough.jpg' => 'Korean Garlic Cheese Sourdough.jpg'
];

foreach ($fixes as $old_file => $new_file) {
    if (file_exists('uploaded_img/' . $old_file)) {
        rename('uploaded_img/' . $old_file, 'uploaded_img/' . $new_file);
        
        $new_name = str_replace('.jpg', '', $new_file);
        
        $update = $conn->prepare("UPDATE `products` SET image = ?, name = ? WHERE image = ?");
        $update->execute([$new_file, $new_name, $old_file]);
        echo "Fixed: $new_file\n";
    }
}
echo "Done";
?>
