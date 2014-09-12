<?php

namespace Inodata\InvoicerBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of BaseAcount
 *
 * @author heriberto
 */
class BaseAcount extends ContainerAware
{
    protected $ch = null;
    
    protected $email = '';
    protected $password = '';
    
    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
        
        $this->initCurlAdapter();
    }
    
    private function initCurlAdapter()
    {
        if(!$this->ch){
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; 
                en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/14.04 (maverick) Firefox/3.6.13');
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, '/tmp/gmailcookie.txt');
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, '/tmp/gmailcookie.txt');
            curl_setopt($this->ch, CURLOPT_HEADER, true);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); 
        }
    }
    
    public function resetCurlAdapter(){
        $this->ch = null;
        $this->initCurlAdapter();
    }
    
    protected function login(){
        
    }
    
    protected function loadInbox(){
        
    }
    
    protected function loadAndGetInbox(){
        
    }
    
    /**
     * @return xml List of new emails
     */
    protected function getNewEmails(){
        
    }
    
    /**
     * Read the CFDI content from an email give as parameter
     * 
     * @param String $url : define new email route
     * @param String $ch : get the CURL adapter.
     * @param String $dataType : define the data format to return, 'Array'' as default
     */
    protected function getEmailContent($url, $ch,  $dataType='array'){
        
    }
    
    /**
     * Read the CFDI content from an email give as parameter
     * 
     * @param Array $urls : define new email route
     * @param String $ch : get the CURL adapter.
     * @param String $dataType : define the data format to return, 'Array'' as default
     */
    protected function getEmailsContent($urls, $ch, $dataType='array'){
        
    }
    
}
