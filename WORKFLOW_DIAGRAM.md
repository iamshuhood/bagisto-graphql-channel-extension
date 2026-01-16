# 🔄 Deployment Workflow Diagram

## Overview: How to Deploy Your Plugin

```
┌─────────────────────────────────────────────────────────────────┐
│                   CURRENT PROJECT (First Time)                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ git push
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      GITHUB REPOSITORY                          │
│  github.com/YOUR_USERNAME/bagisto-graphql-channel-extension    │
└─────────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┼─────────────┐
                │             │             │
                │             │             │
                ▼             ▼             ▼
┌───────────────────┐  ┌──────────────┐  ┌──────────────┐
│  Fresh Bagisto    │  │  Production  │  │   Staging    │
│   Installation    │  │    Server    │  │    Server    │
└───────────────────┘  └──────────────┘  └──────────────┘
         │                     │                 │
         │ git clone           │ git clone       │ git clone
         ▼                     ▼                 ▼
    [Plugin Installed]    [Plugin Installed]  [Plugin Installed]
```

---

## Workflow Steps

### Step 1: Initial Setup (Do Once)

```
Your Computer
└── packages/Webkul/GraphQLChannelExtension/
    ├── git init
    ├── git add .
    ├── git commit -m "Initial commit"
    ├── git remote add origin https://github.com/...
    └── git push origin main
    
    ✅ Plugin now on GitHub
```

### Step 2: Install Anywhere

```
New Bagisto Installation
└── Run: ./install-from-github.sh
    
    What it does:
    1. git clone [your plugin]
    2. composer require webkul/graphql-channel-extension:@dev
    3. Copy vendor file
    4. Clear caches
    
    ✅ Plugin ready to use!
```

### Step 3: Update Bagisto (Anytime)

```
Existing Installation
└── composer update bagisto/bagisto
    └── cp [schema file to vendor]
        └── php artisan cache:clear
        
        ✅ Bagisto updated, plugin still works!
```

### Step 4: Update Plugin (When you make changes)

```
Your Computer (Development)
└── packages/Webkul/GraphQLChannelExtension/
    ├── [make changes]
    ├── git commit -m "New feature"
    └── git push origin main

Production Server
└── cd packages/Webkul/GraphQLChannelExtension/
    └── git pull origin main
        └── composer dump-autoload
        
        ✅ Plugin updated everywhere!
```

---

## File Flow

### What Goes Where

```
┌─────────────────────────────────────────────────────────────┐
│                    YOUR GITHUB REPO                         │
│                                                             │
│  All plugin files:                                          │
│  ├── composer.json                                          │
│  ├── README.md                                              │
│  ├── src/                                                   │
│  │   ├── Providers/                                         │
│  │   ├── Http/Middleware/                                   │
│  │   ├── Queries/                                           │
│  │   └── graphql/channel.graphql ◄── This one is important │
│  └── install-from-github.sh                                 │
└─────────────────────────────────────────────────────────────┘
                         │
                         │ git clone
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                  BAGISTO INSTALLATION                       │
│                                                             │
│  packages/Webkul/GraphQLChannelExtension/                  │
│  └── [All files from GitHub] ◄── Plugin source             │
│                                                             │
│  vendor/bagisto/graphql-api/src/graphql/shop/common/       │
│  └── channel.graphql ◄── Copy here (one file only!)        │
│                                                             │
│  config/lighthouse.php ◄── CLEAN (no edits needed!)        │
└─────────────────────────────────────────────────────────────┘
```

---

## The One Vendor File

```
┌──────────────────────────────────────────────────────────────┐
│  WHY: Lighthouse auto-loads GraphQL schemas from here       │
│  WHAT: channel.graphql (GraphQL schema definitions)         │
│  WHERE: vendor/bagisto/graphql-api/src/graphql/shop/common/ │
│  WHEN: After installation OR after Bagisto update           │
│  HOW: cp packages/.../channel.graphql vendor/.../           │
└──────────────────────────────────────────────────────────────┘
```

**Important:** Everything else is auto-configured! This is the ONLY file you need to copy to vendor.

---

## Complete Deployment Timeline

### Day 1: Development

```
[You] 
  └─► Create plugin
      └─► Test locally
          └─► Push to GitHub ✅
```

### Day 2: First Production Deploy

```
[Production Server]
  └─► Clone Bagisto
      └─► Run install-from-github.sh
          └─► Plugin working ✅
```

### Day 30: Bagisto Update Available

```
[Production Server]
  └─► composer update bagisto/bagisto
      └─► Copy vendor file
          └─► Still working ✅
```

### Day 60: You Add New Feature

```
[You]
  └─► Edit plugin
      └─► git push

[Production Server]
  └─► git pull
      └─► New feature live ✅
```

### Day 90: New Server/Environment

```
[New Server]
  └─► Clone Bagisto
      └─► Run install-from-github.sh
          └─► Plugin working ✅
```

---

## Key Points

### ✅ What You Push to GitHub
```
packages/Webkul/GraphQLChannelExtension/
└── Everything in this directory
```

### ✅ What Gets Installed
```
1. Plugin → packages/Webkul/GraphQLChannelExtension/
2. Schema → vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql
```

### ✅ What Survives Updates
```
1. Plugin → YES (in packages/)
2. Schema → NO (in vendor/, must re-copy)
```

### ✅ What Needs Configuration
```
NOTHING! Everything is auto-configured! 🎉
```

---

## Commands Cheat Sheet

| Task | Command |
|------|---------|
| **Push to GitHub** | `git push origin main` |
| **Install on Fresh** | `./install-from-github.sh` |
| **Update Bagisto** | `composer update bagisto/bagisto` then `cp [schema]` |
| **Update Plugin** | `git pull origin main` then `composer dump-autoload` |
| **Test Working** | `curl -H "X-Channel: default" http://localhost:8000/graphql ...` |
| **Fix Issues** | `php artisan cache:clear && composer dump-autoload` |

---

## Visual: Installation Process

```
┌────────┐
│ START  │
└───┬────┘
    │
    ├─► Fresh Bagisto?
    │   ├─► YES ──► ./install-from-github.sh ──► DONE ✅
    │   │
    │   └─► NO
    │       │
    │       ├─► Has Plugin? ──► Update Plugin ──► DONE ✅
    │       │
    │       └─► No Plugin ──► Run install script ──► DONE ✅
    │
    └─► After Bagisto Update?
        └─► Copy vendor file ──► DONE ✅
```

---

## Common Scenarios

### Scenario 1: New Team Member

```bash
# They clone your project
git clone https://github.com/you/your-bagisto-project.git
cd your-bagisto-project

# Install dependencies
composer install

# Install plugin
./install-from-github.sh

# Done! They're ready to develop
```

### Scenario 2: CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
steps:
  - name: Install dependencies
    run: composer install
    
  - name: Install channel plugin
    run: ./install-from-github.sh
    
  - name: Deploy
    run: # your deployment commands
```

### Scenario 3: Multiple Environments

```bash
# Development
./install-from-github.sh

# Staging
./install-from-github.sh

# Production
./install-from-github.sh

# Same command everywhere! 🎉
```

---

**Remember:** Your plugin is now a proper package that lives on GitHub and can be installed anywhere with one command!
