# GeoIP Sypex Service

Сервис геокодинга на базе библиотеки SypexGeo. HTTP сервер реализован через ReactPHP и поэтому ниебически быстр.

## Запуск в docker

Убедитесь, что у вас уже установлен docker и учётная запись пользователя имеет необходимые привелегии.
В случае, если у вас не установлен docker, вы можете найти информацию по установке [тут](https://docs.docker.com/engine/installation/).

Прежде всего, необходимо создать образ. Сделать это можно с помощью команды:

    docker build -t madiedinro/geoip-sypex

При создании контейнера в него будет загружена последняя версия базы геокодинга.
Для того, чтобы принимать запросы с внешних адресов, укажите `-p [<host>:]<port>:<port>`.
Рассмотрим пример запуска на 0.0.0.0:8087

    docker run -d \
         --name geoip-sypex \
         --hostname=geoip-sypex \
         --restart=always \
         -p 8087:8080 \
         madiedinro/geoip-sypex


## Стандартный запуск 

Если у вас нет composer, его нужно установить [отсюда](https://getcomposer.org/download/)

Установите зависимости
 
    composer install
    
или
    
    php composer.phar install

Загрузите последнюю версию базы геокодинга 
    
    ./update_db

Сервис готов к работе
 
    php server.php --host=0.0.0.0 --port=8087

### Обновление данных

Как и при установке базы выполняется скриптом `./update_db`

## Выполнение запросов

Попробуйте выполнить

    curl http://127.0.0.1:8087/query?ip=92.242.39.60

Вы получите следующий результат:

    {
      "city": {
        "id": 524901,
        "lat": 55.75222,
        "lon": 37.61556,
        "name_ru": "Москва",
        "name_en": "Moscow"
      },
      "region": {
        "id": 524894,
        "name_ru": "Москва",
        "name_en": "Moskva",
        "iso": "RU-MOW"
      },
      "country": {
        "id": 185,
        "iso": "RU",
        "lat": 60,
        "lon": 100,
        "name_ru": "Россия",
        "name_en": "Russia"
      },
      "execution": "0.010790109634",
      "success": true
    }


## Проверка статуса

Работоспособность сервиса проверяется по адресу `/status`. Пример:
     
    curl http://127.0.0.1:8087/?status


## Лицензия
The MIT License (MIT)

Copyright (c) 2017 Dmitry Rodin

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
