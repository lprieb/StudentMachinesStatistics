import math
class ProcessData:
	def __init__(self,user,pid,cpu_u,mem_u,vsz,rss,tty,stat,start,running_time,command):
		self.user = user
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
		self.running_time = running_time
		self.command = command
		self.end_time = 0
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

