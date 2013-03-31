import os
import pyrax
import time

def Selection(list):
  dict = {}
	print "Select one:"
	for pos, itm in enumerate(list):
		print "%s: %s" % (pos, itm.name)
		dict[str(pos)] = itm.id
	selection = None
	while selection not in dict:
		if selection is not None:
			print "   -- Invalid choice"
		selection = raw_input("Enter the number for your choice: ")
	return dict[selection]
def WaitFor(id):
	server = [srv for srv in cs.list()
		if id in srv.id][0]
	while server.status != 'ACTIVE':
		time.sleep(15)
		server = [srv for srv in cs.list()
			if id in srv.id][0]
		print "%s: %3d%% complete, status is %s" % (server.name,server.progress,server.status)
	return
def DisplayInfo(server):
	print "Name: ", srv.name
	print "ID: ", srv.id
	print "Admin Password: ", srv.adminPass
	print "IP: ", srv.networks
	return

# Authenticate
creds_file = os.path.expanduser("~/.rackspace_cloud_credentials")
pyrax.set_credential_file(creds_file)

cs = pyrax.cloudservers

# Select which Image
img_id = Selection(cs.images.list())
# Select which Flavor
flv_id = Selection(cs.flavors.list())

nm = raw_input("Enter a name for the Server: ")
srv = cs.servers.create(nm,img_id,flv_id)
print "Server '%s' is being created. Its ID is: %s" % (nm, srv.id)
WaitFor(srv.id)
DisplayInfo(srv)
