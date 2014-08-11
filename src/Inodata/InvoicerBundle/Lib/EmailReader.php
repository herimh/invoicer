<?php

namespace Inodata\InvoicerBundle\Lib;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Se encarga de leer los correos nuevos de una cuenta, determina si el contenido es un archivo de tipo CFDI,
 * de ser asi, lee el contenido del archivo en XML, y genera un array final.
 *
 * @author Heriberto Monterrubio <heri185403@gmail.com, heriberto@inodata.com.mx>
 */
class EmailReader 
{ 
    private $emailAcount;
    
    public function __construct($emailAcount, $email, $password)
    {
        $this->emailAcount = $this->getEmailAcountObject($emailAcount, $email, $password);
    }
    
    public function getCFDIContents()
    {
        return $this->emailAcount->loadInbox();
    }
    
    private function getEmailAcountObject($emailAcount, $email, $password)
    {
        switch ($emailAcount)
        {
            case 'gmail':
                return new GmailAcount($email, $password);
            case 'hotmail':
                //
                break;
            case 'yahoo':
                //
                break;
        }
        
        return null;
    }
}