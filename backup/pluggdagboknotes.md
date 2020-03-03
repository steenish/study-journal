https://wixelhq.com/blog/how-to-install-postgresql-on-ubuntu-remote-access
https://gist.github.com/Kartones/dd3ff5ec5ea238d4c546
https://www.postgresql.org/docs/10/sql-commands.html
https://axiomq.com/blog/backup-and-restore-a-postgresql-database/

psql: NeverForget

###### Backup (as astee in home folder):
- Sign in to postgres.
- command: pg_dump pluggdagbok > /var/tmp/backup.sql
- Sign out.
- command: sudo mv /var/tmp/backup.sql <new location>
- Store anywhere (preferably dropbox).

###### Restore (as astee in home folder):
- Place backup.sql in some directory A.
- Create the database pluggdagbok, add user pluggdagbok with all permissions.
- EITHER command: psql < A/backup.sql
- OR log into psql and run \i A/backup.sql
