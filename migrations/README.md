# Migrations

This folder contains incremental schema changes for existing installations.

How to use:
- If you are installing the project on a new host, import [C:\xampp\htdocs\Archive\database.sql](C:\xampp\htdocs\Archive\database.sql) only.
- If you already have an older database and need to upgrade it, apply the migration files in date order.

Current structure:
- `2026_05_18_add_contact_submissions.sql`
- `2026_05_19_add_admin_auth.sql`
- `2026_05_20_add_quote_requests.sql`
- `2026_05_21_add_project_media.sql`
- `2026_05_21_add_services_content.sql`
