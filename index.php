<?php

require_once 'class.phpmailer.php';

class OrderPhoto
{
	private $tmp_dir;
	private $archive_dir;
	private $archives_size;
	private $response;
	private $post;
	private $files;
	private $filename;
	private $filelink;

	const MAIL = 'artur@gazetdinov.ru';


	
	function __construct($post, $files)
	{
		if (!empty($post) && !empty($files)) {

			$this->post = $this->validate_post($post);

			$this->files = $files;

			$this->tmp_dir = dirname(__FILE__).'/tmp_files/';
			$this->archive_dir = dirname(__FILE__).'/archives/';
			$this->archives_size = $this->size_dir();

			chmod($this->archive_dir, 0777);

			if ($this->process_files() && $this->zip_files()) {
				$this->send_mail();
				$this->clean_tmp();
			}

			if ($this->archives_size > 200000000) {
				rmdir($archive_dir); 
				mkdir($archive_dir); 
			}

			echo $this->response;

		}else{
			require_once('template.php');
		}
	}
//Обработка файлов, загруженных на сервер
	private function process_files()
	{
		!is_dir($this->tmp_dir) && mkdir($this->tmp_dir) && chmod($this->tmp_dir, 0777);
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
			        	die("Ошибка обратки файлов!");
			        }
	        	}else{
			        die("Для загрузки доступны только изображения!");
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
			die('Ошибка архивации!');
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
		    die('Сообщение не было отправлено!');
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
//очистка временной директории
	private function clean_tmp()
	{
		$files = array_diff(scandir($this->tmp_dir), array('.','..')); 
	    foreach ($files as $file) { 
	      	is_file("$this->tmp_dir/$file") && unlink("$this->tmp_dir/$file"); 
		} 
		rmdir($this->tmp_dir); 
	}
//очистка данных из формы
	private function validate_post($post)
	{
		foreach ($post as $key => $data) {
			$data_cleaned[$key] = htmlspecialchars(strip_tags(trim($data)));
		}
		return $data_cleaned;
	}
}

new OrderPhoto($_POST, $_FILES);