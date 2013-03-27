<?php
require_once('rackspace.php');

define('INIFILE', 'auth.ini');
define('ORIGSERVERID', '64e54691-46d4-405c-94c6-ce54bb756df0');
define('IMAGENAME', 'ChallengeImage');
define('NEWSERVERNAME', 'Challenge2');

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

$original = $compute->Server(ORIGSERVERID);

// Create Image
print("Creating image...\n");
$results = $original->CreateImage(IMAGENAME);
$imageID = substr(trim($results->Header('Location')),-36);
$image = $compute->Image($imageID);
$image->WaitFor('ACTIVE', 600, 'progress');

print("Image Complete!\n\n");

// Create Server
$server1 = $compute->Server();

print("Creating server(s)...");

$server1->Create(array(
  'name' => NEWSERVERNAME,
	'image' => $compute->Image($imageID),
	'flavor' => $compute->Flavor("2")));

print("requested\n");
print("ID=" . $server1->id . "\n\n");
$server1->WaitFor('ACTIVE', 600, 'progress');
print("\n\n");
displayInfo($server1);

print("\ndone\n");
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
