FROM alpine:3.16

LABEL \
    maintainer="code.julien@milletre.fr" \
    org.label-schema.build-date=$BUILD_DATE \
    org.label-schema.license=GPL-2.0 \
    org.label-schema.name=nut-upsd \
    org.label-schema.vcs-ref=$VCS_REF \
    org.label-schema.vcs-url=https://github.com/instantlinux/docker-tools

HEALTHCHECK CMD upsc ups@localhost:3493 2>&1|grep -q stale && exit 1 || true

RUN apk add --update \
    nut \
    libcrypto1.1 \
    libssl1.1 \
    net-snmp-libs

EXPOSE 3493

ADD ups.conf /etc/nut/
ADD upsd.conf /etc/nut/
ADD epdu-managed.dev /etc/nut
ADD evolution500.seq /etc/nut

RUN mkdir /var/run/nut
RUN touch /var/run/nut/upsd.pid

RUN chown -R root:nut /var/run/nut
RUN chmod -R 777 /var/run/nut
RUN chown -R root:nut /etc/nut


COPY entrypoint.sh /usr/local/bin/
RUN chmod -R 777 /usr/local/bin/entrypoint.sh

ENTRYPOINT /usr/local/bin/entrypoint.sh
