#!/usr/bin/env bash

if [ -d ".git/hooks" ]; then
    cp bin/change-detector .git/hooks/change-detector

    cp bin/pre-commit .git/hooks/pre-commit
    chmod +x .git/hooks/pre-commit

    cp bin/post-merge .git/hooks/post-merge
    chmod +x .git/hooks/post-merge

    cp bin/post-checkout .git/hooks/post-checkout
    chmod +x .git/hooks/post-checkout
fi