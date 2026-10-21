---
title: "Working with clients and reviews"
audience: theme-designer
category: theme-design
order: 33
summary: How theme designers run design reviews with clients and editors for an Exponential site - what to review at each stage, reviewing on a preview siteaccess with real content, collecting feedback that can be acted on, sign-off, and explaining content-managed constraints to clients.
keywords: theme designer, clients, design review, feedback, sign-off, approval, preview siteaccess, stakeholders, editors, presenting designs, review rounds
type: help
---
# Working with clients and reviews

This page is for theme designers who present designs to clients, editors and other stakeholders and need decisions
from them. A content-managed site adds two things to an ordinary design review: the people who will fill the site
(editors) should be in the room, and the most convincing review happens on a preview of the real site with real
content. Commercial terms and contracts are covered in the designer sales section
([Contracts and terms](../designers/sales/contracts-and-terms.md)); this page is about the reviews themselves.

## Contents

1. [Who should review](#who-should-review)
2. [What to review at each stage](#what-to-review-at-each-stage)
3. [Reviewing on a preview siteaccess](#reviewing-on-a-preview-siteaccess)
4. [Running a review](#running-a-review)
5. [Feedback that can be acted on](#feedback-that-can-be-acted-on)
6. [Explaining content-managed constraints](#explaining-content-managed-constraints)
7. [Sign-off](#sign-off)

## Who should review

| Person | Why |
|---|---|
| The decision maker | signs off scope and look |
| One or two editors | know the real content and will live with the design daily |
| The developer | flags cost and risk early |
| Someone responsible for accessibility, if the organization has one | catches problems before they are built |

Keep the group small. Editors are the most often forgotten and the most useful.

## What to review at each stage

Tie reviews to the stages in [From mockup to theme](from-mockup-to-theme.md):

| Stage | Review | Decision asked |
|---|---|---|
| Inventory | the list of classes, views, special pages, blocks | "Is this everything the site shows?" |
| Foundations | palette with dark values, type scale, spacing | "Is this the brand, in light and dark?" |
| Templates of record | frame, key class views, special pages | "Is this the site?" |
| Prototype or preview | the real pages, real content | "Is anything missing or wrong?" |
| Launch | the checklists' results | "May we launch?" |

Do not show finished pages before the foundations are agreed; colors debated on a full page take three rounds.

## Reviewing on a preview siteaccess

Once templates exist, review on a preview siteaccess: the live content with your design, at its own address
([Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md)). It shows
problems no mockup does: the editor's longest headline, the image without alternative text, the folder with 300
children. Prepare:

- a list of addresses to review, covering each class and view, search, sign-in and an error page;
- the [style guide page](building-a-style-guide-page.md);
- a test layout with every block view type, if the site uses layouts.

Reviewers use their own devices; ask them to try their phone and, if they can, dark mode.

## Running a review

1. State the decision you need today, and what is out of scope today.
2. Show the context: which page type, which content, which width.
3. Walk through with real content, including the extremes ([Designing with real content](designing-with-real-content.md)).
4. Collect feedback in one place, not in side conversations.
5. End by repeating the decisions taken and the open questions with owners.

## Feedback that can be acted on

Ask reviewers to write each point as: where (address or frame, width, light or dark), what they see, and what they
expected. "The header feels heavy" becomes "On phones the header takes a third of the screen; I expected to see the
headline without scrolling." Sort points into: must fix, should fix, idea for later, and not agreed.

## Explaining content-managed constraints

Clients often ask for things that work in a mockup but not on a site editors fill. Useful explanations:

- **"Make every card the same height."** Cards are filled by editors; the grid aligns them, but text lengths differ.
  Here is the card with the longest title in your archive.
- **"Put this text in the image."** Text in images cannot be translated, read by screen readers, or edited without a
  designer.
- **"Can this one page look different?"** Yes: an override for that page, a layout, or a separate class. Each has a
  cost; here are the options ([Override a single node](../designers/templates/override-a-single-node.md#alternatives)).
- **"Add a slider to the front page."** The layouts editor has slider blocks; here are the trade-offs
  ([Motion and interaction](motion-and-interaction.md#carousels-and-sliders)).
- **"Just use this font from a font service."** Fonts are self-hosted in the theme, for privacy and speed; here is the
  license check.

## Sign-off

Ask for written sign-off at the foundations, the templates of record and launch. A sign-off names the version of the
design (a date or a tag in the theme's repository), the reviewed pages, and the open items accepted for later. If the
theme is released as a package, its version numbers follow the release rules
([Choosing a version number](../designers/distribution/choosing-a-version-number.md)).

## Related pages

- [Working with developers](working-with-developers.md)
- [Design QA checklist](design-qa-checklist.md)
- [Estimating theme work](../designers/sales/estimating-theme-work.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
