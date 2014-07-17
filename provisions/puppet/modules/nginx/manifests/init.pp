class nginx {

	# Add nginx yum repo
	yumrepo { "nginx":
		baseurl => 'http://nginx.org/packages/mainline/centos/7/$basearch/',
		descr => "nginx repo",
		enabled => 1,
		gpgcheck => 0
	}

	# Install the nginx package. This relies on apt-get update
	package { 'nginx':
		ensure => 'present',
		require => Yumrepo['nginx'],
	}

	# Make sure that the nginx service is running
	service { 'nginx':
		ensure => running,
		require => Package['nginx'],
	}

	# Add vhost template
	file { 'vagrant-nginx':
	    path => '/etc/nginx/conf.d/ciin.conf',
	    ensure => file,
	    require => Package['nginx'],
	    source => 'puppet:///modules/nginx/ciin.conf',
	}

	# Disable default nginx vhost
	file { 'default-nginx-disable':
	    path => '/etc/nginx/conf.d/default.conf',
	    ensure => absent,
	    require => Package['nginx'],
	}
}