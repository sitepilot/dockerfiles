# {{ $generated_by }}
vhRoot                    /home/$VH_NAME
configFile                $SERVER_ROOT/conf/vhosts/$VH_NAME.conf
allowSymbolLink           2
enableScript              1
restrained                1

virtualHostConfig  {
  docRoot                 $VH_ROOT/public

  errorlog $VH_ROOT/logs/error.log {
    useServer             0
    logLevel              ERROR
    rollingSize           10M
    keepDays              30
  }

  accesslog $VH_ROOT/logs/access.log {
    useServer             0
    rollingSize           10M
    keepDays              30
  }

  rewrite  {
    enable                1
    autoLoadHtaccess      1
  }

  phpIniOverride  {
    php_admin_value mail.log "$VH_ROOTlogs/php-mail.log"
    php_admin_value error_log "$VH_ROOTlogs/php-error.log"
    php_admin_value date.timezone "{{ $php_timezone }}"
    php_admin_value post_max_size "{{ $php_upload_size }}"
    php_admin_value upload_max_filesize "{{ $php_upload_size }}"
    php_admin_value memory_limit "{{ $php_memory_limit }}"
    php_admin_value expose_php "Off"
    php_admin_value short_open_tag "On"
    php_admin_value max_input_vars "{{ $php_max_input_vars }}"
    php_admin_value opcache.enable "1"
    php_admin_value opcache.max_accelerated_files 10000
    php_admin_value opcache.memory_consumption "{{ $php_opcache_memory }}"
    php_admin_value opcache.revalidate_freq 2
    php_admin_value opcache.save_comments 0
    php_admin_value sendmail_path "/usr/bin/msmtp -C /opt/stack/share/config/msmtp.conf -t"
  }
}
