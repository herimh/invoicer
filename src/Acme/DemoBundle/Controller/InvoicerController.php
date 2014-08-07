<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 * @author Heriberto Monterrubio <heri185403@gmail.com>
 */
class InvoicerController extends Controller{
    
    public function indexAction()
    {
        //Login and get CURL object generated
        $ch = $this->getGmailInbox();
        
        $mailAsXml = $this->getMailsList($ch);
        //$xml = new \SimpleXMLElement($mailAsXml);
        
        print_r($mailAsXml); exit();
        
        $emailsLink = [];
        foreach ($xml->entry as $entry){
            $emailsLink[] = $entry->link['href'];
        }
        
        print_r($emailsLink); exit();
        
        $emailContent = $this->getMailContent($ch, $emailsLink[0]);
        
        return new Response($emailContent);
    }
    
    protected function getMailContent($ch, $url)
    {
        print($url);exit();
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/14.04 (maverick) Firefox/3.6.13');
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $emailContent = curl_exec($ch);
        
        return $emailContent;
    }


    /* Get the mails list from an existing gmail webservice*/
    protected function getMailsList($ch)
    {
        //$email = "fact.herimh@gmail.com";
        //$password = "hola185403";
        $url = "https://mail.google.com/mail/feed/atom";
        
        //$ch = curl_init();
        
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_USERPWD, "{$email}:$password");
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/14.04 (maverick) Firefox/3.6.13');
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $xmlEmails = curl_exec($ch);
        
        return $xmlEmails;
    }
    
    protected function loginToGmail($email, $password){
        
    }


    /** Get gmail inbox content using curl**/
    protected function getGmailInbox()
    {
        $email = "fact.herimh@gmail.com";
        $password = "hola185403";
        $urlLogin = "https://accounts.google.com/ServiceLogin";
        
        //Creating a Login request using curl library and try to get a success response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlLogin);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/gmailcookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/gmailcookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/14.04 (maverick) Firefox/3.6.13');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $login = curl_exec($ch);
        
        //Parsing the html response, using simple_html_dom library
        $parser = $this->container->get("simple_html_dom");
        $form = $parser->load($login)->find('form', 0);
        
        //print_r($login); exit();
        
        $urlAuth = $form->action;
        
        /* Create an array using the login form inputs */
        $data = [];
        foreach ($form->find('input') as $input){
            $data[$input->name] = urlencode($input->value);
        }
        $data['Email'] = urlencode($email);
        $data['Passwd'] = urlencode($password);
        //$data['PersistentCookie'] = urlencode('no');
        
        //Url-ify the data for the POST
        $dataAsString = "";
        foreach($data as $key=>$value) {
            $dataAsString .= $key.'='.$value.'&';            
        }
        rtrim($dataAsString, '&');
        
        curl_setopt($ch, CURLOPT_URL, $urlAuth);
        curl_setopt($ch, CURLOPT_REFERER, $urlLogin);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataAsString);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        
        
        
        curl_setopt($ch, CURLOPT_URL, "https://mail.google.com/mail/u/0/?pli=1#inbox");
        $inbox = curl_exec($ch);
        
        print_r($inbox); exit();
        
        return $ch;
    }
}
