<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MailerModule;

/**
 * Description of Exceptions
 *
 * @author b497
 */

const DIRECTORY_NOT_EXIST_CODE = 0xBADBAD01;
const DIRECTORY_NOT_WRITABLE_CODE = 0xBADBAD02;
const MIMETYPE_NOT_IMAGE_CODE = 0xBADBAD03;
const MOVE_FILE_ERROR_CODE = 0xBADBAD04;
const ZIP_CREATE_ERROR_CODE = 0xBADBAD05;
const ZIP_ADD_ERROR_CODE = 0xBADBAD06;
const MAIL_SEND_ERROR_CODE = 0xBADBAD07;
const CLEANUP_TEMPDIRECTORY_ERROR_CODE = 0xBADBAD08;

class MailerModuleException extends \Exception{
    //just define base class for exceptions
}

class DirectoryNotExistException extends \MailerModule\MailerModuleException {
    private $dirPath;
    public function __construct($dirPath, $message = "", \Exception $previous = null) {
        $this->dirPath = $dirPath;
        
        if(empty($message)){
            $message = Messages::DIRECTORY_NOT_EXIST. ": ".addslashes($dirPath);
        }       
        parent::__construct($message, DIRECTORY_NOT_EXIST_CODE, $previous);
    }
    
    public function getDir(){
        return $this->dirPath;
    }
}

class DirectoryNotWritableException extends \MailerModule\MailerModuleException {
    private $dirPath;
    public function __construct($dirPath, $message = "", \Exception $previous = null) {
        $this->dirPath = $dirPath;

        if (empty($message)) {
            $message = Messages::DIRECTORY_NOT_WRITABLE. ": " . addslashes($dirPath);
        }
        parent::__construct($message, DIRECTORY_NOT_WRITABLE_CODE, $previous);
    }
    public function getDir() {
        return $this->dirPath;
    }
}

class MimeTypeNotImageException extends \MailerModule\MailerModuleException {
    private $wrongFilename;
    
    public function __construct($Filename, $message = "", \Exception $previous = null) {
        $this->wrongFilename = $Filename;

        if (empty($message)) {
            $message = Messages::MIMETYPE_NOT_IMAGE . ": " . addslashes($Filename);
        }
        parent::__construct($message, MIMETYPE_NOT_IMAGE_CODE, $previous);
    }

    public function getFilename() {
        return $this->wrongFilename;
    }
}

class MoveFileToTempException extends \MailerModule\MailerModuleException {
    private $src;
    private $dst;

    public function __construct($SourcePlace, $DestinationPlace, $message = "", \Exception $previous = null) {
        $this->src = $SourcePlace;
        $this->dst = $DestinationPlace;

        if (empty($message)) {
            $message = Messages::MOVE_FILE_ERROR . ": from '" . addslashes($SourcePlace) ."' to '".addslashes($DestinationPlace). "'";
        }
        parent::__construct($message, MOVE_FILE_ERROR_CODE, $previous);
    }

    public function getSrcFilename() {
        return $this->src;
    }

    public function getDstFilename() {
        return $this->dst;
    }
}

class ZipCreationException extends \MailerModule\MailerModuleException {
    private $zipFile;
    public function __construct($filename, $message = "", \Exception $previous = null) {
        $this->zipFile = $filename;

        if (empty($message)) {
            $message = Messages::ZIP_CREATE_ERROR . ": '" . addslashes($filename) . "'";
        }
        parent::__construct($message, ZIP_CREATE_ERROR_CODE, $previous);
    }

    public function getZipFilename() {
        return $this->zipFile;
    }
}

class ZipAddFileException extends \MailerModule\MailerModuleException {

    private $zipFile;
    private $addFile;

    public function __construct($zip,$filename, $message = "", \Exception $previous = null) {
        $this->zipFile = $zip;
        $this->addFile = $filename;

        if (empty($message)) {
            $message = Messages::ZIP_ADDFILE_ERROR . ": add '" . addslashes($filename) . "' to '" . addslashes($zip) . "'";
        }
        parent::__construct($message, ZIP_ADD_ERROR_CODE, $previous);
    }

    public function getZipFilename() {
        return $this->zipFile;
    }
    
    public function getFilename() {
        return $this->addFile;
    }

}

class MailSendException extends \MailerModule\MailerModuleException {

    private $from;
    private $to;

    public function __construct($fromEmail, $toEmail, $message = "", \Exception $previous = null) {
        $this->from = $fromEmail;
        $this->to = $toEmail;

        if (empty($message)) {
            $message = Messages::MAIL_SEND_ERROR . ": from '" . addslashes($fromEmail) . "' to '" . addslashes($toEmail) . "'";
        }
        parent::__construct($message, MAIL_SEND_ERROR_CODE, $previous);
    }

    public function getFom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }
}

class ClenupTempDirectoryException extends \MailerModule\MailerModuleException {
    
    private $dir;

    public function __construct($tempDir, $message = "", \Exception $previous = null) {
        $this->dir = $tempDir;

        if (empty($message)) {
            $message = Messages::CLEANUP_TEMPDIRECTORY_ERROR . ": '" . addslashes($tempDir) . "'";
        }
        parent::__construct($message, CLEANUP_TEMPDIRECTORY_ERROR_CODE, $previous);
    }

    public function getDirectory() {
        return $this->dir;
    }
}