<?php
// filepath: /C:/xampp/htdocs/Cositas/doctrineProject/src/Entity/User.php
namespace Daw\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User 
{
   #[ORM\Id]
   #[ORM\Column(type: 'integer', length: 11)]
   #[ORM\GeneratedValue]
   private ?int $id = null;

   #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
   private string $nombre;

   #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
   private string $apellido;

   #[ORM\Column(type: 'string', length: 200, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
   private string $email;

   #[ORM\Column(type: 'string', length: 100, nullable: false, options: ['collation' => 'utf8mb4_general_ci'])]
   private string $clave;

    // Constructor
      public function __construct(string $nombre, string $apellido, string $email, string $clave) {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->clave = $clave;
    }
   // Getters y Setters
   public function getId(): ?int { return $this->id; }

   public function getNombre(): string { return $this->nombre; }
   public function setNombre(string $nombre): self { 
       $this->nombre = $nombre; 
       return $this;
   }

   public function getApellido(): string { return $this->apellido; }
   public function setApellido(string $apellido): self { 
       $this->apellido = $apellido; 
       return $this;
   }

   public function getEmail(): string { return $this->email; }
   public function setEmail(string $email): self { 
       $this->email = $email; 
       return $this;
   }

   public function getClave(): string { return $this->clave; }
   public function setClave(string $clave): self { 
       $this->clave = $clave; 
       return $this;
   }
}