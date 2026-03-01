<?php /* #?ini charset="utf8"?

[Settings]
# Offset in days
#ArchiveTimeShift=60
# Activate archive cronjob capabilities
#Archive=enabled
# Pings Google, Bing for update.
#Ping=true

# Amount of items fetched per loop
#LimitPerLoop=200

# Plugin interface for Converters o type xrowSitemapConverter
# VideoConverter=xrowSitemapConverter
# NewsConverter=xrowSitemapConverter
# ImageConverter=xrowSitemapConverter

# If there is a node which doesn't have xrowmetadata data the priority
# can be set by depth of the node
# root node priority = 1
# depth 2 means 0.9, depth 3 0.8 and so on.
#AddPriorityToSubtree=true

#HideSiteaccessAlways=false

Sitemap=enabled
NewsSitemap=enabled
VideoSitemap=enabled
# Always add objects even if they do not have a meta datatype
AlwaysAdd=enabled
MobileSitemap=disabled

# Siteaccesses without a sitemap
# ExcludeSiteaccess[]

[SitemapSettings]
# here you need to specify every siteaccess a sitemap shall be created for
# if no siteaccessarray is given, the default siteaccess will be used for generation
# AvailableSiteAccessList[]
# AvailableSiteAccessList[]=ger
# AvailableSiteAccessList[]=eng
# Each entry consists of the hostname;uri;accessname[;start|end|part|strict]
#HostUriMatchMapItems[]
#HostUriMatchMapItems[]=www.xxxxxxxx.de;de;x_de
#HostUriMatchMapItems[]=www.xxxxxxxx.de;;xx_de
MainNodeOnly=false

# If any site accesses use the PathPrefix setting, enter them here
#PathPrefix[]
#PathPrefix[]=XXXX;x_de
#PathPrefix[]=YYYY;x_en

# include or exclude objects of classes listed in ClassFilterArray
#ClassFilterType=exclude

# setting array to include/exclude classes in sitemap
#ClassFilterArray[]
#ClassFilterArray[]=folder
#ClassFilterArray[]=article
#ClassFilterArray[]=image
#ClassFilterArray[]=forum
#ClassFilterArray[]=...

#ImageAlias=original
#GalleryClasses[]
#GalleryClasses[]=gallery
#ImageClasses[]=image
#VideoClasses[]=video

# Use gzip to compress the sitemap
# Deprecated
#Gzip=disabled

#Add additional urls which are module views
# Deprecated, use seperate sitemap file
#AddUrlArray[]
#AddUrlArray[0]=/content/search

# Optional, add priority of additional urls which are module views
# The priority of this URL relative to other URLs on your site. Valid values
# range from 0.0 to 1.0. This value does not affect how your pages are compared
# to pages on other sites—it only lets the search engines know which pages you
# deem most important for the crawlers.
# Deprecated, use seperate sitemap file
#AddPriorityArray[]
#AddPriorityArray[0]=0.9

# Optional, Add frequency of additional urls which are module views
# Allowed values: [always|hourly|daily|weekly|monthly|yearly|never]
# Deprecated, use seperate sitemap file
#AddFrequencyArray[]
#AddPriorityArray[0]=always

# create alternate link to mobile website
# CreateAlternateLink=enabled

# To be included as an item property
# Ex: <xhtml:link rel="alternate" media='only screen and (max-width: 640px)' href=""...>
MobileMaxWidth=640

# Siteaccess name of the mobile site
# MobileSiteAccessName=m

# domain name of the mobile site
# only use this if you have remove siteaccess enabled
# MobileDomainName=m.example.com

[MobileSitemapSettings]
# here you need to specify every siteaccess a sitemap shall be created for
# if no siteaccessarray is given, the default siteaccess will be used for generation
# AvailableSiteAccessList[]
# AvailableSiteAccessList[]=ger
# AvailableSiteAccessList[]=eng

# include or exclude objects of classes listed in ClassFilterArray
#ClassFilterType=exclude

# setting array to include/exclude classes in sitemap
#ClassFilterArray[]
#ClassFilterArray[]=folder
#ClassFilterArray[]=article
#ClassFilterArray[]=image
#ClassFilterArray[]=forum
#ClassFilterArray[]=...

# If there is a node which doesn't have xrowmetadata data the priority
# can be set by depth of the node
# root node priority = 1
# depth 2 means 0.9, depth 3 0.8 and so on.
#AddPriorityToSubtree=true

# Use gzip to compress the sitemap
# Deprecated
#Gzip=disabled

#Add additional urls which are module views
# Deprecated, use seperate sitemap file
#AddUrlArray[]
#AddUrlArray[0]=/content/search

# Optional, add priority of additional urls which are module views
# The priority of this URL relative to other URLs on your site. Valid values
# range from 0.0 to 1.0. This value does not affect how your pages are compared
# to pages on other sites—it only lets the search engines know which pages you
# deem most important for the crawlers.
# Deprecated, use seperate sitemap file
#AddPriorityArray[]
#AddPriorityArray[0]=0.9

# Optional, Add frequency of additional urls which are module views
# Allowed values: [always|hourly|daily|weekly|monthly|yearly|never]
# Deprecated, use seperate sitemap file
#AddFrequencyArray[]
#AddPriorityArray[0]=always

# if you would like to exclude some nodes and their children
#ExcludeNodes[]
#ExcludeNodes[]=70162

[NewsSitemapSettings]
# Name of the publication
#Name=Test
# if RootNode is other as in content.ini
#RootNode=12345
# Addtional Keywords
#AdditionalKeywordList[]=Music
#AdditionalKeywordList[]=xrow GmbH
#Image Alias used for image items
#ImageAlias=rss
# setting array to include classes in sitemap
#ClassFilterArray[]
#ClassFilterArray[]=article
# if you do not want to use genre: <news:genres>PressRelease, Blog, ...</news:genres>
#UseGenres=disable
# if a class should have another genre as PressRelease like the class blog. PressRelease is default.
#Genres[]
#Genres[blog]=Blog
# if you would like to get all objects without limitation for generate xml element <news:access>Subscription</news:access> set it disable
# after fetch all objects the script will check if anonymus has access to read this object and
# set <news:access>Subscription</news:access> if not and nothing if anonymus can read this article
#Limitation=enable
# if you would like to use more AttributeFilter define here the name of the following AttributeFilter block
#ExtraAttributeFilter[]
#ExtraAttributeFilter[]=ExtraAttributeFilter_1

# AttributeFilter block
#[ExtraAttributeFilter_1]
#Value[]
#Value[]=state
#Value[]==
#Value[]=3

*/ ?>