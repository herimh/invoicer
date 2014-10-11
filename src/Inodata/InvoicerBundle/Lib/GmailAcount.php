<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * @author Heriberto Monterrubio <heri185403@gmail.com, heriberto@inodata.com.mx>
 */
class GmailAcount extends BaseAcount
{   
    private $host = '{imap.gmail.com:993/imap/ssl}INBOX';
    
    public function getCFDIsFromUnseen()
    {
        $imap = imap_open($this->host, $this->email, $this->password) 
                or die('cant connect');
        
        $cfdis = [];
        
        if($imap){
            $newEmails = imap_search($imap, 'UNSEEN');
            foreach ($newEmails as $newEmail){
                $uid = imap_uid($imap, $newEmail);
                $cfdi = CFDIXmlReader::fetchContentFromEmail($imap, $uid);
                
                if($cfdi){
                    $cfdis = array_merge($cfdis, $cfdi);
                }
            }
        }
        
        return $cfdis;
    } 
}
