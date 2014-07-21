file { '/var/www':
	ensure  => 'link',
	target  => '/vagrant/src',
}


# PHP module

include php


# nginx module

include nginx


# Git module - https://forge.puppetlabs.com/puppetlabs/git

include git


# Composer module - https://forge.puppetlabs.com/tPl0ch/composer

class { 'composer' :
	download_method => 'wget',
}


# MySQL module - https://forge.puppetlabs.com/puppetlabs/mysql

class { '::mysql::server' :
  	root_password   => 'devuser',
}


# phpMyAdmin module - https://forge.puppetlabs.com/leoc/phpmyadmin

class { 'phpmyadmin':
	path => "/srv/phpmyadmin",
	user => "root",
	servers => [
		{
			desc => "local",
			host => "127.0.0.1",
		}
	],
	require => Package['git']
}

file { 'phpmyadmin-nginx' :
    path => '/etc/nginx/conf.d/phpmyadmin.conf',
    ensure => file,
    source => '/vagrant/provisions/files/phpmyadmin.conf',
    require => Package['nginx'],
    notify	=> Service['nginx'],
}