# Content languages: adding, showing, translating and removing them

This guide teaches everything about the languages content is written in: what a content language is and how it
differs from the language of the interface, how to add one, how to make a siteaccess show it, how editors translate
into it, what "always available" and "main language" mean, how to move content from one language into another, and
how to remove a translation or a whole language without losing anything you meant to keep. It ends with complete
workflows, from adding German to an English site to retiring a language that was used by mistake, and with the
problems people run into.

It is written for administrators and site builders. Read sections 1 and 2 first; after that every section stands on
its own. Every setting, button and command below was checked against the code of Exponential 6.0, and every number
shown is real: it was read on the demonstration server (alpha.se7enx.com) on 5 October 2026. Nothing in this guide
was tried out by adding or removing a language on that server, because removing a language is not something to
practise on content people use; section 9 shows how to try it safely.

[Guides](README.md) · Feature reference: [Translations and languages](../features/6.0/translations-and-languages.md)
· In the installation book: [10.14 Multi-language sites](../install/10-after-installing.md#1014-multi-language-sites)

## In short

- **Setup > Languages** (`content/translations`) lists the content languages: for each one how many objects and
  classes have a translation in it, which siteaccesses show it, whether it can be removed, and a hint when few
  objects use it or no siteaccess lists it.
- A content language is not the interface language. Content languages hold your articles; the interface language
  (`Locale`) and the `.ts` translation files decide the words of buttons and menus.
- Adding a language translates nothing and changes no siteaccess. Translate content in the editor, then add the
  language to `site.ini [RegionalSettings] SiteLanguageList` of each siteaccess that should show it.
- Each siteaccess shows an object in the first language of its `SiteLanguageList` that the object has. Objects in
  none of them are hidden, unless they are **always available**.
- A language can only be removed when no object and no class has a translation in it, unsaved class edits
  included. The kernel refuses anything else, so removing a language never deletes content.
- Removing an object's **translation** does delete content, for good: all its fields in that language, in every
  version. Translate first, make the new language the main one, then remove the old translation.
- An installation holds at most 62 content languages (on 64-bit PHP). Each one is a bit; an object records all its
  translations in one number.

## Contents

- [1. Content languages and interface languages](#1-content-languages-and-interface-languages)
- [2. How languages are stored](#2-how-languages-are-stored)
- [3. The content languages page](#3-the-content-languages-page)
- [4. Adding a language](#4-adding-a-language)
- [5. Showing a language: SiteLanguageList and siteaccesses](#5-showing-a-language-sitelanguagelist-and-siteaccesses)
- [6. Main language and always available](#6-main-language-and-always-available)
- [7. Translating content in the editor](#7-translating-content-in-the-editor)
- [8. Moving content from one language into another](#8-moving-content-from-one-language-into-another)
- [9. Removing translations and languages safely](#9-removing-translations-and-languages-safely)
- [10. Commands for the shell](#10-commands-for-the-shell)
- [11. Workflows, step by step](#11-workflows-step-by-step)
- [12. Troubleshooting](#12-troubleshooting)
- [References](#references)

## 1. Content languages and interface languages

Exponential has two kinds of language, and they are set in different places.

| | Content language | Interface language |
|---|---|---|
| What it is | A language objects (articles, folders, products, users) can be written in | The language of the texts of the administration and of the templates: buttons, menus, messages |
| Where the texts are | In the database, one translation per object and language | In translation files: `share/translations/<locale>/translation.ts` and the `translations/` folder of each extension |
| Where it is managed | **Setup > Languages** (`content/translations`), this guide | `site.ini [RegionalSettings] Locale` per siteaccess, and the user preference of an administrator |
| What chooses it for a page | `SiteLanguageList` of the siteaccess | `Locale` of the siteaccess (with `TextTranslation=enabled`) |
| How you check it | The content languages page | `php bin/php/ezchecktranslation.php ger-DE` |

The two are independent. A siteaccess can show German content with an English interface and the other way round.
On the demonstration server the test siteaccess `admintest_ger` does exactly that: its `Locale` is `ger-DE`, so the
administration speaks German, while its `SiteLanguageList` is `eng-US`, so the content it lists is the English
content.

This guide is about content languages. For the interface, see
[Translations and languages](../features/6.0/translations-and-languages.md).

## 2. How languages are stored

A content language is one row of the table `ezcontent_language`: an ID, a locale code such as `ger-DE`, and a name.

**The ID is a single bit.** The first language gets ID 2, the next 4, then 8, 16 and so on; ID 1 is reserved. When
a language is added, it gets the lowest power of two that no other language has. The demonstration server has:

| ID | Bit | Locale | Name |
|---|---|---|---|
| 2 | 1 | `eng-US` | English (American) |
| 4 | 2 | `ger-DE` | German |
| 8 | 3 | `eng-GB` | English (United Kingdom) |

**An object records its languages in one number**, its *language mask*: the sum of the IDs of the languages it has
a translation in. An object in American English and German has the mask 2 + 4 = 6. Bit 0 (the value 1) is the
*always available* flag (section 6), so an always-available object in American English alone has the mask 3. A class
has a language mask too, for the translations of its name and description.

**Each object has a main language** (its *initial language*), the one it was created in or the one an editor chose
later. It is shown where no better translation exists, and it cannot be removed from the object while it is the main
one.

**At most 62 languages** fit: PHP's integers have 64 bits, one is the sign and one the always-available flag
(`eZContentLanguage::maxCount()`, 30 on 32-bit PHP). The page tells you how many more can be added.

Because languages are bits, the kernel answers "which objects are in German?" with one bit test per row
(`language_mask & 4 > 0`), on every database engine it supports.

## 3. The content languages page

Open **Setup > Languages** in the administration (`/content/translations`). The page needs the policy
`content/translations`; administrators have it.

### The overview

At the top, five figures:

- **Content languages**: how many there are.
- **More can be added**: how many of the 62 are left.
- **Need a look**: languages few objects use (fewer than 10) or that no siteaccess lists. These are the cases worth
  a second look; section 8 explains what to do about them.
- **Unused, can be removed**: languages no object and no class has.
- **Default for new content in this siteaccess**: `ContentObjectLocale` of the siteaccess you are in, the
  language new content is created in when nothing else is chosen.

### One card per language

Each language has a card with its flag, name and locale, and badges:

| Badge | Meaning |
|---|---|
| Default for new content | It is `ContentObjectLocale` of this siteaccess |
| Shown first by N | N siteaccesses list it first in their `SiteLanguageList`: it is their main language |
| In no SiteLanguageList | No siteaccess lists it, so no visitor sees content in it (except always-available objects) |
| Only N objects | Fewer than 10 objects have a translation in it |
| In use / Unused | Whether any object or class has a translation in it |

Below the badges, four facts: the number of **objects** with a translation in the language, and of those how many
have it as their main language and how many have nothing else; the number of **classes** with a name in it; the
**language ID** and its bit; and the **siteaccesses** that show it, the ones that show it first highlighted.

When a language needs a look, the card says why and what to do, with a link to the right part of the language's own
page. The last line of each card says whether it can be removed, and if not, what is in the way.

The checkbox **Select for removal** is only on for languages nothing uses. **Remove selected** removes the selected
ones, after asking (with JavaScript on) and naming the siteaccesses that still list them. **Add language** opens the
form of section 4.

### Siteaccesses and their languages

Below the list, every siteaccess of `site.ini [SiteAccessSettings] AvailableSiteAccessList` with its
`SiteLanguageList` in order, as that siteaccess reads it with its own overrides. The first language of each is
highlighted. A locale a siteaccess lists that is not a content language is marked in red: that siteaccess asks for a
language that does not exist, and shows nothing in it.

Only siteaccesses in `AvailableSiteAccessList` are counted. A settings folder `settings/siteaccess/<name>/` that is
not listed there serves no page, so its `SiteLanguageList` does not show anything either.

### The page of one language

Click a language's name (`/content/translations/<id>`). The page shows the same figures larger, the siteaccesses
that show it, a section **Removing this language** that says whether it can be removed and, if not, the steps to
take it out of use, then three folding sections:

- **Objects with a translation in it**: up to 25, the ones changed last first, each with its class, main language,
  all its languages and whether it is always available. The object names link to the objects.
- **Classes with a name in it**: every class that has the language, including class edits that were started and
  never stored (marked *unsaved edit*); those count for the kernel too.
- **The locale**: how dates, numbers and money are written in this language, from `share/locale/<code>.ini`.

The **Remove** button on this page is off while anything uses the language, and also for the language the
administration interface itself runs in.

### Worked example: the overview on the demonstration server

On 5 October 2026 the page showed:

| Language | Objects (main, only) | Classes | Shown by | Can be removed |
|---|---|---|---|---|
| English (American), `eng-US` | 368 (368, 325) | 81 (74 stored, 7 unsaved edits) | 14 siteaccesses, first in 13 | No |
| German, `ger-DE` | 53 (10, 10) | 6 | `bold_ger`, first there | No |
| English (United Kingdom), `eng-GB` | 0 | 0 | none | Yes |

`eng-GB` was the case this page was redesigned to make obvious: earlier that day it had 6 classes and no siteaccess
of `AvailableSiteAccessList` listing it (only the unused settings folder `settings/siteaccess/eng` did). Its card
said *In no SiteLanguageList* and *Not shown by any siteaccess*, and linked to the six classes. Once their `eng-GB`
translations were removed, the card read *Unused* and its checkbox came on; later that day the language was removed, and the page has listed two languages since.

## 4. Adding a language

1. Open **Setup > Languages** and press **Add language**.
2. Under **Language and country**, type part of the name, the code or the country into **Find a language**
   (`german`, `ger`, `austria`, `deutsch` all work); the list narrows as you type, and Enter picks the first match.
   Without JavaScript the list is a plain list box, sorted by name. The list holds the locales of `share/locale`
   without their variations (43 on a standard installation); languages already added are shown but cannot be
   chosen.
3. Press **Add language**.

The page returns to the overview with the message *German (ger-DE) was added*. The language gets the next free bit,
its name is taken from the locale (`German`), and the content view cache is cleared, because lists that filter by
language change.

### A custom language

For a language that is not in the list, choose **Custom** at the top of the list and fill in:

- **Name of custom translation**: the name the administration shows, such as `Deutsch (Schweiz)`.
- **Locale for custom translation**: three letters for the language, a dash, two for the country: `ger-CH`.

A file `share/locale/<code>.ini` must exist for that code, because the locale also decides how dates and numbers are
written. If it does not, the form comes back with *There is no locale ger-CH in share/locale* and keeps what you
entered. Copy the nearest locale file (`ger-DE.ini`) to the new name and adjust it, then try again. A code that is
not in the form `xxx-YY` comes back with an explanation of the form.

### What adding does not do

It translates nothing, and it changes no siteaccess: no visitor sees anything new yet. The form says so, and lists
the next steps: translate content (section 7), and list the language in `SiteLanguageList` (section 5).

Adding a language that already exists does nothing and says *German (ger-DE) is already a content language*. When
all 62 bits are taken the page says so and adds nothing.

## 5. Showing a language: SiteLanguageList and siteaccesses

What a visitor sees is decided per siteaccess, in `settings/siteaccess/<siteaccess>/site.ini.append.php`, group
`[RegionalSettings]`:

| Setting | What it does |
|---|---|
| `SiteLanguageList[]` | The content languages this siteaccess shows, most preferred first. An object is shown in the first of these it has. |
| `ShowUntranslatedObjects` | `disabled` (shipped): objects in none of those languages are hidden, unless always available. `enabled`: they are shown in their main language. |
| `ContentObjectLocale` | The default language for new content, and the fallback when `SiteLanguageList` is empty. Do not change it on a site with content without reading the warning in `settings/site.ini`. |
| `Locale` | The interface language and the locale for dates and numbers. |
| `TextTranslation` | `enabled` to translate interface texts into `Locale`. |

A siteaccess for German content with English as the fallback:

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

The empty `SiteLanguageList[]` line first clears what `settings/site.ini` or an override gave the list, so this
siteaccess has exactly the languages listed. Here a German visitor sees the German translation where there is one and
the American English one otherwise; an object only in British English is hidden.

A multi-language site usually has one public siteaccess per language, matched by a URL element (`/de/`) or a host
name, all sharing one database and listed in `site.ini [SiteAccessSettings] RelatedSiteAccessList[]`. How to set up
matching is in the installation book,
[10.2 Siteaccesses and site addresses](../install/10-after-installing.md#102-siteaccesses-and-site-addresses), and
the multi-language settings in [10.14](../install/10-after-installing.md#1014-multi-language-sites). For links from
one language version of a page to another, `[RegionalSettings] TranslationSA[]` names the translation siteaccesses,
and the template operator `language_switcher` with the module `switchlanguage/to` builds the links.

After changing a `SiteLanguageList`, clear the INI and content caches (section 10). The content languages page reads
the lists fresh on every visit, so its siteaccess table shows the change at once.

## 6. Main language and always available

**The main language** of an object is its initial language: the language it was created in, or the one an editor
chose later in the object's **Translations** window (radio button in the **Main** column, then **Set main**). The
main translation cannot be removed from an object; another translation has to become the main one first. On the
content languages page, the figure *in it as main language* counts objects with this language as their main one.

**Always available** is a flag per object, bit 0 of its language mask. An always-available object is shown in its
main language on a siteaccess that lists none of its languages, where it would otherwise be hidden. Folders, users,
user groups and media are usually always available, so that a German site still finds the user who wrote an
article, or the image folder. Set it in the object's **Translations** window: **Use the main language if there is no
prioritized translation**, then **Update**. A class has a default for new objects (*Always available* in the class
edit).

On a language's own page, objects that are always available are marked in the object list.

## 7. Translating content in the editor

Editors need the policies `content/edit` (or `content/translate`) and `content/create` for the language; all three
can be limited by **Language** in the role editor, so a translator may be allowed German and nothing else.

To translate an object:

1. Open the object and press **Edit**. When it can be edited in more than one language, the page *Edit
   &lt;name&gt;* opens.
2. Under **New translation**, choose the language to add. Under **Translate based on**, choose the existing
   translation to start from (its fields are copied into the new one), or **None** to start empty.
3. Press **OK**, translate the fields and **Publish**.

The object now has one more language; the content languages page counts it at once.

Each object's **Translations** window (in the full view of the administration) lists its languages with the main one
marked, and has **Remove selected** for translations, **Set main** for the main language and the always-available
setting. Classes have the same window on their page under **Setup > Classes**, for the translations of the class
name and description.

## 8. Moving content from one language into another

This is what to do when content was created in the wrong language, for example a set of newsletter objects created
in British English (`eng-GB`) on a site that shows American English (`eng-US`), so the objects are in no
`SiteLanguageList` and never show. The content languages page points such a language out: *In no SiteLanguageList*,
*Not shown by any siteaccess*, or *Only N objects*, with a link to the list of objects on the language's page.

For each object on that list:

1. **Translate it into the right language.** Edit, choose **New translation** `eng-US`, **Translate based on**
   `eng-GB`, adjust the text if needed, **Publish**. Nothing is lost: both translations exist now.
2. **Make the right language the main one.** In the object's **Translations** window, select `eng-US` in the
   **Main** column and press **Set main**.
3. **Remove the wrong translation.** In the same window, select `eng-GB` and press **Remove selected**, then
   confirm. This deletes the `eng-GB` fields of the object in every version (section 9), which is what you want once
   step 1 has copied them.

Then the classes: for each class on the language's page, open it under **Setup > Classes**; in its **Translations**
window make another language the main one if `eng-GB` is (**Set main**), then remove the `eng-GB` translation. A
class marked *unsaved edit* is a class edit that was started and never stored: open the class, press **Edit**, and
**Store** or **Cancel** the edit; until then it counts as a class in that language.

When the language's page shows 0 objects and 0 classes, it can be removed (section 9), or kept for later.

No shipped console command moves translations in bulk; the steps above are the supported way, one object at a
time, with the editor's permissions and workflows applying. For hundreds of objects, write a script against the
kernel API, try it on a copy of the database, and keep a backup
([10.9 Backups and restore](../install/10-after-installing.md#109-backups-and-restore)).

The other way out, when the content was right and only the settings were missing, is to add the language to the
`SiteLanguageList` of the siteaccess that should show it (section 5).

## 9. Removing translations and languages safely

### What removes content, and what does not

| Action | Where | What it deletes | Can it be undone? |
|---|---|---|---|
| Remove a **language** | Setup > Languages | Only the language row. The kernel refuses while any object or class has a translation in it. | Add it again; it may get a different bit, which is fine because nothing used the old one |
| Remove an object's **translation** | The object's Translations window | All fields and names of the object in that language, in every version, and every version that was started in that language | No, only from a backup |
| Remove a class's **translation** | The class's Translations window | The name and description of the class in that language | Translate the class again |

So removing a language never deletes content: it is refused until nothing uses the language. The content that
matters is removed one translation at a time, in each object's own window, after a confirmation page; the main
translation cannot be removed at all.

### What blocks removing a language

- **Objects** with a translation in it (any status: published, draft, archived). The card shows how many.
- **Classes** with a name in it, including **unsaved class edits**. The language's page lists them.

What the page warns about but allows:

- **Siteaccesses that still list it.** After removal they would ask for a language that does not exist; the page
  names them, and the confirmation asks you to take the language out of their `SiteLanguageList` too.
- **The interface language.** The language's own page does not offer removal for the language the administration
  runs in.

### Removing a language

1. On **Setup > Languages**, check that the card says *Unused* and *Can be removed: no object and no class has a
   translation in it.*
2. Tick **Select for removal** and press **Remove selected**. With JavaScript on, a confirmation lists the
   languages and the siteaccesses that still list them.
3. The page reports *English (United Kingdom) (eng-GB) was removed.*, and, when siteaccesses still list it, names
   them.
4. Take the locale out of those siteaccesses' `SiteLanguageList` and clear the INI cache (section 10).

If something used the language after all (an object was translated meanwhile), the page reports *was not removed:
N objects and M classes still have a translation in it.* and nothing changes.

### Trying it without risk

To see the add and remove forms work, use a language no content has and remove it again at once: add, for example,
`epo-EO` (Esperanto), check that its card says *Unused* and *In no SiteLanguageList*, select it and remove it.
Compare the list of languages before and after; nothing else changes. Never practise on a language your content
uses.

## 10. Commands for the shell

Languages are added and removed from the page; no shipped console command adds or removes a content language or
moves translations. What the shell is for is caches, URL aliases and the interface translations. Run from the
installation root; add `--allow-root-user` when you run as root.

| Command | When |
|---|---|
| `php bin/php/ezcache.php --clear-id=content_language` | The cached list of content languages, after the table was changed by hand or by a script |
| `php bin/php/ezcache.php --clear-tag=ini` | After changing a `SiteLanguageList` or any `[RegionalSettings]` setting |
| `php bin/php/ezcache.php --clear-tag=content` | After a change to languages or translations, so lists and pages are drawn again (adding or removing a language from the page does this itself) |
| `./console exp:updateniceurls --update-nodes` or `php bin/php/updateniceurls.php -s admin` | Rebuild the URL aliases of all nodes, for example after many translations were added by a script; aliases are kept per language |
| `./console exp:verify_aliases` | Check the URL alias table; `--fix` repairs what is safe to repair, `--sql` shows the queries |
| `php bin/php/ezchecktranslation.php ger-DE` | How complete the **interface** translation of a locale is (not content) |
| `php bin/php/ezgeneratetranslationcache.php` | Build the cache of the interface translation files; `--ts-list` limits it to some locales |
| `php bin/php/ezcache.php --clear-tag=i18n` | After changing a `.ts` interface translation file |

Velocity (the application server on port 8080) keeps its own response cache: after any of these, also run
`./console exp:velocity cache clear`.

## 11. Workflows, step by step

### Adding German to an English site

The site has one public siteaccess, `site`, with `SiteLanguageList[]=eng-US`.

1. **Add the language.** Setup > Languages > Add language, type `german`, choose *German · Deutsch · Germany ·
   ger-DE*, press Add language. The overview shows a new card *German, ger-DE*, *Not used yet*, *In no
   SiteLanguageList*.
2. **Give German a siteaccess.** Copy `settings/siteaccess/site` to `settings/siteaccess/site_de`, and in its
   `site.ini.append.php` set the `[RegionalSettings]` of section 5 (`Locale=ger-DE`, `SiteLanguageList[]=ger-DE`
   then `eng-US`). Add `site_de` to `AvailableSiteAccessList[]` and `RelatedSiteAccessList[]` in
   `settings/override/site.ini.append.php`, and set up the matching (a `/de` URL element or a host name, see
   [10.2](../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
3. **Clear the caches**: `php bin/php/ezcache.php --clear-tag=ini,content`, then
   `./console exp:velocity cache clear`. The German card now shows *Shown first by 1* and the chip `site_de`.
4. **Translate.** Start with the front page and the main menu folders (section 7). Until a page is translated,
   `site_de` shows its English translation, because `eng-US` is the second language of its list.
5. **Mark the shared objects always available** where they are not already (users, media folders), so they show on
   `site_de` even without a German translation.
6. **Give translators a role** with `content/translate` and `content/edit` limited to the language `German`.
7. **Check**: open `/de/` (or the German host), and the German card's object count on the languages page.

### Retiring a language that was used by mistake

The case of section 8: a language with few objects, or in no `SiteLanguageList`.

1. Open the language's page from its card. Note the objects and classes listed.
2. Move each object (section 8, steps 1 to 3) and each class into the right language. Store or cancel every
   *unsaved edit* class.
3. Reload the page until it says *No object and no class has a translation in ... so it can be removed.*
4. Remove the language on the overview (section 9) and take it out of every `SiteLanguageList`.
5. Clear the INI and content caches.

### Making a hidden language visible

When the objects are right and only no siteaccess lists their language: add the locale to the `SiteLanguageList` of
the siteaccess that should show it, at the position you want (first means preferred), clear the INI and content
caches, and check that the card now shows that siteaccess.

## 12. Troubleshooting

**An object disappeared from a site after a language change.** The siteaccess lists none of the object's languages
and the object is not always available. Look at the object's languages on its Translations window and at the
siteaccess table of the languages page. Add a translation, make the object always available, or add the language to
the `SiteLanguageList`.

**"Remove selected" does nothing for a language.** Its checkbox is off because objects or classes still have it; the
card's last line says how many. If the page says 0 objects but some classes, look for *unsaved edit* classes on the
language's page: a class edit that was never stored counts.

**The class count on the card is higher than the number of classes in Setup > Classes.** The difference is unsaved
class edits, which the kernel counts too. The language's page lists them marked *unsaved edit*.

**The page says a language is in no SiteLanguageList, but a settings folder lists it.** Only siteaccesses in
`AvailableSiteAccessList` serve pages, so only they count. Either add the siteaccess there or treat the language as
not shown.

**A siteaccess lists a locale marked "not a content language".** That language was never added, or was removed. Add
it on the page, or take it out of the list.

**The add form says the locale is not in share/locale.** Custom languages need a locale file; copy the nearest one
(section 4).

**"No language was added: this installation already has the most languages it can hold."** All 62 bits are taken.
Remove a language nothing uses first.

**After adding a language the editor does not offer it.** The editor offers only languages the user may use: check
the **Language** limitation of the user's `content/edit`, `content/translate` and `content/create` policies.

**German texts in the interface, English content (or the other way round).** That is the separation of section 1:
set `Locale` for the interface, `SiteLanguageList` for content.

**The page looks different on port 8080 than through Apache.** Velocity caches responses and keeps PHP classes
loaded: run `./console exp:velocity cache clear`, and after an update of the kernel `./console exp:velocity deploy`.

## References

- [Translations and languages](../features/6.0/translations-and-languages.md): interface translations, HTML language
  tags, languages in a new installation.
- [10.14 Multi-language sites](../install/10-after-installing.md#1014-multi-language-sites) and
  [10.2 Siteaccesses and site addresses](../install/10-after-installing.md#102-siteaccesses-and-site-addresses) in
  the installation book.
- [The content model and editing content](content-model-and-editing.md): objects, versions and the editor.
- [Operating a site](operating-a-site.md): which caches to clear when.
- [Admin list paging](../features/6.0/admin-list-paging.md): the page size of the language list.
- Settings: `settings/site.ini [RegionalSettings]` (every setting with its comment), `[SiteAccessSettings]
  AvailableSiteAccessList` and `RelatedSiteAccessList`.
- Code: `kernel/classes/ezcontentlanguage.php` (adding, removing, the counts, the masks, `maxCount()`),
  `kernel/private/classes/views/content/translations.php` (the page: counts per language, siteaccess lists, the
  hints), `kernel/classes/ezcontentobject.php` (`removeTranslation()`), templates
  `design/admin4/templates/content/translations.tpl`, `translationview.tpl` and `translationnew.tpl` (the same in
  `design/admin`), tests `tests/tests/kernel/classes/expContentLanguagesOverviewTest.php` (no database) and
  `expContentLanguagesCountsLiveTest.php` (read-only, on an installation).
