import requests
import json

class UserData(object):
	def __init__(self, netid, affiliation, firstname, lastname, college):
		self.netid = netid
		self.firstname = firstname
		self.lastname = lastname
		self.title = affiliation # ie. Student or Staff
		self.college = college # ie. College of Engineering

class StudentData(UserData):
	def __init__(self, netid, affiliation, firstname, lastname, college, grade):
		UserData.__init__(self, netid, affiliation, firstname, lastname, college)
		self.grade = grade

class FacultyData(UserData):
	def __init__(self, netid, affiliation, firstname, lastname, college):
		UserData.__init__(self, netid, affiliation, firstname, lastname, college)

class StaffData(UserData):
	def __init__(self, netid, affiliation, firstname, lastname, college):
		UserData.__init__(self, netid, affiliation, firstname, lastname, college)

