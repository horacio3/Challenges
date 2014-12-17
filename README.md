Challenges
==========


PHP

PHP for Windows Issues:

Need to enable curl via php.ini

curl.cainfo = c:\windows\ca-bundle.crt
include_path = ".;C:\PHP\includes\lib"
date.timezone=America/Chicago
extension_dir = "ext"
extension=php_curl.dll

php curl needs ca bundle to attach to https address

http://ea.tl/2012/02/02/windows-php-curl-ssl-certificate-problem/
vb script for ca-bundle

