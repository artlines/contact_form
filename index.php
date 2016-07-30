<?php

//silent
new \MailerModule\OrderPhoto($_POST, $_FILES, "test@example.com", "test@example.com", 200000000, true);


//exceptionMode
try {
    new \MailerModule\OrderPhoto($_POST, $_FILES, "test@example.com", "test@example.com", 200000000, false);
}
/// you can catch each type
catch (\MailerModule\DirectoryNotExistException $e) {
    echo "Message: " . $e->getMessage();
    echo "Dir: " . $e->getDir();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\DirectoryNotWritableException $e) {
    echo "Message: " . $e->getMessage();
    echo "Dir: " . $e->getDir();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\MimeTypeNotImageException $e) {
    echo "Message: " . $e->getMessage();
    echo "Dir: " . $e->getFilename();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\MoveFileToTempException $e) {
    echo "Message: " . $e->getMessage();
    echo "Src: " . $e->getSrcFilename();
    echo "Dst: " . $e->getDstFilename();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\ZipCreationException $e) {
    echo "Message: " . $e->getMessage();
    echo "Zip: " . $e->getZipFilename();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\ZipAddFileException $e) {
    echo "Message: " . $e->getMessage();
    echo "Zip: " . $e->getZipFilename();
    echo "file: " . $e->getFilename();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\MailSendException $e) {
    echo "Message: " . $e->getMessage();
    echo "from: " . $e->getFom();
    echo "to: " . $e->getTo();
    echo "Code: " . $e->getCode();
}
//
catch (\MailerModule\ClenupTempDirectoryException $e) {
    echo "Message: " . $e->getMessage();
    echo "Dir: " . $e->getDirectory();
    echo "Code: " . $e->getCode();
}
// get base class, switch by codes
//catch (\MailerModule\MailerModuleException $e) {
catch (\Exception $e) {
    switch ($e->getCode()) {
        case MailerModule\DIRECTORY_NOT_EXIST_CODE :
            echo "DIRECTORY_NOT_EXIST";
            echo $e->getMessage();
            break;
        case MailerModule\DIRECTORY_NOT_WRITABLE_CODE :
            echo "DIRECTORY_NOT_WRITABLE";
            echo $e->getMessage();
            break;
        case MailerModule\MIMETYPE_NOT_IMAGE_CODE :
            echo "MIMETYPE_NOT_IMAGE";
            echo $e->getMessage();
            break;
        case MailerModule\MOVE_FILE_ERROR_CODE :
            echo "MOVE_FILE_ERROR";
            echo $e->getMessage();
            break;
        case MailerModule\ZIP_CREATE_ERROR_CODE :
            echo "ZIP_CREATE_ERROR";
            echo $e->getMessage();
            break;
        case MailerModule\ZIP_ADD_ERROR_CODE :
            echo "ZIP_ADD_ERROR";
            echo $e->getMessage();
            break;
        case MailerModule\MAIL_SEND_ERROR_CODE :
            echo "MAIL_SEND_ERROR";
            echo $e->getMessage();
            break;
        case MailerModule\CLEANUP_TEMPDIRECTORY_ERROR_CODE :
            echo "CLEANUP_TEMPDIRECTORY_ERROR";
            echo $e->getMessage();
            break;
        default :
            echo $e->getMessage();
            echo $e->getCode();
            break;
    }
}
