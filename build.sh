#!/bin/bash

# Run Vite build
npm run build

# Create database directory
mkdir -p /tmp

# Run Laravel migrations
php artisan migrate --force --database=sqlite
