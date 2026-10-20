---
title: "Tender template: multilingual clause"
audience: government, project-manager, partner, editor
category: public-sector
order: 140
summary: Clause text for delivering an Exponential site in several official or community languages - languages and scripts, right-to-left support, language addresses, administration languages, translation workflow, encoding and testing per language - with blanks to fill.
keywords: multilingual clause, languages, official languages, translation, right to left, RTL, language switcher, administration language, UTF-8, tender template
type: help
---
# Tender template: multilingual clause

This page is for procurement officers in public bodies that publish in more than one language. The clause makes the
supplier deliver and test each language, not only switch it on. Background and the documented capabilities:
[Language and multilingual requirements](../requirements/language-and-multilingual-requirements.md).

**Template** - adapt to your procurement rules; not legal advice

## Contents
1. [The clause](#the-clause)
2. [Language annex](#language-annex)
3. [What an Exponential-based bid can show](#what-an-exponential-based-bid-can-show)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
G. LANGUAGES

G.1 Languages. The public website is delivered in the languages listed in the
    language annex, with the scripts and writing directions listed there.

G.2 Structure. Each language is reached by <path prefix / host name>; every page
    links to its available translations; pages declare their language to
    browsers and assistive technology.

G.3 Missing translations. Where a page has no translation in a language, the site
    <shows the page in <fallback language> with a notice / hides it from that
    language>, as set out in <annex>.

G.4 Templates and fixed text. All fixed text in templates, forms, e-mails,
    error pages and the cookie or consent notices is translated into each
    language.

G.5 Right-to-left. For right-to-left languages, layout, navigation, forms and
    the editor display in the correct direction; mixed-direction text (names,
    numbers, addresses) remains readable.

G.6 Administration. The administration is available to staff in <languages>, as
    far as translations of the software exist; the Supplier lists the coverage.

G.7 Workflow. The Supplier configures a translation workflow: <who translates,
    who approves, how missing translations are reported>.

G.8 Encoding. All text is stored and delivered as UTF-8; characters required by
    the languages in the annex are stored without loss. The Supplier names the
    database and its character set.

G.9 Testing. Acceptance tests are run in every language, including search,
    forms and e-mails.
```

## Language annex

```text
| Language | Locale code | Script | Direction | Pages | Address | Admin UI needed |
|----------|-------------|--------|-----------|-------|---------|-----------------|
| <name>   | <xxx-YY>    | <...>  | LTR / RTL | all / <subset> | <prefix or host> | yes / no |
```

Exponential's internal locale codes have the form `ger-DE`, `ara-SA` and similar, and browsers receive BCP 47 tags
([Internationalization standards](../../../../standards/internationalization.md)).

## What an Exponential-based bid can show

| Clause | Evidence |
|---|---|
| G.1, G.7 | [Content languages](../../../../guides/content-languages.md), [Languages](../../../languages.md) |
| G.2 | [Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md), [Language switcher and language URLs](../../../creators/website-creators/language-switcher-and-language-urls.md) |
| G.5 | [Right-to-left languages](../../../../features/6.0/right-to-left.md), [Right-to-left sites](../../../creators/website-creators/right-to-left-sites.md) |
| G.6 | [Languages in Exponential](../../../../languages.md) (administration translations and their completeness), [UI switch: interface languages](../../../../features/6.0/ui-switch-languages.md) |
| G.8 | UTF-8 internal encoding Complies; on MySQL and MariaDB the connection uses three-byte `utf8` (Partly complies) |
| G.4 gap | Interface translations have no plural forms (Does not comply) |

## Notes for the evaluator

- Ask to see a right-to-left page and the editor in a right-to-left language during the demonstration.
- G.6 depends on existing translations; a bidder may offer to complete one and contribute it back
  ([Contributing docs and translations](../../../partners/contributing-docs-and-translations.md)).
- More than 30 content languages need 64-bit PHP ([Requirements](../../../../install/02-requirements.md)).

## Related pages
- [Tender templates](README.md)
- [Language and multilingual requirements](../requirements/language-and-multilingual-requirements.md)
- [Acceptance testing clause](acceptance-testing-clause.md)
- [Multilingual sites](../../../creators/website-creators/multilingual-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
