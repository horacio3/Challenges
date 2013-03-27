<?php

require_once('rackspace.php');

define('INIFILE', 'auth.ini');

// Parse out arguments FQDN then IP
if ($_GET) {
    $argument1 = $_GET['argument1'];
    $argument2 = $_GET['argument2'];
}
else {
    $argument1 = $argv[1];
    $argument2 = $argv[2];
}

$ini = parse_ini_file(INIFILE, TRUE);
if (!$ini) {
    printf("Unable to load .ini file [%s]\n", INIFILE);
    exit;
}

// establish our credentials
$RAX = new OpenCloud\Rackspace(
    $ini['Identity']['url'], $ini['Identity']);
// Find Domain.com from subdomain.domain.com
preg_match('/[^.]+\.[^.]+$/', $argument1, $domainsuffix);
  
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
			'type' => 'A',
			'name' => $argument1,
			'ttl'  => 600,
			'data' => $argument2
		));
	}
	// If Domain.com not found
	else {
		printf("Domain not Found!\n");
	}
}
exit();

?>
