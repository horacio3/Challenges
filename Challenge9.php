<?php

require_once('rackspace.php');

define('INIFILE', 'auth.ini');

// Parse out arguments FQDN,image,flavor
if ($_GET) {
    $argument1 = $_GET['argument1'];
    $argument2 = $_GET['argument2'];
  $argument3 = $_GET['argument3'];
}
else {
    $argument1 = $argv[1];
    $argument2 = $argv[2];
	$argument3 = $argv[3];
}

preg_match('/[^.]+\.[^.]+$/', $argument1, $domainsuffix);
preg_match('/[^.]*/', $argument1, $subdomain);

$ini = parse_ini_file(INIFILE, TRUE);
if (!$ini) {
    printf("Unable to load .ini file [%s]\n", INIFILE);
    exit;
}

// establish our credentials
$RAX = new OpenCloud\Rackspace(
    $ini['Identity']['url'], $ini['Identity']);
$RAX->SetDefaults('Compute',
    $ini['Compute']['serviceName'],
    $ini['Compute']['region'],
    $ini['Compute']['urltype']
);

$compute = $RAX->Compute();

// Create Server
$server1 = $compute->Server();

print("Creating server(s)...");

$server1->Create(array(
	'name' => $subdomain[0],
	'image' => $compute->Image($argument2),
	'flavor' => $compute->Flavor($argument3)));

print("requested\n");
print("ID=" . $server1->id . "\n");
$server1->WaitFor('ACTIVE', 600, 'progress');
print("\n\n");
displayInfo($server1);

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
			'data' => $server1->ip()
		));
	}
	// If Domain.com not found
	else {
		printf("Domain not Found!\n");
	}
}

print('DONE');
exit;

function dot($obj) {
	printf("...status: %s\n", $obj->Status());
}
function progress($s) {
	printf("%s: %3d%% complete, status is %s\n",$s->name,$s->progress, $s->status);
}
function displayInfo($server) {
	print("Name = " . $server->name . "\n");
	printf("IP = %s\n",$server->ip());
	print("Admin Pass = " . $server->adminPass . "\n\n");
}
?>
