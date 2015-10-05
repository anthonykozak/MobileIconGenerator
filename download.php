<?php
if(isset($_GET['file']))
{
	$file = 'uploads/'.$_GET['file'];
	$filename = basename($file);
	$realfilename = $_GET['filename'];
	$ext = substr($file, -3, 3);
	if ($ext == 'zip' || $ext == 'ico' || $ext == 'jpg')
	{
		if (file_exists($file))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-disposition: attachment; filename='.$realfilename);
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');
			header('Pragma: public');
			header('Content-Length: '.filesize ($file ) );
			readfile($file);
		}
	}
}
?>