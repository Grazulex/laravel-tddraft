#!/bin/bash

# Script de release automatique
set -e

VERSION="$1"

if [ -z "$VERSION" ]; then
    echo "Usage: $0 <version>"
    echo "Example: $0 1.0.0"
    exit 1
fi

echo "🚀 Preparing release $VERSION"

# Run all quality checks
echo "Running quality checks..."
composer run full

# Update version in relevant files if needed
echo "Preparing release files..."

# Create git tag
echo "Creating git tag..."
git add .
git commit -m "Prepare release $VERSION" || true
git tag -a "v$VERSION" -m "Release version $VERSION"

echo "✅ Release $VERSION ready!"
echo "Don't forget to:"
echo "1. git push origin main"
echo "2. git push origin v$VERSION"
echo "3. Create a GitHub release"
