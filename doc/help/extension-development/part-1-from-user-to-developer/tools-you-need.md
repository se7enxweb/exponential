---
title: The tools you need
audience: user, developer, designer, app-creator
category: extension-development
order: 30
summary: The short list of tools for extension development with Exponential - a terminal, PHP 8, Composer 2, Git, a text editor, a browser with developer tools, xmllint and PHPUnit - with the command that checks each one.
keywords: tools, terminal, php, composer, git, editor, ide, browser, developer tools, xmllint, phpunit, php -l, requirements
type: help
---
# The tools you need

Part 1, chapter 3. Extension development needs very little. This chapter lists the tools, says what each is for, and
gives the one command that tells you whether you have it. Nothing here costs money.

## The list

| Tool | What for | Check | You need at least |
|---|---|---|---|
| A terminal | Every command in this book | open one | any shell (bash, zsh) |
| PHP (command line) | Running the console, the installers, `php -l` | `php -v` | 8.0 (the kernel's `composer.json` accepts 8.0 and later 8.x versions; 8.5 is supported) |
| Composer | Installing Exponential and extensions as packages | `composer --version` | 2.x |
| Git | History, branches, sharing your work | `git --version` | 2.28 (see [Develop with Git](../../develop-with-git.md)) |
| A text editor | Writing PHP, templates, settings | | anything that saves plain UTF-8 without a byte order mark |
| A browser with developer tools | Looking at the HTML, CSS, network requests and console errors your pages produce | press F12 | any current browser |
| `xmllint` | Checking `extension.xml` before a release | `xmllint --version` | part of libxml2 |
| PHPUnit | Running tests (the kernel's and your own) | installed by Composer as a development requirement | the kernel's `composer.json` accepts 9.6 to 13.x |

The requirements of Exponential itself (PHP extensions, databases, web servers) are in
[Requirements](../../../install/02-requirements.md). This chapter is only about what *you* need on top.

## PHP on the command line

You run PHP far more often on the command line than you think: the console (`./console`), the scripts under
`bin/php/`, and the syntax check you will use after every change:

```bash
php -l extension/mysite_hello/ezinfo.php
```

Expected: `No syntax errors detected in extension/mysite_hello/ezinfo.php`. Make this check a habit; it costs a
second and catches the mistake that would otherwise take a whole page down.

> **Note**
> The command line PHP and the PHP that serves your web pages can be different programs with different settings.
> When something works on the command line but not in the browser (or the other way round), compare the two:
> **Setup > System information** shows the web server's PHP.

## Composer

Exponential and its extensions are Composer packages. You need Composer to install them and to install the
development tools (PHPUnit). Set it up once so that every `se7enxweb/*` package comes as a Git clone you can read and
change:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
```

Why and how is explained in [Develop with Git](../../develop-with-git.md). Composer's own documentation is at
[getcomposer.org](https://getcomposer.org/doc/).

## Git

Git keeps the history of every file. For a developer it answers three questions you will ask every day: *what did
I change?* (`git diff`), *why is this line here?* (`git log -p`, `git blame`), and *how do I go back?*
(`git restore`, `git revert`). Every extension in the se7enxweb organization is a Git repository with its full
history. Learn the basics at [git-scm.com](https://git-scm.com/book/en/v2).

## A text editor

Use whatever you like. What matters:

- **UTF-8 without a byte order mark.** A byte order mark before `<?php` is output before the page starts and breaks
  headers. `./console exp:ezcheckphptag` finds files with characters before the PHP start tag.
- **Line endings `LF`**, not `CRLF`.
- **Syntax highlighting** for PHP, and if your editor knows it, for Smarty-like templates; the template language
  is close enough to Smarty that a Smarty mode helps.
- **Search across files.** You will search the kernel a lot. On the command line, `grep -rn` does the same:

  ```bash
  grep -rn "function fetchByNodeID" kernel/classes/
  ```

## A browser with developer tools

The administration and your site are web pages. The developer tools (F12) show:

- **Elements**: the HTML your templates produced. Turn on the template path comments (see
  [Debug output and logs for developers](debug-output-and-logs-for-developers.md)) and the HTML tells you which
  template drew each part.
- **Console**: JavaScript errors and Content Security Policy violations. Exponential's administration enforces a
  Content Security Policy, so an inline script in your template shows up here as a violation, not as a working
  button.
- **Network**: which stylesheets and scripts were loaded, and the response of an ajax call.

## Tools that are already in the installation

You do not need to install these; they come with Exponential:

| Tool | Command | Use |
|---|---|---|
| The console | `./console list` | Every command of the kernel and of active extensions, with a description |
| Settings tool | `./console exp:ini` | Read and change settings, see which file wins |
| Autoload generator | `php bin/php/ezpgenerateautoloads.php -e` | Make the system find your new classes |
| Cache tool | `php bin/php/ezcache.php --clear-all --allow-root-user` | Clear caches after a change |
| Class check | `php bin/php/checkclasses.php --allow-root-user` | Load every class and list those PHP refuses |
| Code style | `./console exp:checkcodestyle` | Check changed PHP files against `phpcs.xml.dist` |
| Run with bootstrap | `php bin/php/ezexec.php <script.php>` | Run your own script with the kernel loaded |
| RAD tools | `Setup > RAD` (`/setup/rad`) | Wizards that write extensions, catalogue, health check |

`--allow-root-user` is only needed when you run a command as the root user; as a normal user, leave it out.

## Optional but useful

- **A database client** for the database your installation uses, to look at tables while you learn. Read, do not
  write: the content tables are maintained by the kernel and break when edited by hand.
- **PHPStan and PHP_CodeSniffer**: the kernel's own quality checks use them (`phpstan.neon.dist`, `phpcs.xml.dist`
  in the root). See Tests for extensions.
- **A GitHub account**, once you want to publish or contribute. Installing needs none.

## Check everything at once

```bash
php -v && composer --version && git --version && xmllint --version 2>&1 | head -1
```

Expected: four version lines. If one is missing, install it with your system's package manager before you continue.

## Related pages

- Previous: [The ladder: user, builder, extension developer, theme developer](the-ladder-from-user-to-developer.md)
- Next: [Setting up a development installation](setting-up-a-development-installation.md)
- [Requirements](../../../install/02-requirements.md)
- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
