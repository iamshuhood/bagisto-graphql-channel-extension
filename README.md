# GraphQL Channel Extension for Bagisto

This package provides enhanced channel support for Bagisto's GraphQL API, including:

- ✅ **Automatic channel detection** via hostname or headers
- ✅ **Channel-aware GraphQL caching** for better performance
- ✅ **Zero configuration** - works out of the box
- ✅ **No vendor modifications** - clean and update-safe
- ✅ **Support for multi-channel storefronts**
- ✅ **GitHub ready** - push once, use everywhere

## 🚀 Quick Start

### For Fresh Bagisto Installation (FROM GITHUB)

```bash
# 1. Navigate to your Bagisto root
cd /path/to/bagisto

# 2. Download and run installation script
curl -o install-plugin.sh https://raw.githubusercontent.com/YOUR_USERNAME/bagisto-graphql-channel-extension/main/install-from-github.sh
chmod +x install-plugin.sh
./install-plugin.sh

# Done! Plugin installed from GitHub
```

### For Current Installation (LOCAL)

**Option 1: Using the installation script**
```bash
cd /path/to/your/bagisto/root
bash packages/Webkul/GraphQLChannelExtension/install.sh
```

**Option 2: Manual installation**
```bash
composer require webkul/graphql-channel-extension:@dev
mkdir -p vendor/bagisto/graphql-api/src/graphql/shop/common
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache
composer dump-autoload
```

That's it! No configuration files to edit.

## 📚 Documentation

- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Complete GitHub deployment workflow
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick command reference
- **[MIGRATION.md](MIGRATION.md)** - Migrating from vendor modifications
- **[COMPLETE_SETUP_GUIDE.md](COMPLETE_SETUP_GUIDE.md)** - Comprehensive guide

## 🔄 Updating Bagisto?

```bash
# Update Bagisto
composer update bagisto/bagisto

# Restore schema file (if removed)
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/

# Clear caches
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache
```

Your plugin survives all updates! ✅

## ✨ How It Works

The package automatically:
1. **Injects middleware** into Lighthouse's GraphQL pipeline
2. **Registers query resolvers** for channel operations
3. **Configures caching** with channel awareness
4. **No manual config changes needed!**

## 📋 Features

### 1. Automatic Channel Detection

The middleware automatically detects the current channel based on:

**Priority 1: X-Channel Header** (Explicit channel code)
```bash
curl -X POST http://your-site.com/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { code name } }"}'
```

**Priority 2: Hostname** (Automatic detection)
```bash
# Automatically detects channel based on request hostname
curl -X POST http://example.com/graphql \
  -H "Content-Type: application/json" \
  -d '{"query": "{ currentChannel { code name } }"}'
```

### 2. Channel-Aware Caching

GraphQL responses are automatically cached with channel context:
- Cache keys include channel identifier
- Prevents cross-channel data leakage
- Respects channel-specific settings

### 3. GraphQL Queries

```graphql
# Get current channel (based on request context)
query {
  currentChannel {
    id
    code
    name
    hostname
    description
  }
}

# Get channel by code
query {
  channelByCode(code: "default") {
    id
    code
    name
    hostname
  }
}

# Get channel by hostname
query {
  channelByHostname(hostname: "example.com") {
    id
    code
    name
    hostname
  }
}
```

## 🔧 Configuration (Optional)

While the package works with zero configuration, you can customize it by creating a `.env` file with:

```bash
# Enable/disable the extension
GRAPHQL_CHANNEL_EXTENSION_ENABLED=true

# Enable/disable channel-aware caching
GRAPHQL_CHANNEL_CACHE_ENABLED=true

# Cache TTL in seconds (default: 24 hours)
GRAPHQL_CHANNEL_CACHE_TTL=86400
```

Advanced configuration in `config/graphql-channel-extension.php` (published optionally):
```php
return [
    'channel_detection' => [
        'priority' => ['header', 'hostname'],  // Detection order
        'header_name' => 'x-channel',          // Header to check
        'auto_detect_hostname' => true,        // Enable hostname detection
        'strip_www' => true,                   // Strip www. from hostnames
    ],
];
```

## 📦 Package Structure

```
packages/Webkul/GraphQLChannelExtension/
├── composer.json                     # Package definition
├── README.md                         # This file
├── MIGRATION.md                      # Migration from vendor mods
├── SETUP.md                          # Setup completion guide
├── install.sh                        # Installation script
├── cleanup-vendor.sh                 # Clean up old modifications
├── config/
│   └── channel-extension.php         # Package configuration
└── src/
    ├── Providers/
    │   └── GraphQLChannelExtensionServiceProvider.php  # Auto-registers everything
    ├── Http/
    │   └── Middleware/
    │       ├── ChannelMiddleware.php                   # Channel detection
    │       └── GraphQLCacheMiddleware.php              # Channel-aware cache
    ├── Queries/
    │   └── Shop/
    │       └── Common/
    │           └── ChannelQuery.php                    # Query resolvers
    └── graphql/
        └── channel.graphql                             # GraphQL schema
```

## 🎯 Why This Approach?

### Before (Vendor Modifications)
- ❌ Modified files in `vendor/` directory
- ❌ Changes lost on `composer update`
- ❌ Hard to track and maintain
- ❌ Not portable across projects
- ❌ Manual config editing required

### After (Plugin Package)
- ✅ All code in `packages/` directory
- ✅ Update-safe and maintainable
- ✅ Version controlled
- ✅ Portable and reusable
- ✅ Zero configuration needed
- ✅ Automatic setup via Service Provider

## 🧹 Cleaning Up Old Vendor Modifications

If you previously modified vendor files, clean them up:

```bash
bash packages/Webkul/GraphQLChannelExtension/cleanup-vendor.sh
```

This will:
1. Backup your current vendor files
2. Restore original files via `composer reinstall`
3. Keep your plugin functionality intact

## 🧪 Testing

### Test Channel Detection by Header
```bash
curl -X POST http://127.0.0.1:8000/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { code name hostname } }"}'
```

### Test Channel Detection by Hostname
```bash
curl -X POST http://your-domain.com/graphql \
  -H "Content-Type: application/json" \
  -H "Host: your-channel-domain.com" \
  -d '{"query": "{ currentChannel { code name } }"}'
```

### Test in GraphQL Playground
1. Open your GraphQL endpoint (usually `/graphql`)
2. Add header: `X-Channel: default`
3. Run query:
```graphql
query {
  currentChannel {
    id
    code
    name
    hostname
  }
}
```

## 🔄 Updates

To update the plugin:

```bash
cd packages/Webkul/GraphQLChannelExtension
git pull  # If using git

# Or if installed via composer
composer update webkul/graphql-channel-extension
```

## 🐛 Troubleshooting

### Issue: Channel not detected

**Solution:**
1. Clear all caches: `php artisan cache:clear && php artisan config:clear`
2. Check channel hostname in database matches your domain
3. Test with explicit `X-Channel` header first
4. Verify middleware is loaded: `php artisan route:list --middleware`

### Issue: GraphQL queries not found

**Solution:**
```bash
php artisan lighthouse:clear-cache
php artisan config:clear
composer dump-autoload
```

### Issue: Caching issues

**Solution:**
```bash
# Disable cache temporarily
php artisan cache:clear

# Check cache config
php artisan config:show cache
```

## 📚 Documentation

- [MIGRATION.md](MIGRATION.md) - Migrating from vendor modifications
- [SETUP.md](SETUP.md) - Setup completion guide
- [Bagisto GraphQL API Docs](https://devdocs.bagisto.com)
- [Laravel Lighthouse Docs](https://lighthouse-php.com)

## 🤝 Contributing

Feel free to submit issues and pull requests!

## 📄 License

MIT License - feel free to use in your projects!
