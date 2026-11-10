# Handoff: marketing automation and campaign management — seven planned changes

Redmine #16741 (https://pm.team.in-mv.com/issues/16741). Builds on the workflow designer, Redmine #16702
(https://pm.team.in-mv.com/issues/16702), whose build spec is `openspec/handoff/expworkflow-16702-spec.md`.

This page is the design source the seven changes below cite, and the guide for the developer who builds them. It
was written on 09.11.2026 against `se7enxweb/exponential` `main` at `d5263e7883`, and against the packages
`se7enxweb/cjw_newsletter` 4.2.x and `se7enxweb/expai` as they are installed by that commit's `composer.json`.

---

## 1. What this is about

Exponential already has most of what a site needs to send content to people: cjw_newsletter (lists, double
opt-in, recurring sends with the "new articles" condition, article pools, approval of editions, A/B subject tests,
open and click statistics), the queue `expqueue`, the AI extension `expai`, and — once #16702 is built — the
editorial workflows of `expworkflow` with their action registry and webhooks.

What is missing is the step from *publishing* to *campaigning*: doing the right thing automatically when content
is published, when somebody subscribes or fills in a form, and doing it safely. These seven changes close that gap.
None of them adds a campaign engine of its own.

## 2. The principles every change follows

1. **Three building blocks, kept apart.** A **list** answers *who* (cjw_newsletter lists). A **stage** answers
   *where something is in a process* (expworkflow stages). An **action** answers *what happens automatically*
   (the expworkflow action registry, run by expqueue). They meet only through events: "content entered the stage
   Published", "a subscription was confirmed", "a form was submitted". A campaign is a workflow definition with
   marketing actions, not a second system.
2. **Extension points, not a new engine.** New behavior is registered where the platform already has a
   registry: actions in `expworkflow.ini [Actions]`, job types in `expqueue.ini`, handlers in
   `cjw_newsletter.ini [ExtensionPointSettings]`. A package that is not installed simply does not register its
   actions; nothing fails.
3. **Nothing reaches the outside by accident.** Anything that sends mail, posts to a channel or calls a webhook
   is an *outward* action. Outward actions are switched off until an administrator switches them on, can be run
   in a test mode that delivers to the person testing only, and never fire during bulk operations (imports,
   content jobs, restores).
4. **Once is once.** Every outward delivery carries an idempotency key of what it is about: instance, action and
   recipient or destination for workflow actions, submission and destination for form destinations. A repeated event, a retried job or a double click is a no-op. A delivery to many
   recipients records each failure and continues; it fails as a whole only when every recipient failed.
5. **A person approves what the public sees.** A post to a public channel needs a transition fired by a person
   since the content was last written by the AI service or a system user. A job or timer transition after that
   approval carries it forward (approve now, go live at a date, post when live); an AI draft that reaches a
   posting stage without a person's transition never posts.
6. **AI output is marked until a person has looked at it.** Fields an AI filled carry a visible "AI — check"
   mark; a person's edit removes it, and a later AI run never overwrites what a person wrote.

## 3. The changes, in the order to build them

| # | Change | Carried by | Depends on | Lands in |
|---|---|---|---|---|
| 1 | `add-workflow-outward-action-safeguards` | `expworkflow` | `add-workflow-jobs-and-timers`, `add-workflow-admin-ui`, `add-workflow-integrations`, for hook deliveries `add-workflow-webhooks-and-importers` (#16702 milestones 3, 4, 5, 7) | this repository |
| 2 | `add-channel-posts` | new extension (proposed name `expchannels`) | 1 | this repository |
| 3 | `add-newsletter-welcome-series` | `cjw_newsletter` | none for the series itself; `add-workflow-jobs-and-timers` and 1 for its workflow action | package `se7enxweb/cjw_newsletter` |
| 4 | `add-form-destinations` | to be decided in its design (see 5.4) | 1, `add-workflow-webhooks-and-importers` for the webhook destination, 3 for the series destination | this repository or a package |
| 5 | `add-ai-field-provenance` | `expai` | none | package `se7enxweb/expai` |
| 6 | `add-newsletter-segments` | `cjw_newsletter` | none for sends; 1 and 3 to use segments in workflow actions and series | package `se7enxweb/cjw_newsletter` |
| 7 | `add-share-metadata` | to be decided in its design (see 5.7) | none; 2 takes its post text and image from it | this repository |

Changes 3 (without its workflow action), 5, 6 (for sends) and 7 do not depend on #16702 and can be built in
parallel with it.
Changes 1, 2 and 4 need #16702 up to the milestones named in the table.

## 4. What to do with the changes

Each change is a directory under `openspec/changes/<name>/`. Today each has a **proposal** and its **specs**; the
remaining artifacts are yours to write. The schema `plan-driven` and `openspec/config.yaml` define the order and
the rules (see `doc/eng-us/standards/planned-changes-with-openspec.md`).

For each change, in the order of section 3:

1. **Read and challenge the proposal and the specs.** They describe observable behavior only. If something is
   wrong, unclear or not wanted, change the proposal and the specs first; do not carry a disagreement into the
   code. The open questions of section 5 are the places where a decision is expected.
2. **Validate**: `openspec validate <change> --strict` must pass after every edit.
3. **Write `design.md`.** The rules in `openspec/config.yaml` apply: name the extension, the INI files and keys
   and where they live, the policies and limitations, the database tables and how an existing installation gets
   them, and justify any edit in `lib/` or `kernel/` (none is expected for any of the seven). Resolve the
   **Verify** items of section 5 by reading or running the code, and record the result in `design.md`.
4. **Write `tasks.md`**, grouped so that each group lands its own tests and documentation.
5. **Write `plan.md`**: the tasks as executable steps, test first.
6. **Apply** in a separate worktree (named `exponential-<change-name>`), never in the main checkout. For changes
   that land in a package (3 and 5), the code goes to that package's repository; the plan stays here.
7. **Write `verify.md`**: the exact phpunit run line and result of each affected suite, and the other checks of
   `openspec/config.yaml`.
8. **Archive** once merged (for a package: once the release that carries it is required by `composer.json`):
   `openspec archive <change>`. Its specs then move to `openspec/specs/`.

With the OpenSpec command line tool (`openspec`), `openspec status --change <name>` shows which artifact is
next and `openspec instructions <artifact> --change <name>` prints the template and the rules for it. A coding
agent with the OpenSpec commands installed (`/opsx:continue`, `/opsx:apply`, `/opsx:verify`, `/opsx:archive`)
walks through the same steps. Without either, the templates are in `openspec/schemas/plan-driven/templates/`.

When a change is done, update the table "Planned and finished changes" in
`doc/eng-us/standards/planned-changes-with-openspec.md` (and its German counterpart).

## 5. Per change: what is decided, what is open

### 5.1 `add-workflow-outward-action-safeguards`

Decided: outward is a property of the action class, declared by the class, not by the definition; every
delivery of a subscribed webhook is outward too. `notify` is not outward (it addresses users of the site only).
A definition that contains an outward action can be published, but outward deliveries are made only when
`expworkflow.ini` allows outward delivery for the site. A test run never changes the real object: outward mail
goes to the person testing, every other outward delivery is recorded with its payload and not sent. Idempotency
per instance, action and recipient. Fan-out decides the `failed` transition only for the on-enter job action of
a stage. Bulk operations set a suppression flag that outward deliveries honor.

Open: how a bulk operation announces itself. Candidates are an explicit scope object that importers, content
jobs and restores enter and leave, or a check of the running script. **Verify** which bulk paths exist in the
kernel and the installed extensions (package import, `ezcontentobject` restore from trash, CSV imports of
cjw_newsletter, content jobs) and whether all of them run outside a web request.

### 5.2 `add-channel-posts`

Decided: one row per post and destination with the states pending, sent, failed and cancelled; producers only
write the row; one poster per provider; retries and the sweep through expqueue; credentials only in a secrets
override, never in the database. The workflow action `channel.post` is the only producer; posting on publish is
a definition with that action. Once per instance, action and destination; a later version posts again only when
the action allows it. First providers: Mastodon and Matrix (open protocols); Slack, LinkedIn and others are later
posters behind the same interface.

Next posters after Mastodon and Matrix: Bluesky (AT Protocol, `com.atproto.repo.createRecord` with an app
password; a deterministic record key (`rkey`) derived from the post id makes a retry fail detectably instead of
posting twice; links need facets to be clickable, images go through `uploadBlob`, and a post has at most 300
graphemes) and LinkedIn (Posts API with an organization or member access token; needs an app approved by LinkedIn and
a token refresh — **verify** the current access requirements before building it).

Open: the extension name (`expchannels` is a proposal); how "the content was last written by the AI service or a
system user" is detected (expai's draft log, the version's creator, or both). **Verify** the posting APIs of the
first two providers (Mastodon `POST /api/v1/statuses` with `Idempotency-Key`; Matrix
`PUT /_matrix/client/v3/rooms/{roomId}/send/m.room.message/{txnId}`), both of which deduplicate by a key the
client chooses.

### 5.3 `add-newsletter-welcome-series`

Decided: a series belongs to a list; a subscriber enters it when the subscription becomes **approved** — the
state the list sends to. In cjw_newsletter, confirming sets the subscription to confirmed and only lists with
`auto_approve_registered_user` approve it at once; otherwise an administrator approves. Entering is idempotent
(confirming an approved subscription again must not restart anything). While a subscription is not approved, no
step is sent and progress is kept; when it is approved again, the series continues. One step per subscriber
per send run; each step at most once. Steps are sent through the existing send path, so statistics, tracking
settings and privacy rules apply unchanged.

Open: whether a step is a dedicated edition or a reference to an existing one. **Verify** where the status change
to approved happens (`CjwNewsletterSubscription`, the admin approval, the list's auto-approve) and add an extension
point for it — `CjwNewsletterExtensionPoints` has none today — and how a send to one subscriber fits
`CjwNewsletterEditionSend`, which today sends an edition to a whole list.

### 5.4 `add-form-destinations`

Decided: a form is an ordinary content object whose class collects information (the kernel's information
collection); destinations are configured per class or per object and each can be switched on alone: subscribe
to a newsletter list (always with double opt-in), start a welcome series, send to a webhook of the webhook admin
as the new event "form submitted" (a submission has no workflow instance, so the payload carries the form, the
time and the mapped fields instead). A failing
destination never loses the submission and never shows the visitor an error caused by a destination.

Open: which extension carries it. **Verify** whether the kernel's information collection has a hook after a
collection is stored that an extension can use without a kernel change; if it does not, the smallest kernel
change must be justified in `design.md` before anything else. Also open: the share link with a QR code
(generated server-side, no external service).

### 5.5 `add-ai-field-provenance`

Decided: expai records per content object, version, language and attribute that a value came from AI; the edit
form and the full view show an "AI — check" mark at such a field; a person's change to the field removes the
mark; a later AI run may replace a field that is still marked, never one a person has written or confirmed.
AI field actions are offered only to users who may edit the version.

Open: whether the mark is copied to a new version when an editor creates one without touching the field (the
expected answer is yes). **Verify** how the existing draft log of expai (`expai_draft_log`) and the field actions
of the edit form already record AI writes — the draft log is per version today, with the action `field`, not per
attribute —, and build on them instead of adding a parallel record.

### 5.6 `add-newsletter-segments`

Decided: a segment is a saved selection of the subscribers of one list (conditions on subscription, profile fields,
interests and reading behavior, combined with all or any), evaluated when a send's mail queue is made. Behavior
conditions read only the per-person statistics, which exist only for subscribers who consented to newsletter
statistics (tracking mode "per person with consent") and only within `[TrackingSettings] PersonRetentionMonths`;
they never match anybody else, and the segment page says how many subscribers they cannot see.

A series audience is a segment: checked when a subscriber would enter and before each step (see
`add-newsletter-welcome-series`). A send to a segment goes to members whose subscription is approved, the state the
list sends to.

Open: whether the existing virtual lists (`CjwNewsletterListVirtual`, filter types e-mail and salutation) become
segments or stay beside them; whether the segment page hides very small counts for users who may not see
subscribers (a count of 1 can identify a person) — decide and record it in design.md. **Verify** how the send form and the queue creation pick the recipients of a list today
and where a segment fits without a second recipient path.

### 5.7 `add-share-metadata`

Decided: one template writes Open Graph and Twitter Card tags from attributes configured per class with fallbacks
and site defaults; the bundled designs include it and drop their own tags in the same step; Matrix and other
messengers are served by "copy link" (they have no share URL); the LinkedIn link carries only the address; admin4 shows a share preview with warnings; share links are plain
links, no script or image of a network is loaded before a click; Mastodon asks for the visitor's instance and keeps
it in the browser only.

Open: the carrying extension (new, or the one that holds the site metadata datatype). Today `bootstrap5`, `media`
and `simple` carry copies of the same `og:*` code in `page_head.tpl` (`media` adds its image through the metadata
extension's own template), and `starter` has a separate `pagelayout/head/opengraph.tpl` with its own fallback
chain; all read the site metadata datatype. **Verify** the attributes each reads, so the default configuration gives
the same result before a site changes anything.

## 6. Out of scope for all seven

- A separate campaign or marketing-automation engine, or an external automation platform inside the CMS.
  External tools talk to the CMS through webhooks and the REST API (as in #16702, section 11).
- A CRM with deals or a sales pipeline.
- Tracking beyond what cjw_newsletter already measures and its privacy rules allow.
- Branching inside a newsletter series (a series is linear; branching belongs to workflow definitions).
