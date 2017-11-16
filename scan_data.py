import subprocess
import threading
import os
import time
import cPickle
import math
import traceback
from usersData import *
from processesData import *

######################### FUNCTION DEFINITION ###################################
# Write Data to file
def writeData(processDict, studentDict, facultyDict, staffDict, write_count, backup_write_count):
	if(write_count % backup_write_count == 0 and write_count != 0):
		print "Creating backup files - version {}. . .".format(write_count/backup_write_count),
		if(os.path.isfile("./data/process_data.p")):
			os.rename("data/process_data.p","data/process_data.p.{}".format(write_count/backup_write_count)) # make copy for backup
		if(os.path.isfile("./data/student_data.p")):
			os.rename("data/student_data.p","data/student_data.p.{}".format(write_count/backup_write_count)) # make copy for backup
		if(os.path.isfile("./data/faculty_data.p")):
			os.rename("data/faculty_data.p","data/faculty_data.p.{}".format(write_count/backup_write_count)) # make copy for backup
		if(os.path.isfile("./data/staff_data.p")):
			os.rename("data/staff_data.p","data/staff_data.p.{}".format(write_count/backup_write_count)) # make copy for backup

		print "DONE"

	cPickle.dump(processDict,open("data/process_data.p","wb"))
	cPickle.dump(studentDict,open("data/student_data.p","wb"))
	cPickle.dump(facultyDict,open("data/faculty_data.p","wb"))
	cPickle.dump(staffDict,open("data/staff_data.p","wb"))

def requestData(netid):
	global INVALID_NETIDS
	r = requests.get("http://ur.nd.edu/request/eds.php?uid={}&full_response=true".format(str(netid)))
	try: 
		resp = json.loads(r.content.decode("utf-8"))
		if resp['ndprimaryaffiliation']  == "Faculty": 
			return "Faculty", FacultyData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"])
		elif resp['ndprimaryaffiliation'] == "Staff:":
			return "Staff", StaffData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"])
		elif resp['ndprimaryaffiliation'] == "Student":
			return "Student", StudentData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"], resp["ndlevel"])
	except ValueError:
		INVALID_NETIDS.add(netid)
		return "Error", None


################################## GLOBALS ######################################

headers = ["USER","PID","%CPU","%MEM","VSZ","RSS","TTY","STAT","START","RUNNING_TIME","COMMAND"]

last_write = time.time()
last_sample = time.time()
WRITE_INTERVAL = 20 # Seconds
WRITE_COUNT = 0 # Number of writes
BACKUP_WRITE_COUNT = 100 # STORE A BACKUP EVERY 100 Writes
SAMPLE_INTERVAL = 3 # Seconds
previous_pids = set()
INVALID_NETIDS = set()

################################ DATA FILES ####################################
# Processes data file
if(os.path.isfile("./data/process_data.p")):
	process_dict = cPickle.load(open("data/process_data.p","rb"))
	for key,value in process_dict.items():
		if(value.end_time == 0):
			previous_pids.add(key)
	
else:
	process_dict = dict() # dict of data aquisitions
			   # it uses start and pid as key
# Student data file
if (os.path.isfile("./data/student_data.p")):
	student_dict = cPickle.load(open("data/student_data.p", "rb"))
			# key = netid
			# value = StudentData object
else: 
	student_dict = {}

# Faculty data file
if (os.path.isfile("./data/faculty_data.p")):
	faculty_dict = cPickle.load(open("data/faculty_data.p", "rb"))
			# key = netid
			# value = StaffData object
else:
	faculty_dict = {}

# Staff data file
if (os.path.isfile("./data/staff_data.p")):
	staff_dict = cPickle.load(open("data/staff_data.p", "rb"))
			# key = netid
			# value = StaffData object
else:
	staff_dict = {}


############################## INFINITE LOOP ################################### 

try:
	# Make sure student and faculty dictionaries are up to date
	print "Syncing up database. . .",
	for pid_start, process_data in process_dict.items():
		# New user
		netid = process_data.user
		if netid not in student_dict.keys() and netid not in faculty_dict.keys():
			title, usersData = requestData(netid)
			if title == "Student":
				student_dict[netid] = usersData
			elif title == "Faculty":
				faculty_dict[netid] = usersData
			elif title == "Staff":
				staff_dict[netid] = usersData
	print "DONE"
			
	while True:
		current_pids = set()
		# Obtain Data
		output = subprocess.check_output(["ps","aux"])
		session_dict = {} 
			# key = (netid, pid)
			# value = temp_dict
		for i, line in enumerate(output.splitlines()):
			if i == 0:
				continue
			elems = line.split()
			temp_dict = dict()
			#print elems
			if(len(elems) > len(headers)):
				elems = elems[0:10] + ["".join(elems[10:]),] # Somtimes commands has lines in between
			for i in range(len(elems)):
				temp_dict[headers[i]] = elems[i]

			# New user in current session
			netid = temp_dict["USER"]
			# Don't waste time making requests fo invalid netids
			if netid in INVALID_NETIDS:
				continue
			if netid not in student_dict.keys() and netid not in faculty_dict.keys() and netid not in staff_dict.keys():
				title, usersData = requestData(netid)
				if title == "Student":
					student_dict[netid] = usersData
				elif title == "Faculty":
					faculty_dict[netid] = usersData
				elif title == "Staff": 
					staff_dict[netid] = usersData
				else: 
					continue # skip root, ndsc, and other invalid netids

			#process_dict[(temp_dict["PID"],temp_dict["START"])]= temp_dict
			#current_pids.add((temp_dict["PID"],temp_dict["START"]))
			current_pids.add((temp_dict["USER"], temp_dict["PID"]))
			session_dict[(temp_dict["USER"], temp_dict["PID"])] = temp_dict
		

		
		# Update data files
		ended_pids = previous_pids - current_pids 
		new_pids = current_pids - previous_pids
		stayed_pids = current_pids & previous_pids # set intersection
		previous_pids = current_pids

		for p in stayed_pids:
			process = process_dict[p]
			temp_dict = session_dict[p]
			process.update_data(temp_dict["%CPU"],temp_dict["%MEM"],temp_dict["VSZ"],temp_dict["RSS"])
			process_dict[p] = process
		for p in new_pids:
			temp_dict = session_dict[p]
			new_proc = ProcessData(temp_dict["USER"],temp_dict["PID"],temp_dict["%CPU"],temp_dict["%MEM"],temp_dict["VSZ"],temp_dict["RSS"],temp_dict["TTY"],temp_dict["STAT"],temp_dict["START"],temp_dict["RUNNING_TIME"],temp_dict["COMMAND"])
			process_dict[p] = new_proc
		for p in ended_pids:
			process = process_dict[p]
			process.end_time = time.asctime(time.localtime())
			process_dict[p] = process
		
		# Write data to file if interval met
		if(time.time() - last_write > WRITE_INTERVAL):
			t = threading.Thread(target=writeData,args=(process_dict, student_dict, faculty_dict, staff_dict, WRITE_COUNT,BACKUP_WRITE_COUNT))
			t.start() # Write in separate Thread
			last_write = time.time()
		
		if(time.time() - last_sample > SAMPLE_INTERVAL):
			print("Time wasn't enough to finish sampling")
			last_sample = time.time()
		else:
			time.sleep(SAMPLE_INTERVAL-(time.time()-last_sample))
			last_sample = time.time()

except KeyboardInterrupt:
	writeData(process_dict,student_dict, faculty_dict, staff_dict, -1,-1) # Parameters force backup to occur
except Exception as e:
	traceback.print_exc()
	print "Error: {}".format(e)
	with open("log.error","a") as f:
		f.write(str(e)+'\n')
		writeData(process_dict,student_dict, faculty_dict, staff_dict, -1,-1) # Parameters force backup to occur

