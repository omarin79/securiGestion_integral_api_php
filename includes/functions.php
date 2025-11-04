<?php
// C:\xampp\htdocs\securigestion\includes\functions.php (Archivo Esencial)

/**
 * Verifica si el usuario ha iniciado sesión.
 * Devuelve true si la variable de sesión 'user_id' existe, false en caso contrario.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Puedes añadir más funciones globales aquí en el futuro.
?>