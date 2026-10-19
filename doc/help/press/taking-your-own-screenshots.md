---
title: Taking your own screenshots
audience: press
category: press-media
order: 38
summary: How a journalist or creator makes clean, honest screenshots of Exponential on an installation of their own - installing a demo site, which screens show the headline features, light and dark themes, sizes, file formats, privacy checks and how to caption the result.
keywords: screenshots, demo installation, exp:install, admin4, dark mode, light mode, capture, image size, WebP, PNG, caption, privacy, test account
type: help
---
# Taking your own screenshots

This page is for reporters, picture editors and creators who want images nobody else has. The safest way is to run
Exponential yourself: you control the content, the version and the data on screen.

## Contents

1. [Get an installation](#get-an-installation)
2. [Prepare the content](#prepare-the-content)
3. [Screens worth capturing](#screens-worth-capturing)
4. [Settings for good images](#settings-for-good-images)
5. [Checks before publishing](#checks-before-publishing)
6. [Captions](#captions)

## Get an installation

The documentation describes a complete installation in one command, with SQLite so that no database server is
needed ([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)):

```bash
./console exp:install
```

Run it in a copy of the code that you got as
[Getting the code](../../install/03-getting-the-code.md) describes.

Follow the page for the requirements and the options; [Setting up a demo installation](setting-up-a-demo-installation.md)
walks through it for creators. Write down the version you installed (`git describe --tags`): your captions will need it.

Do not take screenshots of somebody else's live site or administration without their permission.

## Prepare the content

- Use the demo content of the installer, or write your own neutral content.
- Create a test editor account with an obviously fictional name, for example "Demo Editor", and an address at a
  domain reserved for examples such as `example.com`.
- Change the administrator password from the default first ([Change the admin password](../change-the-admin-password.md)).

## Screens worth capturing

| Story | Screen | Help page that shows the way |
|---|---|---|
| Editing | The edit form of an article; the version list | [Editing content](../editing-content.md), [Publishing and versions](../publishing-and-versions.md) |
| The administration | The dashboard of admin4, in light and in dark | [The admin tour](../admin-tour.md) |
| Page building | The layouts editor with a block being added | [The layouts editor](../layouts-editor.md) |
| Media | Multi-file upload | [Media and uploads](../media-and-uploads.md) |
| Search | The search page with results | [Searching](../searching.md) |
| Security | The two-step sign-in setup | [Accounts and sign-in](../accounts-and-sign-in.md) |
| Languages | A right-to-left language in the administration | [Right to left](../../features/6.0/right-to-left.md) |
| Help | The help dashboard and a help page | Story: the help system |
| Exponential Intelligence | A draft suggestion in the editor (only after you configured a provider yourself) | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |

## Settings for good images

- **Window size**: a common desktop width such as 1440 pixels, and a phone width such as 375 pixels for responsive
  shots. Browser zoom at 100 percent unless you want to show zoom.
- **Theme**: admin4 has light and dark themes; show both if the story is about the interface.
- **Format**: PNG for editing; WebP or JPEG for publication at the size your page needs.
- **No browser clutter**: hide bookmarks and extensions; crop the address bar unless it shows only a demo address.

## Checks before publishing

- [ ] No personal data, no passwords, no tokens, no private host names ([Keeping secrets off screen](keeping-secrets-off-screen.md)).
- [ ] The default administrator password is not the one you used.
- [ ] The screen shows what the caption says, in the version the caption names.
- [ ] Nothing unreleased is presented as released: a screenshot from a development checkout shows 6.0.15 work, which
      is "in development" ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

## Captions

```text
"The admin4 dashboard of Exponential in dark mode (development version of 6.0.15, demo content,
 screenshot by <outlet>, <date>)."
"Adding a block in the Exponential Layouts editor (Exponential <version>, demo installation)."
```

## Related pages

- [Screenshots: what you may use](screenshots-what-you-may-use.md)
- [Setting up a demo installation](setting-up-a-demo-installation.md)
- [Privacy on camera](privacy-on-camera.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
