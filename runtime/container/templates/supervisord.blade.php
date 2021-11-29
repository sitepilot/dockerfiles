[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0
pidfile=/tmp/supervisord.pid
user=root
loglevel=info

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

[program:lshttpd]
command=/opt/container/bin/lshttpd
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[eventlistener:processes]
command=bash -c "printf 'READY\n' && while read line; do kill -SIGQUIT $PPID; done < /dev/stdin"
events=PROCESS_STATE_STOPPED,PROCESS_STATE_EXITED,PROCESS_STATE_FATAL

[include]
files = /opt/container/custom/supervisor.d/*.conf
