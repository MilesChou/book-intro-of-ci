VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.synced_folder ".", "/vagrant"
  config.vm.network "private_network", ip: "10.10.10.10"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.provision "shell", path: "install.sh"
end
