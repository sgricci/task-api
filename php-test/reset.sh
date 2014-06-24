/usr/bin/mysqladmin -u root -proot -f  drop todoapp > /dev/null &&
/usr/bin/mysqladmin -u root -proot -f  create todoapp > /dev/null &&
/usr/bin/mysql -u root -proot todoapp < /home/steve/Projects/sprinkle-test/setup/test.sql 
