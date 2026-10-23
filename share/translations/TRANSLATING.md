# Translating the administration

This file holds the rules that are the same for every language. What belongs to one language (the form of
address, the script and its typography, the glossary) is in that language's `GUIDE.md`, next to its catalogue:
`share/translations/<code>/GUIDE.md`. The languages Exponential supports, how complete each translation is and how
a new one is added are in [doc/eng-us/languages.md](../../doc/eng-us/languages.md) (sections 4 and 8).

## What is translated

- The kernel's catalogue `share/translations/<code>/translation.ts`. Its template is
  `share/translations/untranslated/translation.ts`, which holds every source string.
- The catalogues of the extensions, `extension/<name>/translations/<code>/translation.ts`. They use the glossary
  of the language's `GUIDE.md` too, so that a term reads the same in the kernel and in an extension.

A message counts as finished when its `<translation>` has text and no `type="unfinished"`.

## The rules

`tests/tests/lib/ezi18n/expTranslationQualityTest.php` checks the first four for every language on its list, in the
kernel's, ezoe's and ezjscore's catalogues. They hold for every other catalogue too.

1. **Placeholders** stay exactly as in the source, the same ones, as many, spelled the same: `%1`, `%name`,
   `%count`, `%login_link_start%`. Their order may follow the sentence. Nothing is glued to a placeholder that
   would make it longer: `%countni` or `%namewa` is a different placeholder. Choose a wording in which the
   placeholder stands free ("Count: %count"); a punctuation mark right after it is fine.
2. **HTML tags and entities** stay exactly as in the source (`<b>`, `<a href="...">` with its attributes unchanged,
   `<br />`, `&nbsp;`, `&lt;`, `&amp;`). The text between the tags is translated, and the tags may move with the
   sentence.
3. **Whitespace and line breaks**: leading and trailing whitespace as in the source, the same number of line
   breaks.
4. **Not empty, not the English text.** A translation identical to its source is allowed only where English is
   right in that language: a product name, a code, a word spelled the same. Such a source goes, with its reason, into
   `tests/tests/lib/ezi18n/translation-identical-allowlist.json` under the language's code. Never weaken the
   test instead.
5. **What is not translated**: product and program names (Exponential, eZ Publish, Solr, Tika, PHP, MySQL, Git,
   Composer, Redis, OpenAI and so on), formats and protocols (JSON, YAML, XML, HTML, CSS, SQL, URL, HTTP, REST, API,
   CSV, PDF, MCP, RAG), INI file names and settings (`[Section] Key=Value`), file paths, class and function names,
   commands (`php bin/php/...`), template code (`{...}`) and identifiers that are code, in backticks or quotes. They
   stay in Latin letters inside a text in another script.
6. **Final punctuation** as in the source: a full stop, an ellipsis ("..." or "…"), a colon after a label.
7. **Numbers** come in through placeholders; written digits stay 0-9 unless the language's guide says otherwise.

## Words that need their context

The source context (the template path in `<name>`) and the translator comment (`<comment>`) decide these. Each
`GUIDE.md` gives the word for each meaning.

| English | Meanings to keep apart |
|---|---|
| Order | an order in the shop, or a sort order |
| Section | always the Exponential section (a setting of each object), not a part of a page |
| State | the object state |
| Class | the content class (a PHP class is named as such) |
| Remove / Delete | remove = take out of a list or place, delete = destroy |
| View | to look at (a button) or a module view (a noun) |
| Override / Fallback | a template or setting that replaces another, or the one used when the first is missing |
| Layout / Settings | the page layout of Exponential Layouts, or configuration |
| Archive | an archived version or order, or a file archive (.tar, .zip, .phar) |
| Log | a log file, never the verb "log in" |
| Tag | a keyword tag (eztags) or an HTML/XML tag |
| Format | a file or data format, or text formatting |
| Editor | a person who edits, or the editing software |

## Forms of the interface

- **Buttons, menu items and links** are short. Each guide says whether the language uses the imperative, the
  infinitive or a noun, and the whole interface follows it.
- **Titles, column headings and tabs** are nouns or noun phrases.
- **Sentences, hints, questions and error messages** address the user in the form the guide sets, the same form
  everywhere.

## Checking and measuring

```bash
# The rules, for every language on the test's list
vendor/bin/phpunit --no-coverage tests/tests/lib/ezi18n/expTranslationQualityTest.php

# The finished share of a catalogue
php bin/php/ezchecktranslation.php <code> --allow-root-user
```

Then read the result in the running administration: a user preference or a siteaccess with `Locale=<code>`, after
clearing the translation cache (`php bin/php/ezcache.php --clear-tag=i18n --allow-root-user`).

## A language's GUIDE.md

Each guide has the same parts, so that a translator who knows one finds the way in another:

1. **Language and register**: which standard, which country, who else reads the catalogue (a locale that falls
   back to it).
2. **Form of address**: buttons, titles, sentences, with examples; what is not allowed.
3. **Script and typography**: letters, quotation marks, punctuation, spacing, numbers.
4. **Glossary**: English | translation, binding for the kernel and every extension.
5. **Decisions after the translation**: terms settled when the translations were compared, with the reason where it
   is not obvious. They take precedence over the glossary where both name a term.

When a term changes, change it in the guide first and then in every catalogue, so that the guide stays the
reference.
