FROM ubuntu:latest

MAINTAINER Gevorg Melkumyan <gmelkumyan1995@gmail.com>

USER root

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y curl \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" \
    && nvm install node
