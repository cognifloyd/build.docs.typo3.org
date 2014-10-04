Overview
========

This is the Flow distribution for the Docs Rendering Hub (build.docs.typo3.org) see git.typo3.org/Packages/TYO3.Docs.git for the Flow app.


Vagrant setup
=============

This repository contains a Vagrant setup so that you can run this application on your local machine.

For the first installation, consider one good hour to walk through all the steps which will depend on the speed of your network along with the performance of your machine.
There will about 500 Mb to download which includes a virtual machine and the necessary packages.

The fist step is to download the source files.

<pre>
  composer install
</pre>

The second step is to create the VM and provision it.

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

	# Fire up the Virtual Machine... this may take some time as it will download an empty VM box
	vagrant up

	# Configure the Vagrantfile
	# Check possible options at the bottom of the file and re-provision the VM.
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
