defaults
auth           {{ $smtp_username && $smtp_password ? 'yes' : 'no' }}
tls            {{ $smtp_tls ? 'yes' : 'no' }}
logfile        /home/runtime/logs/mail.log
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