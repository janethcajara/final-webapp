<?php
include 'config/database.php';

try {
    $stmt = $pdo->query('DESCRIBE orders_tbl');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Table structure for orders_tbl:\n";
    foreach($columns as $col) {
        echo $col['Field'] . ' (' . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
