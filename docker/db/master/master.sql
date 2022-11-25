CREATE USER slave_read_user@'%' IDENTIFIED WITH mysql_native_password BY 'xSc1jnBR6r8GW9gQgNvdKsVqGDqm5l';
GRANT REPLICATION SLAVE ON *.* TO slave_read_user@'%';
FLUSH PRIVILEGES;