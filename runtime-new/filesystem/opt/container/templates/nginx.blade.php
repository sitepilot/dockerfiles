# The user account used by the worker processes.
user {{ env('RUNTIME_USER_NAME') }};

# Set to number of CPU cores, auto will try to autodetect.
worker_processes auto;

# Maximum open file descriptors per process. Should be greater than worker_connections.
worker_rlimit_nofile 8192;

# File that stores the process ID. Rarely needs changing.
pid /run/nginx.pid;

events {
	# Set the maximum number of connection each worker process can open. Anything higher than this
	# will require Unix optimisations.
	worker_connections 8000;

	# Accept all new connections as they're opened.
	multi_accept on;
}

http {
	# HTTP
	include /opt/container/config/nginx/http.conf;

	# MIME Types
	include /opt/container/config/nginx/mime-types.conf;
	default_type application/octet-stream;

	# Limits & Timeouts
	include /opt/container/config/nginx/limits.conf;

	# Set the maximum allowed size of client request body. This should be set
	# to the value of files sizes you wish to upload to the WordPress Media Library.
	# You may also need to change the values `upload_max_filesize` and `post_max_size` within
	# your php.ini for the changes to apply.
	client_max_body_size {{ $php_upload_size }};

	# Some WP plugins that push large amounts of data via cookies
	# can cause 500 HTTP errors if these values aren't increased.
	fastcgi_buffers 16 16k;
	fastcgi_buffer_size 32k;

	# Default Logs
	error_log /var/log/nginx/error.log warn;
	access_log /var/log/nginx/access.log;

	# Gzip
	include /opt/container/config/nginx/gzip.conf;

	# exposes configured php pool on $upstream variable
	include /opt/container/config/nginx/php-pool.conf;

    # Define path to cache and memory zone. The memory zone should be unique.
    # keys_zone=single-site-with-caching.com:100m creates the memory zone and sets the maximum size in MBs.
    # inactive=60m will remove cached items that haven't been accessed for 60 minutes or more.
    fastcgi_cache_path /home/runtime/cache levels=1:2 keys_zone=runtime:100m inactive=60m;

    #HTTP
    server {
        # Ports to listen on, uncomment one.
        listen 80 default_server;
        listen [::]:80 default_server;

        # Vhost
	    include /opt/container/config/vhost.conf;
    }

    # HTTPS
    server {
        # Ports to listen on, uncomment one.
        listen 443 ssl http2 default_server;
        listen [::]:443 ssl http2 default_server;

        # Paths to certificate files.
        ssl_certificate {{ $nginx_ssl_cert }};
        ssl_certificate_key {{ $nginx_ssl_key }};

        # SSL rules
        # include /opt/container/config/nginx/server/ssl.conf;

        # Vhost
	    include /opt/container/config/vhost.conf;
    }
}