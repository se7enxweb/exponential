# Plan: user manuals and help maintained inside Exponential

Status: proposal, 18 October 2026; phase 1 (classes, tree, sections, states, roles, validator, web and context help
in the help of the administration) implemented in 6.0.15, see [help maintained as content](../../features/6.0/help-content.md);
from 20 October 2026 also the DITA export (delivery path 3, and the export for path 4), the e-books stored as file
objects and rebuilt on release (delivery path 3) and the dashboard list of topics overdue for review (phase 7), see
[the upgrade note](../../bc/6.0/help-content-dita-ebooks-reviews.md).
Companion page: [the help style guide](help-style-guide.md).

## Idea

The help of a site is **content of that site**. It is written, reviewed and
released in the Exponential admin interface like any other content, and
delivered from there to three places: the public site, a JSON endpoint for
context help in other clients, and e-books (EPUB/PDF). There is no separate
documentation tool and no second copy of the text.

The plan describes the content model, access rules, workflow, delivery paths
and checks. It is independent of any particular site: the audiences, product
names and areas in the examples are placeholders that each installation fills
in.

## Decisions

| # | Question | Decision |
|---|---|---|
| 1 | Documentation standard | **DITA 1.3** (OASIS) as the information model; content rules after IEC/IEEE 82079-1:2019 and ISO/IEC/IEEE 26514; e-books after EPUB Accessibility 1.1 / WCAG 2.2 AA |
| 2 | Reuse | **Topic pool + maps**: every topic exists once; manuals refer to topics through an ordered relation list |
| 3 | Languages | The model is translatable from the start (Exponential translations per object, `xml:lang` in the export). Which languages are written is a decision per installation |
| 4 | Access | What a reader needs **before** having an account is public. Everything else requires login and a matching role |

## Audiences

The audiences are a configurable list. A typical installation:

| Audience | Who | Access | Channel |
|---|---|---|---|
| Visitors | anyone on the public site | public | web |
| Members | registered users of the site | logged in | web, e-book, context help |
| Editors | content editors in the admin interface | logged in (admin siteaccess) | admin, e-book |
| Administrators | site administrators | logged in (admin siteaccess) | admin, e-book |

Operations and IT documentation (installation, deployment, server setup)
**stays in the repository** next to the code, not in the CMS: it has to be
readable when the CMS is not running.

## The standard: what DITA means here

- Every topic has **exactly one** type: `concept`, `task`, `reference`,
  `troubleshooting`. There are no mixed forms; an "FAQ" is a view, not a type.
- A manual is a **bookmap**; chapters are `topichead`/`chapter` with
  `topicref`s.
- Audience, platform and version are DITA filter attributes (`audience`,
  `platform`, `rev`/`props`); a DITAVAL file per manual produces the edition
  for one audience.
- Every topic has a **`shortdesc`** (one sentence). It is link text, search
  hit and preview at the same time.
- 82079-1 requires safety and data protection notices **where the action
  happens** (in the step, not in an appendix), prerequisites before the steps
  and a result after them.

## Content classes (prefix `help_`)

Not the legacy `documentation_page` and not `article`: the help needs fields
those classes do not have, and it must not be mixed into the site's editorial
content. Two hard constraints:

- **Help is never a source for search-based AI answers (RAG)** unless that is
  wanted explicitly. Otherwise the help becomes a citable source for
  questions it was never written to answer.
- **Help does not live under the site's regular content tree**, so it does
  not show up in menus, listings and feeds of the site.

### Structure (maps)

| Class | Container | Fields |
|---|---|---|
| `help_manual` (manual) | yes | title, subtitle, audience (multiple), edition/version, date, short description, cover (`help_screenshot`), licence, authors, `topicrefs` (ordered `ezobjectrelationlist` of `help_section` and topics) |
| `help_section` (chapter) | yes | title, short description, `topicrefs` |

### Topics (in the pool)

Fields shared by all four types:

| Field | Datatype | Purpose |
|---|---|---|
| `title` | ezstring | title |
| `shortdesc` | ezstring (max. 250) | one sentence, required |
| `help_id` | ezstring, unique, `[a-z0-9.-]` | stable, language-neutral key: context help, DITA `id`, file name in the export |
| `audience` | ezselection (multiple) | the audiences of the installation |
| `platform` | ezselection (multiple) | e.g. web, admin, mobile |
| `version_from` / `version_to` | ezstring | validity (`to` empty = current) |
| `review_date` | ezdate | next review |
| `owner` | ezobjectrelation (user) | responsible person |
| `related` | ezobjectrelationlist | DITA `related-links` |
| `figures` | ezobjectrelationlist (`help_screenshot`) | figures, ordered |

Per type:

| Class | Fields |
|---|---|
| `help_concept` | `body` |
| `help_task` | `context`, `prereq`, `steps` (exactly **one** numbered list, checked by the validator), `result`, `postreq` |
| `help_reference` | `body` (tables allowed and expected) |
| `help_troubleshooting` | `condition`, `cause`, `remedy` (DITA 1.3) |

### Building blocks

| Class | Fields |
|---|---|
| `help_screenshot` | image, **alt text (required)**, caption, platform, version, language, section of the topic it belongs to, figure id |
| `help_glossary_entry` | term, definition, **synonyms to avoid**, audience, note |

Terminology drifts as soon as more than one person writes. The glossary
decides each pair of competing words; the validator reports the rejected
form.

**Selection options are only ever appended.** `ezselection` stores the
position of an option. Reordering the list silently gives every topic a
different audience, and through the validator a different section.

### Status

As an **object state group** `help_status` (Draft → Review → Released), not as
a field: object states can be used as role limitations. Only "Released" is
delivered (web, endpoint, e-book).

The state belongs to the **object**, not to the version. Whoever edits a
released topic and publishes it delivers the change immediately. That is why
the editor role may only edit topics in Draft or Review (see "Permissions").

### Access through sections

Two sections, `help_public` and `help_internal`. The role `Anonymous` gets
`content/read` **only** with a limitation on `help_public` **and** state
"Released" — one policy with both limitations, not two policies (two would
grant read on either condition). A topic is public when its `audience`
contains a public audience (visitors in the example above); otherwise it is
internal, and a topic without audience is internal as a safe fallback.

The section is set by the validator script from `audience`, **never by
hand**. Set by hand, it drifts as soon as someone changes the audience, and
an internal topic ends up on the open web.

The section is set **on the object only**, never on the subtree: a manual is a
container, and a subtree assignment would take back the decisions of the
validator on every run. Moving an object in the admin interface gives it the
section of its new parent; run the validator again afterwards.

The glossary is always internal, whatever its audience: it is an editorial
tool, and its notes are not meant for anonymous readers.

### Tree

```
Help                          (folder, remote_id help-root)
├── Topic pool                (folder, remote_id help-pool)
│   ├── Concepts / Tasks / Reference / Troubleshooting
│   ├── Figures               (help_screenshot)
│   └── Glossary              (help_glossary_entry)
├── Manuals                   (folder, remote_id help-manuals)
│   ├── Manual for members
│   ├── Manual for editors
│   └── Manual for administrators
└── E-books                   (folder, remote_id help-ebooks)
```

Everything is found by **`remote_id`**, never by a node ID in an INI file.
Node, section and state IDs differ between installations and are resolved at
runtime.

## Permissions

| Role | May |
|---|---|
| Help editor | `content/create`, `content/edit`, `content/read` in the subtree `help-root`; `edit` and `remove` limited to states Draft and Review; `state/assign` from Draft to Review |
| Help release | `state/assign` on all three states, assigned **in addition** to the editor role |
| Anonymous | `content/read` limited to section `help_public` **and** state Released |

The person who wrote a topic is not the person who releases it. A change to a
released topic, even a typo: someone with "Help release" sets it back to
Review, the editor changes it, then it is released again.

## Delivery paths

1. **Web (public)**: templates `full/help_*.tpl` and `line/help_*.tpl` in the
   site design. Relations (`topicrefs`, `related`, `figures`) are shown only if
   the reader can read the target; access is decided by Exponential
   permissions alone, the templates do not filter by audience.
2. **Context help**: a JSON endpoint `/<module>/help/<help_id>` and
   `/<module>/help/manual/<remote_id>`, Markdown body, ETag. Public topics
   without login; internal topics with a token **and** a matching role. A
   mobile app or a JavaScript frontend opens help at the right place by
   `help_id`.
3. **E-book**: CLI `export_manual.php -s <admin siteaccess> --manual=<remote_id>
   --format=dita|epub|pdf [--dry-run]`. Exponential → DITA bookmap + DITAVAL →
   DITA-OT 4.x → EPUB3/PDF → **epubcheck** + **Ace by DAISY** → stored as a
   file object under "E-books" (section according to audience).
4. **History**: the DITA export runs regularly into its own git repository.
   The content lives in the CMS, the change history still ends up in git.

## CLI scripts

All scripts:

- take `-s <siteaccess>` as a **required** option and print siteaccess and
  database before they start (one tree may serve several installations with
  different databases);
- follow `startup()` → `getOptions()` → `initialize()`, and call
  `initialize()` exactly once;
- support `--dry-run`;
- are idempotent: a second run reports "No change." — that is the acceptance
  test.

| Script | Does |
|---|---|
| `create_classes.php` | creates the eight `help_*` classes in a class group "Help"; existing classes are only extended, existing attributes are never changed |
| `create_tree.php` | sections, state group, folder tree |
| `create_roles.php` | the two help roles and the additional policy on `Anonymous` |
| `check_help.php` | the validator (below); `--fix` sets the section from the audience; exit 1 while an ERROR is in the database |
| `seed_glossary.php` | creates the initial glossary from a data file |
| `seed_topics.php` | creates initial topics and manuals from a data file; existing objects stay as the editors left them unless `--update` is given |

Two kernel facts the scripts have to handle:

- The first state of a new state group is assigned to **every** existing
  object of the installation. That is harmless as long as only help policies
  evaluate the group.
- `ezcobj_state_language.description` has no default value in MySQL. Without a
  description the INSERT fails under `STRICT_TRANS_TABLES`; the script has to
  set one.

## Checks

- Class script idempotent (second run: no change).
- Help never in a RAG/AI source list, never under the site's content tree.
- `Anonymous` reads only `help_public` + Released (verifiable with a role
  inspection and with an anonymous request for an internal topic, which must
  not return content).
- Validator (`check_help.php`):
  - `task` without exactly one numbered list;
  - screenshot without alt text, alt text longer than 150 characters;
  - topic without `shortdesc`, `shortdesc` longer than one sentence;
  - invalid or duplicate `help_id`;
  - word the glossary rejects (text in bold is a quoted UI label and is not
    checked);
  - review date in the past;
  - section that does not match the audience.
- Clients that call help by `help_id` keep a list of the IDs they use; a test
  checks that each of them exists in the export.

## Phases

1. Foundations: classes, tree, sections, states, roles.
2. Editorial style guide ([help-style-guide.md](help-style-guide.md)) and glossary.
3. Validator.
4. Web templates and context help endpoint.
5. E-book export with epubcheck and Ace.
6. Content: what visitors need first, then members, editors, administrators.
7. Maintenance: a list of outdated topics (review date passed) in the admin
   dashboard, "Was this helpful?" on each topic.

## Related pages

- [Help style guide](help-style-guide.md)
- [Help standards](help-standards.md): what the help takes from DITA 1.3, IEC/IEEE 82079-1, ISO/IEC/IEEE 26514,
  ISO 3864-2, EPUB Accessibility 1.1 and WCAG 2.2 AA
- [The help classes as a package](help-content-package.md)
- [Help maintained as content](../../features/6.0/help-content.md#compared-with-the-specifications): what of this
  plan is implemented, in progress or left out
- [Sections](../../guides/sections.md)
- [Object states](../../guides/object-states.md)
- [Roles and policies](../../guides/roles-and-policies.md)
