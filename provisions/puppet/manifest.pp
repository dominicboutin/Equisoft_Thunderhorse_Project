# Not sure what it changes but we don't have a warning if we set allow_virtual
Package {  allow_virtual => false, }

# Set path as provider by default for Exec so we don't have to specify it everytime
Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }

# Set git as provider by default for Vcsrepo so we don't have to specify it everytime
Vcsrepo { provider => git } 

##### users & groups section ####

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


##### file structure section #####

file { '/srv/ciin':
	ensure  => 'link',
	target  => '/vagrant/src',
}

file { '/var/www':
	ensure  => 'link',
	target  => '/vagrant/src/web'
}


###### EPEL module - https://forge.puppetlabs.com/stahnma/epel ######

include epel

###### PHP module - https://forge.puppetlabs.com/example42/php #####

class { 'php':
  service => 'nginx',
  service_autorestart => true,
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

ini_setting { "php-setting-displayerrors":
  ensure  => present,
  path    => '/etc/php.ini',
  section => 'PHP',
  setting => 'display_errors',
  value   => 'On',
  require => Class['php'],
  notify  => Service['php-fpm'],
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

service { 'php-fpm' :
	name => 'php-fpm',
	ensure => 'running',
	enable => true,
	require => Package['php'],
	notify => Service['nginx']
}

###### nginx module ######

include nginx


###### Git module - https://forge.puppetlabs.com/puppetlabs/git ######

include git


###### Composer module - https://forge.puppetlabs.com/tPl0ch/composer ######

class { 'composer' :
	download_method => 'wget',
}

#composer::exec { 'project-install':
#    cmd                  => 'install',  # REQUIRED
#    cwd                  => '/vagrant/src', # REQUIRED
    #prefer_source        => false,
    #prefer_dist          => false,
    #dry_run              => false, # Just simulate actions
    #custom_installers    => false, # No custom installers
    #scripts              => false, # No script execution
    #interaction          => false, # No interactive questions
    #optimize             => false, # Optimize autoloader
    #dev                  => true, # Install dev dependencies
#}


###### MySQL module - https://forge.puppetlabs.com/puppetlabs/mysql ######

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

class { '::mysql::server' :
  	root_password   => 'devuser',
  	users           => $users,
  	service_enabled => true,
}


###### phpMyAdmin module - https://forge.puppetlabs.com/leoc/phpmyadmin ######

class { 'phpmyadmin':
	path => "/srv/phpmyadmin",
	user => "root",
	servers => [
		{
			desc => "local",
			host => "localhost",
		},
		{
			desc => "dev",
			host => "EquiDevMySQL",
		}
	],
	require => Package['git']
}