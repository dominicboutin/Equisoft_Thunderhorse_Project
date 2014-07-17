# Symlink guest /var/www/app to host /vagrant
	file { '/var/www':
	  ensure  => 'link',
	  target  => '/vagrant/src',
	}

include nginx, php