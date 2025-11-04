<?php
// C:\xampp\htdocs\securigestion\includes\db.php

$host = 'localhost';
// --- NOMBRE DE BASE DE DATOS CORRECTO Y FINAL ---
$dbname = 'bd_seguri_gestion_integral1'; 
$user = 'root'; 
$pass = '';     

try {
    // Establece la conexión a la base de datos con el nombre correcto.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configura PDO para que muestre errores si algo sale mal.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Este mensaje de error ahora te ayudará a verificar si el nombre es correcto.
    die("Error crítico: No se pudo conectar a la base de datos. Asegúrate de que la base de datos llamada 'bd_seguri_gestion_integral1' exista en phpMyAdmin.");
}
?>