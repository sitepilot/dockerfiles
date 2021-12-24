# Path to document root
root {{ env('RUNTIME_PUBLIC_PATH') }}/{{ $nginx_doc_root }};

# File to be used as index
index index.php;

# Overrides logs defined in nginx.conf, allows per site logs.
access_log {{ env('RUNTIME_LOGS_PATH') }}/access.log;
error_log {{ env('RUNTIME_LOGS_PATH') }}/error.log;

# Default server block rules
include /opt/runtime/config/nginx/server/defaults.conf;

# Fastcgi cache rules
include /opt/runtime/config/nginx/server/fastcgi-cache.conf;

# Endpoint for health checks
location /sitepilot {
	alias /opt/runtime/www/;
	index index.php;
	access_log off;
	location ~ \.php$ {     
    	fastcgi_pass $upstream;
		include /opt/runtime/config/nginx/fastcgi-params.conf;
        fastcgi_param SCRIPT_FILENAME  $request_filename;
    }
}

location / {
	try_files $uri $uri/ /index.php?$args;
}

location ~ \.php$ {
	try_files $uri =404;
	include /opt/runtime/config/nginx/fastcgi-params.conf;

	# Use the php pool defined in the upstream variable.
	# See /opt/runtime/config/nginx/php-pool.conf for definition.
	fastcgi_pass $upstream;

	@if($nginx_cache)
	# Skip cache based on rules in /opt/runtime/config/nginx/server/fastcgi-cache.conf.
	fastcgi_cache_bypass $skip_cache;
	fastcgi_no_cache $skip_cache;

	# Define memory zone for caching. Should match key_zone in fastcgi_cache_path above.
	fastcgi_cache runtime;

	# Define caching time.
	fastcgi_cache_valid 60m;
	@endif
}
