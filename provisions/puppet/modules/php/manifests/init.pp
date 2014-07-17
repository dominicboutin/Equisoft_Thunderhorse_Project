class php {

	package { 'php-fpm':
	  ensure => present,
	}

	service { 'php-fpm':
	  ensure => running,
	  require => Package['php-fpm'],
	}
}