---
title: "Contribute extensions back: share your work with everyone"
audience: developer, designer, partner, user
category: development
order: 7
summary: Why and how to give the extensions you build back to the Exponential project so everyone can use them; what the GNU GPL really requires when you distribute an extension (and what it does not require for a site you only run yourself); and how to publish one step by step, with a GitHub repository, composer.json, Packagist, ezinfo.php and extension.xml, a license file and a README, offered to the se7enxweb organization, projects.exponential.earth and share.exponential.earth.
keywords: contribute, contribute back, give back, share, extension, publish, release, GPL, GPL-2.0-or-later, license, source code, distribution, private use, internal use, customer, agency, GitHub, composer.json, Packagist, ezinfo.php, extension.xml, README, LICENSE, se7enxweb, projects.exponential.earth, share.exponential.earth, open source, community
featured: true
type: help
---
# Contribute extensions back: share your work with everyone

Almost every Exponential site ends up with an extension of its own: a datatype, an operator, an admin page, a
workflow event, a theme. Many of them would help other sites too. This page explains why it is worth sharing them,
what the license of Exponential asks of you when you hand an extension to someone else, and how to publish one so
that anyone can install it with one command.

This page explains the license in plain words. It is **not legal advice**: for a real decision about a commercial
project, read the license text and ask someone qualified.

## Why contribute an extension back

- **Everyone gets it.** An extension on GitHub and Packagist can be installed by every site with
  `composer require`. The more extensions exist, the more each site can do without new code.
- **Others maintain it with you.** Users report bugs, send fixes and keep it working with new PHP and Exponential
  releases. A private extension is maintained by you alone, forever.
- **It is how Exponential itself grew.** Most extensions in the se7enxweb organization are continuations of work
  that their authors once shared; the project keeps them working because their source was published.
- **It shows what you can do.** A public extension, with your name in its copyright line, is a portfolio for a
  developer, a designer or a partner company.

## What the GPL requires, and when

Exponential is free software under the **GNU General Public License, version 2 or (at your option) any later
version** (`COPYRIGHT.md` and `LICENSE.md` in the root of the installation; `composer.json` says
`GPL-2.0-or-later`). Almost every extension uses the same license: of the 79 extensions installed on the
project's reference site in October 2026, 75 say GPL 2 or later in `composer.json` (`GPL-2.0-or-later` or
`GPL-2.0+`), three say GPL 2 (`GPL-2.0-only`, or the older identifier `GPL-2.0`) and one `GPL-3.0-or-later`. Always read the license of the extension in front of
you ([Reading licenses before you reuse anything](extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)).

The GPL is about **copying, changing and distributing** the code. What it asks of you depends on what you do with
your extension.

### Using it yourself: no obligation to publish

| What you do | Does the GPL require you to give out the source? |
|---|---|
| You write or change an extension and run it on your own site | **No.** The GPL says that running the program is not restricted (GPL 2, section 0). |
| Your site is public on the internet and visitors use it | **No.** Visitors receive web pages, not a copy of the extension. GPL 2 and GPL 3 have no clause about use over a network (that clause is in a different license, the GNU Affero GPL, which Exponential does not use). |
| You use a changed extension inside your company or organization only | **No.** The Free Software Foundation's [GPL FAQ](https://www.gnu.org/licenses/gpl-faq.html#InternalDistribution) says that using copies within one organization is not distribution. |

So a site that is only run privately or internally, and whose code is never handed to anyone else, does not trigger
the distribution clause. You may keep that extension to yourself. This page still hopes you will share it, but the
license does not force you to.

### Distributing it: the source goes with it

You **distribute** an extension when you give a copy to someone else: you publish it, you sell it, or you hand it to
another company or person to use as their own. From that moment the GPL applies in full:

- **The source comes with it.** GPL 2, section 3, requires that whoever receives the program can get its complete
  source code: you give the source with it, or a written offer to provide it. An Exponential extension is PHP,
  templates and settings, so the files you hand over normally *are* the source; make sure nothing is left out
  (compressed or generated JavaScript and CSS need the files they were built from).
- **Under the same license.** A changed version, as a whole, is distributed under the GPL too (GPL 2, section 2).
  You keep every copyright and license notice that is already in the files.
- **No extra restrictions.** You may charge for the copy, for installation and for support, but you may not forbid
  the receiver to study, change or pass on the code (GPL 2, section 6).
- **To the people who receive it.** The obligation is towards the people you distribute to, not towards the whole
  world. Whoever receives your extension gets its source and the GPL's rights, and may pass it on in turn; you do not
  have to publish it on GitHub for everyone.

**Agencies and freelancers.** The project's own [licensing FAQ](https://exponential.earth/faq/licensing) takes the
view that an agency or freelancer who assembles a site's code for a client under contract acts as the client's agent
and is not distributing the code in the GPL's sense. The client owns the result and is the one who decides whether to
distribute it. Selling or giving the same extension to other clients, or publishing it, is distribution.

An extension runs inside the Exponential kernel's PHP process and calls its classes. Code combined that closely with
a GPL program is, in the Free Software Foundation's view
([GPL FAQ: plug-ins](https://www.gnu.org/licenses/gpl-faq.html#GPLPlugins)), covered by the GPL when it is
distributed together with it. The project's [licensing FAQ](https://exponential.earth/faq/licensing) says the
same in its own words: modules and designs are derivative works of Exponential, so when you distribute them you do so
under the GPL version 2 or later, while you are never obliged to distribute them at all. Images and other files your
PHP code only sends to the browser are data, not covered by that rule, unless you made them from Exponential's own
GPL files. That is why most extensions use GPL 2 or later.

The licensing FAQ also answers related questions this page does not repeat: GPL-compatible libraries in an
extension, "bridge" extensions that talk to other systems, selling extensions, and what "source code" means for
PHP, JavaScript, CSS and images. Read it before you decide on a license.

### Choose GPL 2 or later for your extension

Write "GNU General Public License v2.0 (or any later version)" unless you have a reason to choose another
GPL-compatible license. It keeps your extension combinable with the kernel and with nearly every other extension,
including the few under GPL 3 ([Reading licenses before you reuse anything](extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md#combining-licenses-a-quick-table)).
If your extension contains code from someone else, keep their notices and their license; if you built it for a
customer, make sure you may publish it (see [Publishing an extension as a partner](partners/publishing-an-extension.md)).

## How to publish an extension, step by step

The developers' guide [Extensions](../guides/extensions.md) explains how to build an extension; its section
[Release it](../guides/extensions.md#7-release-it) gives the exact commands. Here is the whole path from a folder on
your site to a package anyone can install.

### 1. Clean it up

- Remove everything that belongs to one site: customer names, addresses, content, passwords, API keys and other
  secrets. Turn site-specific behavior into a setting with a neutral default.
- Keep the usual extension layout: `settings/`, `design/`, `classes/`, `modules/`, `translations/`, `autoloads/`
  as needed ([What an extension is](extension-development/part-1-from-user-to-developer/what-an-extension-is.md)).

### 2. Add the license file

- A `LICENSE` file with the full license text, for example the GPL 2 text from
  [gnu.org](https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt).
- A copyright line naming you or your company, and every earlier author whose work it contains.
- A short license header in each PHP file (`@copyright` and `@license`), as the kernel's files have.

### 3. Write ezinfo.php and extension.xml

The about page of the administration (**Setup > About**) and the upgrade checks read these two files. The project's
convention for every release:

- `ezinfo.php`: a class `<extension>Info` with a static `info()` that returns `'Name'`, `'Version'` (the release
  number, without a `v`: tag `v1.0.0` gives `"1.0.0"`), `'Copyright'`, `'License'` and `'Info_url'` (the
  repository's web address).
- `extension.xml`: a `<metadata>` block with the same `<version>`, `<license>` and `<info_url>`. Never leave a
  placeholder such as `//autogentag//` or `dev-master` in it.
- Both files must show the same number. Check them:

  ```bash
  php -l extension/myextension/ezinfo.php
  xmllint --noout extension/myextension/extension.xml
  ```

### 4. Write composer.json

Composer installs the extension into `extension/<name>/` through the installer the kernel already uses:

```json
{
    "name": "yourvendor/myextension",
    "description": "What it does, in one sentence.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "authors": [ { "name": "Your Name" } ],
    "require": { "se7enxweb/ezpublish-legacy-installer": "*" },
    "extra": { "ezpublish-legacy-extension-name": "myextension" }
}
```

Copy the `type`, the installer and the `extra` key from an extension that is already installed on your site
(`extension/<name>/composer.json`) so that yours matches what your kernel expects. The `license` is an
[SPDX identifier](https://spdx.org/licenses/).

### 5. Write the README

A good README answers, in this order: what the extension does (two sentences); the requirements (Exponential and PHP
versions, other extensions); how to install it (`composer require yourvendor/myextension`, or a Git clone); how to
switch it on (**Setup > Extensions**, or `ActiveExtensions[]`); how to configure it; where to report issues; the
license and the credits.

### 6. Put it on GitHub

```bash
cd extension/myextension
git init
git add .
git commit -m "Added: First public version of myextension"
git remote add origin https://github.com/<you>/myextension.git
git push -u origin main
```

Enable **Issues** on the repository, so users can report bugs where you will see them.

### 7. Release a version and register it on Packagist

1. Choose the number from the tags that already exist, sorted by version, and write it into `ezinfo.php`,
   `extension.xml` (and `composer.json` if it has a `version`). Commit that, **then** tag that commit:

   ```bash
   git tag -l 'v*' --sort=version:refname | tail -5
   git commit -am "Updated: Version 1.0.0"
   git tag -a v1.0.0 -m "Version 1.0.0"
   git push origin main v1.0.0
   ```

2. Sign in at [packagist.org](https://packagist.org/) and submit the repository's address. Let Packagist install
   its GitHub webhook, so that every new tag appears without you doing anything.
3. A published tag is permanent. If a release is wrong, never move or delete its tag: publish the next version with
   the fix and say in its notes what was wrong. Packagist reads each tag once, so a moved tag would give two
   different packages the same version number.

Now anyone can install it with `composer require yourvendor/myextension`.

### 8. Offer it to the community

- **The se7enxweb organization.** Most extensions of the Exponential ecosystem live in
  [github.com/se7enxweb](https://github.com/se7enxweb), where they are kept working with each new release. If you
  would like the project to carry your extension, or you have improved one of the extensions already there, open an
  issue or a pull request in the repository concerned, or in
  [se7enxweb/exponential](https://github.com/se7enxweb/exponential/issues) for a new extension, and say what it
  does and under which license. A repository can be transferred to the organization or forked into it, with your
  history and your copyright kept. Code that goes into the project's own repositories must be under the same license
  as Exponential, GPL version 2 or later (not "GPL 2 only" or "GPL 3 only"), as the
  [licensing FAQ](https://exponential.earth/faq/licensing) explains; contributors keep their copyright.
- **The CLA.** 7x and the Exponential Foundation require a signed Contributor Licensing Agreement (the project's
  copyright assignment; legally a non-exclusive license, you keep your copyright) for every code contribution to the
  **core kernel**, `se7enxweb/exponential`, before it is merged. Publishing your own extension in your own repository
  is not affected: the CLA page says "if you have written a contribution and want to make it available on GitHub
  [...] you do not need to sign the CLA". The page does list "complete/incomplete extensions" among the code that
  needs a signed CLA "which you want 7x to include", so an extension handed over to 7x for inclusion is covered by
  it; the page names no individual repositories. See
  [Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla).
- **Exponential Projects**, [projects.exponential.earth](https://projects.exponential.earth/): the project forge
  that lists extensions with their news, downloads and source links
  ([Exponential Projects](extension-development/part-1-from-user-to-developer/projects-exponential-earth.md)).
  Give your extension a project page there that links to its repository.
- **Share Exponential**, [share.exponential.earth](https://share.exponential.earth/): the community site with the
  forums. Announce the extension in the [forums](https://share.exponential.earth/forums), ask for testers and
  answer questions
  ([Share Exponential](extension-development/part-1-from-user-to-developer/share-exponential-earth.md)).

### 9. Keep it alive

Test it against each new Exponential release, answer issues, merge good pull requests, and say in the README when you
stop maintaining it, so that someone else can take it over.

## Improving someone else's extension

Contributing back also means sending your fixes to an extension you did not write, instead of keeping a changed
copy. Fork it, fix it on a branch and open a pull request, exactly as
[Develop with Git: Contribute a change with a pull request](develop-with-git.md#contribute-a-change-with-a-pull-request)
shows. If the original author no longer answers, fork it respectfully: keep the history, the copyright and the
license, and credit them in the README
([Contributing extensions back upstream](partners/contributing-extensions-back.md)).

## Related pages

- [Develop with Git](develop-with-git.md) and [Update with Git](update-with-git.md): work with clones, so that
  sending a change back is one pull request.
- [The extension-development book](extension-development/README.md): from user to extension and theme developer,
  step by step.
- [Extensions](../guides/extensions.md): build, configure and release an extension.
- [Reading licenses before you reuse anything](extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Publishing an extension as a partner](partners/publishing-an-extension.md)
- [Contribute your site package](site-packages/contribute-your-site-package.md): share a whole site (design, classes, demo content) through the setup wizard.
- The full license texts: [GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) and the
  [GPL FAQ](https://www.gnu.org/licenses/gpl-faq.html).
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
