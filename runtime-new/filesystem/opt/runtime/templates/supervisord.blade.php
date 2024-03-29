[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0
pidfile=/tmp/supervisord.pid
user=root
loglevel=info

[program:php-fpm]
command=/usr/sbin/php-fpm%(ENV_BUILD_PHP_VERSION)s -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

@if(env('RUNTIME_USER_PASSWORD'))
[program:sshd]
command=/usr/sbin/sshd -D
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0
@endif

@foreach($supervisor_programs as $program)
[program:{{ $program['name'] }}]
directory={{ env('RUNTIME_PUBLIC_PATH') }}
user={{ env('RUNTIME_USER_NAME') }}
command={{ $program['command'] }}
environment=HOME="{{ env('RUNTIME_USER_HOME') }}",USER="{{ env('RUNTIME_USER_NAME') }}"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0
@endforeach

@if(file_exists("/opt/runtime/custom/supervisor.conf"))
[include]
files = /opt/runtime/custom/supervisor.conf
@endif

[eventlistener:processes]
command=bash -c "printf 'READY\n' && while read line; do kill -SIGQUIT $PPID; done < /dev/stdin"
events=PROCESS_STATE_STOPPED,PROCESS_STATE_EXITED,PROCESS_STATE_FATAL
