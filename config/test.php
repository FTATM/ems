<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("49.0.69.152", "mysql", "FTATM54164000", "ams", 3307);
    echo "Connected successfully";
} catch (mysqli_sql_exception $e) {
    echo $e->getMessage();
}