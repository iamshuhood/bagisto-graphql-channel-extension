# GraphQL Channel Extension for Bagisto

**Channel detection plugin for Bagisto GraphQL API - Zero config, update-safe!**

## 🎯 What This Does

- ✅ **Automatic channel detection** via hostname or X-Channel header
- ✅ **Channel-aware caching** for GraphQL responses
- ✅ **Zero configuration** - works automatically
- ✅ **Update-safe** - survives Bagisto updates

---

## 🚀 Installation (One Command!)

```bash
cd /path/to/your/bagisto
curl -o install-plugin.sh https://raw.githubusercontent.com/iamshuhood/bagisto-graphql-channel-extension/main/install-from-github.sh
chmod +x install-plugin.sh
./install-plugin.sh
```

**Done!** No config editing needed. Works on any server, any Bagisto installation.

---

## 🔄 After Bagisto Update

When you update Bagisto, just copy one file back:

```bash
composer update bagisto/bagisto
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan cache:clear && php artisan config:clear && php artisan lighthouse:clear-cache
```

**That's it!** Your plugin keeps working.

---

## 📋 GraphQL Queries

### Detect Channel Automatically

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

### Query by Channel Code

```graphql
query {
  channelByCode(code: "default") {
    id
    code
    name
  }
}
```

### Query by Hostname

```graphql
query {
  channelByHostname(hostname: "example.com") {
    id
    code
    name
  }
}
```

---

## 🧪 Testing

```bash
# Start server
php artisan serve

# Test with X-Channel header
curl -X POST http://127.0.0.1:8000/graphql \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { id code name } }"}'

# Or run test script
bash packages/Webkul/GraphQLChannelExtension/test.sh
```

---

## 🔧 How It Works

The plugin automatically:
1. **Injects middleware** into Lighthouse at runtime (no config edits!)
2. **Detects channel** via X-Channel header OR hostname
3. **Caches responses** with channel-specific keys
4. **Registers queries** for channel operations

**Everything is automatic!** Just install and go.

---

## 📦 What You Get

```
GraphQLChannelExtension/
├── README.md              ← You're reading it
├── composer.json          ← Package definition
├── install-from-github.sh ← One-command installer
├── test.sh                ← Test script
├── config/                ← Optional config
└── src/
    ├── Providers/         ← Auto-configuration
    ├── Http/Middleware/   ← Channel detection & caching
    ├── Queries/           ← GraphQL resolvers
    └── graphql/           ← Schema definitions
```

---

## 🆘 Troubleshooting

### Plugin not working?

```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
php artisan lighthouse:clear-cache
```

### After Bagisto update, queries not found?

```bash
# Copy the schema file
cp packages/Webkul/GraphQLChannelExtension/src/graphql/channel.graphql \
   vendor/bagisto/graphql-api/src/graphql/shop/common/
php artisan lighthouse:clear-cache
```

### Check if plugin is active

```bash
php artisan package:discover | grep GraphQLChannelExtension
```

---

## ✅ Summary

**Installation:** One command  
**Configuration:** Zero  
**Update-safe:** Yes  
**Works everywhere:** Yes  

```bash
curl -o install-plugin.sh https://raw.githubusercontent.com/iamshuhood/bagisto-graphql-channel-extension/main/install-from-github.sh
chmod +x install-plugin.sh
./install-plugin.sh
```

That's it! 🎉

