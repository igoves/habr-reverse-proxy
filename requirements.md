## Обратите внимание

Перед решением тестового задания заполните, пожалуйста, небольшую анкету
по адресу: 

https://job.ivelum.com/php/form/

После заполнения анкеты мы свяжемся с вами, ответим на ваши вопросы по вакансии 
и расскажем подробнее о наших проектах. По результатам этого общения мы можем
предложить вам попробовать сделать тестовое задание, которое описано ниже.
Присылайте ваше решение на адрес [job@ivelum.com](mailto:job@ivelum.com).


## Хабрапрокси

Реализовать простой http-прокси-сервер, запускаемый локально (порт на ваше
усмотрение), который показывает содержимое страниц Хабра. Прокси должен
модицифировать текст на страницах следующим образом: после каждого слова из
шести букв должен стоять значок «™». Пример:

Исходный текст: https://habr.com/ru/company/yandex/blog/258673/

```
Сейчас на фоне уязвимости Logjam все в индустрии в очередной раз обсуждают 
проблемы и особенности TLS. Я хочу воспользоваться этой возможностью, чтобы 
поговорить об одной из них, а именно — о настройке ciphersiutes.
```

Через ваш прокси™: http://127.0.0.1:8232/ru/company/yandex/blog/258673/

```
Сейчас™ на фоне уязвимости Logjam™ все в индустрии в очередной раз обсуждают 
проблемы и особенности TLS. Я хочу воспользоваться этой возможностью, чтобы 
поговорить об одной из них, а именно™ — о настройке ciphersiutes. 
```

Условия:
* PHP 7+
* страницы должны™ отображаться и работать полностью корректно, в точности так,
  как и оригинальные (за исключением модифицированного текста™);
* при навигации по ссылкам, которые ведут на другие™ страницы хабра, браузер
  должен™ оставаться на адресе™ вашего™ прокси™;
* можно использовать любые общедоступные библиотеки, которые сочтёте нужным™;
* чем меньше™ кода, тем лучше. Рекомендуем следовать PSR;
* если в условиях вам не хватает каких-то данных™, опирайтесь на здравый смысл.

Если задачу™ удалось сделать быстро™, и у вас еще остался энтузиазм - как 
насчет™ написания тестов™?

Присылайте ваше решение в виде ссылки на gist или на публичный репозиторий на 
Github.


## Обратите внимание

Напомним порядок действий:

1. Пожалуйста, заполните анкету: https://job.ivelum.com/php/form/;
2. Мы свяжемся с вами, ответим на ваши вопросы по вакансии и расскажем про наши
   проекты. По результатам этого общения мы можем предложить вам попробовать 
   сделать тестовое задание, которое описано выше;
3. Присылайте ваше решение на [job@ivelum.com](mailto:job@ivelum.com).
