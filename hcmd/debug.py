def index(request):
	from django.http import HttpResponse
	import pprint
	if request.GET.get('body') == '1':
		return HttpResponse(request.body)
	vars = {'GET' : request.GET, 'POST' : request.POST, 'META' : request.META, 'FILES' : request.FILES}
	return HttpResponse(pprint.pformat(vars))
