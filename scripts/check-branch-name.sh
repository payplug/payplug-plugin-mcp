#!/bin/sh
# Check that the current branch name follows the project naming convention.
# Expected pattern: (feature|fix|hotfix|refactor|release)/(PRE|MAG|SYL|SMP)-{ticket-id}[-slug]

BRANCH=$(git symbolic-ref --short HEAD 2>/dev/null)

if [ -z "$BRANCH" ]; then
    echo "Could not determine current branch name."
    exit 1
fi

echo "$BRANCH" | grep -qE '^(feature|fix|hotfix|refactor|release)/(PRE|MAG|SYL|SMP)-[0-9]+(-[a-z0-9]+)*$'

if [ $? -ne 0 ]; then
    echo ""
    echo "  Branch name does not respect the naming convention."
    echo "  Current : $BRANCH"
    echo "  Expected: (feature|fix|hotfix|refactor|release)/(PRE|MAG|SYL|SMP)-{ticket-id}[-slug]"
    echo "  Example : feature/PRE-1234-add-refund-tool"
    echo ""
    exit 1
fi
