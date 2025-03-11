<?php
//este front controller recibe las peticiones por la url e intancia cualquier elemento

require_once "modelos/basededatos.php";

if(!isset($_GET['c'])){ //esto cuando no le pasamos un valor del controlador
    require_once "controladores/inicio.controlador.php";
    $controlador = new InicioControlador();
    call_user_func(array($controlador,"Inicio")); // va a llamar un metodo del controlador llamado Inicio
}else{
    $controlador = $_GET['c'];
    require_once "controladores/$controlador.controlador.php"; //va a tomar el nombre que le pasemos por la url y el prefijo .controlador
    $controlador = ucwords($controlador)."Controlador"; // ucword pasa el primer caracter a mayuscula
    $controlador = new $controlador; //instancio un objeto
    $accion = isset($_GET['a'])?$_GET['a'] : "Inicio";
    call_user_func(array($controlador,$accion));
}
