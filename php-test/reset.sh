/usr/bin/mysqladmin -u root -proot -f  drop todoapp > /dev/null &&
/usr/bin/mysqladmin -u root -proot -f  create todoapp > /dev/null &&
/usr/bin/mysql -u root -proot todoapp < /home/steve/Projects/task-api/php-test/setup/test.sql 
