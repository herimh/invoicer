<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoTaxes
 *
 * @ORM\Table(name="ino_taxes")
 * @ORM\Entity
 */
class Taxes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="cfdi_id", type="string", length=45, nullable=true)
     */
    private $cfdi;

    


}
