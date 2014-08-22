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
Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/', '/usr/local/bin/' ] }

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


##### SSH section #####

# Make sure the sftp subsystem point to the right place
file_line { 'update-sshd_config':
  path   => '/etc/ssh/sshd_config',
  line   => 'Subsystem sftp /usr/libexec/openssh/sftp-server',
  match  => '^Subsystem sftp',
  notify => Service['sshd']
}

service { 'sshd':
  ensure => 'running',
  enable => true,
}


###### PHP section #####

exec { 'install-remi-repo':
  cwd         => '/etc/yum.repos.d',
  command     => 'wget http://rpms.famillecollet.com/enterprise/remi.repo',
  unless      => '[ -f /etc/yum.repos.d/remi.repo ]',
}

yumrepo { 'remi':
  baseurl  => 'http://rpms.famillecollet.com/enterprise/$releasever/remi/$basearch/',
  enabled  => 1,
  gpgcheck => 1,
  gpgkey   => 'http://rpms.famillecollet.com/RPM-GPG-KEY-remi',
}

yumrepo { 'remi-php56':
  baseurl  => 'http://rpms.famillecollet.com/enterprise/$releasever/php56/$basearch/',
  enabled  => 1,
  gpgcheck => 1,
  gpgkey   => 'http://rpms.famillecollet.com/RPM-GPG-KEY-remi',
  require  => Yumrepo['remi'],
}

package { 'php':
  ensure  => present,
  require => [ Yumrepo['remi'], Yumrepo['remi-php56'] ],
}

file { '/var/lib/php/session':
  ensure  => directory,
  owner   => 'vagrant',
  group   => 'www-data',
  require => [ Package['php'], Package['php-fpm'] ],
}

$phpModules = ['php-fpm', 'php-xml', 'php-mysql', 'php-mbstring', 'php-pecl-zendopcache', 'php-pecl-xdebug']
package { $phpModules:
  ensure  => present,
  require => Package['php'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-setting-timezone":
  ensure  => present,
  path    => '/etc/php.ini',
  section => 'Date',
  setting => 'date.timezone',
  value   => 'America/Montreal',
  require => Package['php'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-setting-display_errors":
  ensure  => present,
  path    => '/etc/php.ini',
  section => 'PHP',
  setting => 'display_errors',
  value   => 'On',
  require => Package['php'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-fpm-setting-user":
  ensure  => present,
  path    => '/etc/php-fpm.d/www.conf',
  section => 'www',
  setting => 'user',
  value   => 'vagrant',
  require => Package['php-fpm'],
  notify  => Service['php-fpm'],
}

ini_setting { "php-fpm-setting-group":
  ensure  => present,
  path    => '/etc/php-fpm.d/www.conf',
  section => 'www',
  setting => 'group',
  value   => 'vagrant',
  require => Package['php-fpm'],
  notify  => Service['php-fpm'],
}

class { 'xdebug::settings' :
  require => Package['php-pecl-xdebug']
}

service { 'php-fpm' :
  name => 'php-fpm',
  ensure => 'running',
  enable => true,
  require => Package['php-fpm'],
}


###### NGINX section ######

include nginx


###### Project section ######

vcsrepo { '/srv/ciin':
  ensure   => present,
  source   => 'git://github.com/EquisoftDev/Equisoft_Thunderhorse_Project.git',
  owner    => 'vagrant',
  group    => 'www-data',
  revision => 'develop'
}

exec { 'composer-install':
  command     => 'sudo -u vagrant /usr/local/bin/composer --prefer-source -n -q -o install',
  cwd         => '/srv/ciin/app',
  timeout     => 900,
  require     => [ Class['composer'], Vcsrepo['/srv/ciin'] ],
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
		path    => '/etc/php.d/15-xdebug.ini',
		section => '',
		setting => 'xdebug.idekey',
		value   => 'PHPSTORM',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}

	ini_setting { "php-xdebug-remote_enable":
		ensure  => present,
		path    => '/etc/php.d/15-xdebug.ini',
		section => '',
		setting => 'xdebug.remote_enable',
		value   => '1',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}

	ini_setting { "php-xdebug-remote_connect_back":
		ensure  => present,
		path    => '/etc/php.d/15-xdebug.ini',
		section => '',
		setting => 'xdebug.remote_connect_back',
		value   => '1',
		require => Package['php-pecl-xdebug'],
		notify  => Service['php-fpm'],
	}
}