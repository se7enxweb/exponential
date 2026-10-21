---
title: Content modeling for apps
audience: mobile-app-developer, app-creator, developer, website-creator
category: mobile-apps
order: 400
summary: The landing page of the content modeling pages for app developers - why the content classes, ids, languages and rich text of an Exponential site shape every screen of an app, and what to agree with the site team before you build.
keywords: content model, content classes, fields, remote id, deep links, languages, translations, rich text, ezxmltext, site team, mobile app
type: help
---
# Content modeling for apps

This folder is for app developers who build on content that editors maintain in Exponential. The app does not own
the content model: the site team defines the classes and the tree, and the app reads them. The pages here explain
what to look at and what to agree on, so the app does not break when editors do their normal work.

## Contents

1. [Why the model matters to an app](#why-the-model-matters-to-an-app)
2. [Agree before you build](#agree-before-you-build)
3. [Pages in this folder](#pages-in-this-folder)

## Why the model matters to an app

- Every screen maps to a **content class**: a list cell for `article`, a detail view for `product`.
- Every entry point maps to a **node**: the folder the news list starts from, the page a deep link opens.
- Every text has a **language**, and an item may exist in some languages only.
- Article bodies are **rich text**, which reaches the REST API as plain text.

## Agree before you build

| Topic | Agree on | Read |
|---|---|---|
| Classes and fields | which classes the app shows, which fields, and who announces changes | [Content classes for apps](content-classes-for-apps.md) |
| Entry points | the folders the app starts from, identified by remote id | [Node ids, remote ids and deep links](node-ids-remote-ids-and-deep-links.md) |
| Languages | which locales the app supports and what it shows when a translation is missing | [Multilingual content](multilingual-content.md) |
| Rich text | plain text, rendered XHTML or a structured export | [Rich text in apps](rich-text-in-apps.md) |
| Images | which named sizes (aliases) the app uses | [Image aliases for screen sizes](../media-and-images/image-aliases-for-screen-sizes.md) |

## Pages in this folder

| Page | Answers |
|---|---|
| [Content classes for apps](content-classes-for-apps.md) | Reading class definitions, mapping classes to screens, surviving class changes |
| [Node ids, remote ids and deep links](node-ids-remote-ids-and-deep-links.md) | Stable ids, URL paths and links from the web into the app |
| [Multilingual content](multilingual-content.md) | `Translation`, `languageLocale`, the site's languages and fallbacks |
| [Rich text in apps](rich-text-in-apps.md) | The three ways to show article bodies |

## Related pages

- [How Exponential content reaches an app](../how-exponential-content-reaches-an-app.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [The content model and editing content](../../../guides/content-model-and-editing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
