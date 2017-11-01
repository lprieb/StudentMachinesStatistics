import requests
import json

class UserData(object):
	def __init__(self, netid, affiliation, name, college):
		self.netid = netid
		self.name = name
		self.title = affiliation # ie. Student or Staff
		self.college = college # ie. College of Engineering

class StudentData(UserData):
	def __init__(self, netid, affiliation, name, college, grade):
		UserData.__init__(self, netid, affiliation, name, college)
		self.grade = grade

class FacultyData(UserData):
	def __init__(self, netid, affiliation, name, college):
		UserData.__init__(self, netid, affiliation, name, college)
