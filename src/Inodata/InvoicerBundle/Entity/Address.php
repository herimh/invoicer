<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoAddress
 *
 * @ORM\Table(name="ino_address")
 * @ORM\Entity
 */
class Address
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
     * @ORM\Column(name="street", type="string", length=255, nullable=false)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=500, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="no_ext", type="string", length=10, nullable=true)
     */
    private $noExt;

    /**
     * @var string
     *
     * @ORM\Column(name="no_int", type="string", length=10, nullable=true)
     */
    private $noInt;

    /**
     * @var string
     *
     * @ORM\Column(name="neighborhood", type="string", length=45, nullable=false)
     */
    private $neighborhood;

    /**
     * @var integer
     *
     * @ORM\Column(name="postal_code", type="integer", nullable=true)
     */
    private $postalCode;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="Inodata\ImperaBundle\Entity\City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;

    /**
     * @var State
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    private $state;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;
}
