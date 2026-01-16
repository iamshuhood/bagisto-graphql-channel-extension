# MIGRATION GUIDE: Moving to GraphQL Channel Extension Package

## Overview

This guide helps you migrate from vendor-modified files to the new `GraphQLChannelExtension` package. This approach keeps your customizations separate from vendor code, making updates safer and easier to maintain.

## What Was Changed?

### Before (Modified Vendor Files)
- ❌ Modified `vendor/bagisto/graphql-api/src/Http/Middleware/ChannelMiddleware.php`
- ❌ Modified `vendor/bagisto/graphql-api/src/Http/Middleware/GraphQLCacheMiddleware.php`
- ❌ Modified `vendor/bagisto/graphql-api/src/Queries/Shop/Common/ChannelQuery.php`
- ❌ Modified `vendor/bagisto/graphql-api/src/graphql/shop/common/channel.graphql`
- ❌ Modified `config/lighthouse.php`

### After (Plugin Package)
- ✅ All functionality in `packages/Webkul/GraphQLChannelExtension/`
- ✅ No vendor modifications
- ✅ Update-safe
- ✅ Can be version controlled
- ✅ Can be distributed as a package

## Migration Steps

### Step 1: Install the Package

```bash
cd /path/to/your/bagisto/root
bash packages/Webkul/GraphQLChannelExtension/install.sh
```

Or manually:

```bash
# Install via composer
composer require webkul/graphql-channel-extension:@dev

# Publish configuration
php artisan vendor:publish --tag=graphql-channel-extension-config --force

# Clear caches
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Step 2: Verify Configuration

Check that `/config/lighthouse.php` now references the plugin middleware:

```php
'middleware' => [
    // ... other middleware

    // Should use the plugin namespace
    Webkul\GraphQLChannelExtension\Http\Middleware\ChannelMiddleware::class,
    
    // ... other middleware
    
    Webkul\GraphQLChannelExtension\Http\Middleware\GraphQLCacheMiddleware::class,
    
    // ... rest of middleware
],

'namespaces' => [
    'queries' => [
        'Webkul\\GraphQLAPI\\Queries',
        'Webkul\\GraphQLChannelExtension\\Queries',  // Plugin queries added
    ],
    // ... rest of namespaces
],
```

### Step 3: Test the Functionality

1. **Test Channel Detection by Header:**

```bash
curl -X POST http://your-domain.com/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { id code name hostname } }"}'
```

2. **Test Channel Detection by Hostname:**

```bash
curl -X POST http://your-domain.com/graphql \
  -H "Content-Type: application/json" \
  -H "Host: your-channel-domain.com" \
  -d '{"query": "{ currentChannel { id code name hostname } }"}'
```

3. **Test Channel Queries:**

```graphql
query {
  channelByCode(code: "default") {
    id
    code
    name
    hostname
  }
}

query {
  channelByHostname(hostname: "example.com") {
    id
    code
    name
    hostname
  }
}

query {
  currentChannel {
    id
    code
    name
    hostname
  }
}
```

### Step 4: Revert Vendor Modifications (Optional)

If you want to restore the vendor files to their original state:

```bash
# Re-install the graphql-api package to restore original files
composer reinstall bagisto/graphql-api

# Or if you have the original files backed up, restore them
```

**Important:** Only do this AFTER confirming the plugin works correctly!

### Step 5: Remove Old Schema Import (If Needed)

If the original vendor package has the channel.graphql import, you may need to comment it out or ensure it doesn't conflict:

In `vendor/bagisto/graphql-api/src/graphql/schema.graphql`, check for:
```graphql
#import /shop/common/channel.graphql
```

The plugin will handle channel queries independently.

## Package Structure

```
packages/Webkul/GraphQLChannelExtension/
├── composer.json                          # Package definition
├── README.md                               # Package documentation
├── install.sh                              # Installation script
├── MIGRATION.md                            # This file
├── publishable/
│   └── config/
│       └── lighthouse.php                  # Enhanced config
└── src/
    ├── Providers/
    │   └── GraphQLChannelExtensionServiceProvider.php
    ├── Http/
    │   └── Middleware/
    │       ├── ChannelMiddleware.php       # Channel detection
    │       └── GraphQLCacheMiddleware.php  # Channel-aware cache
    ├── Queries/
    │   └── Shop/
    │       └── Common/
    │           └── ChannelQuery.php        # Query resolvers
    └── graphql/
        └── channel.graphql                 # GraphQL schema
```

## Benefits of This Approach

### 1. **Update Safety**
- Running `composer update` won't overwrite your customizations
- Vendor package updates are safe

### 2. **Version Control**
- All customizations are in `packages/` directory
- Can be committed to git
- Can be reviewed in pull requests

### 3. **Maintainability**
- Clear separation between core and custom code
- Easy to understand what's custom
- Easy to document and share

### 4. **Distribution**
- Can be published to Packagist
- Can be shared across multiple projects
- Can be installed via composer on other instances

### 5. **Testing**
- Can write tests specifically for the extension
- Easier to isolate and test functionality

## Troubleshooting

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### Issue: Middleware not executing

**Solution:**
1. Check `config/lighthouse.php` has the correct middleware classes
2. Clear config cache: `php artisan config:clear`
3. Restart server (Octane): `php artisan octane:reload`

### Issue: GraphQL queries not found

**Solution:**
1. Verify `config/lighthouse.php` includes the plugin queries namespace
2. Clear schema cache: `php artisan lighthouse:clear-cache`
3. Check the graphql schema is properly imported

### Issue: Channel not detected

**Solution:**
1. Verify channel hostname in database matches request host
2. Check middleware order in `lighthouse.php` (ChannelMiddleware should be before LocaleMiddleware)
3. Test with explicit X-Channel header first

## Rollback Plan

If you need to rollback:

1. **Remove the package:**
   ```bash
   composer remove webkul/graphql-channel-extension
   ```

2. **Restore original config:**
   ```bash
   # Restore from backup or git
   git checkout config/lighthouse.php
   ```

3. **Clear caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   composer dump-autoload
   ```

4. **Restore vendor modifications:**
   - Copy back your modified vendor files
   - Or re-apply the modifications manually

## Support

For issues or questions:
- Check the [README.md](README.md) for usage examples
- Review Bagisto GraphQL API documentation
- Check Laravel Lighthouse documentation

## Next Steps

After successful migration:

1. ✅ Test all channel-related functionality
2. ✅ Update your deployment scripts to include the package
3. ✅ Document any custom configuration
4. ✅ Consider creating tests for the extension
5. ✅ Share the package with your team

## Version History

- **v1.0.0**: Initial release with channel detection and caching
  - ChannelMiddleware for hostname and header detection
  - GraphQLCacheMiddleware with channel-aware caching
  - ChannelQuery resolvers
  - GraphQL schema definitions
