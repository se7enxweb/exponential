#!/usr/bin/env bash
# A git pre-commit hook for an Exponential installation: checks the templates the commit changes (.tpl, .stpl, .twig,
# .html.twig) with exp:template:check and refuses the commit when one does not compile.
#
# Install it from the installation root:
#   cp doc/eng-us/examples/hooks/template-check-pre-commit.sh .git/hooks/pre-commit
#   chmod +x .git/hooks/pre-commit
#
# Skip it once with: git commit --no-verify
# Guide: doc/eng-us/guides/template-syntax-check.md
set -u
cd "$(git rev-parse --show-toplevel)" || exit 1

# nothing to check when the commit changes no template
if ! git diff --cached --name-only --diff-filter=ACMR | grep -qE '\.(tpl|stpl|twig)$'; then
    exit 0
fi

./console exp:template:check --changed-since=HEAD --level=compile --format=text
status=$?
case $status in
    0) exit 0 ;;
    1) echo "pre-commit: a changed template does not pass exp:template:check (see above)." >&2 ;;
    3) echo "pre-commit: a template engine is not available; ./console exp:template:engines says which." >&2 ;;
    *) echo "pre-commit: exp:template:check ended with exit code $status." >&2 ;;
esac
exit 1
