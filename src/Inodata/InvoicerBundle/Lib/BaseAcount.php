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
