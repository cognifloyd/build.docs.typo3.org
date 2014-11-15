TYPO3 Docs Rendering Hub
========================

This is the Flow distribution for the Docs Rendering Hub located at [build.docs.typo3.org](http://build.docs.typo3.org).
There are several parts of the Rendering Hub:

* The Flow-based application ([git.typo3.org](https://git.typo3.org/Packages/TYPO3.Docs.git))
* The Flow distribution for the application ([github](https://github.com/TYPO3-infrastructure/build.docs.typo3.org) ***<<< You are here!***)
* The Chef cookbook used to setup the server environment ([github](https://github.com/TYPO3-cookbooks/site-builddocstypo3org))

Both the Flow distribution and the Chef cookbook have a virtual machine (VM) setup. This table highlights the differences, and the reasons for having two different VMs:

| Repository    | Purpose of VM      | Deployment Context | How the App gets into the VM  | Includes Flow Distribution? | Also Includes: |
|---------------|--------------------|--------------------|-------------------------------|-----------------------------|----------------|
| Distribution  | App development    | Developer machine  | rsync folders from host to VM | Yes                         | Rabbitmq       |
| Chef Cookbook | Server development | Production server  | Surf deployment               | No                          |                |

Installation
------------

This repository contains a Vagrant setup so that you can run this application on your local machine.

For the first installation, consider one good hour to walk through all the steps which will depend on the speed of your network along with the performance of your machine.
There will about 500 Mb to download which includes a virtual machine and the necessary packages. Please, also make sure to have the following Software installed on your machine. [Chef DK](http://downloads.getchef.com/chef-dk/), [Vagrant](https://www.vagrantup.com/) and [VirtualBox](https://www.virtualbox.org/). In any case, you will be asked later whether your system is functional.

The fist step is to download the source files.

<pre>
  composer install
</pre>

The second one is to create the VM and provision it.

<pre>

  # Prerequisite: VirtualBox must be > 4.3
  # Download from https://www.virtualbox.org/wiki/Downloads
  VirtualBox --help | grep VirtualBox

  # Prerequisite: vagrant must be > 1.5
  # Download from http://www.vagrantup.com/downloads.html
  vagrant --version

  # Prerequisite: chef must be >= 0.3
  # Download from http://downloads.getchef.com/chef-dk
  chef --version

  # Install Vagrant plugin, will be asked anyway later.
  vagrant plugin install vagrant-cachier
  vagrant plugin install vagrant-omnibus
  vagrant plugin install vagrant-berkshelf

  # Fire up the Virtual Machine. This may take some time...
  # At the first spin, il will download an empty VM box
  vagrant up

  # If the provisionning fails (it may happen the first time), just relaunch the process:
  vagrant provision

  # The VM should be accessible at:
  http://build.docs.typo3.dev/

  # When developing, synchronize files as you edit them.
  vagrant rsync-auto

  # Check possible options at the bottom of the Vagrant file and re-provision the VM.
  edit Vagrantfile
  vagrant reload

</pre>

The final step is to enter the VM and trigger the rendering.

<pre>

  # Enter the machine
  vagrant ssh
  sudo su - builddocstypo3org
  export FLOW_CONTEXT=Development/Vagrant
  cd /var/www/vhosts/build.docs.typo3.org/releases/current
  ./flow help

  -> check the commands "document:*" exist

  # Update the Schema
  ./flow doctrine:migrate

  # Prepare 10 documents from Git and 10 from TER
  # Notice argument "git" and "ter" are optional. If omitted, both TER and Git will be assumed
  ./flow document:importall --limit 10 git
  ./flow document:importall --limit 10 ter

  # Process the queue
  # Notice: normally this command should be run in another terminal within a screen
  ./flow job:work git


  # Process the queue
  ./flow queue:start
</pre>

Installation of the software
============================

Vagrant
-------

Vagrant can be downloaded and installed from the website http://www.vagrantup.com/downloads.html

Virtualbox
----------

VirtualBox is a powerful x86 and AMD64/Intel64 virtualization product for enterprise as well as home use.
Follow this download link to install it on your system https://www.virtualbox.org/wiki/Downloads

Configure Vagrant file
----------------------

To adjust configuration open ``Vagrantfile`` file and change settings according to your needs.

<pre>
  # Define IP of the virtual machine to access it from the host
  config.vm.network :private_network, "192.168.188.140"

  # Turn on verbose Chef logging if necessary
  chef.log_level      = :debug
</pre>


Development Software in Vagrant
===============================

Rabbitmq
--------

The vagrant box comes with Rabbitmq. Use these credentials for development:

* host: 127.0.0.1
* port: 5672
* username: devdocs
* password: devdocs
* vhost: infrastructure_dev

[The managmeent interface](http://192.168.188.140:15672) is a convenient way to watch what's going on.
Use the user/pass mentioned above to get into the management interface.

*TODO*: Include an example settings.yaml file.

When the app uses mq.typo3.org, it will use the 'infrastructure' vhost for production, and
'infrastructure_dev' for development. The server team can provide login credentials if required, however,
the credentials on the production server will probably be made available through chef.
That is not setup yet, so, for now, we'll have to manually include the credentials in the settings files.
