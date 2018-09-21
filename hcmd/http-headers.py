def index(request):
	from django.http import HttpResponse
	from requests import head
	req = request.POST.copy()
	req.update(request.GET)
	url = req.get('url')
	if not url:
		raise RuntimeError("%s: No URL " % (__name__,))
	h = head(url, allow_redirects=True).headers
	return HttpResponse("\n".join(["%s: %s" % (k, h[k]) for k in h]))
