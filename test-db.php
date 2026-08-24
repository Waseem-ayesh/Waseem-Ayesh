<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=green_economy',
        'root',
        ''
    );

    echo "MYSQL CONNECTION OK";
} catch (PDOException $e) {
    echo "MYSQL CONNECTION FAILED: " . $e->getMessage();
}