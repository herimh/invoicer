<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoCustomer
 *
 * @ORM\Table(name="ino_customer", uniqueConstraints={@ORM\UniqueConstraint(name="rfc_UNIQUE", columns={"rfc"})})
 * @ORM\Entity
 */
class Customer
{
    /**
     * @var string
     *
     * @ORM\Column(name="fiscal_name", type="string", length=255, nullable=true)
     */
    private $fiscalName;

    /**
     * @var string
     *
     * @ORM\Column(name="rfc", type="string", length=15, nullable=false)
     */
    private $rfc;

    /**
     * @var string
     *
     * @ORM\Column(name="fiscal_regime", type="string", length=45, nullable=true)
     */
    private $fiscalRegime;

    /**
     * @var integer
     *
     * @ORM\Column(name="fiscal_addres_id", type="integer", nullable=true)
     */
    private $fiscalAddresId;

    /**
     * @var string
     *
     * @ORM\Column(name="expedition_address_id", type="string", length=45, nullable=true)
     */
    private $expeditionAddressId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}
