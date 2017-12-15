import math
class ProcessData:
	def __init__(self,netid=None,pid=None,cpu_u=None,mem_u=None,vsz=None,rss=None,tty=None,stat=None,start=None,machineid=None,command=None,tdict=None):
		if(tdict):
		    self.updateFromDict(tdict)
		else:
		    self.netid = netid
		    self.pid = pid
		    self.avg_cpu_u = float(cpu_u)
		    self.avg_mem_u = float(mem_u)
		    self.avg_vsz = float(vsz)
		    self.avg_rss = float(rss)
		    self.max_cpu_u = float(cpu_u)
		    self.max_mem_u = float(mem_u)
		    self.max_vsz = float(vsz)
		    self.max_rss = float(rss)
		    self.var_cpu_u = 0.0
		    self.var_mem_u = 0.0
		    self.var_vsz = 0.0
		    self.var_rss = 0.0
		    self.tty = tty
		    self.stat = stat
		    self.start = start
		    self.command = command
		    self.end_time = 0
		    self.machineid = machineid
		    self.measures = 1
	def update_data(self,cpu_u,mem_u,vsz,rss):
		cpu_u = float(cpu_u)
		mem_u = float(mem_u)
		vsz = float(vsz)
		rss = float(rss)
		# Update Averages
		old_avg_cpu_u = self.avg_cpu_u
		old_avg_mem_u = self.avg_mem_u
		old_avg_vsz = self.avg_vsz
		old_avg_rss = self.avg_rss
		self.avg_cpu_u = (self.avg_cpu_u*self.measures + cpu_u)/(self.measures+1)
		self.avg_mem_u = (self.avg_mem_u*self.measures + mem_u)/(self.measures+1)
		self.avg_vsz = (self.avg_vsz*self.measures + vsz)/(self.measures+1)
		self.avg_rss = (self.avg_rss*self.measures + rss)/(self.measures+1)

		# Update Maxes
		self.max_cpu_u = max([self.max_cpu_u,cpu_u])
		self.max_mem_u = max([self.max_mem_u,mem_u])
		self.max_vsz = max([self.max_vsz,vsz])
		self.max_rss = max([self.max_rss,rss])

		# Update Variances
		# Proof of formula below can be provided if necessary
		self.var_cpu_u = (1.0/self.measures)*(self.var_cpu_u*(self.measures-1)+math.pow(old_avg_cpu_u,2)*self.measures+math.pow(cpu_u,2) - math.pow(self.avg_cpu_u,2)*(self.measures+1))
		self.var_mem_u = (1.0/self.measures)*(self.var_mem_u*(self.measures-1)+math.pow(old_avg_mem_u,2)*self.measures+math.pow(mem_u,2) - math.pow(self.avg_mem_u,2)*(self.measures+1))
		self.var_vsz = (1.0/self.measures)*(self.var_vsz*(self.measures-1)+math.pow(old_avg_vsz,2)*self.measures+math.pow(vsz,2) - math.pow(self.avg_vsz,2)*(self.measures+1))
		self.var_rss = (1.0/self.measures)*(self.var_rss*(self.measures-1)+math.pow(old_avg_rss,2)*self.measures+math.pow(rss,2) - math.pow(self.avg_rss,2)*(self.measures+1))

	def updateFromDict(self,tdict):
	    self.netid = tdict['netid']
	    self.pid = tdict['pid']
	    self.avg_cpu_u = tdict['average_cpu_usage']
	    self.avg_mem_u = tdict['average_mem_usage']
	    self.avg_vsz = tdict['average_vsz']
	    self.avg_rss = tdict['average_rss']
	    self.max_cpu_u = tdict['max_cpu_usage']
	    self.max_mem_u = tdict['max_mem_usage']
	    self.max_vsz = tdict['max_vsz']
	    self.max_rss = tdict['max_rss']
	    self.var_cpu_u = tdict['variance_cpu_usage']
	    self.var_mem_u = tdict['variance_mem_usage']
	    self.var_vsz = tdict['variance_vsz']
	    self.var_rss = tdict['variance_rss']
	    self.tty = tdict['tty']
	    self.stat = tdict['stat']
	    self.start = tdict['startTime']
	    self.command = tdict['command']
	    self.end_time = tdict['endTime']
	    self.measures = tdict['measures']
	    self.machineid = tdict['machineid']
	    
