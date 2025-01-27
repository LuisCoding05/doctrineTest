<?php
// Importamos las clases necesarias de Doctrine
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;

// Incluimos los archivos necesarios para la autocargar y configuración
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

// Función para solicitar input del usuario
function promptUser($message, $default = null) {
    echo $message . ($default ? " [$default]" : "") . ": ";
    $input = trim(fgets(STDIN));
    return empty($input) ? $default : $input;
}

// Procesar argumentos de la línea de comandos
$options = getopt("", ["namespace:", "dir:"]);

// Solicitar namespace si no se proporcionó
$namespace = $options['namespace'] ?? null;
if (!$namespace) {
    $namespace = promptUser("Introduce el namespace para las entidades", 'Daw\Doctrine\Entity');
}

// Solicitar directorio si no se proporcionó
$outputDir = $options['dir'] ?? null;
if (!$outputDir) {
    $outputDir = promptUser("Introduce el directorio de destino", __DIR__ . '/src/Entity');
}


/**
 * Clase generadora de entidades Doctrine a partir de tablas de base de datos
 */
class EntityGenerator {
    // Almacena la conexión a la base de datos
    private $connection;
    // Directorio donde se guardarán las entidades generadas
    private $outputDir;
    // Namespace que se usará para las entidades generadas
    private $namespace;

    /**
     * Constructor de la clase
     * @param $entityManager Gestor de entidades de Doctrine
     * @param string|null $outputDir Directorio de salida para los archivos generados
     * @param string $namespace Namespace para las entidades generadas
     */
    public function __construct($entityManager, string $outputDir, string $namespace) {
        $this->connection = $entityManager->getConnection();
        $this->outputDir = $outputDir;
        $this->namespace = $namespace;

        // Asegurar que el directorio de salida exista
        if (!is_dir($this->outputDir)) {
            if (!mkdir($this->outputDir, 0755, true)) {
                throw new \RuntimeException("No se pudo crear el directorio $this->outputDir");
            }
            echo "Creado directorio: $this->outputDir\n";
        }
    }

    /**
     * Genera las entidades para todas las tablas de la base de datos
     * @return array Array con los nombres de las entidades generadas
     */
    public function generateEntities() {
        // Obtenemos el gestor de esquema de la base de datos
        $schemaManager = $this->connection->createSchemaManager();
        
        // Obtenemos todas las tablas de la base de datos
        $tables = $schemaManager->listTables();

        // Array para almacenar las entidades generadas
        $generatedEntities = [];

        // Iteramos por cada tabla para generar su entidad
        foreach ($tables as $table) {
            $entityName = $this->convertTableNameToClassName($table->getName());
            $filename = $this->outputDir . '/' . $entityName . '.php';
            
            // Generar contenido de la entidad
            $entityContent = $this->generateEntityContent($table);
            
            // Escribir archivo
            file_put_contents($filename, $entityContent);
            
            $generatedEntities[] = $entityName;
            echo "Generada entidad: $entityName para tabla {$table->getName()}\n";
        }

        return $generatedEntities;
    }

    /**
     * Genera el contenido de una clase entidad para una tabla específica
     * @param $table Tabla de la base de datos
     * @return string Contenido PHP de la clase entidad
     */
    private function generateEntityContent($table) {
        $tableName = $table->getName();
        $className = $this->convertTableNameToClassName($tableName);
        
        $content = "<?php\n";
        $content .= "namespace {$this->namespace};\n\n";
        $content .= "use Doctrine\\ORM\\Mapping as ORM;\n\n";

        $content .= "#[ORM\\Entity]\n";
        $content .= "#[ORM\\Table(name: '$tableName')]\n";
        $content .= "class $className\n";
        $content .= "{\n";

        // Obtenemos las columnas que son clave primaria
        $idColumns = $table->getPrimaryKey()?->getColumns() ?? [];
        $columns = $table->getColumns();

        // Generamos las propiedades para cada columna
        foreach ($columns as $column) {
            $content .= $this->generatePropertyForColumn($column, in_array($column->getName(), $idColumns));
        }

        // Generar propiedades para relaciones foráneas
        $foreignKeys = $table->getForeignKeys();
        foreach ($foreignKeys as $foreignKey) {
            $content .= $this->generateForeignKeyProperty($foreignKey);
        }

        // Añadimos el constructor
        $content .= "\n    public function __construct() {}\n";

        // Generar getters y setters para columnas normales
        foreach ($columns as $column) {
            $content .= $this->generateGetterAndSetter($column);
        }

        // Generar getters y setters para relaciones foráneas
        foreach ($foreignKeys as $foreignKey) {
            $content .= $this->generateForeignKeyGetterAndSetter($foreignKey);
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * Convierte los tipos DBAL a tipos ORM de Doctrine
     * @param string $dbalType Tipo DBAL
     * @return string Tipo ORM correspondiente
     */
    private function convertDBALTypeToORMType($dbalType) {
        // Mapeo de tipos DBAL a tipos ORM
        $typeMap = [
            'Doctrine\DBAL\Types\IntegerType' => 'integer',
            'Doctrine\DBAL\Types\StringType' => 'string',
            'Doctrine\DBAL\Types\BooleanType' => 'boolean',
            'Doctrine\DBAL\Types\DateTimeType' => 'datetime',
            'Doctrine\DBAL\Types\FloatType' => 'float',
            'Doctrine\DBAL\Types\TextType' => 'text',
            'Doctrine\DBAL\Types\DecimalType' => 'decimal',
            'Doctrine\DBAL\Types\DateType' => 'date',
            'Doctrine\DBAL\Types\TimeType' => 'time',
            'Doctrine\DBAL\Types\ArrayType' => 'array',
            'Doctrine\DBAL\Types\JsonType' => 'json',
            'Doctrine\DBAL\Types\BlobType' => 'blob',
            'Doctrine\DBAL\Types\GuidType' => 'guid',
            'Doctrine\DBAL\Types\SmallIntType' => 'smallint',
            'Doctrine\DBAL\Types\BigIntType' => 'bigint',
            'Doctrine\DBAL\Types\DateTimeTzType' => 'datetimetz',
            'Doctrine\DBAL\Types\SimpleArrayType' => 'simple_array',
            'Doctrine\DBAL\Types\ObjectType' => 'object'
        ];

        return $typeMap[$dbalType] ?? 'string';
    }

    /**
     * Genera el código para una propiedad de la entidad
     * @param Column $column Columna de la base de datos
     * @param bool $isPrimaryKey Indica si la columna es clave primaria
     * @return string Código de la propiedad
     */
    private function generatePropertyForColumn(Column $column, bool $isPrimaryKey = false) {
        $propertyName = $this->convertColumnNameToPropertyName($column->getName());
        $phpType = $this->convertDBALTypeToPhpType($column);
        $ormType = $this->convertDBALTypeToORMType(get_class($column->getType()));
        $content = '';
        
        // Anotaciones de columna
        if ($isPrimaryKey) {
            $content .= "    #[ORM\\Id]\n";
            $content .= "    #[ORM\\Column(type: '$ormType')]\n";
            // Solo añadir GeneratedValue si la columna es autoincrement
            if ($column->getAutoincrement()) {
                $content .= "    #[ORM\\GeneratedValue]\n";
            }
        } else {
            $content .= "    #[ORM\\Column(\n";
            $content .= "        type: '$ormType',\n";
            
            // Parámetros adicionales
            if ($column->getLength()) {
                $content .= "        length: {$column->getLength()},\n";
            }
            
            if (!$column->getNotnull()) {
                $content .= "        nullable: true,\n";
            }
            
            $content .= "    )]\n";
        }
        
        // Declaración de propiedad
        $content .= "    private $phpType \$$propertyName;\n\n";

        return $content;
    }

    /**
     * Genera los métodos getter y setter para una columna
     * @param Column $column Columna de la base de datos
     * @return string Código de los métodos getter y setter
     */
    private function generateGetterAndSetter(Column $column) {
        $propertyName = $this->convertColumnNameToPropertyName($column->getName());
        $phpType = $this->convertDBALTypeToPhpType($column);
        $methodName = ucfirst($propertyName);
        
        $content = '';

        // Getter
        $content .= "    public function get$methodName(): $phpType\n";
        $content .= "    {\n";
        $content .= "        return \$this->$propertyName;\n";
        $content .= "    }\n\n";

        // Setter
        $content .= "    public function set$methodName($phpType \$$propertyName): self\n";
        $content .= "    {\n";
        $content .= "        \$this->$propertyName = \$$propertyName;\n";
        $content .= "        return \$this;\n";
        $content .= "    }\n\n";

        return $content;
    }


    /**
     * Genera la propiedad para una clave foránea
     * @param ForeignKeyConstraint $foreignKey
     * @return string
     */
    private function generateForeignKeyProperty($foreignKey) {
        $localColumns = $foreignKey->getLocalColumns();
        $foreignTableName = $foreignKey->getForeignTableName();
        $propertyName = $this->convertColumnNameToPropertyName($foreignTableName);
        $className = $this->convertTableNameToClassName($foreignTableName);
        
        $content = "    #[ORM\\ManyToOne(targetEntity: $className::class)]\n";
        $content .= "    #[ORM\\JoinColumn(\n";
        $content .= "        name: '{$localColumns[0]}',\n";
        $content .= "        referencedColumnName: '{$foreignKey->getForeignColumns()[0]}'\n";
        $content .= "    )]\n";
        $content .= "    private ?$className \$$propertyName = null;\n\n";
        
        return $content;
    }

    /**
     * Genera getter y setter para una clave foránea
     * @param ForeignKeyConstraint $foreignKey
     * @return string
     */
    private function generateForeignKeyGetterAndSetter($foreignKey) {
        $foreignTableName = $foreignKey->getForeignTableName();
        $propertyName = $this->convertColumnNameToPropertyName($foreignTableName);
        $className = $this->convertTableNameToClassName($foreignTableName);
        $methodName = ucfirst($propertyName);
        
        $content = "";
        
        // Getter
        $content .= "    public function get$methodName(): ?$className\n";
        $content .= "    {\n";
        $content .= "        return \$this->$propertyName;\n";
        $content .= "    }\n\n";

        // Setter
        $content .= "    public function set$methodName(?$className \$$propertyName): self\n";
        $content .= "    {\n";
        $content .= "        \$this->$propertyName = \$$propertyName;\n";
        $content .= "        return \$this;\n";
        $content .= "    }\n\n";

        return $content;
    }

    /**
     * Convierte el nombre de una tabla a nombre de clase
     * @param string $tableName Nombre de la tabla
     * @return string Nombre de la clase
     */
    private function convertTableNameToClassName($tableName) {
        // Convertir nombres de tabla a nombres de clase
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));
    }

    /**
     * Convierte el nombre de una columna a nombre de propiedad
     * @param string $columnName Nombre de la columna
     * @return string Nombre de la propiedad
     */
    private function convertColumnNameToPropertyName($columnName) {
        // Convertir nombres de columna a nombres de propiedad
        $parts = explode('_', $columnName);
        $propertyName = array_shift($parts);
        foreach ($parts as $part) {
            $propertyName .= ucfirst($part);
        }
        return $propertyName;
    }

    /**
     * Convierte tipos DBAL a tipos PHP
     * @param Column $column Columna de la base de datos
     * @return string Tipo PHP correspondiente
     */
    private function convertDBALTypeToPhpType(Column $column) {
        $typeMap = [
            'integer' => 'int',
            'smallint' => 'int',
            'bigint' => 'int',
            'string' => 'string',
            'text' => 'string',
            'datetime' => '\DateTime',
            'datetimetz' => '\DateTime',
            'date' => '\DateTime',
            'time' => '\DateTime',
            'boolean' => 'bool',
            'decimal' => 'float',
            'float' => 'float',
            'array' => 'array',
            'simple_array' => 'array',
            'json' => 'array',
            'object' => 'object',
            'blob' => 'resource',
            'guid' => 'string'
        ];
    
        $dbalType = $this->convertDBALTypeToORMType(get_class($column->getType()));
        return $typeMap[$dbalType] ?? 'mixed';
    }
}

// Ejecutar generación de entidades
try {
    echo "\nIniciando generación de entidades...\n";
    echo "Namespace: $namespace\n";
    echo "Directorio de destino: $outputDir\n\n";

    $generator = new EntityGenerator($entityManager, $outputDir, $namespace);
    $generatedEntities = $generator->generateEntities();
    
    echo "\nSe generaron las siguientes entidades:\n";
    print_r($generatedEntities);
} catch (Exception $e) {
    echo "Error al generar entidades: " . $e->getMessage() . "\n";
}