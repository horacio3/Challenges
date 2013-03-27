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
$RAX->SetDefaults('Compute',
    $ini['Compute']['serviceName'],
    $ini['Compute']['region'],
    $ini['Compute']['urltype']
);
$compute = $RAX->Compute();

// Create Server
$server1 = $compute->Server();
$server2 = $compute->Server();
$server3 = $compute->Server();

print("Creating server(s)...");

$server1->Create(array(
  'name' => 'Web1',
	'image' => $compute->Image("3afe97b2-26dc-49c5-a2cc-a2fc8d80c001"),
	'flavor' => $compute->Flavor("2")));
$server2->Create(array(
	'name' => 'Web2',
	'image' => $compute->Image("3afe97b2-26dc-49c5-a2cc-a2fc8d80c001"),
	'flavor' => $compute->Flavor("2")));
$server3->Create(array(
	'name' => 'Web3',
	'image' => $compute->Image("3afe97b2-26dc-49c5-a2cc-a2fc8d80c001"),
	'flavor' => $compute->Flavor("2")));

print("requested\n");
print("ID=" . $server1->id . "\n");
$server1->WaitFor('ACTIVE', 600, 'progress');
$server2->WaitFor('ACTIVE', 600, 'progress');
$server3->WaitFor('ACTIVE', 600, 'progress');
print("\n\n");
displayInfo($server1);
displayInfo($server2);
displayInfo($server3);


print("done\n");
exit();

function progress($s) {
	printf("%s: %3d%% complete, status is %s\n",$s->name,$s->progress, $s->status);
}

function displayInfo($server) {
	print("Name = " . $server->name . "\n");
	printf("IP = %s\n",$server->ip());
	print("Admin Pass = " . $server->adminPass . "\n");
}

?>
