VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "puppetlabs/centos-6.5-64-puppet"

  config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true

  config.vm.usable_port_range = (8080..8999)

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

  config.vm.provision :shell do |s|
    s.path = "provisions/shell/bootstrap-post.sh"
    s.privileged = true
  end

end
