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

// now, connect to the DbService service
print "Connecting...\n";
$dbservice = $RAX->DbService('cloudDatabases','DFW','publicURL');

print "Creating a new instance...\n";
$instance = $dbservice->Instance();
$instance->Create(array(
  'name' => 'ChallengeTest',
	'volume' => array('size' => '1'),
	'flavor' => $dbservice->Flavor("1")));
print "Requested...\n";
$instance->WaitFor('ACTIVE', 600, 'showstatus');

// create a database
print "Creating a new database...\n";
$db = $instance->Database('Mine');
$db->Create(array('character_set'=>'utf8'));

// create a user
print "Creating a new user...\n";
$user = $instance->User('dbuser');
$user->AddDatabase('Mine');
$user->Create(array('password'=>'Challenge!123'));

print "Done\n";

exit();

function showstatus($item) {
    printf("\tStatus: %s\n", $item->status);
}

?>
