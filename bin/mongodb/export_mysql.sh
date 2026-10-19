#!/usr/bin/env bash
# export_mysql.sh — dump all Exponential tables from MySQL to NDJSON via mysql2ndjson.py
#
# Usage:
#   MYSQL_PASSWORD=... bash export_mysql.sh [outdir]
#
# Defaults to ./json_export if outdir is not specified.
#
# Environment:
#   MYSQL_HOST (default localhost), MYSQL_USER (default pm), MYSQL_DB (default exp)
#   MYSQL_PASSWORD  required; there is no default. Take it from your secret store, e.g.
#                   MYSQL_PASSWORD="$(cat /path/to/secret)" bash export_mysql.sh
#                   When it is not set and the script runs in a terminal, it asks for it (not echoed).
#
# The password is handed to mysql2ndjson.py in its environment, never on the command line, so it does not
# show in the process list or the shell history.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

MYSQL_HOST="${MYSQL_HOST:-localhost}"
MYSQL_USER="${MYSQL_USER:-pm}"
MYSQL_DB="${MYSQL_DB:-exp}"
OUTDIR="${1:-./json_export}"

if [ -z "${MYSQL_PASSWORD:-}" ]; then
    if [ -t 0 ]; then
        read -r -s -p "MySQL password for ${MYSQL_USER}@${MYSQL_HOST}: " MYSQL_PASSWORD
        echo
    fi
fi
if [ -z "${MYSQL_PASSWORD:-}" ]; then
    echo "FAIL: MYSQL_PASSWORD is not set. Set it in the environment (from your secret store) and run again:" >&2
    echo "      MYSQL_PASSWORD=... bash $0 ${OUTDIR}" >&2
    exit 1
fi
export MYSQL_PASSWORD

echo "Exporting MySQL database '${MYSQL_DB}' → ${OUTDIR} ..."

python3 "${SCRIPT_DIR}/mysql2ndjson.py" \
    --host    "${MYSQL_HOST}" \
    --user    "${MYSQL_USER}" \
    --db      "${MYSQL_DB}" \
    --outdir  "${OUTDIR}"

echo "Export complete. Files written to ${OUTDIR}/"
