<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

use Daw\Doctrine\Entity\User;

// Crear un nuevo usuario
function crearUsuario($nombre, $apellido, $email, $clave) {
    global $entityManager;

    $usuario = new User($nombre, $apellido, $email, password_hash($clave, PASSWORD_DEFAULT)); // Importante: hashear la contraseña

    try {
        $entityManager->persist($usuario);
        $entityManager->flush();
        echo "Usuario creado exitosamente\n";
        return $usuario;
    } catch (Exception $e) {
        echo "Error al crear usuario: " . $e->getMessage() . "\n";
        return null;
    }
}

function crearVariosUsuarios() {

    $usuarios = [
        ['María', 'González', 'maria@ejemplo.com', 'clave1'],
        ['Carlos', 'Rodríguez', 'carlos@ejemplo.com', 'clave2'],
        ['Ana', 'López', 'ana@ejemplo.com', 'clave3']
    ];

    foreach ($usuarios as $datosUsuario) {
        crearUsuario(...$datosUsuario);
    }
}

function mostrarTodosLosUsuarios() {
    global $entityManager;

    $usuarios = $entityManager->getRepository(User::class)->findAll();

    foreach ($usuarios as $usuario) {
        echo "ID: " . $usuario->getId() . "\n";
        echo "Nombre: " . $usuario->getNombre() . "\n";
        echo "Apellido: " . $usuario->getApellido() . "\n";
        echo "Email: " . $usuario->getEmail() . "\n";
        echo "-------------------------\n";
    }
}

function mostrarUsuarioPorId($id) {
    global $entityManager;

    $usuario = $entityManager->find(User::class, $id);

    if ($usuario) {
        echo "ID: " . $usuario->getId() . "\n";
        echo "Nombre: " . $usuario->getNombre() . "\n";
        echo "Apellido: " . $usuario->getApellido() . "\n";
        echo "Email: " . $usuario->getEmail() . "\n";
    } else {
        echo "Usuario no encontrado\n";
    }
}

function actualizarUsuario($id, $nombre, $apellido, $email, $clave) {
    global $entityManager;

    $usuario = $entityManager->find(User::class, $id);

    if ($usuario) {
        $usuario->setNombre($nombre)
                ->setApellido($apellido)
                ->setEmail($email)
                ->setClave(password_hash($clave, PASSWORD_DEFAULT)); // Importante: hashear la contraseña

        try {
            $entityManager->flush();
            echo "Usuario actualizado exitosamente\n";
            return $usuario;
        } catch (Exception $e) {
            echo "Error al actualizar usuario: " . $e->getMessage() . "\n";
            return null;
        }
    } else {
        echo "Usuario no encontrado\n";
        return null;
    }
}

function eliminarUsuario($id) {
    global $entityManager;

    $usuario = $entityManager->find(User::class, $id);

    if ($usuario) {
        try {
            $entityManager->remove($usuario);
            $entityManager->flush();
            echo "Usuario eliminado exitosamente\n";
        } catch (Exception $e) {
            echo "Error al eliminar usuario: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Usuario no encontrado\n";
    }
}