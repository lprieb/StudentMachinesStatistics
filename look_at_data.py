from processesData import *
from usersData import *
import cPickle

student_dict = cPickle.load(open("data/student_data.p", "rb"))
process_dict = cPickle.load(open("data/process_data.p", "rb"))
faculty_dict = cPickle.load(open("data/faculty_data.p", "rb"))

while True:
	# Get a student or faculty
	netid = raw_input("Enter a netid: ")
	while netid not in student_dict and netid not in faculty_dict:
		netid = raw_input("{} does not exist in database. Try again: ".format(netid))

	if netid in student_dict.keys():
		student = student_dict[netid]
		print "Name: {}".format(student.name)
		print "NetID: {}".format(netid)
		print "Grade Level: {}".format(student.grade)
		print "College: {}".format(student.college)
	
	if netid in faculty_dict.keys():
		faculty = faculty_dict[netid]
		print "Name: {}".format(faculty.name)
		print "NetID: {}".format(netid)
		print "College: {}".format(faculty.college)
	
	# Get the user's processes
	my_proc = []
	for pid_start, process_data in process_dict.items():
		if process_data.user == netid:
			my_proc.append(pid_start)

	# Display all the user's processes
	print "PID\tnetid\tstart\tend"
	for proc in my_proc:
		pd = process_dict[proc]
		print "{}\t{}\t{}\t{}".format(pd.pid, pd.user, pd.start, pd.end_time)
