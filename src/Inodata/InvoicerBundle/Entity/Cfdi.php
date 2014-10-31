<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoCfdi
 *
 * @ORM\Table(name="ino_cfdi")
 * @ORM\Entity
 */
class Cfdi
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
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="string", length=20, nullable=false)
     */
    private $folio;

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=15, nullable=false)
     */
    private $serie;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issuing_id", referencedColumnName="id")
     * })
     */
    private $issuing;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     * })
     */
    private $receiver;

    /**
     * @var string
     *
     * @ORM\Column(name="subtotal", type="decimal",  scale=2, nullable=false)
     */
    private $subtotal;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal",  scale=2, nullable=false)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal",  scale=2, nullable=false)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="money_type", type="string", length=5, columnDefinition="ENUM('MXN','USD')")
     */
    private $moneytype;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal",  scale=2, nullable=false)
     */
    private $exchangeRate;

    /**
     * @var PaymentCondition
     *
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\PaymentCondition")
     * @ORM\JoinColumn(name="payment_condition_id, referencedColumnName="id")
     */
    private $paymentCondition;

    /**
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="payment_method_id, referencedColumnName="id")
     */
    private $paymentMethod;

    /**
     * @var PaymentForm
     *
     * @ORM\ManyToOne(targetEntity="Inodata\InvoicerBundle\Entity\PaymentForm")
     * @ORM\JoinColumn(name="payment_form_id, referencedColumnName="id")
     */
    private $paymentForm;

    /**
     * @var string
     *
     * @ORM\Column(name="certificate_number", type="string", length=255, nullable=true)
     */
    private $certificateNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="cretificate", type="string", length=500, nullable=true)
     */
    private $cretificate;

    /**
     * @var string
     *
     * @ORM\Column(name="cfdi_attr", type="string", length=255, nullable=true)
     */
    private $cfdiAttr;

    /**
     * @var string
     *
     * @ORM\Column(name="xsi_attr", type="string", length=255, nullable=true)
     */
    private $xsiAttr;

    /**
     * @var string
     *
     * @ORM\Column(name="tfd_attr", type="string", length=255, nullable=true)
     */
    private $tfdAttr;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_trasferred", type="string", length=45, nullable=true)
     */
    private $taxTrasferred;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_reatined", type="string", length=45, nullable=true)
     */
    private $taxReatined;
}
