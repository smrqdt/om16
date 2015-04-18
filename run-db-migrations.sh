#!/bin/sh

if [ ! -f mysql.cnf ]; then
    echo "Mysql settings file 'mysql.cnf' not found. Copy the sample file:"
    echo "cp sample.mysql.cnf mysql.cnf"
    exit 1
fi

EXEC_MYSQL="mysql --defaults-extra-file=mysql.cnf tapeshop --disable-column-names"

DB_MIGRATIONS=$(echo "SELECT filename, md5hash FROM dbmigrations" | eval $EXEC_MYSQL)

DB_MIGRATIONS_FOLDER='db-migrations'

eval $EXEC_MYSQL < dbmigrations-setup.sql

#INSERT INTO `dbmigrations` (`filename`, `md5hash`) VALUES
#  ('db-migrations/00-initial.sql', '6c8ef6842c7243b4543d0481bd7d0725'),
#  ('db-migrations/01-add-dbversion-table.sql ', 'e70dec9b963f68081c22c7d9d9c219ac');

for DB_MIGRATION_FILE in $(ls $DB_MIGRATIONS_FOLDER);
do
  echo '-----'
  echo 'Found migration file: '$DB_MIGRATION_FILE
  DB_MIGRATION_FILE_PATH=$DB_MIGRATIONS_FOLDER/$DB_MIGRATION_FILE

  DB_CHECKSUM=$(echo "SELECT md5hash FROM dbmigrations WHERE filename = '$DB_MIGRATION_FILE_PATH'" | eval $EXEC_MYSQL)
  FILE_CHECKSUM=$(md5 -q $DB_MIGRATION_FILE_PATH);

  if [ -z $DB_CHECKSUM ]; then
    echo "No checksum for $DB_MIGRATION_FILE_PATH found"


    while [[ ! $EXECUTE =~ ^[YyNn]$ ]]
    do
      read -p "Apply migration ('${DB_MIGRATION_FILE_PATH}') now [Y/N]: " -n 1 -r EXECUTE
      echo ""
    done
    if [[ $EXECUTE =~ ^[Nn]$ ]]
    then
      exit 0
    fi

    eval $EXEC_MYSQL < $DB_MIGRATION_FILE_PATH
    echo "INSERT INTO dbmigrations (filename, md5hash) VALUES ('${DB_MIGRATION_FILE_PATH}','${FILE_CHECKSUM}');" | eval $EXEC_MYSQL

  else
    echo "Found checksum in db: "$DB_CHECKSUM

    if [ $DB_CHECKSUM == $FILE_CHECKSUM ]; then
       echo "✓ Checksum is valid"
    else
       echo "✗ File checksum is different: "$FILE_CHECKSUM
    fi
  fi

done