#!/bin/bash

# GraphQL Channel Extension Installation Script
# This script helps you install and configure the GraphQL Channel Extension for Bagisto

set -e

echo "========================================="
echo "GraphQL Channel Extension Installation"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if we're in the correct directory
if [ ! -f "composer.json" ]; then
    echo -e "${RED}Error: composer.json not found. Please run this script from your Bagisto root directory.${NC}"
    exit 1
fi

echo -e "${BLUE}This plugin provides automatic channel detection for GraphQL API.${NC}"
echo -e "${BLUE}No manual configuration changes needed!${NC}"
echo ""

echo -e "${YELLOW}Step 1: Installing package via Composer...${NC}"
composer require webkul/graphql-channel-extension:@dev

echo ""
echo -e "${YELLOW}Step 2: Dumping autoload...${NC}"
composer dump-autoload

echo ""
echo -e "${YELLOW}Step 3: Clearing all caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo -e "${YELLOW}Step 4: Clearing Lighthouse schema cache...${NC}"
php artisan lighthouse:clear-cache 2>/dev/null || echo "Lighthouse cache cleared (or not applicable)"

echo ""
echo -e "${GREEN}========================================="
echo "✅ Installation Complete!"
echo "=========================================${NC}"
echo ""
echo -e "${GREEN}The GraphQL Channel Extension has been installed successfully!${NC}"
echo ""
echo -e "${BLUE}✨ Key Features:${NC}"
echo "  ✅ Automatic channel detection via hostname"
echo "  ✅ Channel detection via X-Channel header"
echo "  ✅ Channel-aware GraphQL caching"
echo "  ✅ Zero configuration needed!"
echo ""
echo -e "${BLUE}📋 What Was Done:${NC}"
echo "  • Middleware automatically injected into Lighthouse config"
echo "  • Query namespaces automatically registered"
echo "  • All settings applied at runtime"
echo ""
echo -e "${YELLOW}⚠️  Important: Restart your server${NC}"
echo "  Laravel dev server: php artisan serve"
echo "  Laravel Octane:     php artisan octane:reload"
echo ""
echo -e "${BLUE}🧪 Test with this GraphQL query:${NC}"
echo '  query {' 
echo '    currentChannel {'
echo '      id'
echo '      code'
echo '      name'
echo '      hostname'
echo '    }'
echo '  }'
echo ""
echo -e "${GREEN}For more information: packages/Webkul/GraphQLChannelExtension/README.md${NC}"
echo ""
