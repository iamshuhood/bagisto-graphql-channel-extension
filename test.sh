#!/bin/bash

# Quick test script for GraphQL Channel Extension

echo "🧪 Testing GraphQL Channel Extension..."
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Get the GraphQL endpoint
GRAPHQL_URL="http://127.0.0.1:8000/graphql"

echo -e "${BLUE}Test 1: Current Channel Query${NC}"
echo "Query: { currentChannel { id code name } }"
echo ""

RESULT=$(curl -s -X POST "$GRAPHQL_URL" \
  -H "Content-Type: application/json" \
  -H "X-Channel: default" \
  -d '{"query": "{ currentChannel { id code name } }"}')

echo "$RESULT" | python3 -m json.tool 2>/dev/null || echo "$RESULT"
echo ""

if echo "$RESULT" | grep -q "currentChannel"; then
    echo -e "${GREEN}✅ Test 1 PASSED${NC}"
else
    echo -e "${RED}❌ Test 1 FAILED${NC}"
fi

echo ""
echo "---"
echo ""

echo -e "${BLUE}Test 2: Channel By Code Query${NC}"
echo "Query: { channelByCode(code: \"default\") { id code name } }"
echo ""

RESULT2=$(curl -s -X POST "$GRAPHQL_URL" \
  -H "Content-Type: application/json" \
  -d '{"query": "{ channelByCode(code: \"default\") { id code name } }"}')

echo "$RESULT2" | python3 -m json.tool 2>/dev/null || echo "$RESULT2"
echo ""

if echo "$RESULT2" | grep -q "channelByCode"; then
    echo -e "${GREEN}✅ Test 2 PASSED${NC}"
else
    echo -e "${RED}❌ Test 2 FAILED${NC}"
fi

echo ""
echo "---"
echo ""
echo -e "${GREEN}Testing complete!${NC}"
echo ""
echo "Note: Make sure your server is running on port 8000"
echo "Run: php artisan serve"
