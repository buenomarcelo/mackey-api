FROM nginx:stable-alpine

ARG TZ='America/Sao_Paulo'
ENV DEFAULT_TZ ${TZ}

RUN apk update && apk add bash nano

RUN set -euxo pipefail ;\
    cp /usr/share/zoneinfo/${DEFAULT_TZ} /etc/localtime ;\
    apk del tzdata ;\
    rm -rf /var/cache/apk/* ;\
    rm -rf /root/.cache
