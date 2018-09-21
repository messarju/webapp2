def index(request):
	from django.http import HttpResponse
	from request import get
	req = request.POST.copy()
	req.update(request.GET)
	url = req.get('url')
	if not url:
		raise RuntimeError("%s: No URL " % (__name__,))
	_bytes = req.get('bytes')
	if not _bytes:
		raise RuntimeError("%s: No Range " % (__name__,))
	elif _bytes == '0-':
		r = get(u)
	else:
		r = get(url, headers={'Range': 'bytes=' + _bytes})
	return HttpResponse(r, status=r.status_code)