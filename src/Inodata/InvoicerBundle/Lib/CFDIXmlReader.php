<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * Description of CFDIXmlReader
 *
 * @author heriberto
 */
class CFDIXmlReader 
{
    private static $instance = null;


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
    const TEMP_FOLDER =  "uploads/temp/";
    const BACKUP_FOLDER = "uploads/backup/";
    
    
    private static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CFDIXmlReader();
        }
        
        return self::$instance;
    }

    /**
     * @param object $imap email connection using imap port
     * @param string $uid  email uid to read               
     * @param string $mainMimeType  email mime type over recursivity when email is multipart
     * @param array $structure email structure
     * @param int $partNumber email number part to control multipart recursivity
     * 
     * @return array  collection of parsed CFDIs
     */
    public static function fetchContentFromEmail($imap, $uid, $mainMimeType = null, $structure = false, $partNumber = 1)
    {
        $cfdiXmlReader = self::getInstance();
        
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
                    return $cfdiXmlReader->getFromEmailText($content, $structure);
                }
                break; 
                
            case self::MIME_TYPE_MULTIPART:
                $xmlDocs = [];
                foreach ($structure->parts as $index => $subStruct){
                    
                    $newPartNumber = ($index+1);
                    $xmlDocs = self::fetchContentFromEmail($imap, $uid, $mainMimeType, $subStruct, $newPartNumber);
                }
                return $xmlDocs;
            
            case self::MIME_TYPE_MESSAGE:
                //TODO: logic for message type
                break;
            
            case self::MIME_TYPE_APPLICATION:
                return $cfdiXmlReader->getFromEmailApplication($content, $structure);
            
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
    }
    
    /**
     * Parse email text to find URLs and try to download XMLs to parse an retrieve.
     * 
     * @param binary $content of email part
     * @param type $structure email part structure
     * 
     * * @return array  collection of parsed CFDIs
     */
    protected function getFromEmailText($content, $structure){
        //@TODO: pending 
    }
    
    /**
     * Get application files from email content and find XMLs to parse and retrieve
     */
    protected function getFromEmailApplication($content, $structure)
    {
        $contentEncoded = $this->encodeContent($content, $structure->encoding);
        $applicationName = $this->getApplicationName($structure);
        
        //print_r($structure); exit();
        $subtype = $structure->subtype;
        
        if($subtype == 'OCTET-STREAM'){
            $subtype = $this->getApplicationSubtype($structure);
        }
        
        switch (strtoupper($subtype)){
            case 'PDF':
                break;
            case 'XML':
                break;
            case 'ZIP':
                if($this->findXmlFromZIP($contentEncoded, $applicationName)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
            case 'RAR':
                if($this->findXmlFromRAR($contentEncoded, $applicationName)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
            case 'TAR':
                if($this->findXmlFromTar($contentEncoded, $applicationName)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
        }
    }
    
    /**
     * Try to find an xml files from url 
     * @param string $url 
     */
    private function findXmlFromUrl($url){
        
    }
    
    /**
     * Extract the ZIP file to find XML files
     * 
     * @param binary $content zip file content as binary data
     * @param string $filename name for file
     */
    private function findXmlFromZIP($content, $filename)
    {
        $tempDir = $this->getTempDirectory();
        
        //Creating zip file temporally
        $file = fopen($tempDir.$filename, 'w');
        fwrite($file, $content);
        //fclose($file);
        
        //Uncompress zip file
        $zip = new \ZipArchive();
        $res = $zip->open($tempDir.$filename);
        if($res === TRUE){
            $zip->extractTo($tempDir);
            $zip->close();
        }
        
        fclose($file);
        //Delet zip file created previously
        unlink($tempDir.$filename);
        
        
        return true;
    }
    
    /**
     * Extract the RAR file to find XML files
     */
    private function findXmlFromRAR($content, $filename)
    {
        $tempDir = $this->getTempDirectory();
        
        //Creating zip file temporally
        $file = fopen($tempDir.$filename, 'w');
        fwrite($file, $content);
        
        $rar = rar_open($tempDir.$file);
        $files = rar_list($rar);
        
        foreach ($files as $file){
            $entry = rar_entry_get($rar, $file);
            $entry->extract('.');
        }
        
        rar_close($rar);
        unlink($tempDir.$filename);
        
        return true;
    }
    
    /**
     * Extract the TAR file to find XML files
     */
    private function findXmlFromTar($conent, $filename){
        return false;
    }
    
    /**
     * Read XML files from a directory and retrieve, parse if CFDI type was found and retrieve it as an array
     * 
     * @param type $dir directory where xml files are stored
     */
    public function getFromDir($dir){
        $files = scandir($dir);
        
        $cfdis = [];
        foreach ($files as $file){
            //print_r($file);
            if(mime_content_type($dir.$file) == self::TYPE_XML){
                $cfdi = new CfdiParser\Parser($dir.$file);
                $cfdis[] = $cfdi->getCfdiArray();
            }
            
            if(is_file($dir.$file)){
                rename($dir.$file, $this->getBackupDir().$file);
            }
            
            //TODO: if is folder
        }
        
        print_r($cfdis); exit();
        
        return $cfdis;
    }
    
    private function encodeContent($content, $encodingType)
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
    
    private function getApplicationName($structure){
        if(isset($structure->parameters[0])){
            return $structure->parameters[0]->value;
        }
        
        return "file_".  strtotime(date("Y-m-d H:i:s"));
    }
    
    private function getApplicationSubtype($structure)
    {
        $filename = $this->getApplicationName($structure);
        
        return substr($filename, -3, 3);
    }
    
    private function getTempDirectory(){
        $dir =  __DIR__."/../../../../web/".self::TEMP_FOLDER;
        
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        
        return $dir;
    }
    
    private function getBackupDir(){
        $dir =  __DIR__."/../../../../web/".self::BACKUP_FOLDER;
        
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        
        return $dir;
    }
}
