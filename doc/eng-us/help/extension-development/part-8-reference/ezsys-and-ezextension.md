---
title: eZSys and eZExtension
audience: developer
category: extension-development
order: 110
summary: Paths and the environment with eZSys (varDirectory, cacheDirectory, serverURL, isShellExecution) and active extensions with eZExtension (activeExtensions, expandedPathList, getHandlerClass).
keywords: eZSys, varDirectory, cacheDirectory, storageDirectory, rootDir, wwwDir, indexFile, serverURL, hostname, isShellExecution, isSSLNow, eZExtension, activeExtensions, baseDirectory, expandedPathList, extensionInfo, getHandlerClass
type: help
---
# eZSys and eZExtension

Part 8, reference 11. Two helper classes every extension sooner or later needs: `eZSys` (`lib/ezutils/classes/ezsys.php`)
knows where things are and how the request came in; `eZExtension` (`lib/ezutils/classes/ezextension.php`) knows which
extensions are active and where they live.

## eZSys: paths

All static:

| Method | Returns (on a typical installation) |
|---|---|
| `varDirectory()` | the site's var directory, e.g. `var/site` |
| `cacheDirectory()` | its cache directory, e.g. `var/site/cache` |
| `storageDirectory()` | its storage directory (uploaded files) |
| `rootDir()` | the installation's root directory |
| `wwwDir()`, `indexDir()`, `indexFile()` | the web paths of the installation |
| `siteDir()` | the site directory |

Never write paths such as `var/site/cache` into code: the var directory is a setting and differs per installation.

```php
<?php
$dir = eZSys::cacheDirectory() . '/myext';
eZDir::mkdir( $dir, false, true );
```

## eZSys: the request

| Method | Returns |
|---|---|
| `isShellExecution()` | `true` on the command line |
| `serverURL()` | scheme and host of the current request |
| `hostname()` | the host name |
| `isSSLNow()` | whether the request came over HTTPS (trusting configured proxies) |
| `clientIP()` | the client's address (trusting configured proxies) |
| `serverVariable( $variableName, $quiet = false )` | a `$_SERVER` value |
| `osType()`, `fileSeparator()` | the operating system |

Behind a reverse proxy, `isSSLNow()` and `clientIP()` depend on the trusted proxy settings
([Trusted proxies](../../../bc/6.0/trusted-proxies.md)).

## eZExtension

| Method (static) | Returns |
|---|---|
| `activeExtensions( $extensionType = false, $siteINI = null )` | the names of the active extensions, in load order |
| `baseDirectory( $siteINI = null )` | the extension directory, usually `extension` |
| `expandedPathList( $extensionList, $subdirectory = false )` | each extension's path, optionally with a subdirectory appended |
| `extensionInfo( $extension )` | the extension's metadata (name, version, copyright, license, info URL) |
| `nameFromPath( $path )` | the extension a path belongs to |
| `getHandlerClass( ezpExtensionOptions $options )` | an object of the class an INI setting names |

```php
<?php
if ( in_array( 'ezjscore', eZExtension::activeExtensions() ) )
{
    // ezjscore is there: use its packer keys
}
$settingsDirs = eZExtension::expandedPathList( array( 'myext' ), 'settings' );   // array( 'extension/myext/settings' )
```

Run on a demo installation, `activeExtensions()` returned 62 names and `expandedPathList( array( 'ezjscore' ), 'settings' )`
returned `extension/ezjscore/settings`. For richer metadata (about page data), the kernel's `expInfo` class reads
`extension.xml` and `ezinfo.php` together ([Extension metadata specification](../../../specifications/6.0/extension-metadata.md)).

`getHandlerClass()` and the options it takes are explained, with the mail transport as example, in
[Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md).

## Related pages

- Previous: [eZHTTPTool: requests, sessions and redirects](ezhttptool.md)
- Next: [eZTemplate from PHP](eztemplate.md)
- [Directories under var/](directories-under-var.md)
- [Extension load order](../part-3-extension-developer/extension-load-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
