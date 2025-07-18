#!/bin/bash

# Script de vérification des releases
echo "🔍 Checking release status..."

# Check if there are uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ There are uncommitted changes"
    git status --short
    exit 1
fi

# Check if tests pass
echo "Running tests..."
if ! composer run test; then
    echo "❌ Tests are failing"
    exit 1
fi

# Check code quality
echo "Checking code quality..."
if ! composer run pint; then
    echo "❌ Code formatting issues found"
    exit 1
fi

if ! composer run phpstan; then
    echo "❌ Static analysis issues found"
    exit 1
fi

echo "✅ Ready for release!"
