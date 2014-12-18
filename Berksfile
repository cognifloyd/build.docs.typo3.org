source "https://supermarket.getchef.com"

cookbook 'site-builddocstypo3org', github: 'TYPO3-Cookbooks/site-builddocstypo3org'
cookbook 'php', github: 'TYPO3-Cookbooks/php'

# rabbitmq is not needed in production. However, the development vagrant box does need it (recipes/dev_vagrant.rb).
# 2.3.2 is the same version as the site-mqtypo3org cookbook. Please keep them in sync.
cookbook 'rabbitmq', '~> 2.3.2'
