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
# Run with no arguments.  The defaults are the ones a release wants: everything
# that is part of the installation is hashed, and everything that is not is left
# out - .git of the root and of every extension that is its own checkout, var/,
# tmp/, vendor/, node_modules/, tool caches, per-machine editor and agent
# settings, the ai/ working directory, and editor leftovers such as *~ and #*#.
# Listing those only fills the admin's File consistency check with files nobody
# can act on.
#
# Usage:
#   cd /var/www/vhosts/example.com/doc/edit.alpha.example.com
#   bash bin/shell/generatefilelist.sh
#   bash bin/shell/generatefilelist.sh --list      # what is being left out
#   bash bin/shell/generatefilelist.sh --dry-run   # count without writing

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
#
# Running this with no arguments has to produce a manifest that is worth
# checking against.  Anything listed here is either not part of the
# installation at all - a repository's own book-keeping, an editor's leftovers,
# a working directory - or is generated, and listing it only fills the admin's
# File consistency check with files nobody can act on.
#
# Keys are short labels; each holds one or more find patterns, separated by
# spaces.  Use --include=<key> to put a group back in.
declare -A EXCLUDE_PATHS
declare -A EXCLUDE_NAME_PATTERNS

# Repository book-keeping.  The nested pattern matters: every extension that is
# its own checkout has a .git of its own, and without it the manifest fills up
# with COMMIT_EDITMSG, index and refs/ - files that change on every commit.
EXCLUDE_PATHS[git]='./.git/* */.git/* ./.svn/* */.svn/* ./.hg/* */.hg/*'

# Written by the installation as it runs.
EXCLUDE_PATHS[var]='./var/*'
EXCLUDE_PATHS[tmp]='./tmp/*'

# Installed by a package manager, not shipped by hand.
EXCLUDE_PATHS[vendor]='./vendor/* ./node_modules/* */node_modules/*'
EXCLUDE_PATHS[extension_vendor]='./extension/*/vendor/* ./extension/*/node_modules/*'

# Caches that tools drop in the root.
EXCLUDE_PATHS[caches]='./.phpunit.cache/* */.phpunit.cache/* ./.sass-cache/* ./.pytest_cache/*'

# Per-machine settings for editors and agents.  These are nobody's business but
# the machine they sit on, and they are not part of any release.
EXCLUDE_PATHS[local]='./.claude/* ./.devin/* ./.idea/* ./.vscode/* ./.cursor/* ./.aider*'

# Working directories that are not part of a release.
EXCLUDE_PATHS[work]='./ai/*'

# What editors leave behind: vim and emacs backups, merge leftovers, saved
# copies.  A manifest listing settings/site.ini.append.php~ tells the admin
# nothing they can do anything about.
EXCLUDE_NAME_PATTERNS[backups]='*~ #*# .#* *.orig *.rej *.bak *.swp *.swo *.save'

# Logs and the operating system's own droppings.
EXCLUDE_NAME_PATTERNS[logs]='*.log'
EXCLUDE_NAME_PATTERNS[os]='.DS_Store Thumbs.db desktop.ini'

# Fixed exclusions that cannot be overridden (filelist cannot self-reference).
EXCLUDE_FIXED=('./share/filelist.md5')
EXCLUDE_NAMES=('*.pyc' 'filelist.md5.tmp.*')

# Exclusions added at runtime with --exclude=<value>. Both may be repeated.
EXCLUDE_EXTRA_PATHS=()
EXCLUDE_EXTRA_NAMES=()

# ── Option parsing ────────────────────────────────────────────────────────
DRY_RUN=0
LIST_SKIPPED=0
DO_EXTENSIONS=0

for arg in "$@"; do
    case "$arg" in
        --dry-run)
            DRY_RUN=1
            ;;
        --list)
            LIST_SKIPPED=1
            DRY_RUN=1
            ;;
        --extensions)
            DO_EXTENSIONS=1
            ;;
        --include=*)
            KEY="${arg#--include=}"
            MATCHED=0
            if [[ -v EXCLUDE_PATHS[$KEY] ]]; then
                unset "EXCLUDE_PATHS[$KEY]"
                MATCHED=1
            fi
            if [[ -v EXCLUDE_NAME_PATTERNS[$KEY] ]]; then
                unset "EXCLUDE_NAME_PATTERNS[$KEY]"
                MATCHED=1
            fi
            if [[ "$MATCHED" -eq 0 ]]; then
                echo "Warning: --include=$KEY does not match any default exclusion"
                echo "         known keys: ${!EXCLUDE_PATHS[*]} ${!EXCLUDE_NAME_PATTERNS[*]}"
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
            echo "  With no arguments it does the right thing: everything that is"
            echo "  part of the installation is hashed, and everything that is not -"
            echo "  repository book-keeping, editor leftovers, caches, per-machine"
            echo "  settings, working directories - is left out."
            echo
            echo "Options:"
            echo "  --dry-run           Preview the file count without writing anything"
            echo "  --list              List every file the exclusions leave out, then stop"
            echo "  --extensions        Also rewrite extension/*/share/filelist.md5 for"
            echo "                      extensions that already ship one.  Off by default:"
            echo "                      an extension's manifest is the record of what it"
            echo "                      shipped with, and rewriting it erases the evidence"
            echo "                      that somebody patched that extension in place."
            echo "  --include=<key>     Put one group of exclusions back in."
            echo "                      May be specified multiple times."
            echo "  --exclude=<value>   Exclude something extra. May be specified"
            echo "                      multiple times, and combined with --include."
            echo "                        ai            a directory, wherever it sits"
            echo "                        ./ai/*        a find -path pattern, as given"
            echo "                        '*.log'       a file name glob, at any depth"
            echo "  --help, -h          This message"
            echo
            echo "Left out by default (use --include=<key> to put a group back):"
            echo "  --include=git                .git/ .svn/ .hg/, at the root and inside"
            echo "                               every extension that is its own checkout"
            echo "  --include=var                var/"
            echo "  --include=tmp                tmp/"
            echo "  --include=vendor             vendor/ node_modules/"
            echo "  --include=extension_vendor   extension/*/vendor/ and node_modules/"
            echo "  --include=caches             .phpunit.cache/ .sass-cache/ .pytest_cache/"
            echo "  --include=local              .claude/ .devin/ .idea/ .vscode/ .cursor/"
            echo "  --include=work               ai/"
            echo "  --include=backups            *~  #*#  .#*  *.orig  *.rej  *.bak"
            echo "                               *.swp  *.swo  *.save"
            echo "  --include=logs               *.log"
            echo "  --include=os                 .DS_Store  Thumbs.db  desktop.ini"
            echo
            echo "Always excluded (cannot be overridden):"
            echo "  share/filelist.md5   (cannot self-reference)"
            echo "  *.pyc, filelist.md5.tmp.*"
            echo
            echo "Examples:"
            echo "  # Include top-level vendor/ in the hash:"
            echo "  bash bin/shell/generatefilelist.sh --include=vendor"
            echo
            echo "  # Include both vendor/ and extension vendor dirs:"
            echo "  bash bin/shell/generatefilelist.sh --include=vendor --include=extension_vendor"
            echo
            echo "  # See exactly what the defaults are keeping out:"
            echo "  bash bin/shell/generatefilelist.sh --list"
            echo
            echo "  # Hash the editor backups too, for an audit:"
            echo "  bash bin/shell/generatefilelist.sh --include=backups"
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
echo "Hashing: $( pwd )"
echo -n "Collecting files to hash..."

# Assemble find arguments dynamically from the remaining exclusion maps.
# Use -L to follow symbolic links — essential when the document root uses
# symlinks for most content; hashes then match exactly what PHP md5_file() sees.
FIND_ARGS=()

# read -ra splits on spaces without expanding the wildcards, which a bare
# unquoted expansion would turn into a list of matching files.
for group in "${EXCLUDE_PATHS[@]}"; do
    read -ra patterns <<< "$group"
    for pattern in "${patterns[@]}"; do
        FIND_ARGS+=( -not -path "$pattern" )
    done
done
for group in "${EXCLUDE_NAME_PATTERNS[@]}"; do
    read -ra patterns <<< "$group"
    for pattern in "${patterns[@]}"; do
        FIND_ARGS+=( -not -name "$pattern" )
    done
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

# How much the exclusions kept out, so a default run explains itself rather
# than leaving the operator to wonder what was skipped.
TOTAL_COUNT=$( find -L . -type f 2>/dev/null | wc -l )
SKIPPED=$(( TOTAL_COUNT - FILE_COUNT ))
if [[ "$SKIPPED" -gt 0 ]]; then
    echo "  ${SKIPPED} left out by the exclusions (--list shows which, --help why)"
fi

if [[ ${#EXCLUDE_EXTRA_PATHS[@]} -gt 0 || ${#EXCLUDE_EXTRA_NAMES[@]} -gt 0 ]]; then
    echo "Extra exclusions applied:"
    for pattern in "${EXCLUDE_EXTRA_PATHS[@]}"; do
        echo "  path  $pattern"
    done
    for name in "${EXCLUDE_EXTRA_NAMES[@]}"; do
        echo "  name  $name"
    done
fi

# ── What was left out ─────────────────────────────────────────────────────
if [[ "$LIST_SKIPPED" -eq 1 ]]; then
    echo
    echo "Left out of the manifest:"
    comm -23 \
        <( find -L . -type f 2>/dev/null | sed 's~^\./~~' | sort ) \
        <( printf '%s\n' "${FILES[@]}" | sort ) \
        | sed 's/^/  /'
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
ERRFILE="${OUTPUT_FILE}.err.$$"

# Through xargs rather than one command line: an installation with tens of
# thousands of files would otherwise fail with "Argument list too long", and it
# would fail only on the installations large enough to matter.
printf '%s\0' "${FILES[@]}" | xargs -0 --no-run-if-empty md5sum > "$TMPFILE" 2>"$ERRFILE"
MD5_EXIT=${PIPESTATUS[1]}

HASHED=$( wc -l < "$TMPFILE" )
UNREADABLE=$( wc -l < "$ERRFILE" )

if [[ "$HASHED" -eq 0 ]]; then
    rm -f "$TMPFILE" "$ERRFILE"
    echo_failure
    echo "ERROR: md5sum produced nothing (exit code ${MD5_EXIT})."
    exit 1
fi

if [[ "$UNREADABLE" -gt 0 ]]; then
    echo_warning
    echo "  ${UNREADABLE} file(s) could not be read and are not in the manifest:"
    head -5 "$ERRFILE" | sed 's/^/    /'
    [[ "$UNREADABLE" -gt 5 ]] && echo "    ... and $(( UNREADABLE - 5 )) more"
else
    echo_success
fi

rm -f "$ERRFILE"

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

# ── Extensions that carry a manifest of their own ─────────────────────────
#
# The admin's File consistency check reads the root manifest and then one per
# active extension that has one. Rewriting those is deliberate, never automatic:
# an extension's manifest records what it shipped with, and a file of its that
# no longer matches is exactly what somebody needs to know before an upgrade
# replaces it.
if [[ "$DO_EXTENSIONS" -eq 1 ]]; then
    echo
    echo "Rewriting extension manifests:"

    EXT_DONE=0
    for extManifest in extension/*/share/filelist.md5; do
        [[ -f "$extManifest" ]] || continue

        extDir="${extManifest%/share/filelist.md5}"
        echo -n "  ${extDir}..."

        mapfile -t EXT_FILES < <(
            cd "$extDir" && find -L . -type f "${FIND_ARGS[@]}" | sed 's~^\./~~' | sort
        )

        if [[ "${#EXT_FILES[@]}" -eq 0 ]]; then
            echo_warning
            continue
        fi

        EXT_TMP="${extManifest}.tmp.$$"
        ( cd "$extDir" && printf '%s\0' "${EXT_FILES[@]}" | xargs -0 --no-run-if-empty md5sum ) \
            > "$EXT_TMP" 2>/dev/null

        if [[ -s "$EXT_TMP" ]] && mv "$EXT_TMP" "$extManifest"; then
            echo -n "  ${#EXT_FILES[@]} files"
            echo_success
            EXT_DONE=$(( EXT_DONE + 1 ))
        else
            rm -f "$EXT_TMP"
            echo_failure
        fi
    done

    echo "  ${EXT_DONE} extension manifest(s) rewritten."
fi

# ── Summary ───────────────────────────────────────────────────────────────
echo
echo "Done.  ${FILE_COUNT} file hashes written to ${OUTPUT_FILE}."
if [[ "$DO_EXTENSIONS" -eq 0 ]]; then
    echo "Extensions that ship a manifest of their own were left alone; add"
    echo "--extensions to rewrite those too."
fi
echo "Run 'bash bin/shell/verifyfiles.sh' to confirm the check passes."
echo
