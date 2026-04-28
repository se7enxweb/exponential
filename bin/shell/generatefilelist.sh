#!/bin/bash
# generatefilelist.sh — generate share/filelist.md5 for the current release.
#
# Run from the document root once per release (after all files are in their
# final state) so that the eZ admin Setup › System Upgrade › File consistency
# check shows green.  Uses standard md5sum output format expected by
# eZMD5::checkMD5Sums.
#
# IMPORTANT: Run from the directory that Apache/PHP-FPM uses as the document
# root.  On this server, that is edit.alpha.example.com/, NOT alpha.example.com/,
# because edit.alpha.example.com/ contains symlinks for most files and its own
# .htaccess — PHP's cwd during a web request is edit.alpha.example.com/.
#
# Usage:
#   cd /var/www/vhosts/example.com/doc/edit.alpha.example.com
#   bash bin/shell/generatefilelist.sh [--dry-run] [--help]

OUTPUT_FILE="share/filelist.md5"

# ── Colour helpers (borrowed from common.sh style) ────────────────────────
RES_COL=60
MOVE_TO_COL="echo -en \\033[${RES_COL}G"
SETCOLOR_SUCCESS="echo -en \\033[1;32m"
SETCOLOR_FAILURE="echo -en \\033[1;31m"
SETCOLOR_WARNING="echo -en \\033[1;35m"
SETCOLOR_NORMAL="echo -en \\033[0;39m"

echo_success() { $MOVE_TO_COL; $SETCOLOR_SUCCESS; echo -n "[ OK ]"; $SETCOLOR_NORMAL; echo; }
echo_failure() { $MOVE_TO_COL; $SETCOLOR_FAILURE; echo -n "[FAIL]"; $SETCOLOR_NORMAL; echo; }
echo_warning() { $MOVE_TO_COL; $SETCOLOR_WARNING; echo -n "[WARN]"; $SETCOLOR_NORMAL; echo; }

# ── Option parsing ────────────────────────────────────────────────────────
DRY_RUN=0

for arg in "$@"; do
    case "$arg" in
        --dry-run)
            DRY_RUN=1
            ;;
        --help|-h)
            echo "Usage: $0 [options]"
            echo
            echo "  Generates share/filelist.md5 from the current state of all"
            echo "  reachable files in the document root.  Symlinks are followed"
            echo "  so that hashes match exactly what PHP's md5_file() sees."
            echo
            echo "  Run from the Apache/PHP document root, e.g.:"
            echo "    cd /var/www/vhosts/alpha.example.com/doc/edit.alpha.example.com"
            echo "    bash bin/shell/generatefilelist.sh"
            echo
            echo "Options:"
            echo "  --dry-run    Preview the file count without writing anything"
            echo "  --help, -h   This message"
            echo
            echo "Excluded paths (never hashed):"
            echo "  .git/   var/   tmp/   vendor/   extension/*/vendor/"
            echo "  share/filelist.md5  (cannot self-reference)"
            echo
            exit 0
            ;;
        *)
            echo "$arg: unknown option — run $0 --help"
            exit 1
            ;;
    esac
done

# ── Sanity: must be run from site root ────────────────────────────────────
if [[ ! -f "index.php" || ! -d "share" ]]; then
    echo "ERROR: Run this script from the site root (directory containing index.php and share/)."
    exit 1
fi

# ── Build the file list ───────────────────────────────────────────────────
echo -n "Collecting files to hash..."

# Use -L to follow symbolic links during traversal — this is essential when
# running from a directory (e.g. edit.alpha.example.com) that uses symlinks for
# most of its content.  md5sum also follows symlinks for individual files, so
# the stored hashes will match exactly what PHP's md5_file() sees.
# Excluded paths: generated/runtime dirs, third-party deps, and filelist.md5
# itself (its own hash cannot be stored correctly since it changes on write).
mapfile -t FILES < <(
    find -L . -type f \
        -not -path './.git/*' \
        -not -path './var/*' \
        -not -path './tmp/*' \
        -not -path './vendor/*' \
        -not -path './extension/*/vendor/*' \
        -not -path './share/filelist.md5' \
        -not -name '*.pyc' \
        | sed 's~^\./~~' \
        | sort
)

FILE_COUNT="${#FILES[@]}"

if [[ "$FILE_COUNT" -eq 0 ]]; then
    echo_failure
    echo "ERROR: No files found. Are you in the site root?"
    exit 1
fi

echo -n "  found ${FILE_COUNT} files"
echo_success

# ── Dry-run exit ──────────────────────────────────────────────────────────
if [[ "$DRY_RUN" -eq 1 ]]; then
    echo
    echo "Dry run — no files written."
    echo "Would write ${FILE_COUNT} hashes to: ${OUTPUT_FILE}"
    exit 0
fi

# ── Hash and write ────────────────────────────────────────────────────────
echo -n "Generating MD5 hashes (this may take a moment)..."

TMPFILE="${OUTPUT_FILE}.tmp.$$"
md5sum "${FILES[@]}" > "$TMPFILE" 2>/dev/null
MD5_EXIT=$?

if [[ "$MD5_EXIT" -ne 0 ]]; then
    rm -f "$TMPFILE"
    echo_failure
    echo "ERROR: md5sum failed (exit code ${MD5_EXIT})."
    exit 1
fi

echo_success

# ── Atomic replace ────────────────────────────────────────────────────────
echo -n "Writing ${OUTPUT_FILE}..."
mv "$TMPFILE" "$OUTPUT_FILE"
if [[ $? -ne 0 ]]; then
    echo_failure
    echo "ERROR: Could not write ${OUTPUT_FILE}. Check permissions."
    rm -f "$TMPFILE"
    exit 1
fi
echo_success

# ── Summary ───────────────────────────────────────────────────────────────
echo
echo "Done.  ${FILE_COUNT} file hashes written to ${OUTPUT_FILE}."
echo "Run 'bash bin/shell/verifyfiles.sh' to confirm the check passes."
echo
