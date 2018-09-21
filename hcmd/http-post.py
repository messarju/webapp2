def getheaders(request, m):
	def puth(*args):
		for l in args:
			l = l.strip().split(':', 1)
			if l and len(l) > 1:
				a = l[0].strip()
				b = l[1].strip()
				if a and b:
					# print(a, b)
					m[a] = b
	for n in ('H', 'headers'):
		for d in (request.GET, request.POST):
			_ = d.get(n, '').strip()
			if _:
				if _[0].isalnum():
					puth(_)
				else:
					puth(_ for _ in _.split(_[0]))
			i = 0
			while 1:
				_ = d.get("%s[%d]" % (n, i))
				if _ is None:
					break
				puth(_)
				i += 1
	for d in (request.GET, request.POST):
		for k in d:
			if k and k.endswith(":"):
				v = d[k]
				k = k[0:-1]
				m[k] = v
def index(request):
	from django.http import HttpResponse
	from requests import post
	req = request.POST.copy()
	req.update(request.GET)
	url = req.get('url')
	if not url:
		raise RuntimeError("%s: No URL " % (__name__,))
	content = None
	while 1:
		_ = req.get('content', None)
		if _:
			content = _
			break
		_ = request.FILES
		if _:
			_ = _.get('content')
			if _:
				content = _
			break
		break
	if content is None:
		raise RuntimeError("%s: No Content " % (__name__,))
	headers = {}
	getheaders(request, headers)
	_ = req.get('content-type', None)
	if _: headers["Content-Type"] = _
	_ = req.get('authorization', None)
	if _: headers["Authorization"] = _
	_ = req.get('user-agent', None)
	if _:
		if _ == '.': _ = request.META['HTTP_USER_AGENT']
		if _: headers["User-Agent"] = _
	# print(headers)
	# return HttpResponse(repr([(url, content, headers)]), content_type="text/html")
	use_status_code = req.get('use_status_code', None)
	r = post(url, content, headers=headers)
	return HttpResponse(r, status=(use_status_code is not None) and int(use_status_code) or r.status_code)
