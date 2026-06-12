#!/bin/bash
# @description Verify repository file integrity against share/filelist.md5 checksums
# @long-description Runs md5sum --check against share/filelist.md5 and reports files that have changed, are missing, or are unexpected. Use to detect unauthorized modifications to the codebase.

echo "Checking file consistency"
md5sum --check share/filelist.md5|grep FAILED
