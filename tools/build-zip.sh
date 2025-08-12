#!/usr/bin/env bash
set -euo pipefail
SLUG="${SLUG:-mailhealth-lite}"
VERSION="${VERSION:-0.9.0}"
OUT="../${SLUG}-${VERSION}.zip"
rm -f "$OUT"
zip -r "$OUT"   ${SLUG}.php readme.txt src assets languages screenshot-*.png   -x "*/.DS_Store" "*/node_modules/*" "*/vendor/*" "*/assets-wporg/*"
echo "Built $OUT"
