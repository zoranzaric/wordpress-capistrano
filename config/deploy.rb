set :application, "Client Blog One"
#You probably want to change this to be the location of the repo you just forked
set :repository,  "git@github.com:vluther/wordpress-capistrano.git"


#The following is not the document root, but just the app root 
set :deploy_to, "/home/demouser/websites/clientblogone/"


#The unix/ftp user 
set :user, "demouser"

#role :app, "clients.example.com"
#role :web, "clients.example.com"
#role :db,  "clients.example.com", :primary => true

#########################
#things you'll probably not change, unless you know what you're doing 
###########################
# If you aren't using Subversion to manage your source code, specify
# your SCM below:
# set :scm, :subversion
set :scm, :git

#the following is needed because if it's not there, for some reason we don't get
#asked to accept the key from github..annoying when deploying to a new server
default_run_options[:pty] = true

#since this is PHP, we don't really need to restart apache or anything
set :use_sudo, false

#ssh agent forwarding..
ssh_options[:forward_agent] = true

#A lot of this stuff has been overridden for PHP/Non Rails magic

namespace :deploy do
  desc <<-DESC
    This gets the latest code from your git repository, and puts it in the right document root
    sets the proper permissions for wp-content/cache (in case you activated super-cache) and other
    things
  DESC
  task :default do
    update
    finalized_update
  end

  desc <<-DESC
    [internal] Touches up the released code. This is called by update_code \
    after the basic deploy finishes. It assumes a Rails project was deployed, \
    so if you are deploying something else, you may want to override this \
    task with your own environment's requirements.

    This task will make the release group-writable (if the :group_writable \
    variable is set to true, which is the default). It will then set up \
    symlinks to the shared directory for the log and cache.
  DESC
  task :finalize_update, :except => { :no_release => true } do
    run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
    run "mkdir #{latest_release}/htdocs/wp-content/cache"
    run "ln -s #{shared_path}/wp-content/uploads #{latest_release}/htdocs/wp-content/uploads"
    run "chmod -R 755 #{latest_release}/htdocs/wp-content"
  end
end


