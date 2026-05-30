#!/bin/bash
set -e

echo "Fetching secrets from AWS Secrets Manager..."

SECRET=$(aws secretsmanager get-secret-value \
  --secret-id smartpos/prod \
  --region ap-southeast-1 \
  --query SecretString \
  --output text)

# Export all secrets as environment variables
export APP_KEY=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['APP_KEY'])")
export APP_URL=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['APP_URL'])")
export DB_HOST=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['DB_HOST'])")
export DB_PORT=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['DB_PORT'])")
export DB_DATABASE=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['DB_DATABASE'])")
export DB_USERNAME=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['DB_USERNAME'])")
export DB_PASSWORD=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['DB_PASSWORD'])")
export AWS_BUCKET=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['AWS_BUCKET'])")
export AWS_DEFAULT_REGION=$(echo $SECRET | python3 -c "import sys,json; print(json.load(sys.stdin)['AWS_DEFAULT_REGION'])")

# Set remaining env vars that don't need to be secret
export APP_ENV=production
export APP_DEBUG=false
export LOG_CHANNEL=stderr
export SESSION_DRIVER=database
export QUEUE_CONNECTION=database
export CACHE_STORE=database
export FILESYSTEM_DISK=s3
export AWS_ACCESS_KEY_ID=""
export AWS_SECRET_ACCESS_KEY=""

echo "Secrets loaded successfully."

# Run Laravel setup commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link

echo "Laravel setup complete. Starting server..."

# Start Apache
apache2-foreground

# Start PHP-FPM + Apache
apache2-foreground
