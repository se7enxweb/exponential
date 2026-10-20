# Help maintained as content: DITA export, stored e-books, overdue reviews (6.0.15)

Three parts of the help plan ([help-system.md](../../specifications/6.0/help-system.md)) that were still open after
phase 1 of [help maintained as content](../../features/6.0/help-content.md) are in 6.0.15. This page says what
changes for a site that runs it, and what to do after the deploy.

## What changes

| Part | What it does | Where |
|---|---|---|
| DITA export | the Released manuals, topics and glossary as DITA 1.3 bookmaps, DITAVAL files and topics, checked after writing | `./console exp:help:content dita`; **Download DITA** on a manual's page and **Download all as DITA** on the list of manuals |
| Stored e-books | the EPUB and PDF of every Released manual as file objects below E-books (`help-ebooks`), a new version per new build, rebuilt when an object of the help is set to Released | `./console exp:help:content ebooks`; the state assignment; the manual's page lists them |
| Overdue reviews | topics whose review date has passed, or that have been in Review longer than `ReviewMaxDays` without a change | the help's dashboard, for who can act on them; `./console exp:help:content review` |

Nothing here changes a site that has not installed help as content (`exp:help:content install`): the dashboard shows
no list, the release hook returns at once, the manual pages do not exist.

## Behaviour changes

- **A release can now create content.** Setting an object of the help to Released (in the administration, or through
  `eZContentOperationCollection::updateObjectState()`) builds the e-books of the Released manuals that contain it and
  publishes them as objects of the class `file` below E-books, as the user who released. The objects get the state
  Released and the section `help_public` (public manual) or `help_internal`. To keep releases free of this, set
  `exphelp.ini [ContentSettings] EbooksOnRelease=disabled` and build the books with the command when you want them.
- **A public e-book is downloadable without login.** The book of a manual whose audience is public goes to the section
  `help_public` with the state Released, which Anonymous may read (the help's own policy). It carries only public,
  Released topics, chapters and figures. Check the audience of your manuals before the first build.
- **EPUB files are dated by their content.** The entries of an EPUB and its `dcterms:modified` are the newest change
  of the manual and its topics, not the time of the build. Two builds of the same content are the same file.
- **The kernel calls the help when states are assigned.** `eZContentOperationCollection::updateObjectState()` calls
  `expHelpContentEbooks::onStateAssign()` after the `content/state/assign` event. It returns at once for objects that
  are not help, states other than Released, or when the setting is disabled.
- **The help's dashboard has a new card** for help editors and release managers. Others see nothing new.

## New settings: exphelp.ini [ContentSettings]

| Setting | Default | |
|---|---|---|
| `EbooksOnRelease` | `enabled` | a release builds the stored e-books again; `disabled`: only the command does |
| `ReviewMaxDays` | `14` | days a topic may stay in Review without a change before it is overdue |
| `ReviewListLimit` | `10` | at most this many overdue topics on the dashboard |

## After the deploy

The new classes must be in the autoload array, and a server that keeps PHP running (PHP-FPM, Exponential Velocity)
must load the changed kernel file `kernel/content/ezcontentoperationcollection.php`:

```
php bin/php/ezpgenerateautoloads.php -k        # or: ./console exp:velocity deploy --kernel
./console exp:help:content ebooks -s <admin siteaccess> --dry-run
./console exp:help:content ebooks -s <admin siteaccess>
./console exp:help:content review -s <admin siteaccess>
```

There is no database change. The first `ebooks` run creates one file object per manual and format; a second run
prints `No change.`. A server process started before the deploy shows no review card and offers no DITA download
until it is restarted (the classes are looked up first; nothing fails).

The interface strings are in the 24 translation catalogues that have the help's context (`kernel/exphelp`); clear the
translation cache with the other caches.
