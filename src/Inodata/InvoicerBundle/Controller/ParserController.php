<?php

namespace Inodata\InvoicerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ParserController extends Controller
{
    //Define the curl adapter as global
    private $ch;
    
    public function indexAction()
    {
        $email = 'fact.herimh@gmail.com';
        $password = 'hola185403';
        
        $this->initCurlAdapter();
        
        $authResult = $this->loginToEmail($email, $password);
        $emailsList = $this->getMailsList();
        
        return new Response($emailsList);
    }
    
    /* Get the mails list from an existing gmail webservice*/
    protected function getMailsList()
    {
        $url = "https://mail.google.com/mail/feed/atom";
        
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "{$email}:$password");
        curl_setopt($this->ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        
        $xmlEmails = curl_exec($this->ch);
        
        return $xmlEmails;
    }
    
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
        
        return $authResult;
    }
    
    private function initCurlAdapter(){
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
}
