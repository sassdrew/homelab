<?php
$CONFIG = array (
  'trusted_proxies' => ['192.168.1.54'], #added
  'overwritehost' => 'nextcloud.dragon.local', #added
  'overwriteprotocol' => 'https', #added
  'htaccess.RewriteBase' => '/',
  'memcache.local' => '\\OC\\Memcache\\APCu',
  'apps_paths' =>
  array (
    0 =>
    array (
      'path' => '/var/www/html/apps',
      'url' => '/apps',
      'writable' => false,
    ),
    1 =>
    array (
      'path' => '/var/www/html/custom_apps',
      'url' => '/custom_apps',
      'writable' => true,
    ),
  ),
  'instanceid' => 'hash',
  'passwordsalt' => 'hash',
  'secret' => 'hash',
  'trusted_domains' =>
  array (
    0 => 'nextcloud.dragon.local', #updated
  ),
  'datadirectory' => '/var/www/html/data',
  'dbtype' => 'mysql',
  'version' => '27.0.1.2',
  'overwrite.cli.url' => 'https://nextcloud.dragon.local', #updated
  'overwriteprotocol' => 'https', #added
  'dbname' => 'nextcloud',
  'dbhost' => 'db',
  'dbport' => '',
  'dbtableprefix' => 'oc_',
  'mysql.utf8mb4' => true,
  'dbuser' => 'nextcloud',
  'dbpassword' => 'hash',
  'installed' => true,
);