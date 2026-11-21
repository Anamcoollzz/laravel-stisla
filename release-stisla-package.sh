#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PKG="$ROOT/stisla-package"

if [[ ! -e "$PKG/.git" ]]; then
    echo "[ERROR] Git repository not found in \"$PKG\"." >&2
    exit 1
fi

if [[ -f "$ROOT/sync-stisla-stubs.sh" ]]; then
    bash "$ROOT/sync-stisla-stubs.sh"
fi

LAST_TAG="$(git -C "$PKG" describe --tags --abbrev=0 2>/dev/null || true)"
if [[ -n "$LAST_TAG" ]]; then
    echo "Latest tag: $LAST_TAG"
else
    echo "No existing tags found."
fi

TAG="${1:-}"
if [[ -z "$TAG" ]]; then
    read -r -p "Enter new tag (e.g. v1.0.2): " TAG
fi
if [[ -z "$TAG" ]]; then
    echo "[ERROR] Tag cannot be empty." >&2
    exit 1
fi

MSG="${2:-}"
if [[ -z "$MSG" ]]; then
    read -r -p "Enter commit message [Release $TAG]: " MSG
fi
if [[ -z "$MSG" ]]; then
    MSG="Release $TAG"
fi

git -C "$PKG" add -A
if ! git -C "$PKG" diff --cached --quiet; then
    if ! git -C "$PKG" commit -m "$MSG"; then
        echo "[ERROR] Git commit failed." >&2
        exit 1
    fi
else
    echo "No file changes to commit. Proceeding to tag..."
fi

if ! git -C "$PKG" push origin master; then
    echo "[ERROR] Failed to push branch master." >&2
    exit 1
fi

if ! git -C "$PKG" tag "$TAG"; then
    echo "[ERROR] Failed to create tag $TAG." >&2
    exit 1
fi

if ! git -C "$PKG" push origin "$TAG"; then
    echo "[ERROR] Failed to push tag $TAG." >&2
    exit 1
fi

echo
echo "Successfully released $TAG to origin!"
