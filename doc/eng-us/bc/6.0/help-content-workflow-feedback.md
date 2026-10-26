# Help maintained as content: review workflow, "Was this helpful?", token clients, EPUB checks, history, public pages (6.0.15)

The parts of the help plan ([help-system.md](../../specifications/6.0/help-system.md)) and its style guide that were
still open, or only a convention, after [the DITA export, stored e-books and overdue reviews](help-content-dita-ebooks-reviews.md)
are in 6.0.15. This page says what changes for a site that runs [help maintained as content](../../features/6.0/help-content.md),
and what to do after the deploy.

## What changes

| Part | What it does | Where |
|---|---|---|
| The author does not release | the state Released is taken out of an assignment when the current user created the help object's current version | every state assignment (`eZContentOperationCollection::updateObjectState()`) |
| Review workflow at the topic | the card **Review and release** under a topic: the steps the user's roles allow as buttons, the style guide check of the topic, its answers to "Was this helpful?" | `exphelp/topic/<help_id>` (admin, admin4), the POST view `exphelp/state` |
| Released help_ids | every help_id is recorded when its topic is released; the validator reports a rename (`help-id.renamed`), a reuse (`help-id.reused`) and a released help_id without a topic (`help-id.gone`) | table `exphelp_help_id`, `check --fix` |
| More style guide rules | `review-date.too-far`, `version.successor`, `verb.mix` (WARNING), `language.sentence-too-long`, `language.paragraph-too-long` (NOTICE) | `./console exp:help:content check` |
| "Was this helpful?" | yes, or no with a reason, per topic and day, nothing about who answered | table `exphelp_feedback`, `exphelp/feedback`, `./console exp:help:content feedback` |
| Context help for token clients | `/api/help/v1/topic/<help_id>` and `/manual/<remote_id>` with an OAuth token or a personal API key with the new scope `help` | REST provider `help` |
| EPUB checks | epubcheck and Ace by DAISY on every EPUB, where the site names their commands | `EpubCheckCommand`, `AceCommand`, `BookChecks`; `export --check` |
| History in git | the DITA export written into a git repository and committed on each run that changed something | cronjob part `exphelp`, `DitaHistoryDirectory`; `dita --commit` |
| Public pages | the full and line views of the help classes on the public site | extension `exphelpsite` (per public siteaccess) |

Nothing here changes a site that has not installed help as content: the state operation returns at once for objects
that are not help, the topic page and the feedback view are not reachable, the cronjob part says it is skipped.

## Behaviour changes

- **The author of a help object can no longer release it**, in the Object states tab either. A user with the role
  Help release who also wrote the current version of a topic sees no Release button and gets the state not assigned
  (the other states of the same assignment are). A site with a single editor sets `exphelp.ini [ContentSettings]
  ReleaseByAuthor=enabled`. The command line seeds (`seed-topics --release`, `build-manuals --release`) are not limited.
- **The role Help editor gets `exphelp/context`** with the next roles step (`exp:help:content roles` or `install`): the
  "JSON" link on a topic's page works for help editors, and a help editor can make an API key with the scope `help`.
- **`exphelp/feedback` is a new policy function.** Readers with `exphelp/read` may answer already; Anonymous gets it only
  with `AnonymousFeedback=enabled` and the roles step.
- **The validator reports more.** `check` may now list WARNINGs and NOTICEs it did not list before (review dates more
  than 12 months away, or 6 for the areas privacy and account; long sentences in topics for visitors and members; a mix
  of "click" and "tap"). None of them is an ERROR, so `check` keeps passing, except `help-id.renamed` and
  `help-id.reused`, which only appear after help_ids were recorded.
- **The REST API has a provider `help`** (`rest.ini [ApiProvider] ProviderClass[help]`) and an API key scope `help`. A
  site that lists its own providers in an override keeps them; the scope is offered on the API keys page to users who
  hold `exphelp/context`.
- **The cronjob part `infrequent` runs `exphelp.php`.** It removes answers older than `FeedbackKeepDays` (730) and,
  only with `DitaHistoryDirectory` set, writes and commits the DITA export.

## New settings

`exphelp.ini [ContentSettings]`: `ReleaseByAuthor` (disabled), `SentenceWordsPublic` (20), `SentenceWordsTechnical` (25),
`ParagraphSentencesPublic` (4), `PlainLanguageAudiences[]` (Visitors, Members), `ReviewMonths` (12), `ShortReviewMonths`
(6), `ShortReviewAreas[]` (privacy, account), `Feedback` (enabled), `FeedbackKeepDays` (730), `AnonymousFeedback`
(disabled), `EpubCheckCommand`, `AceCommand` (empty), `BookChecks` (report), `DitaHistoryDirectory` (empty),
`DitaHistoryLogin` (admin), `DitaHistoryAuthor` (empty). `rest.ini`: `[ApiProvider] ProviderClass[help]`,
`[ApiKeySettings] Scopes[help]`, `ScopeNames[help]`, `RouteScopes[expHelpRestController_topic]`,
`RouteScopes[expHelpRestController_manual]`, `[expHelpRestController_CacheSettings] ApplicationCache=disabled`.
`cronjob.ini`: `exphelp.php` in `[CronjobPart-infrequent]`, `[CronjobPart-exphelp]`.

## Database

Two new tables, `exphelp_help_id` and `exphelp_feedback`, in the kernel schemas of MySQL, PostgreSQL and SQLite and in
`share/db_schema.dba`. An existing installation gets them from the database update
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, written with `IF NOT EXISTS`) or, on any engine (Oracle and
MongoDB included), from the command's new step `tables`. Without them "Was this helpful?" is off and no help_id is
recorded; everything else works.

## After the deploy

```
php bin/php/ezpgenerateautoloads.php -k        # or: ./console exp:velocity deploy --kernel
./console exp:help:content install -s <admin siteaccess> --dry-run
./console exp:help:content install -s <admin siteaccess>     # tables, the editor's exphelp/context
./console exp:help:content install -s <admin siteaccess>     # No change.
./console exp:help:content check -s <admin siteaccess> --fix # records the help_ids of the Released topics
```

A server that keeps PHP running (PHP-FPM, Exponential Velocity) must load the changed kernel file
`kernel/content/ezcontentoperationcollection.php`; clear the INI cache (`--clear-tag=ini`) for the new settings and the
REST provider. For public help pages, add `ActiveAccessExtensions[]=exphelpsite` to the public siteaccess only, and
clear the template override cache. The interface strings are in the 24 translation catalogues that have the help's
context (`kernel/exphelp`).
