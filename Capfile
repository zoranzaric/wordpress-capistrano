load 'deploy' if respond_to?(:namespace) # cap2 differentiator
Dir['vendor/plugins/*/recipes/*.rb'].each { |plugin| load(plugin) }
#Added for railsless deploy
require 'rubygems'
require 'railsless-deploy'
load    'config/deploy'
