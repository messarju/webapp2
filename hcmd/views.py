from django.views.decorators.csrf import csrf_exempt
@csrf_exempt
def index(request):
	_eno = request.GET.get('_eno') or 500 #or request.POST.get('_eno') or 500
	try:
		from django.http import HttpResponse
		from django.conf import settings
		settings.DEFAULT_CONTENT_TYPE = "image/png"
		_ = request.GET.get('_') #or request.POST.get('_')
		if _:
			return getattr(__import__(__package__, fromlist=[_,]), _).index(request)
		raise RuntimeError("No command")
	except:
		from traceback import format_exc
		return HttpResponse(format_exc(), status=int(_eno), content_type="text/html")
