# EPEL module - https://forge.puppetlabs.com/stahnma/epel
include epel

# Git module - https://forge.puppetlabs.com/puppetlabs/git
include git

# Composer module - https://forge.puppetlabs.com/tPl0ch/composer
class { 'composer' :
	download_method => 'wget',
}

# Not sure what it changes but we don't have a warning if we set allow_virtual
Package {  allow_virtual => false, }

# Set path as provider by default for Exec so we don't have to specify it everytime
Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }

# Set git as provider by default for Vcsrepo so we don't have to specify it everytime
Vcsrepo { provider => git } 

##### Users & groups section ####

group { 'puppet':   ensure => present }
group { 'www-data': ensure => present }
group { 'www-user': ensure => present }

user { 'vagrant':
  shell   => '/bin/bash',
  home    => "/home/vagrant",
  ensure  => present,
  groups  => ['www-data', 'www-user'],
  require => [Group['www-data'], Group['www-user']]
}

user { ['apache', 'nginx', 'httpd', 'www-data']:
  shell  => '/bin/bash',
  ensure => present,
  groups => 'www-data',
  require => Group['www-data']
}


###### PHP section #####

# PHP module - https://forge.puppetlabs.com/example42/php
class { 'php':
  service => 'nginx',
  service_autorestart => true,
}

file { '/var/lib/php/session':
  ensure  => directory,
  owner   => 'www-data',
  group   => 'www-data',
  require => Class['php'],
}

php::module { "fpm": }
php::module { "xml": }
php::module { "mysql": }
php::module { "mbstring": }
php::pecl::module { "pecl-zendopcache": 
	service => 'nginx',
	service_autorestart => true,
	require => Yumrepo['epel'],
}
php::pecl::module { "pecl-xdebug": 
	service => 'nginx',
	service_autorestart => true,
	require => Yumrepo['epel'],
}

ini_setting { "php-setting-timezone":
  ensure  => present,
  path    => '/etc/php.ini',
  section => 'Date',
  setting => 'date.timezone',
  value   => 'America/Montreal',
  require => Class['php'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-setting-display_errors":
  ensure  => present,
  path    => '/etc/php.ini',
  section => 'PHP',
  setting => 'display_errors',
  value   => 'On',
  require => Class['php'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-fpm-setting-user":
  ensure  => present,
  path    => '/etc/php-fpm.d/www.conf',
  section => 'www',
  setting => 'user',
  value   => 'www-data',
  require => Php::Module['fpm'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-fpm-setting-group":
  ensure  => present,
  path    => '/etc/php-fpm.d/www.conf',
  section => 'www',
  setting => 'group',
  value   => 'www-data',
  require => Php::Module['fpm'],
  notify  => Service['php-fpm'],
}

class { 'xdebug::settings' :
	require => Php::Pecl::Module['pecl-xdebug']
}

service { 'php-fpm' :
	name => 'php-fpm',
	ensure => 'running',
	enable => true,
	require => Package['php'],
	notify => Service['nginx']
}

###### NGINX section ######

include nginx


###### Project section ######

vcsrepo { '/srv/ciin':
  ensure   => present,
  source   => 'git://github.com/EquisoftDev/Equisoft_Thunderhorse_Project.git',
  owner    => 'vagrant',
  group    => 'www-data',
  notify   => File['/srv/ciin'],
}

# TODO - Only set the folders that need a write permission
file { '/srv/ciin':
  ensure  => directory,
  mode    => 'g+w',
  recurse => true,
}

composer::exec { 'project-install':
    cmd                  => 'install',
    cwd                  => '/srv/ciin/src',
    optimize             => true, # Optimize autoloader
}

###### Database section ######

$users = {
	'devuser@localhost' => {
    ensure                   => 'present',
    max_connections_per_hour => '0',
    max_queries_per_hour     => '0',
    max_updates_per_hour     => '0',
    max_user_connections     => '0',
    password_hash            => '*D7F685475BE8D76672B4E15962BB085F55726E4B',
  },
}

# MySQL module - https://forge.puppetlabs.com/puppetlabs/mysql
class { '::mysql::server' :
  	root_password   => 'devuser',
  	users           => $users,
  	service_enabled => true,
}


###### phpMyAdmin section ######

# phpMyAdmin module - https://forge.puppetlabs.com/leoc/phpmyadmin
#class { 'phpmyadmin':
#	path => "/srv/phpmyadmin",
#	user => "root",
#	servers => [
#		{
#			desc => "local",
#			host => "localhost",
#		},
#		{
#			desc => "dev",
#			host => "EquiDevMySQL",
#		}
#	],
#	require => Package['git']
#}


###### Webgrind section ######

vcsrepo { '/srv/webgrind':
  ensure   => latest,
  source   => 'git://github.com/jokkedk/webgrind.git',
  owner    => 'vagrant',
  group    => 'www-data',
}

###### Misc section ######

class xdebug::settings {

	ini_setting { "php-xdebug-idekey":
		ensure  => present,
		path    => '/etc/php.d/xdebug.ini',
		section => '',
		setting => 'xdebug.idekey',
		value   => 'PHPSTORM',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}

	ini_setting { "php-xdebug-remote_enable":
		ensure  => present,
		path    => '/etc/php.d/xdebug.ini',
		section => '',
		setting => 'xdebug.remote_enable',
		value   => '1',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}

	ini_setting { "php-xdebug-remote_connect_back":
		ensure  => present,
		path    => '/etc/php.d/xdebug.ini',
		section => '',
		setting => 'xdebug.remote_connect_back',
		value   => '1',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}
}