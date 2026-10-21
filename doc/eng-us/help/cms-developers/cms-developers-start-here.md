---
title: "CMS developers: start here"
audience: cms-developer
category: cms-development
order: 1
summary: Where developers who build websites and content applications on Exponential start, often coming from another CMS. It covers what this section holds, a first-day plan, every page grouped by topic, the rules every project keeps, and the pages of the developer and designer help you will use alongside.
keywords: CMS developer, CMS developers, start here, site building, content model, templates, overrides, siteaccess, layouts, REST, headless, deployment, WordPress, Drupal, TYPO3, Ibexa, eZ Platform, onboarding, overview
featured: true
type: help
---
# CMS developers: start here

This section of the help is for **developers who build websites and content applications** on Exponential: agency and
in-house developers, freelancers, and developers who arrive from WordPress, Drupal, TYPO3, eZ Platform or Ibexa and
need to be productive fast. It explains the content model, site building, delivery and project work from the
developer's side, and links into the extension development book and the designers' help where those go deeper.

Every fact on these pages was checked against the code, the settings or the existing documentation of Exponential 6.
When the software changes, the pages change with it: every page has **Edit on GitHub**.

## Contents

1. [What you find here](#what-you-find-here)
2. [Your first day](#your-first-day)
3. [The pages of this section](#the-pages-of-this-section)
4. [The rules every project keeps](#the-rules-every-project-keeps)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## What you find here

| Kind of page | What it gives you |
|---|---|
| Orientation | what Exponential is, reading paths, and comparisons for developers coming from other systems |
| Explanations | the content model, templates, view modes, caching, siteaccesses, layouts, APIs |
| Tasks | designing a content model, creating a class, writing templates, importing, deploying |
| Checklists | content model review, security, project launch |
| Reference | an FAQ and a glossary |

## Your first day

1. **The overview** (20 minutes): [Exponential for CMS developers](exponential-for-cms-developers.md).
2. **If you know another CMS** (20 minutes): the page for it under [Comparisons](#comparisons).
3. **An installation of your own** (40 minutes): [Development environment](development-environment.md).
4. **The model** (45 minutes): [The content model](the-content-model.md) and [Objects, nodes and locations](objects-nodes-and-locations.md).
5. **The presentation** (45 minutes): [Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md).
6. **Caches** (20 minutes): [Caching for CMS developers](caching-for-cms-developers.md).

Then follow a path in [Reading paths for CMS developers](reading-paths.md).

## The pages of this section

### Getting oriented

- [Exponential for CMS developers](exponential-for-cms-developers.md)
- [Reading paths for CMS developers](reading-paths.md)

### Comparisons

- [Coming from WordPress](coming-from-wordpress.md)
- [Coming from Drupal](coming-from-drupal.md)
- [Coming from TYPO3](coming-from-typo3.md)
- [Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md)

### The content model

- [The content model](the-content-model.md), [Objects, nodes and locations](objects-nodes-and-locations.md)
- [Content classes and attributes](content-classes-and-attributes.md), [Choosing datatypes](choosing-datatypes.md)
- [Designing a content model](designing-a-content-model.md), [Creating a content class](creating-a-content-class.md)
- [Relations and embeds](relations-and-embeds.md), [Rich text](rich-text.md), [Images and image aliases](images-and-image-aliases.md)
- [Versions, drafts and publishing](versions-drafts-and-publishing.md), [Translations and languages](translations-and-languages.md)
- [Sections, roles and policies](sections-roles-and-policies.md), [Users and registration](users-and-registration.md)

### Building the site

- [Siteaccesses and multisite](siteaccesses-and-multisite.md), [URL aliases and routing](url-aliases-and-routing.md)
- [Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md), [View modes](view-modes.md)
- [Fetching content in templates](fetching-content-in-templates.md), [Layouts for CMS developers](layouts-for-cms-developers.md)
- [Search](search.md), [Forms and information collection](forms-and-information-collection.md), [The built-in shop](the-built-in-shop.md)
- [Workflows and triggers](workflows-and-triggers.md), [Cronjobs](cronjobs.md)
- [Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md), [Extensions you will write](extensions-you-will-write.md)

### Content and delivery

- [Content from PHP](content-from-php.md), [Importing content](importing-content.md)
- [The REST API](the-rest-api.md), [Headless delivery](headless-delivery.md)
- [Caching for CMS developers](caching-for-cms-developers.md), [Performance](performance.md)

### Project work

- [Development environment](development-environment.md), [Deploying a site](deploying-a-site.md),
  [Site packages and installers](site-packages-and-installers.md)
- [Security checklist](security-checklist.md), [Accessibility](accessibility.md), [SEO basics](seo-basics.md)
- [Project launch checklist](project-launch-checklist.md), [Content model review checklist](content-model-review-checklist.md)
- [CMS developer FAQ](cms-developer-faq.md), [Glossary for CMS developers](glossary-for-cms-developers.md)

## The rules every project keeps

1. **Never edit shipped files.** The kernel, `design/standard` and `settings/*.ini` are replaced on update; your work
   lives in your extensions and in override settings ([Extensions you will write](extensions-you-will-write.md)).
2. **Model first.** Classes and the tree decide templates, permissions and URLs; review the model before building
   ([Content model review checklist](content-model-review-checklist.md)).
3. **Change content through the kernel.** Fetch functions in templates, the kernel API in PHP; never SQL on content
   tables ([Content from PHP](content-from-php.md)).
4. **Know your caches.** Clear the cache that follows a change, after the server runs the new code
   ([Caching for CMS developers](caching-for-cms-developers.md)).
5. **Keep templates clean and safe.** Wash output; no inline CSS or JavaScript; CSS from stylesheets with tokens
   ([Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md#rules-for-maintainable-templates)).
6. **No real people in tests.** Test accounts and mail use `example.invalid`; no copies of live personal data on
   development machines.
7. **Use the right names.** The product is Exponential; 7x is the company that leads it; the Exponential Foundation is
   the nonprofit ([Who is 7x?](../who-is-7x.md)).

## Pages from other audiences you will use

| Page | Why a CMS developer reads it |
|---|---|
| [Extension development: the book](../extension-development/README.md) | modules, operators, datatypes, workflows and releasing, in depth |
| [Reading paths through the book](../extension-development/reading-paths.md) | the builder path and the PHP developer path |
| [Setting up a development installation](../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md) | the detailed version of your first install |
| [Designers: start here](../designers/designers-start-here.md) | how your designers work: themes, tokens, dark mode, accessibility |
| [Designers' template pages](../designers/templates/templates-overview.md) | every template topic with examples |
| [The template language](../template-language/README.md) and [Template operators](../template-operators/README.md) | the language reference |
| [The layouts editor](../layouts-editor.md) and [Two ways to build](../build-two-ways/README.md) | what editors do with Layouts, and how code and the editor meet |
| [Site packages](../site-packages/README.md) | the installers and packages in depth |
| [Developers course](../trainers/developers/README.md) | the same ground as a course with exercises |
| [Guides](../../guides/README.md) | step-by-step guides for administrators: caches, workflows, languages, REST |
| [Features of 6.0](../../features/6.0/README.md) | what each feature of the 6.0 line does |
| [The installation guide](../../install/README.md) | installing, upgrading and migrating |
| [Application creators: start here](../creators/app-creators/app-creators-start-here.md) | applications on Exponential beyond websites |

## Finding these pages again

- In the Help center, use the audience filter **CMS Developers** or the category of this section, or open **All
  pages** and the folder `cms-developers`.
- Every page has **Edit on GitHub**: if a step changed or a fact is wrong, fix it for every developer after you.

## Related pages

- [Reading paths for CMS developers](reading-paths.md)
- [CMS developer FAQ](cms-developer-faq.md)
- [Who is 7x?](../who-is-7x.md) and [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
