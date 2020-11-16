#!/bin/bash

if [ $1 = 'up' ]; then
	rm -rf www/auction;
	docker-compose up -d;
elif [ $1 = 'down' ]; then
	docker-compose down;
else 
	echo "Usage: app.sh (up|down)"
fi
