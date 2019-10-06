#banklv-rss

1. git clone https://github.com/ozo2003/banklv-rss.git  
2. cd banklv-rss  
###> optional ###  
3. vim .env -> set wanted url  
4. sudo sed -i "127.0.0.1 <url_from_3rd_step>" /etc/hosts  
###< optional ###  
5. docker-compose up -d  

http://127.0.0.1:9666 or url from 3rd point:9000
