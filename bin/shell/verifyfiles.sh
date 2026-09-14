#!/bin/bash
# @description Verify repository file integrity against share/filelist.md5 checksums
# @long-description Runs md5sum --check against share/filelist.md5 and reports files that have changed or gone missing, with a summary and a meaningful exit status. Use to detect unauthorized modifications to the codebase, and to confirm a freshly generated manifest is clean.
#
# Run from the same directory generatefilelist.sh was run from - the document
# root that Apache/PHP-FPM uses - or the paths in the manifest will not resolve
# and every file will look as though it has gone.
#
# Usage:
#   bash bin/shell/verifyfiles.sh            # summary, and what changed
#   bash bin/shell/verifyfiles.sh --quiet    # exit status only
#
# Exit status:
#   0  every file matches
#   1  something changed, went missing, or the manifest could not be read

QUIET=0
for arg in "$@"; do
    case "$arg" in
        --quiet|-q) QUIET=1 ;;
        --help|-h)
            sed -n '2,20p' "$0" | sed 's/^# \{0,1\}//'
            exit 0
            ;;
        *)
            echo "$arg: unknown option — run $0 --help"
            exit 1
            ;;
    esac
done

MANIFEST="share/filelist.md5"

if [[ ! -f "$MANIFEST" ]]; then
    echo "ERROR: $MANIFEST not found. Run from the document root, and generate it with:"
    echo "  bash bin/shell/generatefilelist.sh"
    exit 1
fi

[[ "$QUIET" -eq 0 ]] && echo "Checking file consistency against $MANIFEST"

RESULT=$( md5sum --check "$MANIFEST" 2>&1 )

EXPECTED=$( grep -c . < "$MANIFEST" )
CHANGED=$( grep -c ': FAILED$' <<< "$RESULT" )
MISSING=$( grep -c 'FAILED open or read' <<< "$RESULT" )

if [[ "$QUIET" -eq 0 ]]; then
    if [[ "$CHANGED" -gt 0 ]]; then
        echo
        echo "Changed since the manifest was written:"
        grep ': FAILED$' <<< "$RESULT" | sed 's/: FAILED$//' | sed 's/^/  /'
    fi
    if [[ "$MISSING" -gt 0 ]]; then
        echo
        echo "In the manifest but no longer on disk:"
        grep 'FAILED open or read' <<< "$RESULT" | sed 's/: FAILED open or read$//' | sed 's/^/  /'
    fi

    echo
    echo "  ${EXPECTED} files in the manifest"
    echo "  ${CHANGED} changed"
    echo "  ${MISSING} missing"
fi

if [[ "$CHANGED" -gt 0 || "$MISSING" -gt 0 ]]; then
    [[ "$QUIET" -eq 0 ]] && echo && echo "File consistency check FAILED."
    exit 1
fi

[[ "$QUIET" -eq 0 ]] && echo && echo "File consistency check passed."
exit 0
