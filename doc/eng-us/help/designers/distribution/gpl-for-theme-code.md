---
title: "The GPL for theme code"
audience: designer
category: theme-distribution
order: 250
summary: Why most themes for Exponential are licensed GPL-2.0-or-later, what the GPL asks of you and of people who share your theme, the difference between "only" and "or later", and how to apply it. Not legal advice.
keywords: GPL, GPL-2.0-or-later, GPL-2.0-only, GPL-3.0, copyleft, derivative work, source code, theme code, license notice
type: help
---
# The GPL for theme code

This page is for designers choosing a license for the code of a theme: templates, stylesheets, scripts and PHP. It
explains the common reasoning; it is not legal advice.

## Why the GPL is the usual choice

- Exponential is licensed `GPL-2.0-or-later`, and so are the themes 7x publishes.
- Templates and PHP of a theme run inside Exponential, call its functions and override its templates. Many authors
  and the Free Software Foundation consider such code a work based on the program, which then has to be distributed
  under compatible terms. Choosing the same license avoids the question.
- The GPL lets everyone use, study, change and share the theme, and keeps those rights for everyone who receives a
  changed copy.

## What the GPL asks

| Of you, when you distribute the theme | Of others, when they distribute it |
|---|---|
| Include the license text and keep the notices | Keep your notices and the license |
| Make the source available (a public repository does this) | Pass on the source of their changed version |
| Not add restrictions beyond the license | Not add restrictions either |

Using the theme on a website is not distributing it. Sending a copy to a client, or publishing it, is.

## "Only" or "or later"

| Identifier | Means |
|---|---|
| `GPL-2.0-or-later` | Version 2, or any later version at the recipient's choice. Matches Exponential |
| `GPL-2.0-only` | Version 2 and no other |
| `GPL-3.0-or-later` | Version 3 or later |

`GPL-2.0-or-later` keeps your theme combinable with Exponential and with code under GPL 3.

## Step by step

1. Copy the official GPL 2 text into `LICENSE`.
2. Write the copyright notice and "licensed under the GNU General Public License v2.0 or any later version" in the
   README ([Copyright notices in files](copyright-notices-in-files.md)).
3. Set `"license": "GPL-2.0-or-later"` in `composer.json` and the full name in `ezinfo.php` and `extension.xml`.
4. Check that every third-party part is under a GPL-compatible license
   ([Third-party fonts, icons and libraries](third-party-fonts-icons-and-libraries.md)).

## Related pages

- [The GPL and selling](../sales/gpl-and-selling.md)
- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
