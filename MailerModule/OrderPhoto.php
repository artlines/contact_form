<?php

/**
 * OrderPhoto Module
 * 
 * @author artur gazetdinov
 * @link https://github.com/artlines/contact_form
 * 
 * @author bercut497
 * @link https://github.com/bercut497/contact_form
 */
require_once 'class.phpmailer.php';

namespace MailerModule;

class OrderPhoto {

    private $tmp_dir;
    private $archive_dir;
    private $archives_size;
    private $response;
    private $post;
    private $files;
    private $filename;
    private $filelink;
    private $toMail = 'test@example.com';
    private $fromMail = 'test@example.com';
    private $archMaxSize = 200000000;
    private $silentMode = true;

    /**
     * Предварительная проверка есть ли у нас доступ 
     * к необходимым директориям
     * 
     * @throws DirectoryNotExistException
     * @throws DirectoryNotWritableException
     */
    private function checkDirs() {
        //перемнная хранилище прловеряемой директории
        //используется для общей ошибки доступа.
        $d = "";
        try {
            $d = $this->tmp_dir;
            if (!is_dir($d) && !mkdir($d, 0775)) {
                throw new DirectoryNotExistException($d);
            }

            $d = $this->archive_dir;
            if (!is_dir($d) && !mkdir($d, 0775)) {
                throw new DirectoryNotExistException($d);
            }
        } catch (DirectoryNotExistException $e) {
            // ловим свое исключение что бы не сработало 
            // общее правило описанное ниже
            // и передаем выше 
            throw $e;
        } catch (Exception $e) {
            // общая ошибка доступа к директории
            throw new DirectoryNotExistException($d, Messages::DIRECTORY_NOT_EXIST, $e);
        }

        try {
            $d = $this->tmp_dir;
            if (!is_writable($d) && !chmod($d, 0775)) {
                throw new DirectoryNotWritableException($d);
            }

            $d = $this->archive_dir;
            if (!is_writable($d) && !chmod($d, 0775)) {
                throw new DirectoryNotWritableException($d);
            }
        } catch (DirectoryNotWritableException $e) {
            // ловим свое исключение что бы не сработало 
            // общее правило описанное ниже
            // и передаем выше 
            throw $e;
        } catch (Exception $e) {
            throw new DirectoryNotWritableException($d, Messages::DIRECTORY_NOT_WRITABLE, $e);
        }
    }

    private function doWork($post, $files) {
        $this->post = $this->validate_post($post);
        $this->files = $files;

        $this->tmp_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmp_files';
        $this->archive_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'archives';
        $this->archives_size = $this->size_dir();

        $this->checkDirs();

        $this->process_files();
        $this->zip_files();

        if (!empty($this->workMail)) {
            $this->send_mail();
        }

        $this->clean_tmp();

        if ($this->archives_size > $this->archMaxSize) {
            rmdir($archive_dir);
            mkdir($archive_dir);
        }
    }

    /**
     * 
     * @param type $post POST data
     * @param type $files Posted files
     * @param type $FromEmail Email source
     * @param type $ToEmail Email Destination
     * @param type $archMaxSize 
     * @param bool $isSilent throw esceptions or be quiet
     *
     * @return type
     * @throws \MailerModule\Exception
     */
    function __construct($post, $files, $FromEmail = "test@example.com", $ToEmail = "test@example.com", $archMaxSize = 200000000, bool $isSilent = true) {

        $this->silentMode = $isSilent;
        $this->toMail = $ToEmail;
        $this->fromMail = $FromEmail;

        if (is_numeric($archMaxSize)) {
            $_archsize = intval($archMaxSize);
            if ($_archsize > 0) {
                $this->archMaxSize = $_archsize;
            }
        }

        try {
            if (empty($post) || empty($files)) {
                require_once('template.php');
                return;
            }
            $this->doWork($post, $files);
        } catch (Exception $e) {
            if (!$this->silentMode) {
                throw $e;
            }
            $this->response = '<p>' . \MailerModule\Messages::GLOBAL_ERROR_MESSAGE . '</p>';
        }
        echo $this->response;
    }

    /**
     * генерирует имя для файла во временной папке.
     * если такой файл есть генерирует имя вида
     * {N}_name где {N} число  
     * 
     * @param string $name
     * @return string
     */
    private function getNewName($name) {
        // нужно использовать DIRECTORY_SEPARATOR для
        // построения пути т.к. ты не знаешь на какой системе
        // это будет развернуто а для windows и linux они разные

        $newName = $this->tmp_dir . DIRECTORY_SEPARATOR . $name;

        //дальше нужно проверить вдруг есть такой файл? 
        //и что с ним делать если есть.

        if (file_exists($newName)) {
            $prefix = 0;
            //будем добавлять префикс <n>_
            //если такой файл существует будем увеличивать n
            $newName = $this->tmp_dir . DIRECTORY_SEPARATOR . $prefix . '_' . $name;
            while (file_exists($newName)) {
                $prefix++;
                $newName = $this->tmp_dir . DIRECTORY_SEPARATOR . $prefix . '_' . $name;
            }
            unset($prefix);
        }
        return $newName;
    }

    /**
     * Обработка файлов, загруженных на сервер
     * перемещает файлы ( @see $files ) во временную папку ( @see $temp_dir )
     * 
     * @throws MimeTypeNotImageException
     * @throws MoveFileToTempException
     */
    private function process_files() {
        //проверки на директории не нужны мы их проверили в checkDirs
        foreach ($this->files as $index => $file) {

            if (!is_array($file['name'])) {
                $normalized_array[$index][] = $file;
                continue;
            }

            foreach ($file['name'] as $idx => $name) {
                $normalized_array[$index][$idx] = array(
                    'name' => $name,
                    'type' => $file['type'][$idx],
                    'tmp_name' => $file['tmp_name'][$idx],
                    'error' => $file['error'][$idx],
                    'size' => $file['size'][$idx]
                );

                $tmp_name = $normalized_array[$index][$idx]['tmp_name'];
                $name = $normalized_array[$index][$idx]['name'];

                // если чтото не так кидаем Exception.
                // else блок не нужен тк Exception прерывает дальнейшее
                // исполнение кода. Так мы сократим вложенность

                if (!exif_imagetype($tmp_name)) {
                    throw new MimeTypeNotImageException($tmp_name);
                }

                $newName = getNewName($name);
                if (!move_uploaded_file($tmp_name, $newName)) {
                    throw new MoveFileToTempException($tmp_name, $newName);
                }
            }
        }
    }

    /**
     * Создание архива
     * 
     * @throws ZipCreationException
     * @throws ZipAddFileException
     */
    private function zip_files() {
        $zip = new ZipArchive;

        $name = "photo_" . date("d-m-Y-H-i-s") . ".zip";
        $this->filename = $this->archive_dir . DIRECTORY_SEPARATOR . $name;

        $proto = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $proto = 'https';
        }

        // может генерировать неверный URL если развернуто в поддоме или в подпапке
        $this->filelink = $proto . '//' . $_SERVER['HTTP_HOST'] . '/modules/mod_photo/archives/' . $name;

        if ($zip->open($this->filename, ZIPARCHIVE::CREATE) !== TRUE) {
            throw new ZipCreationException($this->filename);
        }

        $file = "";
        try {
            $dir = opendir($this->tmp_dir);
            $i = 0;
            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..') {
                    $i++;
                    $realpath = realpath($this->tmp_dir . DIRECTORY_SEPARATOR . $file);
                    $zip->addFile($realpath, iconv('utf-8', 'CP866//TRANSLIT//IGNORE', $file));
                }
            }
            $zip->close();

            $this->response = '<p>Отправлено файлов: ' . $i . '.</p><p>Ваше сообщение поступило в обработку. Спасибо за заказ у нас! </p>';
        } catch (Exception $e) {
            throw new ZipAddFileException($this->filename, $file, null, $e);
        }
    }

    private function fillBodyData() {
        $body = "";
        $body = '

			<p><strong>Имя: </strong>' . $this->post['name'] . '</p>
			<p><strong>Телефон: </strong>' . $this->post['phone'] . '</p>
			<p><strong>Параметры печати: </strong> Формат: ' . $this->post['format'] . ', бумага: ' . $this->post['paper'] . ', поля: ' . $this->post['brim'] . '</p>
			<p><strong>Ссылка на файлы: </strong><a href="' . $this->filelink . '">' . $this->filelink . '</a></p>

		';
        if (!empty($this->post['address'])) {
            $body .="<p><strong>Адрес: </strong>'" . $this->post['address'] . "'</p>";
        }
        if (!empty($this->post['note'])) {
            $body .="<p><strong>Примечание: </strong>'" . $this->post['note'] . "'</p>";
        }
        if (!empty($this->post['quantity'])) {
            $body .="<p><strong>Количество: </strong>'" . $this->post['quantity'] . "'</p>";
        }

        if (!empty($this->post['result'])) {
            $body .="<p><strong>Цена: </strong>'" . $this->post['result'] . "'</p>";
        }
        return $body;
    }

//письмо на мыло		 
    private function send_mail() {
        //allow phpMailer throw exceptions
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'utf-8';

            $mail->setFrom($this->fromMail, 'Печать фотографий');
            $mail->addAddress($this->toMail);
            $mail->addReplyTo($this->fromMail, 'Печать фотографий');

            $mail->isHTML(true);

            $mail->Subject = 'Заказ с сайта ' . $_SERVER['HTTP_HOST'];
            $mail->Body = $this->fillBodyData();
            $mail->AltBody = 'Новый заказ на печать фотографий';

            if (!$mail->send()) {
                throw new MailSendException($this->fromMail, $this->toMail);
            }
        } catch (MailerModule\MailSendException $e) {
            throw $e;
        } catch (phpmailerException $e) {
            throw new MailSendException($this->fromMail, $this->toMail, "Get PhpMailerException: " . $e->getMessage(), $e);
        } catch (\Exception $e) {
            //отлавливаем общие исключения, например исключения class.phpmailer.php
            throw new MailSendException($this->fromMail, $this->toMail, null, $e);
        }
    }

//определение размера директории
    private function size_dir() {
        $directory = dir($this->archive_dir);
        $size_dir = 0;

        while ($dir = $directory->read()) {
            if ($dir == "." || $dir == "..") {
                continue;
            }

            $fname = $this->archive_dir . DIRECTORY_SEPARATOR . $dir;
            if (file_exists($fname) && filetype($fname) == "file") {
                $size_dir += filesize($fname);
            }
        }
        $directory->close();
        return $size_dir;
    }

//очистка временной директории
    private function removeDirectoryRecursive($path) {
        $files = glob($path . '/*', GLOB_MARK);
        foreach ($files as $file) {
            is_dir($file) ? removeDirectoryRecursive($file) : unlink($file);
        }
        rmdir($path);
        return;
    }

    private function clean_tmp() {
        try {
            $this->removeDirectoryRecursive($this->tmp_dir);
        } catch (Exception $e) {
            throw new ClenupTempDirectoryException($this->tmp_dir, "get exception: " . $e->getMessage(), $e);
        }
    }

//очистка данных из формы
    private function validate_post($post) {
        foreach ($post as $key => $data) {
            $data_cleaned[$key] = htmlspecialchars(strip_tags(trim($data)));
        }
        return $data_cleaned;
    }
}
