#!/usr/bin/env bash
# validate_ndjson.sh — check each .ndjson file is valid JSON (one object per line)
#
# Usage:
#   bash validate_ndjson.sh [indir]
#
# Default indir: ./json_export
# Exit code: 0 if all files are valid, 1 if any errors found.

set -uo pipefail

INDIR="${1:-./json_export}"
ERRORS=0

for f in "${INDIR}"/*.ndjson; do
    [[ -f "$f" ]] || continue
    result=$(python3 - "$f" <<'PYEOF'
import sys, json
path = sys.argv[1]
bad = 0
with open(path) as fh:
    for i, line in enumerate(fh, 1):
        line = line.strip()
        if not line:
            continue
        try:
            json.loads(line)
        except Exception as e:
            print(f'  Line {i}: {e}')
            bad += 1
print(f'{bad} error(s)' if bad else 'OK')
PYEOF
)
    echo "$f: $result"
    if [[ "$result" != "OK" ]]; then
        ERRORS=1
    fi
done

if [[ $ERRORS -eq 0 ]]; then
    echo "All files valid."
    exit 0
else
    echo "Validation FAILED — see errors above."
    exit 1
fi
