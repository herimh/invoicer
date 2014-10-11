<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * Description of GmailAcount
 *
 * @author heriberto
 */
class HotmailAcount extends BaseAcount
{   
    //@TODO: Define imap connection with hotmail accounts
    
    public function getCFDIsFromUnseen()
    {
        //@TODO: read new emnails using imap and find CFDIs
        return null;
    }
}
