<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * @author Heriberto Monterrubio <heri185403@gmail.com, heriberto@inodata.com.mx>
 */
class EmailReader 
{ 
    private $emailAcount;

    public function __construct($emailAcount, $email, $password, $container)
    {
        $this->emailAcount = $this->getEmailAcountObject($emailAcount, $email, $password);
        $this->emailAcount->setContainer( $container);
    }
    
    public function getCFDIsFromEmail(){
        return $this->emailAcount->getCFDIsFromUnseen();
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
