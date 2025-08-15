#!/usr/bin/env bash
set -euo pipefail
SLUG="${SLUG:-mailhealth-lite}"
VERSION="${VERSION:-0.9.0}"
OUT_DIR="svn-${SLUG}-${VERSION}"
rm -rf "$OUT_DIR"
mkdir -p "$OUT_DIR/trunk" "$OUT_DIR/tags/${VERSION}" "$OUT_DIR/assets"

# Copy plugin files to trunk and tag
rsync -a --delete ./ "$OUT_DIR/trunk/" --exclude 'vendor/dealerdirect' --exclude 'vendor/phpcsstandards' --exclude 'vendor/squizlabs' --exclude 'vendor/wp-coding-standards' --exclude 'phpcs.xml.dist'  --exclude '.git*' --exclude $OUT_DIR --exclude 'tools' --exclude 'assets-wporg' --exclude '*.zip'
rsync -a --delete "$OUT_DIR/trunk/" "$OUT_DIR/tags/${VERSION}/"

# Copy wp.org assets (banners/icons/screenshots) to SVN /assets
if [ -d assets-wporg ]; then
  rsync -a --delete assets-wporg/ "$OUT_DIR/assets/"
fi

echo "SVN export ready at $OUT_DIR"
