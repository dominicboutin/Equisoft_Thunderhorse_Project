# Symlink guest /var/www/app to host /vagrant
file { '/var/www':
	ensure  => 'link',
	target  => '/vagrant/src',
}

include php, nginx

# Git module - https://forge.puppetlabs.com/puppetlabs/git

include git

# MySQL module - https://forge.puppetlabs.com/puppetlabs/mysql

#class { '::mysql::server' :
#	server_package_name => 'mysql-community-server'
#}

# phpMyAdmin module - https://forge.puppetlabs.com/leoc/phpmyadmin

#class { 'phpmyadmin':
#  path => "/srv/phpmyadmin",
#  user => "apache",
#  servers => [
#    {
#      desc => "local",
#      host => "127.0.0.1",
#    }
#  ],
#  require => Package['git']
#}