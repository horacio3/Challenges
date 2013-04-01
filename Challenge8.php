<?php

require_once('rackspace.php');

define('INIFILE', 'auth.ini');
define('CONTAINERNAME', 'Challenge8');
define('CNAME', 'Challenge.domain.com');
define('TEMP_URL_SECRET', 'April is the cruelest month, breeding lilacs...');

function UploadProgress($len) {
  printf("[uploading %d bytes]", $len);
}

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
$objstore->SetTempUrlSecret(TEMP_URL_SECRET);

// create a new container
print("Creating CdnContainer\n");
$container = $objstore->Container();
$container->Create(array('name'=>CONTAINERNAME));

// publish it to the CDN
print("Publishing to CDN...\n");
$cdnversion = $container->PublishToCDN();

// Create HTML
printf("Creating object...\n");
$doc = DOMDocument::loadHTML("<html><body>Test<br></body></html>");
$object = $container->DataObject();
$doc->SaveHTMLFILE("temp.html");

// Upload HTML to Container
$object->Create(array('name'=>'index.html', 'content_type'=>'text/html'),'temp.html');

// Find Domain.com from subdomain.domain.com
preg_match('/[^.]+\.[^.]+$/', CNAME, $domainsuffix);
preg_match('/[^.]*/', CNAME, $subdomain);
	
$dns = $RAX->DNS();
$dlist = $dns->DomainList();

// Search for Domain.com
while($domain = $dlist->Next()) {
	printf("\n%s [%s]\n",
		$domain->Name(), $domain->emailAddress);
	// add record
	if ($domain->Name() == $domainsuffix[0] ) {
		printf("Adding subdomain...\n");
		$sub = $domain->Record();
		$sub->Create(array(
			'type' => 'CNAME',
			'name' => CNAME,
			'ttl'  => 600,
			'data' => $container->PublicURL(),
			'comment' => 'Added '.date('Y-m-d H:i:s')))
		;
		}
	// If Domain.com not found
	else {
		printf("Domain not Found!\n");
	}
}

exit();

print("Done\n");

exit();

?>
