---
title: The expinfo operator
audience: developer, designer, app-creator
category: development
order: 280
summary: expinfo from eZTemplateExpInfoOperator - the active extensions, all available extensions, one extension's metadata from extension.xml and ezinfo.php, and the kernel information, which keys each mode returns, and which kernel values must never be printed on a public page; examples rendered on alpha.
keywords: expinfo, extension info, ezinfo.php, extension.xml, expInfo, kernel info, version, eZTemplateExpInfoOperator
type: help
---
# The expinfo operator

`expinfo` answers "which extensions are installed, which are active, and what version is this?" from a template. It is
registered by `eZTemplateExpInfoOperator` (`lib/eztemplate/classes/eztemplateexpinfooperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`, and reads everything through the class `expInfo`
(`lib/ezutils/classes/expinfo.php`).

## Contents

1. [expinfo](#expinfo)
2. [One extension](#one-extension)
3. [Lists of extensions](#lists-of-extensions)
4. [The kernel](#the-kernel)
5. [Related pages](#related-pages)

## expinfo

### expinfo

The mode is the first parameter, or the piped string when there is no parameter:

| Mode | Returns (`expInfo` method) |
|---|---|
| none, `''` or `'active'` | a hash of the active extensions, name to info (`activeExtensions()`) |
| `'all'` | every available extension, each with `active` true or false (`availableExtensions()`) |
| `'kernel'` | the kernel information (`kernelInfo()`) |
| an extension name | that extension's info with `active`, or `false` when it does not exist (`extensionInfo()`) |

| Parameter | Type | Required | Default |
|---|---|---|---|
| mode or extension name | string | no | active extensions |

There are no named parameters. Generated reference: [expinfo](../../examples/template-operators/expinfo.md).

## One extension

The info of an extension is built from its `extension.xml` and its `ezinfo.php` (the `<extension>Info::info()` array),
plus `mtime`, `mtime_formatted`, `extension_name` and `active`. Keys that an `ezinfo.php` adds, such as the
third-party software notes of ezjscore, appear under their own names:

```tpl
{def $ext = expinfo( 'ezjscore' )}
{if $ext}keys: {foreach $ext as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}

ezjscore {$ext.version}, license: {$ext.license|wash}, active: {$ext.active|choose( 'no', 'yes' )}{/if}

expinfo( 'no_such_extension' ) = [{expinfo( 'no_such_extension' )|choose( 'false', 'true' )}]
```

Output on alpha:

```text
keys: name, version, copyright, license, info_url, Includes the following third-party software, Includes the following third-party software (2), Includes the following third-party software (3), mtime, mtime_formatted, extension_name, description, summary, author, meta, active
ezjscore 1.8.5, license: GNU General Public License v2.0 (or any later version), active: yes
expinfo( 'no_such_extension' ) = [false]
```

`{if expinfo( 'name' )}` is a safe way to make a template part depend on an extension being installed; test `.active`
too when the extension must also be switched on.

## Lists of extensions

```tpl
{def $active = expinfo()
     $all = expinfo( 'all' )
     $piped = 'ezjscore'|expinfo
     $inactive = 0}
{foreach $all as $name => $info}{if $info.active|not}{set $inactive = $inactive|inc}{/if}{/foreach}
expinfo() returns {$active|count} active extensions; expinfo( 'all' ) returns {$all|count}, of which {$inactive} are not active.
'ezjscore'|expinfo works too: {$piped.name|wash}
```

Output on alpha:

```text
expinfo() returns 62 active extensions; expinfo( 'all' ) returns 87, of which 25 are not active.
'ezjscore'|expinfo works too: eZ JSCore LS
```

Attribute access on the result works on a variable (`$piped.name`), but not directly after an operator applied to a
literal: `{'ezjscore'|expinfo.name}` is a parser error ("Extra characters found in expression"). Assign the result
first, as above.

## The kernel

`expinfo( 'kernel' )` returns a large hash, built once per request (`buildKernelInfo()`):

```tpl
{def $k = expinfo( 'kernel' )}
sections: {foreach $k as $section => $value}{$section}{delimiter}, {/delimiter}{/foreach}

version keys: {foreach $k.version as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}

PHP sapi in this run: {$k.php.sapi}
```

Output on alpha:

```text
sections: version, php, memory, server, database, ini, user, extensions, cache, filesystem, timestamps, git, composer
version keys: major, minor, release, state, development, alias, edition, full, full_alias
PHP sapi in this run: cli
```

The `version` section is meant for templates: `$k.version.full` is the release, as a string. The
other sections describe the server: `server` includes the host name, document root and installation paths, `php`
includes `uname` and the loaded extensions, `database` includes the database name, server and user (the password is
replaced by `***`), and `git` and `composer` the state of the checkout. **Never print those sections on a page that
visitors can see**; they are for administration pages behind a login. The examples on this page print only section
and key names, and the PHP SAPI of the command-line run.

All examples gave the same output interpreted and compiled on alpha.

## Related pages

- [Extension anatomy](../creators/app-creators/extension-anatomy.md): `ezinfo.php` and `extension.xml`
- [What an extension is](../extension-development/part-1-from-user-to-developer/what-an-extension-is.md)
- [Finding the extensions on your site](../extension-development/part-1-from-user-to-developer/finding-the-extensions-on-your-site.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
