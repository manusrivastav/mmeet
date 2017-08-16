<?php 
/***************************************************
* File Component
*
* Manages uploaded files to be saved to the file system.
*
*/
class FileComponent extends Component{ 

	/**
	* function to check file existance
	* @param $path string file path
	* @return boolean
	**/
	function fileExists($path){
		if(is_file($path) && file_exists($path)){
			return true;
		} else {
			return false;
		}
	}

	/**
	* function to delete file
	* @param $path string file path
	* @return boolean
	**/
	function deleteFile($path){
		if($this->fileExists($path)){
			if(unlink($path)){
				return true;
			} else {
				return false;
			}			
		} else {
			return false;
		}
	}

	/**
	* function to rename file
	* @param $name
	* @return string
	**/
	function renameFile($name){
		//$basefilename = preg_replace("/(.*)\.([^.]+)$/","\\1", $name);
		//$ext = preg_replace("/.*\.([^.]+)$/","\\1", $name);
		return date('YmdHis').'.'.$this->getExtension($name);
	}

	/**
	* function to get extension of file
	* @param string $name
	* @return string
	**/
	function getExtension($name){
		$ext = preg_replace("/.*\.([^.]+)$/","\\1", $name);
		return strtolower($ext);
	}

	/**
	* function to check allowed type
	* @param array $allowed_mimes
	* @param string $name file name
	* @return boolean
	**/
	function inAllowedMime($allowed_mimes, $name){
		if(in_array($this->getExtension($name), $allowed_mimes)){
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Upload file to server
	 * 
	 * @param array $file file array
	 * @param string $upload_dir upload dir
	 * @param array $allowed_mimes allowed file type
	 * @access public
	 */ 	
	function uploadFile($file, $upload_dir, $rename = false,$max_size=2097152000,$i=0,$j=0){
		// check for upload directory
		if(is_dir($upload_dir)) {
			// check for file size
	
			if(count($file['tmp_name'])==1 && $i==0 && $j==0){
			if ($file['size'] <= 2097152000) {
				// check in allowed mime types
				/*if($this->inAllowedMime($allowed_mimes, $file['name'])){*/
					if(is_uploaded_file($file['tmp_name'])){
						$final_file = $file['name'];
						// check for file existance
						if($this->fileExists($upload_dir.$final_file) || $rename){
							$final_file = $this->renameFile($file['name']);
						}
						if(move_uploaded_file($file['tmp_name'], $upload_dir.$final_file)) {
						    return array('name'=>$final_file);
						} else {
							return 'Some error has been occurred while uploading file. Please try again later';
						}
					} else {
						return 'Please upload valid file';
					}
				/*}else {
					return 'Please upload valid file';
				}*/
			} else {
				return 'Please upload valid sized file';
			}
			}else{
				//	print_r($file);	echo $j;die;
				if ($file['size'][$i] <= 2097152) {
				// check in allowed mime types
				/*if($this->inAllowedMime($allowed_mimes, $file['name'])){*/
					if(is_uploaded_file($file['tmp_name'][$i])){
						$final_file = $file['name'][$i];
						// check for file existance
						if($this->fileExists($upload_dir.$final_file) || $rename){
							$final_file = $this->renameFile($file['name'][$i]);
						}
						if(move_uploaded_file($file['tmp_name'][$i], $upload_dir.$final_file)) {
						    return array('name'=>$final_file);
						} else {
							return 'Some error has been occured while uploading file. Please try again later';
						}
					} else {
						return 'Please upload valid file';
					}
				/*}else {
					return 'Please upload valid file';
				}*/
			} else {
				return 'Please upload valid sized file';
			}
			}
		} else {
			return 'Please create directory at '.$upload_dir.' and allowed proper permissions to same';
		}
	}
}
?>