<?php

namespace Inodata\InvoicerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InodataInvoicerBundle:Default:index.html.twig', array('name' => $name));
    }
}
