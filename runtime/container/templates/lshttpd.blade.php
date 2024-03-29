# {{ $generated_by }}
user                      {{ env('RUNTIME_USER_NAME') }}
group                     {{ env('RUNTIME_USER_NAME') }}
serverName                {{ $server_name }}
priority                  0
inMemBufSize              60M
swappingDir               /tmp/lshttpd/swap
autoFix503                1
gracefulRestartTimeout    300
mime                      conf/mime.properties
showVersionNumber         0
useIpInProxyHeader        1
adminEmails               {{ $admin_email }}

errorlog logs/error.log {
  logLevel                WARN
  debugLevel              0
  rollingSize             10M
  keepDays                30
  enableStderrLog         1
}

accesslog logs/access.log {
  rollingSize             10M
  keepDays                30
  compressArchive         0
}

indexFiles                index.html, index.php

expires  {
  enableExpires           1
  expiresByType           image/*=A604800,text/css=A604800,application/x-javascript=A604800,application/javascript=A604800,font/*=A604800,application/x-font-ttf=A604800
}

autoLoadHtaccess          1
uploadTmpDir              /tmp/lshttpd

tuning  {
  maxConnections          10000
  maxSSLConnections       10000
  connTimeout             300
  maxKeepAliveReq         10000
  keepAliveTimeout        5
  sndBufSize              0
  rcvBufSize              0
  maxReqURLLen            32768
  maxReqHeaderSize        65536
  maxReqBodySize          2047M
  maxDynRespHeaderSize    32768
  maxDynRespSize          2047M
  maxCachedFileSize       4096
  totalInMemCacheSize     20M
  maxMMapFileSize         256K
  totalMMapCacheSize      40M
  useSendfile             1
  fileETag                28
  enableGzipCompress      1
  compressibleTypes       default
  enableDynGzipCompress   1
  gzipCompressLevel       6
  gzipAutoUpdateStatic    1
  gzipStaticCompressLevel 6
  brStaticCompressLevel   6
  gzipMaxFileSize         10M
  gzipMinFileSize         300
  quicEnable              1
  quicShmDir              /dev/shm
}

fileAccessControl  {
  followSymbolLink        1
  checkSymbolLink         0
  requiredPermissionMask  000
  restrictedPermissionMask 000
}

perClientConnLimit  {
  staticReqPerSec         0
  dynReqPerSec            0
  outBandwidth            0
  inBandwidth             0
  softLimit               10000
  hardLimit               10000
  gracePeriod             15
  banPeriod               300
}

CGIRLimit  {
  maxCGIInstances         20
  minUID                  11
  minGID                  10
  priority                0
  CPUSoftLimit            10
  CPUHardLimit            50
  memSoftLimit            1460M
  memHardLimit            1470M
  procSoftLimit           400
  procHardLimit           450
}

accessDenyDir  {
  dir                     /
  dir                     /etc/*
  dir                     /dev/*
  dir                     conf/*
  dir                     admin/conf/*
}

accessControl  {
  allow                   ALL
}

extprocessor php {
  type                    lsapi
  address                 UDS://tmp/lshttpd/php.sock
  maxConns                {{ $php_workers }}
  initTimeout             60
  retryTimeout            0
  respBuffer              0
  autoStart               2
  path                    /usr/local/lsws/lsphp{{ env('PHP_VERSION') }}/bin/lsphp
  env                     LSAPI_MAX_IDLE=60
  env                     LSAPI_CHILDREN={{ $php_workers }}
}

scripthandler  {
  add                     lsapi:php php
}

module cache {
  internal                1
  checkPrivateCache       1
  checkPublicCache        1
  maxCacheObjSize         10000000
  maxStaleAge             200
  qsCache                 1
  reqCookieCache          1
  respCookieCache         1
  ignoreReqCacheCtrl      1
  ignoreRespCacheCtrl     0
  enableCache             0
  expireInSeconds         3600
  enablePrivateCache      0
  privateExpireInSeconds  3600
  ls_enabled              1
}

listener http {
  address                 *:80
  secure                  0
}

listener https {
  address                 *:443
  secure                  1
  keyFile                 {{ $ssl_key_file }}
  certFile                {{ $ssl_cert_file }}
}

vhTemplate runtime {
  templateFile            $SERVER_ROOT/conf/templates/vhost.conf
  listeners               http, https

  member default {
    vhDomain              *
    vhRoot                {{ env('RUNTIME_USER_HOME') }}
  }
}
