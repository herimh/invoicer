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
    
    /* Get the mails list from an existing gmail webservice*/
    public function getNewEmailsList()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->email}:$this->password");
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->newEmailsUrl);
        $xmlEmails = curl_exec($ch);
        
        return $xmlEmails;
    }
    
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
