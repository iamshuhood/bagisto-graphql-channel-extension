# ✅ YOUR EXACT STEPS - GitHub Deployment

## 🎯 What You Asked For

> "i want like this i will push this to github and then pull whenever i install new bagisto"

**Here are your EXACT steps:**

---

## 📝 STEP-BY-STEP GUIDE

### PART 1: Push to GitHub (Do This Once)

#### Step 1: Create GitHub Repository

1. Go to https://github.com/new
2. Repository name: `bagisto-graphql-channel-extension`
3. Description: "Channel detection plugin for Bagisto GraphQL API"
4. Make it **Public** or **Private** (your choice)
5. Click "Create repository"

#### Step 2: Push Your Plugin

```bash
# Navigate to your plugin directory
cd /Users/msgshuhood/Documents/backup/dressachi-dashboard-backup-site-2026/public_html_new/public_html/packages/Webkul/GraphQLChannelExtension

# Initialize git
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit: GraphQL Channel Extension for Bagisto"

# Add remote (REPLACE 'YOUR_USERNAME' with your actual GitHub username!)
git remote add origin https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git

# Push to GitHub
git branch -M main
git push -u origin main
```

✅ **Done! Your plugin is now on GitHub!**

#### Step 3: Update the Installation Script

Edit this file: `install-from-github.sh`

Change this line:
```bash
GITHUB_REPO="YOUR_USERNAME/bagisto-graphql-channel-extension"
```

To your actual username:
```bash
GITHUB_REPO="msgshuhood/bagisto-graphql-channel-extension"  # Example
```

Then push the change:
```bash
git add install-from-github.sh
git commit -m "Update GitHub username"
git push origin main
```

---

### PART 2: Install on Fresh Bagisto (Whenever You Want)

#### Method 1: Using Installation Script (Easiest)

```bash
# 1. Navigate to your new Bagisto installation
cd /path/to/new/bagisto

# 2. Download the installation script
curl -o install-plugin.sh https://raw.githubusercontent.com/YOUR_USERNAME/bagisto-graphql-channel-extension/main/install-from-github.sh

# 3. Make it executable
chmod +x install-plugin.sh

# 4. Run it
./install-plugin.sh
```

**That's it!** The script does everything:
- Clones your plugin from GitHub
- Installs via composer
- Copies the vendor file
- Clears caches
- Verifies installation

#### Method 2: Manual Steps (If you prefer)

```bash
# 1. Navigate to your Bagisto root
cd /path/to/bagisto

# 2. Create directory
mkdir -p packages/Webkul

# 3. Clone your plugin
cd packages/Webkul
git clone https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git GraphQLChannelExtension
cd ../..

# 4. Configure composer
composer config repositories.graphql-channel-extension path packages/Webkul/GraphQLChannelExtension

# 5. Install
composer require webkul/graphql-channel-extension:@dev

# 6. Copy vendor file (IMPORTANT!)
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# 7. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
composer dump-autoload

# 8. Start server
php artisan serve
```

#### Test It Works

```bash
curl -X POST http://127.0.0.1:8000/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { id code name } }"}'
```

Expected response:
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

✅ **Plugin working!**

---

### PART 3: When You Update Bagisto

```bash
# 1. Update Bagisto normally
composer update bagisto/bagisto

# 2. Copy the vendor file (it might get removed)
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache

# 4. Restart
php artisan octane:reload  # or php artisan serve
```

✅ **Bagisto updated, plugin still works!**

---

### PART 4: When You Update Your Plugin

#### On Your Development Machine

```bash
# 1. Make changes to your plugin
cd packages/Webkul/GraphQLChannelExtension
# ... edit files ...

# 2. Commit and push
git add .
git commit -m "Your update message"
git push origin main
```

#### On Production/Other Servers

```bash
# 1. Pull the updates
cd packages/Webkul/GraphQLChannelExtension
git pull origin main

# 2. Refresh autoload
cd ../../..
composer dump-autoload

# 3. Clear caches
php artisan cache:clear
php artisan config:clear

# Done!
```

---

## 🚀 Quick Command Reference

### Push Plugin to GitHub
```bash
cd packages/Webkul/GraphQLChannelExtension
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/bagisto-graphql-channel-extension.git
git push -u origin main
```

### Install on Fresh Bagisto
```bash
cd /path/to/bagisto
curl -o install-plugin.sh https://raw.githubusercontent.com/YOUR_USERNAME/bagisto-graphql-channel-extension/main/install-from-github.sh
chmod +x install-plugin.sh
./install-plugin.sh
```

### Update Bagisto
```bash
composer update bagisto/bagisto
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache
```

### Update Plugin
```bash
cd packages/Webkul/GraphQLChannelExtension
git pull origin main
cd ../../..
composer dump-autoload && php artisan cache:clear
```

---

## 📋 What You Need to Remember

### 1. **One File Goes to Vendor**

This file:
```
packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql
```

Must be copied to:
```
vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql
```

**When?**
- After fresh installation
- After Bagisto updates (if missing)

### 2. **Everything Else is Automatic**

- ✅ Middleware auto-injects
- ✅ Queries auto-register
- ✅ No config file edits needed

### 3. **Your Plugin Survives Updates**

- ✅ In `packages/` directory (safe)
- ✅ Not in `vendor/` directory (gets updated)

---

## 🎯 Complete Example Workflow

### Scenario: New Production Server

```bash
# 1. Install Bagisto
composer create-project bagisto/bagisto my-store
cd my-store

# 2. Configure Bagisto
php artisan bagisto:install
# ... database setup, etc ...

# 3. Install your plugin
curl -o install-plugin.sh https://raw.githubusercontent.com/YOUR_USERNAME/bagisto-graphql-channel-extension/main/install-from-github.sh
chmod +x install-plugin.sh
./install-plugin.sh

# 4. Done! Your channel plugin is working!
```

### Scenario: Update Bagisto

```bash
# Update Bagisto
composer update bagisto/bagisto

# Restore plugin file
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache

# Restart
php artisan octane:reload
```

---

## ✅ Success Checklist

### Initial Setup
- [ ] Created GitHub repository
- [ ] Pushed plugin to GitHub
- [ ] Updated install-from-github.sh with your username
- [ ] Pushed the update

### Fresh Installation Test
- [ ] Cloned Bagisto
- [ ] Ran installation script
- [ ] Vendor file exists
- [ ] GraphQL queries work

### Update Test
- [ ] Updated Bagisto
- [ ] Copied vendor file
- [ ] Queries still work

---

## 📚 More Documentation

- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Detailed deployment guide
- **[WORKFLOW_DIAGRAM.md](WORKFLOW_DIAGRAM.md)** - Visual workflow
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick commands
- **[README.md](README.md)** - Full plugin documentation

---

## 🆘 If Something Goes Wrong

### Plugin not working after install

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
php artisan package:discover
```

### Vendor file missing

```bash
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan lighthouse:clear-cache
```

### GraphQL errors

```bash
tail -f storage/logs/laravel.log
```

Then clear everything:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan lighthouse:clear-cache
composer dump-autoload
```

---

## 🎉 You're All Set!

You now have:
- ✅ Plugin on GitHub
- ✅ One-command installation
- ✅ Update-safe setup
- ✅ Works everywhere

**Just pull from GitHub whenever you need it!** 🚀
