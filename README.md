# Proyecto de Presentación de Doctrine

Este proyecto demuestra el uso de Doctrine ORM en una aplicación PHP. Incluye operaciones básicas de CRUD para las entidades `User` y `Producto`.

## Estructura del Proyecto

El proyecto sigue una estructura modular con los siguientes componentes principales:

- **`bootstrap.php`**: Configura Doctrine ORM e inicializa el gestor de entidades.
- **`main.php`**: Script de ejemplo que demuestra cómo usar las funciones de `OptionsUsuario.php` y `OptionsProducto.php`.
- **`OptionsUsuario.php`**: Contiene funciones para las operaciones CRUD de la entidad `User`.
- **`OptionsProducto.php`**: Contiene funciones para las operaciones CRUD de la entidad `Producto`.
- **`src/Entity/User.php`**: Define la entidad `User`.
- **`src/Entity/Producto.php`**: Define la entidad `Producto`.

## Configuración

Sigue estos pasos para configurar y ejecutar el proyecto:

1. **Clona el repositorio:**
    ```sh
    git clone <repository-url>
    cd <repository-directory>
    ```

2. **Instala las dependencias:**
    ```sh
    composer install
    ```

3. **Configura las variables de entorno:**
    
    Crea un archivo `.env` en el directorio raíz y configura tu base de datos:
    ```env
    DB_HOST=tu_host_de_base_de_datos
    DB_PORT=tu_puerto_de_base_de_datos
    DB_NAME=tu_nombre_de_base_de_datos
    DB_USER=tu_usuario_de_base_de_datos
    DB_PASSWORD=tu_contraseña_de_base_de_datos
    DB_CHARSET=utf8
    ```

4. **Ejecuta las migraciones de la base de datos:**
    ```sh
    php vendor/bin/doctrine orm:schema-tool:update --force
    ```

## Uso

### Crear un Usuario

Para crear un nuevo usuario, utiliza la función `crearUsuario` en `OptionsUsuario.php`:

```php
require_once 'OptionsUsuario.php';

$nuevoUsuario = crearUsuario('Juan', 'Pérez', 'juan.perez@ejemplo.com', 'contraseña123');
```

### Mostrar Todos los Usuarios

Para mostrar todos los usuarios, utiliza la función `mostrarTodosLosUsuarios`:

```php
require_once 'OptionsUsuario.php';

mostrarTodosLosUsuarios();
```

### Crear un Producto

Para crear un nuevo producto, utiliza la función `crearProducto` en `OptionsProducto.php`:

```php
require_once 'OptionsProducto.php';

$nuevoProducto = crearProducto('Laptop Gaming', 1299.99, 10, 'Laptop de alto rendimiento para juegos');
```

## Descripción de Archivos

- **`bootstrap.php`**: Configura Doctrine ORM y inicializa el gestor de entidades.
- **`main.php`**: Demuestra el uso de las operaciones CRUD para las entidades `User` y `Producto`.
- **`OptionsUsuario.php`**: Proporciona funciones como `crearUsuario`, `mostrarTodosLosUsuarios`, `actualizarUsuario` y `eliminarUsuario`.
- **`OptionsProducto.php`**: Proporciona funciones como `crearProducto`, `mostrarTodosLosProductos`, `actualizarProducto` y `eliminarProducto`.
- **`src/Entity/User.php`**: Define la entidad `User` con propiedades como `id`, `nombre`, `apellido`, `email` y `password`.
- **`src/Entity/Producto.php`**: Define la entidad `Producto` con propiedades como `id`, `nombre`, `precio`, `cantidad` y `descripcion`.

## Licencia

Este proyecto está licenciado bajo la Licencia MIT.

