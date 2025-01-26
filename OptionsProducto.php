<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

use Daw\Doctrine\Entity\Producto;

function crearProducto($nombre, $precio, $stock = null, $descripcion = null) {
    global $entityManager;

    $producto = new Producto($nombre, $precio, $stock, $descripcion);

    try {
        $entityManager->persist($producto);
        $entityManager->flush();
        echo "Producto creado exitosamente\n";
        return $producto;
    } catch (Exception $e) {
        echo "Error al crear producto: " . $e->getMessage() . "\n";
        return null;
    }
}