# 到處流浪的伺服器

[昨天][Day 13]有提到，概念上是要開發人員每次測試的時候都自己建環境。但相信維運人員幫開發人員建一個專用的伺服器後，開發人員可能會為了 compile `.Sass` 檔，所以裝一個 Ruby + Node ；為了要切換版本，又裝了 rvm 和 nvm ，還裝了 gulp 之類的 global 套件，結果就開不了機了。相信維運人員很難照顧到開發人員求新求變的需求。

「什麼？難道開發人員沒辦法求新求變嗎？」當然可以，正所謂「自己的環境自己管」。環境建在自己電腦上不就好了嗎？

「可是有一堆不一樣的開發環境耶，版本也都不一樣」當然沒問題，現在虛擬化技術已經很成熟了，有很多專案正是做自動化建立客製化的虛擬機，最常見的應該就屬 [Vagrant][] 與 [Docker][] 了，這些技術都有著下列特色：

* **應用隔離:** 可以同時執行多個應用
* **儲存再發佈:** 環境資訊可以以檔案的形式傳輸與重現
* **環境即程式碼:** 代表環境資訊可以跟原始碼一起被記錄進版控系統
* **具備可攜性:** 因有上面兩個特性，環境就可以隨時帶著走

這些特色不但可以讓環境資訊輕鬆共享，也讓開發人員能輕鬆建立與管理虛擬機。

## Vagrant 基本 

Vagrant 安裝需注意它要搭配虛擬化系統，通常是用 [VirtualBox][] 。裝好 VirtualBox 再裝 Vagrant 即可。

Vagrant 操作並不難，首先要先了解它的啟動主要是參考 `Vagrantfile` 這個檔案，只要跟這個檔案同目錄或是子目錄，都會去認這個檔的設定做操作。那以下是一個練習，先建個子目錄，再初始化 Vagrantfile：

```
$ mkdir -p /path/to/project
$ cd /path/to/project
$ vagrant init ubuntu/trusty64
```

這個指令會初始化 Ubuntu Trusty64 的 Vagrantfile 的樣版，裡面有非常多設定的說明可以參考，晚點再來看。先看如何啟動：

```
$ vagrant up

...

==> default: Mounting shared folders...
    default: /vagrant => /Users/miles/GitHub/MilesChou/book-intro-of-ci
```

第一次執行會花比較久時間，它會把虛擬機的映像檔載下來並啟動它。訊息裡會看到它有掛載了一個共用資料夾，表示 `/Users/miles/GitHub/MilesChou/book-intro-of-ci` 的修改，會同步進虛擬機的 `/vagrant` 。代表主機寫的程式可以靠這個方法連結進虛擬機，或是虛擬機做的 compile 結果可以回傳至主機。

另外可以看到它會開一個 ssh port 供連線，接著我們再下連結指令：

```
$ vagrant ssh
Welcome to Ubuntu 14.04.4 LTS (GNU/Linux 3.13.0-92-generic x86_64)

...

vagrant@vagrant-ubuntu-trusty-64:~$ 
```

這樣就進去虛擬機了，因為是 ubuntu 所以可以下 apt 指令安裝想要的開發軟體，如 PHP 等，裝完輸入 exit 即可離開。

接著可能會需要關機：

```
$ vagrant halt
```

系統玩壞了想砍掉重練：

```
$ vagrant destroy
```

如果想重開機或重新載入設定：

```
$ vagrant reload
```

## Example

以下先來個範例：

```ruby
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.synced_folder ".", "/vagrant"
  config.vm.network "private_network", ip: "10.10.10.10"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.provision "shell", path: "install.sh"
end
```

Vagrantfile 是用 Ruby 的語法寫的，會 Ruby 應該不陌生。來看設定的部分在做什麼：

```
config.vm.synced_folder ".", "/vagrant"
```

這個就是預設的同步資料夾設定，左邊是 Host 的 Vagrantfile 相對目錄，右邊是 Guest 的絕對目錄，就算沒有這一行 Vagrant 也會幫忙做。

```
config.vm.network "private_network", ip: "10.10.10.10"
```

這是指主機限定的網路設定，意思就是 `10.10.10.10` 只有開 Vagrant 的主機才連得到。通常用 Vagrant 應該用 `private_network` 居多，畢竟通常主要目的是為了隔離應用

```
config.vm.network "forwarded_port", guest: 80, host: 8080
```

偶爾還是會有需求讓其他人能連線進來，這時就可以開 `forwarded_port` ，上面的設定代表連 Host 8080 port 會轉到 Guest 的 80 port 。因此假設虛擬機裝好 Apache （預設 80 port ），其他人連主機的 8080 就會看到 Apache 的預設首頁。 

```
config.vm.provision "shell", path: "install.sh"
```

最後這就是關鍵了，當 `vagrant up` 的時候，預設會啟動 provision ，也就是預置環境。預置的方法有很多，通常我使用的是單純的 shell script ，如同範例這樣。 `install.sh` 預設會在虛擬機裡以 root 權限執行，範例如下：

```bash
#!/bin/bash
########################################
#                                      #
#  Install packages for Ubuntu 14.04   #
#                                      #
########################################

# Setup Timezone
TIMEZONE="Asia/Taipei"

# Setup packages and version
PACKAGES_LIST="
build-essential
curl
git
mariadb-server
nginx
php-pear
php5-cli
php5-curl
php5-dev
php5-fpm
php5-mysqlnd
php5-xdebug
vim
"

PACKAGES=""
for package in $PACKAGES_LIST
do
    PACKAGES="$PACKAGES $package"
done

DEFAULT_SITE="
server {
    listen 80;
    root /vagrant/public;
    index index.html index.htm index.php;
    server_name localhost;
    location / {
        try_files \$uri \$uri/ =404 /index.php\$is_args\$args;
        expires off;
    }
    location ~ \.php\$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_read_timeout $TIMEOUT;
        fastcgi_index index.php;
        include fastcgi_params;
    }
    location ~ /\.ht {
        deny all;
    }
}
"

# is root?
if [ "`whoami`" != "root" ]; then
    echo "You may use root permission!"
    exit 1
fi

# set time zone
ln -sf /usr/share/zoneinfo/$TIMEZONE /etc/localtime

# update time
ntpdate time.stdtime.gov.tw

# update server
apt-get update
apt-get install python-software-properties

# Add PHP 5.6 PPA
add-apt-repository -y ppa:ondrej/php5-5.6
apt-get update

# set default root password
export DEBIAN_FRONTEND=noninteractive
debconf-set-selections <<< 'mariadb-server-5.5 mysql-server/root_password password password'
debconf-set-selections <<< 'mariadb-server-5.5 mysql-server/root_password_again password password'

# install packages
apt-get install -y $PACKAGES $EXTENSIONS

# set MySQL password and domain
mysql -uroot -ppassword -e 'USE mysql; UPDATE `user` SET `Host`="%" WHERE `User`="root" AND `Host`="localhost"; DELETE FROM `user` WHERE `Host` != "%" AND `User`="root"; FLUSH PRIVILEGES;'
mysql -uroot -ppassword -e 'CREATE DATABASE `default` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;'
mysql -uroot -ppassword -e 'CREATE DATABASE `testing` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;'
# mysql -uroot -ppassword -e "SET PASSWORD = PASSWORD('');"

# modified mysql config
sed -i 's/127\.0\.0\.1/0\.0\.0\.0/g' /etc/mysql/my.cnf

# modified php5-fpm conf
sed -i 's/^listen =.*/listen = \/var\/run\/php5-fpm\.sock/g' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^;listen.owner =/listen.owner =/g' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^;listen.group =/listen.group =/g' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^;listen.mode =/listen.mode =/g' /etc/php5/fpm/pool.d/www.conf
sed -i 's/^;clear_env =/clear_env =/g' /etc/php5/fpm/pool.d/www.conf

# modified php.ini
sed -i "s/^;date.timezone =.*/date.timezone = $TIMEZONE/g" /etc/php5/fpm/php.ini

# install composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# modified php.ini
sed -i "s/^error_reporting =.*/error_reporting = E_ALL \| E_STRICT/g" /etc/php5/fpm/php.ini
sed -i "s/^display_errors =.*/display_errors = On/g" /etc/php5/fpm/php.ini
sed -i "s/^display_startup_errors =.*/display_startup_errors = On/g" /etc/php5/fpm/php.ini

# modified nginx default site
echo "$DEFAULT_SITE" > "/etc/nginx/sites-available/default"

# restart services
service php5-fpm restart
service nginx restart
service mysql restart
```

## 今日回顧

有了自動化建置的工具，開發者就不需要再害怕把伺服器搞壞了，甚至是可以做一些進階的測試。不管怎麼看，對開發人員或維運人員都是有益無害的。

下一篇：[管理貨櫃的碼頭工人－－ Docker （ 1/3 ）][]

## 相關連結

* [04. 怎麼用 Vagrant 練習 Ansible？](http://ithelp.ithome.com.tw/articles/10185003) | 現代 IT 人一定要知道的 Ansible 自動化組態技巧系列 第 4 天
* [[Day 02] Vagrant 介紹](http://ithelp.ithome.com.tw/articles/10184824) | 30 天入門 Ansible 及 Jenkins-CI
* [[Day 03] Vagrant 基本設定](http://ithelp.ithome.com.tw/articles/10184915) | 30 天入門 Ansible 及 Jenkins-CI

[VirtualBox]: https://www.virtualbox.org/
[Vagrant]: https://www.vagrantup.com/
[Docker]: https://www.docker.com/

[Day 13]: /docs/day13.md
[管理貨櫃的碼頭工人－－ Docker （ 1/3 ）]: /docs/day15.md
