#!/bin/bash

# ================================================
# AgriSys Cache Busting Deployment Script
# ================================================
# This script automatically updates the asset version
# and deploys your changes
# ================================================

echo "🚀 AgriSys Cache Busting Deployment"
echo "===================================="

# Get current version from .env
CURRENT_VERSION=$(grep "^ASSET_VERSION=" .env | cut -d '=' -f2)
echo "Current version: $CURRENT_VERSION"

# Ask for new version or use timestamp
read -p "Enter new version (or press Enter for timestamp): " NEW_VERSION

if [ -z "$NEW_VERSION" ]; then
    NEW_VERSION=$(date +%Y%m%d-%H%M)
    echo "Using timestamp version: $NEW_VERSION"
fi

# Update .env file
sed -i "s/^ASSET_VERSION=.*/ASSET_VERSION=$NEW_VERSION/" .env
echo "✓ Updated .env: ASSET_VERSION=$NEW_VERSION"

# Update .env.production file
sed -i "s/^ASSET_VERSION=.*/ASSET_VERSION=$NEW_VERSION/" .env.production
echo "✓ Updated .env.production: ASSET_VERSION=$NEW_VERSION"

# Clear Laravel config cache
php artisan config:clear
echo "✓ Cleared config cache"

# Optional: Run other deployment commands
# Uncomment these as needed:
# composer dump-autoload
# php artisan optimize:clear
# npm run build

echo ""
echo "✅ Version updated successfully!"
echo "📝 Remember to:"
echo "   1. Test locally"
echo "   2. Commit changes: git add . && git commit -m 'Bump version to $NEW_VERSION'"
echo "   3. Push to server: git push"
echo "   4. Users will automatically get new version!"
