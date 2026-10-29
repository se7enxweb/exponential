---
title: "Licensing questions: the GPL and Exponential"
audience: user, admin, developer, designer, partner
category: project
order: 20
summary: The licensing FAQ of exponential.earth in plain words - the GPL version 2 or later, compatible and incompatible code, assets and dependencies, who holds the copyright, what source code means for a web application, whether your extension or design must be GPL, whether you must share it, selling, agencies working for clients, license violations (what distributing Exponential or a design requires, common violations, giving back, what customers can ask for, what to do), bridge extensions, website visitors and GPL version 3. Not legal advice.
keywords: license, licensing, license violation, GPL violation, GPL compliance, distribution, conveying, written offer, corresponding source, minified, further restrictions, giving back, GPL, GPL-2.0-or-later, GPL version 3, GPL compatible, AGPL, LGPL, MIT, BSD, PHP license, PEAR, copyright, source code, derivative work, extension, design, module, sell, agency, client, bridge module, website visitors, trademark, Creative Commons
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

The kernel's notice names the "Exponential Foundation and 7x" for the years from 1998 and eZ Systems AS for 1999 to
2014 (`COPYRIGHT.md`): "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Since
29 October 2026 the Foundation and 7x hold the copyright together, the Foundation named first; see
[Copyright and copyleft](../copyright-and-copyleft.md). The code also contains the work of many contributors over the years. The website's FAQ explains
the rule: contributors agree to release their code under the same license as Exponential.

For code contributions to the core kernel, the Exponential Foundation and 7x also require a signed Contributor
Licensing Agreement (CLA) before the contribution is merged, and **by signing and submitting, you assign the copyright in your contribution to the Exponential Foundation and 7x, which become its owners, and give up control over it.** The Exponential Foundation and 7x may use, change, relicense and distribute your contribution in any way, also under
other licenses and in commercial products, without asking or paying you; you cannot withdraw it or set conditions. Your own extensions and designs are not affected. See
[Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla).

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

That answer covers a narrow case: a contractor working on the client's behalf and under its direction, much like an
employee. Whether a particular hand-over is that or a distribution depends on the contract and the facts, and not
everyone draws the line in the same place. When in doubt, treat the delivery as distribution and meet the
obligations in [License violations](#license-violations): it costs the agency nothing but handing over files it
already has.

## License violations

This section explains how the license of Exponential is broken in practice, so that whoever passes Exponential or a
design on knows what they owe, and whoever receives it knows what they are entitled to. It follows the
[GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) and names its sections (§); where
[GPL version 3](https://www.gnu.org/licenses/gpl-3.0.html), which anyone may choose under "or any later version",
differs, that is noted. It is not legal advice.

### What triggers the obligations: distribution

The conditions of the GPL apply when you **distribute** (GPL version 3 says "convey") Exponential or a work based on
it to a third party. A work based on Exponential is, for example:

- Exponential itself, changed or unchanged;
- an extension that uses Exponential's classes and runs in the same PHP process;
- a site package built from Exponential's packages and data;
- a design or theme whose templates, stylesheets or scripts include or are built from Exponential's GPL templates,
  stylesheets or scripts, such as overrides of the standard templates or a shipped design copied and changed.

Distribution means handing a copy to someone else, in any form: an archive, a Git repository, a copy uploaded to the
recipient's server, a download from a theme shop. Typical cases:

- an agency delivers a finished site, with its design and extensions, to its customer, who runs it or has another
  host run it;
- a designer sells or gives away a theme;
- a company publishes an extension or a site package, or sells it.

What is **not** distribution under GPL version 2:

- installing and running Exponential for yourself, changed or unchanged. The GPL does not restrict running the
  program (§0: "Activities other than copying, distribution and modification are not covered by this License");
- copies made and used inside your own organization;
- offering a website built with Exponential to its visitors. The PHP code runs on your server; visitors receive its
  output, not the program. Neither GPL version 2 nor version 3 has a clause about use over a network. The GNU Affero
  GPL has one (its §13), but Exponential is not under the AGPL; only code under the AGPL that you add to an
  installation would bring that obligation with it.

One precise point: the JavaScript and stylesheet files a site sends to the browser are copies of those files, and the
Free Software Foundation regards sending GPL JavaScript to visitors as conveying it to them. Keep the license
notices in such files and make their unminified source available, for example by linking the public repository it
comes from.

### What you owe when you distribute

| Obligation | GPL version 2 | GPL version 3 |
|---|---|---|
| Give every recipient a copy of the license with the program | §1 | §4 |
| Keep the copyright notices, the license notices and the notice that there is no warranty intact | §1 | §4, §5 |
| Mark changed files with a prominent notice that you changed them, and the date | §2(a) | §5(a) |
| License the whole work based on Exponential under the GPL to every third party, at no charge | §2(b) | §5(c), §10 |
| Provide the complete corresponding source code | §3 | §6 |
| Impose no further restrictions on the recipients' rights | §6 | §10, §7 |

The source code, in the license's words, is "the preferred form of the work for making modifications to it"; the
complete source also includes "the scripts used to control compilation and installation" (§3). For a site or a
design that means:

- the templates and the PHP code of the extensions;
- the original, unminified CSS and JavaScript, and the files they are built from (Sass, Less, TypeScript and the
  like): a minified bundle or a compiled stylesheet is not source code;
- the build configuration needed to produce the delivered files from those sources (`package.json`, the bundler's
  configuration, a `Makefile`, build scripts);
- the editable original of an image you made from a GPL image.

Under §3 you provide it in one of these ways:

- **with the delivery** (§3(a)): the source goes with the copy, which for a web project usually means the
  repository or an archive of it;
- **with a written offer** (§3(b)), valid for at least three years, to give **any third party** the complete source
  for no more than the cost of physically making the copy;
- **by passing on an offer** you received yourself (§3(c)), allowed only for noncommercial distribution.

Two more rules complete the picture:

- **You may charge.** "You may charge a fee for the physical act of transferring a copy, and you may at your option
  offer warranty protection in exchange for a fee" (§1). Pricing a theme, a site, the installation, support or
  hosting is allowed. What you may not do is take the freedoms away: the recipient gets the license itself at no
  charge (§2(b)) and may change the work, share it, sell it or give it away.
- **No further restrictions.** "You may not impose any further restrictions on the recipients' exercise of the
  rights granted herein" (§6). If a contract, a non-disclosure agreement or any other obligation stops you from
  meeting the GPL's conditions, you may not distribute the work at all (§7). Under GPL version 3, a recipient may
  remove a further restriction that comes with the work (§7).

Parts that are separate works and only travel alongside, such as photos, illustrations and fonts the agency made or
licensed on their own, may keep their own licenses, because "mere aggregation" with a GPL work does not bring them
under the GPL (§2, last paragraph). Say which parts these are and under which terms they come.

### Common violations

- Delivering a site without the source of its design or of its custom extensions.
- Delivering only minified or compiled assets, without the files and build scripts they were made from.
- Delivering without the license text, or with a written offer that is never honored.
- Removing or replacing the copyright and license notices of Exponential or of a template a design was built from.
  Adding your own copyright notice for your own changes is fine; removing the others is not.
- Contracts or terms that forbid the customer to pass the code on, to change it, to reuse it, to publish it or to
  have another provider work on it.
- Calling a design proprietary, or selling it under a "single-site license", while it is a derivative of GPL
  templates, stylesheets or scripts.
- Charging a fee for the license of the GPL parts, or making their use depend on a license key.

The consequence under GPL version 2 is that the distributor's rights under the license end automatically (§4:
"Any attempt otherwise to copy, modify, sublicense or distribute the Program is void, and will automatically
terminate your rights under this License"); those who already received copies from them keep their own licenses.
Distributing without a license is copyright infringement, which the copyright holders can take to court; German
courts have enforced the GPL since the Landgericht München I decided so on 19 May 2004 (21 O 6123/04). A distributor
who follows GPL version 3 gets the license back by ending the violation: provisionally at once, and permanently if
the copyright holder does not notify them of it within 60 days after it ended, or if it is the first notice from that
copyright holder and the violation is cured within 30 days of receiving it (§8).

### Giving back: what the project asks, and what the GPL does not require

The GPL does **not** oblige anyone to give their changes, extensions or designs to the Exponential project. It obliges
whoever distributes them to give the source and the license **to the people they distribute to**. Not contributing a
design back is therefore not a violation of the license.

Once the customer has received the design under the GPL, the customer holds every right the GPL gives: to use it,
change it, have it changed by anyone, and share it with anyone, including the project.

The project asks agencies and designers to contribute their designs, extensions and fixes back, and the community
expects it: that is how free software stays healthy, and every site built on Exponential stands on work others gave
before. It is a request and an expectation of the community, not a legal duty. How to do it:
[Contribute extensions back](../contribute-extensions-back.md) and
[Contribute to the project](contribute-to-the-project.md).

### What customers can ask their agency for

- The license text, and the copyright notices of what was delivered.
- The complete source of everything delivered under the GPL: the extensions, the design with its templates,
  unminified stylesheets and scripts and their build scripts, the site package, ideally as the Git repository.
- A contract that confirms the rights the GPL gives, and has no clause forbidding changes, passing on, reuse or the
  choice of another provider. Clauses to look for and to ask for:
  [Lock-in clauses to avoid](../government/procurement/exit-plans/lock-in-clauses-to-avoid.md) and the
  [open source license and source availability clause](../government/procurement/tender-templates/open-source-license-and-source-availability-clause.md).
- A list of the parts under other licenses (photos, fonts, paid libraries) and what their terms allow.

The contract may give the customer more than the GPL, for example the copyright of work it paid for; it can never
give less.

### What to do about a violation

1. **Ask first.** Most violations are mistakes. Write to the agency or vendor, name what is missing (the license,
   the source, a clause that conflicts with the GPL), cite the sections above and give a reasonable time.
2. **The copyright holders can enforce the license.** For the kernel these are the Exponential Foundation and 7x,
   and eZ Systems AS for the inherited code (`COPYRIGHT.md`); for an extension or a design, its authors. Tell them
   what you found: for Exponential, write to 7x at info@se7enx.com. A customer who did not receive what was promised
   may also have claims under the contract; that is a question for the customer's lawyer.
3. **Read the guides on GPL compliance.**
   - Free Software Foundation: [Violations of the GNU licenses](https://www.gnu.org/licenses/gpl-violation.html)
     and the [Frequently asked questions about the GNU licenses](https://www.gnu.org/licenses/gpl-faq.html).
   - Software Freedom Conservancy: [Copyleft compliance](https://sfconservancy.org/copyleft-compliance/), the
     [Principles of community-oriented GPL enforcement](https://sfconservancy.org/copyleft-compliance/principles.html)
     and the [Copyleft and the GNU General Public License: a comprehensive tutorial and guide](https://copyleft.org/guide/).

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
