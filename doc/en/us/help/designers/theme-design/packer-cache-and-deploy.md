---
title: The packer cache and deploys
audience: designer, admin
category: theme-design
order: 240
summary: Which caches to clear after a stylesheet, script, template or settings change of a design, why packed files and template-blocks go together, and the one deploy command that does it on installations with Velocity.
keywords: deploy, cache clear, packer cache, ezjscore-packer, template-block, design change deploy, exp:velocity deploy, --packer, ezcache, hard reload, stale css
type: help
---
# The packer cache and deploys

This page is for designers who changed a design and want the change on the site. It answers: what to clear for each
kind of change, and why.

## Contents

1. [What to clear, by change](#what-to-clear-by-change)
2. [Why packed files and template-blocks go together](#why-packed-files-and-template-blocks-go-together)
3. [The deploy command](#the-deploy-command)
4. [After the deploy](#after-the-deploy)

## What to clear, by change

| You changed | Clear | Command |
|---|---|---|
| a `.css` or `.js` file | nothing: the packed file's name carries the source time, a new one is made | hard-reload the browser |
| a stylesheet list (`ezcss_load` call, `design.ini` list) | the template caches that hold the link | `--clear-id=template-block`, and `--clear-id=content` for cached full views |
| an existing `.tpl` | compiled templates, views, blocks | `--clear-id=template,content,template-block` |
| a **new** `.tpl` or override rule | the override cache too | `--clear-id=template-override` (plus the above) |
| an INI file | the INI caches | `--clear-tag=ini` |
| an `ezjscServer_*` function or its settings | packed files and template-blocks together | `--clear-id=ezjscore-packer,template-block` |
| activated a design extension | design bases and autoloads | `--clear-id=design_base`, `php bin/php/ezpgenerateautoloads.php -e` |

All commands are `php bin/php/ezcache.php <option>`; `--list-ids` lists every id.

## Why packed files and template-blocks go together

The administration's page head is kept in a `{cache-block}`, and the cached head names the packed files. If only
`ezjscore-packer` is cleared, the cached head points to files that no longer exist, and the admin loses its styles and
scripts until `template-block` is cleared too. Clear them together, and in that order (`doc/en/us/bc/6.0/velocity-engines.md`,
"Deploying a PHP change").

An edited `.css` file does not need this: its packed file gets a new name by itself.

## The deploy command

Installations that run the Velocity application server have one command that runs every step in the right order
(autoloads, INI caches, template caches, PHP reload, Velocity restart, content and template-block caches):

```bash
./console exp:velocity deploy --allow-root-user            # templates, settings, PHP
./console exp:velocity deploy --packer --allow-root-user   # also packed scripts and styles
./console exp:velocity deploy --dry-run --allow-root-user  # show the steps only
```

`--packer` clears `ezjscore-packer` right before `template-block` and never elsewhere. Details:
[Velocity engines](../../../bc/6.0/velocity-engines.md).

## After the deploy

- Hard-reload (Shift with the reload button, or the developer tools' "Empty cache and hard reload") if a browser still
  shows the old file.
- On sites that run Velocity, its response cache is separate: `./console exp:velocity cache clear`.
- Open the pages in a private window, signed out, to see what visitors see.

## Related pages

- [The packer](the-packer.md)
- [Caching for designers](caching-for-designers.md)
- [Debugging a design](debugging-a-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
