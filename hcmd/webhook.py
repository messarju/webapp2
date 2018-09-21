from django.views.decorators.csrf import csrf_exempt
@csrf_exempt
def index(request):
	try:
		from django.http import HttpResponse
		if request.method == "POST":
			PAT = 'EAAcxqD8o2oUBAIZAZBw3S8cueKRy5fD4TvJraupOy6A6nuTYq7dDKlv1i8a6N21xpY3nd7qwLMcessCxxZAIzZA2SQiM1TZBidMwZBKnRL2aVmkWmx0Jvso5QDMGaH2Mu7octi1bIQhzDB1ovTPTFhdhiufyLR6ZAhxhuZBghwiwRKtoenU9ZAGcU'
			url = "https://graph.facebook.com/v2.6/me/messages?access_token=" + PAT
			from requests import post
			from json import load, dump, dumps
			data = load(request)
			with open("json.txt", 'at') as o:
				dump(data, o, sort_keys=True, indent=4)
			for entry in data.get('entry', ''):
				for msg in entry.get('messaging', ''):
					message = msg.get('message')
					if not message or message.get('is_echo') is True:
						continue
					text = message.get('text')
					if not text:
						continue
					elif text.startswith("cmd ") or text.startswith("Cmd "):
						from shlex import split
						try:
							text = exec_cmd(split(text)[1:]).decode("UTF-8")
						except:
							from traceback import format_exc
							text = format_exc()
						if text is None:
							text = "None"
					elif text.startswith("py ") or text.startswith("Py "):
						try:
							text = exec_py(text[3:].strip())
						except:
							from traceback import format_exc
							text = format_exc()
						if text is None:
							text = "None"
					elif text.startswith("dl ") or text.startswith("Py "):
						from shlex import split
						try:
							text = exec_dl(split(text)[1:]).decode("UTF-8")
						except:
							from traceback import format_exc
							text = format_exc()
						if text is None:
							text = "None"
					else:
						text = dumps(msg)
					sender = msg['sender']
					with open("post.txt", 'at') as o:
						o.write(dumps(sender))
						o.write(" ")
					headers = {'Content-type': 'application/json'}
					i = 0
					j = 2000
					n = len(text)
					while i < n:
						msg = text[i:i+j]
						r = post(url, json={"recipient":sender, "message":{"text":msg,}}, headers=headers)
						with open("post.txt", 'at') as o:
							o.write(str(r.status_code) + "\n" + str(r.content) + "\n")
						if r.status_code != 200:
							r = post(url, json={"recipient":sender, "message":{"text":r.content.decode("UTF-8", 'replace'),}}, headers=headers)
							break
						i += j
			return HttpResponse("OK", content_type="text/plain")
		else:
			hub_mode = request.GET.get('hub.mode')
			if hub_mode != 'subscribe':
				raise RuntimeError("Unexpected mode %r" % hub_mode)
			hub_verify_token = request.GET.get('hub.verify_token')
			if hub_verify_token != 'asameshimae':
				raise RuntimeError("Unexpected token %r" % hub_verify_token)
			hub_challenge = request.GET.get('hub.challenge')
			if not hub_challenge:
				raise RuntimeError("Unexpected challenge %r" % hub_challenge)
			return HttpResponse(hub_challenge, content_type="text/plain")
	except:
		from traceback import format_exc
		resp = format_exc()
		with open("webhook.except", 'at') as o:
			o.write(resp)
		return HttpResponse(resp, status=500, content_type="text/plain")

def exec_cmd(args):
	from subprocess import Popen, PIPE, STDOUT, check_output, check_call, call
	kwargs = {}
	shell = None
	detached = None
	while args:
		a = args[0]
		if a.find('=') > 0:
			args.pop()
			(k, v) = a.split('=', 1)
			if 'detached' == k and v == '1':
				kwargs['stdout'] = open('detached.out', 'wb')
				kwargs['stderr'] = open('detached.err', 'wb')
				detached = True
			elif 'err2out' == k and v == '1':
				kwargs['stderr'] = STDOUT
			elif 'shell' == k:
				kwargs['shell'] = v == '1'
			elif 'stdin' == k:
				from tempfile import TemporaryFile as Temp
				f = kwargs['stdin'] = Temp()
				f.write(v.encode('UTF-8'))
				f.seek(0)
		else:
			if shell:
				from shlex import quote
				cmd = " ".join(quote(a) for a in args)
			else:
				cmd = args
			if detached:
				return str(call(cmd, **kwargs))
			return check_output(cmd, **kwargs)

def exec_dl(args):
	from subprocess import Popen, PIPE, STDOUT, check_output, check_call, call
	kwargs = {}
	shell = None
	detached = None
	while args:
		a = args[0]
		if a.find('=') > 0:
			args.pop()
			(k, v) = a.split('=', 1)
			if 'detached' == k and v == '1':
				kwargs['stdout'] = open('detached.out', 'wb')
				kwargs['stderr'] = open('detached.err', 'wb')
				detached = True
			elif 'err2out' == k and v == '1':
				kwargs['stderr'] = STDOUT
			elif 'shell' == k:
				kwargs['shell'] = v == '1'
			elif 'stdin' == k:
				from tempfile import TemporaryFile as Temp
				f = kwargs['stdin'] = Temp()
				f.write(v.encode('UTF-8'))
				f.seek(0)
		else:
			if shell:
				from shlex import quote
				cmd = " ".join(quote(a) for a in args)
			else:
				cmd = args
			if detached:
				return str(call(cmd, **kwargs))
			return check_output(cmd, **kwargs)

def exec_py(code):
	return __import__(__package__, fromlist=['run',]).run.load_compiled_from_memory("__run_module", "__run_file", compile(code, "__run_file", "exec")).main()
