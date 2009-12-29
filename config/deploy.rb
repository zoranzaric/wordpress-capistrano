set :application, "Client Blog One"
#You probably want to change this to be the location of the repo you just forked
set :repository,  "git@github.com:vluther/wordpress-capistrano.git"


#The following is not the document root, but just the app root 
set :deploy_to, "/home/demouser/websites/clientblogone/"



#The unix/ftp user 
set :user, "demouser"


role :app, "clients.example.com"
role :web, "clients.example.com"
role :db,  "clients.example.com", :primary => true

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


