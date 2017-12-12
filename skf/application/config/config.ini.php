; config file for SKF Framework

[application]
default_controller = index
default_action = index
error_controller = error404
error_reporting = E_ALL
display_errors = 1
language = en
timezone = "America/Los_Angeles"
site_name = SKF
version = 0.0.15
currency = USD
domain = multi

[database]
db_type = mysql
db_name = new_skf 
db_host = localhost
db_user = username
db_pass = password
db_port = 3306
db_sqlite_path = /lib

[template]
template_dir = "templates"
cache_dir = "/tmp/cache"
cache_lifetime = 3600

[mail]
mailer_type = system
admin_email = admin@example.com
admin_name = "SKF Admin"
smtp_server = mail.example.com 
smtp_port = 25;
x_mailer = "PHPRO.ORG Mail"
smtp_server = "mail.example.com"
smtp_port = 25
smtp_timeout = 30

[logging]
log_level = 200
log_handler = file
log_file = /tmp/sevenkevins.log

[memcached]
;servers[] = '192.168.178.200:11211:100'
;servers[] = '192.168.178.201:11211:100'
servers[] = '127.0.0.1:11211:100'

