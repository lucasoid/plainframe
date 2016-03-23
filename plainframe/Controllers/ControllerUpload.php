<?php
namespace plainframe\Controllers;

use plainframe\Config;
use plainframe\Auth\LoggedInUser;
use plainframe\Data\MapperFactory;
use plainframe\Domain\Upload;

/**
 * This class takes the following precautions with uploaded files:
 * <ul>
 * <li>Only allows authenticated users to upload files</li>
 * <li>Accepts only certain file extensions and mimetypes</li>
 * <li>Defines minimum and maximum file sizes</li>
 * <li>Renames files with a random alias and stores a reference to them in the database</li>
 * <li>Can store files above the web root to prevent remote execution, using readfile() to serve them</li>
 * </ul>
 */
class ControllerUpload extends Controller {
	
	private $extensions = array(
		'doc',
		'docx',
		'txt',
		'pdf',
		'jpg',
		'jpeg',
		'gif',
		'png',
		'svg',
	);
	private $mimetypes = array(
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/pdf',
		'text/plain',
		'image/jpeg',
		'image/gif',
		'image/png',
		'image/svg+xml',
	
	);
	private $minfilesize = 5;
	private $maxfilesize = 100000000;
		
	public function index($params = array()) {
		if(false === LoggedInUser::LoggedIn()) {
			header("Location:" . Config::get('redirect', 'login'));
			die();
		}
		$this->upload();
	}
	
	private function upload() {
		//you could further restrict the filetype requirements within each function.
		//$this->extensions = array('pdf');
		//$this->mimetypes = array('application/pdf');
		$filenames = $this->saveFiles();
		if($filenames && !empty($filenames)) {
			$uploads = array();
			foreach($filenames as $file) {
				$id = !empty($_POST['id']) ? $_POST['id'] : '';
				$userid = LoggedInUser::getLoggedInUserId();
				
				$upload = new Upload($id);
				$upload->observe('creatorid', $userid);
				$upload->observe('title', $file['title']);
				$upload->observe('filename', $file['name']);
				$upload->observe('mimetype', $file['mimetype']);
				$upload->observe('updated', date('Y-m-d'));
					
				$mapper = MapperFactory::makeMapper('Upload');
				$upload = $mapper->save($upload);
				$uploads[] = $upload->toArray();
			}
			echo json_encode($uploads, true);
		}
	}
		
	private function saveFiles() {
		$filenames = array();
		if(!empty($_FILES)) {
		
			foreach($_FILES as $file) {
				if($error = $this->fileExists($file)) {
					header( 'HTTP/1.1 400 BAD REQUEST' );
					echo $error;
					return false;
				}
				elseif($error = $this->hasUploadErrors($file)) {
					header( 'HTTP/1.1 400 BAD REQUEST' );
					echo $error;
					return false;
				}
				elseif($error = $this->hasValidationErrors($file)) {
					header( 'HTTP/1.1 400 BAD REQUEST' );
					echo $error;
					return false;
				}
				else {
					$name = sha1(uniqid(mt_rand(), true)) . $file['name'];
					$title = $file['name'];
					$size = $file['size'];
					$type = $file['type'];
					$tmp_name = $file['tmp_name'];
					
					try{
						@mkdir(Config::get('uploads', 'dir'), null, true);
					}
					catch(\Exception $e) {
						//echo $e;
					}
					if(move_uploaded_file($tmp_name, Config::get('uploads', 'dir') . DIRECTORY_SEPARATOR . $name)) {
						$filenames[] = array('title'=>$title, 'name'=>$name, 'mimetype'=>$type);
					}
				}
			}
		}
		return $filenames;
	}
	
	private function fileExists($file) {
		if(file_exists(Config::get('uploads', 'dir') . DIRECTORY_SEPARATOR . $file['name'])) {
			return 'A file with that name already exists. Please rename the file and try again.';
		}
		return false;
	}
		
	private function hasUploadErrors($file) {
		
		if(!empty($file['error']) && $file['error'] !== UPLOAD_ERR_OK ) {
			$messages = array(
					0=>"There is no error, the file uploaded with success",
					1=>"The uploaded file exceeds the max file size",
					2=>"The uploaded file exceeds the MAX_FILE_SIZE",
					3=>"The uploaded file was only partially uploaded",
					4=>"No file was uploaded",
					6=>"Missing a temporary folder",
					7=>"Failed to write to disk",
					8=>"The file could not be uploaded"
			);
			return $messages[$file['error']];
		}	
		//check filesize to confirm a file was uploaded
		if($file['size'] == 0) {
			return "No file was uploaded.";
		}
		else return false;
	}
	
	private function hasValidationErrors($file) {
		$tmp_name = $file['tmp_name'];
		$name = $file['name'];
		$nameparts = explode('.', $name);
		$ext = array_pop($nameparts);
		
		if($file['size'] > $this->maxfilesize ) {
			return 'upload exceeds the max file size';
		}
		if($file['size'] < $this->minfilesize ) {
			return 'upload less than the min file size';
		}
		
		if(!in_array(strtolower($ext), $this->extensions)) {
			return 'extension ' . $ext . ' not allowed';
		}
		//check MIME type
		if(!in_array(strtolower($file['type']), $this->mimetypes)) {
			return 'MIME type not allowed';
		}
		return false;
	}
	
}
?>