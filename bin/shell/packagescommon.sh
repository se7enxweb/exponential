#!/bin/bash
# @description Shared functions library sourced by package build scripts (not executable)
# @long-description Provides common shell functions used by makestylepackages.sh, makeaddonpackages.sh, and related package assembly scripts. Source this file — do not run it directly.

PACKAGES="blog corporate forum gallery intranet news shop"
ALL_PACKAGES="$PACKAGES plain"

ADDON_PACKAGES="contacts contact_us files forum gallery links media news poll products weblog"
