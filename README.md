1) clone repo https://github.com/oznest/litvinov.git
2) in project folder run: make build
3) Than make test to run tests
4) open http://localhost:8080/weather/Kiev to check weather by Kiev


Використав такі бандли:
1) jms serializer bundle - для десериалізації json
2) eightpoints/guzzle-bundle - для зручного конфігурування клієнтами + автовайрінг клієнтів зручний + логує запити у symfony devtools
3) monolog bundle - для логуванн

WEATHER_API_KEY i WEATHER_HOST задаються у файлі .env
