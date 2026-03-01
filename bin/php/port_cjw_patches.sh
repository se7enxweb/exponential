#!/usr/bin/env bash
# =============================================================================
# Port Script: cjw-network ezpublish7x patches -> exponential
# Branch: exp_cjw_network_cms_improvements_and_features_2026_02
# Generated: 2026-02-28
# Based on: ezpublish7x commits after c079b52d59 (Feb 1, 2025)
# =============================================================================
set -euo pipefail

# Optional: pass a legacy vendor prefix string as $1 to have it replaced with
# its Exponential equivalent (exp_ece) in all ported files and patches.
# Leave empty or omit to skip that substitution.
VENDOR_LEGACY_STR="${1:-}"

# Build optional extra sed expressions from the legacy vendor string argument.
# Used in both apply_string_replacements() and the patch pre-processing block.
extra_seds=()
if [ -n "${VENDOR_LEGACY_STR}" ]; then
    _uc=$(echo "$VENDOR_LEGACY_STR" | tr '[:lower:]' '[:upper:]')
    _lc=$(echo "$VENDOR_LEGACY_STR" | tr '[:upper:]' '[:lower:]')
    extra_seds=(-e "s/${_uc}/exp_ece/g" -e "s/${_lc}/exp_ece/g")
fi

EZP7X_REPO="/web/vh/se7enx.com/doc/platform.alpha.se7enx.com/repos/ezpublish7x"
EXP_REPO="/web/vh/se7enx.com/doc/platform.alpha.se7enx.com/repos/exponential"
BASE_COMMIT="c079b52d59950cc39576f0864221fa2293b3a278"
LOG_FILE="${EXP_REPO}/doc/bc/6.0/port-cjw-patches-log-2026-02.txt"
CONFLICT_LOG="${EXP_REPO}/doc/bc/6.0/port-cjw-conflicts-2026-02.txt"

# Ensure doc dir exists
mkdir -p "${EXP_REPO}/doc/bc/6.0"

echo "===========================================================================" | tee -a "$LOG_FILE"
echo "Port Log: cjw-network patches -> exponential" | tee -a "$LOG_FILE"
echo "Date: $(date)" | tee -a "$LOG_FILE"
echo "Base commit in ezpublish7x: ${BASE_COMMIT}" | tee -a "$LOG_FILE"
echo "===========================================================================" | tee -a "$LOG_FILE"

# The list of files to port (excluding vendor, README, LICENSE, version, package.ini)
FILES=(
    "design/admin3/templates/content/browse_mode_list.tpl"
    "index.php"
    "kernel/classes/ezcontentcachemanager.php"
    "kernel/classes/ezcontentobject.php"
    "kernel/classes/ezcontentobjecttreenode.php"
    "kernel/classes/ezcontentobjectversion.php"
    "kernel/classes/ezcontentupload.php"
    "kernel/classes/eznodeviewfunctions.php"
    "kernel/classes/ezorder.php"
    "kernel/classes/ezrole.php"
    "kernel/classes/ezsiteaccess.php"
    "kernel/classes/notification/eznotificationschedule.php"
    "kernel/collaboration/item.php"
    "kernel/content/collectedinfo.php"
    "kernel/content/download.php"
    "kernel/content/edit.php"
    "kernel/content/ezcontentoperationcollection.php"
    "kernel/content/history.php"
    "kernel/content/module.php"
    "kernel/content/operation_definition.php"
    "kernel/content/removeeditversion.php"
    "kernel/content/versionview.php"
    "kernel/content/versionviewframe.php"
    "kernel/private/classes/ezautoloadgenerator.php"
    "kernel/private/classes/ezpkernelweb.php"
    "kernel/role/view.php"
    "kernel/search/plugins/ezsearchengine/ezsearchengine.php"
    "kernel/user/edit.php"
    "kernel/workflow/processlist.php"
    "lib/ezdb/classes/ezdb.php"
    "lib/ezdbschema/classes/ezpgsqlschema.php"
    "lib/ezfile/classes/ezdir.php"
    "lib/ezfile/classes/ezlog.php"
    "lib/ezimage/classes/ezimagemanager.php"
    "lib/ezsession/classes/ezsession.php"
    "lib/ezutils/classes/ezdebug.php"
    "lib/ezutils/classes/ezexpiryhandler.php"
    "lib/ezutils/classes/ezextension.php"
    "lib/ezutils/classes/ezini.php"
    "lib/ezutils/classes/ezsys.php"
    "settings/notification.ini"
    "settings/site.ini"
)

APPLIED=0
FAILED=0
CONFLICTS=()

apply_string_replacements() {
    local file="$1"
    # Replace identifiers per naming rules:
    # 'cjw_network' -> 'exp' (must come before 'cjw')
    # 'cjw' -> 'exp'
    # Optional legacy vendor prefix -> 'exp_ece' (supplied via script argument)
    # 'JAC_PATCH' patterns -> 'exp_feature' with preserved numbering
    # German-text identifiers handled in code separately
    sed -i \
        -e 's/cjw_network/exp/g' \
        -e 's/CJW_NETWORK/exp/g' \
        -e 's/CJW_Network/exp/g' \
        -e 's/cjw/exp/g' \
        -e 's/CJW/exp/g' \
        "${extra_seds[@]+${extra_seds[@]}}" \
        -e 's/###JAC_PATCH_G_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_g\1_ez\2###/g' \
        -e 's/###JAC_SECURITY_PATCH_S_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_security_s\1_ez\2###/g' \
        -e 's/###JAC_BACKPORT_PATCH_B_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_backport_b\1_ez\2###/g' \
        -e 's/JAC_PATCH_G_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_g\1_ez\2/g' \
        -e 's/JAC_SECURITY_PATCH_S_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_security_s\1_ez\2/g' \
        -e 's/JAC_BACKPORT_PATCH_B_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_backport_b\1_ez\2/g' \
        -e 's/JAC_PATCH/exp_feature/g' \
        -e 's/jac_patch/exp_feature/g' \
        -e 's/JAC_/exp_/g' \
        -e 's/jac_/exp_/g' \
        "$file"
}

PATCH_TMPDIR=$(mktemp -d)
trap "rm -rf $PATCH_TMPDIR" EXIT

for RELATIVE_FILE in "${FILES[@]}"; do
    EZP7X_FILE="${EZP7X_REPO}/${RELATIVE_FILE}"
    EXP_FILE="${EXP_REPO}/${RELATIVE_FILE}"
    PATCH_FILE="${PATCH_TMPDIR}/$(echo "${RELATIVE_FILE}" | tr '/' '_').patch"

    echo "" | tee -a "$LOG_FILE"
    echo "--- Processing: ${RELATIVE_FILE}" | tee -a "$LOG_FILE"

    # Check if file exists in both repos
    if [ ! -f "$EZP7X_FILE" ]; then
        echo "  SKIP: File does not exist in ezpublish7x" | tee -a "$LOG_FILE"
        continue
    fi

    if [ ! -f "$EXP_FILE" ]; then
        echo "  NEW: File does not exist in exponential - copying" | tee -a "$LOG_FILE"
        mkdir -p "$(dirname "$EXP_FILE")"
        cp "$EZP7X_FILE" "$EXP_FILE"
        apply_string_replacements "$EXP_FILE"
        echo "  COPIED and replacements applied" | tee -a "$LOG_FILE"
        APPLIED=$((APPLIED + 1))
        continue
    fi

    # Generate diff of net patch effect in ezpublish7x (base commit to HEAD)
    git -C "$EZP7X_REPO" diff "${BASE_COMMIT}..HEAD" -- "${RELATIVE_FILE}" > "$PATCH_FILE" 2>/dev/null || true

    if [ ! -s "$PATCH_FILE" ]; then
        echo "  NOCHANGE: No diff from base to HEAD in ezpublish7x" | tee -a "$LOG_FILE"
        continue
    fi

    # Apply string replacements to the patch itself before applying
    sed -i \
        -e 's/cjw_network/exp/g' \
        -e 's/CJW_NETWORK/exp/g' \
        -e 's/cjw/exp/g' \
        -e 's/CJW/exp/g' \
        "${extra_seds[@]+${extra_seds[@]}}" \
        -e 's/###JAC_PATCH_G_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_g\1_ez\2###/g' \
        -e 's/###JAC_SECURITY_PATCH_S_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_security_s\1_ez\2###/g' \
        -e 's/###JAC_BACKPORT_PATCH_B_\([0-9]*\)_EZ_\([0-9.]*\)###/###exp_feature_backport_b\1_ez\2###/g' \
        -e 's/JAC_PATCH_G_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_g\1_ez\2/g' \
        -e 's/JAC_SECURITY_PATCH_S_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_security_s\1_ez\2/g' \
        -e 's/JAC_BACKPORT_PATCH_B_\([0-9]*\)_EZ_\([0-9.]*\)/exp_feature_backport_b\1_ez\2/g' \
        -e 's/JAC_PATCH/exp_feature/g' \
        -e 's/jac_patch/exp_feature/g' \
        -e 's/JAC_/exp_/g' \
        -e 's/jac_/exp_/g' \
        "$PATCH_FILE"

    # Try to apply the patch (dry run first)
    if patch -p1 --dry-run -d "$EXP_REPO" < "$PATCH_FILE" > /dev/null 2>&1; then
        # Apply for real
        patch -p1 -d "$EXP_REPO" < "$PATCH_FILE" >> "$LOG_FILE" 2>&1
        # Now apply string replacements to the target file
        apply_string_replacements "$EXP_FILE"
        echo "  OK: Patch applied and string replacements done" | tee -a "$LOG_FILE"
        APPLIED=$((APPLIED + 1))
    elif patch -p1 --dry-run --fuzz=3 -d "$EXP_REPO" < "$PATCH_FILE" > /dev/null 2>&1; then
        # Apply with fuzz tolerance
        patch -p1 --fuzz=3 -d "$EXP_REPO" < "$PATCH_FILE" >> "$LOG_FILE" 2>&1
        apply_string_replacements "$EXP_FILE"
        echo "  OK (fuzz=3): Patch applied with context fuzz" | tee -a "$LOG_FILE"
        APPLIED=$((APPLIED + 1))
    else
        # Log conflict
        echo "  CONFLICT: Could not apply patch automatically" | tee -a "$LOG_FILE"
        echo "  CONFLICT: ${RELATIVE_FILE}" | tee -a "$CONFLICT_LOG"
        CONFLICTS+=("$RELATIVE_FILE")
        FAILED=$((FAILED + 1))
        # Copy diff for manual review
        cp "$PATCH_FILE" "${PATCH_TMPDIR}/../conflict_${RELATIVE_FILE//\//_}.patch" 2>/dev/null || true
    fi
done

echo "" | tee -a "$LOG_FILE"
echo "===========================================================================" | tee -a "$LOG_FILE"
echo "SUMMARY" | tee -a "$LOG_FILE"
echo "Applied: ${APPLIED}" | tee -a "$LOG_FILE"
echo "Conflicts/Failed: ${FAILED}" | tee -a "$LOG_FILE"
if [ ${#CONFLICTS[@]} -gt 0 ]; then
    echo "Conflicting files:" | tee -a "$LOG_FILE"
    for f in "${CONFLICTS[@]}"; do
        echo "  - $f" | tee -a "$LOG_FILE"
    done
fi
echo "===========================================================================" | tee -a "$LOG_FILE"
echo ""
echo "Port complete. See log: $LOG_FILE"
echo "Conflicts log: $CONFLICT_LOG"
