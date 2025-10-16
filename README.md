# Тестовое задание на Laravel

## Описание задачи
Laravel (Docker, Laravel echo, redis, mariadb)
- Развернуть laravel в docker с установкой laravel cron и сервером очередей rabbitmq
- Реализовать контроллер с валидацией и загрузкой excel файла
- Загруженный файл через jobs поэтапно (по 1000 строк) парсить в бд (таблица rows)
- Прогресс парсинга файла хранить в redis (уникальный ключ + количество обработанных строк)

Поля excel:
```
id
name
date (d.m.Y)
```
- Для парсинга excel можете использовать maatwebsite/excel
- Реализовать контроллер для вывода данных (rows) с группировкой по date - двумерный массив
* Будет плюсом если вы реализуете через laravel echo передачу event-а на создание записи в rows
- Написать тесты

[Пример файла](https://docs.google.com/spreadsheets/d/1cC9wOVxV4fCA7nzyJ6ZpIK4ljeXgsyhdILs1F3bO4os/edit?usp=sharing)


## Запуск сервиса в Docker
```shell
git clone https://github.com/sergoslav/mgtt.git
cd mgtt
make up
```
- Сервис: http://localhost:80  
- Events host: http://localhost:18080


### Страница загрузки документа
http://localhost:80
- Форматы документов: .xls,.xlsx
- Отображение статуса загрузки (by events)

### Страница списка Rows
 
http://localhost/rows?from_date=2020-10-14&to_date=2020-11-20

- Параметры запроса: from_date, to_date
- Результат: массив, сгруппированный по date


## Запуск тестов
```shell
make test
```
