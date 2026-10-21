---
title: Multilingual content
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 430
summary: Serve an app in several languages from Exponential content - locales such as eng-US and ger-DE, the Translation parameter of REST reads, languageLocale on create, the site's languages from explanguage services, translations of one object, fallbacks and untranslated items, and mapping device languages to locales.
keywords: multilingual, languages, translation, locale, Translation parameter, languageLocale, eng-US, ger-DE, explanguage, prioritized, exptranslation, fallback, always available, siteaccess, device language, mobile app
type: help
---
# Multilingual content

This page is for app developers whose app shows content in more than one language. It explains how Exponential names
languages, how to ask the APIs for one, and what happens with items that are not translated.

## Contents

1. [How Exponential stores languages](#how-exponential-stores-languages)
2. [Reading a language through REST](#reading-a-language-through-rest)
3. [The site's languages](#the-sites-languages)
4. [Writing in a language](#writing-in-a-language)
5. [Fallbacks in the app](#fallbacks-in-the-app)

## How Exponential stores languages

Every object can hold translations in several content languages, and has a main language. Languages are named by
locale codes such as `eng-US`, `eng-GB`, `ger-DE`, `fre-FR`, `nor-NO`. A site (a siteaccess) shows each object in the
first language of its `SiteLanguageList[]` that the object has; objects in none of those languages are hidden unless
they are "always available" ([Building a multilingual site](../../creators/website-creators/multilingual-sites.md#how-languages-work)).

## Reading a language through REST

Add `Translation=<locale>` to a node, object or field read:

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?ResponseGroups=Fields&Translation=ger-DE"
```

The controller sets the content's active language to that locale before it builds the answer. The `links` in the
answer carry the parameter on, so following them stays in the language. Three cautions from the code:

- The parser takes the value as given; a comment in `http_parser.php` notes that the locale is not checked yet. Send
  only locales the installation has.
- The `list` route does not set a language for its children; their names and fields come in the default language.
  Each child's `link` carries `Translation`, so a detail read of the child is in the right language.
- Without the parameter, the language is chosen by the installation's settings for the request; check what your
  installation returns before you rely on it.

## The site's languages

| Service | Gives | Access |
|---|---|---|
| `explanguage::prioritized` | the languages of the siteaccess in priority order | public |
| `explanguage::top` | the first of them | public |
| `explanguage::languages` | every content language with object counts | public |
| `exptranslation::ofObject::<object id>` | the translations of one object | `content/read` |
| `exptranslation::missing::<object id>` | the site languages an object lacks | `content/read` |
| `expnode::names::<node id>` | the node's name in every language | `content/read` |

Use them to build the app's language menu from the server instead of a list compiled into the app.

## Writing in a language

- REST create needs `languageLocale` ([Creating and removing content](../rest-api/creating-and-removing-content.md)).
- `expnode::create` and `expnode::update` take a POST field `language`.
- A translation of an existing item, with its own policy `content/translate`, is `exptranslation::translate`.

## Fallbacks in the app

1. Map the device language to a locale the site has (`de` to `ger-DE`); keep the mapping in configuration, not code.
2. Read with `Translation=<locale>`. If a field comes back empty while the main language has it, decide per field:
   show the main language with a hint, or hide the item.
3. Cache per locale: the same node in two languages is two cache entries
   ([Caching in the app](../offline-and-performance/caching-in-the-app.md)).
4. Right-to-left languages are supported by Exponential's content; the app's layout must mirror for them
   ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).

## Related pages

- [Content languages](../../../guides/content-languages.md)
- [Languages in Exponential](../../languages.md)
- [Selling in several languages and markets](../../businesses/multilingual/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
