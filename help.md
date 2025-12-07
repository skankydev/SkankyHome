
# 🏠 SkankyHome

> Serveur domotique sur Raspberry Pi 4 pour contrôle de LEDs ESP32 via MQTT

## 📋 Stack Technique

- **OS** : Raspberry Pi OS 64-bit (Bookworm)
- **Web Server** : Apache 2.4
- **PHP** : 8.4.11
- **Framework** : SkankyDev (PHP custom framework)
- **Database** : MongoDB 7.0.14
- **MQTT Broker** : EMQX 5.8.3
- **Package Manager** : Composer 2.8.8

## 🚀 Installation

### Prérequis

```bash
# Update système
sudo apt update && sudo apt upgrade -y
```

### Apache + PHP

```bash
# Installation
sudo apt install apache2 php libapache2-mod-php php-cli php-mbstring php-xml php-curl -y

# Active mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### MongoDB 7.0.14

```bash
# Download binaries (Pi 4 ARM64)
cd /tmp
wget https://github.com/themattman/mongodb-raspberrypi-binaries/releases/download/r7.0.14-rpi-unofficial/mongodb.ce.pi4.r7.0.14.tar.gz
tar -zxvf mongodb.ce.pi4.r7.0.14.tar.gz

# Installation
sudo cp mongod mongo mongos /usr/local/bin/
sudo chmod +x /usr/local/bin/mongo*

# Création utilisateur et dossiers
sudo useradd mongodb -r -s /bin/false
sudo mkdir -p /var/lib/mongodb /var/log/mongodb
sudo chown -R mongodb:mongodb /var/lib/mongodb /var/log/mongodb

# Configuration
sudo nano /etc/mongod.conf
```

**Fichier `/etc/mongod.conf` :**

```yaml
storage:
  dbPath: /var/lib/mongodb

systemLog:
  destination: file
  path: /var/log/mongodb/mongod.log
  logAppend: true

net:
  bindIp: 127.0.0.1
  port: 27017
```

**Service systemd** (`/etc/systemd/system/mongod.service`) :

```ini
[Unit]
Description=MongoDB Database Server
After=network.target

[Service]
User=mongodb
Group=mongodb
ExecStart=/usr/local/bin/mongod --config /etc/mongod.conf
Restart=on-failure

[Install]
WantedBy=multi-user.target
```

**Démarrage :**

```bash
sudo systemctl daemon-reload
sudo systemctl start mongod
sudo systemctl enable mongod
```

**Fix libssl 1.1 (si nécessaire) :**

```bash
wget http://ftp.debian.org/debian/pool/main/o/openssl/libssl1.1_1.1.1w-0+deb11u1_arm64.deb
sudo dpkg -i libssl1.1_1.1.1w-0+deb11u1_arm64.deb
```

### Extension PHP MongoDB

```bash
# Dépendances
sudo apt install -y php-dev php-pear build-essential libssl-dev libbson-dev libmongoc-dev

# Installation
sudo pecl install mongodb

# Activation
echo "extension=mongodb.so" | sudo tee /etc/php/8.4/mods-available/mongodb.ini
sudo phpenmod mongodb
sudo systemctl restart apache2

# Vérification
php -m | grep mongodb
```

### EMQX (MQTT Broker)

```bash
cd /tmp
wget https://www.emqx.com/en/downloads/broker/5.8.3/emqx-5.8.3-debian12-arm64.deb
sudo apt install ./emqx-5.8.3-debian12-arm64.deb

# Démarrage
sudo systemctl start emqx
sudo systemctl enable emqx
```

**Ports EMQX :**
- MQTT : `1883`
- WebSocket : `8083`  
- Dashboard : `18083` (admin/public)

**Test MQTT :**

```bash
# Installer les clients mosquitto
sudo apt install mosquitto-clients -y

# Subscribe
mosquitto_sub -h localhost -p 1883 -t "test/#"

# Publish
mosquitto_pub -h localhost -p 1883 -t "test/led" -m "Hello ESP32!"
```

### Composer

```bash
sudo apt install composer -y
```

## 🔧 Configuration du Projet

### Clone du repo

```bash
cd /home/skankydev/www/SkankyHome
git init
git remote add origin https://github.com/skankydev/SkankyHome.git
git pull origin main
```

### Installation des dépendances

```bash
composer install
```

### Permissions

```bash
# Fichiers en 664
find . -type f -exec chmod 664 {} \;

# Dossiers en 775
find . -type d -exec chmod 775 {} \;

# Propriétaire www-data
sudo chown -R www-data:www-data .
```

### Configuration Apache

**VirtualHost** (`/etc/apache2/sites-available/000-default.conf`) :

```apache
DocumentRoot /home/skankydev/www/SkankyHome/public

<Directory /home/skankydev/www/SkankyHome/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

**Permissions utilisateur :**

```bash
sudo usermod -a -G skankydev www-data
chmod 755 /home/skankydev
chmod -R 755 /home/skankydev/www
sudo systemctl restart apache2
```

## 🎯 Projet LED

Contrôle de bandes LED WS2812B via ESP32.

### Matériel

- **ESP32** (modules WiFi/BLE)
- **LED WS2812B** (bandes RGB adressables)
- **Alimentation** : 5V adaptée au nombre de LEDs

### Communication

- **Protocole** : MQTT over WiFi
- **Broker** : EMQX sur Raspberry Pi 4
- **Contrôle** : Interface web SkankyDev

## 🔐 Sécurité

- MongoDB : `bindIp: 127.0.0.1` (accès local uniquement)
- EMQX : Firewall recommandé pour exposition externe
- Apache : AllowOverride pour .htaccess

## 📝 Logs

- **Apache** : `/var/log/apache2/`
- **MongoDB** : `/var/log/mongodb/mongod.log`
- **EMQX** : `/var/log/emqx/`

## 👨‍💻 Développement

**Auteur** : SkankyDev  
**Framework** : SkankyDev (custom PHP framework)  
**Repo** : [github.com/skankydev/SkankyHome](https://github.com/skankydev/SkankyHome)

## 📄 Licence

MIT

---

**Hostname** : `skankyhome`  
**User** : `skankydev`  
**Location** : Perpignan, France 🇫🇷
