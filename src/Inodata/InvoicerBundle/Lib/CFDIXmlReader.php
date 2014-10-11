<?php

namespace Inodata\InvoicerBundle\Lib;

/**
 * Se encarga de leer los correos nuevos de una cuenta, determina si el contenido es un archivo de tipo CFDI,
 * de ser asi, lee el contenido del archivo en XML, y genera un array final.
 *
 * @author Heriberto Monterrubio <heri185403@gmail.com, heriberto@inodata.com.mx>
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
    
    const FILE_TYPE_XML = 'application/xml';
    const FILE_TYPE_PDF = 'application/pdf';
    const FILE_TYPE_RAR = 'application/x-rar';
    const FILE_TYPE_ZIP = 'application/zip';
    const FILE_TYPE_HTML = 'text/html';
    const FILE_TYPE_TEXT = 'text/plain';
    
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
                $cfdis = [];
                
                foreach ($structure->parts as $index => $subStruct){
                    $newPartNumber = ($index+1);
                    $cfdi = self::fetchContentFromEmail($imap, $uid, $mainMimeType, $subStruct, $newPartNumber);
                    if($cfdi){
                        $cfdis = array_merge($cfdis, $cfdi);
                    }
                }
                return $cfdis;
            
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
    protected function getFromEmailText($content, $structure)
    {
        $contentEncoded = $this->encodeContent($content, $structure->encoding);
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        
        $urls = [];
        preg_match_all($reg_exUrl, $contentEncoded, $urls);
        
        $filesDownloaded = [];
        if($urls[0]){
            foreach ($urls[0] as $index=>$url){
                $downloadedFile = $this->downloadFile($url,$index);
                if($downloadedFile){
                    $filesDownloaded[] = $downloadedFile;
                }
            }
        }
        
        foreach ($filesDownloaded as $file){
            $mimeType = mime_content_type($file);
            switch ($mimeType){
                case self::FILE_TYPE_XML;
                    rename($file, $file.".xml");
                    break;
                case self::FILE_TYPE_PDF:
                    rename($file, $file.".pdf");
                    break;
                case self::FILE_TYPE_ZIP:
                    rename($file, $file.".zip");
                    $this->findXmlFromZIP($file.".zip");
                    break;
                case self::FILE_TYPE_RAR:
                    rename($file, $file.".rar");
                    $this->findXmlFromRAR($file.".rar");
                    break;
                
                //TODO: case for TAR, 7zip, GZ an other compress technologies files
                
                default :
                    unlink($file);
                    break;
            }
        }
        
        return $this->getFromDir($this->getTempDirectory());
    }
    
    /**
     * Get application files from email content and find XMLs to parse and retrieve
     */
    protected function getFromEmailApplication($content, $structure)
    {
        $contentEncoded = $this->encodeContent($content, $structure->encoding);
        $applicationName = $this->getApplicationName($structure);
        
        $subtype = $structure->subtype;
        
        if($subtype == 'OCTET-STREAM'){
            $subtype = $this->getApplicationSubtype($structure);
        }
        
        switch (strtoupper($subtype)){
            case 'PDF':
                $this->saveFileContent($this->getTempDirectory().$applicationName, $contentEncoded);
                return null;
            case 'XML':
                if($this->saveFileContent($this->getTempDirectory().$applicationName, $contentEncoded)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
            case 'ZIP':
                if($this->findXmlFromZIP($this->getTempDirectory().$applicationName, $contentEncoded)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
            case 'RAR':
                if($this->findXmlFromRAR($this->getTempDirectory().$applicationName, $contentEncoded)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
            case 'TAR':
                if($this->findXmlFromTar($this->getTempDirectory().$applicationName, $contentEncoded)){
                    return $this->getFromDir($this->getTempDirectory());
                }
                break;
        }
    }
    
    /**
     * Try to find an xml files from url 
     * @param string $url 
     */
    private function downloadFile($url, $index){
        $file = fopen($url, 'r');

        if($file){
            $newFilename = $this->getTempDirectory()."_file{$index}_".date("Ymd_H:i:s");
            $source = fopen($newFilename, 'w');
            $content= stream_get_contents($file);
            
            if($content){
                fwrite($source, $content);
                fclose($source);
                
                return $newFilename;
            }
        }
        
        return null;
    }
    
    private function saveFileContent($fileDir, $content)
    {
        $file = fopen($fileDir, "w");
        
        if($file){
            fwrite($file, $content);
            fclose($file);
            
            return true;
        }
        
        return false;
    }

        /**
     * Extract the ZIP file to find XML files
     * 
     * @param binary $content zip file content as binary data
     * @param string $fileDir name for file
     */
    private function findXmlFromZIP($fileDir, $content=null)
    {
        $tempDir = $this->getTempDirectory();
        
        //If file is received as binary
        if($content){
            //Creating zip file temporally
            $file = fopen($fileDir, 'w');
            fwrite($file, $content);
            fclose($file);
        }
        
        //Uncompress zip file
        $zip = new \ZipArchive();
        $res = $zip->open($fileDir);
        if($res === TRUE){
            $zip->extractTo($tempDir);
            $zip->close();
        }
        
        fclose($file);
        //Delet zip file created previously
        unlink($fileDir);
        
        return true;
    }
    
    /**
     * Extract the RAR file to find XML files
     */
    private function findXmlFromRAR($fileDir, $content=null)
    {
        $tempDir = $this->getTempDirectory();
        
        //If file is received as binary
        if($content){
            //Creating rar file temporally
            $file = fopen($fileDir, 'w');
            fwrite($file, $content);
            fclose($file);
        }
        
        $rar = rar_open($fileDir);
        $files = rar_list($rar);
        
        foreach ($files as $file){
            $file->extract($tempDir);
        }
        
        rar_close($rar);
        unlink($fileDir);
        
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
            if(mime_content_type($dir.$file) == self::FILE_TYPE_XML){
                $cfdi = new CfdiParser\Parser($dir.$file);
                $cfdis[] = $cfdi->getCfdiArray();
            }
            
            if(is_file($dir.$file)){
                rename($dir.$file, $this->getBackupDir().$file);
            }
            
            //TODO: if is folder
        }
        
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
