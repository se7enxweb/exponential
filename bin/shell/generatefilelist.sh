#!/bin/bash
# @description Generate share/filelist.md5 file integrity checksums for the current release
# @long-description Walks the repository and computes MD5 checksums for all tracked files, writing the result to share/filelist.md5. This file is used by verifyfiles.sh to detect unauthorized modifications.
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

# ── Default exclusions ───────────────────────────────────────────────────
# Keys are short labels; values are the find -path patterns to exclude.
# Use --include=<key> to remove an entry at runtime.
declare -A EXCLUDE_PATHS
EXCLUDE_PATHS[git]='./.git/*'
EXCLUDE_PATHS[var]='./var/*'
EXCLUDE_PATHS[tmp]='./tmp/*'
EXCLUDE_PATHS[vendor]='./vendor/*'
EXCLUDE_PATHS[extension_vendor]='./extension/*/vendor/*'

# Fixed exclusions that cannot be overridden (filelist cannot self-reference).
EXCLUDE_FIXED=('./share/filelist.md5')
EXCLUDE_NAMES=('*.pyc')

# Exclusions added at runtime with --exclude=<value>. Both may be repeated.
EXCLUDE_EXTRA_PATHS=()
EXCLUDE_EXTRA_NAMES=()

# ── Option parsing ────────────────────────────────────────────────────────
DRY_RUN=0

for arg in "$@"; do
    case "$arg" in
        --dry-run)
            DRY_RUN=1
            ;;
        --include=*)
            KEY="${arg#--include=}"
            if [[ -v EXCLUDE_PATHS[$KEY] ]]; then
                unset "EXCLUDE_PATHS[$KEY]"
            else
                echo "Warning: --include=$KEY does not match any default exclusion (known keys: ${!EXCLUDE_PATHS[*]})"
            fi
            ;;
        --exclude=*)
            VALUE="${arg#--exclude=}"
            if [[ -z "$VALUE" ]]; then
                echo "ERROR: --exclude= needs a value — run $0 --help"
                exit 1
            fi
            if [[ "$VALUE" == \*.* && "$VALUE" != */* ]]; then
                # A bare glob such as *.log: match on the file name at any depth.
                EXCLUDE_EXTRA_NAMES+=( "$VALUE" )
            elif [[ "$VALUE" == */* || "$VALUE" == *\** ]]; then
                # Anything with a slash or a wildcard is used as a find -path
                # pattern as given, with ./ added so it anchors at the root.
                case "$VALUE" in
                    ./*|\**) ;;
                    *) VALUE="./$VALUE" ;;
                esac
                EXCLUDE_EXTRA_PATHS+=( "$VALUE" )
            else
                # A plain name such as ai or .git: exclude it wherever it sits,
                # at the root and nested inside extensions.
                EXCLUDE_EXTRA_PATHS+=( "./$VALUE/*" "*/$VALUE/*" )
            fi
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
            echo "  --dry-run           Preview the file count without writing anything"
            echo "  --include=<key>     Remove a path from the default exclusion list."
            echo "                      May be specified multiple times."
            echo "  --exclude=<value>   Exclude something extra. May be specified"
            echo "                      multiple times, and combined with --include."
            echo "                        ai            a directory, wherever it sits"
            echo "                        ./ai/*        a find -path pattern, as given"
            echo "                        '*.log'       a file name glob, at any depth"
            echo "  --help, -h          This message"
            echo
            echo "Default excluded paths (use --include=<key> to include):"
            echo "  --include=git                 .git/"
            echo "  --include=var                 var/"
            echo "  --include=tmp                 tmp/"
            echo "  --include=vendor              vendor/"
            echo "  --include=extension_vendor    extension/*/vendor/"
            echo
            echo "Always excluded (cannot be overridden):"
            echo "  share/filelist.md5   (cannot self-reference)"
            echo "  *.pyc"
            echo
            echo "Examples:"
            echo "  # Include top-level vendor/ in the hash:"
            echo "  bash bin/shell/generatefilelist.sh --include=vendor"
            echo
            echo "  # Include both vendor/ and extension vendor dirs:"
            echo "  bash bin/shell/generatefilelist.sh --include=vendor --include=extension_vendor"
            echo
            echo "  # Include extension/, and leave out working directories that"
            echo "  # are not part of a release:"
            echo "  bash bin/shell/generatefilelist.sh --include=extension --exclude=ai"
            echo
            echo "  # Several at once:"
            echo "  bash bin/shell/generatefilelist.sh --exclude=ai --exclude=node_modules \\"
            echo "                                    --exclude=.git --exclude='"'"'*.backup_*'"'"'"
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

# Assemble find arguments dynamically from the remaining exclusion maps.
# Use -L to follow symbolic links — essential when the document root uses
# symlinks for most content; hashes then match exactly what PHP md5_file() sees.
FIND_ARGS=()
for pattern in "${EXCLUDE_PATHS[@]}"; do
    FIND_ARGS+=( -not -path "$pattern" )
done
for path in "${EXCLUDE_FIXED[@]}"; do
    FIND_ARGS+=( -not -path "$path" )
done
for name in "${EXCLUDE_NAMES[@]}"; do
    FIND_ARGS+=( -not -name "$name" )
done
for pattern in "${EXCLUDE_EXTRA_PATHS[@]}"; do
    FIND_ARGS+=( -not -path "$pattern" )
done
for name in "${EXCLUDE_EXTRA_NAMES[@]}"; do
    FIND_ARGS+=( -not -name "$name" )
done

mapfile -t FILES < <(
    find -L . -type f "${FIND_ARGS[@]}" \
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

if [[ ${#EXCLUDE_EXTRA_PATHS[@]} -gt 0 || ${#EXCLUDE_EXTRA_NAMES[@]} -gt 0 ]]; then
    echo "Extra exclusions applied:"
    for pattern in "${EXCLUDE_EXTRA_PATHS[@]}"; do
        echo "  path  $pattern"
    done
    for name in "${EXCLUDE_EXTRA_NAMES[@]}"; do
        echo "  name  $name"
    done
fi

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
