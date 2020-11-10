#!/bin/bash

if [ $1 = 'up' ]; then
	#delete file that causes premission problems
	rm -rf www/auction;
	docker-compose up -d;
elif [ $1 = 'down' ]; then
	docker-compose down;
else 
	echo "Usage: app.sh (up|down)"
fi
