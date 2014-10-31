<?php

namespace Inodata\InvoicerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InoPaymentForm
 *
 * @ORM\Table(name="ino_payment_form")
 * @ORM\Entity
 */
class PaymentForm
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}
