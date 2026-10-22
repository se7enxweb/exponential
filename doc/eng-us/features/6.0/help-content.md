# Help maintained as content

Status: phase 1 in 6.0.15 (18 October 2026); the DITA export, the stored e-books and the overdue reviews on 20 October
2026 ([upgrade note](../../bc/6.0/help-content-dita-ebooks-reviews.md)). The plan is
[help-system.md](../../specifications/6.0/help-system.md), the editorial rules are the
[help style guide](../../specifications/6.0/help-style-guide.md). For the people who write and release the help:
[keeping the help up to date](../../help/maintaining-help-topics.md).

Help topics, manuals, figures and the glossary are **content of the site**: written, reviewed and released in the
administration like any other content, and delivered into the help of the administration (`exphelp`, see
[help-system.md](help-system.md)) next to the pages of `doc/`, and as JSON for context help in other clients.

## What phase 1 brings

| Part | Where |
|---|---|
| The eight classes `help_manual`, `help_section` (chapter), `help_concept`, `help_task`, `help_reference`, `help_troubleshooting`, `help_screenshot`, `help_glossary_entry` in the class group "Help" | `./console exp:help:content classes` |
| The sections `help_public` and `help_internal`, the object state group `help_status` (Draft, Review, Released), the folder tree (`help-root`, `help-pool` ..., found by remote_id) | `./console exp:help:content tree` |
| The roles "Help editor" and "Help release" and the one additional policy of Anonymous | `./console exp:help:content roles` |
| The validator of the style guide, `--fix` sets the sections from the audience | `./console exp:help:content check` |
| The initial glossary and topics (the templates of the style guide, one manual) | `./console exp:help:content seed-glossary`, `seed-topics` |
| The manuals made from the Markdown help: one per folder of `doc/eng-us/help/` and per reference area, rebuilt on each release, with the German translation | `./console exp:help:content build-manuals` ([below](#manuals-from-the-markdown-help)) |
| What is installed, uninstall | `./console exp:help:content report`, `uninstall` |
| An e-book (EPUB, PDF) of a manual | `./console exp:help:content export`, **Download EPUB/PDF** on the manual's page |
| The DITA 1.3 export (bookmaps, DITAVAL, topics, glossary), checked after writing | `./console exp:help:content dita`, **Download DITA** on the manual's page and the list of manuals |
| The e-books stored as file objects under E-books, versioned, rebuilt on release | `./console exp:help:content ebooks`, and each release of a topic |
| The topics overdue for review (review date passed, too long in Review) | the help's dashboard, `./console exp:help:content review` |
| The eight classes as the content class package `exp_help_classes` (.ezpkg), and its check | `./console exp:help:content package`, `verify-package` ([the package](../../specifications/6.0/help-content-package.md)) |
| A topic in the help: `exphelp/topic/<help_id>`; a manual: `exphelp/manual/<remote_id>`; the list of manuals: `exphelp/manual` | admin, policy `exphelp/read` |
| Context help as JSON: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>` | policy `exphelp/context` |

The plan names the scripts `create_classes.php`, `create_tree.php`, `create_roles.php`, `check_help.php`,
`seed_glossary.php` and `seed_topics.php`. They are the actions `classes`, `tree`, `roles`, `check`, `seed-glossary`
and `seed-topics` of one command, `./console exp:help:content` (`bin/php/exphelpcontent.php`), and behave as the
plan says:

- `-s <siteaccess>` is **required**; the command prints the siteaccess and the database before it starts;
- `--dry-run` shows what would change and changes nothing;
- every step is idempotent: a second run prints `No change.` (the acceptance test of the plan);
- `check` exits 1 while an ERROR is in the database.

```
./console exp:help:content install -s admin --dry-run      # the plan
./console exp:help:content install -s admin                # classes, tree, roles
./console exp:help:content install -s admin                # No change.
./console exp:help:content seed-glossary -s admin
./console exp:help:content seed-topics -s admin --release  # the style guide's templates, Released
./console exp:help:content check -s admin --fix
./console exp:help:content report -s admin
```

`--login=<user>` names who creates the classes and objects (default `admin`). `seed-*` take `--file=<json>` (the
format of `share/help/seed-topics.json` and `share/help/seed-glossary.json`) and leave existing objects as the
editors left them unless `--update` is given.

### On a new installation

The kickstarter and `./console exp:install` queue the build of the manuals at the end of an installation
(`./console exp:help:content build-manuals -s admin --queue`, from `expKickstarter::installHelpManuals()`) and do not
wait for it: the installation takes a few seconds longer, no more. The content job `helpmanuals` installs the help
as content (classes, tree, roles) and then builds the manuals in the background over the next 20 to 30 minutes,
while the site is already in use ([the background job](#the-background-job)). `exphelp.ini [ManualSettings]
BuildOnInstall=disabled` leaves it out. A queue that fails is reported and the installation stands: the site works
without the manuals, and the command can be run at any time. The setup wizard in the browser does not queue it:
after a wizard installation, run `./console exp:help:content build-manuals -s admin --queue` once. `seed-glossary`
and `seed-topics` are run by hand when the starting content is wanted.

The same commands that extend an existing site create everything on a new one, and find what is already there by
identifier and remote_id, so a site built from a package that already carries the classes is only completed. The
data the installer needs are the files of the kernel: the content model in `expHelpContentSchema`, the seeds in
`share/help/`, the list of manuals in `exphelp.ini [ManualSettings]`.

## Manuals from the Markdown help

The pages of the help in `doc/eng-us/` (and their translations in `doc/ger-de/`) are the real help: thousands of
pages. `build-manuals` makes manuals of them, so they are also content of the site: readable as manuals in the help,
as e-books, as DITA and as context help, with sections, states and permissions like every other topic.

**The Markdown stays the source.** The manuals are rebuilt from it on each release: a run makes the manuals,
chapters and topics match the pages again. An edit made only in the administration is overwritten by the next run;
a correction belongs in the Markdown page. Objects the command did not make are never touched: everything it makes
has a remote_id that starts with `help-gen-`, and only those objects are changed or removed. The style guide's seed
topics, the glossary and anything editors wrote stay as they are.

```
./console exp:help:content build-manuals -s admin --dry-run    # what would change
./console exp:help:content build-manuals -s admin --release    # make, update, remove; set Released
./console exp:help:content build-manuals -s admin --release    # No change.
./console exp:help:content build-manuals -s admin --manual=press --release   # one manual
./console exp:help:content build-manuals -s admin --language=none            # without the translations
./console exp:help:content build-manuals -s admin --queue                    # the same in the background (content job)
./console exp:help:content report -s admin                     # "Manuals are being built (n of 52)" while it runs
./console exp:help:content ebooks -s admin                     # the e-books of every Released manual
./console exp:help:content ebooks -s admin --language=ger-DE   # and their German edition
```

### What becomes what

| Markdown help | Content |
|---|---|
| a line of `exphelp.ini [ManualSettings] Manuals[]`: a folder below `doc/eng-us/` | a manual (`help_manual`, remote_id `help-gen-manual-<key>`) below the folder Manuals |
| a subfolder (and a subfolder of it) | a chapter (`help_section`, `help-gen-chapter-<hash of the folder>`) below its manual or chapter |
| a Markdown page | a topic (`help-gen-topic-<hash of the page>`) below its chapter or manual |
| the folder's `README.md` | the first topic of its manual or chapter; its title is the chapter's title |
| `<folder>/*.md` in `Manuals[]` | a manual of the pages directly in the folder, without chapters ("Getting started": `help/*.md`) |
| the page's place in the help | the order: front matter `order`, then the title (as the help's navigation shows it) |
| `doc/ger-de/` (paired through `doc/ger-de/.paths.json`) | the ger-DE translation of the same objects |

- **Topic type.** Front matter `topic_type: concept|task|reference|troubleshooting` decides. Without it: reference for
  the reference pages of the help (type `reference`, `catalogue`, `specification`, `generated`), a task for a page
  whose title starts with "How to" and that has exactly one numbered list (the text before it is the context, the
  list the steps, the text after it the result), else a concept. A troubleshooting page needs the headings
  Condition (Symptom, Problem) and Remedy (Solution, Fix); a page that does not have the parts of its type is made a
  concept and reported (`notice`).
- **Text.** The page without its front matter and its title, as Markdown in the topic's text fields. A link to a
  page that is a topic becomes `help:<help_id>`, so it opens the topic; a link to any other page or image of the help
  becomes `/doc/eng-us/<path>`, which the help resolves when it shows the topic (the help viewer, the help's
  images). Links with a scheme, anchors and code are left alone.
- **Identifiers.** The help_id is `doc.<path of the page>`: `doc.help.editors.publishing` for
  `help/editors/publishing.md` (area `doc`; a path longer than 80 characters, or two pages that would get the same
  one, end in a hash). The remote_ids depend only on the path, so a run finds every object again; a page that moves
  is a new topic, and the old one is removed.
- **Title and short description.** The page's title; the short description is the first sentence of its summary
  (front matter `summary`, else its first paragraph), at most 250 characters. A manual's title is its line's title,
  else the title of the folder's `README.md`, else of its start page without ": start here" (`Adopters: start here`
  gives `Adopters`). A translated title of a manual whose title comes from its line: `TranslatedTitles[]`.
- **Audience, section, state.** The help's audiences of the page (front matter `audience`, else `[AudienceSettings]
  PathRules[]` ...), each as the first audience option that `[ContentSettings] AudienceMap[]` maps to it. The section
  is `help_public` only when one of them is a public audience (`PublicAudiences[]`, Visitors: the pages for `user`),
  else `help_internal`; a chapter of a public manual is public. New objects are Draft; `--release` sets every
  generated object to Released (the first build of a release does that, so the manuals are delivered).
- **Translations.** Each language of `[ManualSettings] Languages[]` (ger-DE) that the installation has as a content
  language: a page with a translation (not the English page shown instead) is the translation of its topic; a chapter
  or manual is translated when it contains a translated topic. A language the installation does not have is
  reported and never created; add it under Setup, Languages, then run again. A topic whose translation no longer
  exists loses the translation.
- **Audience options.** The audiences of the help are appended to `[ContentSettings] Audiences[]` (Developers,
  Partners, Press ...) with their `AudienceMap[]`, and `build-manuals` appends them to the classes first, as the
  `classes` step does (only ever appended, so the stored audiences keep their meaning).

### Updates, removals, "No change."

What is stored is read with three queries and compared with what the pages give before anything is written: an
object whose values are the same costs no write. Then the command:

- creates what is missing (a new topic with its translation in **one** published version),
- updates what differs (a new version, in the language that differs),
- moves an object whose manual or chapter changed, makes an object of another type again,
- sets the chapters and topics of each manual and chapter (the relation list), the section and, with `--release`,
  the state,
- removes the generated objects whose page or folder is gone, and with `--manual=<key>` only those below that manual.

A run that finds nothing to do prints `No change.`. Removing a line from `Manuals[]` removes its manual with its
chapters and topics at the next run without `--manual`.

### How long it takes

The first build makes every object: on the copy of alpha's SQLite database, 52 manuals, 342 chapters and 5,315
topics (4,614 of them with a German translation) took 28 minutes on a busy shared server (load around 23, the
command at the lowest priority); a second run, which only compares, 34 seconds, most of it reading the pages. Most
of a first build is the publishing of each object (its version, node, names and URL aliases). A release that changes a few pages updates a few objects. The command is
written for that:

- one published version per new object, with its translations (a translation published on its own would double the
  first build);
- the writes in transactions of `[ManualSettings] BatchSize` objects (50);
- published without notification (no mail to the subscribers of the help tree);
- the search index is updated by the `indexcontent` cronjob afterwards (`DelayedIndexing` for this run only: the help
  searches its topics itself), and the view cache is not cleared per object (help topics are shown by `exphelp`);
  the help's own cache is cleared once at the end;
- every 250 objects of a run that changes something, a line says how far it is.

### The background job

`build-manuals --queue` (and a new installation) does the same work as the content job `helpmanuals`
(`expContentJobHelpManuals`, `content.ini [ContentJobSettings] JobTypes[helpmanuals]`), with the engine of the
content jobs: a detached worker (`bin/php/expcontentjob.php run <id>`), its log and progress on the job page
(`content/job/<id>`), and the cronjob part `contentjobs` (in `frequent`), which resumes a job whose worker died.

- **Never in the way of the site.** The worker runs at the lowest CPU priority; it makes `[ManualSettings]
  JobBatchSize` objects (10) per batch, each batch a transaction of its own, and waits `JobPause` milliseconds (200)
  after each, so the site's own writes never wait long.
- **Safe to run twice.** One `helpmanuals` job is active at a time: a second `--queue` returns the running one. The
  job holds the subtree lock of the help root, so no content job removes or moves it meanwhile. Every batch is
  idempotent: a resumed job finds what was made and goes on.
- **First the help as content.** The job runs the `install` step first (classes, tree, roles; `No change.` when they
  exist), so a new installation needs nothing else.
- **How far it is.** While it runs, the help's manual pages and `./console exp:help:content report -s admin` say
  "Manuals are being built (n of 52)": n counts the manuals whose objects are made.
- Started by root, the worker becomes the owner of `var/` before it starts (as every content job does); when the
  installation belongs to root, it runs as root (content jobs started by root now pass `--allow-root-user` to their
  worker instead of failing to start).

### On each release

The Markdown changes with every release, so the manuals are rebuilt with it. After the release is deployed:

```
./console exp:help:content install -s admin                    # new audience options, roles (usually No change.)
./console exp:help:content build-manuals -s admin --release
./console exp:help:content ebooks -s admin
./console exp:help:content ebooks -s admin --language=ger-DE
```

`--release` releases what the build made or changed: the pages were reviewed in the repository before the release.
Without it, new and changed topics wait in Draft for a release manager.

### Settings: exphelp.ini [ManualSettings]

| Setting | Default | |
|---|---|---|
| `Manuals[]` | 52 manuals: the 45 folders of `doc/eng-us/help/`, Getting started (`help/*.md`), install, guides, features, bc (Contributor notes), standards, specifications | `<key>;<folder>[;<title>]`, in the order of the manuals; the scope is an edit of this list |
| `Languages[]` | ger-DE | the translations written, when the installation has the language |
| `TranslatedTitles[]` | Getting started, Contributor notes in German | `<key>;<locale>;<title>` |
| `License` | GNU Free Documentation License 1.3 | the license of each manual |
| `BuildOnInstall` | `enabled` | the kickstarter and `exp:install` queue the background build (`build-manuals --queue`) after an installation |
| `JobBatchSize` | `10` | objects per batch of the background job, each batch a transaction of its own |
| `JobPause` | `200` | milliseconds the background job waits after each batch |
| `BatchSize` | `50` | objects per database transaction |

`doc/eng-us/history/` and `doc/eng-us/examples/` (generated) are not in the list.

Classes: `expHelpManualBuilder` (the plan: pages to manuals, chapters and topics, no database),
`expHelpManualSource` (the help's index of one language as the source), `expHelpManualSync` (the database made to
match the plan). Tests: `tests/tests/kernel/classes/help/expHelpManualBuilderTest.php`.

## The content model

As the plan says, with these decisions where it leaves a choice:

- **Text fields are `eztext` holding Markdown** (`body`, `context`, `prereq`, `steps`, `result`, `postreq`,
  `condition`, `cause`, `remedy`, the glossary's `definition`, `avoid`, `note`). The help renders them with the
  help's safe Markdown renderer; the JSON endpoint delivers them as they are ("Markdown body", plan, delivery path 2);
  the validator sees the one numbered list of a task and the **bold** UI labels of the style guide. Raw HTML is
  escaped, as in every help page.
- A link to another topic is written `[text](help:<help_id>)` (with `#anchor` if needed); the help turns it into the
  topic's address.
- `audience` and `platform` are `ezselection` (multiple). Their options come from `exphelp.ini [ContentSettings]
  Audiences[]`, `Platforms[]` (default: Visitors, Members, Editors, Administrators; Web, Admin, Mobile). The
  command **only appends** missing options, never reorders: `ezselection` stores the position of an option.
- The plan's "licence" field of the manual is the attribute `license`. The figure's "section of the topic it belongs to" is the
  attribute `section` (the field of the topic, such as `steps`), not the Exponential section of the figure.
- The help root goes below the **Media** root by default (`exphelp.ini [ContentSettings] ParentNode=media`;
  `content`, `users` or a remote_id are possible): not under the site's content tree, so it shows up in no menu,
  listing or feed of the site. There is no AI answer source (RAG) list in the kernel; an extension that adds one must
  leave out the `help_*` classes and `help-root` unless the site wants them.

### Sections and states

- A topic is **public** (section `help_public`) when its audience contains a public audience (`[ContentSettings]
  PublicAudiences[]`, default Visitors); otherwise, and without audience, it is **internal** (`help_internal`).
  Figures are public as soon as one public topic refers to them; chapters as soon as a public manual lists them
  (directly or through chapters); the glossary is always internal. The folders are internal.
- The section is set by `check --fix` (and after every `seed-*`), on the **object only**, never on a subtree.
  Moving an object gives it the section of its new parent: run `check --fix` afterwards.
- `help_status` is Draft (the default of every new object), Review, Released. Only Released is delivered. The
  first state of the new group is given to every object of the installation once (one INSERT; only help policies
  evaluate the group). Every state has a description (MySQL in strict mode has no default for it).

### Roles

| Role | Policies |
|---|---|
| Help editor | `content/read`, `versionread`, `create` in the subtree `help-root`; `content/edit` and `remove` there in the states Draft and Review; `state/assign` from Draft to Review; `exphelp/read` |
| Help release | `state/assign` in `help-root`, every state to every state; assigned **in addition** to the editor role |
| Anonymous | **one** `content/read` policy limited to section `help_public` **and** state Released |

The roles are created, not assigned: assign them to the editors' user groups in the administration. A policy is
compared by module, function and limitation values; a missing one is added, nothing is changed or removed.

**Opt-in:** `exphelp.ini [ContentSettings] AnonymousContextHelp=enabled` (default `disabled`) makes the roles step
also give Anonymous `exphelp/context`, so the JSON of **public, Released** topics can be read without login (a public
site, a mobile app). Without it the endpoint needs a login (session or the site's token login) and the policy.

## The validator

`./console exp:help:content check -s <siteaccess> [--fix] [--json]` checks every topic, figure, chapter, manual and
glossary entry below `help-root` (`expHelpContentValidator`, no database in the rules themselves):

| Level | Code | Rule (style guide section) |
|---|---|---|
| ERROR | `field.required` | the required fields of the topic type (1); a manual's title and short description |
| ERROR | `help-id.invalid`, `help-id.duplicate` | `area.topic[.variant]`, a-z 0-9, inner hyphens, 2+ parts, 80 characters; unique (7) |
| WARNING | `help-id.area`, `help-id.type` | the area is in `[ContentSettings] Areas[]`; the topic part names the subject, not the type (7) |
| ERROR | `shortdesc.sentences`, `shortdesc.too-long` | one sentence, at most 250 characters (8) |
| WARNING | `shortdesc.full-stop`, `shortdesc.markup`, `shortdesc.this-topic`, `shortdesc.repeats-title` | (8) |
| ERROR | `task.steps-list` | `steps` is exactly one numbered list (4) |
| WARNING | `task.sub-steps`, `task.too-many-steps`, `task.steps-elsewhere` | no sub-steps, at most 9, no steps in context/prereq/result (4) |
| WARNING | `notice.signal-word` | no DANGER, WARNING, CAUTION (5) |
| WARNING | `notice.position` | an **Attention:** or **Data protection:** notice of a task stands in its step, not in `context`, `prereq`, `result` or `postreq` (5) |
| WARNING | `notice.before-action`, `notice.per-step` | in a step, such a notice comes first, before the action; one notice per step (5) |
| WARNING | `notice.too-long` | a notice outside the steps has at most three sentences (5) |
| ERROR | `client.help-id-unknown` | a help_id in `[ContentSettings] ContextHelpIds[]` or `ClientHelpIds[]` that no topic has (plan, "Checks") |
| WARNING | `client.help-id-not-released` | such a help_id names a topic that is not Released yet: clients get a 404 until it is |
| WARNING | `glossary.avoid` | a word the glossary avoids; bold text (a quoted UI label) and code are not checked (Terminology) |
| ERROR/WARNING | `figure.alt-missing`, `figure.alt-too-long` | alt text required; more than 150 characters is an ERROR for a generated figure (with `figure_id`), else a WARNING (6) |
| WARNING | `figure.alt-image-of`, `figure.alt-caption`, `figure.id-format` | (6) |
| ERROR | `figure.image-missing`, `related.target`, `figures.target`, `map.target`, `map.cycle` | relations name what they should |
| WARNING | `map.empty`, `map.duplicate`, `map.orphan-chapter` | |
| WARNING | `review-date.past`, `version.format` | (8) |
| NOTICE | `field.recommended` | `platform`, `version_from`, `review_date`, `owner` are empty (1, 8) |
| ERROR | `section.mismatch` | the section does not match the audience; `--fix` sets it |

A notice is a bold label with a colon: `**Attention:**`, `**Data protection:**` or `**Note:**` (also `**Attention**:`),
outside code. In the help viewer, a block quote that starts with one (`> **Attention:** ...`) or a container
(`:::data-protection`) is a callout with that title; Data protection has its own icon.

Not machine-checkable and left to the review: minimalism, plain language, one action per step, the length of a
notice inside its step (the notice and the action share the step's text), whether a label matches the interface.

## In the help (exphelp)

- `exphelp/topic/<help_id>`: the topic in the layout of the help viewer, its type, audiences, platform, validity,
  the rendered fields (headings Before you begin, Steps, Result ... for tasks; Condition, Cause, Remedy for
  troubleshooting), its figures and related topics, the list of topics and manuals. "Edit this topic" for those who
  may edit it; "JSON" opens the context help answer.
- `exphelp/manual/<remote_id>`: the manual's table of contents, chapters nested; `exphelp/manual`: the manuals.
- Who sees what: Exponential permissions alone. A **Released** topic is shown to who may read it (`content/read`,
  with its section and state limitations). A Draft or Review topic is shown only to who may **edit** it (help
  editors), with a **Preview** badge. Anything else is a 404, the same as an unknown help_id. Relations (related
  topics, figures, the entries of a manual) are shown only if the reader may see their target.
- Search, the dashboard and the context map of the help: the provider class `expHelpContentProvider` has the hooks
  (`search()`, `topics()`, `manuals()`, and as a context provider `helpContext()` with
  `[ContentSettings] ContextHelpIds[<module>/<view>]=<help_id>`, listed in `[ContextHelp] Providers[]`). They are wired
  into the help's search, dashboard and settings with the second wave of the help system.

## Context help by help_id (JSON)

```
GET /exphelp/context/content.publish-article
GET /exphelp/context/manual/help-manual-editors
```

A topic: `help_id`, `type`, `title`, `shortdesc`, `audience`, `platform`, `version_from`, `version_to`, `state`,
`preview`, `language`, `modified`, `markdown` (the whole topic, with English headings), `fields` (each field's
Markdown), `related` (help_id, title, shortdesc, url), `figures` (url, alt, caption, figure_id, field), `url`.
A manual: its metadata and `toc` (nested chapters and topics with their JSON `url`).

`ETag` on every answer, `If-None-Match` answers 304; `Cache-Control: private, no-cache`, `Vary: Cookie,
Authorization`. Unknown, internal without access, or not Released: the same `404 {"error":"not_found"}`.

`expHelpContextRegistry` asks its providers first: a provider that also implements `expHelpContextPageInterface`
may answer with `content/<help_id>`, and the "?" Help control of an admin page then opens that topic. A map entry
can never name `content/...`, and a provider that does not implement the interface cannot either.

## Settings: exphelp.ini [ContentSettings]

| Setting | Default | |
|---|---|---|
| `Enabled` | `enabled` | `disabled`: the topic, manual and context views are 404 |
| `ParentNode` | `media` | where `help-root` is created: `media`, `content`, `users` or a remote_id |
| `Audiences[]` | Visitors, Members, Editors, Administrators, then the other audiences of the help (Developers, Partners, Press, media and influencers ...) | the audience options (only ever appended) |
| `PublicAudiences[]` | Visitors | an audience that makes a topic public |
| `Platforms[]` | Web, Admin, Mobile | the platform options |
| `Areas[]` | account, content, media, search, users, setup, privacy, help, doc | the areas of help_ids (`doc`: the topics of `build-manuals`) |
| `AudienceMap[<name>]` | visitors, members: user; editors: editor; administrators: admin; developers: developer ... one per audience of the help | the help's audience of an audience option |
| `ContextHelpIds[<key>]` | none | `module/view` (or `module`, `design:module/view`) = help_id |
| `AnonymousContextHelp` | `disabled` | `enabled`: the roles step gives Anonymous `exphelp/context` |
| `EbooksOnRelease` | `enabled` | a release builds the stored e-books of the manuals again; `disabled`: only `exp:help:content ebooks` does |
| `ReviewMaxDays` | `14` | days a topic may stay in Review without a change before it is overdue |
| `ReviewListLimit` | `10` | at most this many overdue topics on the help's dashboard |
| `ClientHelpIds[]` | none (a commented example in the shipped file; set it in an override) | the help_ids other clients open (a mobile app, a site's JavaScript); `check` reports one no topic has, or whose topic is not Released |

## E-books

```
./console exp:help:content export -s admin --manual=help-manual-editors --format=epub [--output=file] [--dry-run]
./console exp:help:content export -s admin --manual=help-manual-editors --format=pdf
```

and on the manual's page in the help, **Download EPUB** and **Download PDF** (`exphelp/manual/<remote_id>/(format)/epub`).
An e-book carries only the **Released** chapters and topics the user may read (on the command line: `--login`,
default `admin`); a preview never goes into a book. The default file of the command is
`var/<site>/cache/exphelp/ebooks/<remote_id>.<format>`; the download is made per request and not kept.

- **EPUB 3** (`expHelpContentExport::writeEpub()`, PHP's zip extension): a title page (title, subtitle, short
  description, edition, authors, license), one page per chapter and per topic in the order of the manual, a
  navigation document with the nested contents, the figures from the storage with their alt text and caption,
  `xml:lang` from the language of the manual, and the accessibility metadata of EPUB Accessibility 1.1
  (`schema:accessMode`, `accessModeSufficient`, `accessibilityFeature`, `accessibilityHazard`,
  `accessibilitySummary`, `dcterms:conformsTo`). Links between topics of the book stay inside the book; other topic
  links go to the topic in the help. epubcheck and Ace by DAISY are not shipped: a site that publishes the books
  runs them on the file.
- **PDF** (`writePdf()`) uses the help's PDF writer (`expHelpPdfWriter`, the print and PDF download of help pages):
  one document with the manual's title block, chapters and topics as headings, internal links and the figures. The
  format is offered only where that writer is installed (`expHelpContentExport::formats()`).

### Stored e-books

The plan stores the built books as content (delivery path 3): each format of each Released manual is one object of
the class **file** below the folder E-books (remote_id `help-ebooks`), found by its remote_id
`help-ebook-<manual remote_id>-<format>` (`help-ebook-help-manual-editors-epub`).

```
./console exp:help:content ebooks -s admin [--manual=help-manual-editors] [--language=ger-DE] [--dry-run] [--json]
```

- **Languages.** Without `--language` the books are the edition of the default language. `--language=ger-DE` builds
  the German edition: the topics, chapters and manual in their German translation, in English where a topic has
  none, stored as `help-ebook-<manual remote_id>-ger-de-<format>` and named "<title> (EPUB, ger-DE)". The manual's
  page lists the editions of the default language and of `[ManualSettings] Languages[]`. `export` takes `--language`
  too. A manual that is not itself translated into the language has no edition in it: the book is skipped ("the
  manual has no translation in ger-DE"), and one stored by an earlier build goes to the trash. The rebuild on
  release builds the edition of the default language; the other editions are built by the
  command.
- **Rebuilt on release.** When an object of the help is set to Released (the state assignment of the
  administration, or any code that assigns states through `eZContentOperationCollection::updateObjectState()`), the
  books of every Released manual that contains it (the manual itself, its chapters, their topics, the figures of
  those topics) are built again (`expHelpContentEbooks::onStateAssign()`). `exphelp.ini [ContentSettings]
  EbooksOnRelease=disabled` leaves that to the command. Setting Draft or Review changes no book. A book that cannot
  be made is written to the debug log; the release stands.
- **Versions.** A new build is published as a new version of the same object, so the version history of the object
  keeps the earlier editions (`content.ini [VersionManagement]` decides how many). A build whose file is the same as
  the stored one changes nothing: the files are dated by their content, not by the clock, so a second run of the
  command prints `No change.`.
- **State.** Released, like everything the help delivers.
- **Section and permissions.** `help_public` for a manual whose audience is public, else `help_internal`, set on the
  object only. So the policies of the plan decide who may download a book: Anonymous reads `help_public` and
  Released, help editors the subtree `help-root`. The book of a public manual carries only public objects: Released
  topics in `help_public` with a public audience, chapters and figures in `help_public`; an internal manual's book
  carries every Released object it lists. The person whose release starts a build does not change what goes in.
- **Linked from the manual.** The manual's page in the help lists its stored books that the reader may read: the
  file (a `content/download` link), its version (a link to the object), size and date.

The download buttons above still make a book on request with the reader's own rights; the stored books are the
editions for everyone.

## DITA export

The help is written in Exponential, and leaves it as **DITA 1.3**: the information model of the plan (decision 1),
for DITA-OT and other DITA tools, and for the change history in git (plan, delivery paths 3 and 4).

```
./console exp:help:content dita -s admin [--manual=help-manual-editors] [--output=<dir>] [--dry-run] [--json]
./console exp:help:content export -s admin --format=dita ...     # the same, under the plan's name
```

and on the help's pages of manuals: **Download DITA** on a manual's page (`exphelp/manual/<remote_id>/(format)/dita`)
and **Download all as DITA** on the list of manuals (`exphelp/manual/(format)/dita`), a zip file. As in an e-book, only
**Released** objects the user may read go into an export (on the command line: `--login`, default `admin`).

Without `--manual` the export holds every Released manual, every Released topic of the pool and the Released glossary
entries; with it, one manual and the topics it lists. The default directory is `var/<site>/cache/exphelp/dita/all`
(or `/<remote_id>`). Only changed files are written, and files of an earlier export that are no longer in it are
removed (only in `topics/`, `chapters/`, `glossary/`, `images/` and the `.ditamap` and `.ditaval` files of the
directory itself). So a git repository can be the output directory: its `.git` and anything else stay, and a commit
after the export records what changed. A second run prints `No change.`.

| Help as content | DITA 1.3 |
|---|---|
| `help_concept`, `help_task`, `help_reference`, `help_troubleshooting` | `concept`, `task`, `reference`, `troubleshooting`, one file per topic: `topics/<help_id>.dita` |
| `help_id` | the topic's `id`, its file name, and `prolog/resourceid/@appid` (the DITA way to name a topic for context help) |
| `title`, `shortdesc` | `title`, `shortdesc` |
| `audience`, `platform`, `version_from` | the filter attributes `audience`, `platform` (lower case, blanks as hyphens) and `rev`; the prolog repeats the audiences (`audience/@othertype`) and has `version_from`, `version_to`, `review_date` as `othermeta` and the dates in `critdates` |
| `body` of a concept | `conbody`; a heading opens a `section` with its `title` |
| `body` of a reference | `refbody`, every block in a `section` |
| `context`, `prereq`, `steps`, `result`, `postreq` | `prereq`, `context`, `steps` (each item of the one numbered list a `step` with `cmd` and `info`), `result`, `postreq`. Steps that are not one numbered list become `steps-informal`, and the topic then uses the DITA 1.3 General Task DOCTYPE |
| `condition`, `cause`, `remedy` | `condition`, `troubleSolution` with `cause` and `remedy` (`steps` or `steps-informal`) |
| figures | `fig` with `title` (caption) and `image` with `alt`, in the element of the field the figure names (`section`), else at the end; the image file in `images/` |
| `related` | `related-links` |
| Markdown | `p`, `ul`, `ol`, `codeblock`, `lq`, `simpletable`, `note` (from a callout, with its type); **bold** (a quoted UI label in the style guide) is `uicontrol`, *italic* `i`, code `codeph`; a `help:` link is an `xref` to the topic in the export, or to its page in the help when it is not in the export |
| `help_manual` | a `bookmap`, `<remote_id>.ditamap`: `booktitle` (title, subtitle), `bookmeta` (short description, authors, dates, audiences, license, edition), a table of contents, a `chapter` per entry |
| `help_section` | a `chapter` (`chapters/<remote_id>.dita`, a concept with the chapter's title and short description) with its topics as `topicref`s |
| the manual's audiences | `<remote_id>.ditaval`: the manual's audiences `include`, the other audience options `exclude` (the edition for its audiences) |
| `help_glossary_entry` | `glossentry` (`glossary/<term>.dita`): `glossterm`, `glossdef`, the note, and every word to avoid as `glossAlt/glossSynonym` with `glossStatus value="prohibited"` |
| the whole export | `help-pool.ditamap`: every topic by type, and the glossary |

Every file has the DITA 1.3 DOCTYPE of its root element (`-//OASIS//DTD DITA 1.3 Concept//EN` ...) and `xml:lang`
from its object's language. After writing, the command checks the directory (`expHelpContentDita::validate()`):
every file well-formed, the right DOCTYPE, topic ids unique, and every `href` of a map, topic or image that is not
external resolves to a file of the export; it exits 1 and prints `FAIL` when one does not. The download is checked
the same way before it is sent. The DTDs are not shipped and nothing is fetched: for a full DTD validation run
DITA-OT, or `xmllint --valid` with the OASIS catalog, on the directory (the export of the test set and of the
shipped seed topics pass it).

## Overdue reviews

The plan's maintenance phase (7) asks for "a list of outdated topics (review date passed) in the admin dashboard".
The help's start page (`exphelp/dashboard`) shows **Help topics overdue for review** to help editors and release
managers, and the command lists them all:

```
./console exp:help:content review -s admin [--json]
```

A topic is overdue when

- its **review date has passed** (the validator's `review-date.past`, style guide section 8), in any state. The
  release manager sets a Released topic back to Review, the editor checks it and sets a new review date, and it is
  released again (plan, "Permissions");
- or it has been **in Review longer than** `exphelp.ini [ContentSettings] ReviewMaxDays` days (default 14) without a
  change. The kernel keeps no date of a state change, so the topic's last publication counts: a topic an editor is
  still changing in Review is not overdue.

The list shows each topic with its state, why it is overdue and for how long, the longest overdue first, and a link
to the topic in the help and to its object in the administration (**Edit**, or **Set state** for who may only assign
its state). It is shown only to who can act on a topic: who may edit it, or assign it another state of
`help_status`. A help editor (Draft and Review) sees the topics in Review; a release manager also the Released ones
whose review date has passed. Who can act on none sees nothing, and nothing is shown while no topic is overdue. At
most `ReviewListLimit` topics (default 10) are listed, the rest are counted. The card uses the shared admin
components (`a4-card`, `a4-table`, `a4-tag`, `a4-btn`) and so follows the light and dark mode of the
administration; it has no CSS of its own.

## The help classes as a package

`./console exp:help:content package -s <admin siteaccess>` writes the eight classes of the installation as the content
class package `exp_help_classes` (a package directory and `exp_help_classes-<version>.ezpkg`), and
`verify-package --file=<.ezpkg>` compares a package with the classes of the release, without a database. The package
holds the classes only; `exp:help:content install` still makes the sections, states, tree and roles. Build, check and
handoff: [the help classes as a package](../../specifications/6.0/help-content-package.md).

## Compared with the specifications

Checked on 19 October 2026 against the [plan](../../specifications/6.0/help-system.md), the
[style guide](../../specifications/6.0/help-style-guide.md) and the [standards](../../specifications/6.0/help-standards.md)
(pull request #288), item by item in the code, not from this page. The DITA export, the e-books as file objects,
the review list and their translations, in progress on 19 October, followed on 20 October.

| Specification | State |
|---|---|
| DITA topic types: one type per topic, concept, task, reference, troubleshooting | done: `expHelpContentSchema::classes()`, the fields of each type as the plan lists them |
| Maps: manual (bookmap) and chapter with ordered `topicrefs`, topic pool | done: `help_manual`, `help_section`, the tree below `help-pool`; validator `map.*` |
| Figures and glossary as classes | done: `help_screenshot` (alt text required), `help_glossary_entry` (synonyms to avoid) |
| The style guide's templates (concept, task, reference, troubleshooting, the second task) | done: all five are in `share/help/seed-topics.json` (`content.versions`, `content.publish-article`, `content.edit-buttons`, `content.not-visible`, `account.log-in`) and pass the validator without a finding (test `testStyleGuideTemplatesPass`) |
| Sections `help_public`/`help_internal` set from the audience, on the object only | done: `check --fix`, after every `seed-*` |
| Object states Draft, Review, Released; only Released delivered | done: `help_status`, the provider shows Draft and Review only to editors, as a preview |
| Roles Help editor, Help release; one Anonymous policy with both limitations | done: `exp:help:content roles` |
| CLI scripts with `-s`, `--dry-run`, idempotent ("No change.") | done: the actions of `exp:help:content` |
| Validator rules of the plan (steps, alt text, `shortdesc`, `help_id`, glossary, review date, section) | done |
| Notice levels (style guide section 5: Attention, Data protection, Note; in the step, first, one per step, at most three sentences) | **added now**: `notice.position`, `notice.before-action`, `notice.per-step`, `notice.too-long`; Attention and Data protection callouts with their own title in the viewer. The length of a notice inside a step stays a review rule |
| `help_id` schema and uniqueness | done: `help-id.*` |
| Context help JSON by `help_id`, ETag, public topics without login | done: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>`; anonymous access is opt-in (`AnonymousContextHelp`) |
| "Clients keep a list of the help_ids they use; a test checks each exists" | **added now**: `client.help-id-unknown`, `client.help-id-not-released` over `ContextHelpIds[]` and the new `ClientHelpIds[]` |
| E-book EPUB and PDF of a manual, accessibility metadata | done: `exp:help:content export`, **Download EPUB/PDF** |
| DITA bookmap + DITAVAL export, regular DITA export into a git repository (delivery paths 3 and 4) | done: `exp:help:content dita`, **Download DITA** ([DITA export](#dita-export)); valid against the OASIS DITA 1.3 DTDs. The regular run into git is a cron job of the site on top of the export (only changed files are written, so its directory can be the git repository) |
| E-book stored as a file object under "E-books" (`help-ebooks`) | done: `exp:help:content ebooks`, rebuilt on release, versioned, section from the audience ([stored e-books](#stored-e-books)) |
| List of topics whose review date has passed, in the dashboard (phase 7) | done: the help's dashboard and `exp:help:content review`, also topics too long in Review ([overdue reviews](#overdue-reviews)) |
| epubcheck and Ace by DAISY on every e-book | left out: Java and Node tools, not shipped with a PHP application. A site that publishes books runs them. The EPUB carries `dcterms:conformsTo` EPUB Accessibility 1.1 / WCAG 2.2 AA although Exponential does not run these checks; see [the standards](../../specifications/6.0/help-standards.md#epub-accessibility-11) |
| Web templates `full/help_*.tpl`, `line/help_*.tpl` for the public site (delivery path 1) | left out for now: the kernel has no public site design of its own to put them in; the help is delivered in the administration (`exphelp/topic`, `exphelp/manual`) and as JSON, which a site's design can render. A site package that wants public help pages adds the templates in its design |
| "Was this helpful?" on each topic (phase 7) | left out for now: it needs a place to store answers and a privacy decision (who answered, kept how long) per site |
| Help never a source of AI answers (RAG) unless wanted | done by default: the kernel has no RAG source list, and the help root is below Media, not the content tree. An extension that adds one must leave `help-root` out |
| The help classes as a package | **added now**: `exp:help:content package`, `verify-package` (CJW #16564) |

## Later

"Was this helpful?", public site templates, editor conveniences.

## Classes

`expHelpContentSchema` (the model, data only), `expHelpContentInstaller` (classes, tree, roles, report, uninstall,
section fixes), `expHelpContentRepository` (reads the help objects into arrays), `expHelpContentValidator` (the
rules, no database), `expHelpContentSeeder`, `expHelpContentProvider` (what the current user may see, rendering,
JSON, search, context), `expHelpContextPageInterface`, `expHelpContentPackage` (the class package),
`expHelpContentExport` (EPUB, PDF), `expHelpContentDita` (the DITA export, its check, writing and zip),
`expHelpDitaMarkup` (the help's HTML as DITA elements), `expHelpContentEbooks` (the stored e-books, rebuilt on
release), `expHelpContentEditionProvider` (what goes into a stored edition) and `expHelpContentReview` (overdue
reviews). Views: `Exponential\View\Kernel\Exphelp\Topic`, `Manual`, `Context`. Tests (no database):
`tests/tests/kernel/classes/help/expHelpContentTest.php`, `expHelpContentPackageTest.php`, `expHelpContentExportTest.php`,
`expHelpContentDitaTest.php`, `expHelpContentEbooksTest.php`, `expHelpContentReviewTest.php`,
`expHelpContentTranslationsTest.php`.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
