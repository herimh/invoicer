<?php

namespace Inodata\InvoicerBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Heriberto Monterrubio <heri185403@gmail.com, heriberto@inodata.com.mx>
 */
class BaseAcount extends ContainerAware
{   
    protected $email = '';
    protected $password = '';
    
    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }
}
