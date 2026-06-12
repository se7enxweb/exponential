# @description Dump the current running site as static HTML for offline documentation
# @long-description Uses wget to recursively mirror the running site and produce a static HTML snapshot for offline documentation browsing. Requires the development site to be running.
# dump site for offline reading
# only parameter is url to site
# -Jo Henrik
wget -m -E --convert-links $1
