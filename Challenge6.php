<?php

require_once('rackspace.php');

define('INIFILE', 'auth.ini');

$ini = parse_ini_file(INIFILE, TRUE);
if (!$ini) {
    printf("Unable to load .ini file [%s]\n", INIFILE);
    exit;
}

// establish our credentials
$RAX = new OpenCloud\Rackspace(
    $ini['Identity']['url'], $ini['Identity']);

// now, connect to the ObjectStore service
$objstore = $RAX->ObjectStore('cloudFiles', 'DFW');

// create a new container
print("Creating CdnContainer\n");
$container = $objstore->Container();
$container->Create(array('name'=>'Challenge6'));

// publish it to the CDN
print("Publishing to CDN...\n");
$cdnversion = $container->PublishToCDN();

printf("Container: %s\n", $container->name);
printf("      URL: %s\n", $container->Url());
printf("  CDN URL: %s\n", $container->CDNUrl());

print("Only CDN-enabled containers:\n");
$cdnlist = $objstore->CDN()->ContainerList(array('enabled_only'=>TRUE));
while($cdncontainer = $cdnlist->Next()) {
    printf("* %s (CDN)\n", $cdncontainer->name);
}

print("Done\n");

exit();

?>
