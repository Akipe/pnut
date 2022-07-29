#! /bin/sh -e

#echo 0 > /var/run/nut/upsd.pid && chown root:nut /var/run/nut/upsd.pid
#echo 0 > /var/run/upsmon.pid

/usr/sbin/upsdrvctl -u root start dummy-sim
/usr/sbin/upsdrvctl -u root start dummy-seq
#/usr/sbin/upsdrvctl -u root start
/usr/sbin/upsd -u root -FF
#exec /usr/sbin/upsmon -D
