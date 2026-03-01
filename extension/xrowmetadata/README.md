# Xrow Meta Data

# ﻿Installation

After this the extension has been installed. You should be able to generate the sitemap 
using the runcronjobs.php script. See "xrowsitemap.ini" for more configuration options.

### `php runcronjobs.php sitemap;`

`php runcronjobs.php sitemap;`

	Running cronjob part 'sitemap'
	Running extension/xrowmetadata/cronjobs/sitemap.php at: 14.10.2011 12:31
	Generating Regular Sitemaps...
	
	Generating Sitemap for Siteaccess XYZ 
	
	Adding 578 nodes to the sitemap.
	578 / 578 [+++++++++++++++++++++++++++++++++++++++++++++++++++++++++>] 100,00%
	
	Sitemap var/storage/sitemap/XYZ/urlset_standard_XYZ.xml for siteaccess XYZ has been generated.
	
	Completing extension/xrowmetadata/cronjobs/sitemap.php at: 14.10.2011 12:31
	Elapsed time: 00:00:08

This will create a file for every siteaccess within your eZ Publish root directory. These files are usually named "sitemap_access.xml", but you can change that in the INI file.

Please ensure that your Apache rewrite rules permit access to the XML File. This can be done by adding the following line to your .htaccess or Apache configuration file:

    RewriteRule ^sitemap[^/]*\.xml - [L]

Finally you need to attached the Metadatatype to some of your content classes and add the following code to you head of your HTML document.

{def $meta = metadata( $module_result.node_id ) }
{if $meta}
    {if $meta.title}
        <title>{$meta.title|wash}</title>
    {/if}
    {if $meta.keywords}
    	<meta name="keywords" content="{$meta.keywords|implode(',')|wash}" />
    {/if}
    {if $meta.description}
        <meta name="description" content="{$meta.description|wash}" />
    {/if}
{else} 
    <title>{$site_title}</title>
    {foreach $site.meta as $key => $item }
    <meta name="{$key|wash}" content="{$item|wash}" />
    {/foreach}
{/if}

Register sitemap in robots.txt:

Option 1.)
    Add this to the end of the "robots.txt" file
  
`Sitemap: http://www.example.com/sitemaps/index`

Option 2.)
    Add this to the end of the ".htaccess" file

    RewriteRule ^robots\.txt$ - [C]
    RewriteRule .* http://%{HTTP_HOST}/sitemaps/robots [P]
    
    RewriteRule ^sitemap\.xml$ - [C]
    RewriteRule .* http://%{HTTP_HOST}/sitemaps/index [P]

# Troubleshooting & support

Send email to service [at] xrow [dot] de


