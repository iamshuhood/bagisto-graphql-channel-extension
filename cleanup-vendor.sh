#!/bin/bash

# Script to clean up old vendor modifications
# This removes all custom code added to vendor files

set -e

echo "========================================="
echo "Cleaning Up Vendor Modifications"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

VENDOR_DIR="vendor/bagisto/graphql-api"

if [ ! -d "$VENDOR_DIR" ]; then
    echo -e "${RED}Error: Vendor directory not found: $VENDOR_DIR${NC}"
    exit 1
fi

echo -e "${YELLOW}This script will restore vendor files to their original state.${NC}"
echo -e "${YELLOW}Your custom code is safe in the plugin package!${NC}"
echo ""
echo -e "${RED}⚠️  Warning: This will remove custom code from:${NC}"
echo "  • $VENDOR_DIR/src/Http/Middleware/ChannelMiddleware.php"
echo "  • $VENDOR_DIR/src/Http/Middleware/GraphQLCacheMiddleware.php"
echo "  • $VENDOR_DIR/src/Queries/Shop/Common/ChannelQuery.php"
echo "  • $VENDOR_DIR/src/graphql/shop/common/channel.graphql"
echo ""
read -p "Continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Aborted."
    exit 0
fi

echo ""
echo -e "${YELLOW}Step 1: Backing up current vendor files...${NC}"
BACKUP_DIR="vendor-backups/graphql-api-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

files_to_backup=(
    "src/Http/Middleware/ChannelMiddleware.php"
    "src/Http/Middleware/GraphQLCacheMiddleware.php"
    "src/Queries/Shop/Common/ChannelQuery.php"
    "src/graphql/shop/common/channel.graphql"
)

for file in "${files_to_backup[@]}"; do
    if [ -f "$VENDOR_DIR/$file" ]; then
        mkdir -p "$BACKUP_DIR/$(dirname $file)"
        cp "$VENDOR_DIR/$file" "$BACKUP_DIR/$file"
        echo "  ✓ Backed up: $file"
    fi
done

echo ""
echo -e "${YELLOW}Step 2: Reinstalling bagisto/graphql-api package...${NC}"
composer reinstall bagisto/graphql-api

echo ""
echo -e "${YELLOW}Step 3: Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
composer dump-autoload

echo ""
echo -e "${GREEN}========================================="
echo "✅ Cleanup Complete!"
echo "=========================================${NC}"
echo ""
echo -e "${GREEN}Vendor files have been restored to their original state.${NC}"
echo -e "${BLUE}Backup saved to: $BACKUP_DIR${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. The plugin is still active and provides all functionality"
echo "2. Restart your server (php artisan serve or php artisan octane:reload)"
echo "3. Test that channel detection still works"
echo ""
echo -e "${GREEN}Your custom code is safe in: packages/Webkul/GraphQLChannelExtension/${NC}"
echo ""
