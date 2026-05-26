# Database Setup

Use the SQL files in this project like this:

1. Fresh hosting or fresh local install:
   Run [C:\xampp\htdocs\Archive\database.sql](C:\xampp\htdocs\Archive\database.sql)

2. Existing database upgrade:
   Run only the needed files in [C:\xampp\htdocs\Archive\migrations](C:\xampp\htdocs\Archive\migrations)
   Apply them in filename/date order.

Notes:
- `database.sql` is now the single full install file for new hosting.
- Migration files are kept separate so existing live databases can be upgraded safely.
- Do not use any old local-only root privilege reset scripts on shared hosting.
