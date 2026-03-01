Adding mobile links to sitemaps:
----------------------------------------------

Google has introduced a new sitemap format for mobile websites:
https://developers.google.com/webmasters/smartphone-sites/details

Google like to have sitemap entries like this example:

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">
<url>
<loc>http://www.example.com/page-1/</loc>
<xhtml:link
    rel="alternate"
    media="only screen and (max-width: 640px)"
    href="http://m.example.com/page-1" />
</url>
</urlset>

To enable this you need to add these settings to xrowsitemap.ini:

[SitemapSettings]
# create alternate link to mobile website
CreateAlternateLink=enabled

# Siteaccess name of the mobile site
MobileSiteAccessName=m

# domain name of the mobile site
# only use this if you have remove siteaccess enabled
# MobileDomainName=m.example.com