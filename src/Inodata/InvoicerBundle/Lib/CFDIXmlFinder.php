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
    
    const ENCODING_7BIT = 0;
    const ENCODING_8BIT = 1;
    const ENCODING_BINARY = 2;
    const ENCODING_BASE64 = 3;
    const ENCODING_QUOTED_PRINTABLE = 4;


    public static function getCFDIFromFile($file){
        
    }

    /*public static function getCFDIFromText($text){
        return $this->getCFDIFromText($text);
    }*/
    
    private static function getCFDIFromText($body, $structure){
        print_r("Text <br>");
        print_r(imap_base64($body));        print_r("<br><br>");
        print_r($structure);        print_r("<br><br>");
        return "";
    }
    
    private static function getCFDIFromApplication($content, $structure){
        switch ($structure->subtype){
            case 'pdf':
                break;
            case 'xml':
                break;
            default :
                self::parseFileFromZIP(self::encodeContent($content, $structure->encoding));
                break;
        }
        return "";
    }
    
    private static function parseFileFromZIP($content)
    {
        $file = fopen("email-122.zip", 'w+');
        fwrite($file, $content);
        fclose($file);
        
        
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
        
        switch ($structure->type)
        {
            case self::MIME_TYPE_TEXT:
                //if($mainMimeType == self::MIME_TYPE_TEXT){
                    self::getCFDIFromText($content, $structure);
                //}
                break; 
                
            case self::MIME_TYPE_MULTIPART:
                //TODO: logic for multipart email
                foreach ($structure->parts as $index => $subStruct){
                    
                    $newPartNumber = ($index+1);
                    self::fetchContent($imap, $uid, $mainMimeType, $subStruct, $newPartNumber);
                    
                    //return $data;
                }
                break;
            
            case self::MIME_TYPE_MESSAGE:
                //TODO: logic for message type
                break;
            
            case self::MIME_TYPE_APPLICATION:
                self::getCFDIFromApplication($content, $structure);
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
    
    protected static function encodeContent($content, $encodingType)
    {
        switch ($encodingType){
            case self::ENCODING_7BIT:
                return imap_7bit($content);
            case self::ENCODING_8BIT:
                return imap_8bit($content);
            case self::ENCODING_BASE64:
                print_r("Encoding base64");
                return imap_base64($content);
            case self::ENCODING_BINARY:
                return imap_binary($content);
            case self::ENCODING_QUOTED_PRINTABLE;
                return imap_qprint($content);
        }
        
        return $content;
    }
    
}


