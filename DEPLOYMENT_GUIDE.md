# 🚀 Deployment Guide - GraphQL Channel Extension

## Overview

This guide shows you **EXACT steps** for:
1. ✅ Pushing your plugin to GitHub
2. ✅ Installing on fresh Bagisto
3. ✅ Updating Bagisto without breaking

---

## 📦 Step 1: Push Plugin to GitHub

### 1.1 Create Git Repository for Your Plugin

```bash
cd /Users/msgshuhood/Documents/backup/dressachi-dashboard-backup-site-2026/public_html_new/public_html/packages/Webkul/GraphQLChannelExtension

# Initialize git
git init

# Create .gitignore
cat > .gitignore << 'EOF'
vendor/
.DS_Store
*.log
EOF

# Add all files
git add .

# Commit
git commit -m "Initial commit: GraphQL Channel Extension for Bagisto"
```

### 1.2 Create GitHub Repository

1. Go to https://github.com/new
2. Create new repository (e.g., `bagisto-graphql-channel-extension`)
3. **Don't** initialize with README (you already have one)

### 1.3 Push to GitHub

```bash
# Add remote (replace with YOUR GitHub username and repo name)
git remote add origin https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git

# Push
git branch -M main
git push -u origin main
```

✅ **Done! Your plugin is now on GitHub**

---

## 🆕 Step 2: Install on Fresh Bagisto (EXACT STEPS)

### Scenario: You just installed fresh Bagisto and want to add your channel plugin

```bash
# 1. Navigate to your new Bagisto installation
cd /path/to/new/bagisto

# 2. Create packages directory if it doesn't exist
mkdir -p packages/Webkul

# 3. Clone your plugin from GitHub
cd packages/Webkul
git clone https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git GraphQLChannelExtension

# 4. Go back to Bagisto root
cd ../..

# 5. Add repository to composer.json
composer config repositories.graphql-channel-extension path packages/Webkul/GraphQLChannelExtension

# 6. Install the plugin
composer require webkul/graphql-channel-extension:@dev

# 7. Create vendor schema directory
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common

# 8. Copy schema file to vendor location (ONE-TIME SETUP)
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# 9. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
composer dump-autoload

# 10. Restart server
php artisan serve
```

### Verify Installation

```bash
# Test the plugin works
curl -X POST http://127.0.0.1:8000/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { id code name } }"}'
```

**Expected result:**
```json
{
  "data": {
    "currentChannel": {
      "id": "1",
      "code": "default",
      "name": "Your Channel Name"
    }
  }
}
```

✅ **Plugin installed and working!**

---

## 🔄 Step 3: Update Bagisto (Keep Your Plugin Safe)

### Scenario: Bagisto releases new version, you want to update

```bash
# 1. Navigate to your Bagisto root
cd /path/to/your/bagisto

# 2. Update Bagisto
composer update bagisto/bagisto

# 3. Check if vendor schema file still exists
ls -la vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# 4. If file was removed by update, copy it back
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache

# 6. Restart server
php artisan octane:reload  # or php artisan serve
```

✅ **Bagisto updated, plugin still works!**

---

## 📋 Quick Reference Commands

### Install Plugin on Fresh Bagisto

```bash
cd /path/to/bagisto
mkdir -p packages/Webkul
cd packages/Webkul
git clone https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git GraphQLChannelExtension
cd ../..
composer config repositories.graphql-channel-extension path packages/Webkul/GraphQLChannelExtension
composer require webkul/graphql-channel-extension:@dev
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache
composer dump-autoload
```

### Update Plugin from GitHub

```bash
cd packages/Webkul/GraphQLChannelExtension
git pull origin main
cd ../../..
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### After Bagisto Update

```bash
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
```

---

## 🤖 Automation Script (Recommended!)

Create this script to automate installation:

### Create `install-channel-plugin.sh` in your Bagisto root:

```bash
#!/bin/bash

echo "🚀 Installing GraphQL Channel Extension..."

# Configuration
GITHUB_REPO="YOUR_USERNAME/bagisto-graphql-channel-extension"
PLUGIN_PATH="packages/Webkul/GraphQLChannelExtension"

# Create directories
mkdir -p packages/Webkul

# Clone or update plugin
if [ -d "$PLUGIN_PATH" ]; then
    echo "📦 Plugin exists, updating..."
    cd $PLUGIN_PATH
    git pull origin main
    cd ../../..
else
    echo "📦 Cloning plugin from GitHub..."
    cd packages/Webkul
    git clone https://github.com/$GITHUB_REPO.git GraphQLChannelExtension
    cd ../..
fi

# Configure composer
echo "🔧 Configuring composer..."
composer config repositories.graphql-channel-extension path packages/Webkul/GraphQLChannelExtension

# Install package
echo "📦 Installing package..."
composer require webkul/graphql-channel-extension:@dev

# Create vendor directory
echo "📁 Creating vendor schema directory..."
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common

# Copy schema file
echo "📄 Copying schema file..."
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
composer dump-autoload

echo "✅ Installation complete!"
echo ""
echo "Test with:"
echo "curl -X POST http://127.0.0.1:8000/graphql -H \"Content-Type: application/json\" -H \"X-Channel: default\" -d '{\"query\": \"{ currentChannel { id code name } }\"}'"
```

### Usage:

```bash
# Make executable
chmod +x install-channel-plugin.sh

# Run it
./install-channel-plugin.sh
```

---

## 📝 Important Notes

### What Gets Committed to Git?

**Your Bagisto Project:**
```
.gitignore should include:
/packages/Webkul/GraphQLChannelExtension  ← Don't commit, it's installed via composer
/vendor/                                   ← Don't commit
```

**Your Plugin Repository:**
```
packages/Webkul/GraphQLChannelExtension/
├── All plugin files ✅ COMMIT
├── .git/           ✅ This is the git repo
└── vendor/         ❌ Don't commit (in .gitignore)
```

### The One Vendor File You Need

After ANY Bagisto update or fresh install, always ensure this file exists:

```bash
vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql
```

If it's missing, copy it:
```bash
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
```

---

## 🎯 Complete Workflow Example

### Day 1: Initial Setup (Current Project)

```bash
# 1. Push your plugin to GitHub
cd packages/Webkul/GraphQLChannelExtension
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git
git push -u origin main
```

### Day 30: Install on Production Server

```bash
# 1. Clone your Bagisto project (without plugin)
git clone https://github.com/YOUR_USERNAME/your-bagisto-project.git
cd your-bagisto-project

# 2. Install dependencies
composer install

# 3. Run installation script
./install-channel-plugin.sh

# Done!
```

### Day 60: Update Bagisto

```bash
# 1. Update Bagisto
composer update bagisto/bagisto

# 2. Restore schema file if needed
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/

# 3. Clear caches
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache

# Done!
```

### Day 90: Update Your Plugin

```bash
# 1. Make changes to plugin
cd packages/Webkul/GraphQLChannelExtension
# ... edit files ...

# 2. Commit and push
git add .
git commit -m "Add new feature"
git push origin main

# 3. On other servers, pull updates
cd packages/Webkul/GraphQLChannelExtension
git pull origin main
cd ../../..
composer dump-autoload
php artisan cache:clear
```

---

## 🔥 Pro Tips

### 1. Add to Your Deployment Script

```bash
# In your deploy.sh
composer install
./install-channel-plugin.sh  # ← Add this line
php artisan migrate
# ... rest of deployment
```

### 2. Document in Your Main README

Add to your project's README.md:

```markdown
## GraphQL Channel Extension

This project uses a custom GraphQL Channel Extension.

**Installation:**
```bash
./install-channel-plugin.sh
```

**After Bagisto updates:**
```bash
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
```
```

### 3. Version Your Plugin

Tag releases in GitHub:

```bash
cd packages/Webkul/GraphQLChannelExtension
git tag -a v1.0.0 -m "Version 1.0.0 - Initial release"
git push origin v1.0.0
```

Then install specific versions:
```bash
composer require webkul/graphql-channel-extension:v1.0.0
```

---

## ✅ Checklist

### Setting Up GitHub
- [ ] Initialize git in plugin directory
- [ ] Create GitHub repository
- [ ] Push plugin to GitHub
- [ ] Test cloning from GitHub

### Fresh Installation
- [ ] Clone Bagisto
- [ ] Run installation script
- [ ] Copy vendor schema file
- [ ] Test GraphQL queries
- [ ] Verify channel detection

### Bagisto Updates
- [ ] Update Bagisto
- [ ] Check vendor schema file exists
- [ ] Copy if missing
- [ ] Clear caches
- [ ] Test functionality

---

## 🆘 Troubleshooting

### Plugin not found after install

```bash
composer dump-autoload
php artisan package:discover
```

### Schema file missing after update

```bash
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan lighthouse:clear-cache
```

### Changes not applied

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
php artisan serve  # or octane:reload
```

---

## 🎉 Summary

**To deploy anywhere:**

1. Clone plugin from GitHub ✅
2. Install via composer ✅
3. Copy ONE file to vendor ✅
4. Clear caches ✅
5. Done! ✅

**Your plugin is:**
- ✅ In version control (GitHub)
- ✅ Portable (works anywhere)
- ✅ Update-safe (survives Bagisto updates)
- ✅ Maintainable (clean separation)

**No more touching vendor files! 🚀**
