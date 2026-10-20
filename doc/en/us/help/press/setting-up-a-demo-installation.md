---
title: Setting up a demo installation
audience: press
category: press-media
order: 153
summary: How a creator builds an Exponential installation of their own to film or stream - get the code with Git and Composer, install it with one command on SQLite, serve it, make it camera-ready with invented content and demo accounts, and reset it between takes. Every command is from the installation chapters.
keywords: demo installation, install, exp:install, SQLite, Composer, Git, development line, demo content, demo account, reset, camera-ready, creator, video
type: help
---
# Setting up a demo installation

This page is for creators who want to show Exponential on camera. Every demo script in this section runs on a
**demo installation you control**: your own computer or a test server of yours, with invented content. Never record a
customer's or an employer's live site, and never a shared installation whose settings belong to someone else
([Live demo safety](../advocates/live-demo-safety.md)).

The commands here come from the installation guide and the feature pages linked beside them. Run them yourself before
you record; do not type them for the first time on camera.

## Contents

1. [Decide which version you show](#decide-which-version-you-show)
2. [Get the code](#get-the-code)
3. [Install in one command](#install-in-one-command)
4. [Serve the site](#serve-the-site)
5. [Make it camera-ready](#make-it-camera-ready)
6. [Reset between takes](#reset-between-takes)
7. [When something fails](#when-something-fails)
8. [A demo that others sign in to](#a-demo-that-others-sign-in-to)

## Decide which version you show

| You want to show | What you need | Say on camera |
|---|---|---|
| The newest release | The tag `v6.0.14` (7 June 2026) | "Exponential 6.0.14" |
| What this help describes: the admin4 design, `exp:install`, Velocity, Exponential Intelligence in the distribution, the current layouts editor | The development line, branch `main` | "The 6.0.15 development line, not a release, as of <date>" |

Most screens in the help belong to the 6.0.15 line, which "Not released yet" opens in its changelog
([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)). `exp:install`, Velocity and the admin4 design are listed there
as added in that line. Never give 6.0.15 a release date.

## Get the code

You need Git, Composer 2 and PHP as listed in the requirements
([Requirements](../../install/02-requirements.md), [Develop with Git](../develop-with-git.md#what-you-need)):

```bash
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
git switch -c my-demo            # your own branch on main; for the release: git switch -c my-demo v6.0.14
composer install
```

`composer install` is required before Exponential can run (`README.md`). It fetches the extensions and libraries.

## Install in one command

On the development line, one command installs the multisite package on SQLite in `eng-US`, with no database server
([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)):

```bash
php bin/php/console exp:install --dry-run    # checks the configuration, installs nothing
php bin/php/console exp:install              # installs
```

- Many installations add the shortcut `ln -s bin/php/console console`, after which `./console exp:install` is the
  same command; the shortcut is not shipped ([The console install](../../install/07-console-install.md)).
- Without options the administrator is `admin` with a generated 24-character password, printed in the summary and
  written to `var/log/initial-admin-password`. **Do not record this summary**: it shows the password.
- It refuses to run over an existing installation unless you add `--force`.
- On 6.0.14, use the setup wizard or the kickstarter instead
  ([Choosing an install method](../../install/04-choosing-an-install-method.md)).

## Serve the site

`exp:install` does not start a web server. Serve the installation root with your usual web server
([Serving the site](../../install/08-serving-the-site.md)), or, on the development line, with Velocity:

```bash
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status    # lists the addresses to open
```

The engine package for the bundled server is installed separately; the
[Velocity demo script](demo-script-velocity.md) covers it.

## Make it camera-ready

```text
[ ] Signed in once as admin; password changed; var/log/initial-admin-password deleted
[ ] A demo editor account with an invented name (for example "Demo Editor") and the Editor role
[ ] A second content language added (Setup > Languages) so items show a dashed badge to translate
[ ] One folder with a few items of invented text and your own or freely licensed images
[ ] At least one layout in the Layouts tab (Layouts > New layout, any type, a name)
[ ] The Interface box in the right column lists German, Hebrew and Arabic
[ ] Optional: Exponential Intelligence switched on with a key you can revoke (see its demo script)
[ ] Browser: a separate profile, zoom readable on a phone, notifications off, bookmarks bar hidden
[ ] Nothing on screen with real names, e-mail addresses, keys, passwords or server addresses
```

The same list for presales demos is in [Demo scripts, Before any demo](../business/demo-scripts.md#before-any-demo-the-setup-checklist).
Password rules for the first sign-in: [The default administrator password](../default-administrator-password.md).

## Reset between takes

| Left over | Clean it |
|---|---|
| Drafts from the last take | **Dashboard > My drafts**, discard them |
| A layout draft | Open the layout, **Discard**, confirm |
| A test translation | Remove it from the item ([Languages](../languages.md#remove-a-translation)) |
| Interface in Arabic or Hebrew | **Interface**, English, **Apply** |
| Drafts written by the assistant | Discard them in the **Intelligence** tab |
| Anything else | Reinstall with `--force` on the demo machine only |

## When something fails

| Symptom | What to do |
|---|---|
| "This directory already holds an installation ..." | Add `--force` only on a demo you may wipe |
| The site shows the maintenance page after a failed run | Fix the cause and run again, or `php bin/php/maintenance.php off` |
| Permission errors after installing as root | Give the files to the web server's user |
| The summary did not appear | Read `var/log/setup.log` |

Full table: [The console install, What can go wrong](../../install/07-console-install.md#711-what-can-go-wrong).

## A demo that others sign in to

A recording on your own machine needs nothing more. A demo on the internet whose administrator sign-in you publish
(so viewers can try it themselves) is a **public demo**: run it with the environment variable `EXP_DEMO_MODE=1`.

- Install with the variable, so `exp:install` keeps the password you publish instead of generating one (any length,
  `publish` included; an empty one is refused):

  ```bash
  EXP_DEMO_MODE=1 php bin/php/console exp:install --password='<the password you publish>'
  ```

- Keep the variable for the site's PHP, cron jobs and console afterwards. While it is set, nobody can change or reset
  the administrator password, so the next viewer can still sign in, and the administration shows a **Demo mode**
  banner.
- Rebuild the demo on a schedule: demo mode protects only the administrator password, not the content.
- Never set the variable for a real site, and never reuse the published password anywhere.

How to set it for each kind of server: [Demo mode for hosts](../hosting/security/demo-mode-for-hosts.md).

## Related pages

- [Demo mode](../demo-mode.md)

- [Keeping secrets off screen](keeping-secrets-off-screen.md)
- [Demo script: install from zero](demo-script-install-from-zero.md)
- [Giving demos as an advocate](../advocates/giving-demos-as-an-advocate.md)
- [Getting started](../getting-started.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
