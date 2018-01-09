#!/usr/bin/env bash

echo 'export ALGOLIA_API_KEY=${ALGOLIA_API_KEY_MASTER}' >> /home/bas/applicationrc
echo 'export ALGOLIA_APP_ID=${ALGOLIA_API_APPLICATION_ID}' >> /home/bas/applicationrc
echo 'export DATABASE_DB=${MYSQL_ADDON_DB}' >> /home/bas/applicationrc
echo 'export DATABASE_HOST=${MYSQL_ADDON_HOST}' >> /home/bas/applicationrc
echo 'export DATABASE_PASSWORD=${MYSQL_ADDON_PASSWORD}' >> /home/bas/applicationrc
echo 'export DATABASE_PORT=${MYSQL_ADDON_PORT}' >> /home/bas/applicationrc
echo 'export DATABASE_URL=${MYSQL_ADDON_URI}' >> /home/bas/applicationrc
echo 'export DATABASE_USER=${MYSQL_ADDON_USER}' >> /home/bas/applicationrc

source /home/bas/applicationrc