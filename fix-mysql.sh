
#!/bin/bash

echo "Stopping MySQL service..."
sudo systemctl stop mysql

echo "Creating MySQL socket directory..."
sudo mkdir -p /var/run/mysqld
sudo chown mysql:mysql /var/run/mysqld

echo "Fixing MySQL data directory permissions..."
sudo chown -R mysql:mysql /var/lib/mysql
sudo chmod 750 /var/lib/mysql

echo "Moving InnoDB log files (if they exist)..."
sudo mv /var/lib/mysql/ib_logfile0 /var/lib/mysql/ib_logfile0.bak 2>/dev/null
sudo mv /var/lib/mysql/ib_logfile1 /var/lib/mysql/ib_logfile1.bak 2>/dev/null

echo "Starting MySQL service..."
sudo systemctl start mysql

echo "Checking MySQL status..."
sudo systemctl status mysql --no-pager

echo "If MySQL started, test connection:"
echo "mysql -u root -p -h 127.0.0.1 -P 3306"
