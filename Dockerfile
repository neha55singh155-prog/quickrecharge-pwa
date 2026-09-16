FROM php:8.2-cli

WORKDIR /app/recharge-app

COPY recharge-app/ .

RUN mkdir -p admin/data uploads && \
    chmod -R 755 admin/data uploads

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
