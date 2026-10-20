---
title: eZFile, eZDir and cluster file handlers
audience: developer
category: extension-development
order: 150
summary: Read and write files the way Exponential expects - eZFile::create() and rename(), eZDir::mkdir() and path(), and eZClusterFileHandler for anything under var/ so the extension works on clustered installations too.
keywords: eZFile, create, rename, eZDir, mkdir, path, recursiveDelete, findSubitems, eZClusterFileHandler, instance, storeContents, fetchContents, exists, delete, processCache, cluster
type: help
---
# eZFile, eZDir and cluster file handlers

Part 8, reference 15. An extension writes files: caches, exports, generated images. On a single server any file
function works. On a cluster (several web servers sharing storage through a database or a shared file system), files
under `var/` must go through the cluster file handler, or one server writes a file the others never see. This page
shows the three helpers and when to use each.

## eZFile (lib/ezfile/classes/ezfile.php)

| Method (static) | Use |
|---|---|
| `create( $filename, $directory = false, $data = false, $atomic = false )` | write a file, creating the directory; `$atomic` writes to a temporary file and renames it |
| `rename( $srcFile, $destFile, $mkdir = false, $flags = 0 )` | move a file |
| `suffix( $filename )` | the file's extension |
| `download( $file, $isAttachedDownload = true, ... )` | send a file to the browser |

## eZDir (lib/ezfile/classes/ezdir.php)

| Method (static) | Use |
|---|---|
| `mkdir( $dir, $perm = false, $recursive = false )` | create a directory with the installation's permissions |
| `path( $names, $includeEndSeparator = false, ... )` | join path parts |
| `cleanPath( $path, ... )` | normalize a path |
| `findSubitems( $dir, $types = false, $fullPath = false, ... )` | list a directory |
| `recursiveFind( $dir, $suffix )` | find files by suffix |
| `recursiveDelete( $dir, $rootCheck = true )` | delete a directory tree |
| `isWriteable( $dirname )` | whether a directory is writable |

`mkdir()` uses the permissions the installation is configured for, so files are readable by the web server and the
command line user alike ([File modes](../../../bc/6.0/file-modes.md)).

## eZClusterFileHandler (kernel/classes/ezclusterfilehandler.php)

Use it for every file under the site's var directory that more than one request reads:

```php
<?php
$path = eZSys::cacheDirectory() . '/myext/summary-' . (int)$nodeID . '.html';
$file = eZClusterFileHandler::instance( $path );
if ( $file->exists() )
{
    $html = $file->fetchContents();
}
else
{
    $html = '...';  // build it
    $file->storeContents( $html, 'myext', 'text/html' );
}
```

| Method | Use |
|---|---|
| `eZClusterFileHandler::instance( $filename = false )` (static) | the handler for one file; the class depends on the installation (`eZFSFileHandler` on a single server) |
| `exists()` | whether the file exists |
| `storeContents( $contents, $scope = false, $datatype = false, $storeLocally = false )` | write it; `$scope` names your extension, `$datatype` the MIME type |
| `fetchContents()` | read it |
| `delete()` | remove it |
| `processCache( $retrieveCallback, $generateCallback = null, $ttl = null, $expiry = null, $extraData = null )` | read-or-generate with locking, so only one request regenerates an expired file |

The example above was run read-write in a scratch folder of a test installation: `exists()`, `fetchContents()` and
`delete()` behaved as shown, with `eZFSFileHandler` as the handler.

## Which to use

| File | Use |
|---|---|
| under `var/` (caches, storage, generated files) | `eZClusterFileHandler` |
| a temporary file used within one request | `eZFile` / `tempnam()` in the system's temporary directory |
| files of the extension itself | never write to them at run time |

Paths come from `eZSys` (`cacheDirectory()`, `storageDirectory()`), never written into code
([eZSys and eZExtension](ezsys-and-ezextension.md)).

## Related pages

- Previous: [eZScript and eZCLI](ezscript-and-ezcli.md)
- Next: [The eZDataType API](ezdatatype-api.md)
- [Directories under var/](directories-under-var.md)
- [Performance in extensions](../part-3-extension-developer/performance-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
