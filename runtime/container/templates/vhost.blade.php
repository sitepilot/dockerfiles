# {{ $generated_by }}
vhRoot                    /home/$VH_NAME
configFile                $SERVER_ROOT/conf/vhosts/$VH_NAME.conf
allowSymbolLink           2
enableScript              1
restrained                1

virtualHostConfig  {
  docRoot                 $VH_ROOT/public/{{ $doc_root }}

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
  }
}
