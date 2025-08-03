<?php
$host = "sqlXXX.infinityfree.com"; // your InfinityFree DB host
$dbname = "epiz_XXX_company_db";  // your InfinityFree DB name
$username = "epiz_XXX";           // your InfinityFree DB username
$password = "your_password";      // your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
