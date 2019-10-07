# banklv-rss

1. git clone https://github.com/ozo2003/banklv-rss.git
2. cd banklv-rss
###> optional ###  
3. vim .env -> set wanted url
4. sudo sed -i "127.0.0.1 <url_from_3rd_step>" /etc/hosts
###< optional ###  
5. docker-compose up -d
6. docker exec -it rss bash
7. composer install -n
8. touch .env.local
9. vim .env.local -> enter url of db like in .env
10. composer dump-env prod
11. bin/console d:d:c
12. bin/console d:m:m -n

cron: bin/console app:r:p

http://127.0.0.1:9666 or url from 3rd point:9000
