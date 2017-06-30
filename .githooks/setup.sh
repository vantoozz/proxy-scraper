#!/usr/bin/env bash

if [ -d ".git/hooks" ]; then
    cp .githooks/change-detector .git/hooks/change-detector

    cp .githooks/pre-commit .git/hooks/pre-commit
    chmod +x .git/hooks/pre-commit

    cp .githooks/post-merge .git/hooks/post-merge
    chmod +x .git/hooks/post-merge

    cp .githooks/post-checkout .git/hooks/post-checkout
    chmod +x .git/hooks/post-checkout
fi