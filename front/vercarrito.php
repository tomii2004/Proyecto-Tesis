<?php
session_start();

echo "<pre>";
print_r($_SESSION['carrito']['variantes']);
echo "</pre>";
?>