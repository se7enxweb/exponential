---
title: "Licensing questions: the GPL and Exponential"
audience: user, admin, developer, designer, partner
category: project
order: 20
summary: The licensing FAQ of exponential.earth in plain words - the GPL version 2 or later, compatible and incompatible code, assets and dependencies, who holds the copyright, what source code means for a web application, whether your extension or design must be GPL, whether you must share it, selling, agencies working for clients, bridge extensions, website visitors and GPL version 3. Not legal advice.
keywords: license, licensing, GPL, GPL-2.0-or-later, GPL version 3, GPL compatible, AGPL, LGPL, MIT, BSD, PHP license, PEAR, copyright, source code, derivative work, extension, design, module, sell, agency, client, bridge module, website visitors, trademark, Creative Commons
type: help
---
# Licensing questions: the GPL and Exponential

The project website answers the common licensing questions on
[exponential.earth/faq/licensing](https://exponential.earth/faq/licensing). This page gives the same answers in plain
words, checked against the license files of this repository. It is **not legal advice**: for your own case, read the
license and ask a lawyer, as the website itself recommends.

The license texts: `LICENSE` and `COPYRIGHT.md` in the root of the installation, and the
[GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) at gnu.org.

## The license

### Under which license is Exponential?

The **GNU General Public License, version 2 or (at your option) any later version** (`COPYRIGHT.md`;
`"license": "GPL-2.0-or-later"` in `composer.json`). "Or any later version" means you may follow the terms of GPL
version 2 or of GPL version 3, whichever suits you, and you may combine Exponential with code under licenses that are
compatible with either, such as the GNU Affero GPL version 3, as the website notes.

### Who holds the copyright?

The kernel's notice names "7x & Exponential Foundation" for the years from 1998 and eZ Systems AS for 1999 to 2014
(`COPYRIGHT.md`). The code also contains the work of many contributors over the years. The website's FAQ explains
the rule: contributors agree to release their code under the same license as Exponential.

For code contributions to the core kernel, 7x and the Exponential Foundation also require a signed Contributor
Licensing Agreement (CLA) before the contribution is merged, and **by signing and submitting, you assign the copyright in your contribution to 7x, which becomes its owner, and give up control over it.** 7x may use, change, relicense and distribute your contribution in any way, also under
other licenses and in commercial products, without asking or paying you; you cannot withdraw it or set conditions. Your own extensions and designs are not affected. See
[Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla).

### Is the GPL going to change to version 3 only?

The website says there are no plans to distribute Exponential under GPL version 3 or later only, though it may be
considered one day. Because the license already says "or any later version", anyone may distribute it under GPL 3
today.

## Using Exponential

### Do I have to give my website's code to its visitors?

**No.** Visiting a website is not distribution in the GPL's sense, so nothing obliges you to share the code that runs
on your server. GPL versions 2 and 3 have no clause about use over a network.

### Can I use Exponential for commercial work?

Yes. The GPL allows any use, including running a commercial site, building sites for customers and selling
services around it.

### Can I sell Exponential, or an extension or design for it?

**Yes**, but you sell it under the GPL version 2 or later: the buyer gets the source and may change it and pass it on,
even for free. You may charge for the copy, the installation, support and updates.

## Your own extensions and designs

### Must my extension or design be under the GPL?

The website's answer: extensions ("modules") and designs are derivative works of Exponential, so **if you distribute
them**, you distribute them under the GPL version 2 or later. If you never distribute them, the question does not
arise.

What the GPL covers, the website explains, is code that works together with Exponential's code:

- your **PHP code** that uses Exponential's classes must be GPL or GPL-compatible;
- **images and other files** that your PHP only sends to the browser are data, not affected, unless you made them
  from an image Exponential provides under the GPL;
- **JavaScript** that works together with Exponential's own JavaScript (which is GPL, including the copy of jQuery
  shipped with it) must be GPL or GPL-compatible as well.

### Must I give my extension to everyone?

**No.** The GPL only says that whoever *receives* your extension from you must also receive its source, under the
GPL. You do not have to give it to anyone, and an extension you only use inside your organization does not have to be
shared at all. The website adds that sharing a generally useful extension is still a good idea: you get feedback,
bug reports and improvements back. How to do it: [Contribute extensions back](../contribute-extensions-back.md).

### What does "source code" mean for a web application?

The form of a file that people edit:

| File | Its source code |
|---|---|
| PHP, templates | the file itself, not compressed or obfuscated |
| JavaScript, CSS | the file itself, not minified or obfuscated; for a minified file, the original it was built from |
| Images | the version meant for editing: the PNG or JPEG itself, a high-resolution original, or the file of the drawing program |
| Compiled or packed formats | the editable files and whatever is needed to build the result |

### My extension uses a library under another license. Is that allowed?

| The library is | Then |
|---|---|
| GPL, or GPL-compatible (MIT, BSD, LGPL and others on the [FSF's list](https://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses)) | You may distribute your extension together with it. The combination is distributed under the GPL. |
| GPL-incompatible, but your extension only talks to it over the network (HTTP, XML-RPC, SOAP, a web API) | Allowed: the other system stays separate. |
| GPL-incompatible and loaded into the same PHP process | You may not distribute the combination: it would be a derivative of both, and break one license or the other. The website names the PHP License of many PEAR packages as an example of an open source license that is not GPL-compatible. |
| GPL-incompatible, installed by the user through Composer | The website says depending on such code is allowed, because the GPL limits distribution, not use; the project only will not host that code itself. |

Assets that are not code, such as fonts and icons under other licenses, may be shipped "in aggregate" with GPL code
when you have the right to distribute them, the website adds.

If you write a "bridge" extension for an outside library, the website asks you not to copy the library into a
project repository; tell users how to install it instead (with Composer where possible), and open an issue first if
you think it really must be included.

### May I contribute code under GPL version 3 only, or GPL version 2 only?

You may publish your own work under any license compatible with GPL 2 or later. But code that goes **into the
project's own repositories** must be under the same license as Exponential, GPL version 2 or later, so that everyone
can keep the choice between versions. If you cannot or will not license a contribution that way, do not submit it,
the website says. The kernel's `CONTRIBUTING.md` says the same.

## Agencies and clients

### Is an agency distributing code when it builds a site for a client?

The website's answer is **no**: an agency, freelancer or other service provider that assembles a code base for a
client under contract acts as the client's agent, so this is not distribution in the GPL's sense. The website adds
that such a provider therefore may combine GPL code with incompatible code for the client, but may not distribute
that combination to the public.

The client can do with the result what the GPL allows. Selling or giving the same extension to other clients, or
publishing it, is distribution, with everything that follows from it.

## Problems and questions

### I found an extension that says "GPL 2 only", "GPL 3 only" or another license. What should I do?

The website asks you to open an issue in that project's tracker first, and if the maintainer does not respond, to
report it in the [Exponential issue tracker](https://github.com/se7enxweb/exponential/issues). Before you copy code
from such an extension into your own, read
[Reading licenses before you reuse anything](../extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md).

### What license does documentation have?

This help and the documentation in `doc/` are under the GNU Free Documentation License 1.3 or later
([The license of this documentation](../../README-LICENSE.md), [Free documentation](../free-documentation.md)). The
website's licensing FAQ says its own content is under Creative Commons Attribution-ShareAlike 2.0, while the footer of
exponential.earth reads "All rights reserved"; until the two agree, link to the website instead of copying it.

### I have another question.

Ask general questions in the [Share Exponential forums](https://share.exponential.earth/forums). For your own case,
ask a lawyer: the project does not give legal advice.

## Related pages

- [Contribute extensions back](../contribute-extensions-back.md)
- [Reading licenses before you reuse anything](../extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Exponential: frequently asked questions](faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
