def index(request):
	from . import http
	return http.http(request, 'patch')
