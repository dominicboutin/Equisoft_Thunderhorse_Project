VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "hfm4/centos7"
  config.vm.box_version = "1.1"

  config.vm.network "forwarded_port", guest: 80, host: 8081, auto_correct: true
  config.vm.network "forwarded_port", guest: 8080, host: 8082, auto_correct: true
  config.vm.network "forwarded_port", guest: 8081, host: 8083, auto_correct: true

  config.vm.usable_port_range = (10200..10500)

  config.ssh.username = 'vagrant'

  config.vm.provider "virtualbox" do |v|
    v.name = "CIIN"  
    #v.memory = 1024
    #v.cpus = 2
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "provisions/puppet"
    puppet.manifest_file = "manifest.pp"
    puppet.module_path = "provisions/puppet/modules"
  end

  config.vm.provision :shell do |s|
    s.path = "provisions/shell/flush-iptables.sh"
    s.privileged = true
  end

end
