https://wixelhq.com/blog/how-to-install-postgresql-on-ubuntu-remote-access
https://gist.github.com/Kartones/dd3ff5ec5ea238d4c546
https://www.postgresql.org/docs/10/sql-commands.html
https://axiomq.com/blog/backup-and-restore-a-postgresql-database/

psql: NeverForget

###### Backup (as astee in home folder):
- Sign in to pluggdagbok.
- command: pg_dump -F p > ~/backup.sql
- Sign out.
- command: sudo mv ../pluggdagbok/backup.sql Documents/temp/
- Store anywhere (preferably dropbox).

###### Restore (as astee in home folder):
- Place backup.sql in pluggdagbok home folder.
- Sign in to pluggdagbok.
- command: psql < backup.sql
- Sign out.
