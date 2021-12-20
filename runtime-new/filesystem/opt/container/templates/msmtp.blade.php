defaults
auth           {{ $smtp_username && $smtp_password ? 'on' : 'off' }}
tls            {{ $smtp_tls ? 'on' : 'off' }}
logfile        {{ env('RUNTIME_LOGS_PATH') }}/mail.log
tls_trust_file /etc/ssl/certs/ca-certificates.crt

account        mailrelay
host           {{ $smtp_host }}
port           {{ $smtp_port }}
from           {{ $smtp_from }}
@if($smtp_username)
user          {{ $smtp_username }}
@endif
@if($smtp_password)
password      {{ $smtp_password }}
@endif

account default : mailrelay