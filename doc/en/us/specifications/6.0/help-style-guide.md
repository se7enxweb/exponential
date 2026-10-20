# Editorial style guide: help topics in Exponential

Status: proposal, 18 October 2026. Basis: [the help system plan](help-system.md) (content model, access,
workflow). The machine-checkable rules are checked by `./console exp:help:content check` since 6.0.15
([help maintained as content](../../features/6.0/help-content.md#the-validator)).

This guide applies to every topic in the topic pool (`help-pool`). Part of it
is checked by the validator `check_help.php`; the rest is the job of the
review. Terms are defined in the glossary (see [Terminology](#terminology)).

The examples are taken from the Exponential admin interface, so they can be
checked against a running installation. A site writes its own topics in the
same form.

**Use the product name the reader sees on screen**, not an internal project
or code name.

## 1. Topic types

Every topic has **exactly one** type (DITA 1.3). There are no mixed forms: a
topic that explains *and* instructs is split into two topics that link to
each other through `related`. An "FAQ" is a view (a `help_section` with
references), not a type.

| Class | DITA | Question the topic answers | Title form | Required fields |
|---|---|---|---|---|
| `help_concept` | concept | What is this, what is it for? | Noun: "Sections" | `title`, `shortdesc`, `help_id`, `audience`, `body` |
| `help_task` | task | How do I do this? | Verb, imperative or gerund: "Publish an article" | `title`, `shortdesc`, `help_id`, `audience`, `steps` |
| `help_reference` | reference | Which values, options, limits exist? | Noun, often plural: "Object states" | `title`, `shortdesc`, `help_id`, `audience`, `body` |
| `help_troubleshooting` | troubleshooting | What is wrong, and how do I fix it? | The symptom in the reader's words: "A published article does not appear on the site" | `title`, `shortdesc`, `help_id`, `audience`, `condition`, `remedy` |

Shared optional fields: `platform`, `version_from`, `version_to`,
`review_date`, `owner`, `related`, `figures`. Editors **always** fill in
`platform`, `version_from`, `review_date` and `owner` (section 8); the
content model does not enforce them only so that a draft can be saved
without them.

**Which type when:**

| If the text … | then |
|---|---|
| describes a sequence of actions the reader carries out | `help_task` |
| explains, gives background, explains rules (permissions, why something is locked) | `help_concept` |
| consists mainly of a table or list to look things up | `help_reference` |
| starts from an error message or an unexpected state | `help_troubleshooting` |
| needs more than two sentences of explanation in the `context` of a task | move the explanation into a `help_concept`, keep one sentence in `context` and add `related` |

### 1.1 Template `help_concept`

| Field | Content |
|---|---|
| `title` | Drafts and versions |
| `shortdesc` | Every change to content is saved as a new version; readers only see the published one. |
| `help_id` | `content.versions` |
| `audience` | Editors |
| `platform` | Admin |
| `version_from` | 6.0.0 |
| `body` | see below |

```
When you edit an object, Exponential creates a draft. The draft is a new
version of the object. Readers of the site do not see it.

When you publish the draft, it becomes the current version. The previous
version stays in the history. You can compare versions and copy an old one
into a new draft.

Drafts you do not publish stay in "My drafts" until you discard them.
```

### 1.2 Template `help_task`

| Field | Content |
|---|---|
| `title` | Publish an article |
| `shortdesc` | Create an article in a folder and publish it on the site. |
| `help_id` | `content.publish-article` |
| `audience` | Editors |
| `platform` | Admin |
| `version_from` | 6.0.0 |
| `context` | An article appears on the site as soon as you publish it, if its section and state allow readers to see it. |
| `prereq` | You are logged in to the admin interface and may create content in the folder. |
| `steps` | see below |
| `result` | The article is published. It is listed in the folder and visible on the site. |
| `postreq` | Check the article on the site with **Preview** or by opening its page. |
| `related` | `content.versions`, `content.not-visible` |

```
1. Open the folder in which the article belongs.
2. Select **Article** in the list of content classes.
3. Click **Create here**.
   The edit form opens.
4. Fill in at least the title and the text.
5. Click **Send for publishing**.
   The folder opens again and shows the new article.
```

### 1.3 Template `help_reference`

| Field | Content |
|---|---|
| `title` | Buttons in the edit form |
| `shortdesc` | These buttons are at the bottom of the edit form, and this is what each of them does. |
| `help_id` | `content.edit-buttons` |
| `audience` | Editors |
| `body` | table (below) |

| Button | What happens | Visible on the site? |
|---|---|---|
| **Send for publishing** | the draft becomes the current version | yes, unless a workflow holds it back |
| **Store draft** | the draft is saved, the form stays open | no |
| **Discard draft** | the draft is deleted | no, the current version stays |
| **Preview** | shows the draft as the site would show it | no |

### 1.4 Template `help_troubleshooting`

| Field | Content |
|---|---|
| `title` | A published article does not appear on the site |
| `shortdesc` | If an article is published but missing on the site, its section, state or the cache is usually the reason. |
| `help_id` | `content.not-visible` |
| `audience` | Editors, Administrators |
| `condition` | The article is listed in the admin interface as published. On the site it is missing from the list, or its address shows "Access denied". |
| `cause` | Anonymous readers may not read the section or the object state of the article, or the site still shows a cached page. |
| `remedy` | Open the article in the admin interface and look at **Section** and **Object states**. Compare them with an article that is visible. If they are the same, ask an administrator to clear the cache. |

A troubleshooting topic starts from the **symptom** the reader sees. If there
is an error message, it appears verbatim in `condition` (in quotation marks,
not bold: it is text, not a label). That is what readers search for.

### 1.5 Second template `help_task`, for login

| Field | Content |
|---|---|
| `title` | Log in |
| `shortdesc` | Log in with your user name and your password. |
| `help_id` | `account.log-in` |
| `audience` | Members |
| `prereq` | You have an account. |
| `steps` | 1. Open the login page. 2. Enter your user name in **Username**. 3. Enter your password in **Password**. 4. Click **Login**. Your start page opens. |
| `result` | You are logged in. |
| `related` | `account.forgot-password` |

Variants (single sign-on, two-factor login) are **separate** topics, not
if-then branches in the steps. A step starting with "If …" is allowed when it
is the only branch in the topic.

Labels in this guide are quoted as the English admin interface shows them.
Before a topic is released, every label is checked against the installation
it describes (section 4).

## 2. Minimalism

- **Action first.** No "In this section you will learn …", no "Exponential
  offers the convenient option to …". The first sentence of a task is the
  first step or the prerequisite.
- **Only what readers need for their task.** Technology (server, cache
  internals, search index) does not appear in help for visitors and members.
- **One statement per sentence.** Reasons briefly, after the action.
- **Nothing self-evident:** no "Click **Save** to save".
- **Plan for mistakes.** What can go wrong gets its own troubleshooting topic
  and a `related` link, not a paragraph in the task.

## 3. Language

### 3.1 Form of address

Address the reader as **you**, in every audience. A topic for several
audiences uses the same form throughout. For languages with a formal and an
informal "you", each installation decides per audience and writes it down in
its glossary; the rule is to follow the interface the reader uses.

### 3.2 Plain language (visitors, members)

| Rule | Value |
|---|---|
| Sentence length | at most 20 words |
| Subordinate clauses | at most one per sentence |
| Voice and tense | active, present |
| Foreign words, abbreviations | explain on first use; none in titles |
| Negation | never double |
| Numbers | digits ("5 steps") |
| Paragraph | at most 4 sentences |
| References to laws | only in a data protection notice |

Readers with little reading practice (children, second-language readers)
need shorter limits; an installation with such an audience sets them here,
e.g. 15 words per sentence and 3 sentences per paragraph.

### 3.3 Technical language (editors, administrators)

- Sentences up to 25 words.
- The Exponential terms of the admin interface (object, node, content class,
  section, object state, version, siteaccess) are used without explanation.
- "Class" always means **content class** in help for editors. If the site has
  its own meaning of the word, the glossary decides.

## 4. Steps

| Rule | Right | Wrong |
|---|---|---|
| Imperative, the first sentence is the action | Click **Create here**. | Now you can click "Create here". |
| One action per step | 2. Enter your password. 3. Click **Login**. | 2. Enter your password and click **Login**. |
| The result after the step, as a separate sentence | Click **Send for publishing**. The folder opens again. | Click **Send for publishing**, whereupon the folder opens again. |
| Condition before the action | If the article has no image yet, click **Upload file**. | Click **Upload file** if the article has no image yet. |
| UI labels exact and bold, without quotation marks | **Send for publishing** | "Publish", *Send for publishing*, "Send for publishing" |
| One verb per platform | web and admin: "click"; touch devices: "tap" | mixing "click", "press" and "tap" in one topic |

Further rules:

- `steps` contains **exactly one** numbered list (checked by the validator),
  no sub-steps. More than 9 steps: split into two tasks.
- Steps do not appear in `context`, `prereq` or `result`.
- **Labels come from the interface's translation files**, not from memory and
  not from a screenshot of an old version. Placeholders are replaced by an
  example.
- If a label contains a word the glossary avoids, it is still quoted
  **exactly**; in running text the glossary term applies. The inconsistency
  goes to the developers as a ticket, not into the help text.
- If the way differs per platform, each platform gets its own topic with a
  `.variant` in the `help_id` (section 7) — only if the **steps** differ, not
  just the look.

## 5. Notices

IEC/IEEE 82079-1:2019 requires safety notices at the point of danger and a
graded marking; the signal words DANGER, WARNING and CAUTION (after
ISO 3864-2) refer to **hazards to persons**. Web software without actuators
causes no physical hazard; these three levels are therefore **not used**.
Whoever uses them anyway devalues them for the case in which they matter, and
makes deleting a folder sound as loud as an electric shock.

| Level | DITA `note/@type` | For | Example |
|---|---|---|---|
| **Attention** | `notice` | consequences that cannot be undone: data loss, links break, access is lost | Remove an object without the trash; delete a content class; remove a user |
| **Data protection** | `other`, `othertype="dataprotection"` | who sees which data after this action, what goes to third parties | Make a section readable for anonymous users; embed external media |
| **Note** | `note` | useful additional information without consequences | A draft is kept until you discard it |

Rules:

- **Where the action happens.** An attention or data protection notice
  stands in the step, **before** the action that triggers it — not in
  `context`, not at the end, not in a collected chapter "Data protection".
- Such a notice is the only exception to "the first sentence is the action":
  it stands in the step **before** the imperative.
- Structure: level — what happens — what the reader can do. At most three
  sentences.
- One notice per step. Whoever needs more has cut the step too large.
- The data protection notice does not repeat the privacy policy; it states
  only what **this** action does and links the legal texts.

The validator reports a notice in `context`, `prereq`, `result` or `postreq`
(`notice.position`), after the action in its step (`notice.before-action`),
a second notice in one step (`notice.per-step`) and a notice outside the steps
with more than three sentences (`notice.too-long`). Write the label in bold
with a colon: `**Attention:**`, `**Data protection:**`, `**Note:**`.

Example in a step:

```
4. **Attention:** Without "Move to trash" the objects are deleted at once and
   cannot be restored.
   Click **OK**.
```

## 6. Figures (`help_screenshot`)

**When a figure at all:** only if a control is hard to find (icon without a
label, menu behind a symbol) or if the target state has to be checked
visually (a diagram, a preview). Not one per step, not for decoration. A
topic must be complete **without** the figure — the figure adds, it never
carries information alone.

**Prefer generated figures.** Where the interface is covered by automated
browser or UI tests, let the tests draw the real screens with invented data
and store the images. When the interface changes, the tests run again and the
figure is replaced. A screenshot taken by hand goes out of date silently.

| Field | Rule |
|---|---|
| `platform` | the platform of the capture; another platform only as a separate figure if it looks different |
| `version` | the version used for the capture, `MAJOR.MINOR.PATCH` |
| `language` | the language of the interface in the figure |
| `caption` | short, what is shown: "Edit form of an article" |
| `alt_text` | see below; required |
| `section` | the field of the topic under which the figure stands (`steps`, `body`, `context` …); if empty, it stands under the last one |
| `figure_id` | `<help_id>/<n>` for generated figures; empty for a figure made by hand |

The section of a figure is set by `check_help.php --fix` from the topics that
refer to it: public as soon as one of them is public. It goes out only when
**released**, like the topic.

**Capture** (also for a figure made by hand): light theme, default font
size, the interface language of the topic, no notifications, no browser
extensions in view. Highlight at most with a frame around the element meant,
no arrows with text.

**No real personal data.** Capture only with test accounts: invented names,
e-mail addresses under `example.org`, no real customer, company or school
names, no real access codes or tokens. Whoever needs a screenshot from a
production system blacks out first — opaque, not blurred.

**Alt text:**

- describes **what readers need from the figure for their step**, not the
  image as such: "Edit form with the fields Title and Body, below it the
  button Send for publishing."
- not "Image of …", "Screenshot of …" — the screen reader says that itself;
- labels verbatim, without formatting;
- **at most 150 characters**, one sentence (the field holds 500; the reserve
  is for diagrams whose message would otherwise be lost). The validator
  reports more as an ERROR for a generated figure, otherwise as a WARNING;
- does not repeat the caption.

With every minor version the editors check the figures of the areas
affected; a figure that shows a different interface is replaced, not left
"roughly right".

## 7. `help_id`

The `help_id` is the stable key of a topic: context help, DITA `id`, file
name in the export.

**Schema:** `area.topic[.variant]`:

- only `a-z`, `0-9`, a hyphen inside a part, a dot between parts;
- at least two parts, at most 80 characters;
- always English and ASCII, whatever the language of the text: the ID is a
  key, not a title;
- `topic` names the subject, not the type: `account.log-in`, not
  `account.task-log-in`;
- `variant` only for platform or role variants: `account.log-in.sso`,
  `media.upload.mobile`.

**Areas** (example for the Exponential admin; a site adds its own):

| Area | Content |
|---|---|
| `account` | log in, log out, password, profile, two-factor login |
| `content` | create, edit, publish, versions, translations, move, trash |
| `media` | images, files, media library |
| `search` | site search, search in the admin interface |
| `users` | users, groups, roles and policies |
| `setup` | sections, object states, content classes, cache, siteaccesses |
| `privacy` | personal data, deleting data, legal texts |
| `help` | maintaining the help, glossary |

A new area is entered here before the first topic uses it.

**A `help_id` is never renamed and never reused.** Clients call it; a changed
ID is a dead link for every client version already in use. If the subject of
a topic changes so much that the ID no longer fits, a new topic with a new ID
is created; the old one gets `version_to` and a link to the new one. A typo
in a released ID stays.

## 8. Fields

### `shortdesc`

- **One** sentence, with a full stop, at most 250 characters; aim for under
  150.
- Says what readers can do or know afterwards. Task: purpose of the action;
  concept: the core statement; reference: what the table contains;
  troubleshooting: symptom and direction of the fix.
- Does not repeat the title, does not begin with "This topic …".
- No markup, no link, no bold labels — the `shortdesc` is link text, search
  hit and preview at the same time.

### Audience and visibility

| `audience` contains | Section | Who reads |
|---|---|---|
| a public audience (also together with others) | `help_public` | **everyone, without login** |
| only audiences that require login | `help_internal` | logged in, with a matching role |
| nothing | `help_internal` | safe fallback |

From this follows:

- **A topic for visitors *and* editors is public.** What only editors should
  know belongs in a separate internal topic.
- Readers with a wider role also read the topics of the narrower audiences;
  an editor topic therefore links to the member topic instead of repeating
  it.
- The section is set by `check_help.php --fix`, **not by the editors by
  hand**.

### States (`help_status`)

| State | Meaning | Who sets it |
|---|---|---|
| Draft | work in progress, delivered nowhere | help editor (default on creation) |
| Review | ready for review: technical (does it match the software?) and linguistic (this guide) | help editor |
| Released | delivered (web, context help, e-book) | only someone with the release role, not the author of the topic |

Any change to a released topic, even a typo: someone with the release role
sets it back to **Review**, the editor changes it, then it is released again.

### Version and review date

| Field | Rule |
|---|---|
| `version_from` | first version of the software for which the topic is correct, `MAJOR.MINOR.PATCH` |
| `version_to` | last version for which it is correct; **empty = current**. Set when a new version changes the procedure, and at the same time the successor topic is created |
| `review_date` | release date + 12 months; areas `privacy` and `account` + 6 months. The validator reports past review dates |
| `owner` | one person, not a shared account |

## Terminology

The glossary decides: `help_glossary_entry` in the folder "Glossary"
(`help-pool-glossary`). Each entry names the term, the synonyms to avoid and
the reason. Principle: **the word the interface shows** wins over the word
from specifications, tickets or the code. The validator reports the rejected
form.

Typical decisions for Exponential:

| Use | Avoid | Reason |
|---|---|---|
| object | content item, entry | the term of the admin interface |
| content class | class, type, template | "class" is ambiguous; "template" is a design file |
| publish | save, release | **Send for publishing** creates the published version; "release" is reserved for the help workflow |
| draft | unsaved version | the term of the admin interface ("My drafts") |
| trash | recycle bin, deleted items | the term of the admin interface |

A term missing from the glossary is added before it appears in more than one
topic.

## Related pages

- [Help system plan](help-system.md)
- [Help standards](help-standards.md)
- [Glossary](../../glossary.md)
