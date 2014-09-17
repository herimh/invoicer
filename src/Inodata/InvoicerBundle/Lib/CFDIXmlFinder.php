<?php
namespace Inodata\InvoicerBundle\Lib;

class CFDIXmlFinder
{
    const MIME_TYPE_TEXT = 0;
    const MIME_TYPE_MULTIPART = 1;
    const MIME_TYPE_MESSAGE = 2;
    const MIME_TYPE_APPLICATION = 3;
    const MIME_TYPE_AUDIO = 4;
    const MIME_TYPE_IMAGE = 5;
    const MIME_TYPE_VIDEO = 6;
    const MIME_TYPE_OTHER = 7;
    
    public static function getCFDIFromFile($file){
        
    }

    /*public static function getCFDIFromText($text){
        return $this->getCFDIFromText($text);
    }*/
    
    private static function getCFDIFromText($body, $structure){
        print_r("Text <br>");
        print_r($body);        print_r("<br><br>");
        print_r($structure);        print_r("<br><br>");
        return "";
    }
    
    private static function getCFDIFromApplication($body, $structure){
        print_r("Application <br>");
        print_r($body);        print_r("<br>");
        print_r($structure);        print_r("<br>");
        return "";
    }

    protected function findUrlsInText($body){
        return '';
    }
    
    protected function getDownloadCFDIFile($url){
        return null;
    }
    
    protected function getTextDecoded($encoding, $text){
        
    }

    public static function fetchContent($imap, $uid, $mainMimeType = null, $structure = false, $partNumber = 1 )
    {
        if(!$structure){
            $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }
        
        if(!$mainMimeType){
            $mainMimeType = $structure->type;
        }
        
        $content = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
        
        print_r("pART ".$partNumber."<br><br>");
        print_r($content."<br><br>");
        print_r($structure);
        print_r("<br><br>");
        
        switch ($structure->type)
        {
            case self::MIME_TYPE_TEXT:
                if($mainMimeType == self::MIME_TYPE_TEXT){
                    return self::getCFDIFromText($content, $structure);
                }
                break; 
                
            case self::MIME_TYPE_MULTIPART:
                //TODO: logic for multipart email
                foreach ($structure->parts as $index => $subStruct){
                    
                    $newPartNumber = $partNumber.".".($index+1);
                    $data = self::fetchContent($imap, $uid, $mainMimeType, $subStruct, $newPartNumber);
                    
                    return $data;
                }
                break;
            
            case self::MIME_TYPE_MESSAGE:
                //TODO: logic for message type
                break;
            
            case self::MIME_TYPE_APPLICATION:
                return self::getCFDIFromApplication($content, $structure);
                break;
            
            case self::MIME_TYPE_AUDIO:
                //TODO: logic for audio type
                break;
            
            case self::MIME_TYPE_IMAGE:
                //TODO: logic for image type
                break;
            
            case self::MIME_TYPE_VIDEO:
                //TODO: logic fotr video type
                break;
            
            default :
                //TODO: logic for others
                break;
        }
        
        return null;
    }
    
}


