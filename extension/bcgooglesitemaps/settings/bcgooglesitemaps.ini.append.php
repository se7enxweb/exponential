<?php /* #?ini charset="utf-8"?

[BCGoogleSitemapSettings]
# Content subtree NodeID to include in sitemap content
SitemapRootNodeID=2
# Sitemap link default protocol
Protocol=https
# Sitemap XML file name prefix
Filename=sitemap
# Sitemap XML file name suffix
Filesuffix=.xml
# Sitemap output directory path. Path to save sitemap into.
# Default is empty which causes code to use site.ini:[FileSettings] VarDir path
# Path is relative to the eZ Publish root directory
Path=

[SiteAccessSettings]
# Specify every siteaccess a separate sitemap shall be created for.
# If no siteaccessarray is given, the default siteaccess will be used for generation.
# If using the generatemultilingual cronjob then this setting is a list of siteaccesses / languages to include in a single sitemap.
# SiteAccessArray[]
# SiteAccessArray[]=de
# SiteAccessArray[]=en

[Classes]
# Specify include or exclude of objects of classes listed in ClassFilterArray
ClassFilterType=exclude

# Specify object classes to include or exclude from sitemap
ClassFilterArray[]
#ClassFilterArray[]=folder
#ClassFilterArray[]=article
#ClassFilterArray[]=image
#ClassFilterArray[]=forum
#ClassFilterArray[]=...

[NodeSettings]
# Set this setting to false to include only main node of content objects in sitemap
# Set this setting to true to include all nodes of content object in sitemap
Main_Node_Only=false
ExcludeNodes=disabled
# Add parent nodeIDs to exclude content tree results from sitemap
ExcludedNodeIDs[]
#ExcludedNodeIDs[]=341

*/ ?>
