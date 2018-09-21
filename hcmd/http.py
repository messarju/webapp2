def index(request, method='get'):
	url = None
	include = None
	use_md5 = None
	use_status_code = None
	use_content_type = None
	hide_status = None
	args = {}
	# HEADERS
	args['headers'] = headers = {}
	for d in (request.POST, request.GET):
		for (k, v) in d.items():
			if not k:
				pass
			elif k == 'url':
				url = v
			elif k == 'method':
				method = v
			elif k == 'include':
				include = bool(int(v))
			elif k == 'md5':
				use_md5 = "MD5"
			elif k == 'hide_status':
				hide_status = bool(int(v))
			elif k == 'use_status_code':
				use_status_code = int(v)
			elif k == 'use_content_type':
				use_content_type = v
			elif k == 'allow_redirects':
				args['allow_redirects'] = bool(int(v))
			elif k == 'timeout':
				args['timeout'] = int(v)
			elif k == 'content_type':
				headers["Content-Type"] = v
			elif k == 'authorization':
				headers["Authorization"] = v
			elif k == 'user_agent':
				if v == '.': v = request.META['HTTP_USER_AGENT']
				if v == '-':
					headers.pop("User-Agent", None)
					headers.pop("user-agent", None)
				elif v:
					headers["User-Agent"] = v
			elif k.endswith(":"):
				headers[k[0:-1]] = v
			elif k in ('content', 'data'):
				args['data'] = v
	# CONTENT
	d = request.FILES
	if d:
		for (k, v) in d.items():
			if (k in ('content', 'data')) and v:
				args['data'] = v.read()
	if method in ('post', 'put', 'patch') and ('data' not in args):
		raise RuntimeError("%s: No Content " % (__name__,))
	if not url:
		raise RuntimeError("%s: No URL " % (__name__,))
	# print(url)
	# REQUEST
	from requests import request as http
	from django.http import HttpResponse
	req = http(method, url, **args)
	if include:
		res = HttpResponse(status=200)
		res.write("HTTP/1.1 %d\r\n" % req.status_code)
		for (k, v) in req.headers.items():
			res.write("%s: %s\r\n" % (k, v))
		if use_content_type:
			res.write("%s: %s\r\n" % ('Content-Type', use_content_type))
		if use_md5:
			body = req.content
			import hashlib
			from base64 import b64encode
			h = hashlib.new('md5')
			h.update(body)
			h = b64encode(h.digest())
			res.write("%s: %s\r\n" % (use_md5, h))
			res.write(b"\r\n")
			res.write(body)
		else:
			res.write(b"\r\n")
			for _ in req.iter_content(chunk_size=128*1024):
				res.write(_)
	else:
		from wsgiref.util import is_hop_by_hop
		res = HttpResponse(status=(use_status_code is not None) and use_status_code or req.status_code)
		if hide_status:
			res.status_code = 200
			res['X-Status-Code'] = req.status_code
		if use_content_type:
			res['Content-Type'] = use_content_type
			for (k, v) in req.headers.items():
				if is_hop_by_hop(k):
					continue
				elif k.lower() == 'content-type':
					continue
				res[k] = v
		else:
			for (k, v) in req.headers.items():
				if is_hop_by_hop(k):
					continue
				res[k] = v
		if method == 'head':
			pass
		else:
			if use_md5:
				body = req.content
				import hashlib
				from base64 import b64encode
				h = hashlib.new('md5')
				h.update(body)
				h = b64encode(h.digest())
				res[use_md5] = h
				res.write(body)
			else:
				for _ in req.iter_content(chunk_size=128*1024):
					res.write(_)
	return res
