<?php

<<<<<<< HEAD
require_once 'class.phpmailer.php';
new OrderPhoto($_POST, $_FILES);
=======
//silent
new \MailerModule\OrderPhoto($_POST, $_FILES, "test@example.com", "test@example.com", 200000000, true);
>>>>>>> a3b0b738743e4e3d3bcb54a1252e695425811301


<<<<<<< HEAD
	const MAIL = 'example@mail.ru';

	function __construct($post, $files)
	{
		if (!empty($post) && !empty($files)) {

			$this->post = $this->validate_post($post);
			$this->files = $files;

			try{
				$this->check_directories();
				$this->process_files();	
				$this->zip_files();	
				$this->clean_dir($this->tmp_dir);
				$this->send_mail();	
			} catch(Exception $e){
				echo $e->getMessage();
			}

			echo $this->response;

		}else{
			require_once('template.php');
		}
	}
	//Обработка файлов, загруженных на сервер
	private function process_files()
	{
		foreach($this->files as $index => $file) {

		    if (!is_array($file['name'])) {
		        $normalized_array[$index][] = $file;
		        continue;
		    }

		    foreach($file['name'] as $idx => $name) {
		        $normalized_array[$index][$idx] = array(
		            'name' => $name,
		            'type' => $file['type'][$idx],
		            'tmp_name' => $file['tmp_name'][$idx],
		            'error' => $file['error'][$idx],
		            'size' => $file['size'][$idx]
		        );

		        $tmp_name = $normalized_array[$index][$idx]['tmp_name'];
	        	$name = $normalized_array[$index][$idx]['name'];

	        	if (exif_imagetype($tmp_name)) {
			        if (!move_uploaded_file($tmp_name, "$this->tmp_dir/$name")) {
			        	throw new Exception("Ошибка обратки файлов!");
			        }
	        	}else{
			        throw new Exception("Для загрузки доступны только изображения!");
	        	}

		    }
		    return true;
		}
	}
	//Создание архива
	private function zip_files()
	{
		$zip = new ZipArchive;
		$name = "photo_".date("d-m-Y-H-i-s").".zip";
		$this->filename = $this->archive_dir.$name;
		$this->filelink =  'http://'.$_SERVER['HTTP_HOST'].'/modules/mod_photo/archives/'.$name;

		if ($zip->open($this->filename, ZIPARCHIVE::CREATE) === TRUE) {

			$dir = opendir( $this->tmp_dir );
	 		$i = 0;
		    while( $file = readdir( $dir ) ){
				if($file != '.' && $file != '..'){
			    	$i++;
			    	$realpath = realpath($this->tmp_dir.$file);
			        $zip->addFile($realpath, iconv('utf-8','CP866//TRANSLIT//IGNORE', $file));          
				} 
		    }
			$zip->close();

			$this->response = '<p>Отправлено файлов: '.$i.'.</p><p>Ваше сообщение поступило в обработку. Спасибо за заказ у нас! </p>';

			return $this->response;

		}else{
			throw new Exception('Ошибка архивации!');
		}
	}
	//письмо на мыло		 
	private function send_mail()
	{
		$mail = new PHPMailer;
		$mail->CharSet = 'utf-8';

		$mail->setFrom('info@vsesnpch.ru', 'Печать фотографий');
		$mail->addAddress(OrderPhoto::MAIL);
		$mail->addReplyTo('info@vsesnpch.ru', 'Печать фотографий');

		//$mail->addAttachment($file_mail); 
		$mail->isHTML(true);                   

		$mail->Subject = 'Заказ с сайта '.$_SERVER['HTTP_HOST'];
		$mail->Body = '

			<p><strong>Имя: </strong>'.$this->post['name'].'</p>
			<p><strong>Телефон: </strong>'.$this->post['phone'].'</p>
			<p><strong>Параметры печати: </strong> Формат: '.$this->post['format'].', бумага: '.$this->post['paper'].', поля: '.$this->post['brim'].'</p>
			<p><strong>Ссылка на файлы: </strong><a href="'.$this->filelink.'">'.$this->filelink.'</a></p>

		';
		if (!empty($this->post['address'])) {
			$mail->Body .="<p><strong>Адрес: </strong>'".$this->post['address']."'</p>";
		}
		if (!empty($this->post['note'])) {
			$mail->Body .="<p><strong>Примечание: </strong>'".$this->post['note']."'</p>";
		}
		if (!empty($this->post['quantity'])) {
			$mail->Body .="<p><strong>Количество: </strong>'".$this->post['quantity']."'</p>";
		}

		if (!empty($this->post['result'])) {
			$mail->Body .="<p><strong>Цена: </strong>'".$this->post['result']."'</p>";
		}


		$mail->AltBody = 'Новый заказ на печать фотографий';

		if(!$mail->send()) {
		    throw new Exception('Сообщение не было отправлено!');
		}

		return true;
	}
	//определение размера директории
	private function size_dir()
	{
	    $directory = dir($this->archive_dir);
	    $size_dir = 0;

	    while($dir=$directory->read()){
	        if($dir != "." && $dir != ".."){
	            if(filetype($this->archive_dir.$dir) == "file"){
	             	$size_dir += filesize($this->archive_dir.$dir);
	            }
	        }
	    }

	    $directory->close();
	    return $size_dir;
	}
	//очистка директории
	private function clean_dir($dir)
	{
		$files = array_diff(scandir($dir), array('.','..')); 
	    foreach ($files as $file) { 
	      	is_file("$dir/$file") && unlink("$dir/$file"); 
		} 
		if(!rmdir($dir)){
			throw new Exception('Ошибка очистки временной директории!');
		} 
	}
	//очистка данных из формы
	private function validate_post($post)
	{
		foreach ($post as $key => $data) {
			$data_cleaned[$key] = htmlspecialchars(strip_tags(trim($data)));
		}
		return $data_cleaned;
	}
	//проверка директорий на существование, права и размер
	private function check_directories(){
		$this->tmp_dir = dirname(__FILE__).'/tmp_files/';
		$this->archive_dir = dirname(__FILE__).'/archives/';
		$this->archives_size = $this->size_dir();

		!is_dir($this->tmp_dir) && mkdir($this->tmp_dir);
		if(!chmod($this->tmp_dir, 0777)){
			throw new Exception("Ошибка прав доступа к временной директории!");
		}
		
		!is_dir($this->archive_dir) && mkdir($this->archive_dir);
		if(!chmod($this->archive_dir, 0777)){
			throw new Exception("Ошибка прав доступа к директории для сохранения архива!");
		}

		if ($this->archives_size > 200000000) {
			$this->clean_dir($this->archive_dir);
		}
	}
}
=======
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
>>>>>>> a3b0b738743e4e3d3bcb54a1252e695425811301
