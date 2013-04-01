<?php
// php Challenge3.php C:\upload Test
require_once('rackspace.php');

define('INIFILE', 'auth.ini');
define('TEMP_URL_SECRET', 'April is the cruelest month, breeding lilacs...');

// progress callback function
function UploadProgress($len) {
  printf("[uploading %d bytes]\n", $len);
}

// Parse out arguments Directory & Container Name
if ($_GET) {
    $argument1 = $_GET['argument1'];
    $argument2 = $_GET['argument2'];
}
else {
    $argument1 = $argv[1];
    $argument2 = $argv[2];
}

if(!is_dir($argument1))
	exit;
else
	echo "Directory Exists...\n";
	
$ini = parse_ini_file(INIFILE, TRUE);
if (!$ini) {
    printf("Unable to load .ini file [%s]\n", INIFILE);
    exit;
}

// establish our credentials
$RAX = new OpenCloud\Rackspace(
    $ini['Identity']['url'], $ini['Identity']);
$RAX->SetUploadProgressCallback('UploadProgress');

// now, connect to the ObjectStore service
$objstore = $RAX->ObjectStore('cloudFiles', 'DFW');

// set the temp URL secret
$objstore->SetTempUrlSecret(TEMP_URL_SECRET);

// create a new container
print("Creating CdnContainer\n");
$container = $objstore->Container();
$container->Create(array('name'=>$argument2));

$files = glob($argument1 . "\\*");
foreach ($files as $file) { 
	printf("Creating object...\n");
	$object = $container->DataObject();
	$object->Create(array('name'=>basename($file), 'content_type'=>'text/plain'),
	$file);
}
 
print("Done\n");

exit();

?>
