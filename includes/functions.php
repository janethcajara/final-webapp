<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function getProductById($product_id, $pdo) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products_tbl WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
