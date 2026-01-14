#!/usr/bin/env bash
set -euo pipefail

# Deploy script for beta launch on Apache (no sample data seeding).
# Assumptions:
# - Running from project root.
# - PHP CLI and MySQL client available.
# - Apache vhost will be configured separately using the provided snippet.

GREEN="$(printf '\033[32m')"
RED="$(printf '\033[31m')"
YELLOW="$(printf '\033[33m')"
RESET="$(printf '\033[0m')"

info()  { printf "%s[INFO]%s %s\n" "$YELLOW" "$RESET" "$*"; }
ok()    { printf "%s[OK]%s   %s\n" "$GREEN" "$RESET" "$*"; }
fail()  { printf "%s[FAIL]%s %s\n" "$RED" "$RESET" "$*"; exit 1; }

# --- Config (edit these before running) ---
DB_HOST="localhost"
DB_NAME="ithmpwus_ithm_cms"
DB_USER="ithmpwus_ztdcp"
DB_PASS="A?B-L32][GsVnztU"
APP_URL="https://cms.ithm.edu.pk"
APACHE_WEBROOT="/var/www/ithm-cms"
# Adjust these to match your Apache/PHP user on Windows/XAMPP or Linux:
PHP_USER="${PHP_USER_OVERRIDE:-www-data}"
PHP_GROUP="${PHP_GROUP_OVERRIDE:-www-data}"
UPLOAD_DIRS=("storage/uploads" "storage/logs")

# --- Helpers ---
run() { info "$*"; eval "$@"; }

# --- Steps ---
info "1) Ensure config values are set in this script."

info "2) Create env/config (edit if needed)."
cat > config/config.php <<'PHP'
<?php
define('BASE_URL', getenv('APP_URL') ?: 'http://localhost/ithm');
define('ITEMS_PER_PAGE', 20);
define('UPLOADS_PATH', __DIR__ . '/../storage/uploads');
define('LOGS_PATH', __DIR__ . '/../storage/logs');
define('CSRF_TOKEN_NAME', 'csrf_token');
PHP

cat > config/database.php <<PHP
<?php
return [
    'host' => '${DB_HOST}',
    'database' => '${DB_NAME}',
    'username' => '${DB_USER}',
    'password' => '${DB_PASS}',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];
PHP
ok "Config files written (config/config.php, config/database.php)."

info "3) Run migrations (schema + feature migrations)."
php database/run_migration.php
php database/run_migration_campus.php
php database/run_migration_fee_structures.php
php database/run_migration_academics.php
ok "Migrations executed."

info "4) Reset DB to clean state (roles, main campus, admin user)."
php database/reset_fresh_launch.php
ok "Database reset and minimal seed applied."

info "5) Set permissions on writable paths."
for d in "${UPLOAD_DIRS[@]}"; do
  mkdir -p "$d"
  if id -u "${PHP_USER}" >/dev/null 2>&1; then
    chown -R "${PHP_USER}:${PHP_GROUP}" "$d"
  else
    info "User ${PHP_USER} not found; skipping chown for $d (set PHP_USER_OVERRIDE/PHP_GROUP_OVERRIDE if needed)"
  fi
  chmod -R 775 "$d"
done
ok "Writable paths prepared."

info "6) Apache vhost snippet (create separately):"
cat <<'APACHE'
<VirtualHost *:80>
    ServerName cms.ithm.edu.pk
    DocumentRoot /var/www/ithm-cms/public

    <Directory /var/www/ithm-cms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ithm-cms-error.log
    CustomLog ${APACHE_LOG_DIR}/ithm-cms-access.log combined
</VirtualHost>
APACHE
ok "Apache vhost template output above (enable and reload Apache)."

info "7) Production .htaccess (already in public/, ensure it exists/enabled):"
cat <<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS
ok ".htaccess snippet shown."

info "8) Security hardening recommendations (manual):"
cat <<'SECURITY'
- Change the default admin password immediately (admin@ithm.edu.pk / Admin@123).
- Disable /register route for public access if not needed.
- Set proper file upload limits and PHP memory/post_max_size as needed.
- Enable HTTPS with Let's Encrypt and force redirect to HTTPS.
- Keep display_errors off in production; log errors to files.
SECURITY

ok "Deployment script completed. Review outputs and adjust values as needed."

