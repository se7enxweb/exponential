---
title: Reading paths for CMS developers
audience: cms-developer
category: cms-development
order: 3
summary: For CMS developers, reading orders through this section and the related books by goal and available time. They cover a first day, a first week, coming from another CMS, building a first site, going headless, and going deep into extension development. Each path says what you can do at the end.
keywords: reading paths, learning path, where to start, first day, first week, onboarding, curriculum, CMS developer, migration, headless, extension development, course
type: help
---
# Reading paths for CMS developers

This page is for developers who want to know what to read in which order. Pick the path that matches your goal; each
says what you can do at the end. All pages of this section are listed in
[CMS developers: start here](cms-developers-start-here.md#the-pages-of-this-section).

## Contents

1. [The first day](#the-first-day)
2. [The first week](#the-first-week)
3. [Coming from another CMS](#coming-from-another-cms)
4. [Building a first site](#building-a-first-site)
5. [Going headless](#going-headless)
6. [Going deep: the extension development book](#going-deep-the-extension-development-book)
7. [Learning in a course](#learning-in-a-course)

## The first day

About four hours.

1. [Exponential for CMS developers](exponential-for-cms-developers.md): the system in overview.
2. [Development environment](development-environment.md): an installation running on your machine.
3. [The content model](the-content-model.md) and [Objects, nodes and locations](objects-nodes-and-locations.md).
4. [Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md).
5. [Caching for CMS developers](caching-for-cms-developers.md), sections 1 and 7.

At the end you can explain the model, find the template behind a page, and know which cache to clear.

## The first week

One topic a day, with your development installation open.

| Day | Read | Do |
|---|---|---|
| 1 | the first day above | install, click through the demo site and the administration |
| 2 | [Content classes and attributes](content-classes-and-attributes.md), [Choosing datatypes](choosing-datatypes.md), [Creating a content class](creating-a-content-class.md) | create a class with a full view template |
| 3 | [View modes](view-modes.md), [Fetching content in templates](fetching-content-in-templates.md), [Relations and embeds](relations-and-embeds.md) | a list page with line views and paging |
| 4 | [Sections, roles and policies](sections-roles-and-policies.md), [Users and registration](users-and-registration.md), [Siteaccesses and multisite](siteaccesses-and-multisite.md) | a members-only section tested with a test account |
| 5 | [Layouts for CMS developers](layouts-for-cms-developers.md), [Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md), [Extensions you will write](extensions-you-will-write.md) | a landing page in the layouts editor; your settings in your extension |

At the end you can build the parts of a typical site and know where each belongs.

## Coming from another CMS

Read the comparison first, then the model, then the migration guide if you are moving a site:

| From | Read |
|---|---|
| WordPress | [Coming from WordPress](coming-from-wordpress.md), then [WordPress and Exponential](../../install/migration/wordpress.md) |
| Drupal | [Coming from Drupal](coming-from-drupal.md), then [Drupal and Exponential](../../install/migration/drupal.md) |
| TYPO3 | [Coming from TYPO3](coming-from-typo3.md), then [Other applications and databases](../../install/migration/other-applications.md) |
| eZ Platform or Ibexa | [Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md), then [chapter 16](../../install/16-migrating-from-ez-platform-and-ibexa.md) |
| Anything else | [Mapping content to Exponential](../../install/migration/content-model-mapping.md), then [Importing content](importing-content.md) |

## Building a first site

In project order:

1. [Designing a content model](designing-a-content-model.md) and the [Content model review checklist](content-model-review-checklist.md).
2. [Creating a content class](creating-a-content-class.md), [Rich text](rich-text.md), [Images and image aliases](images-and-image-aliases.md).
3. [Templates and overrides](templates-and-overrides-for-cms-developers.md), [View modes](view-modes.md),
   [Layouts for CMS developers](layouts-for-cms-developers.md).
4. [Translations and languages](translations-and-languages.md), [Siteaccesses and multisite](siteaccesses-and-multisite.md),
   [URL aliases and routing](url-aliases-and-routing.md).
5. [Forms and information collection](forms-and-information-collection.md), [Search](search.md),
   [Workflows and triggers](workflows-and-triggers.md), [Cronjobs](cronjobs.md).
6. [Importing content](importing-content.md), [Site packages and installers](site-packages-and-installers.md).
7. [Performance](performance.md), [Security checklist](security-checklist.md), [Accessibility](accessibility.md),
   [SEO basics](seo-basics.md).
8. [Deploying a site](deploying-a-site.md) and the [Project launch checklist](project-launch-checklist.md).

## Going headless

[Headless delivery](headless-delivery.md), [The REST API](the-rest-api.md), [Content from PHP](content-from-php.md),
then the application creators' pages ([Application creators: start here](../creators/app-creators/app-creators-start-here.md)).

## Going deep: the extension development book

When the site needs code of its own, continue in [Extension development](../extension-development/README.md). Its own
[Reading paths](../extension-development/reading-paths.md) has a builder path (settings, templates, overrides, no PHP)
and a PHP developer path; both start where this section ends. Designers on your team start at
[Designers: start here](../designers/designers-start-here.md), and the template language has its own reference
([The template language](../template-language/README.md)).

## Learning in a course

The trainers' [Developers course](../trainers/developers/README.md) teaches the same ground in thirteen modules with
exercises and quizzes; [Exponential Layouts](../trainers/layouts/README.md) and
[Migrating to Exponential](../trainers/migration/README.md) are courses of their own.

## Related pages

- [CMS developers: start here](cms-developers-start-here.md)
- [CMS developer FAQ](cms-developer-faq.md)
- [Glossary for CMS developers](glossary-for-cms-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
