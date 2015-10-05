<?php
require 'class-php-ico.php';

$rootUrl = 'http://'.$_SERVER['SERVER_ADDR'].dirname($_SERVER['REQUEST_URI']).'/';
$uploadDirName = 'uploads/';

$data = array();

if(isset($_GET['files']))
{  
    $error = false;
    $files = array();

   foreach($_FILES as $file)
    {
        if(move_uploaded_file($file['tmp_name'], $uploadDirName . basename($file['name'])))
        {
            $files[] = $uploadDirName . $file['name'];
        }
        else
        {
            $error = true;
        }
    }
    $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
}
else if(isset($_REQUEST['inputtype']) && $_REQUEST['inputtype'] == 'favicon')
{
	$data = array('success' => 'Form was submitted', 'formData' => $_POST);
	$type = $_POST['type'];
	$rootUrl = 'http://'.$_SERVER['SERVER_ADDR'].dirname($_SERVER['REQUEST_URI']).'/';
	$filepath = $_POST['filenames'][0];
	$filenameWE = pathinfo($filepath, PATHINFO_FILENAME);
	$fileurl = $rootUrl . $filepath;
	
	resize($fileurl, $type, 1024, 1024, $filepath);
		
	$source = $filepath;
	$destinationName = uniqid().time().'.ico';
	$destination = $uploadDirName.$destinationName;
	
	$sizes = array();
	$formats = $_POST['formats'];
	$formats = preg_match_all("#([0-9]+)x([0-9]+)#", $formats, $formatsArr);
	for ( $i = 0; $i < count( $formatsArr[0]) ; $i++ )
	{
		$sizes[] = array($formatsArr[1][$i], $formatsArr[2][$i]);
	}

	$ico_lib = new PHP_ICO( $source, $sizes );
	$ico_lib->save_ico( $destination );
	$data['realname'] = $filenameWE.'.ico';
	$data['zip'] = $destinationName;
	
	@unlink($filepath);
}
else
{
    $data = array('success' => 'Form was submitted', 'formData' => $_POST);
	$type = $_POST['type'];
	$formats = $_POST['formats'];
	$formats = preg_match_all("#([0-9]+)x([0-9]+)#", $formats,$formatsArr);
	$filepath = $_POST['filenames'][0];
	$filename = basename($filepath);
	$filenameWE = pathinfo($filepath, PATHINFO_FILENAME);
	$fileext = pathinfo($filepath, PATHINFO_EXTENSION);
	$fileurl = $rootUrl . $filepath;
	
	$zipFileName = uniqid().time().".zip";
	$zipFileUri = "uploads/".$zipFileName;
	$zipUrl = $rootUrl . $zipFileName;
	$zip = new ZipArchive();
	if ($zip->open($zipFileUri, ZipArchive::CREATE)!==TRUE) {
		$data['error'] = ("Impossible d'ouvrir le fichier <$zipFileName>\n");
	}
	$allFiles = array($filepath);
	for ( $i = 0; $i < count( $formatsArr[0]) ; $i++ )
	{
		$pathfile = $filenameWE.$formatsArr[0][$i].'.'.$fileext;
		$path = $uploadDirName.$pathfile;
		
		resize($fileurl, $type, $formatsArr[1][$i], $formatsArr[2][$i], $path);
		
		$allFiles[] = $path;
		
		$zip->addFile($path, $pathfile);
	}
	$zip->close();
	$data['realname'] = $filenameWE.'.zip';
	$data['zip'] = $zipFileName;
	
	foreach($allFiles as $f)
		@unlink($f);
	
}

echo json_encode($data);



function resize($fileUrl, $resizeType, $w, $h, $newPath)
{
	global $rootUrl, $uploadDirName;
	switch($resizeType)
	{
		case "resize" : $zc = 0;break;
		default : $zc = 1; break;
	}
	$url = $rootUrl . 'timthumb.php?src='.$fileUrl.'&w='.$w.'&h='.$h.'&zc='.$zc;
	$c = file_get_contents($url);
	file_put_contents($newPath, $c);
	return $newPath;
}
?>