set :application, "tpb proxy"
set :deploy_to, "/var/www/"
default_run_options[:pty] = true

set :scm, :git
set :repository,  "$PWD"
set :deploy_via, :copy
set :branch, "master"
set :keep_releases, 3

server "127.0.0.1:2222", :app, :web, :db, :primary => true
set :ssh_options, {:forward_agent => true, :port => 22}
set :user, "root"
set :use_sudo, true
logger.level = Logger::MAX_LEVEL

namespace :project do

    task :vendors do
        run "curl -s http://getcomposer.org/installer | php -- --install-dir=#{release_path}"
        run "cd #{release_path} && #{release_path}/composer.phar install"
    end
end

after "deploy:update_code", "project:vendors"
