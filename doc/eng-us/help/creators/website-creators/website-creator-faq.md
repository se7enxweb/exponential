---
title: "Website creators: frequently asked questions"
audience: website-creator, admin
category: website-creation
order: 750
summary: Short answers to the questions website creators ask most - changes that do not show, templates, menus, languages, forms, mail, images, URLs, layouts, speed and where to get help - each with the page that explains it.
keywords: FAQ, questions, troubleshooting, change does not show, cache, template not used, menu, language, mail not sent, image size, URL, layouts, help, website creator
type: help
---
# Website creators: frequently asked questions

Short answers with the page that explains each one.

## My template change does not show

Clear the template cache (`--clear-tag=template`); a new template file or override rule also needs
`--clear-id=template-override`; then the content cache and, under Velocity, its response cache. See
[Performance and caching](performance-and-caching.md#clearing-caches-in-the-right-order).

## Which template made this part of the page?

Switch on template path comments and search the page source for `START`
([Designs and the fallback chain](designs-and-the-fallback-chain.md#reading-the-chain-of-a-siteaccess)).

## How do I change one kind of page?

An override rule matching the class identifier ([Template overrides](template-overrides.md)).

## A folder does not appear in the menu

Its class is not in `TopIdentifierList[]`, it is hidden, or the menu's cache block still holds the old menu
([Menus and navigation](menus-and-navigation.md)).

## Visitors get "access denied" on a new section

Add `content/read` for the section to the Anonymous role ([Visitor permissions](visitor-permissions.md)).

## A page in my second language is missing

The object has no translation in any language of the siteaccess's `SiteLanguageList[]` and is not always available
([Building a multilingual site](multilingual-sites.md)).

## Form mails do not arrive

A fresh installation writes mail to files; set a real transport and a real sender
([Forms with the information collector](forms-with-the-information-collector.md#mail-to-a-receiver)).

## Images are the wrong size

Define an alias for that use and clear generated variations ([Images and image variations](images-and-image-variations.md)).

## An old address shows "not found"

Add it as an alias of the node, or a wildcard ([Redirects and moved content](redirects-and-moved-content.md)).

## My landing page layout is not used

A rule with a higher priority matches first, or the layout is not published ([Layout rules](layout-rules.md)).

## The site is slow

Measure with `exp:benchmark`, then follow the levers in order ([Performance and caching](performance-and-caching.md)).

## Where do I report a bug?

The help's **Report an error** page and [When something goes wrong](../../when-something-goes-wrong.md).

## Related pages

- [Website creators: start here](website-creators-start-here.md)
- [The website creator's reading path](website-creators-reading-path.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
