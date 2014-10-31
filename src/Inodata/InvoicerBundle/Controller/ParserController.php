<?php

namespace Inodata\InvoicerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Inodata\InvoicerBundle\Lib\EmailReader;

class ParserController extends Controller
{
    //Define the curl adapter as global
    private $ch;
    
    public function indexAction()
    {
        $emailReader = $this->get("invoicer.email_reader");

        return new Response($emailReader->getCFDIsFromEmail());
    }
}
