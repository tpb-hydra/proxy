# Hydra Proxy

---

TPB Hydra proxy. An Nginx, PHP-FPM and Varnish web proxy for The Pirate Bay (and with a few tweaks for anything you want to proxy).

All aboard the good ship: Fuck The Lawyers!

## Setup

- Get the Vagrant VM setup by cloning the repo:

```bash

git clone https://github.com/tpb-hydra/vm
cd vm && vagrant init && vagrant up
````

This provides a Debian Squeeze (32 bits) VM to run the proxy on. You may deploy it somewhere else if you want to.

- Clone the proxy application:

```bash

git clone https://github.com/tpb-hydra/proxy
cd proxy && composer install
cap deploy:setup && cap deploy
````

Be sure to have installed and runnig the Vagrant VM (or configure the deploy at ``config/deploy.rb``).

## Stack
* [Vagrant][4]
* [Puppet][5]
* [Nginx][6]
* [Varnish][7]
* [PHP FPM][8]
* [Silex][9]
* [Composer][10]
* [Capistrano][11]

## Contact and Feedback

If you'd like to contribute to the project or file a bug or feature request, please visit [the project page][1].

## License

The project is licensed under the [GNU GPL v3][2] ([tldr][3]) license. Which means you're allowed to copy, edit, change, hack, use all or any part of this project *as long* as all of the changes and contributions remains under the same terms and conditions.

  [1]: https://github.com/tpb-hydra/proxy
  [2]: http://www.gnu.org/licenses/gpl.html
  [3]: http://www.tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
  [4]: http://www.vagrantup.com/
  [5]: https://puppetlabs.com/
  [6]: http://nginx.com/
  [7]: https://www.varnish-cache.org/
  [8]: http://php-fpm.org/
  [9]: http://silex.sensiolabs.org
  [10]: http://getcomposer.org/
  [11]: http://www.capistranorb.com/

