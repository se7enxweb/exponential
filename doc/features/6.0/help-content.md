# Help maintained as content

Status: phase 1 in 6.0.15 (18 October 2026). The plan is [help-system.md](../../specifications/6.0/help-system.md),
the editorial rules are the [help style guide](../../specifications/6.0/help-style-guide.md).

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
| What is installed, uninstall | `./console exp:help:content report`, `uninstall` |
| An e-book (EPUB, PDF) of a manual | `./console exp:help:content export`, **Download EPUB/PDF** on the manual's page |
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

Nothing is created by the installer itself. After an installation, run `./console exp:help:content install -s
<admin siteaccess>` once (and `seed-glossary`, `seed-topics` if the starting content is wanted): the same command
that extends an existing site creates everything on a new one, and finds what is already there by identifier and
remote_id, so a site built from a package that already carries the classes is only completed. The data the
installer needs are the files of the kernel: the content model in `expHelpContentSchema`, the seeds in `share/help/`.

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
| WARNING | `glossary.avoid` | a word the glossary avoids; bold text (a quoted UI label) and code are not checked (Terminology) |
| ERROR/WARNING | `figure.alt-missing`, `figure.alt-too-long` | alt text required; more than 150 characters is an ERROR for a generated figure (with `figure_id`), else a WARNING (6) |
| WARNING | `figure.alt-image-of`, `figure.alt-caption`, `figure.id-format` | (6) |
| ERROR | `figure.image-missing`, `related.target`, `figures.target`, `map.target`, `map.cycle` | relations name what they should |
| WARNING | `map.empty`, `map.duplicate`, `map.orphan-chapter` | |
| WARNING | `review-date.past`, `version.format` | (8) |
| NOTICE | `field.recommended` | `platform`, `version_from`, `review_date`, `owner` are empty (1, 8) |
| ERROR | `section.mismatch` | the section does not match the audience; `--fix` sets it |

Not machine-checkable and left to the review: minimalism, plain language, one action per step, the position of a
notice in its step, whether a label matches the interface.

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
| `Audiences[]` | Visitors, Members, Editors, Administrators | the audience options (only ever appended) |
| `PublicAudiences[]` | Visitors | an audience that makes a topic public |
| `Platforms[]` | Web, Admin, Mobile | the platform options |
| `Areas[]` | account, content, media, search, users, setup, privacy, help | the areas of help_ids |
| `AudienceMap[<name>]` | visitors, members: user; editors: editor; administrators: admin | the help's audience of an audience option |
| `ContextHelpIds[<key>]` | none | `module/view` (or `module`, `design:module/view`) = help_id |
| `AnonymousContextHelp` | `disabled` | `enabled`: the roles step gives Anonymous `exphelp/context` |

## E-books

```
./console exp:help:content export -s admin --manual=help-manual-editors --format=epub [--output=file] [--dry-run]
./console exp:help:content export -s admin --manual=help-manual-editors --format=pdf
```

and on the manual's page in the help, **Download EPUB** and **Download PDF** (`exphelp/manual/<remote_id>?format=epub`).
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
- Not yet: the DITA bookmap export and the e-book stored as a file object under "E-books" (`help-ebooks`), the
  regular DITA export into a git repository (plan, delivery paths 3 and 4).

## Later

The dashboard's list of topics with a past review date, "Was this helpful?", editor conveniences.

## Classes

`expHelpContentSchema` (the model, data only), `expHelpContentInstaller` (classes, tree, roles, report, uninstall,
section fixes), `expHelpContentRepository` (reads the help objects into arrays), `expHelpContentValidator` (the
rules, no database), `expHelpContentSeeder`, `expHelpContentProvider` (what the current user may see, rendering,
JSON, search, context), `expHelpContextPageInterface`. Views: `Exponential\View\Kernel\Exphelp\Topic`, `Manual`,
`Context`. Tests: `tests/tests/kernel/classes/help/expHelpContentTest.php` (no database).
