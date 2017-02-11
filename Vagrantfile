VAGRANTFILE_API_VERSION = "2"

box = "ubuntu/trusty64"
ip = "192.168.11.98"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = box

  config.vm.network "private_network", ip: ip

  config.vm.synced_folder ".", "/vagrant_data"

  config.vm.provision "shell" do |s|
    s.path = "vagrant/provision.sh"
    s.args = ip
  end
end