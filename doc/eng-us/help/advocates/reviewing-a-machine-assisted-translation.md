---
title: Reviewing a machine-assisted translation
audience: advocate
category: advocacy
order: 101
summary: How a native speaker reviews one of Exponential's machine-assisted administration translations - set up the language, walk the main screens, note wrong, awkward and inconsistent strings, fix them in the translation file, keep a glossary - with a review sheet.
keywords: translation review, machine-assisted, machine translation, native speaker, Spanish, French, Ukrainian, Hebrew, Latin, Klingon, glossary, consistency, quality
type: help
---
# Reviewing a machine-assisted translation

This page is for native speakers of a language whose administration translation is complete but machine-assisted.
According to [Languages in Exponential](../../languages.md#8-adding-or-completing-an-administration-translation), a
review of the machine-assisted Spanish, French, Ukrainian, Hebrew, Latin and Klingon translations "is the quickest
win". You do not need to be a developer.

## Contents

1. [Set up](#set-up)
2. [Walk the screens](#walk-the-screens)
3. [What to look for](#what-to-look-for)
4. [Keep a glossary](#keep-a-glossary)
5. [Fix and send](#fix-and-send)
6. [The review sheet](#the-review-sheet)

## Set up

- An installation you control ([Install in one command](../../features/6.0/install-in-one-command.md)).
- Switch your own interface language in the **Interface** box of the right column ([Languages](../languages.md)).
- If strings do not change, an administrator clears the translation cache
  (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`, per section 8).

## Walk the screens

Review in the order editors meet them:

1. Sign-in page and dashboard.
2. Content tree, an item's full view, the **Edit** form, **Preview**, **Send for publishing**.
3. **Manage versions**, the trash.
4. Media upload.
5. The layouts editor.
6. **Setup** pages an administrator uses.
7. Error and confirmation messages (try discarding a draft).

## What to look for

| Problem | Example |
|---|---|
| Wrong meaning | A button that says the opposite of what it does |
| Too literal | Word-for-word phrasing nobody would use |
| Inconsistent | The same English term translated three ways |
| Wrong register | Formal and informal address mixed |
| Too long | Text that breaks the layout |
| Untranslated | English left over |
| Broken placeholders | `%1` missing or moved so the sentence breaks |
| Right to left issues | Punctuation or numbers in the wrong place (Hebrew, Arabic) |

## Keep a glossary

Write down how key terms are translated, and use them everywhere: object, content class, draft, version, publish,
siteaccess, section, role, policy, trash, layout, zone, block. The help's [glossary](../../glossary.md) explains each
English term.

## Fix and send

- Fix strings in `share/translations/<code>/translation.ts` with Qt Linguist or an XML editor, keeping placeholders,
  entities and spaces ([Helping with translations](helping-with-translations.md#rules-that-keep-translations-working)).
- If you cannot edit files, open an issue with your review sheet; someone else can apply it
  ([Which tracker for which report](which-tracker-for-which-report.md)).
- Send fixes as pull requests, screen by screen.

## The review sheet

```text
TRANSLATION REVIEW                       Language: <code>   Version: Exponential <6.0.x>   Reviewer: <you>

| Screen          | English source            | Current translation       | Proposed              | Why        |
|-----------------|---------------------------|---------------------------|-----------------------|------------|
| Edit form       | Send for publishing       | <...>                     | <...>                 | meaning    |
| Dashboard       | My drafts                 | <...>                     | <...>                 | consistency|

Glossary decisions:
  draft = <...>   publish = <...>   content class = <...>
```

## Related pages

- [Helping with translations](helping-with-translations.md)
- [Proposing a contribution sprint](proposing-a-contribution-sprint.md)
- [Languages in Exponential](../../languages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
