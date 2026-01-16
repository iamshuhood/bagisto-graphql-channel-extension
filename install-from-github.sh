#!/bin/bash

# GraphQL Channel Extension - Installation Script
# This script installs the plugin from GitHub on a fresh Bagisto installation

set -e  # Exit on error

echo "🚀 Installing GraphQL Channel Extension from GitHub..."
echo ""

# Configuration
GITHUB_REPO="iamshuhood/bagisto-graphql-channel-extension"
GITHUB_BRANCH="main"
PLUGIN_PATH="packages/Webkul/GraphQLChannelExtension"
SCHEMA_VENDOR_PATH="vendor/bagisto/graphql-api/src/graphql/shop/common"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if we're in Bagisto root
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Not in Bagisto root directory!${NC}"
    echo "Please run this script from your Bagisto root directory."
    exit 1
fi

echo -e "${YELLOW}📋 Configuration:${NC}"
echo "   GitHub: https://github.com/$GITHUB_REPO"
echo "   Branch: $GITHUB_BRANCH"
echo "   Target: $PLUGIN_PATH"
echo ""

# Step 1: Create directories
echo -e "${GREEN}Step 1/9:${NC} Creating directories..."
mkdir -p packages/Webkul

# Step 2: Clone or update plugin
if [ -d "$PLUGIN_PATH" ]; then
    echo -e "${GREEN}Step 2/9:${NC} Plugin exists, updating from GitHub..."
    cd $PLUGIN_PATH
    git pull origin $GITHUB_BRANCH
    cd ../../..
else
    echo -e "${GREEN}Step 2/9:${NC} Cloning plugin from GitHub..."
    cd packages/Webkul
    git clone -b $GITHUB_BRANCH https://github.com/$GITHUB_REPO.git GraphQLChannelExtension
    cd ../..
fi

# Step 3: Configure composer
echo -e "${GREEN}Step 3/9:${NC} Configuring composer repository..."
composer config repositories.graphql-channel-extension path $PLUGIN_PATH

# Step 4: Install package
echo -e "${GREEN}Step 4/9:${NC} Installing package via composer..."
composer require webkul/graphql-channel-extension:@dev --no-interaction

# Step 5: Create vendor directory
echo -e "${GREEN}Step 5/9:${NC} Creating vendor schema directory..."
mkdir -p $SCHEMA_VENDOR_PATH

# Step 6: Copy schema file
echo -e "${GREEN}Step 6/9:${NC} Copying GraphQL schema to vendor..."
if [ -f "$PLUGIN_PATH/src/graphql/channel.graphql" ]; then
    cp $PLUGIN_PATH/src/graphql/channel.graphql $SCHEMA_VENDOR_PATH/channel.graphql
    echo "   ✅ Schema file copied"
else
    echo -e "${RED}   ❌ Warning: Schema file not found!${NC}"
fi

# Step 7: Clear caches
echo -e "${GREEN}Step 7/9:${NC} Clearing caches..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan lighthouse:clear-cache > /dev/null 2>&1 || true
echo "   ✅ Caches cleared"

# Step 8: Dump autoload
echo -e "${GREEN}Step 8/9:${NC} Regenerating autoload..."
composer dump-autoload > /dev/null 2>&1
echo "   ✅ Autoload regenerated"

# Step 9: Verify installation
echo -e "${GREEN}Step 9/9:${NC} Verifying installation..."

# Check if files exist
if [ -f "$PLUGIN_PATH/composer.json" ]; then
    echo "   ✅ Plugin files present"
else
    echo -e "   ${RED}❌ Plugin files missing!${NC}"
    exit 1
fi

if [ -f "$SCHEMA_VENDOR_PATH/channel.graphql" ]; then
    echo "   ✅ Vendor schema file present"
else
    echo -e "   ${YELLOW}⚠️  Vendor schema file missing!${NC}"
fi

# Check if service provider is registered
if php artisan package:discover 2>&1 | grep -q "GraphQLChannelExtension"; then
    echo "   ✅ Service provider registered"
else
    echo -e "   ${YELLOW}⚠️  Service provider not showing in package:discover${NC}"
fi

echo ""
echo -e "${GREEN}✅ Installation complete!${NC}"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 Next Steps:"
echo ""
echo "1. Start your server:"
echo "   php artisan serve"
echo ""
echo "2. Test the installation:"
echo "   curl -X POST http://127.0.0.1:8000/graphql \\"
echo "     -H \"Content-Type: application/json\" \\"
echo "     -H \"X-Channel: default\" \\"
echo "     -d '{\"query\": \"{ currentChannel { id code name } }\"}'"
echo ""
echo "3. Or use GraphQL Playground:"
echo "   Open: http://127.0.0.1:8000/graphql"
echo "   Add header: X-Channel: default"
echo "   Query: { currentChannel { id code name } }"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${YELLOW}💡 Important:${NC}"
echo "   After Bagisto updates, run:"
echo "   cp $PLUGIN_PATH/src/graphql/channel.graphql $SCHEMA_VENDOR_PATH/"
echo ""
echo "🎉 Happy coding!"
