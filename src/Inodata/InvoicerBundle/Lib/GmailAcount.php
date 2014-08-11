<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * Description of GmailAcount
 *
 * @author heriberto
 */
class GmailAcount extends BaseAcount
{
    private $loginUrl = 'https://accounts.google.com/ServiceLogin';
    private $inboxUrl = 'https://mail.google.com/mail/u/0/?pli=1#inbox';


    public function login() 
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->loginUrl);
        $loginPage = curl_exec($this->ch);
        
        //Parsing the html response, using simple_html_dom library
        $parser = $this->container->get("simple_html_dom");
        $form = $parser->load($loginPage)->find('form', 0);
        
        /* Create a data array using the login form inputs */
        $data = [];
        foreach ($form->find('input') as $input){
            $data[$input->name] = urlencode($input->value);
        }
        $data['Email'] = urlencode($this->email);
        $data['Passwd'] = urlencode($this->password);
        
        //Url-ify the data for the POST
        $dataAsString = "";
        foreach($data as $key=>$value) {
            $dataAsString .= $key.'='.$value.'&';            
        }
        rtrim($dataAsString, '&');
        
        //Setting up the curl for the authentication request
        $authUrl = $form->action;
        curl_setopt($this->ch, CURLOPT_URL, $authUrl);
        curl_setopt($this->ch, CURLOPT_REFERER, $this->loginUrl);
        curl_setopt($this->ch, CURLOPT_POST, count($data));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $dataAsString);
        curl_exec($this->ch);
    }
    
    public function loadInbox() 
    {
        $this->login();
        
        curl_setopt($this->ch, CURLOPT_URL, $this->inboxUrl);
        return curl_exec($this->ch);
    }
}
