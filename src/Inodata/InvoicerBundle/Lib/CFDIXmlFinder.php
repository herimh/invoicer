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
    
    const TYPE_XML = 'application/xml';
    const ZIP_FILES_FOLDER = "/home/heriberto/invoicer/zip/";
    
    private static function getCFDIFromText($body, $structure){
        print_r("Text <br>");
        print_r(imap_base64($body));        print_r("<br><br>");
        print_r($structure);        print_r("<br><br>");
        return "";
    }
    
    private static function getCFDIFromApplication($content, $structure){
        print_r("Text <br>");
        print_r($structure); print_r("<br><br>");
        
        $contentEncoded = self::encodeContent($content, $structure->encoding);
        $applicationName = self::getApplicationName($structure);
        
        switch ($structure->subtype){
            case 'pdf':
                break;
            case 'xml':
                break;
            default :
                return self::parseFileFromZIP($contentEncoded, $applicationName);
        }
        return "";
    }
    
    private static function parseFileFromZIP($content, $fileName)
    {
        //Creating zip file temporally
        $file = fopen(self::ZIP_FILES_FOLDER.$fileName, 'w');
        fwrite($file, $content);
        //fclose($file);
        
        //Uncompress zip file
        $zip = new \ZipArchive();
        $res = $zip->open(self::ZIP_FILES_FOLDER.$fileName);
        if($res === TRUE){
            $zip->extractTo(self::ZIP_FILES_FOLDER);
            $zip->close();
        }
        
        //Delet zip file created previously
        unlink(self::ZIP_FILES_FOLDER.$fileName);
        
        //Reading folder contend
        return self::findXMLFromDir(self::ZIP_FILES_FOLDER);
    }
    
    private static function findXMLFromDir($dir){
        $files = scandir($dir);
        
        $xmlDocs = [];
        foreach ($files as $file){
            if(mime_content_type($dir.$file) == self::TYPE_XML){
                $xmlDocs[] = simplexml_load_file($dir.$file);
            }
        }
        
        print_r($xmlDocs); exit();
        
        return $xmlDocs;
    }

    protected function findUrlsInText($body){
        return '';
    }
    
    protected function getDownloadCFDIFile($url){
        return null;
    }
    
    protected function getTextDecoded($encoding, $text){
        
    }

    public static function fetchContent($imap, $uid, $mainMimeType = null, $structure = false, $partNumber = 1)
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
                if($mainMimeType == self::MIME_TYPE_TEXT){
                    return self::getCFDIFromText($content, $structure);
                }
                break; 
                
            case self::MIME_TYPE_MULTIPART:
                $xmlDocs = [];
                foreach ($structure->parts as $index => $subStruct){
                    
                    $newPartNumber = ($index+1);
                    $xmlDocs = self::fetchContent($imap, $uid, $mainMimeType, $subStruct, $newPartNumber);
                }
                return $xmlDocs;
            
            case self::MIME_TYPE_MESSAGE:
                //TODO: logic for message type
                break;
            
            case self::MIME_TYPE_APPLICATION:
                return self::getCFDIFromApplication($content, $structure);
            
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
        
        return $xmlDocs;
    }
    
    protected static function encodeContent($content, $encodingType)
    {
        switch ($encodingType){
            case self::ENCODING_7BIT:
                return '';//imap_7bit($content);
            case self::ENCODING_8BIT:
                return imap_8bit($content);
            case self::ENCODING_BASE64:
                return imap_base64($content);
            case self::ENCODING_BINARY:
                return imap_binary($content);
            case self::ENCODING_QUOTED_PRINTABLE;
                return imap_qprint($content);
        }
        
        return $content;
    }
    
    protected static function getApplicationName($structure){
        if(isset($structure->parameters[0])){
            return $structure->parameters[0]->value;
        }
        
        return "file_".  strtotime(date("Y-m-d H:i:s"));
    }
    
}


