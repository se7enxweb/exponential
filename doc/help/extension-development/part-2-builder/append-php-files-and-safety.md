---
title: .ini.append.php files and keeping settings safe
audience: developer, designer, app-creator, admin
category: extension-development
order: 70
summary: Why override files are PHP, which settings files hold secrets, file modes, what never goes into version control or into an extension, and how to check that settings cannot be read from the web.
keywords: ini.append.php, security, secrets, settings/override, file permissions, 0640, version control, gitignore, audit key, database password, web access
type: help
---
# .ini.append.php files and keeping settings safe

Part 2, chapter 7. Settings files are configuration, and some of them are secrets. This chapter explains how the
system protects them and what you must do so that your extensions and your repositories never leak them. The facts
come from [Security hardening](../../../install/13-security-hardening.md) and
[INI files keep their permissions](../../../bc/6.0/ini-save-file-permissions.md).

## Why the PHP wrapper

An override file is named `<file>.ini.append.php` and wraps its INI text in a PHP comment:

```php
<?php /* #?ini charset="utf-8"?

[DatabaseSettings]
Database=mysite

*/ ?>
```

If a web server is ever misconfigured and serves the file directly, PHP runs it, the comment produces no output, and
the visitor sees an empty page instead of your settings. Always use this form for override files, and keep the closing
`*/ ?>` on the last line.

## The files that hold secrets

| File | Secret |
|---|---|
| `settings/override/site.ini.append.php` | the database user and password (`[DatabaseSettings]`) |
| `settings/override/audit.ini.append.php` | the audit signing key and pseudonym key |
| `settings/override/mailpreferences.ini.append.php` | the site secret `[SecretSettings] TokenSecret` |
| `settings/override/velocity.ini.append.php` | `[DashboardSettings] Token`, when set |
| `config.env.php` | machine settings |
| `var/log/initial-admin-password` | the first administrator password of a console or kickstart install |

Some of these are generated on first use, so a development installation grows them too. They are all under
`settings/override/` or outside `settings/` for exactly that reason.

## Rules for builders and extension authors

1. **Never commit `settings/override/`.** In a Git clone of the kernel, `git status` lists it as untracked; leave it
   so, add it to your own `.gitignore` or `.git/info/exclude`, and stage files by name rather than with `git add -A`.
2. **Never put a secret into an extension.** Extensions are shared, packaged and published. An extension that needs a
   key should read it from a setting the installation fills in its own `settings/override/`, and ship the setting
   empty with a comment explaining it.
3. **Never copy settings between servers wholesale.** Copy the blocks you need; leave database and secret blocks to
   each server.
4. **Mask secrets in output.** `exp:ini` and the settings page mask variables named like `Password`, `Secret`, `Token`,
   `Salt` or ending in `Key`. Name your own secret settings the same way and they are masked too.
5. **Do not log settings.** A debug line that prints a whole block can print a password.

The package download of **Setup > Extensions** refuses to pack an extension that holds a secret value or key material,
and names the files and settings it found; see [the secret check](../../../bc/6.0/extension-package-secret-check.md).

## File modes

`eZINI::save()` gives a **new** settings file the mode `0640` and the owner and group of its directory; an existing file
keeps its mode. Check that nothing is world-writable:

```bash
find settings extension/*/settings -name '*.ini*' -perm -o+w
```

It must print nothing. Fix what it lists with `chmod 0640` (or `0644` where another user must read the file).

## Can the web read your settings?

The document root of Exponential is the installation root, and the rewrite rules send requests for `settings/`,
`var/` and the sources to `index.php`, which answers with a page or a 404, never the file. Check your server:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/settings/site.ini
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/settings/override/site.ini.append.php
```

Neither may return the file's contents. The full list of addresses to test is in
[Security hardening](../../../install/13-security-hardening.md).

## A safe settings extension, summarized

| In the extension (committed) | In `settings/override/` (never committed) |
|---|---|
| design, languages, image aliases, overrides, view cache rules | database, mail server credentials |
| your extension's own options, with safe defaults | API keys, tokens, secrets |
| comments explaining each setting | host names, paths, debug switches per server |

## Related pages

- Previous: [Settings in the administration](settings-in-the-admin.md)
- Next: [Templates for builders](templates-for-builders.md)
- [Security hardening](../../../install/13-security-hardening.md)
- [Security in extensions](../part-3-extension-developer/security-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
