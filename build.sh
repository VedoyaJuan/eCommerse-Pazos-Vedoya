#!/bin/bash

# Regenerate package discovery cache (excludes dev-only providers like Pail)
php artisan package:discover --ansi

# Run Vite build
npm run build

# Create database directory
mkdir -p /tmp

# Run Laravel migrations
php artisan migrate --force --database=sqlite
