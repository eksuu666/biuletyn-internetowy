#!/bin/sh
# Fresh Debian 12/13 VM only. Run as root. No cloud billing changes.
set -eu
export DEBIAN_FRONTEND=noninteractive
if [ ! -e /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    printf '/swapfile none swap sw 0 0\n' >> /etc/fstab
fi
mkdir -p /etc/docker /etc/systemd/journald.conf.d
printf '[Journal]\nSystemMaxUse=100M\n' > /etc/systemd/journald.conf.d/slodziak.conf
systemctl restart systemd-journald
printf '{"log-driver":"local","log-opts":{"max-size":"10m","max-file":"3"}}\n' > /etc/docker/daemon.json
apt-get update -qq
apt-get install -y -qq ca-certificates curl
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc
. /etc/os-release
printf 'deb [arch=%s signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian %s stable\n' "$(dpkg --print-architecture)" "$VERSION_CODENAME" > /etc/apt/sources.list.d/docker.list
apt-get update -qq
apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-compose-plugin
systemctl enable --now docker
install -d -m 0700 /opt/slodziak
echo 'sLodziak VM provisioning complete'
