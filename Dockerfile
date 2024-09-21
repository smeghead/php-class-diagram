FROM plantuml/plantuml

RUN apt-get update && apt-get install -y php8.1-cli php-xml php-mbstring php-xdebug fonts-noto-cjk git zip && apt-get clean && rm -rf /var/lib/apt/lists/*
RUN echo '#!/bin/sh' > /usr/bin/plantuml && echo 'java -jar /opt/plantuml.jar "$@"' >> /usr/bin/plantuml && chmod +x /usr/bin/plantuml

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENTRYPOINT []
