set :stages, %w(staging production)
require 'capistrano/ext/multistage'
load 'deploy' if respond_to?(:namespace) # cap2 differentiator
Dir['vendor/plugins/*/recipes/*.rb'].each { |plugin| load(plugin) }
#Added for railsless deploy
require 'rubygems'
require 'railsless-deploy'
load    'config/deploy'


set :default_stage, "staging"


task :identify do
  run "cat /etc/issue"
end

