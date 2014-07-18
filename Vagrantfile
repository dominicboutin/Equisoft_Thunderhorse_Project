VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "hfm4/centos7"

  config.vm.network "forwarded_port", guest: 80, host: 8081, auto_correct: true

  config.vm.usable_port_range = (10200..10500)

  config.ssh.username = 'vagrant'

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "provisions/puppet"
    puppet.manifest_file = "manifest.pp"
    puppet.module_path = "provisions/puppet/modules"
  end

  config.vm.provision :shell do |s|
    s.path = "provisions/shell/flush-iptables.sh"
  end

end
