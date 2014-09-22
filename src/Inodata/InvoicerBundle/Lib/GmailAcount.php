<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * Description of GmailAcount
 *
 * @author heriberto
 */
class GmailAcount extends BaseAcount
{   
    private $host = '{imap.gmail.com:993/imap/ssl}INBOX';
    
    public function getEmailsList()
    {
        $imap = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', $this->email, $this->password) 
                or die('cant connect');
        
        if($imap){
            $newEmails = imap_search($imap, 'UNSEEN');
            
            foreach ($newEmails as $newEmail){
                $header = imap_header($imap, $newEmail);
                print_r($header->Subject.'<br><br>');
                
                $uid = imap_uid($imap, $newEmail);
                
                CFDIXmlFinder::fetchContent($imap, $uid);
                
                exit();
                return;
            }
        }
    }
    
    
}
