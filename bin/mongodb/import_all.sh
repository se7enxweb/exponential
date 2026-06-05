#!/usr/bin/env bash
# import_all.sh — import all NDJSON files into MongoDB in correct dependency order.
#
# Usage:
#   bash import_all.sh [indir]
#
# Default indir: ./json_fixed
# Credentials / URI can be overridden via environment:
#   MONGO_URI  (default: mongodb://db:publishing$8088@localhost:27017/exp)
#   MONGO_DB   (default: exp)

set -euo pipefail

URI="${MONGO_URI:-mongodb://db:publishing\$8088@localhost:27017/exp}"
DB="${MONGO_DB:-exp}"
INDIR="${1:-./json_fixed}"

# Ordered list — dependencies (referenced collections) must come before dependents
TABLES=(
    ezcontentlanguage
    ezsection
    ezcontentclass
    ezcontentclass_attribute
    ezcontentclass_classgroup
    ezcontentclass_name
    ezcontentobject
    ezcontentobject_version
    ezcontentobject_attribute
    ezcontentobject_tree
    ezcontentobject_name
    ezcontentobject_link
    ezurlalias_ml
    ezurlalias_ml_incr
    ezurlalias
    eznode_assignment
    ezuser
    ezuser_setting
    ezrole
    ezpolicy
    ezpolicy_limitation
    ezpolicy_limitation_value
    ezuser_role
    ezsubtree_limitation_item
    ezcobj_state_group
    ezcobj_state
    ezcobj_state_link
    ezcontentbrowsebookmark
    ezcontentcache_list
    ezpersistentcookie
)

echo "Importing into MongoDB database '${DB}' from ${INDIR}/ ..."

for TABLE in "${TABLES[@]}"; do
    FILE="${INDIR}/${TABLE}.ndjson"
    if [[ ! -f "$FILE" ]]; then
        echo "  SKIP $TABLE (no file)"
        continue
    fi
    echo "  Importing ${TABLE} ..."
    mongoimport \
        --uri    "$URI" \
        --db     "$DB" \
        --collection "$TABLE" \
        --file   "$FILE" \
        --type   json \
        --mode   upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

# Import any remaining files not in the ordered list above
for FILE in "${INDIR}"/*.ndjson; do
    [[ -f "$FILE" ]] || continue
    TABLE=$(basename "$FILE" .ndjson)
    if printf '%s\n' "${TABLES[@]}" | grep -qx "$TABLE"; then
        continue
    fi
    echo "  Importing (remaining) ${TABLE} ..."
    mongoimport \
        --uri    "$URI" \
        --db     "$DB" \
        --collection "$TABLE" \
        --file   "$FILE" \
        --type   json \
        --mode   upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

echo "Done."
