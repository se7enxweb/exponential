---
title: Demo scripts
audience: sales-marketing, editor
category: business
order: 60
summary: Ready-to-run demonstrations of Exponential in 5, 15 and 45 minutes, on a demo installation you control: the setup checklist, the talk track, what to click and what can go wrong, for the administration, the layouts editor, the help and languages including right to left.
keywords: demo, demonstration, presales, script, talk track, walk-through, presentation, tour, layouts editor, help system, languages, right to left, RTL, audit trail, Exponential Intelligence, checklist, follow-up
type: help
---
# Demo scripts

This page is for sales and marketing people who show Exponential to prospects, and for editors who are asked to help
with a demonstration. It gives three scripts, of 5, 15 and 45 minutes, that walk through the administration, the
layouts editor, the help and languages including right to left. Each script has a setup checklist, a talk track built
only from documented facts, the exact clicks, and a table of what can go wrong and how to recover. Every script runs on
a **demo installation you control**: your own laptop, a test server of your team, or a copy made for the customer.
Never demonstrate on a customer's live site or on a shared installation whose settings belong to someone else.

## Contents

1. [Before any demo: the setup checklist](#before-any-demo-the-setup-checklist)
2. [The 5-minute demo](#the-5-minute-demo)
3. [The 15-minute demo](#the-15-minute-demo)
4. [The 45-minute demo](#the-45-minute-demo)
5. [What can go wrong, and how to recover](#what-can-go-wrong-and-how-to-recover)
6. [After the demo](#after-the-demo)
7. [Tailoring a demo to the customer](#tailoring-a-demo-to-the-customer)

## Before any demo: the setup checklist

### Build the demo installation

One command installs a complete site on SQLite, with no database server, in `eng-US`, with the siteaccesses `site` and
`admin` ([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run    # checks the configuration and installs nothing
./console exp:install              # installs the multisite package on SQLite
```

- The administrator is `admin` with a generated password, printed in the summary and written to
  `var/log/initial-admin-password`. Change it after the first sign-in and delete that file
  ([The default administrator password](../default-administrator-password.md)).
- `exp:install` refuses to run over an existing installation unless you add `--force`. Never point it at a database
  you want to keep: an installer creates a new database ([Upgrading, 11.1](../../install/11-upgrading.md)).
- Other ways to install, and what each writes, are in
  [chapter 4, Choosing an install method](../../install/04-choosing-an-install-method.md).

### The checklist, one day before

```text
[ ] The demo installation opens in the browser: site and administration
[ ] Administrator password changed; var/log/initial-admin-password deleted
[ ] A demo editor account exists, with the Editor role (the help is readable to Editor on a new installation)
[ ] The Layouts tab shows at least one layout (if not: Layouts > New layout, any type, give it a name)
[ ] A second content language is added (Setup > Languages), so items show a dashed badge to translate
[ ] One folder with a few items exists to edit, and nothing on screen contains real personal data
[ ] The Interface box in the right column lists German, Hebrew and Arabic
[ ] Optional, only if you will show it: Exponential Intelligence has a provider key on this installation
[ ] Optional, 45-minute demo: a terminal on the demo machine, for exp:velocity and exp:audit
[ ] The browser: zoom readable for the room, other tabs closed, notifications off, a private window
[ ] This page and the pages you will send afterwards are open in a second window
```

### The checklist, ten minutes before

```text
[ ] Signed in as the demo account you will use (the editor for the 5- and 15-minute demos)
[ ] Interface language set back to English; color mode set to light (or to what the room's projector shows best)
[ ] No drafts left from the last demo (Dashboard > My drafts)
[ ] The first screen of the script is open
```

> **Warning**
> Never show a provider key, a password, a server address or the content of `settings/override` on a shared screen.
> If a page asks for a password during the demo, type it with the screen share paused.

## The 5-minute demo

**Goal:** the prospect sees that editors work alone, safely, in a clear interface, and that help is built in.
**Audience:** a decision maker, an editor lead, a first meeting.

| Time | Click | Say |
|---|---|---|
| 0:00 | The dashboard | "This is the administration after sign-in: quick actions, the numbers of your content, publishing over the last 14 days, and your own drafts and pending items." ([A tour of the administration](../admin-tour.md#the-dashboard)) |
| 0:40 | Open a folder from the tree, press **Edit** | "Every edit starts a draft. The site keeps showing the published version until you publish." |
| 1:20 | Change the **Short name**, press **Preview**, press the phone width button | "Before publishing you see the page at phone, tablet and desktop width." |
| 2:00 | **Back to edit**, **Send for publishing** | "Publishing keeps the earlier version. You can always go back." |
| 2:30 | **Layouts** tab, **Layouts**, **Open** on a layout | "Pages are arranged here by editors: zones, and blocks inside them. Exponential Layouts brings the Netgen Layouts concept to Exponential." (`README.md`) |
| 3:10 | Click a block | "Its settings are on the right: what it shows, and how it looks." |
| 3:40 | **Discard**, confirm | "This was a draft. Discarding it leaves the published page exactly as it was." |
| 4:10 | Press the **Help** button in the header | "The help is built in. The Help button opens the page that belongs to the screen you are on." ([Help system](../../features/6.0/help-system.md)) |
| 4:40 | Close | "It is open source under the GPL, and you can host it yourself." ([Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses)) |

**Do not** in five minutes: install, configure, open settings, or show more than one layout.

## The 15-minute demo

**Goal:** editing, versions, layouts, languages and right to left, help, and one proof of security.
**Audience:** an editorial team, a project manager, a second meeting.

### Part 1: editing and versions (4 minutes)

Follow the [folder walk-through of the tour](../admin-tour.md#in-action-editing-a-folder):

1. Open a folder, choose the language in the list, press **Edit**.
2. Show the **field bar**: one button per field jumps to its card; a red `*` marks a required field.
3. Type in **Description**; point at the rich text toolbar and the **Editor** switch below the field.
4. Press **Store draft and exit**. Say: "The draft waits under My drafts; only you see it. The form also saves by itself
   every few minutes."
5. Open **Manage versions** on the item, press **Edit** on the draft, then **Send for publishing**.
6. In **Manage versions**, press **Compare** between the two versions.

Talk track: "If your site uses an approval, the version waits under My pending items until someone approves it."
([Workflows](../../guides/workflows.md))

### Part 2: the layouts editor (4 minutes)

Follow the [layouts walk-through of the tour](../admin-tour.md#in-action-the-layouts-editor):

1. **Layouts** tab, **Layouts**, **Open** a layout. Point at the zones and their names.
2. Press **Add a block**, drag a **Title** block into a zone, type a title in its **Content** tab.
3. Drag the block by its header bar to another zone.
4. Point at a linked zone (red dashed border, hatched blocks): "Header and footer live in one shared layout; other
   layouts link to them instead of copying them."
5. Open the menu with three dots, show **Save draft and close**; then press **Discard** and confirm.

Talk track: "Which pages use a layout is decided by its mappings: a location, a subtree, a content type or a
siteaccess." ([Which pages use a layout](../layouts-editor.md#which-pages-use-a-layout))

### Part 3: languages and right to left (3 minutes)

1. Open an item. Point at the language badges: a filled one exists, a dashed one does not yet.
2. Click the dashed badge, choose **Translate based on** the existing translation, translate the name, publish. Say:
   "Each translation is published on its own." ([Languages](../languages.md#translate-an-item))
3. In the right column, **Interface**, choose German and press **Apply**. Say: "The editor's own administration
   language; the content does not change."
4. Choose Hebrew or Arabic and press **Apply**. Say: "In a right-to-left language the administration turns around:
   the side columns change places and text starts at the right." ([Right to left](../../features/6.0/right-to-left.md))
5. Switch back to English.

Talk track: "355 locale files can be used as content languages; administration translations exist (the current totals are at the top of the languages page)." ([Languages in Exponential](../../languages.md))

### Part 4: help and proof (4 minutes)

1. Press **Help**. Press `/`, type `publish`, open a result.
2. Press **Download as PDF**. Say: "Every help page prints cleanly or downloads as a PDF, drawn on the server, no
   outside service." ([Help system, Print and PDF](../../features/6.0/help-system.md#print-and-pdf))
3. If you are signed in as an administrator: open the **Audit** tab (`/audit/dashboard`), then **Console**. Say: "Your
   sign-in is already recorded. Every record carries the hash of the one before, so a change to the record shows."
   ([The audit trail](../../features/6.0/audit-trail.md))

## The 45-minute demo

**Goal:** a complete picture for a team that will decide: editors, an administrator, a developer and a project manager
in the room. Plan 35 minutes of demonstration and 10 of questions. Use the administrator account for parts 5 to 8.

| Part | Minutes | Content | Source to show or send |
|---|---|---|---|
| 1 | 3 | Who stands behind it, the license, the lineage | [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| 2 | 6 | The administration and the editing walk-through, all eight steps | [A tour of the administration](../admin-tour.md) |
| 3 | 4 | The content model: a content class and its attributes | [The content model and editing content](../../guides/content-model-and-editing.md) |
| 4 | 6 | The layouts editor, including mappings | [The layouts editor](../layouts-editor.md) |
| 5 | 4 | Languages, right to left, accessibility by keyboard | [Languages](../languages.md), [Keyboard shortcuts](../keyboard-shortcuts.md) |
| 6 | 4 | Exponential Intelligence (only with a provider key on the demo) | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| 7 | 4 | Security: audit trail, two-step sign-in, CSP, hardening | [Security hardening](../security-hardening.md) |
| 8 | 4 | Operations: logs, upgrade check, speed | [Log viewer](../../features/6.0/log-viewer.md), [Velocity](../../features/6.0/velocity-persistent-worker-server.md) |
| 9 | 10 | Questions | [Handling objections](objection-handling.md) |

### Part 1: who and what (3 minutes)

Say, and show the sources on screen:

- "Exponential is a professional PHP application framework with advanced CMS functionality; its most notable feature
  is its fully customizable and extendable content model." (`README.md`)
- "It is free software under the GNU GPL version 2 or later, and its documentation is free under the GNU FDL."
- "Exponential continues the code base of eZ Publish, a product its creator discontinued after almost 18 years. 7x
  has led it since 2023, and the Exponential Foundation, a nonprofit, operates the project."
  ([Who is 7x?](../who-is-7x.md#in-short))

### Part 2: the administration (6 minutes)

Walk the [header](../admin-tour.md#the-header) from left to right (logo, tabs, search with `/`, the globe button to
open the site, the sun button for color mode, the user menu), then all eight steps of
[editing a folder](../admin-tour.md#in-action-editing-a-folder), ending with **Discard draft** on a second draft.

### Part 3: the content model (4 minutes)

Open **Setup > Classes**, open the class *Folder* and point at its attributes; return to the folder's edit form and
show that each attribute is a card. Say: "Your content types are defined here, not in code. An article, a product, an
event: each gets exactly the fields it needs." Do not change a class during a demo.

### Part 4: layouts, end to end (6 minutes)

1. The **Layouts Dashboard**: the numbers of layouts, zones, blocks and mappings.
2. **Open** a layout; add, configure, move and duplicate a block; show the block groups (**Basic**, **Components**,
   **Listing**, **Gallery**, **Containers**, **Placeholders**).
3. The zone linking mode: **Link zone with shared layout zone**, then **Back**.
4. **Discard**.
5. **Rules**: point at one mapping's **Targets** and **Conditions**. Say: "The first enabled mapping by priority whose
   targets and conditions match a page wins."

### Part 5: languages, right to left and the keyboard (4 minutes)

1. Translate an item (15-minute script, part 3), then switch the interface to Arabic and back.
2. Reload a page and press `Tab` once: **Skip to main content** appears.
3. Press `/`: the focus jumps to search. Press `Esc`.
4. Focus the content tree; use the arrow keys to open and close a folder; press `Enter` to open an item.

Say: "The admin4 design follows WCAG rules that tests check: color contrast in light and dark mode and targets of at
least 24 by 24 pixels." ([Who is the Exponential Foundation?, Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility))

### Part 6: Exponential Intelligence (4 minutes, optional)

Only if the demo installation has a provider key and you have tried the prompt beforehand.

1. Open a folder, choose the **Intelligence** tab, ask for one short article below it.
2. Show the step list and the draft it wrote; open the draft; **Discard** it.
3. Say: "It writes drafts only, as the signed-in editor and with that editor's permissions. It never publishes on its
   own. Each provider has a region, US, EU or local, and every request records where it went."

Without a key: show the [Exponential Intelligence](../../features/6.0/extensions/expai.md) page instead and say that it
sends nothing anywhere until a provider key is added (`README.md`).

### Part 7: security (4 minutes)

1. **Audit** tab, **Console**: show the newest `access.session.login`.
2. `/user2fa/setup` on the demo account: show the choice of authenticator app or e-mail code; do not finish the setup
   on a shared screen ([Two-step sign-in](../../guides/two-factor-login.md)).
3. Open [Content Security Policy](../../features/6.0/content-security-policy.md) in the help: "By default it is
   report-only on the administration and off on the sites; the page explains how to enforce it."
4. Open the [Checklist of Security hardening](../security-hardening.md#checklist).

### Part 8: operations and speed (4 minutes)

1. **Setup > Log files**: the navigator, a search, the masking of personal data ("Privacy first").
2. **Setup > Upgrade check**: "Before an upgrade, this compares every file with the release."
3. In the terminal on the demo machine:

   ```bash
   ./console exp:velocity status
   ./console exp:audit verify --allow-root-user
   ```

4. Speed: say only what is documented. "The README documents 0.36 ms per cached page and 8,941 requests per second on a
   12-core machine, reproducible with shipped scripts. We can run `exp:benchmark` on your own hardware."
   ([Benchmarking](../../guides/benchmarking.md))

> **Note**
> A benchmark on a laptop during a meeting proves little: "One run on a busy machine proves little"
> ([Benchmarking](../../guides/benchmarking.md)). Offer a measurement on the customer's hardware as a follow-up.

## What can go wrong, and how to recover

| What happens | Why | What to do on the spot |
|---|---|---|
| No **Layouts** tab | The role may not work with layouts | Switch to the administrator account, or skip to the next part |
| The layouts editor asks whether to clear caches after a publish | The site does not clear its caches by itself | Choose **Clear caches** ([The layouts editor](../layouts-editor.md)) |
| *Possible edit conflict* | A draft from an earlier demo exists | Open **Manage versions**, discard the old draft; clean drafts before each demo |
| No **Interface** box in the right column | You are in the edit form, or the role lacks the permission | Leave the edit form; use the administrator account |
| A language shows English texts | It has no complete administration translation | Say so: it shows English until a translation exists; the complete ones are listed at the top of the languages page ([Support levels](../../languages.md#2-support-levels)) |
| No **Intelligence** tab | Not activated for this siteaccess, no policy `expai/write`, or no key | Show the documentation page instead |
| No **Help** button | The role lacks `exphelp/read` | Grant it ([Help system, Who may read it](../../features/6.0/help-system.md#who-may-read-it)) or use the administrator |
| A page says *Something went wrong on our side* with a reference | A real error | Note the reference; say it is logged and findable ([When something goes wrong](../when-something-goes-wrong.md)); move on |
| A change does not show on a Velocity server | Workers keep the application in memory | `./console exp:velocity restart` after the demo, not during it (`README.md`) |
| The projector washes out dark mode | Contrast of the room, not of the page | Press the sun button to switch to light |
| A question you cannot answer | It is not in the documentation | "The documentation does not state that; I will check and send you the source." Write it down. |

## After the demo

1. Discard every draft you made (**Dashboard > My drafts**) and remove test translations, so the next demo starts clean.
2. Set the interface language back to English.
3. Within one day, send a follow-up that links the pages behind what you showed:

```text
Subject: Exponential: the pages behind today's demonstration

Thank you for your time today. Every point we showed is documented here:

- Editing, drafts and versions: <link to "A tour of the administration">
- The layouts editor: <link to "The layouts editor">
- Languages and right to left: <link to "Languages in Exponential">
- Security and audit: <link to "Security hardening"> and <link to "The audit trail">
- Licensing: <link to "Licensing explained for buyers">

Open questions we will answer with a source: <list>
Proposed next step: <a workshop / a test installation for your team / a measurement on your hardware>
```

## Tailoring a demo to the customer

| The customer cares most about | Put first | Spend less time on |
|---|---|---|
| Editorial independence | Layouts, editing, versions | Operations |
| Many countries and languages | Multisite (the globe button), translations, right to left | Layouts details |
| Compliance and security | Audit trail, two-step sign-in, CSP, hardening checklist | Layouts details |
| Accessibility | Keyboard, landmarks, the contrast and target-size statements | Speed |
| IT and hosting | Databases, operating systems, Velocity, upgrade check | Editing details |
| Content production at scale | Exponential Intelligence, content jobs, packages | Right to left |

Use the [feature map](feature-highlights-for-customers.md#the-map-need-feature-where-to-show-it-proof) to find the place
to show any other need.

**On a public demo.** The **Demo mode** banner at the top of the administration is expected: it tells viewers the administrator password is published and cannot be changed. Mention it if asked; skip **Change password** in the script, since it is refused there. [Demo mode FAQ](../demo-mode-faq.md).

## Related pages

- [Feature highlights mapped to customer needs](feature-highlights-for-customers.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [A tour of the administration](../admin-tour.md)
- [The layouts editor](../layouts-editor.md)
- [Working together](../leadership/working-together.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
