---
title: Licensing for hosts
audience: hosting
category: hosting
order: 90
summary: What the GNU GPL v2 or later means for a hosting company: hosting customer sites, preinstalled images and one-click installers, changed code, extensions with other licenses, and copying this documentation. Plain words, not legal advice.
keywords: license, GPL, GPL-2.0-or-later, hosting, distribution, image, installer, one-click, changed code, patches, source code, MIT, Velocity, GFDL, documentation, copyright notice
type: help
---
# Licensing for hosts

This page is for managers and system administrators at hosting companies. It answers the licensing questions that
come up when you host Exponential for others: may you host it, may you ship it in images and installers, what if you
change it, and what about extensions and this documentation?

> **Warning**
> This is a summary in plain words, not legal advice. The license texts in the repository count: the GNU General
> Public License version 2 in `LICENSE.md` and `LICENSE`, and the GNU Free Documentation License 1.3 in
> [doc/LICENSE-GFDL-1.3.txt](../../../LICENSE-GFDL-1.3.txt). For a specific plan, ask your legal adviser. The
> buyer's view of the same licenses is on [Licensing explained for buyers](../../business/licensing-for-buyers.md).

## Contents

1. [In short](#in-short)
2. [Hosting customer sites](#hosting-customer-sites)
3. [Images, templates and one-click installers](#images-templates-and-one-click-installers)
4. [When you change the code](#when-you-change-the-code)
5. [Extensions and other components](#extensions-and-other-components)
6. [Copying this documentation](#copying-this-documentation)
7. [Questions for your legal adviser](#questions-for-your-legal-adviser)

## In short

- Exponential is licensed under the **GNU GPL v2 or any later version** (`COPYRIGHT.md`, `composer.json`:
  `"license": "GPL-2.0-or-later"`).
- There is **no license fee**: "Using Exponential under the terms of the GNU GPL is free of charge. The Professional
  License is no longer available." (`README.md`).
- **Running** the software is not restricted (GPL section 0). Hosting a site is running it.
- **Distributing** copies (giving or selling them to others) brings the GPL's conditions: keep the notices, provide
  the source, pass on the same rights.

## Hosting customer sites

Section 0 of the GPL: "The act of running the Program is not restricted". You may run Exponential on as many servers
for as many customers as you like, without asking anyone and without paying a fee. Nothing in the license ties the
software to a particular host; your customers may move to another host at any time, with the same rights.

## Images, templates and one-click installers

When you hand a copy of the software to a customer (for example a virtual machine image they download, or files you
place in their account that they then own and may take elsewhere), you may be distributing it. In that case the
conditions apply:

| Condition | What it means for an image or installer |
|---|---|
| Keep the notices | leave `COPYRIGHT.md`, `LICENSE.md` and the notices in the files as they are |
| Provide the source | Exponential is PHP, so the files are the source; if you change them, the changed files are what you provide |
| Same license | your changed version of Exponential is under the GPL as a whole |
| No extra restrictions | do not add terms that take away the customer's GPL rights |

Practical rule: build images and installers from the public repositories and packages, keep them unchanged where you
can, and put your own configuration in files that belong to the installation, such as `settings/override`
([Automating provisioning](../provisioning/automating-provisioning.md)).

## When you change the code

- A change you keep on your own servers and only run is not distributed.
- A change you give to customers with their copy is distributed: mark the changed files with a notice and date
  (GPL section 2a) and provide the changed source.
- Either way, contributing the change upstream saves you from re-applying it at every update
  ([Working with the project and 7x](working-with-the-project-and-7x.md)). Contributions are made under the same
  license (`CONTRIBUTING.md`).

## Extensions and other components

| Component | License | Where to check |
|---|---|---|
| The kernel | GPL v2 or later | `COPYRIGHT.md`, `composer.json` |
| Each extension | its own, stated in `ezinfo.php` and `extension.xml` | **Info > About** in the administration |
| Exponential Velocity | MIT | its `composer.json` |
| Third-party components | their own | the **Third-party software** table on the About page |

Check the About page of the installation you ship before you state "everything is GPL"
([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).

## Copying this documentation

The help, including these hosting pages, is under the GNU FDL 1.3 or later. You may copy it into your own customer
documentation, translate it and adapt it, under the license's conditions: keep the notices, keep it under the same
license, say what you changed ([Free documentation](../../free-documentation.md)). Linking to the pages needs no
conditions at all.

## Questions for your legal adviser

1. Do we give customers copies of the software they then control? If yes, do our images and installers meet the
   distribution conditions?
2. Do we change Exponential or extensions, and do those changes leave our servers?
3. Do we combine Exponential with proprietary code that we distribute?
4. Which extensions do we preinstall, and what are their licenses?

## Related pages

- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [One-click installers](../provisioning/one-click-installers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
