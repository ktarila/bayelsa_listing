#!/bin/bash
sudo rm -r var/cache/*
sudo rm -r var/log/*

HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | sed 's/ .*$//'`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log  public/images/uploads
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log  public/images/uploads
touch public/images/uploads/.gitkeep
# symfony linux directory permissions
