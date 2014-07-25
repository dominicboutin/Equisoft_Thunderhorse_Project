Package {  allow_virtual => false, }

Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }

Vcsrepo { provider => git } 

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

php::module { "fpm": }
php::module { "xml": }
php::module { "mysql": }
php::module { "mbstring": }
php::pecl::module { "pecl-zendopcache": 
	require => Yumrepo['epel']
}

service { 'php-run' :
	name => 'php-fpm',
	ensure => 'running',
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

#$users = {
#  'devuser@localhost' => {
#    ensure                   => 'present',
#    max_connections_per_hour => '0',
#    max_queries_per_hour     => '0',
#    max_updates_per_hour     => '0',
#    max_user_connections     => '0',
#    password_hash            => '*F3A2A51A9B0F2BE2468926B4132313728C250DBF',
#  },
#}

class { '::mysql::server' :
  	root_password   => 'devuser',
  	#users           => $users,
}


###### phpMyAdmin module - https://forge.puppetlabs.com/leoc/phpmyadmin ######

class { 'phpmyadmin':
	path => "/srv/phpmyadmin",
	user => "root",
	servers => [
		{
			desc => "local",
			host => "127.0.0.1",
		},
		{
			desc => "dev",
			host => "EquiDevMySQL",
		}
	],
	require => Package['git']
}