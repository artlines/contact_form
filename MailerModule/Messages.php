<?php

namespace MailerModule;

/**
 * Description of Messages
 *
 * @author b497
 */
class Messages {    
    const GLOBAL_ERROR_MESSAGE = "Errors while process data.";
    
    const DIRECTORY_NOT_EXIST = "Directory not exist. ";
    const DIRECTORY_NOT_WRITABLE = "Directory not writable. ";
    const MIMETYPE_NOT_IMAGE = "Wrong FileType. It must be image. ";
    const MOVE_FILE_ERROR = "Cant move POSTed file to temp directory. ";    
    const ZIP_CREATE_ERROR = "Cant create zip file. ";
    const ZIP_ADDFILE_ERROR = "Cant add file to zip. ";
    const MAIL_SEND_ERROR = "Cant send eMail.";
    const CLEANUP_TEMPDIRECTORY_ERROR = "Cant remove temp directory.";
}
