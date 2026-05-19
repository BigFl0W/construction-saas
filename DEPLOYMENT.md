# TPV Deployment Notes

## One-time move to Namecheap

1. Upload the project files to your hosting account.
2. Import the database into your Namecheap/MySQL database.
3. Copy `.env.namecheap.example` to `.env`.
4. Fill in:
   - `APP_URL`
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `MAIL_FROM_ADDRESS`
   - `SMTP_HOST`
   - `SMTP_PORT`
   - `SMTP_USERNAME`
   - `SMTP_PASSWORD`
   - `SMTP_ENCRYPTION`
   - `ENCRYPTION_KEY`

## Important SMTP note

The app already supports SMTP settings from the admin dashboard.

Priority order is:

1. Admin dashboard SMTP settings stored in the `settings` table
2. `.env` SMTP values as fallback

That means:

- If you migrate your database, your admin-entered SMTP settings move with it.
- If those settings are already correct, you do not need to re-enter them.
- If you prefer server-level defaults, keep the `.env` SMTP values filled in as a fallback.

## Recommended production approach

- Keep the real working SMTP details in the admin dashboard if you want non-technical control later.
- Keep matching SMTP values in `.env` as backup defaults.
- Make sure `smtp_from_email` uses a mailbox/domain that actually exists on Namecheap.

## Base URL behavior

The app now detects the site URL automatically, but `APP_URL` in `.env` should still be set in production so links in emails and assets are consistent.

## Security

- Do not commit `.env`
- Keep `.htaccess` deployed so `.env` files are not web-accessible
- Replace the default `ENCRYPTION_KEY` with a long random value in production
