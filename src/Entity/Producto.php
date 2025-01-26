<?php
namespace Daw\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'productos')]
class Producto 
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    private string $nombre;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $precio;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaCreacion = null;

    // Constructor
    public function __construct($nombre = '', $precio = 0.0, $stock = null, $descripcion = null) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->descripcion = $descripcion;
        $this->fechaCreacion = new \DateTime();
    }

    // Getters y Setters
    public function getId(): ?int { return $this->id; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): self { 
        $this->nombre = $nombre; 
        return $this;
    }

    public function getPrecio(): float { return $this->precio; }
    public function setPrecio(float $precio): self { 
        $this->precio = $precio; 
        return $this;
    }

    public function getStock(): ?int { return $this->stock; }
    public function setStock(?int $stock): self { 
        $this->stock = $stock; 
        return $this;
    }

    public function getDescripcion(): ?string { return $this->descripcion; }
    public function setDescripcion(?string $descripcion): self { 
        $this->descripcion = $descripcion; 
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface { return $this->fechaCreacion; }
}