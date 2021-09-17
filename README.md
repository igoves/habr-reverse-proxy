# Habr Reverse Proxy


## Overview
Test task for Ivelum. Look at [requirements](https://github.com/utz0r2/habr-reverse-proxy/requirements.md).

___

## Installing and start

To install [Git](http://git-scm.com/book/en/v2/Getting-Started-Installing-Git), download it and install following the instructions :

```sh
git clone https://github.com/utz0r2/habr-reverse-proxy.git
```

Go to the project directory :

```sh
cd habr-reverse-proxy
```

Start the application :

```sh
docker-compose up
```

Open your favorite browser :

* [http://127.0.0.1:8232](http://127.0.0.1:8232/)

Stop and clear services

```sh
docker-compose down -v
```

___

## Use Docker commands

### Testing PHP application with PHPUnit

```sh
docker-compose exec -T php-apache ./vendor/bin/phpunit
```

___

## About me
Hello, my name is Igor Veselov. I am Senior Full Stack Engineer. Main specialization - Ecommerce websites. Opened for interesting offers.

___

## Contacts
- EMAIL: [dev@xfor.top](mailto:dev@xfor.top)
- WWW: [https://xfor.top/](https://xfor.top/)
- LinkedIn: [Link](https://www.linkedin.com/in/igor-veselov/)
- GitHub: [Link](https://github.com/utz0r2)




