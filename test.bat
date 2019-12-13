@ECHO OFF
vendor/bin/phpcs -p -s --standard=phpcs.xml src && codecept run unit && codecept run wpunit && codecept run functional & codecept run acceptance
