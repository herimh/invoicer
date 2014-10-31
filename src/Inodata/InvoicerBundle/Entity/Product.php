<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoProduct
 *
 * @ORM\Table(name="ino_product")
 * @ORM\Entity
 */
class Product
{
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="cfdi_id", type="string", length=45, nullable=true)
     */
    private $cfdiId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}
