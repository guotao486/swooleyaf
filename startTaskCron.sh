#!/bin/sh
while [ true ]
do
    /bin/sleep 1
    curl -s -o /dev/null "http://domain_task/Index/Task/handlePersistCronTask"
done