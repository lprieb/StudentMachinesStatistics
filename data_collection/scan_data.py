import subprocess
import threading
import os
import time
import cPickle
import math
import traceback
from usersData import *
from processesData import *
import sqlalchemy as sql
import datetime
import sys
#import pymysql
#from pymysql.err import *
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
insert_proc_on_next = set()
update_proc_on_next = set()
insert_user_on_next = set()
faculty_dict = dict()
staff_dict = dict()
student_dict = dict()
process_dict = dict()
MACHINE_ID = None
firstTime = True # Used first time updating databases to prevent errors
found_pids = set()
ended_pids_global = set()
filter_pids = set()
if(len(sys.argv) != 2):
    print "Incorrect number of arguments. Call:\n{} machineid".format(sys.argv[0])
    sys.exit(1)
else:
    MACHINE_ID = sys.argv[1]

################################ DB INTIALIZATION ####################################
dbs = "mysql+pymysql://lprieb:open1234@dsg1.crc.nd.edu/feb31"
engine = sql.create_engine(dbs, echo=False)
conn = engine.connect()
metadata = sql.MetaData()
user = sql.Table('user', metadata,
    sql.Column('netid',sql.String(20),primary_key=True),
    sql.Column('firstName',sql.String(30),nullable=False),
    sql.Column('lastName',sql.String(30),nullable=False),
    sql.Column('affiliation',sql.String(50)),
    sql.Column('college', sql.String(50)),
    sql.Column('classLevel',sql.String(15)),
    )
machine = sql.Table('machine',metadata,
    sql.Column('machineid',sql.String(20),primary_key=True),
    sql.Column('total_memory',sql.BigInteger),
    sql.Column('num_cpus',sql.Integer),
    sql.Column('total_swap',sql.BigInteger),
    )

process = sql.Table('process2',metadata,
    sql.Column('netid',sql.String(20),sql.ForeignKey('user.netid'),primary_key=True),
    sql.Column('pid',sql.Integer, primary_key=True),
    sql.Column('machineid',sql.String(20),sql.ForeignKey('machine.machineid'),primary_key=True),
    sql.Column('average_cpu_usage',sql.Float),
    sql.Column('average_mem_usage',sql.Float),
    sql.Column('average_rss',sql.Float),
    sql.Column('average_vsz',sql.Float),
    sql.Column('max_cpu_usage',sql.Float),
    sql.Column('max_mem_usage',sql.Float),
    sql.Column('max_vsz',sql.Float),
    sql.Column('max_rss',sql.Float),
    sql.Column('variance_cpu_usage',sql.Float),
    sql.Column('variance_mem_usage',sql.Float),
    sql.Column('variance_vsz',sql.Float),
    sql.Column('variance_rss',sql.Float),
    sql.Column('tty',sql.String(30)),
    sql.Column('stat',sql.String(10)),
    sql.Column('startTime',sql.DateTime,primary_key=True),
    sql.Column('endTime',sql.DateTime,default=datetime.datetime(2000,1,1,0,0,0,0)),
    sql.Column('command', sql.String(500)),
    sql.Column('measures',sql.Integer)
    )

metadata.create_all(engine) # won't create if already exists
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

def writeToDatabase(processDict):
	global firstTime
	real_proc_to_update = update_proc_on_next - insert_proc_on_next
	if(firstTime):
		firstTime = False
		to_remove = list()
		for p in insert_proc_on_next:
			if p in found_pids:
				to_remove.append(p)
		for p in to_remove:
			insert_proc_on_next.remove(p)
	for u in insert_user_on_next:
		result = conn.execute(sql.select([user]).where(user.c.netid == u.netid))
		if(result.rowcount == 0):
			try:
				ins = user.insert().values(netid=u.netid,affiliation=u.title,firstName=u.firstname,lastName=u.lastname,college=u.college,classLevel=u.grade)
				result = conn.execute(ins)
			except sql.exc.IntegrityError as e:
				print "User Error: {}".format(e)
				with open("log.error","a") as f:
					f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))
					#insert_user_on_next.remove(u)
		
	for proc in insert_proc_on_next:
		try:
			if(proc in filter_pids): continue

			procObj = processDict[proc]
			ins = process.insert().values(
				netid= procObj.netid,
				pid = procObj.pid,
				machineid = procObj.machineid,
				average_cpu_usage =procObj.avg_cpu_u,
				average_mem_usage = procObj.avg_mem_u,
				average_rss = procObj.avg_rss,
				average_vsz = procObj.avg_vsz,
				max_cpu_usage = procObj.max_cpu_u,
				max_mem_usage = procObj.max_mem_u,
				max_vsz  = procObj.max_vsz,
				max_rss = procObj.max_rss,
				variance_cpu_usage = procObj.var_cpu_u,
				variance_mem_usage  = procObj.var_mem_u,
				variance_vsz  = procObj.var_vsz,
				variance_rss = procObj.var_rss,
				tty = procObj.tty,
				stat = procObj.stat,
				startTime = procObj.start,
				endTime = procObj.end_time,
				command = procObj.command,
				measures = procObj.measures
			)
			result = conn.execute(ins)
		except sql.exc.IntegrityError as e:
			print "Insert Error: {}".format(e)
			filter_pids.add(proc)
			with open("insert_log.error","a") as f:
				f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))
	for proc in real_proc_to_update:
		try:
			procObj = processDict[proc]
			ins = process.update().where(sql.and_(
					process.c.netid==procObj.netid,
					process.c.startTime==procObj.start,
					process.c.machineid==procObj.machineid,
					process.c.pid== procObj.pid)).values(
						average_cpu_usage =procObj.avg_cpu_u,
						average_mem_usage = procObj.avg_mem_u,
						average_rss = procObj.avg_rss,
						average_vsz = procObj.avg_vsz,
						max_cpu_usage = procObj.max_cpu_u,
						max_mem_usage = procObj.max_mem_u,
						max_vsz  = procObj.max_vsz,
						max_rss = procObj.max_rss,
						variance_cpu_usage = procObj.var_cpu_u,
						variance_mem_usage  = procObj.var_mem_u,
						variance_vsz  = procObj.var_vsz,
						variance_rss = procObj.var_rss,
						endTime = procObj.end_time,
						measures = procObj.measures
						)

			result = conn.execute(ins)
		except sql.exc.IntegrityError as e:
		    print "Update Error: {}".format(e)
		    with open("updat_log.error","a") as f:
			    f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))
			#insert_proc_on_next.remove(proc)
	insert_user_on_next.clear()
	update_proc_on_next.clear()
	insert_proc_on_next.clear()
    
def requestData(netid):
	global INVALID_NETIDS
	r = requests.get("http://ur.nd.edu/request/eds.php?uid={}&full_response=true".format(str(netid)))
	try: 
		resp = json.loads(r.content.decode("utf-8"))
		if resp['ndprimaryaffiliation']  == "Faculty": 
			return "Faculty", FacultyData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"])
		elif resp['ndprimaryaffiliation'] == "Staff":
			return "Staff", StaffData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"])
		elif resp['ndprimaryaffiliation'] == "Student":
			return "Student", StudentData(netid, resp["ndprimaryaffiliation"], resp["givenname"], resp["sn"], resp["ndtoplevelprimarydepartment"], resp["ndlevel"])
		else:
			print "Unexpect affiliation {}".format(resp['ndprimaryaffiliation'])
			raise ValueError
	except (ValueError,KeyError) as e:
		INVALID_NETIDS.add(netid)
		return "Invali Netid Error", None



################################ DATA FILES ####################################
# Processes data file
'''
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

'''

############################## UPDATE FROM DATABASE ############################
result = conn.execute(sql.select([user]))
for row in result:
    if row["affiliation"] == "Student":
	student_dict[row["netid"]] = StudentData(tdict=row)
    elif row["affiliation"] == "Staff":
	staff_dict[row["netid"]] = StaffData(tdict=row)
    elif row["affiliation"] == "Faculty":
	faculty_dict[row["netid"]] = FacultyData(tdict=row)

result = conn.execute(sql.select([process]))
for row in result:
	process_dict[(row["pid"],row["startTime"])] = ProcessData(tdict=row)
	found_pids.add((row["pid"],row["startTime"]))	


############################## INFINITE LOOP ################################### 

try:
	# Make sure student and faculty dictionaries are up to date
	print "Syncing up database. . .",
	for pid_start, process_data in process_dict.items():
		# New user
		netid = process_data.netid
		if netid not in student_dict.keys() and netid not in faculty_dict.keys():
			title, usersData = requestData(netid)
			if title == "Student":
				student_dict[netid] = usersData
				insert_user_on_next.add(usersData)
			elif title == "Faculty":
				faculty_dict[netid] = usersData
				insert_user_on_next.add(usersData)
			elif title == "Staff":
				staff_dict[netid] = usersData
				insert_user_on_next.add(usersData)
	print "DONE"
	while True:
	    try:
			current_pids = set()
			# Obtain Data
			output = subprocess.check_output(["ps","aux"])
			session_dict = {} 
				# key = (startTime, pid)
				# value = temp_dict
			for i, line in enumerate(output.splitlines()):
				if i == 0:
					continue
				elems = line.split()
				temp_dict = dict()
				#print elems
				if(len(elems) > len(headers)):
					elems = elems[0:10] + [" ".join(elems[10:]),] # Somtimes commands has lines in between
				for i in range(len(elems)):
					temp_dict[headers[i]] = elems[i]

				if(temp_dict["USER"] == "lprieb" and temp_dict["COMMAND"] == "ps aux"):
					continue # Dont want to store our data collection process
				# New user in current session
				netid = temp_dict["USER"]
				# Don't waste time making requests fo invalid netids
				if netid in INVALID_NETIDS:
					continue
				if netid not in student_dict.keys() and netid not in faculty_dict.keys() and netid not in staff_dict.keys():
					title, usersData = requestData(netid)
					insert_users_on_next = usersData
					if title == "Student":
						student_dict[netid] = usersData
						insert_user_on_next.add(usersData)
					elif title == "Faculty":
						faculty_dict[netid] = usersData
						insert_user_on_next.add(usersData)
					elif title == "Staff": 
						staff_dict[netid] = usersData
						insert_user_on_next.add(usersData)
					else: 
						continue # skip root, ndsc, and other invalid netids

				# Correct starttime
				try:
					temp_dict["START"] = datetime.datetime.strptime(temp_dict["START"],"%H:%M")
					temp_dict["START"] = temp_dict["START"].replace(year=datetime.datetime.now().year)
					temp_dict["START"] = temp_dict["START"].replace(month=datetime.datetime.now().month)
				except Exception:
					temp_dict["START"] = datetime.datetime.strptime(temp_dict["START"],"%b%d")
				
				# Correct pid
				temp_dict["PID"] = int(temp_dict["PID"])
				session_dict[(temp_dict["PID"],temp_dict["START"])]= temp_dict
				current_pids.add((temp_dict["PID"],temp_dict["START"]))
				#current_pids.add((temp_dict["USER"], temp_dict["PID"]))
				#session_dict[(temp_dict["USER"], temp_dict["PID"])] = temp_dict
			

			
			# Update data files
			ended_pids = previous_pids - current_pids 
			new_pids = current_pids - previous_pids
			stayed_pids = current_pids & previous_pids # set intersection
			previous_pids = current_pids

			for p in stayed_pids:
				cprocess = process_dict[p]
				temp_dict = session_dict[p]
				
				cprocess.update_data(temp_dict["%CPU"],temp_dict["%MEM"],temp_dict["VSZ"],temp_dict["RSS"])
				process_dict[p] = cprocess
				update_proc_on_next.add(p)
			for p in new_pids:
				#if(firstTime):
				if not p in process_dict:
					temp_dict = session_dict[p]
					new_proc = ProcessData(temp_dict["USER"],temp_dict["PID"],temp_dict["%CPU"],temp_dict["%MEM"],temp_dict["VSZ"],temp_dict["RSS"],temp_dict["TTY"],temp_dict["STAT"],temp_dict["START"],MACHINE_ID,temp_dict["COMMAND"])
					process_dict[p] = new_proc
					insert_proc_on_next.add(p)
				#else:
				#	temp_dict = session_dict[p]
				#	new_proc = ProcessData(temp_dict["USER"],temp_dict["PID"],temp_dict["%CPU"],temp_dict["%MEM"],temp_dict["VSZ"],temp_dict["RSS"],temp_dict["TTY"],temp_dict["STAT"],temp_dict["START"],MACHINE_ID,temp_dict["COMMAND"])
				#	process_dict[p] = new_proc
				#	insert_proc_on_next.add(p)
					
			for p in ended_pids:
				cprocess = process_dict[p]
				cprocess.end_time = datetime.datetime.now()
				process_dict[p] = cprocess
				update_proc_on_next.add(p)
				ended_pids_global.add(p)
			
			# Write data to file if interval met
			if(time.time() - last_write > WRITE_INTERVAL):
				#t = threading.Thread(target=writeToDatabase,args=(process_dict, student_dict, faculty_dict, staff_dict))
				#t.start() # Write in separate Thread
				writeToDatabase(process_dict)
				last_write = time.time()
			
			if(time.time() - last_sample > SAMPLE_INTERVAL):
				print("Time wasn't enough to finish sampling")
				last_sample = time.time()
			else:
				time.sleep(SAMPLE_INTERVAL-(time.time()-last_sample))
				last_sample = time.time()

	    except sql.exc.IntegrityError as e:
		    #traceback.print_exc()
		    print "Integrity Error: {}".format(e)
		    with open("integrity_log.error","a") as f:
			    f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))
	    except Exception as e:
			traceback.print_exc()
			print "Error: {}".format(e)
			sys.exit(1)
			with open("log.error","a") as f:
				f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))

    
except KeyboardInterrupt:
	print "Halting Data collection..."
	sys.exit(0)
	#writeData(process_dict,student_dict, faculty_dict, staff_dict, -1,-1) # Parameters force backup to occur
except Exception as e:
	print "Im outside while"
	traceback.print_exc()
	print "Error: {}".format(e)
	with open("log.error","a") as f:
		f.write("[{}]: {}\n".format(datetime.datetime.now(),str(e)))
		#writeData(process_dict,student_dict, faculty_dict, staff_dict, -1,-1) # Parameters force backup to occur

