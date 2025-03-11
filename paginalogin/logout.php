<?php

require '../modelos/configproduct-detail.php';

unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_cliente']);


header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/index.php");


?>