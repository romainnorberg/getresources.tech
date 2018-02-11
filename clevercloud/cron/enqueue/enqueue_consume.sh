#!/usr/bin/env bash

# Run from env CC_POST_BUILD_HOOK=sh clevercloud/hook/run_succeeded.sh
# doc: https://www.clever-cloud.com/doc/clever-cloud-overview/hooks/

# source environment variables
# https://www.clever-cloud.com/doc/admin-console/environment-variables/
# APP_ID : the ID of the application. Each application has a unique identifier used to identify it in our system. This ID is the same than the one you can find in the information section of your application.
#
# INSTANCE_ID : the ID of the current instance of your application. It's unique for each instance of your application and change every time you deploy it.
#
# INSTANCE_TYPE : The type of instance. Values can be build or production. build is when your application is being built on a dedicated instance.
#
# COMMIT_ID : the commit ID used as a base to deploy your application. As we remove the .git directory before the deployment (to avoid security problems), it can be used to know which version of your application is running on the server.
#
# APP_HOME : The absolute path of your application on the server. Can be used to create absolute link in your application (ex : ${APP_HOME}/foo/bar).
#
# INSTANCE_NUMBER : See below
#
#
source /home/bas/applicationrc

# run only on first production instance
if [ -n ${INSTANCE_TYPE} ] && [ ${INSTANCE_TYPE} = 'production' ]  && [ -n ${INSTANCE_NUMBER} ] && [ ${INSTANCE_NUMBER} -eq 0 ]
then
  status=`ps -efww | grep -w "[e]nqueue:consume" | awk -vpid=$$ '$2 != pid { print $2 }'`
  if [ ! -z "$status" ]; then
      echo "[`date`] : enqueue:consume : Process is already running"
      exit 1;
  fi

  # Log path
  enqueue_log_path="${APP_HOME}/clevercloud/buckets/logs/server/enqueue_${INSTANCE_ID}.log"

  # Running
  echo "running enqueue:consume... (log into ${enqueue_log_path})"
  /usr/bin/php ${APP_HOME}/bin/console enqueue:consume -vv >> ${enqueue_log_path} &
fi

echo "====="
