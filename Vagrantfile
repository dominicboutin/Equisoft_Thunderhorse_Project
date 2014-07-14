VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "hfm4/centos7"

  config.vm.network "private_network", ip: "192.168.56.101"
  config.vm.network "forwarded_port", guest: 80, host: 8081, auto_correct: true

  config.vm.usable_port_range = (10200..10500)

  config.ssh.username = 'vagrant'

end
