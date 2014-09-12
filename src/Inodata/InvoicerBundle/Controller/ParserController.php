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
        
        
        return new Response($emailReader->getCFDIContents());
    }
    
    protected function getEmailContent($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $emailContent = curl_exec($this->ch);
        
        return $emailContent;
    }


    /* Get the mails list from an existing gmail webservice*/
    protected function getNewEmailsList($email, $password)
    {
        $url = "https://mail.google.com/mail/feed/atom";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$email}:$password");
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $xmlEmails = curl_exec($ch);
        
        return $xmlEmails;
    }
    
    /* Get the mails list from an existing gmail webservice*/
    /*protected function getGlobalNewEmailsList()
    {
        $url = "https://mail.google.com/mail/feed/atom";

        //curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($this->ch, CURLOPT_USERPWD, "{$email}:$password");
        curl_setopt($this->ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        
        $xmlEmails = curl_exec($this->ch);
        
        return $xmlEmails;
    }*/
    
    protected function loginToEmail($email, $password)
    {
        //Setting up the curl for login page request
        $loginUrl = "https://accounts.google.com/ServiceLogin";
        curl_setopt($this->ch, CURLOPT_URL, $loginUrl);
        $loginPage = curl_exec($this->ch);
        
        //Parsing the html response, using simple_html_dom library
        $parser = $this->container->get("simple_html_dom");
        $form = $parser->load($loginPage)->find('form', 0);
        
        /* Create a data array using the login form inputs */
        $data = [];
        foreach ($form->find('input') as $input){
            $data[$input->name] = urlencode($input->value);
        }
        $data['Email'] = urlencode($email);
        $data['Passwd'] = urlencode($password);
        
        //Url-ify the data for the POST
        $dataAsString = "";
        foreach($data as $key=>$value) {
            $dataAsString .= $key.'='.$value.'&';            
        }
        rtrim($dataAsString, '&');
        
        //Setting up the curl for the authentication request
        $authUrl = $form->action;
        curl_setopt($this->ch, CURLOPT_URL, $authUrl);
        curl_setopt($this->ch, CURLOPT_REFERER, $loginUrl);
        curl_setopt($this->ch, CURLOPT_POST, count($data));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $dataAsString);
        $authResult = curl_exec($this->ch);
        
        curl_setopt($this->ch, CURLOPT_URL, "https://mail.google.com/mail/u/0/?pli=1#inbox");
        $inbox = curl_exec($this->ch);
        
        print_r($inbox); exit();
        
        return $authResult;
    }
    
    private function initCurlAdapter(){
        
    }
}
