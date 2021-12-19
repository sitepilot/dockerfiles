; {{ $generated_by }}
disable_functions = {{ $php_disable_functions }}
date.timezone = {{ $php_timezone }}
post_max_size = {{ $php_upload_size }}
upload_max_filesize = {{ $php_upload_size }}
memory_limit = {{ $php_memory_limit }}
expose_php = Off
short_open_tag = On
max_input_vars = {{ $php_max_input_vars }}
disable_functions = {{ $php_disable_functions }}
opcache.enable = 1
opcache.max_accelerated_files = 10000
opcache.memory_consumption = {{ $php_opcache_memory }}
opcache.revalidate_freq = 2
opcache.save_comments = 0
sendmail_path = /usr/bin/msmtp -t
