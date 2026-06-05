#!/usr/bin/env bash
# export_mysql.sh — dump all eZ Publish tables from MySQL to NDJSON via mysql2ndjson.py
#
# Usage:
#   bash export_mysql.sh [outdir]
#
# Defaults to ./json_export if outdir is not specified.
# Credentials are read from the environment or the defaults below.
#
# Environment overrides:
#   MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

MYSQL_HOST="${MYSQL_HOST:-localhost}"
MYSQL_USER="${MYSQL_USER:-pm}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-publishing\$8088}"
MYSQL_DB="${MYSQL_DB:-exp}"
OUTDIR="${1:-./json_export}"

echo "Exporting MySQL database '${MYSQL_DB}' → ${OUTDIR} ..."

python3 "${SCRIPT_DIR}/mysql2ndjson.py" \
    --host    "${MYSQL_HOST}" \
    --user    "${MYSQL_USER}" \
    --password "${MYSQL_PASSWORD}" \
    --db      "${MYSQL_DB}" \
    --outdir  "${OUTDIR}"

echo "Export complete. Files written to ${OUTDIR}/"
