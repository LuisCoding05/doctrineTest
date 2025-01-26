<?php
    require_once 'OptionsUsuario.php';
    require_once "OptionsProducto.php";
    // Crear un nuevo usuario
    /*
    $nuevoUsuario = crearUsuario('Juan', 'Pérez', 'juan.perez@ejemplo.com', 'contraseña123');

    // Mostrar todos los usuarios
    mostrarTodosLosUsuarios();

    // Mostrar un usuario específico por ID
    mostrarUsuarioPorId(6);

    // Actualizar un usuario
    actualizarUsuario(6, 'Juan', 'Pérez', 'juan.perez@ejemplo.com', 'nuevaContraseña123');

    // Eliminar un usuario
    eliminarUsuario(9);
    // Descomentar para crear varios usuarios
    // crearVariosUsuarios();
    // Ejemplo de uso de creación de productos
    */
    $nuevoProducto = crearProducto('Laptop Gaming', 1299.99, 10, 'Laptop de alto rendimiento para juegos');