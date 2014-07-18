class php {

	package { ['php-fpm', 'php-xml']:
	  ensure => present,
	}

	exec { 'php-fpm-set-timezone':
        path => '/usr/bin:/usr/sbin:/bin',
        command => 'sed -i \'s/^[; ]*date.timezone =.*/date.timezone = America\/New_York/g\' /etc/php.ini',
        onlyif => 'test "`php -c /etc/php.ini -r \"echo ini_get(\'date.timezone\');\"`" = ""',
        require => Package['php-fpm'],
        notify => Service['php-fpm']
    }

	file { 'php-fpm-set-session-dir':
		path => '/var/lib/php/session',
		owner => 'apache',
		ensure => 'directory',
        require => Package['php-fpm'],
    }

	service { 'php-fpm':
	  ensure => running,
	  require => Package['php-fpm', 'php-xml'],
      notify => Service['nginx']
	}
}