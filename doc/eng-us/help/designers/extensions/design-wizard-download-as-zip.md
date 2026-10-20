---
title: "Downloading the wizard's result as a zip"
audience: designer
category: theme-extensions
order: 260
summary: Use Download as zip in the design extension wizard to get a theme skeleton without writing to the server - when it is the better choice, what the archive contains, and what to do when the button is missing.
keywords: download as zip, zip archive, design wizard, ZipArchive, skeleton, no write access
type: help
---
# Downloading the wizard's result as a zip

This page is for designers who want the generated theme on their own computer, for example because the server's
`extension/` folder is not writable or because the theme will live in its own repository from the start.

## When to use it

| Situation | Use |
|---|---|
| You develop on the same installation | **Create in extension/** |
| `extension/` is not writable for the web server | **Download as zip** |
| An extension of that name already exists and you want to compare | **Download as zip** (the "already exists" problem does not block the archive) |
| You start a repository on your own computer | **Download as zip** |

## Step by step

1. Fill the wizard as described in [The design extension wizard](design-extension-wizard.md) and press
   **Refresh preview**. Fix any problem the page lists (other than "already exists").
2. Press **Download as zip**. The browser saves `<name>.zip`.
3. Unpack it: `unzip mytheme.zip`. Everything is inside one folder `mytheme/`, ready to be moved to `extension/`.
4. Put it under Git right away ([Putting a theme under Git](putting-a-theme-under-git.md)).

## What happens on the server

The archive is built in the installation's cache directory (`<cache>/designextension/`), sent and deleted again.
Nothing is written to `extension/`.

## When the button reports a problem

- "This installation has no zip support, so an archive cannot be built.": PHP's `zip` extension (`ZipArchive`) is
  missing. Ask the administrator to install it, or use **Create in extension/** on a development copy.
- Any other message names the field to correct.

## Related pages

- [From the wizard's output to the first commit](from-wizard-output-to-first-commit.md)
- [The design wizard's parts](design-wizard-parts-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
