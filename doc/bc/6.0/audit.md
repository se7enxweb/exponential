# Audit: tracking what happens in Exponential

Status: **design, agreed with the owner on 2026-10-02; stages 1 and 2 built (see "Stage 1 results" and "Stage 2 — built" at the end).** This document is the specification the
work is built against, stage by stage (see "Delivery"). It also records the dashboard permission defect found the same
day, which is stage 1.

Where the owner has decided, this document says so and cites the decision (Q1 … Z10). Where a detail was not decided,
the text says **Proposed:** — those points are open for the owner to confirm or change before the stage that needs
them starts. Every file and function named as an existing hook point was read in the code on 2026-10-02; a point that
does not exist yet is written "new hook point: <where>".

Contents: what exists today · owner decisions · the event model · **the event catalogue** · **the record format**
(fields, privacy, hash chain, archive manifest) · **the settings reference** (`settings/audit.ini`) · **the developer
API** · **the index** · **the console** · **sinks** · **alerts** · **rotation, archives and retention** · **the
command `exp:audit`** · **security notes** · **performance** · default installation · delivery (with deliverables
per stage) · **acceptance tests** · dashboard defect.

## What exists today

`kernel/classes/ezaudit.php` (`eZAudit`, 134 lines) and `settings/audit.ini`:

- **Off** by default (`[AuditSettings] Audit=disabled`); no installation file enables it on alpha.
- `eZAudit::writeAudit( $name, $attributes )` writes a plain text block through `eZLog::write()` to
  `<VarDir>/log/audit/<file>.log` (`[FileSettings] VarDir` of site.ini plus `[AuditSettings] LogDir`, see
  `eZAudit::fetchAuditNameSettings()`), one file per event name (`AuditFileNames[<name>]=<file>`). `eZLog::write()`
  (lib/ezfile/classes/ezlog.php) prefixes the time, the siteaccess (`eZLog::siteAccessName()`) and the URL or command
  line (`eZLog::requestContext()`); eZAudit adds the address and `login:user id`, then one `Key: value` line per
  attribute:
  ```
  [ Oct 02 2026 13:30:01 ][ admin ][ https://admin.example.com/content/action ] [203.0.113.7] [editor1:14]
  Node ID: 275
  Object ID: 273
  ...
  ```
- **Rotation loses audit data.** `eZLog::write()` rotates a file when it is over `eZLog::maxLogSize()` (200 KiB,
  `MAX_LOGFILE_SIZE`, or the constant `CUSTOM_LOG_MAX_FILE_SIZE`) and `eZLog::rotateLog()` keeps
  `MAX_LOGROTATE_FILES` = 3 old copies, deleting the oldest (`@unlink`). An audit file therefore holds at most about
  800 KiB of history; a busy failed-login file loses its oldest entries within hours, silently.
- **Not safe under Velocity.** `eZAudit::isAuditEnabled()` and `eZAudit::auditNameSettings()` cache their result in
  `$GLOBALS['eZAuditEnabled']` and `$GLOBALS['eZAuditNameSettings']`, which a persistent worker keeps across
  requests: a changed `audit.ini` is not seen until the worker is restarted.
- **Some call sites record things they must not.** `extension/ezmbpaex/modules/userpaex/forgotpassword.php` writes
  the password-reset `HashKey` (a token) into the log; `eZUser::loginFailed()` writes the login name that was typed,
  which is sometimes a password typed into the wrong field; `eZUser::loginFailed()` passes the login through
  `eZDB::escapeString()` first, so the log shows SQL escaping.
- Ten event names are configured: user-login, user-failed-login, content-delete, content-move, content-hide,
  role-change, role-assign, section-assign, state-assign, order-delete. They are written from about a dozen places
  (eZUser, eZContentObject, eZContentObjectTreeNode, eZRole, the role edit view, the content operations, eZOrder; since
  today also the content jobs' hide and section types).
- The ezmbpaex extension writes `user-forgotpassword*` and `user-password-change*` events that have **no file
  configured**, so they are dropped.
- The 36 call sites, by enclosing function (collected 2026-10-02; the full mapping to the new names is in
  "Compatibility mapping" below):

  | Old name | Call sites (file:function) |
  |---|---|
  | `user-login` | kernel/classes/datatypes/ezuser/ezuser.php:`eZUser::loginSucceeded` |
  | `user-failed-login` | kernel/classes/datatypes/ezuser/ezuser.php:`eZUser::loginFailed`; extension/ezmbpaex/login_handler/ezpaexuser.php:`eZPaExUser::passwordHasExpired` |
  | `content-delete` | kernel/classes/ezcontentobject.php:`eZContentObject::purge`, `eZContentObject::removeThis`; kernel/classes/ezcontentobjecttreenode.php:`eZContentObjectTreeNode::removeThis` |
  | `content-move` | kernel/classes/ezcontentobjecttreenode.php:`eZContentObjectTreeNode::move` |
  | `content-hide` | kernel/classes/ezcontentobjecttreenode.php:`hideSubTree`, `unhideSubTree`; kernel/classes/contentjob/expcontentjobhidesubtree.php:`mainStep` |
  | `role-change` | kernel/private/classes/views/role/edit.php:`applyRole` |
  | `role-assign` | kernel/classes/ezrole.php:`eZRole::assignToUser` |
  | `section-assign` | kernel/classes/ezcontentobjecttreenode.php:`assignSectionToSubTree`; kernel/classes/contentjob/expcontentjobsectionsubtree.php:`prepare` |
  | `state-assign` | kernel/content/ezcontentoperationcollection.php:`updateObjectState`; extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:`updateObjectState` (a copy) |
  | `order-delete` | kernel/classes/ezorder.php:`eZOrder::cleanupOrder`, `eZOrder::cleanup` |
  | `user-password-change`, `user-password-change-self` | extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:`fetchObjectAttributeHTTPInput`; extension/ezmbpaex/modules/userpaex/password.php (file scope, 1 site) |
  | `user-password-change-self-fail` | extension/ezmbpaex/modules/userpaex/password.php (file scope, 5 sites) |
  | `user-forgotpassword`, `user-forgotpassword-fail` | extension/ezmbpaex/modules/userpaex/forgotpassword.php (file scope, 10 sites) |
- Missing: an event id, request/session correlation, siteaccess, URL, server engine, duration, result, before/after
  values, a structured format, retention, compression, archives, integrity, a viewer, access control, alerts.
- Not audited at all: publish/edit/translate, locations, swap, trash/restore, classes, policies, user lifecycle,
  settings writes (setup views, `exp:ini`, the debug bar), cache clears, cronjobs, commands, logout/sessions,
  permission and form-token refusals, packages/installs, the repair queue, content jobs as a whole, commerce beyond
  order delete, exports/imports.

## Owner decisions (27 questions, 2026-10-02)

| # | Question | Decision |
|---|---|---|
| Q1 | Storage | **Both**: JSON lines files are the record; an index in the site's database serves the console (F4) |
| Q2 | Fields | **Request context** (event id, request id, siteaccess, URL/method, module/view, engine Apache/Velocity/CLI, host, pid, duration, HTTP status), **actor** (user id, login, roles at the time, session hash, IP v4/v6, user agent, impersonation, CLI user + command), **before/after values** (configurable, never passwords/tokens), **result + reason** (success/refused/failed, policy/token/lock reason, error) |
| Q3 | Privacy | **Configurable, safe default**: per field full / truncated IP (/24, /48) / hashed / off; user agent on/off; never passwords or tokens; personal fields pseudonymised after the retention period |
| Q4 | Integrity | **Hash chain + signed archives**: each event carries the previous event's hash per file; archives get a checksum manifest and an HMAC; the console shows whether a chain is intact or where it breaks |
| Q5 | Families | **Content lifecycle, users + access, system + config, commerce + data** — and the classification must be generic and reusable ("track almost everything, zoology style"): see the taxonomy below |
| Q6 | Files | **Channels by family, daily files** (`content-2026-10-02.jsonl`, `access-…`, `system-…`, `commerce-…`); INI maps events to channels; the old per-event file names keep working as aliases |
| Q7 | Sinks | **syslog/journald, webhook/HTTP, e-mail on critical events, a sink registry** for extensions |
| Q8 | Rotation | **By day and size; compressed archives** (gzip, bzip2, xz, zstd, zip through format handlers in a registry) to a configurable archive path; **retention per channel; scheduled by a cronjob part** (also runnable from the console and the command) |
| Q9 | Access | New policies **audit/read** (view, search, export) and **audit/manage** (rotation, archives, retention, settings); every console access is itself audited; optional password re-entry before manage actions |
| Q10 | Console | **Timeline + filters + search, event detail + links, charts + alerts view, export** |
| F1 | Taxonomy | **Both**: hierarchical dotted names for configuration and routing, and every record also carries actor / verb / object / target / result (ActivityStreams-like) |
| F2 | Correlation | **Request id** (also a response header), **session and job ids** (content jobs, cronjob runs), **parent/child events** (a subtree remove is a parent with child events, depth configurable) |
| F3 | Speed | **Buffered**, flushed in one append at request end (also on fatal errors through the shutdown handler); security events written at once; per-request state reset for Velocity's persistent workers |
| F4 | Index | **The site's main database** (schema on every engine: Z1) |
| F5 | Alerts | **Built-in rules** (brute force, admin role granted, settings written out of hours, mass delete, audit disabled or chain broken), **INI rules** (`[AlertRule_x]` Event, Threshold, Window, GroupBy, Severity, Sinks[]), **rule classes** in a registry |
| F6 | Retention | **90 days live, 2 years archived**, per channel; personal fields pseudonymised in the index after 90 days |
| F7 | Dashboard | **Only what the user's policies allow**: every dashboard block and sidebar link checks access to its module/view (and limitations) |
| Z1 | Engines | **SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB** — schema and tests on each one reachable here |
| Z2 | Placement | **A new `audit` module with its own top tab** (console, event, archives, settings), **dashboard sidebar link + block** (recent security events, alerts), **links from content/job and content/jobs** (the job's audit trail), **the Setup menu** |
| Z3 | API | **`expAudit::event()`** (the old `eZAudit::writeAudit()` keeps working and maps to it), **template operator/fetch** (policy checked), **ezpEvent bridge** (audit existing kernel events by INI mapping), **command `exp:audit`** (tail, search, verify, rotate, archive, reindex, export, import) |
| Z4 | Old logs | **Imported** into the new format (marked imported, outside the chain); originals archived |
| Z5 | Default | **On by default in every installation** (owner, 2026-10-02: "enabled in a default installation by default conventions, vs ezp4 where it was off"): the shipped `settings/audit.ini` has `Audit=enabled` with access, security, system/config and destructive content actions on; read tracking off. See "Default installation" |
| Z6 | Reads | **Optional, sampled**: node views and searches per section/class with a sample rate; views of sensitive admin modules (setup, role, user, audit) always |
| Z7 | Key | **Generated on first use, stored in settings/override** (never committed), shown as a fingerprint; key rotation with key ids in archives |
| Z8 | Delivery | **Stages with sign-off** (below) |
| Z9 | Proof | **Coverage matrix, tamper test, performance, permission matrix** |
| Z10 | Docs | **Operator guide, developer guide, event reference, security notes** |

## The event model

### Taxonomy (F1)

A dotted name with ranks like a biological classification — **domain.subject.action[.detail]** — so configuration
and routing can work at any level, and new branches can be added without touching existing ones:

```
content.node.publish        content.node.move         content.node.remove.trash    content.object.translate
content.job.start           content.job.finish        content.job.cancel
access.session.login        access.session.login.failed   access.session.logout    access.permission.refused
access.token.refused        access.user.create        access.role.assign           access.policy.change
system.setting.write        system.cache.clear        system.cronjob.run           system.command.run
system.package.install      system.velocity.deploy    system.audit.chain.broken
commerce.order.delete       commerce.basket.checkout  data.export.csv              data.import
```

INI switches on or off at any rank (`content.*`, `content.node.*`, `content.node.remove.*`), maps branches to
channels and sinks, and extensions register whole branches (`[AuditEventSettings] Branches[myext]=…`). Every record
also carries the activity fields **actor, verb, object, target, result**, so it can be fed to activity-stream
consumers unchanged.

### One record (JSON line)

```json
{"v":1,"id":"01J9Z…","name":"content.node.move","time":"2026-10-02T13:30:01.123Z",
 "request":{"id":"r-7f3c…","siteaccess":"admin","method":"POST","url":"/content/action","module":"content/action",
            "engine":"velocity","host":"alpha","pid":991876,"ms":184,"status":302},
 "actor":{"user_id":14,"login":"admin","roles":[2],"session":"h:5d2e…","ip":"203.0.113.0/24","ua":"Firefox 131"},
 "verb":"move","object":{"type":"node","id":275,"object_id":273,"name":"Workout"},
 "target":{"type":"node","id":89},"before":{"parent":2},"after":{"parent":89},
 "result":"success","reason":null,"parent":null,"job":null,
 "prev":"sha256:9b1c…","hash":"sha256:41aa…"}
```

Every field, its privacy options and the hash are specified in "The record format" below.

### Naming rules

- A name is 3 to 6 ranks of `[a-z][a-z0-9_]*`, joined by dots: `domain.subject.action[.detail[.detail]]`. The
  domain is one of the five of Q5/F1 (`content`, `access`, `system`, `commerce`, `data`); an extension adds subjects
  under a domain (`content.myext_poll.vote`), never a sixth domain. **Proposed:** an extension's subjects start with
  its name (`content.myext_*`) so two extensions cannot claim the same branch; the taxonomy registry refuses a branch
  registered twice and the RAD survey lists it as broken.
- The action is a verb in the present tense (`move`, `remove`, `assign`); a detail narrows it (`remove.trash`,
  `login.failed`). An outcome other than success is **not** a new name except where an alert or a filter needs it on
  its own (`login.failed`, `password.change.failed`, `reset.failed`): everywhere else the record's `result` says
  `refused` or `failed` and `reason` says why.
- The record's `verb` is the action rank (`move`), its `object.type` the subject rank (`node`); a consumer that only
  understands activity streams reads those and ignores `name`.
- Patterns in settings use `*` for one or more whole ranks at the end (`content.*`, `content.node.remove.*`) and
  match the name itself too (`content.node.remove.*` matches `content.node.remove` and `content.node.remove.trash`).
  The most specific pattern wins (the one with the most literal ranks); on a tie the later line wins.

### Actor, verb, object, target, result

| Field | What it is | Examples |
|---|---|---|
| actor | who did it: the current user, or the user a command or a content job runs as | `{"user_id":14,"login":"editor1"}`; a cronjob: the user it runs as (anonymous unless the part logs in) plus `cli` |
| verb | the action rank of the name | `move`, `assign`, `write`, `clear`, `login` |
| object | what was acted on | `{"type":"node","id":275,"object_id":273,"name":"Workout"}`, `{"type":"setting","file":"site.ini","block":"DebugSettings","variable":"DebugOutput","scope":"override"}` |
| target | where to / whom to, when there is one | the new parent of a move, the user or group a role is assigned to, the section assigned |
| result | `success`, `refused` (a policy, a token, a lock or a validation said no) or `failed` (an error) | with `reason` |

## The event catalogue

The events the system emits, by domain. Columns:

- **Where**: the file and function where the event is raised — verified in the code — or "new hook point: …" where
  no single function exists yet. Paths are relative to the installation root; `V/` is
  `kernel/private/classes/views/`, `OC` is `kernel/content/ezcontentoperationcollection.php` (class
  `eZContentOperationCollection`), `TN` is `kernel/classes/ezcontentobjecttreenode.php` (`eZContentObjectTreeNode`).
- **Actor / verb / object → target**: the actor is the current user unless stated.
- **Before → after**: what the record holds in `before` and `after`. *Never*: what is never recorded, whatever the
  privacy settings say.
- **Default**: `on` / `off` in the shipped `settings/audit.ini` (Z5: access, security, system/config and destructive
  content actions on; reads off), `always` (cannot be switched off: the audit trail of the audit itself, and the
  sensitive views of Z6), `sampled` (a read, off by default, sampled when switched on).
- **Ch.**: the channel (Q6) the shipped routing sends it to.
- `[old]`: the 4.x name it replaces (see "Compatibility mapping").

**Children (F2).** An action that touches many things is one **parent** event with **child** events of the same name
(`parent` set to the parent's id, `depth` 1, 2, …). A subtree remove of 400 nodes is one `content.node.remove` parent
with up to `[AuditRecordSettings] ChildDepth` levels and `MaxChildren` children; past those limits the parent's
`after.children_omitted` counts what was not written one by one. Content jobs are parents of the events their
batches raise; a cronjob run is the parent of its parts.

### content (channel `content`)

| Name | Fires when | Where | Actor / verb / object → target | Before → after (never) | Default | Ch. |
|---|---|---|---|---|---|---|
| `content.object.create` | the first version of a new object is published | OC:`setObjectStatusPublished` (operation `content/publish`, kernel/content/operation_definition.php), when no earlier version was published | publish · object (id, class, version, languages) → node (main location) | – → class, languages, section, owner (never attribute values) | off | content |
| `content.object.publish` | a later version is published | OC:`setObjectStatusPublished` | publish · object → main node | version, languages, modified → version, languages, modified, changed attribute identifiers (never attribute values) | off **Proposed:** on — "who published this" is the most asked audit question; the owner's default list names destructive actions only, so it ships off until confirmed | content |
| `content.object.translate` | a published version adds a language the previous one did not have | OC:`setObjectStatusPublished` (compare language masks) | translate · object → language | languages → languages | off | content |
| `content.object.translation.remove` | a translation is removed from an object | OC:`removeTranslation` | remove · object → language(s) | languages → languages | on | content |
| `content.version.remove` | archived or draft versions are removed by an editor | V/content/history.php:`run`, V/content/removeeditversion.php:`run` | remove · version (object id, version numbers) | status, language → – | on | content |
| `content.node.move` | a location gets a new parent `[content-move]` | TN:`move` (called by OC:`moveNode`, the move job) | move · node → new parent node | parent, path → parent, path | on | content |
| `content.node.copy` | a node or subtree is copied | OC:`copyNode`, V/content/copy.php, V/content/copysubtree.php, kernel/classes/contentjob/expcontentjobcopysubtree.php | copy · source node → new parent; children per copied node | – → new node id, new object id | off | content |
| `content.node.add` | a location is added to an object | OC:`addAssignment` | add · object → parent node(s) | locations → locations | off | content |
| `content.node.remove` | a location is removed, the object stays | OC:`removeNodes`, kernel/classes/contentjob/expcontentjobremovelocation.php | remove · node → – (children per subtree node) | parent, path, object id, name → – | on | content |
| `content.node.remove.trash` | an object goes to the trash `[content-delete]` | OC:`deleteObject` with `$moveToTrash`, TN:`removeThis`, kernel/classes/contentjob/expcontentjobremovesubtree.php | remove · node → trash (children per node) | parent, path, object id, name, class → – | on | content |
| `content.object.remove` | an object is removed for good, not through the trash `[content-delete]` | kernel/classes/ezcontentobject.php:`eZContentObject::removeThis` | remove · object | name, class, owner, locations → – | on | content |
| `content.object.purge` | an object is purged (trash emptied, version purge) `[content-delete]` | kernel/classes/ezcontentobject.php:`eZContentObject::purge` | purge · object | name, class → – | on | content |
| `content.object.restore` | an object is restored from the trash | V/content/restore.php:`run` | restore · object → parent node | – → node, parent | on | content |
| `content.trash.empty` | the trash is emptied, or a selection purged | kernel/private/classes/services/trash.php:`Trash::purgeObjects`, `Trash::emptyTrash`, `Trash::purge` (V/content/trash.php, bin/php/trashpurge.php, cronjobs/trashpurge.php); children: `content.object.purge` | purge · trash | count → count | on | content |
| `content.node.hide` | a subtree is hidden `[content-hide]` | TN:`hideSubTree` (OC:`changeHideStatus`, kernel/classes/contentjob/expcontentjobhidesubtree.php:`mainStep`) | hide · node | visibility → visibility | on | content |
| `content.node.reveal` | a subtree is revealed `[content-hide]` | TN:`unhideSubTree`, kernel/classes/contentjob/expcontentjobrevealsubtree.php | reveal · node | visibility → visibility | on | content |
| `content.node.swap` | two locations swap their objects | OC:`swapNode` | swap · node → node | object ids → object ids | on | content |
| `content.node.section` | a section is assigned to a subtree `[section-assign]` | TN:`assignSectionToSubTree`, OC:`updateSection`, kernel/classes/contentjob/expcontentjobsectionsubtree.php:`prepare` | assign · node → section | section → section | on | content |
| `content.object.state` | object states are assigned `[state-assign]` | OC:`updateObjectState`, kernel/classes/contentjob/expcontentjobstatesubtree.php | assign · object → state(s) | states → states | on | content |
| `content.node.main` | the main location changes | OC:`updateMainAssignment` | assign · object → node | main node → main node | off | content |
| `content.node.sort` | a node's sort order changes | OC:`changeSortOrder` | sort · node | field, order → field, order | off | content |
| `content.node.priority` | priorities of children change | OC:`updatePriority` | sort · parent node | priorities → priorities | off | content |
| `content.object.always_available` | the always-available flag changes | OC:`updateAlwaysAvailable` | change · object | flag → flag | off | content |
| `content.object.initial_language` | the initial language changes | OC:`updateInitialLanguage` | change · object → language | language → language | off | content |
| `content.urlalias.change` | URL aliases or wildcards are added or removed | V/content/urlalias.php, urlalias_global.php, urlalias_wildcard.php:`run` | change · alias → node | alias → alias | off | content |
| `content.class.create` | a new content class is stored | V/class/edit.php:`run` (first store of a class) | create · class | – → identifier, attribute identifiers | on | content |
| `content.class.change` | a class definition is stored | V/class/edit.php:`run` | change · class | identifier, attributes (identifier, datatype, required, searchable) → the same | on | content |
| `content.class.remove` | classes are removed | V/class/removeclass.php:`run`; V/class/removegroup.php:`run` | remove · class | identifier, object count → – | on | content |
| `content.class.copy` | a class is copied | V/class/copy.php:`run` | copy · class → new class | – → identifier | off | content |
| `content.section.change` | a section is created or edited | V/section/edit.php:`run` | change · section | name, identifier, navigation part → the same | on | content |
| `content.section.remove` | a section is removed | V/section/list.php:`run` (ConfirmRemoveSectionButton) | remove · section | name, identifier → – | on | content |
| `content.state.change` | a state or state group is created or edited | V/state/edit.php, V/state/group_edit.php:`run` | change · state | identifier, translations → the same | on | content |
| `content.state.remove` | state groups or states are removed | V/state/groups.php, V/state/group.php:`run` | remove · state group | identifier → – | on | content |
| `content.job.create` | a content job is created | kernel/classes/contentjob/expcontentjob.php:`expContentJob::create` | create · job (id, type, root node) | – → type, params (node ids only), mode | on | content |
| `content.job.start` | a worker starts or resumes a job | kernel/classes/contentjob/expcontentjobworker.php:`runLocked` | start · job | state → state, attempts | on | content |
| `content.job.finish` | a job ends `done` | expcontentjobworker.php:`runLocked` | finish · job | – → nodes done, ms | on | content |
| `content.job.fail` | a job ends `failed` | expcontentjobworker.php:`fail` | fail · job | – → error, node id | on | content |
| `content.job.cancel` | a cancel is requested or takes effect | expcontentjob.php:`cancel`; expcontentjobworker.php:`cancelled` | cancel · job | state → state | on | content |
| `content.job.resume` | a stopped job is resumed | expcontentjob.php:`resume` | resume · job | state → state | on | content |
| `content.node.view` | a node is viewed (Z6) | V/content/view.php:`run` | read · node | – (never the rendered page) | sampled | read |
| `content.search.query` | a search is run (Z6) | V/content/search.php, V/content/advancedsearch.php:`run` | read · search | – → phrase (truncated to 64 characters), hit count | sampled | read |
| `content.object.download` | a file attribute is downloaded | V/content/download.php:`run` | read · object → attribute | – | sampled | read |

### access (channel `access`)

| Name | Fires when | Where | Actor / verb / object → target | Before → after (never) | Default | Ch. |
|---|---|---|---|---|---|---|
| `access.session.login` | a user logs in `[user-login]` | kernel/classes/datatypes/ezuser/ezuser.php:`eZUser::loginSucceeded` (every login handler reaches it through `eZUser::loginUser`) | login · user | – → session (hashed), handler (standard, ldap, …) (never the password) | on | access |
| `access.session.login.failed` | a login attempt fails `[user-failed-login]` | ezuser.php:`eZUser::loginFailed`; extension/ezmbpaex/login_handler/ezpaexuser.php:`passwordHasExpired` (reason `password_expired`) | login · user (or the attempted login) | – → attempts, reason (never the password; the attempted login of an **unknown** user is always hashed, as it is often a password typed in the wrong field) | on | access |
| `access.session.logout` | a user logs out | ezuser.php:`eZUser::logoutCurrent` | logout · user | session → – | on | access |
| `access.session.regenerate` | the session id is renewed | lib/ezsession/classes/ezsession.php:`eZSession::regenerate` (ezpEvent `session/regenerate`) | regenerate · session | old hash → new hash | off | access |
| `access.session.expire` | a session is destroyed or collected | ezpEvent `session/destroy` (lib/ezsession/classes/ezpsessionhandlerdb.php); kernel/private/classes/services/sessiongarbagecollector.php | expire · session | – → count | off | access |
| `access.session.reauth` | a user re-enters the password before an audit/manage action (Q9) | new hook point: the audit module's re-authentication view | reauth · user | – | on | access |
| `access.session.reauth.failed` | that re-entry fails | same | reauth · user | – (never the password) | on | access |
| `access.user.lock` | failed logins reach `[UserSettings] MaxNumberOfFailedLogin` | new hook point: ezuser.php:`eZUser::setFailedLoginAttempts` when the count reaches the limit | lock · user | attempts → attempts, enabled | on | access |
| `access.user.unlock` | the failed-login counter is reset by an administrator | ezuser.php:`eZUser::setFailedLoginAttempts` with `$setByForce`; V/user/setting.php:`run` | unlock · user | attempts → 0 | on | access |
| `access.permission.refused` | a module view is refused by policy | kernel/private/classes/ezpkernelweb.php:`dispatchLoop` (`eZError::KERNEL_ACCESS_DENIED`); lib/ezutils/classes/ezmodule.php:`eZModule::handleError` for refusals raised inside a view | access · module/view → node or object when known | – → the policy asked (module/function), limitation that failed | on | access |
| `access.token.refused` | a POST is refused for a missing or wrong form token | extension/ezformtoken/event/ezxformtoken.php:`ezxFormToken::input` (`refusal('missing'|'wrong')`), answered by ezpkernelweb.php:`formTokenRefusalResult` | post · module/view | – → reason `missing`/`wrong` (never the token) | on | access |
| `access.view.sensitive` | a view of setup, role, user or audit is opened (Z6 "always") | new hook point: ezpkernelweb.php:`dispatchLoop` after `hasAccessToView()` passed, for modules in `[AuditReadSettings] AlwaysModules[]` | read · module/view | – (never POST bodies) | always | access |
| `access.user.create` | a user account is created | new hook point: ezuser.php:`eZUser::store` when the row is new (V/user/register.php, the admin's user edit, installers) | create · user | – → login, email (privacy rules), groups | on | access |
| `access.user.activate` | an account is activated by its link | V/user/activate.php:`run` | activate · user | enabled → enabled (never the activation hash) | on | access |
| `access.user.enable` / `access.user.disable` | an administrator enables or disables an account | V/user/setting.php:`run` (`is_enabled`) | enable/disable · user | enabled, max_login → the same | on | access |
| `access.user.remove` | a user is removed | ezuser.php:`eZUser::removeUser` | remove · user | login, email (privacy rules) → – | on | access |
| `access.user.email.change` | the e-mail address of an account changes | new hook point: ezuser.php:`eZUser::store` when `email` differs from the stored row | change · user | email → email (privacy rules: hashed by default) | on | access |
| `access.user.login.change` | the login name changes | same, `login` differs | change · user | login → login | on | access |
| `access.user.password.change` | a password is changed `[user-password-change*]` | V/user/password.php:`run`; extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:`fetchObjectAttributeHTTPInput`; extension/ezmbpaex/modules/userpaex/password.php | change · user (self or another) | – (never the password, the hash or its type) | on | access |
| `access.user.password.change.failed` | a change is refused (wrong old password, rules) `[user-password-change-self-fail]` | same | change · user | – → reason | on | access |
| `access.user.password.reset.request` | a reset mail is requested `[user-forgotpassword]` | V/user/forgotpassword.php:`run`; extension/ezmbpaex/modules/userpaex/forgotpassword.php | request · user (when found) | – → mail sent yes/no (never the hash key) | on | access |
| `access.user.password.reset` | a reset completes `[user-forgotpassword]` | same | reset · user | – (never the hash key, never the password) | on | access |
| `access.user.password.reset.failed` | a reset link is unknown or expired, or the address unknown `[user-forgotpassword-fail]` | same | reset · user or – | – → reason `unknown_key`/`expired`/`unknown_email` (never the key; an unknown e-mail address is hashed) | on | access |
| `access.role.create` | a role is created | kernel/classes/ezrole.php:`eZRole::createNew` (V/role/edit.php) | create · role | – → name | on | access |
| `access.role.change` | a role is stored after editing `[role-change]` | V/role/edit.php:`applyRole` (before `revertFromTemporaryVersion()`); children `access.policy.*` | change · role | name, policies → name, policies | on | access |
| `access.role.remove` | roles are removed | ezrole.php:`eZRole::removeRole` (V/role/list.php:`run`) | remove · role | name, policies, assignments → – | on | access |
| `access.role.copy` | a role is copied | V/role/copy.php:`run` | copy · role → new role | – → name | on | access |
| `access.role.assign` | a role is assigned to a user or group `[role-assign]` | ezrole.php:`eZRole::assignToUser` (V/role/assign.php) | assign · role → user or group | – → limitation (subtree, section) | on | access |
| `access.role.unassign` | an assignment is removed | ezrole.php:`eZRole::removeUserAssignment`, `removeUserAssignmentByID` | unassign · role → user or group | limitation → – | on | access |
| `access.policy.add` | a policy is added to a role | ezrole.php:`eZRole::appendPolicy` (child of `access.role.change`) | add · policy → role | – → module, function, limitations | on | access |
| `access.policy.remove` | a policy is removed | kernel/classes/ezpolicy.php:`eZPolicy::removeThis`; ezrole.php:`removePolicy` | remove · policy → role | module, function, limitations → – | on | access |

### system (channel `system`)

| Name | Fires when | Where | Actor / verb / object → target | Before → after (never) | Default | Ch. |
|---|---|---|---|---|---|---|
| `system.setting.write` | an INI file is written | kernel/classes/ini/expinieditor.php:`expIniEditor::save` (exp:ini, the debug bar's `expDebugBarSettings::write`); lib/ezutils/classes/ezini.php:`eZINI::save` (V/settings/edit.php, V/settings/view.php, V/setup/settingstoolbar.php, the setup wizard steps in kernel/setup/steps/) | write · setting (file, block, variable, scope, path) | value in that file and value in effect → the same; for `expIniEditor` also the unified diff from `diff()` (never a value whose variable `expIniEditor::isSecret()` recognises: it is written as `[secret]`) | on | system |
| `system.setting.undo` | a debug bar write is undone | kernel/classes/debugbar/expdebugbarsettings.php:`undo`, `undoGroup` | undo · setting → the undone log entry | as `system.setting.write` | on | system |
| `system.extension.change` | ActiveExtensions or its order is written | kernel/private/classes/ezpactiveextensions.php:`ezpActiveExtensions::write` (V/setup/extensions.php:`run`) | change · ActiveExtensions | list → list | on | system |
| `system.cache.clear` | caches are cleared on request | kernel/classes/ezcache.php:`eZCache::clearAll`, `clearByTag`, `clearByID` (V/setup/cache.php, bin/php/ezcache.php, the debug bar, exp:velocity deploy) | clear · cache (tags or ids) | – → ids, ms | on | system |
| `system.cronjob.run` | a cronjob part runs (parent: the runcronjobs invocation) | kernel/private/classes/runnable/runnable.php:`Runnable::runWithEvents` (kind `cronjob`); kernel/classes/ezruncronjobs.php:`eZRunCronjobs::runScript` for plain-file parts | run · cronjob part → part name (`frequent`, `contentjobs`, …) | – → ms, result | on | system |
| `system.cronjob.fail` | a part throws or exits non-zero | same | run · cronjob part | – → error | on | system |
| `system.command.run` | a command runs | runnable.php:`Runnable::runWithEvents` (kind `command`); new hook point: kernel/classes/ezscript.php:`eZScript::startup`/`shutdown` for plain scripts | run · command (name, options; never option values whose name `isSecret()` recognises) | – → exit code, ms | on | system |
| `system.package.install` | a package is installed | kernel/classes/ezpackage.php:`eZPackage::install` (V/package/install.php, installers) | install · package | – → name, version, items | on | system |
| `system.package.uninstall` | a package is uninstalled | ezpackage.php:`eZPackage::uninstall` | uninstall · package | name, version → – | on | system |
| `system.package.import` | a package archive is imported | ezpackage.php:`eZPackage::import` (V/package/upload.php) | import · package | – → name, version, sha256 of the archive | on | system |
| `system.install.run` | an installation is made | new hook point: the end of the setup wizard (kernel/setup/steps/ezstep_create_sites.php), bin/php/install.php, bin/php/kickstarter.php | install · installation | – → siteaccesses, packages, database engine | on | system |
| `system.upgrade.run` | an upgrade check or upgrade script runs | V/setup/systemupgrade.php:`run`; new hook point: the upgrade scripts | run · upgrade | – → result | on | system |
| `system.velocity.deploy` | `exp:velocity deploy` or `restart` runs | new hook point: the exp:velocity command (vendor/se7enxweb/exponential-velocity) | deploy · Velocity | – → steps, ms, result | on | system |
| `system.repair.queue` | the repair queue is written or a repair key created | lib/ezutils/classes/ezprepairqueue.php:`ezpRepairQueue::writeSettings`, `createKey` | change · repair queue | entries → entries (never the key) | on | system |
| `system.maintenance.change` | maintenance mode is switched | kernel/private/classes/commands/maintenance.php (bin/php/maintenance.php) | change · maintenance | mode → mode | on | system |
| `system.template.change` | a template is created or edited in the admin | V/setup/templateedit.php, V/setup/templatecreate.php:`run` | change · template (path) | sha256 → sha256 (never the template text) | on | system |
| `system.workflow.trigger.change` | workflow triggers are changed | V/trigger/list.php:`run` | change · trigger | workflow → workflow | on | system |
| `system.error.fatal` | a request ends in a fatal error | lib/ezutils/classes/ezexecution.php:`eZExecution::uncleanShutdownHandler` | fail · request | – → error reference (`eZExecution::errorReference()`), file:line (never the message's arguments) | on **Proposed** | system |
| `system.audit.enable` | `Audit=enabled` takes effect where it was disabled | new hook point: `expAudit` startup, comparing with the state file `var/<site>/log/audit/.state` | enable · audit | state → state | always | system |
| `system.audit.disable` | `Audit=disabled` is written or found (Default installation) | new hook points: `expIniEditor::save`/`eZINI::save` for audit.ini, and `expAudit` startup (the state file) | disable · audit | state → state | always | system |
| `system.audit.setting.write` | any audit.ini variable is written (child of `system.setting.write`) | same | write · audit setting | as `system.setting.write` | always | system |
| `system.audit.read` | an audit console view, the fetch or `exp:audit tail/search` is used (Q9) | new: the audit module views, the fetch function, the command | read · audit (view, filters) | – → filters, result count | always | system |
| `system.audit.export` | records are exported | new: audit/export, `exp:audit export` | export · audit | – → filters, format, count, sha256 of the file | always | system |
| `system.audit.rotate` | a live file is closed and a new one started | new: `expAuditWriter` | rotate · channel file | – → file, records, last hash | always | system |
| `system.audit.archive` | files are compressed into the archive | new: the cronjob part, `exp:audit archive` | archive · channel → archive path | – → files, manifest, key id | always | system |
| `system.audit.purge` | retention removes live files, archives or index rows | new: the cronjob part, `exp:audit rotate` | purge · channel | – → files, rows, oldest kept | always | system |
| `system.audit.pseudonymise` | index rows past `PseudonymiseAfterDays` are pseudonymised | new: the cronjob part | pseudonymise · index | – → rows | always | system |
| `system.audit.verify` | a chain or an archive is verified | new: console, `exp:audit verify`, the cronjob part | verify · channel or archive | – → intact/broken, records | always | system |
| `system.audit.chain.broken` | verification finds a break | same | verify · channel file → line | – → file, line, kind (see Verification) | always | system |
| `system.audit.chain.repair` | the writer finds a torn last line and continues after it | new: `expAuditWriter` | repair · channel file | – → bytes skipped, last good hash | always | system |
| `system.audit.checkpoint` | the daily signed anchor of every channel's last hash | new: the cronjob part | checkpoint · channels | – → per channel: file, seq, hash, HMAC | always | system |
| `system.audit.reindex` | the index is rebuilt | new: `exp:audit reindex`, console | reindex · index | – → rows, ms | always | system |
| `system.audit.import` | old text logs are imported (Z4) | new: `exp:audit import` | import · old log file | – → file, sha256, records | always | system |
| `system.audit.key.create` | a signing or pseudonym key is generated (Z7) | new: `expAuditKeys` on first use | create · key | – → key id, fingerprint (never the key) | always | system |
| `system.audit.key.rotate` | a new signing key becomes active | new: `exp:audit key rotate`, console settings | rotate · key | key id → key id | always | system |
| `system.audit.sink.failed` | a sink cannot deliver (after its retries) | new: `expAuditSinkRegistry` | deliver · sink | – → sink, error, spooled count | always | system |
| `system.audit.alert` | an alert rule fires | new: `expAuditAlertEvaluator` | alert · rule → the events that matched | – → rule, count, window, group | always | system |
| `system.audit.overflow` | the buffer exceeded its limit and flushed early, or could not be written | new: `expAuditBuffer` | overflow · buffer | – → events, bytes | always | system |
| `system.audit.file.open` | the first record of every channel file (links to the previous file) | new: `expAuditWriter` | open · channel file | – → previous file, its last seq and hash | always | (each channel) |
| `system.audit.file.close` | the last record of a rotated file | new: `expAuditWriter` | close · channel file | – → records | always | (each channel) |

### commerce (channel `commerce`)

| Name | Fires when | Where | Actor / verb / object → target | Before → after (never) | Default | Ch. |
|---|---|---|---|---|---|---|
| `commerce.order.delete` | an order is removed `[order-delete]` | kernel/classes/ezorder.php:`eZOrder::cleanupOrder` (V/shop/removeorder.php) | remove · order | order number, status, total → – (never customer address fields) | on | commerce |
| `commerce.order.purge` | all orders are removed `[order-delete]` | ezorder.php:`eZOrder::cleanup` | purge · orders | count → – | on | commerce |
| `commerce.order.item.remove` | an order item is removed | ezorder.php:`eZOrder::removeItem` | remove · order item → order | product, count → – | on | commerce |
| `commerce.order.create` | an order is activated (checkout confirmed) | ezorder.php:`eZOrder::activate` | create · order | – → order number, total, currency (never the customer's address) | off | commerce |
| `commerce.order.status` | the order status changes | ezorder.php:`eZOrder::modifyStatus` (V/shop/setstatus.php) | change · order | status → status | off | commerce |
| `commerce.order.archive` / `commerce.order.unarchive` | an order is archived or brought back | ezorder.php:`eZOrder::archiveOrder`, `unArchiveOrder` | archive · order | flag → flag | off | commerce |
| `commerce.basket.checkout` | a basket goes to checkout | V/shop/checkout.php:`run` | checkout · basket | – → items, total | off | commerce |
| `commerce.payment.approve` | a payment is approved | kernel/shop/classes/ezpaymentobject.php:`eZPaymentObject::approve`; kernel/shop/classes/ezpaymentcallbackchecker.php:`approvePayment` | approve · payment → order | status → status (never card or account data) | off | commerce |
| `commerce.vat.change` | VAT types or rules change | V/shop/vattype.php, V/shop/vatrules.php, V/shop/editvatrule.php:`run` | change · VAT | rates → rates | on | commerce |
| `commerce.currency.change` | currencies are created, changed or removed | kernel/shop/classes/ezcurrencydata.php:`eZCurrencyData::create`, `store`, `removeCurrencyList` | change · currency | code, rate → code, rate | on | commerce |
| `commerce.discount.change` | discount groups or rules change | V/shop/discountgroupedit.php, V/shop/discountruleedit.php:`run` | change · discount | rule → rule | on | commerce |

### data (channel `commerce`, the family "commerce + data" of Q5)

| Name | Fires when | Where | Actor / verb / object → target | Before → after (never) | Default | Ch. |
|---|---|---|---|---|---|---|
| `data.export.csv` | content is exported as CSV | V/content/subitemsexport.php:`run`; bin/php/ezcsvexport.php | export · subtree | – → node, rows, columns | on **Proposed** (an export takes data out of the system; the owner's default list does not name it) | commerce |
| `data.export.package` | a package is exported | V/package/export.php:`run` | export · package | – → name, sha256 | on | commerce |
| `data.export.pdf` | a PDF export is generated | V/content/pdf.php:`run` | export · node | – | off | commerce |
| `data.import.csv` | CSV is imported | bin/php/ezcsvimport.php | import · file → parent node | – → rows, created | on | commerce |
| `data.import.dba` | a .dba file is imported | bin/php/ezimportdbafile.php | import · file | – → tables, rows | on | commerce |
| `data.import.rss` | an RSS import runs | cronjobs/rssimport.php | import · feed → parent node | – → created | off | commerce |
| `data.infocollection.remove` | collected information is removed | V/infocollector/collectionlist.php:`run` (RemoveCollections), V/infocollector/overview.php:`run` (RemoveObjectCollection) | remove · collection(s) → object | count → – (never the collected values) | on | commerce |
| `data.infocollection.view` | collected information is opened | V/infocollector/view.php:`run` | read · collection | – | off | commerce |
| `data.index.rebuild` | the search index is rebuilt | bin/php/updatesearchindex.php | rebuild · search index | – → objects, ms | on | commerce |

**Count.** 42 content, 32 access (`access.user.enable`/`disable` counted as two), 40 system, 12 commerce (`archive`/
`unarchive` as two), 9 data: **135 events**. The generated event reference (stage 6) is produced from the taxonomy
registry, so it and this table must agree; the coverage matrix test (stage 3) fails for a name in the registry that no
test raises.

**Proposed:** the reads `content.node.view`, `content.search.query` and `content.object.download` go to a fifth
channel `read`, so that switching sampled reads on does not multiply the size of the content chain that has to be
verified and archived. The owner named four channels (Q6); a fifth only exists when reads are switched on.

### Compatibility mapping

`eZAudit::writeAudit( $name, $attributes )` keeps working (Z3) and becomes a call of `expAudit::event()`. The mapping
is `[AuditCompatSettings] Map[<old name>]=<new name>` in audit.ini; the attributes the old call passes are kept under
`after.legacy` (keys as given, `Comment` dropped), except keys on the deny list (`HashKey`, `Hash`, `Password`,
anything `expIniEditor::isSecret()` recognises), which are never recorded.

| Old name (4.x) | Old file | New name | Notes |
|---|---|---|---|
| `user-login` | login.log | `access.session.login` | |
| `user-failed-login` | failed_login.log | `access.session.login.failed` | the ezmbpaex site gets `reason: password_expired` |
| `content-delete` | content_delete.log | `content.node.remove.trash` from `eZContentObjectTreeNode::removeThis`; `content.object.remove` from `eZContentObject::removeThis`; `content.object.purge` from `eZContentObject::purge` | one old name, three new ones: the call site decides (the native instrumentation of stage 3 replaces the compat call there) |
| `content-move` | content_move.log | `content.node.move` | |
| `content-hide` | content_hide.log | `content.node.hide` / `content.node.reveal` | `hideSubTree` vs `unhideSubTree` |
| `role-change` | role_change.log | `access.role.change` | |
| `role-assign` | role_assign.log | `access.role.assign` | |
| `section-assign` | section_assign.log | `content.node.section` | |
| `state-assign` | state_assign.log | `content.object.state` | |
| `order-delete` | order_delete.log | `commerce.order.delete`; `commerce.order.purge` from `eZOrder::cleanup` | |
| `user-password-change` | (none: was dropped) | `access.user.password.change` | object is another user |
| `user-password-change-self` | (none) | `access.user.password.change` | object is the actor |
| `user-password-change-self-fail` | (none) | `access.user.password.change.failed` | |
| `user-forgotpassword` | (none) | `access.user.password.reset.request` (attribute `Email` given) / `access.user.password.reset` (`UserID` given) | the e-mail address follows the privacy rule of `email` |
| `user-forgotpassword-fail` | (none) | `access.user.password.reset.failed` | `HashKey` is dropped (a token) |
| any other name | its `AuditFileNames[]` file | `system.legacy.<name>` (name lower-cased, `-` → `_`) | so an extension's own 4.x audit names keep being recorded; channel `system` |

**Old file names as aliases (Q6).** `AuditFileNames[]` stays readable. A filter by old file name (`file=login.log` in
the console, `exp:audit search --legacy-file=login.log`) resolves through the mapping to the new names. **Proposed:**
writing the old text files as well is possible with `[AuditCompatSettings] LegacyFiles=enabled` (default
`disabled`), for sites whose external tools read them; those files keep the old format and rotation and are outside
the chain.

### The ezpEvent bridge

Kernel events that already exist (`ezpEvent::getInstance()->notify()`, kernel/private/classes/ezpevent.php) can be
recorded without touching their code: `[AuditBridgeSettings] Bridge[<ezpEvent name>]=<audit name>` attaches one
listener per mapped name when `ezpEvent::registerEventListeners()` runs, and for commands and cronjobs through
`[RunnableSettings] Listeners[]` (site.ini). The ezpEvent names available today (grep of kernel and lib):
`content/cache`, `content/cache/all`, `content/cache/version`, `content/class/cache`, `content/class/cache/all`,
`content/class/group/cache`, `content/section/cache`, `content/state/assign`, `content/state/cache`,
`content/state/cache/all`, `content/state/group/cache`, `content/translations/cache`, `content/view` (a filter),
`image/invalidateAliases`, `image/purgeAliases`, `image/removeAliases`, `image/trashAliases`, `request/preinput`,
`request/input`, `response/preoutput`, `response/output` (filters), `session/destroy`, `session/regenerate`,
`user/cache/all`, and `runnable/<command|cronjob|view>/<before|after>` (kernel/private/classes/runnable/runnable.php).
Most are cache events, so the bridge is for extensions and sites; the kernel's own events in the catalogue are raised
directly by stage 3.

## The record format

One record is one line of UTF-8 JSON (JSON lines), ending in `\n`, in the channel's live file. The format has a
version (`v`); a reader skips fields it does not know, and a new field never changes the meaning of an old one.

### Fields

Types: `str`, `int`, `bool`, `obj`, `list`, `null`. "Privacy" says which privacy options the field takes
(`[AuditPrivacySettings] Field[<name>]`, see the settings reference): **full** (as is), **truncate** (a shorter form
defined per field), **hash** (`h:` + the first 16 hex digits of HMAC-SHA-256 with the installation's pseudonym key —
the same input gives the same output within one installation, so it can still be grouped and searched by exact value,
but it cannot be reversed or compared across installations), **off** (the field is left out). A field with "–" is
always written as is.

| Field | Type | Meaning | Privacy (default) | Example |
|---|---|---|---|---|
| `v` | int | record format version | – | `1` |
| `id` | str | event id: a ULID (26 characters, Crockford base 32, sortable by time) | – | `01J9ZK3M7Q8R2T4V6X8Z0B2D4F` |
| `seq` | int | position in this channel file, 1, 2, 3 … with no gaps | – | `1842` |
| `name` | str | the taxonomy name | – | `content.node.move` |
| `channel` | str | the channel the record was written to | – | `content` |
| `time` | str | RFC 3339 UTC with milliseconds, when the event happened (not when it was flushed) | – | `2026-10-02T13:30:01.123Z` |
| `severity` | str | RFC 5424 severity name: `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency`; from the taxonomy registry, raised by the result (`refused` → at least `notice`, `failed` → at least `warning`) | – | `notice` |
| `imported` | bool | only on imported records (Z4); then `source` is set and `prev`/`hash` are absent | – | `true` |
| `source` | obj | imported records: old file, line, sha256 of the old file | – | `{"file":"login.log.1","line":214,"sha256":"…"}` |
| `request.id` | str | request id (F2), also sent as the response header `X-Request-Id`; a command or a cronjob run gets one per invocation | – | `r-01J9ZK3M7Q…` |
| `request.siteaccess` | str | the siteaccess (`eZLog::siteAccessName()`) | – | `admin` |
| `request.method` | str | HTTP method, `CLI` on the command line | – | `POST` |
| `request.url` | str | the path and query string | **truncate**: path only, query values replaced by `…`, and path parameters of the views in `SecretPathViews[]` (`user/activate/<hash>`, `user/forgotpassword/<hash>`, `userpaex/forgotpassword/<hash>`) replaced by `…`; full / hash / off | `/content/action` |
| `request.module` | str | module/view that ran | – | `content/action` |
| `request.engine` | str | `apache` (fpm-fcgi), `velocity`, `frankenphp`, `cli` — from `expDebugBarSummary::engine()` (kernel/classes/debugbar/expdebugbarsummary.php) mapped to these names | – | `velocity` |
| `request.host` | str | the server's host name (not the client's Host header) | full (default) / hash / off | `web1` |
| `request.pid` | int | process id | – | `991876` |
| `request.ms` | int | request duration up to the flush, milliseconds (integer: there are no floats in a record, see Canonical JSON) | – | `184` |
| `request.status` | int | HTTP status of the response; the exit code for a command | – | `302` |
| `actor.user_id` | int\|null | content object id of the user, null when none | – | `14` |
| `actor.login` | str | login name | **full**; hash / off | `editor1` |
| `actor.roles` | list | role ids the user had at the time | – | `[2,5]` |
| `actor.session` | str | the session, never the session id itself | **hash** (only hash or off are allowed: the session id is a token) | `h:5d2e8a0c41f7b3e9` |
| `actor.ip` | str | client address (`eZSys::clientIP()`) | **truncate**: IPv4 to /24, IPv6 to /48 (`IPv4Prefix`, `IPv6Prefix`), written as a network; full / hash / off | `203.0.113.0/24`, `2001:db8:12::/48` |
| `actor.ua` | str | user agent | **truncate**: browser family and major version, and the OS family (`Firefox 131 / Linux`); full / hash / off (Q3: user agent on/off) | `Firefox 131 / Linux` |
| `actor.impersonator` | obj\|null | when a process acts as another user: who really runs it. The content job worker switches to the job's user (expcontentjobworker.php:`switchUser`): then `actor` is the job's user and `impersonator` the process user | as `actor.login` | `{"user_id":null,"login":null,"os_user":"alpha"}` |
| `actor.cli` | obj\|null | command line only: OS user and the command (`eZLog::requestContext()` form), option values that look secret masked | os_user full / hash / off | `{"os_user":"alpha","command":"bin/php/ezcache.php --clear-tag=content"}` |
| `verb` | str | the action | – | `move` |
| `object` | obj | `type`, `id`, and identifying fields (`object_id`, `name`, `class`, `path`, `file`, `block`, `variable` …) | `object.name`: **full** (content names are not personal data in general); `truncate` = 64 characters | `{"type":"node","id":275,"object_id":273,"name":"Workout"}` |
| `target` | obj\|null | as `object` | as `object` | `{"type":"node","id":89}` |
| `before` / `after` | obj\|null | the values that changed, as the catalogue lists them per event; values longer than `MaxValueLength` are cut and get `"…"` appended | `[AuditRecordSettings] BeforeAfter`: **full**; `truncate` (keys only, values replaced by their sha256) / off | `{"parent":2}` → `{"parent":89}` |
| `result` | str | `success`, `refused`, `failed` | – | `success` |
| `reason` | str\|null | a code: `policy`, `limitation`, `token`, `lock`, `validation`, `not_found`, `expired`, `password_expired`, `cancelled`, `error`, or an extension's own | – | `policy` |
| `error` | obj\|null | `failed` only: an error reference and a short message without arguments | – | `{"ref":"ERR-3F9A0C12B4","message":"Transaction rolled back"}` |
| `parent` | str\|null | id of the parent event (F2) | – | `01J9ZK3M5…` |
| `depth` | int | 0 for a top-level event, 1, 2 … for children | – | `1` |
| `job` | str\|null | content job id (`expContentJobStore::newID()`, `Ymd-His-<8 hex>`) | – | `20261002-133001-4f2a9c1e` |
| `run` | str\|null | the cronjob run (the request id of the runcronjobs invocation) a part belongs to | – | `r-01J9ZK…` |
| `x` | obj\|null | an extension's own fields, under its name: `{"myext":{…}}` | an extension declares the privacy of its fields in its branch registration | `{"myext":{"poll":12}}` |
| `prev` | str | hash of the previous record in this channel's chain | – | `sha256:9b1c…` |
| `hash` | str | hash of this record | – | `sha256:41aa…` |

Never recorded, whatever the settings: passwords and password hashes, password hash types, session ids, form tokens,
activation and reset hash keys, the values of settings whose variable `expIniEditor::isSecret()`
(kernel/classes/ini/expinieditor.php) recognises (written as the string `[secret]`), POST bodies, attribute values of
content (only identifiers of changed attributes), payment card or account data, collected information values.

**Pseudonymisation after the retention period (Q3, F6).** In the index, after `PseudonymiseAfterDays` (90), the fields
`actor.login`, `actor.ip`, `actor.ua`, `actor.cli.os_user` and the person-describing parts of `object`/`target`/
`before`/`after` for `access.user.*` events are replaced by their `hash` form. The files are not rewritten (that would
break the chain): they are archived at 90 days, and the archive is the only place the full values remain, for the
archive retention period, readable by audit/manage only. **Proposed:** a site that must not keep full values even in
archives sets the privacy options to `hash` from the start; the console's settings view says this plainly.

### A complete record

```json
{"v":1,"id":"01J9ZK3M7Q8R2T4V6X8Z0B2D4F","seq":1842,"name":"content.node.remove.trash","channel":"content",
 "time":"2026-10-02T13:30:01.123Z","severity":"notice",
 "request":{"id":"r-01J9ZK3M5A0000000000000000","siteaccess":"admin","method":"POST","url":"/content/removeobject",
            "module":"content/removeobject","engine":"velocity","host":"web1","pid":991876,"ms":184,"status":302},
 "actor":{"user_id":14,"login":"editor1","roles":[2],"session":"h:5d2e8a0c41f7b3e9","ip":"203.0.113.0/24",
          "ua":"Firefox 131 / Linux","impersonator":null,"cli":null},
 "verb":"remove","object":{"type":"node","id":275,"object_id":273,"name":"Workout","class":"article"},
 "target":{"type":"trash"},"before":{"parent":89,"path":"/1/2/89/275/"},"after":null,
 "result":"success","reason":null,"error":null,"parent":null,"depth":0,"job":null,"run":null,"x":null,
 "prev":"sha256:9b1c0f…","hash":"sha256:41aa7e…"}
```

(Shown wrapped; on disk it is one line. **Proposed:** fields whose value is `null` are left out on disk to keep lines
short; the canonical form below treats an absent field and a `null` field differently, so the writer always omits
them and the verifier never adds them.)

### Canonical JSON

The hash is computed over the record's canonical form, so it does not depend on how a library orders or escapes:

1. Take the record as written, **without** its `hash` field (`prev` stays in: that is what links the chain).
2. Objects: keys sorted by their UTF-8 bytes, ascending, at every depth. Lists keep their order.
3. No whitespace outside strings; `,` and `:` without spaces.
4. Strings: UTF-8, escaped only where JSON requires (`"`, `\`, control characters as `\u00XX` with lower-case hex,
   except `\b \f \n \r \t`); `/` and non-ASCII characters are **not** escaped (PHP: `JSON_UNESCAPED_SLASHES |
   JSON_UNESCAPED_UNICODE`). Strings are not Unicode-normalised: the bytes given are the bytes hashed.
5. Numbers: integers only, without leading zeros, `-` for negatives. A record never holds a float (durations are
   integer milliseconds, money is a string with its currency), because floats have no single canonical text.
6. `true`, `false`, `null` as literals; an empty object is `{}`, an empty list `[]` (the writer builds objects as
   objects, so PHP's `array()` never turns `{}` into `[]`).

The line on disk **is** the canonical form plus `,"hash":"…"` inserted in sorted position. **Proposed:** the writer
always writes the canonical form, so verification can hash the line's bytes with the `hash` member cut out, without
re-encoding; a line whose bytes are not canonical is reported as `noncanonical` (a sign of editing), which is a break.

### The hash chain (Q4)

- `hash = "sha256:" + hex( SHA-256( canonical(record without hash) ) )`. **Proposed:** SHA-256, as named in Q4's
  example; `[AuditChainSettings] Algorithm` exists so a later release can move to SHA-512/256 or BLAKE2b with the
  prefix telling a verifier which one was used.
- One chain **per channel**, running through all its files in order. `prev` of a record is the `hash` of the record
  before it in the same channel.
- **Appending under concurrency.** PHP-FPM workers, Velocity workers and commands write to the same file. The writer
  opens the file in append mode, takes `flock( LOCK_EX )`, reads the last line from the end of the file (a seek and at
  most `MaxLineBytes` bytes read, not the whole file), takes its `seq` and `hash`, computes `seq`, `prev` and `hash`
  for each buffered record of that channel in order, writes them all with one `fwrite()`, `fflush()`es and releases
  the lock. The chain is therefore taken from the file, never from memory, and two processes cannot interleave.
- **A torn last line** (a process killed mid-write: the file does not end in `\n`, or the last line is not valid
  JSON): the writer appends `\n`, then a `system.audit.chain.repair` record whose `prev` is the last **valid** hash
  and whose `after` gives the byte offset and length of the torn line. Verification shows it as "repaired", not as a
  break: the torn bytes are still in the file to be inspected.
- **Files.** `<LogDir>/<channel>-<YYYY-MM-DD>.jsonl`; when a day's file passes `MaxFileSize` the next part is
  `<channel>-<YYYY-MM-DD>.2.jsonl`, `.3.jsonl` … The date is the UTC date of the record's `time` at the moment of
  writing (**Proposed:** UTC, so files do not shift at daylight saving changes; the console shows local time).
- **Linking files.** The first record of every file is `system.audit.file.open`: `seq` 1, `prev` = the last `hash` of
  the channel's previous file, `after` = `{"previous_file":"content-2026-10-01.jsonl","previous_seq":18211,
  "previous_hash":"sha256:…"}`. When a file is closed by rotation its last record is `system.audit.file.close` with
  `after.records` = its record count. The very first file of a channel starts with `prev` =
  `"sha256:" + hex(SHA-256("exponential-audit:" + installation id + ":" + channel))`, the **genesis** value, so a chain
  cannot be passed off as belonging to another installation or channel. The installation id is a UUID generated with
  the keys (Z7) and kept beside them.
- **Daily checkpoints.** The cronjob part writes `system.audit.checkpoint` to the `system` channel once a day, with
  each channel's current file, last `seq`, last `hash` and an HMAC-SHA-256 over those with the active signing key, and
  hands it to the sinks (syslog, webhook) so that a copy of every chain's head exists outside the server. Without the
  key, nobody can produce a valid checkpoint for a rewritten chain.

### Verification

`expAuditVerifier` (stage 2; used by the console, `exp:audit verify` and the cronjob part) checks one channel:

1. List the channel's files (live and, with `--archives`, the archived ones read through their format handlers) in
   order of date and part number.
2. For each line: parse it (else `unparseable`), check it is canonical (else `noncanonical`), recompute the hash
   (else `altered`), check `prev` equals the previous record's `hash` (else `link`), check `seq` is the previous
   `seq` + 1 (else `gap`, or `reordered` when it is lower).
3. At each file boundary: the first record must be `system.audit.file.open` naming the previous file and its last
   seq and hash (else `missing_file`, when a file in between is gone, or `truncated`, when the previous file's last
   record is not the one named); a file other than the newest without `system.audit.file.close` is reported as
   `unclosed` (a crash, a notice, not a break) unless the next file's open record names a different last hash, which
   is `truncated`.
4. The first file must start from the genesis value or from a file listed in an archive manifest (else `no_origin`).
5. Checkpoints: each `system.audit.checkpoint` is compared with the channel at that `seq`; a different hash there is
   `rewritten` (the strongest finding: someone recomputed the chain after changing it).

The result per channel is **intact** (with the count and the time span), **repaired** (intact apart from
`system.audit.chain.repair` points) or **broken** with the first break: file, line, record id, kind. Verification
continues after a break with the record's own `prev` to find further breaks, so a report lists every damaged
stretch, not only the first.

What a break means: the file was changed after it was written — a record altered, removed, inserted or reordered, a
file removed or cut short — by someone or something with write access to the log directory, or by disk damage. It does
not say who. What the chain proves and does not prove is in "Security notes" (stage 6 text).

### The archive manifest and its HMAC

An archive run writes, per channel and per archived day, the compressed files and one manifest
`<ArchiveDir>/<channel>/<YYYY>/<channel>-<YYYY-MM-DD>.manifest.json`:

```json
{"format":"exponential-audit-manifest","v":1,
 "installation":"6f1c3e0a-2b7d-4c55-9a01-3d2e4f5a6b7c","site":"example","channel":"content",
 "created":"2026-12-31T02:15:00Z","created_by":{"user_id":null,"login":null,"os_user":"alpha","run":"r-01JB…"},
 "handler":"gzip","handler_options":{"level":9},
 "files":[{"name":"content-2026-10-02.jsonl","archive":"content-2026-10-02.jsonl.gz",
           "records":18211,"first_seq":1,"last_seq":18211,
           "first_time":"2026-10-02T00:00:00.412Z","last_time":"2026-10-02T23:59:58.007Z",
           "first_prev":"sha256:…","last_hash":"sha256:…",
           "bytes":9437184,"sha256":"…","archive_bytes":1048576,"archive_sha256":"…"}],
 "verification":{"result":"intact","checked":"2026-12-31T02:14:58Z"},
 "previous_manifest":{"name":"content-2026-10-01.manifest.json","sha256":"…"},
 "key_id":"k1-20261002-3fa94c1b",
 "hmac":"hmac-sha256:…"}
```

- `hmac` = `"hmac-sha256:" + hex( HMAC-SHA-256( key[key_id], canonical(manifest without hmac) ) )`, with the same
  canonical JSON as records.
- Manifests of a channel are chained by `previous_manifest`, so removing a whole archived day is visible too.
- A file is archived only when it verifies (`VerifyBeforeArchive`), and the live file is removed only after the
  archive has been written, read back through its format handler and found to have the same `sha256` as the live file.
- **Keys (Z7).** On the first event, `expAuditKeys` generates a 256-bit signing key and a 256-bit pseudonym key from
  `random_bytes()`, an installation id, and writes them to `settings/override/audit.ini.append.php` (mode 0640, the
  site user's owner and group, like `expIniEditor::save()` keeps them) as `[AuditKeySettings] SigningKey[<key id>]`,
  `ActiveSigningKey`, `PseudonymKey` and `InstallationID`. The variable names are chosen so that
  `expIniEditor::isSecret()` masks them (`SigningKey` and `PseudonymKey` end in `Key` after a lower-case letter), so
  exp:ini, the debug bar and the settings view never show them. `settings/override` is never committed.
- **Key id** = `k<n>-<YYYYMMDD of creation>-<first 8 hex of SHA-256(key)>`; the **fingerprint** shown in the console
  is the first 16 hex digits of SHA-256(key), in groups of four.
- **Rotation.** `exp:audit key rotate` (and the console's settings view, audit/manage) adds `SigningKey[k<n+1>-…]`,
  switches `ActiveSigningKey`, and records `system.audit.key.rotate`. Old keys stay as long as an archive or
  checkpoint signed by them is retained; the retention run refuses to drop a key that is still needed and says which
  archives need it. **Proposed:** the pseudonym key is not rotated (rotating it would make old and new pseudonyms of
  the same person differ); it is replaced only by an explicit `exp:audit key rotate --pseudonym`, which re-pseudonymises
  the index.

## The settings reference: `settings/audit.ini`

The complete file as it will ship. Every variable has its default; allowed values are given in the comment. The
shipped default is **`Audit=enabled`** (Z5, owner 2026-10-02). Sites change it with
`settings/override/audit.ini.append.php` or a siteaccess's `audit.ini.append.php`, as with every INI file; exp:ini and
the debug bar's settings write through `expIniEditor`, and any write to audit.ini is itself the event
`system.audit.setting.write`.

```ini
#?ini charset="utf-8"?
# Audit: what happens in Exponential, recorded as JSON lines per channel, hash chained,
# rotated, archived and signed. Guide: doc/bc/6.0/audit.md
#
# Do not edit this file: override it in settings/override/audit.ini.append.php or in a
# siteaccess's audit.ini.append.php. Writing any variable of this file is itself audited
# (system.audit.setting.write); switching Audit off is recorded before it takes effect.

[AuditSettings]
# enabled | disabled. On in every installation by convention (unlike the 4.x releases).
Audit=enabled
# Where the live channel files are written, relative to the site's var directory
# ([FileSettings] VarDir of site.ini). Created with the site user's owner and group.
LogDir=log/audit
# 4.x compatibility: the old per-event file names. Kept so the old names resolve as
# aliases in filters and so a site's own names keep a home; see [AuditCompatSettings].
AuditFileNames[]
AuditFileNames[user-login]=login.log
AuditFileNames[user-failed-login]=failed_login.log
AuditFileNames[content-delete]=content_delete.log
AuditFileNames[content-move]=content_move.log
AuditFileNames[content-hide]=content_hide.log
AuditFileNames[role-change]=role_change.log
AuditFileNames[role-assign]=role_assign.log
AuditFileNames[section-assign]=section_assign.log
AuditFileNames[state-assign]=state_assign.log
AuditFileNames[order-delete]=order_delete.log
# What to do when the audit cannot write (disk full, permissions):
#   continue  the request goes on; the failure is in error.log and the dashboard warns
#   refuse    actions of the "always written at once" kind (ImmediateEvents[]) are refused
#             with an error page, so nothing security-relevant happens unrecorded
# Proposed default: continue (a full disk must not take the site down).
OnWriteFailure=continue

[AuditEventSettings]
# Which events are recorded, by taxonomy pattern: a whole domain (content.*), a subject
# (content.node.*), an action with its details (content.node.remove.*) or one name.
# The most specific pattern wins; on a tie the later line wins. Events marked "always"
# in the catalogue (system.audit.*, access.view.sensitive) ignore Disabled[].
Enabled[]
Enabled[]=access.*
Enabled[]=system.*
Enabled[]=content.node.move
Enabled[]=content.node.remove.*
Enabled[]=content.node.hide
Enabled[]=content.node.reveal
Enabled[]=content.node.swap
Enabled[]=content.node.section
Enabled[]=content.object.remove
Enabled[]=content.object.purge
Enabled[]=content.object.restore
Enabled[]=content.object.state
Enabled[]=content.object.translation.remove
Enabled[]=content.version.remove
Enabled[]=content.trash.empty
Enabled[]=content.class.create
Enabled[]=content.class.change
Enabled[]=content.class.remove
Enabled[]=content.section.*
Enabled[]=content.state.*
Enabled[]=content.job.*
Enabled[]=commerce.order.delete
Enabled[]=commerce.order.purge
Enabled[]=commerce.order.item.remove
Enabled[]=commerce.vat.*
Enabled[]=commerce.currency.*
Enabled[]=commerce.discount.*
Enabled[]=data.export.csv
Enabled[]=data.export.package
Enabled[]=data.import.csv
Enabled[]=data.import.dba
Enabled[]=data.infocollection.remove
Enabled[]=data.index.rebuild
Disabled[]
Disabled[]=access.session.regenerate
Disabled[]=access.session.expire
# Reads are switched on in [AuditReadSettings], not here.
#
# Extensions register their taxonomy branches: name => the branch class (implements
# expAuditTaxonomyBranch), which lists the names, their severity, channel and the
# privacy of its own fields. Counted by the RAD survey (registry "auditbranches").
#Branches[myext]=myExtAuditBranch
Branches[]
# The severity (RFC 5424 name) below which an event is not recorded at all.
# debug | info | notice | warning | error | critical | alert | emergency
MinSeverity=info

[AuditChannelSettings]
# The channels (Q6): each is one chain, one file per day (and per MaxFileSize part).
Channels[]
Channels[]=content
Channels[]=access
Channels[]=system
Channels[]=commerce
# Proposed: a fifth channel for sampled reads, used only when reads are switched on.
Channels[]=read
# Routing by taxonomy pattern, most specific wins. An event no pattern matches goes to
# DefaultChannel.
Route[]
Route[content.*]=content
Route[content.node.view]=read
Route[content.search.*]=read
Route[content.object.download]=read
Route[access.*]=access
Route[system.*]=system
Route[commerce.*]=commerce
Route[data.*]=commerce
DefaultChannel=system

# One block per channel. Every variable may be left out: the [AuditRotationSettings]
# defaults apply.
[AuditChannel_content]
# Days a file stays live (uncompressed, in LogDir) before it is archived (F6: 90).
LiveDays=90
# Days an archive is kept before retention removes it (F6: 2 years).
ArchiveDays=730
# Size at which a day's file is closed and a new part started; K, M, G suffixes.
MaxFileSize=64M
# The archive format handler (see [AuditArchiveSettings] FormatHandlers[]).
ArchiveFormat=gzip
# Sinks this channel's events are sent to, besides the file (see [AuditSinkSettings]).
Sinks[]

[AuditChannel_access]
LiveDays=90
ArchiveDays=730
MaxFileSize=64M
ArchiveFormat=gzip
Sinks[]
Sinks[]=syslog

[AuditChannel_system]
LiveDays=90
ArchiveDays=730
MaxFileSize=64M
ArchiveFormat=gzip
Sinks[]
Sinks[]=syslog

[AuditChannel_commerce]
LiveDays=90
ArchiveDays=730
MaxFileSize=64M
ArchiveFormat=gzip
Sinks[]

[AuditChannel_read]
# Reads are many and of little value after a while (Proposed).
LiveDays=30
ArchiveDays=90
MaxFileSize=256M
ArchiveFormat=zstd
Sinks[]

[AuditRecordSettings]
# Record before/after values (Q2).  enabled | keys (keys kept, values replaced by their
# sha256) | disabled.  Secrets are never recorded either way.
BeforeAfter=enabled
# Longest value kept in before/after/object fields, in characters; longer ones are cut
# and end in "…".
MaxValueLength=512
# Child events (F2): how many levels below a parent are written one by one, and how many
# children per parent at most. Past either limit the parent counts the rest in
# after.children_omitted.  0 = no children at all.
ChildDepth=3
MaxChildren=10000
# Extra request fields.  enabled | disabled
RequestContext=enabled
# Send the request id as a response header (F2); an empty value sends none.
RequestIdHeader=X-Request-Id
# Accept the request id from a trusted front proxy instead of making one; empty = never.
# The header is only trusted from the addresses in TrustedProxies[] (site.ini's list
# when empty).
TrustedRequestIdHeader=
# Longest line the writer reads back to find the chain's head, in bytes.
MaxLineBytes=262144

[AuditPrivacySettings]
# Per field: full | truncate | hash | off (see "The record format" for what truncate
# means per field). Q3: a safe default; the session can only be hashed or off.
Field[]
Field[actor.login]=full
Field[actor.ip]=truncate
Field[actor.ua]=truncate
Field[actor.session]=hash
Field[actor.cli.os_user]=full
Field[request.url]=truncate
Field[request.host]=full
Field[object.name]=full
# E-mail addresses in access.user.* events and attempted logins of unknown users.
Field[email]=hash
Field[attempted_login]=hash
# Prefix lengths for truncate of addresses: /24 for IPv4, /48 for IPv6 (Q3).
IPv4Prefix=24
IPv6Prefix=48
# Views whose path parameters are tokens: their parameters are never recorded.
SecretPathViews[]
SecretPathViews[]=user/activate
SecretPathViews[]=user/forgotpassword
SecretPathViews[]=userpaex/forgotpassword
# Attribute and parameter names never recorded, besides everything
# expIniEditor::isSecret() recognises.
NeverRecord[]
NeverRecord[]=HashKey
NeverRecord[]=Hash
NeverRecord[]=Password
NeverRecord[]=PasswordConfirm
NeverRecord[]=password_hash
NeverRecord[]=ezxform_token
# Days after which the index replaces personal fields by their hashed form (Q3, F6).
PseudonymiseAfterDays=90

[AuditBufferSettings]
# F3: events are collected and written in one append per channel at the end of the
# request (also on a fatal error, through the shutdown handler).  enabled | disabled
# (disabled writes every event at once: for debugging only).
Buffering=enabled
# Events written at once, not buffered (F3: security events): patterns.
ImmediateEvents[]
ImmediateEvents[]=access.*
ImmediateEvents[]=system.audit.*
ImmediateEvents[]=system.setting.write
# Flush early when the buffer holds this many events or this many bytes (long requests,
# commands). K, M suffixes.
MaxEvents=500
MaxBytes=1M
# Commands, cronjob parts and content job workers also flush every this many seconds.
FlushInterval=5

[AuditChainSettings]
# The hash chain (Q4). Always on: there is no "disabled".
# sha256 (Proposed; the prefix in each hash names the algorithm, so it can change later).
Algorithm=sha256
# A signed checkpoint of every channel's head, written daily by the cronjob part and
# sent to the sinks.  enabled | disabled
Checkpoints=enabled

[AuditKeySettings]
# Generated on first use into settings/override/audit.ini.append.php (Z7); never here,
# never committed. Shown in the console as fingerprints only.
#InstallationID=<uuid>
#ActiveSigningKey=<key id>
#SigningKey[<key id>]=<base64>
#PseudonymKey=<base64>
# Generate the keys on the first event.  enabled | disabled (disabled: an installation
# that provides its own keys, e.g. from a secrets store, through settings/override).
GenerateKeys=enabled

[AuditReadSettings]
# Z6: reads are optional and sampled.  enabled | disabled
Reads=disabled
# Share of reads recorded, 0.0 to 1.0 (0.01 = one in a hundred).
SampleRate=0.01
# Only reads in these sections or of these classes (identifiers); empty = all.
Sections[]
Classes[]
# Views of these modules are always recorded, sampled or not, Reads on or off
# (access.view.sensitive).
AlwaysModules[]
AlwaysModules[]=setup
AlwaysModules[]=role
AlwaysModules[]=user
AlwaysModules[]=audit
# Proposed: settings writes go through setup and settings; the settings module's views
# are sensitive too.
AlwaysModules[]=settings

[AuditSinkSettings]
# Sinks besides the file (which is always written). name => class implementing
# expAuditSink. Extensions add their own (RAD survey registry "auditsinks").
SinkClasses[]
SinkClasses[syslog]=expAuditSyslogSink
SinkClasses[webhook]=expAuditWebhookSink
SinkClasses[mail]=expAuditMailSink
# Undeliverable batches wait here and are retried by the cronjob part.
SpoolDir=log/audit/spool

[AuditSink_syslog]
# RFC 5424 with structured data (stage 5).
# local (the system's syslog socket, journald reads it) | udp | tcp | tls
Transport=local
Host=
Port=514
# Facility: auth | authpriv | daemon | local0 … local7
Facility=authpriv
AppName=exponential
# Which events: patterns; empty = everything routed to this sink.
Events[]
MinSeverity=info

[AuditSink_webhook]
# Off until a URL is set.
URL=
# HMAC-SHA-256 of the body with this secret, sent as X-Exponential-Signature.
# Put it in settings/override (it is masked as a secret).
SigningSecret=
BatchSize=100
BatchSeconds=10
Timeout=5
Retries=5
# Seconds before the first retry; doubled each time.
RetryBackoff=30
Events[]
MinSeverity=notice

[AuditSink_mail]
# E-mail on critical events (Q7). Empty Receivers[] = the site's AdminEmail.
Receivers[]
MinSeverity=critical
Events[]
Events[]=system.audit.*
Events[]=access.role.assign
# At most one mail per rule and group within this many seconds.
Throttle=900

[AuditAlertSettings]
# F5: built-in rules, INI rules ([AlertRule_*]) and rule classes.  enabled | disabled
Alerts=enabled
# Where rules are evaluated: flush (at the end of the request that wrote the event) and
# cronjob (the audit cronjob part, for windows and rules over many requests).
EvaluateIn[]
EvaluateIn[]=flush
EvaluateIn[]=cronjob
# The rules in use: [AlertRule_<name>] blocks.
Rules[]
Rules[]=brute_force
Rules[]=brute_force_user
Rules[]=admin_role_granted
Rules[]=settings_out_of_hours
Rules[]=mass_delete
Rules[]=audit_disabled
Rules[]=chain_broken
# Rule classes: name => class implementing expAuditAlertRule (RAD survey registry
# "auditalertrules").
RuleClasses[]
RuleClasses[threshold]=expAuditThresholdRule
RuleClasses[match]=expAuditMatchRule
RuleClasses[schedule]=expAuditScheduleRule
# Business hours for rules with OutOfHours=enabled: days 1 (Mon) … 7 (Sun), hours 0-24,
# in the site's time zone.
BusinessDays=1-5
BusinessHours=7-19

# An INI rule: Event (pattern), Threshold (events), Window (seconds), GroupBy (record
# field), Severity, Sinks[], Class (default threshold). Each fires system.audit.alert.

[AlertRule_brute_force]
# Many failed logins from one network.
Class=threshold
Event=access.session.login.failed
Threshold=20
Window=300
GroupBy=actor.ip
Severity=critical
Sinks[]=syslog
Sinks[]=mail

[AlertRule_brute_force_user]
# Many failed logins for one account, from anywhere.
Class=threshold
Event=access.session.login.failed
Threshold=10
Window=900
GroupBy=object.id
Severity=alert
Sinks[]=syslog
Sinks[]=mail

[AlertRule_admin_role_granted]
# A role holding these policies is assigned to anyone.
Class=match
Event=access.role.assign
# Match when the role grants any of these module/function pairs.
Policies[]
Policies[]=*/*
Policies[]=setup/*
Policies[]=role/*
Policies[]=audit/manage
Severity=critical
Sinks[]=syslog
Sinks[]=mail

[AlertRule_settings_out_of_hours]
Class=schedule
Event=system.setting.write
OutOfHours=enabled
Severity=warning
Sinks[]=syslog

[AlertRule_mass_delete]
# Many nodes removed by one user in a short time (children are counted).
Class=threshold
Event=content.node.remove.*
Threshold=500
Window=600
GroupBy=actor.user_id
CountChildren=enabled
Severity=critical
Sinks[]=syslog
Sinks[]=mail

[AlertRule_audit_disabled]
Class=match
Event=system.audit.disable
Severity=emergency
Sinks[]=syslog
Sinks[]=mail

[AlertRule_chain_broken]
Class=match
Event=system.audit.chain.broken
Severity=alert
Sinks[]=syslog
Sinks[]=mail

[AuditRotationSettings]
# Q8: by day and by size, compressed archives, retention per channel; run by the audit
# cronjob part ([CronjobPart-audit] in cronjob.ini), from the console and by exp:audit.
# Defaults for the [AuditChannel_*] blocks:
LiveDays=90
ArchiveDays=730
MaxFileSize=64M
ArchiveFormat=gzip
# Verify a file's chain before it is archived; a broken file is archived all the same
# (removing evidence is worse) but its manifest says broken and system.audit.chain.broken
# is written.  enabled | disabled
VerifyBeforeArchive=enabled
# Read every archive back and compare its sha256 before the live file is removed.
VerifyAfterArchive=enabled
# The daily tasks (rotation of the previous day, archiving, retention, checkpoint) run on
# the first cronjob run after this time of day (HH:MM, the site's time zone).
RotateAfter=00:15

[AuditArchiveSettings]
# Where archives go: absolute, or relative to the site's var directory. Best on another
# file system or a mount the web server cannot write (see the security notes).
ArchiveDir=log/audit/archive
# Format handlers: name => class implementing expAuditFormatHandler (RAD survey registry
# "auditformats"). A handler whose PHP extension or binary is missing reports itself
# unavailable and the archive run falls back to gzip.
FormatHandlers[]
FormatHandlers[gzip]=expAuditGzipFormat
FormatHandlers[bzip2]=expAuditBzip2Format
FormatHandlers[xz]=expAuditXzFormat
FormatHandlers[zstd]=expAuditZstdFormat
FormatHandlers[zip]=expAuditZipFormat
# Compression level per handler (each handler's own range).
Level[gzip]=9
Level[bzip2]=9
Level[xz]=6
Level[zstd]=19
Level[zip]=9
# Mode of archive files and directories.
FileMode=0440
DirMode=0750

[AuditIndexSettings]
# F4: an index in the site's main database for the console.  enabled | disabled
# (disabled: the console reads the files, slower, no charts).
Index=enabled
# Rows indexed per run of the incremental indexer.
BatchSize=2000
# Index the read channel too.  enabled | disabled
IndexReads=disabled
# Full-text search over names, object names and before/after values (per engine; see
# "The index").  enabled | disabled
FullText=enabled
# Rows older than this are removed from the index (they stay in the archives).
# Proposed: as long as the archives, so the console can search the whole period.
KeepDays=730

[AuditConsoleSettings]
# Rows per page in the timeline.
PageSize=50
# Q9: ask for the password again before audit/manage actions.  enabled | disabled
ReauthForManage=disabled
# Minutes a re-entered password stays valid.
ReauthMinutes=10
# Largest export, in records; larger exports run as a content-job-like background task.
MaxExportRecords=100000

[AuditCompatSettings]
# eZAudit::writeAudit( <old name> ) is recorded as the new name (Z3).
Map[]
Map[user-login]=access.session.login
Map[user-failed-login]=access.session.login.failed
Map[content-delete]=content.node.remove.trash
Map[content-move]=content.node.move
Map[content-hide]=content.node.hide
Map[role-change]=access.role.change
Map[role-assign]=access.role.assign
Map[section-assign]=content.node.section
Map[state-assign]=content.object.state
Map[order-delete]=commerce.order.delete
Map[user-password-change]=access.user.password.change
Map[user-password-change-self]=access.user.password.change
Map[user-password-change-self-fail]=access.user.password.change.failed
Map[user-forgotpassword]=access.user.password.reset
Map[user-forgotpassword-fail]=access.user.password.reset.failed
# Old names without a Map[] entry: recorded as system.legacy.<name>.  enabled | disabled
UnmappedAsLegacy=enabled
# Also write the old text files (old format, outside the chain).  enabled | disabled
LegacyFiles=disabled

[AuditBridgeSettings]
# ezpEvent bridge (Z3): record an existing kernel event as an audit event.
# Bridge[<ezpEvent name>]=<audit name>
Bridge[]
#Bridge[content/state/assign]=content.object.state
#Bridge[session/regenerate]=access.session.regenerate
```

The `[CronjobPart-audit]` that runs rotation, archiving, retention, checkpoints, the incremental index and the
cronjob-side alert rules is added to `settings/cronjob.ini` (stage 5): `Scripts[]=audit.php`, the part class
`Exponential\Cronjob\Kernel\Audit` in `kernel/private/classes/cronjobs/audit.php` (doc/bc/6.0/cli_cronjob_view_abstractions.md).
**Proposed:** it also joins `[CronjobPart-frequent]`, so an installation that only runs the `frequent` part (as the
installer sets up) gets rotation without an extra crontab line; each task inside it keeps its own schedule
(index every run, rotation and checkpoint once a day after `RotateAfter=00:15`).

## The developer API (Z3)

The classes live in `kernel/classes/audit/` (`expAudit`, `expAuditBuffer`, `expAuditWriter`, `expAuditVerifier`,
`expAuditKeys`, `expAuditPrivacy`, `expAuditTaxonomy`, the registries) and, for the module, command and cronjob part,
in the runnable classes of doc/bc/6.0/cli_cronjob_view_abstractions.md. Every example below is a specification:
each is marked with the stage whose tests prove it, and the developer guide (stage 6) only keeps examples that ran.

### `expAudit::event()`

```php
/**
 * Records one event. Returns its id, or null when the event is not recorded (audit off, the name switched
 * off, below MinSeverity, not sampled). Never throws: a failure to record is reported to error.log and as
 * system.audit.overflow / the dashboard warning, and the caller's work goes on (OnWriteFailure=continue).
 *
 * @param string $name  a taxonomy name, e.g. 'content.node.move'
 * @param array  $data  object, target, before, after, result ('success'), reason, error, severity, parent,
 *                      x (the extension's own fields), actor (only to name an actor other than the current
 *                      user: a command acting for a user, a worker acting for a job's owner)
 * @return string|null  the event id (a ULID)
 */
public static function event( $name, array $data = array() )
```

```php
// kernel/classes/ezcontentobjecttreenode.php, eZContentObjectTreeNode::move() (stage 3)
expAudit::event( 'content.node.move', array(
    'object' => array( 'type' => 'node', 'id' => $nodeID, 'object_id' => $objectID, 'name' => $name ),
    'target' => array( 'type' => 'node', 'id' => $newParentNodeID ),
    'before' => array( 'parent' => $oldParentNodeID ),
    'after'  => array( 'parent' => $newParentNodeID ),
) );
```

A refusal is the same name with a result:

```php
expAudit::event( 'access.permission.refused', array(
    'object' => array( 'type' => 'view', 'id' => 'setup/cache' ),
    'result' => 'refused', 'reason' => 'policy',
    'after'  => array( 'policy' => 'setup/managecache' ),
) );
```

To be proven in stage 2 (core: the record, privacy, buffer, chain) and stage 3 (each kernel call site).

**Cheap when off.** `event()` first looks the name up in the compiled routing table (one array lookup, built once
per settings state, see Performance); a name that is off returns `null` before any record is built. Callers that need
work to build `before`/`after` (a role's policy list) ask first: `if ( expAudit::isOn( 'access.role.change' ) ) …`.

### Parent and child events (F2)

```php
$parent = expAudit::begin( 'content.node.remove', array(
    'object' => array( 'type' => 'node', 'id' => 89, 'name' => 'Archive 2019' ) ) );
foreach ( $subtree as $node )
{
    expAudit::event( 'content.node.remove', array( 'parent' => $parent,
        'object' => array( 'type' => 'node', 'id' => $node->attribute( 'node_id' ) ) ) );
}
expAudit::end( $parent, array( 'after' => array( 'removed' => count( $subtree ) ) ) );
```

- `begin()` returns the id at once (children can name it) and keeps the parent open; `end()` completes it. The parent
  record is written when it ends, after its children, so its `after` can hold totals and `children_omitted`.
- A child's `depth` is its parent's + 1; past `ChildDepth` or `MaxChildren` `event()` returns `null` and counts the
  child on the parent.
- A parent still open when the request ends (an exception) is written by the flush with `result: failed`, `reason:
  error`. A parent's children may be flushed before it (`FlushInterval` in a long command): readers join by `parent`,
  not by order.
- `expAudit::withParent( $id, function () { … } )` runs code with `$id` as the implicit parent of every event inside,
  for code that does not pass ids (the content job batches: `expContentJobWorker::runLocked()` wraps each batch).
- `expAudit::setJob( $jobID )` / `setRun( $runID )` set `job` and `run` on every following event of this process
  until reset; the content job worker calls `setJob()` before its first batch.

To be proven in stage 2 (unit) and stage 3 (a 400-node subtree remove through the content job, on Apache, Velocity
and CLI).

### Buffering and flushing (F3)

- `expAuditBuffer` holds the records of this request per channel. `ImmediateEvents[]` (access.*, system.audit.*,
  settings writes) skip it: they are appended at once, in their own `flock()`ed write.
- **Normal end of a web request**: `expAudit::flush()` is registered with `eZExecution::addCleanupHandler()`
  (lib/ezutils/classes/ezexecution.php), which `ezpKernelWeb::shutdown()` runs through `eZExecution::cleanup()`
  (kernel/private/classes/ezpkernelweb.php). By then the status code and duration are known; the flush fills
  `request.status` and `request.ms` into the request's records.
- **Fatal errors**: `eZExecution::uncleanShutdownHandler()` runs on an unclean end; the audit registers
  `expAudit::flushOnFatal()` with `eZExecution::addFatalErrorHandler()`, which records `system.error.fatal` and
  flushes the buffer. As a last resort, `expAudit` registers one `register_shutdown_function()` per process, as
  `eZDebug::setLogContext()` does for its repeat counts (lib/ezutils/classes/ezdebug.php) — under PHP-FPM and the CLI
  that runs at the end of each request or command.
- **Velocity's persistent workers**: shutdown functions only run when a worker exits, so the cleanup handler is what
  flushes each request. Per-request state is reset at the start of the next request in `ezpKernelWeb::__construct()`,
  next to `eZDBQueryCache::resetRequest()` (the pattern already there, kernel/private/classes/ezpkernelweb.php):
  `expAudit::resetRequest()` first flushes anything left (a request that ended without cleanup), then clears the
  buffer, open parents, request id, job and run, and the cached actor. The compiled routing table is kept across
  requests and rebuilt when the INI cache changes. The `$GLOBALS['eZAuditEnabled']` and
  `$GLOBALS['eZAuditNameSettings']` caches of `eZAudit` are removed in stage 2 (they are the Velocity defect noted in
  "What exists today").
- **Commands, cronjob parts, content job workers** flush on `MaxEvents`/`MaxBytes`, every `FlushInterval` seconds
  (checked at each `event()`), at `eZScript::shutdown()` (kernel/classes/ezscript.php, which runs the cleanup
  handlers) and by the shutdown function.
- **Size limits**: past `MaxEvents` or `MaxBytes` the buffer flushes early; if the flush fails, the records are kept
  for one more try at the end and then written to error.log as one line each (never lost silently), and
  `system.audit.overflow` is recorded when writing works again.

To be proven in stage 2: a request that throws, a request that calls `exit` in a view, `eZExecution::cleanExit()`, a
fatal error (`E_ERROR` from a test view), 1000 Velocity requests in one worker with the buffer empty at each start.

### The `eZAudit::writeAudit()` compatibility path

`eZAudit::writeAudit( $name, $attributes )` keeps its signature and its `bool` return (true when recorded). It
becomes:

```php
static function writeAudit( $auditName, $auditAttributes = array() )
{
    return expAudit::legacy( $auditName, $auditAttributes ) !== null;
}
```

`expAudit::legacy()` maps the name through `[AuditCompatSettings] Map[]` (or `system.legacy.<name>`), drops the
`NeverRecord[]` and secret keys, puts the rest in `after.legacy`, and calls `event()`. `eZAudit::isAuditEnabled()`
returns `expAudit::isEnabled()`; `fetchAuditNameSettings()` and `auditNameSettings()` keep returning the
`AuditFileNames[]` map for code that reads them. Stage 3 replaces the kernel's own `writeAudit()` calls by native
`event()` calls with full fields; extensions (ezmbpaex, nxc_powercontent) keep working unchanged. To be proven in
stage 2: each of the 15 old names of "Compatibility mapping" called through `writeAudit()` gives the mapped name, and
`HashKey` never reaches a file.

### The ezpEvent bridge

```ini
# extension/myext/settings/audit.ini.append.php
[AuditBridgeSettings]
Bridge[content/state/assign]=content.object.state
Bridge[myext/vote]=content.myext_poll.vote
```

For each entry `expAudit` attaches a listener to `ezpEvent` when `ezpEvent::registerEventListeners()` runs (the
listener is kept in `globalListenerIds`, so Velocity does not attach it twice — the fix already in
kernel/private/classes/ezpevent.php). The listener records the audit name with `after.args` = the event's arguments
made scalar (ids kept, objects as `class#id`), and for a filter event passes the value through unchanged. Mapping a
`runnable/*/after` event records every command, cronjob or view run. To be proven in stage 3 with
`content/state/assign` and a test extension's event.

### Template operator and fetch

```smarty
{* the audit trail of a node, in the admin's node view (stage 4) *}
{def $trail = fetch( 'audit', 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ),
                                             'limit', 10 ) )}
{foreach $trail as $e}
    {$e.time|l10n( 'shortdatetime' )} {$e.actor.login|wash} {$e.name|audit_label|wash}
{/foreach}
```

- Fetch functions of the `audit` module (kernel/audit/function_definition.php): `events` (filters as the console's
  URL parameters, `limit`, `offset`), `event` (`id`), `count`, `chain_status` (`channel`). Each checks `audit/read`
  with its Channel limitation for the current user and returns an empty list (not an error) when not allowed, so a
  template cannot leak events to a user without the policy. A use is recorded as `system.audit.read` once per request
  and fetch name (Proposed: not once per call, which would flood the system channel from a list template).
- Operator `audit_label`: the human label of a name from the taxonomy registry (`content.node.move` → "Node moved"),
  translated through `ezpI18n` context `kernel/audit`.

To be proven in stage 4: the permission matrix runs the fetch as each role.

### Extension interfaces and their registries

Every registry is an INI variable that names classes, so the RAD survey counts it: four descriptors are added to
`expRadSurvey::registryDescriptors()` (kernel/setup/expradsurvey.php), which then reports each entry, the ones a
site or extension added, and the broken ones (a class that does not exist or does not implement the interface,
`expRadSurvey::registryProblem()`):

```php
'auditbranches'   => array( 'title' => 'Audit taxonomy branches', 'ini' => 'audit.ini',
                            'section' => 'AuditEventSettings', 'variables' => array( 'Branches' => 'expAuditTaxonomyBranch' ) ),
'auditsinks'      => array( 'title' => 'Audit sinks', 'ini' => 'audit.ini',
                            'section' => 'AuditSinkSettings', 'variables' => array( 'SinkClasses' => 'expAuditSink' ) ),
'auditalertrules' => array( 'title' => 'Audit alert rule classes', 'ini' => 'audit.ini',
                            'section' => 'AuditAlertSettings', 'variables' => array( 'RuleClasses' => 'expAuditAlertRule' ) ),
'auditformats'    => array( 'title' => 'Audit archive formats', 'ini' => 'audit.ini',
                            'section' => 'AuditArchiveSettings', 'variables' => array( 'FormatHandlers' => 'expAuditFormatHandler' ) ),
```

```php
interface expAuditTaxonomyBranch
{
    /** @return array name => array( 'label' => …, 'severity' => 'info', 'channel' => null|'content',
     *                                'default' => 'on'|'off', 'privacy' => array( field => default ) ) */
    public function events();
}

interface expAuditSink
{
    public function name();
    /** '' when it can work here, else why not (missing extension, no URL) — shown in the console */
    public function problem();
    /** @param array[] $records records already through the privacy rules; @return int delivered count */
    public function deliver( array $records );
}

interface expAuditAlertRule
{
    /** @param array $config the [AlertRule_*] block; @param array[] $records new records to look at
     *  @return array[] alerts: array( 'group' => …, 'count' => …, 'events' => ids, 'message' => … ) */
    public function evaluate( array $config, array $records, expAuditAlertState $state );
}

interface expAuditFormatHandler
{
    public function name();
    /** '' when available (PHP extension or binary present), else why not */
    public function problem();
    public function extension();                                  // '.gz'
    public function compress( $source, $target, $level );          // false on failure
    /** @return resource a stream reading the plain lines (for verify, reindex, restore) */
    public function open( $archive );
}
```

A worked example, to be proven in stage 2 (branch) and stage 5 (sink):

```php
// extension/myext/classes/myextauditbranch.php
class myExtAuditBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array( 'content.myext_poll.vote'  => array( 'label' => 'Poll vote', 'severity' => 'info', 'default' => 'off' ),
                      'content.myext_poll.close' => array( 'label' => 'Poll closed', 'severity' => 'notice', 'default' => 'on' ) );
    }
}
// extension/myext/settings/audit.ini.append.php:  [AuditEventSettings]  Branches[myext]=myExtAuditBranch
// extension/myext/modules/poll/close.php:        expAudit::event( 'content.myext_poll.close', array( 'object' => array( 'type' => 'poll', 'id' => $pollID ) ) );
```

## The index (F4, Z1)

The files are the record; the index is a copy in the site's main database for the console, rebuilt from the files at
any time. Nothing is written to the database at request time (Performance).

### Tables

`expaudit_event` (one row per record), `expaudit_cursor` (how far each live file has been indexed) and
`expaudit_file` (each file's verification state). Names are at most 30 characters for Oracle.

| Column | Meaning | MySQL/MariaDB | PostgreSQL | SQLite | Oracle | MongoDB (expMongoDB) |
|---|---|---|---|---|---|---|
| `id` | event id (ULID), primary key | `CHAR(26)` | `CHAR(26)` | `TEXT` | `CHAR(26)` | string |
| `channel` | channel | `VARCHAR(32)` | `VARCHAR(32)` | `TEXT` | `VARCHAR2(32)` | string |
| `seq` | position in the file | `INT` | `INTEGER` | `INTEGER` | `NUMBER(10)` | int |
| `file_name` | channel file | `VARCHAR(64)` | `VARCHAR(64)` | `TEXT` | `VARCHAR2(64)` | string |
| `name` | taxonomy name | `VARCHAR(128)` | `VARCHAR(128)` | `TEXT` | `VARCHAR2(128)` | string |
| `domain_name` | first rank | `VARCHAR(16)` | same | `TEXT` | `VARCHAR2(16)` | string |
| `severity` | 0 (emergency) … 7 (debug) | `TINYINT` | `SMALLINT` | `INTEGER` | `NUMBER(1)` | int |
| `time_ms` | epoch milliseconds | `BIGINT` | `BIGINT` | `INTEGER` | `NUMBER(15)` | int |
| `request_id` | | `VARCHAR(40)` | same | `TEXT` | `VARCHAR2(40)` | string |
| `siteaccess`, `engine`, `module_view` | | `VARCHAR(64)`, `(16)`, `(128)` | same | `TEXT` | `VARCHAR2` | string |
| `user_id` | | `INT NULL` | `INTEGER NULL` | `INTEGER` | `NUMBER(10)` | int |
| `login`, `ip`, `session_h`, `ua` | after the privacy rules; hashed after 90 days | `VARCHAR(150)`, `(64)`, `(24)`, `(128)` | same | `TEXT` | `VARCHAR2` | string |
| `verb`, `object_type`, `object_id`, `object_name` | | `VARCHAR(32)`, `(32)`, `(64)`, `(255)` | same | `TEXT` | `VARCHAR2` | string |
| `target_type`, `target_id` | | `VARCHAR(32)`, `(64)` | same | `TEXT` | `VARCHAR2` | string |
| `result`, `reason` | | `VARCHAR(8)`, `(32)` | same | `TEXT` | `VARCHAR2` | string |
| `parent_id`, `depth`, `job_id`, `run_id` | | `CHAR(26)`, `TINYINT`, `VARCHAR(32)`, `(40)` | same | `TEXT`/`INTEGER` | `CHAR`/`NUMBER`/`VARCHAR2` | string/int |
| `imported`, `pseudonymised` | 0/1 | `TINYINT` | `SMALLINT` | `INTEGER` | `NUMBER(1)` | int |
| `record` | the record as indexed (privacy applied) | `MEDIUMTEXT` | `TEXT` | `TEXT` | `CLOB` | string |
| `search_text` | name, object/target names, before/after values, lower-cased, for search | `TEXT` | `TEXT` + `tsvector` column | (FTS5 table) | `CLOB` | string |

Indexes (the same on every engine; MongoDB gets them through `CREATE TABLE … INDEX`, which `expMongoDB` turns into
`createIndex()` calls — lib/ezdb/classes/expmongodb.php, the `CREATE TABLE` branch of `query()`):
primary key `id`; unique `(channel, file_name, seq)`; `(time_ms)`; `(name, time_ms)`; `(user_id, time_ms)`;
`(object_type, object_id, time_ms)`; `(request_id)`; `(job_id)`; `(parent_id)`; `(ip, time_ms)`;
`(result, time_ms)`; `(domain_name, severity, time_ms)`.

`expaudit_cursor`: `channel`, `file_name` (primary key together), `byte_offset`, `last_seq`, `last_hash`,
`updated_ms`. `expaudit_file`: `channel`, `file_name`, `state` (`live`, `archived`, `purged`), `records`,
`verified` (`intact`, `repaired`, `broken`, `unchecked`), `verified_ms`, `break_line`, `archive_path`.

The schema ships the way the kernel's tables do: `share/db_schema.dba` (from which the installer and `eZDbSchema`
generate each engine's SQL) and `kernel/sql/{mysql,postgresql,sqlite}/` files; Oracle through
`extension/ezoracle/ezdb/dbms-schema/ezoracleschema.php`; MongoDB through the `CREATE TABLE` emulation. An upgrade
script creates the tables on existing installations (stage 4).

### Full-text search per engine

| Engine | Approach |
|---|---|
| MySQL / MariaDB | `FULLTEXT(search_text)` on InnoDB (MySQL 5.6+, MariaDB 10.0.5+), `MATCH … AGAINST` in boolean mode |
| PostgreSQL | a generated `tsvector` column (`to_tsvector('simple', search_text)`, PostgreSQL 12+) with a GIN index; `plainto_tsquery('simple', …)`. `simple`, not a language: names and ids must not be stemmed |
| SQLite | an FTS5 table `expaudit_event_fts(search_text)` with `content='expaudit_event'`, filled by the indexer; when the SQLite build lacks FTS5, `LIKE` on `search_text` |
| Oracle | an Oracle Text `CONTEXT` index on `search_text` with `SYNC (ON COMMIT)` when the schema has the `CTXAPP` role; else `LIKE` on `search_text` (`DBMS_LOB.INSTR`) |
| MongoDB | `expMongoDB` emulates `LIKE` as a regular expression (`expMongoDB` turns `LIKE '%…%'` into a regex); search uses that, always with a time range so it scans the `time_ms` index range, not the collection. **Proposed:** a native text index is out of scope while the driver is an SQL emulation |

Without full text (`FullText=disabled`), search is `LIKE` on `search_text` within the time range of the filter.

### Size

A record is about 0.9 KiB on disk; an index row with its indexes about 1.2 KiB (measured figures replace these in
stage 4). Retention keeps index rows for `KeepDays` (730).

| Events per day | Rows after 2 years | Index size | Live files (90 days) | Archives (2 years, gzip about 1:8) |
|---|---|---|---|---|
| 1 000 (a small site, defaults) | 0.73 M | about 0.9 GiB | about 80 MiB | about 80 MiB |
| 10 000 (an editorial site) | 7.3 M | about 9 GiB | about 0.8 GiB | about 0.8 GiB |
| 100 000 (reads sampled on) | 73 M | about 90 GiB — **Proposed:** `IndexReads=disabled` by default, so reads stay in the files | 8 GiB | 8 GiB |

### Incremental indexing

The audit cronjob part (and `exp:audit reindex --incremental`, and the console when it opens) reads each live file
from its cursor's `byte_offset`, parses whole lines only (a line without `\n` waits for the next run), checks each
record's `prev` against the cursor's `last_hash` (a mismatch marks the file `broken` in `expaudit_file` and records
`system.audit.chain.broken`), inserts the rows in batches of `BatchSize` and moves the cursor **in the same
transaction**, so a crash never indexes a line twice or skips one. The console shows the newest events, which may
not be indexed yet, by merging the tail of the live files after the cursor (a few KiB) into the first page.

### Rebuild

`exp:audit reindex` (audit/manage, also in the console's settings view) empties the three tables and indexes the live
files, and with `--archives` the archives within `KeepDays`, through their format handlers. It runs as a background
task with progress, like a content job. Rows of imported records come from the `imported/` files.

### Pseudonymisation after 90 days

Once a day the cronjob part updates, in batches, the rows with `time_ms` older than `PseudonymiseAfterDays` and
`pseudonymised = 0`: `login`, `ip`, `ua`, `session_h` and the person fields inside `record` and `search_text` are
replaced by their `hash` form (HMAC with the pseudonym key, the same function as the privacy option), and
`pseudonymised = 1`. Grouping and counting still work (the same person gives the same pseudonym); the name is gone.
The run is recorded as `system.audit.pseudonymise`. A reindex applies the same rule to old records as it reads them.

## The console: module `audit` (Q10, Z2)

A kernel module `kernel/audit/` (`module.php`, `function_definition.php`), its views as classes
`Exponential\View\Kernel\Audit\<View>` in `kernel/private/classes/views/audit/` (doc/bc/6.0/cli_cronjob_view_abstractions.md),
templates in `design/admin4/templates/audit/` (and the admin designs that are still shipped), navigation part
`expauditnavigationpart`.

### Policies (Q9)

| Function | Grants | Limitation |
|---|---|---|
| `audit/read` | console, event, charts, alerts (view), export, the fetch functions, `exp:audit tail/search/show` as a user | **Proposed:** `Channel` (content, access, system, commerce, read), so a shop manager can be given commerce only |
| `audit/manage` | archives (archive now, verify, restore), settings (write audit.ini, keys), alerts (acknowledge, rules), reindex, retention | none |

Neither is in any role of a new installation except Administrator (which has `*/*`). Every view records
`system.audit.read` (or the manage event it performs); with `ReauthForManage=enabled` every manage action first asks
for the password (`access.session.reauth`).

### Views

| View | URL | Shows | Policy |
|---|---|---|---|
| console | `audit/console` | the timeline: newest first, filters, search, page by page; chain state per channel at the top | audit/read |
| event | `audit/event/<id>` | one record in full: fields, before/after side by side, parent and children, the other events of the same request, job and run; links to the node, object, user, role, job; the chain position (prev, hash, intact or not) | audit/read |
| charts | `audit/charts` | events per day per channel, refusals and failures per day, top actors, top names, logins vs failed logins; the filter of the console applies | audit/read |
| alerts | `audit/alerts` | fired alerts (open, acknowledged), the rules and their state; acknowledge (manage) | audit/read, acknowledge: audit/manage |
| export | `audit/export` | the current filter as JSON lines, CSV or a signed bundle (JSON lines + manifest + HMAC); large exports in the background | audit/read |
| archives | `audit/archives` | per channel: live files, archives, manifests, verification state and date; verify now, archive now, restore for reading | audit/manage |
| settings | `audit/settings` | the effective audit.ini with origin per value (as the debug bar shows settings), key fingerprints, sinks with their problem(), format handlers available, index state; writes through `expIniEditor` | audit/manage |
| reauth | `audit/reauth` | password re-entry before a manage action | audit/read |

URL parameters of console, charts and export (ordered parameters, the kernel's `(name)/value` form):
`(channel)/<name>`, `(name)/<pattern>` (`access.session.*`), `(user)/<user id>`, `(login)/<login>`,
`(object)/<type>:<id>` (`node:275`), `(target)/<type>:<id>`, `(result)/success|refused|failed`,
`(severity)/<min>`, `(request)/<request id>`, `(job)/<job id>`, `(run)/<run id>`, `(ip)/<network>`,
`(from)/<YYYY-MM-DD[THH:MM]>`, `(to)/…`, `(q)/<search text>`, `(legacy_file)/<old file name>`, `(offset)/<n>`,
`(limit)/<n>` (at most 500).

```
+--------------------------------------------------------------------------------------------------+
| Dashboard  Content  Media  Users  Shop  Design  Setup  [Audit]                                   |
+---------------------+----------------------------------------------------------------------------+
| Audit               |  Audit console                          content: intact  access: intact   |
|  > Console          |                                         system: intact   commerce: intact |
|    Charts           |  [Channel: all v] [Name: ________] [User: ______] [Result: all v]        |
|    Alerts (2)       |  [From: 2026-10-01] [To: 2026-10-02] [Search: ______________] [Filter]   |
|    Export           |  -----------------------------------------------------------------------  |
|    Archives  (M)    |  Time      Name                      Actor     Object            Result   |
|    Settings  (M)    |  13:30:01  content.node.remove.trash editor1   node 275 Workout  success  |
|                     |  13:29:44  access.permission.refused editor2   setup/cache       refused  |
|                     |   +- 13:12 content.job.finish        editor1   job 20261002-…    success  |
|                     |  13:02:10  access.session.login      editor1   user 14           success  |
|                     |  [< newer]                                   page 1 of 12  [older >]    |
+---------------------+----------------------------------------------------------------------------+
  (M) = shown only with audit/manage
```

```
+--------------------------------------------------------------------------------------------------+
| Event 01J9ZK3M7Q8R2T4V6X8Z0B2D4F                        content.node.remove.trash   success       |
|  When     2026-10-02 15:30:01.123 (UTC 13:30:01)   Channel content, seq 1842   Chain: intact     |
|  Who      editor1 (user 14), roles Editor; 203.0.113.0/24; Firefox 131 / Linux                   |
|  Request  r-01J9ZK3M5A… POST /content/removeobject, admin, velocity, 184 ms, 302  [all events]   |
|  Object   node 275 "Workout" (object 273, article)            [open node]                         |
|  Target   trash                                                [open trash]                       |
|  Before   parent 89, path /1/2/89/275/        After  –                                           |
|  Parent   –    Children  –    Job  –                                                            |
|  prev sha256:9b1c0f…  hash sha256:41aa7e…                      [raw JSON]  [export this event]   |
+--------------------------------------------------------------------------------------------------+
```

```
+--------------------------------------------------------------------------------------------------+
| Archives (audit/manage)                                         [Verify all]  [Archive now]      |
|  Channel   Live files        Oldest live   Archives   Oldest archive   Last verified   State     |
|  content   90 (812 MiB)      2026-07-04    274        2024-10-03       02:15 today     intact    |
|  access    90 (95 MiB)       2026-07-04    274        2024-10-03       02:15 today     intact    |
|  system    90 (41 MiB)       2026-07-04    274        2024-10-03       02:15 today     BROKEN    |
|             first break: system-2026-09-14.jsonl line 2211 (altered)  [show]                      |
|  Signing key k2-20261002-3fa94c1b, fingerprint 3FA9 4C1B 77D0 E215     [Rotate key]              |
+--------------------------------------------------------------------------------------------------+
```

### Placement (Z2)

- **Top tab**: `[TopAdminMenu] Tabs[]=audit` and `[Topmenu_audit]` in settings/menu.ini with
  `URL[default]=audit/console` and `PolicyList[]=audit/read` — the same mechanism `[Topmenu_setup]` uses with
  `PolicyList[]=setup/managecache`, so the tab is not shown without the policy.
- **Dashboard sidebar link**: `[Leftmenu_my] Links[audit]=audit/console`, `PolicyList_audit[]=audit/read`, as the
  existing `PolicyList_change_password[]` entries do.
- **Dashboard block**: `[DashboardBlock_audit]` in settings/dashboard.ini (`Priority=20`, `NumberOfItems=10`,
  `PolicyList[]=audit/read`): the latest `notice`-or-higher events of the access and system channels, open alerts,
  and the "audit is off" or "chain broken" warning for users with audit/manage.
- **content/job and content/jobs**: the job page shows "Audit trail" (`audit/console/(job)/<id>`) and the jobs list
  a link per job, both only with audit/read.
- **Setup menu**: `[Leftmenu_setup] Links[audit]=audit/settings`, `PolicyList_audit[]=audit/manage`.
- **Node view** (Proposed): an "Audit" tab in the admin's node view with the fetch example above, audit/read only.

**A user without the policy sees nothing** — no tab, no sidebar link, no block, no link on the job pages, an empty
fetch, and `access.permission.refused` for a typed URL. This depends on the dashboard and menus checking policies
the way the kernel does, which is the dashboard defect fixed in stage 1. Today the dashboard view
(kernel/private/classes/views/content/dashboard.php) checks a block's `PolicyList` with
`$currentUser->hasAccessTo( $module, $function )`, but setup features still reach users who may not use them (see
"Dashboard defect"); stage 1 makes every block and link go through the kernel's check with limitations, and stage 4
relies on it.

## Sinks (Q7)

The file is always written; sinks are copies, delivered after the record is in the file (so a sink failure never
loses a record) and fed only records that passed the privacy rules.

### syslog / journald (`expAuditSyslogSink`)

RFC 5424, one message per record:

```
<PRI>1 2026-10-02T13:30:01.123Z web1 exponential 991876 access [exp@32473 id="01J9ZK…" name="access.session.login.failed" seq="211" channel="access" user="" ip="203.0.113.0/24" result="failed" request="r-01J9ZK…" hash="sha256:41aa…"] {"v":1,…}
```

- `PRI` = facility × 8 + severity (`authpriv` = 10, so a `notice` failed login is `<85>`).
- `MSGID` is the **channel**, not the name: RFC 5424 limits MSGID to 32 characters and names can be longer.
- The structured data element is `exp@32473`. **Proposed:** 32473 is the private enterprise number RFC 5612
  reserves for documentation; it is replaced by Exponential's own number when one is registered. `"`, `\` and `]`
  in values are escaped with `\` (RFC 5424 §6.3.3).
- `Transport=local` writes to the system log socket (`/dev/log`), which journald and rsyslog both read;
  `udp` sends the structured data only (RFC 5426: the JSON body would exceed a safe datagram); `tcp` and `tls`
  (RFC 5425 octet counting) send the full record as MSG.
- Delivery at flush time for `local` (a local socket write is cheap); network transports from the spool (below).

### Webhook (`expAuditWebhookSink`)

- `POST` to `URL` with `Content-Type: application/json`:
  `{"v":1,"installation":"6f1c…","site":"example","batch":"b-01J9…","events":[<record>,…]}`.
- Headers: `X-Exponential-Timestamp` (epoch seconds), `X-Exponential-Batch`,
  `X-Exponential-Signature: sha256=<hex HMAC-SHA-256(SigningSecret, timestamp + "." + body)>`. A receiver checks the
  signature and rejects timestamps more than 300 seconds away (replay).
- Batching: up to `BatchSize` records or `BatchSeconds`, whichever comes first.
- Web requests never wait for a webhook: records for network sinks are appended to the spool
  (`SpoolDir/<sink>.jsonl`, `flock()`ed) at flush; the audit cronjob part (every run of `frequent`) delivers the spool.
  `critical` and higher are also tried right after the response (`fastcgi_finish_request()` when available, a 2
  second timeout) — **Proposed**.
- Retry: `Retries` times with `RetryBackoff` doubled each time; a 2xx answer removes the batch from the spool; after
  the last retry the batch stays spooled and `system.audit.sink.failed` is recorded (once per sink per hour).
  Delivery is at least once: receivers de-duplicate by event `id`.

### E-mail (`expAuditMailSink`)

Events at `MinSeverity` (`critical`) or matching `Events[]` are mailed through `eZMail` to `Receivers[]` (default:
the site's `AdminEmail`), one mail per alert or event with the console link, no personal data beyond what the
record holds after privacy, at most one per rule and group per `Throttle` seconds; from the cronjob part, so a
request never waits on SMTP.

### The sink registry

`[AuditSinkSettings] SinkClasses[<name>]=<class implementing expAuditSink>`, configured in `[AuditSink_<name>]`,
attached per channel with `[AuditChannel_*] Sinks[]`. The console's settings view lists each sink with
`problem()`, the spool size and the last delivery. `exp:audit sinks test <name>` sends a test record. Counted by the
RAD survey (registry `auditsinks`).

## Alerts (F5)

- **Built-in rules** are `[AlertRule_*]` blocks shipped in audit.ini (brute force by address, brute force by account,
  admin role granted, settings written out of hours, mass delete, audit disabled, chain broken); **INI rules** are
  more blocks of the same form; **rule classes** implement `expAuditAlertRule` and are named by `Class=`
  (`threshold`, `match`, `schedule` built in).
- **Evaluation at flush**: after the buffer is written, each rule whose `Event` pattern matches one of the flushed
  records is evaluated with those records. `match` and `schedule` rules decide at once. `threshold` rules keep their
  window in a small state file per rule (`<LogDir>/alerts/<rule>.json`: per group the timestamps inside the window),
  read and written under `flock()` — only when a matching event was flushed, so a request without failed logins or
  role changes pays nothing.
- **Evaluation in the cronjob part**: rules over many requests that need the index (Proposed: none of the built-in
  ones), the "audit disabled" check (the state file says enabled but the settings say disabled — catches a change
  made by editing a file by hand), the result of the daily verification (`chain_broken`), and expiry of windows.
- **De-duplication**: an alert for (rule, group) fires once per window; while it is open, more matches update its
  count and last event instead of firing again; it fires again when the count doubles, or in a new window after the
  window closed. Each firing is `system.audit.alert` with the rule, group, count, window and the ids of the first and
  last matching events, sent to the rule's `Sinks[]`.
- **Acknowledging** in the console (audit/manage) closes the alert and is recorded.

To be proven in stage 5: 20 failed logins from 203.0.113.0/24 within 300 s fire `brute_force` once; the 40th fires it
again; 19 do not.

## Rotation, archives and retention (Q8)

- **Schedule**: the cronjob part `Exponential\Cronjob\Kernel\Audit` (`cronjobs/audit.php`, `[CronjobPart-audit]`
  and, Proposed, in `[CronjobPart-frequent]`). Every run: incremental index, spool delivery, alert windows. Once a day
  after `RotateAfter`: close yesterday's files (`system.audit.file.close`), verify, archive what is older than
  `LiveDays`, purge archives older than `ArchiveDays`, pseudonymise index rows, write the checkpoint. A run takes a
  lock (`<LogDir>/.cron.lock`) so two runs never overlap. The same tasks run from the console (archives view) and
  from `exp:audit rotate|archive|purge`.
- **Size rotation** happens in the writer: a file past `MaxFileSize` is closed and the next part opened in the same
  append, under the same lock.
- **Format handlers**: `gzip` (zlib, always available), `bzip2` (ext-bz2), `xz` (the `xz` binary; **Proposed:** no
  PHP extension is commonly available), `zstd` (ext-zstd, else the `zstd` binary), `zip` (ext-zip; one archive per
  file, so a single file can be restored). A handler that reports a `problem()` is skipped and gzip used, and the
  manifest records the handler actually used.
- **Archive path**: `ArchiveDir/<channel>/<YYYY>/<file>.jsonl.<ext>` with the manifest beside it, modes `FileMode`
  and `DirMode`. **Proposed:** recommend (operator guide) a path on another file system, or one the web server user
  can only add to, so a compromised web process cannot rewrite archives.
- **Verification**: before archiving (`VerifyBeforeArchive`), after writing (`VerifyAfterArchive`: decompress,
  compare sha256), and on demand (`exp:audit verify --archives`), which also checks each manifest's HMAC and the
  `previous_manifest` chain.
- **Retention** per channel (`LiveDays`, `ArchiveDays`): an archive is removed only when it is older than
  `ArchiveDays` **and** its key is not needed by a newer manifest; each removal is recorded in `system.audit.purge`
  with the file names and their sha256, so the record of what existed outlives the data.
- **Restore**: `exp:audit restore --channel=content --date=2026-01-15` decompresses into `<LogDir>/restored/` for
  reading, verifying and reindexing; restored files are never put back into the live chain.

## The command: `exp:audit`

`bin/php/audit.php` (so `./console exp:audit`), class `Exponential\Command\Kernel\Audit` in
`kernel/private/classes/commands/audit.php`. Every subcommand that reads records is recorded as
`system.audit.read`, every one that changes something as its manage event. Run as root, it writes files with the
site user's owner and group (as `expContentJobStore::fixOwner()` and `expDebugBarLog::ownLikeParent()` do).

| Subcommand | Does | Options |
|---|---|---|
| `status` | audit on/off, channels, today's counts, chain state, index lag, spool sizes, key fingerprints | `--json` |
| `tail` | prints new records as they are written | `--channel=`, `--name=`, `--follow`, `--lines=20`, `--json` |
| `search` | searches the index (or the files with `--files`) | the console's filters as options: `--name=`, `--user=`, `--login=`, `--object=node:275`, `--result=`, `--from=`, `--to=`, `--q=`, `--request=`, `--job=`, `--legacy-file=`, `--limit=`, `--json` |
| `show <id>` | one record with parent, children and request siblings | `--json` |
| `verify` | verifies chains (and archives) | `--channel=`, `--from=`, `--archives`, `--json`; exit 0 intact, 1 broken, 2 error |
| `rotate` | closes files due for rotation, runs retention | `--channel=`, `--dry-run` |
| `archive` | archives files older than LiveDays (or `--before=`) | `--channel=`, `--format=`, `--dry-run` |
| `restore` | decompresses an archive for reading | `--channel=`, `--date=`, `--to=` |
| `purge` | applies retention to archives and the index | `--channel=`, `--dry-run` |
| `reindex` | rebuilds or catches up the index | `--incremental`, `--archives`, `--channel=` |
| `pseudonymise` | runs the 90-day pseudonymisation now | `--dry-run` |
| `export` | writes records to a file | filters as `search`, `--format=jsonl|csv|bundle`, `--subject-user=<id>` (an access request: every record about or by that user, with the pseudonymised ones matched by hashing the user's identifiers), `--out=` |
| `import` | imports the 4.x text logs (Z4) | `--dir=` (default the old `LogDir`), `--file=`, `--dry-run`, `--keep-originals` |
| `key` | `list`, `rotate`, `rotate --pseudonym`, `fingerprint` | |
| `checkpoint` | writes a checkpoint now | |
| `sinks` | `list`, `test <name>`, `flush` (deliver the spool now) | |
| `alerts` | `list`, `test <rule> --replay=<from>` (runs a rule over past records without firing) | |

```bash
./console exp:audit tail --channel=access --follow
./console exp:audit search --name='access.session.login.failed' --from=2026-10-01 --ip=203.0.113.0/24
./console exp:audit verify --archives --json
./console exp:audit export --job=20261002-133001-4f2a9c1e --format=csv --out=var/tmp/job-trail.csv
./console exp:audit import --dry-run
```

**Import of the old logs (Z4).** `import` reads each file named in `AuditFileNames[]` and its rotated copies (`.1`,
`.2`, `.3`, oldest first), splits entries at lines starting with `[ ` (both the current form
`[ time ][ siteaccess ][ context ] [ip] [login:id]` and the older form without siteaccess and context), maps the file
to its old name and the old name through `Map[]`, drops `NeverRecord[]` attributes, applies the privacy rules, and
writes `<LogDir>/imported/<channel>-<YYYY-MM-DD>.jsonl` with `imported: true` and `source` (file, line, sha256 of the
original file), outside the chain. The originals are then compressed into `ArchiveDir/legacy/` with a signed manifest
listing each file's sha256, and the import is recorded as `system.audit.import`. Importing a file whose sha256 was
imported before is skipped, so `import` can be run again safely.

## Security notes

### Threat model

| Who | Can | The design's answer |
|---|---|---|
| An editor without audit policies | see nothing of the audit | policies on every view, link, block and fetch; refusals recorded |
| A user with audit/read | read events (of the channels the limitation allows), export them | reads and exports are themselves recorded; personal fields truncated/hashed by default |
| An administrator with audit/manage | change settings, switch audit off, rotate keys | each of those is recorded before it takes effect; `audit_disabled` alert to syslog and mail; optional password re-entry; the change and the alert leave the server through sinks |
| An attacker running code as the web server user | stop recording; read the signing key in settings/override; rewrite live files and recompute the chain | cannot rewrite what has already left the server (syslog, webhook, mailed alerts, checkpoints); archives on a path the web user cannot rewrite; checkpoints signed before the compromise expose a rewritten chain (`rewritten`) |
| root or the hosting provider | anything on the server | out of scope: only copies held elsewhere (sinks) can show what happened |
| Someone reading the network | — | syslog over `tls`, webhook over HTTPS with signatures |

### What the chain proves, and what it does not

It proves that the records of a channel, from its genesis or the last verified checkpoint or manifest up to the
point checked, are the records that were written, in that order, with none removed, inserted or changed — **unless**
the chain was recomputed by someone who could write the files. It does not prove that every event was recorded (code
that never calls the audit, or a recorder that was switched off, leaves no record and no break), that a record's
content is true (a forged `actor` in code is recorded faithfully), or who changed a file. The HMAC of manifests and
checkpoints adds that only a holder of the key could have signed them; a key read by an attacker weakens everything
signed after that moment, not what was signed and sent elsewhere before.

### Key handling

Keys are generated on first use into `settings/override/audit.ini.append.php` (Z7), mode 0640, never committed (the
project's rule for `settings/override`), masked by `expIniEditor::isSecret()` in exp:ini, the debug bar and the
settings views, shown only as fingerprints. Backups of `settings/override` must be treated as secrets. **Proposed:**
`[AuditKeySettings] KeyFile=` may name a file outside the web root and readable only by the user that runs the cronjob
part, for sites that want archive signing out of the web process's reach; the writer then signs nothing (records are
only hash-chained) and checkpoints and manifests are signed by the cronjob part alone.

### GDPR

(This is the design's position, not legal advice; each operator documents it in its own record of processing.)

- **Lawful basis**: legitimate interest (Art. 6(1)(f)) in the security and accountability of the system, and legal
  obligation (Art. 6(1)(c)) where a law requires access logging; Art. 32 (security of processing) names logging as a
  measure.
- **Minimisation by default**: addresses truncated, sessions hashed, user agents shortened, no content values, no
  passwords, tokens or secrets ever; reads off.
- **Retention**: 90 days live, 2 years archived (F6), per channel, removed automatically and recorded.
- **Pseudonymisation** (Art. 4(5)) in the index after 90 days with a keyed hash.
- **Access requests** (Art. 15): `exp:audit export --subject-user=<id>` and the console's export with the user filter
  give every record by or about that user, including pseudonymised ones (matched by hashing the user's identifiers
  with the pseudonym key).
- **Erasure** (Art. 17): audit records are kept under Art. 17(3)(b) and (e) for their retention period; deleting one
  would break the chain. A request is answered with the retention period and the pseudonymisation already applied.

## Performance design and budget

Targets (owner): **under 2 ms per request** with buffered writes and the default settings on; **content jobs within
+5 %** of their time with audit off.

- No database access at request time: the index is filled by the cronjob part.
- A request that raises no event costs a request id (16 random bytes), the `X-Request-Id` header and an empty flush:
  measured target under 0.05 ms.
- `event()` for a name that is off: one array lookup in the compiled routing table (patterns resolved once per
  settings state and cached with the INI cache), target under 2 µs.
- `event()` for a name that is on: build and privacy-filter the record, target under 30 µs; no I/O.
- Flush: per channel that has records, one `fopen` (append), `flock`, one seek and read of the last line, the hashes,
  one `fwrite`, `fflush`, unlock — target under 0.5 ms per channel on local disk. Most requests touch no channel; a
  publish touches at most two.
- Immediate events (logins, refusals) cost one such write each; they are rare per request.
- Sinks at flush: only `syslog` with `Transport=local` (one socket write); everything else is spooled.
- Content jobs: one parent per job, children per node, flushed every `FlushInterval` seconds or `MaxEvents`: for a
  10 000-node remove, 10 000 children at about 30 µs plus 20 flushes — about 0.3 s against minutes of work.
- `access.view.sensitive` on every admin view of setup/role/user/audit: one immediate write per such request.

Measured in stage 6 (and checked at each stage): `Audit=disabled` against the defaults, Apache (PHP-FPM) and Velocity,
1000 requests each of the front page, a node view in the admin, a publish, and a failed login; p50 and p95 of the
difference; a 2 000-node subtree remove as a content job, three runs each way.

## Default installation

Audit is a convention of the product, not an option one remembers to switch on. Unlike the 4.x releases, where
`audit.ini` shipped with `Audit=disabled`:

- `settings/audit.ini` ships with `Audit=enabled`; the families access, security, system/config and the
  destructive content actions (remove, trash, move, hide, section, state, role and policy changes, content jobs)
  are on; read tracking (node views, searches) is off; the channels, rotation, 90 days live / 2 years archived,
  privacy defaults (truncated IPs, user agent on, no passwords or tokens) and the hash chain are on.
- Every way an installation is made gets it with no extra step: the setup wizard, `exp:install`, kickstarter, the
  multisite package installer and an upgrade of an existing installation (whose `settings/override` may still say
  `Audit=disabled` from the 4.x releases: the upgrade reports that override and asks before keeping it).
- The archive signing key is generated on the first event, so a fresh installation never runs unsigned.
- The log directory is created with the site user's ownership, also when the first event comes from a command run
  as root.
- Turning it off is an explicit, audited decision: `Audit=disabled` written anywhere is itself recorded as
  `system.audit.disable` before it takes effect, and the dashboard shows an "audit is off" warning to users with
  audit/manage.
- The performance budget (stage 6) is measured with these defaults on, since that is what every site runs.
- The `[CronjobPart-audit]` tasks run with the installation's existing cronjob line (Proposed: through
  `[CronjobPart-frequent]`), so rotation, archiving and retention need no extra crontab entry; an installation whose
  cronjobs never run gets a dashboard warning for audit/manage users when the newest checkpoint is older than two days.
- The audit tables (`expaudit_event`, `expaudit_cursor`, `expaudit_file`) are part of the kernel schema of every
  engine, so a new installation has them; an upgrade creates them (stage 4 upgrade script).
- The policies `audit/read` and `audit/manage` exist in every installation; no shipped role other than Administrator
  holds them, and the installer does not add them to Editor or Partner roles.
- An installation made from a package that carries its own `audit.ini.append.php` keeps the shipped defaults for
  everything that file does not set; a package cannot ship keys (a key in a package is refused and a new one
  generated).
- When the log directory cannot be written at install time (a read-only `var`), the installer stops with that
  message instead of finishing an installation that cannot record.

## Delivery (Z8) — each stage committed and shown before the next

1. **Dashboard permission defect (F7)**: every dashboard block and sidebar link checks the user's access to its
   module/view and limitations; a permission matrix test (Anonymous, Editor, Member, Partner, Administrator).
2. **Event core**: `expAudit::event()`, taxonomy registry, buffered writer with request/session/job correlation,
   JSON lines channels, hash chain, privacy rules, `audit.ini` (every setting documented), the `eZAudit::writeAudit()`
   compatibility mapping, per-request reset for Velocity.
3. **Instrumentation**: every family of Q5 in the kernel (and the ezpEvent bridge), with the coverage matrix test on
   Apache, Velocity and CLI.
4. **Index + console**: the database index on all five engines (Z1), the `audit` module and tab, timeline, filters,
   search, event detail with links, charts and alerts view, export; dashboard block and sidebar link; links from
   content/job and content/jobs; Setup menu entry; policies audit/read and audit/manage.
5. **Sinks, alerts, rotation**: syslog/journald, webhook, e-mail, sink registry; built-in, INI and class alert rules;
   rotation by day and size, compressed archives through format handlers, retention, HMAC-signed manifests, the
   cronjob part, `exp:audit` (tail, search, verify, rotate, archive, reindex, export, import of the old logs).
6. **Docs and proof**: this guide becomes the operator guide, developer guide (worked, tested examples), generated
   event reference and security notes; the tamper test, the performance measurement (target: under 2 ms per request
   with buffered writes, content jobs within +5% time) and the RAD survey counting the new registries.

### Deliverables per stage

| Stage | Code | Settings and schema | Tests (see "Acceptance tests") | Shown to the owner |
|---|---|---|---|---|
| 1 | dashboard view and templates, `[Leftmenu_*]`/`[Topmenu_*]` checks through `eZUser::hasAccessTo()` with limitations | – | permission matrix of the dashboard (A1) | the dashboard as Editor, Member, Partner, Administrator |
| 2 | `kernel/classes/audit/`: `expAudit` (event, begin/end, withParent, setJob, legacy, isOn, flush, resetRequest), `expAuditBuffer`, `expAuditWriter` (lock, tail, chain, rotation by size, torn-line repair), `expAuditPrivacy`, `expAuditTaxonomy` (+ branch registry), `expAuditKeys`, `expAuditVerifier`; `eZAudit` rewritten as the compatibility path; `X-Request-Id`; the Velocity reset in `ezpKernelWeb::__construct()`; the cleanup, fatal and shutdown flushes | `settings/audit.ini` as in the settings reference, `Audit=enabled` | record format and canonical JSON (B1), chain and verifier unit tests (B2), the tamper test's file cases (T1–T8), compatibility (B3), buffering and Velocity (B4), privacy (B5) | a day of alpha's channels, `exp:audit verify` (the minimal command lands here), a tampered copy reported broken |
| 3 | every catalogue event raised at its hook point (the `writeAudit()` calls replaced by native ones), the new hook points, the ezpEvent bridge, the runnable events for commands and cronjobs | `[AuditBridgeSettings]` | coverage matrix C1 on Apache, Velocity and CLI | the coverage report: 135 names, each raised, each where expected |
| 4 | module `audit` (views, fetch, operator), `expaudit_*` tables and the incremental indexer, pseudonymisation, dashboard block, sidebar link, top tab, Setup menu entry, job links, node view tab (Proposed) | `share/db_schema.dba`, `kernel/sql/*`, the Oracle schema, an upgrade script; menu.ini, dashboard.ini entries; policies `audit/read` (Channel limitation), `audit/manage` | index on each engine (D1), console permission matrix (A2), fetch as each role (A3), search per engine (D2) | the console on alpha (MySQL) and on the SQLite, MongoDB and Oracle installations reachable here; PostgreSQL where reachable |
| 5 | sinks (syslog, webhook, mail) and their registry and spool, alert rules and evaluator, format handlers, archive and manifest writer, retention, checkpoints, the cronjob part, `exp:audit` complete including `import` | `[CronjobPart-audit]` in cronjob.ini; the sink, alert, rotation and archive blocks | sinks (E1), alerts (E2), archives and retention (E3), tamper test's archive cases (T9–T12), import (E4) | a webhook received and verified, a brute-force alert mailed, a year of archives verified, the 4.x logs of alpha imported |
| 6 | – (fixes only) | – | performance P1–P4, the full tamper test, the full permission matrix, the RAD survey count (F1) | operator guide, developer guide, generated event reference, security notes |

## Acceptance tests (Z9)

Each test is a script that prints PASS/FAIL per case and runs against the live installation (Apache and Velocity) and
the CLI. Users are generic test accounts (`editor1`, `member1`, `partner1`, `auditor1`, `auditmanager1`); addresses
from 203.0.113.0/24 and 2001:db8::/32; domains example.com.

### Coverage matrix (C1, stage 3)

For every name of the taxonomy registry (the catalogue's 135 and any branch an active extension registers): the test
performs the action that raises it — through the web view on Apache, the same on Velocity (port 8080), and through the
command or cronjob part where one exists — and checks that exactly the expected record appears in the expected
channel, with `name`, `verb`, `object.type`, `object.id`, `result` and `request.engine` as expected, `before`/`after`
holding the catalogue's fields and none of the "never" ones. The output is a matrix (name × Apache/Velocity/CLI:
PASS, FAIL, n/a with the reason) and the test fails for a registry name no case raises. Events that are `off` by
default are tested with them switched on in a test override, and once more switched off (no record).

### Tamper test (T1–T12, stages 2, 5 and 6)

On a copy of a channel with 1 000 records over three day files:

| Case | Change | Expected verification result |
|---|---|---|
| T1 | one byte of one record's `object.name` changed | broken: `altered` at that line |
| T2 | one line removed | broken: `link` and `gap` at the next line |
| T3 | two adjacent lines swapped | broken: `reordered` |
| T4 | a forged line inserted with a correctly computed `hash` | broken: `link` at the line after it |
| T5 | the last 100 records changed and the chain recomputed (an attacker without the key) | broken: `rewritten` at the last checkpoint before them |
| T6 | the middle day file removed | broken: `missing_file` |
| T7 | the last 10 lines of a closed file cut off | broken: `truncated` |
| T8 | a writer killed (`SIGKILL`) in the middle of a `fwrite()` | `repaired`: a `system.audit.chain.repair` record, chain intact around it |
| T9 | one byte of an archive changed | broken: archive sha256 mismatch |
| T10 | a manifest edited (its `records` count) | broken: manifest HMAC invalid |
| T11 | a manifest signed with a key id that is not in the settings | broken: `unknown_key` |
| T12 | one day's manifest and archive removed | broken: `previous_manifest` gap |
| T0 | no change | intact, 1 000 records, the time span right |

Every broken result also produces `system.audit.chain.broken` and the `chain_broken` alert (stage 5 on).

### Performance (P1–P4, stage 6; checked at each stage)

| Case | Measure | Pass |
|---|---|---|
| P1 | front page and an admin node view, 1 000 requests each, defaults vs `Audit=disabled`, Apache and Velocity | p50 difference < 2 ms, p95 difference < 3 ms (Proposed for p95) |
| P2 | a publish and a failed login (immediate write), 200 each | difference < 2 ms per request |
| P3 | a 2 000-node subtree remove as a content job, three runs each way | time with audit ≤ 1.05 × time without |
| P4 | 10 concurrent writers × 10 000 events to one channel | chain intact, 100 000 records, no torn lines |

### Permission matrix (A1–A3, stages 1 and 4)

Roles: Anonymous, Member, Editor, Partner, Administrator, Auditor (a test role with `audit/read`), Audit manager
(`audit/read` + `audit/manage`), and a Shop auditor (`audit/read` limited to Channel `commerce`).

| Case | Anonymous | Member | Editor | Partner | Auditor | Shop auditor | Audit manager | Administrator |
|---|---|---|---|---|---|---|---|---|
| A1 dashboard: setup blocks and links | – | – | only those its policies allow | same | same | same | same | all |
| A2 top tab Audit, sidebar link, dashboard block | no | no | no | no | yes | yes | yes | yes |
| A2 `audit/console` | refused | refused | refused | refused | all channels | commerce only | all | all |
| A2 `audit/event/<id of an access event>` | refused | refused | refused | refused | yes | refused | yes | yes |
| A2 `audit/archives`, `audit/settings` | refused | refused | refused | refused | refused | refused | yes (reauth if on) | yes |
| A2 links on content/job and content/jobs | – | – | no | no | yes | yes (commerce: none shown) | yes | yes |
| A3 `fetch( 'audit', 'events' )` | empty | empty | empty | empty | events | commerce events | events | events |
| A3 `exp:audit search` as that user (`--login`) | refused | refused | refused | refused | yes | commerce only | yes | yes |

Each refusal must produce `access.permission.refused`; each allowed console use `system.audit.read`.

### Other stage tests

- **B1** record format: every field of the fields table present or absent as specified; canonical JSON of crafted
  records (key order, unicode, `/`, control characters, `{}` vs `[]`) hashed to known values.
- **B2** chain: genesis value, file open/close records, rotation by size within one request, two processes appending.
- **B3** compatibility: the 15 old names through `eZAudit::writeAudit()`; `HashKey` and secrets never written; an
  unmapped name becomes `system.legacy.<name>`.
- **B4** buffering: exception, `exit`, `eZExecution::cleanExit()`, fatal error, 1 000 Velocity requests in one worker
  (no record carries another request's id).
- **B5** privacy: each field × full/truncate/hash/off; IPv4 /24 and IPv6 /48; `SecretPathViews[]` URLs; a failed login
  for an unknown user writes the attempted login hashed.
- **D1** index on SQLite, MySQL/MariaDB, PostgreSQL, Oracle and MongoDB (each reachable here): create, incremental
  index of 10 000 records, a crash between batch and cursor (no duplicate, no gap), rebuild equals incremental,
  pseudonymisation after a simulated 91 days.
- **D2** search: the same ten queries give the same ids on every engine (full text where available, `LIKE` otherwise).
- **E1** sinks: RFC 5424 messages parsed by a syslog parser; webhook signature verified by a test receiver; retries and
  spool after a receiver outage of 10 minutes; mail throttled.
- **E2** alerts: each built-in rule's threshold reached, not reached, and repeated (de-duplication).
- **E3** archives: each format handler (or its fallback), restore, retention keeps keys still needed.
- **E4** import: alpha's 4.x audit files imported, counts equal to their entries, re-import skipped, originals
  archived with a valid manifest.
- **F1** the RAD survey lists the four audit registries, counts an extension's branch as added and a missing class
  as broken.

## Dashboard defect (recorded 2026-10-02)

The admin dashboard shows setup features (and other module views editors may not use) to every user who can open
the dashboard. It must show each block and link only when the current user has access to the module/view behind it,
checked the way the kernel checks it (`eZUser::hasAccessTo()` with limitations), not by role name. Fixed in stage 1.

### Stage 1 results (2026-10-02)

**Inventory.** 99 configured targets reach the admin dashboard page: 13 top tabs (`menu.ini [TopAdminMenu]`), 65
left menu links (`[Leftmenu_*]`, all drawn by `parts/ini_menu.tpl`; the dashboard's own is `Leftmenu_my`, 8 links),
4 dashboard blocks (`dashboard.ini`) and 17 fixed links of the admin4 dashboard template; besides them the right
menu tools (current user, clear cache, bookmarks, quick settings), the node context menus and the debug bar. As the
administrator sees it, the page carries 61 distinct links and form targets.

**What was shown to whom.** Measured before the fix with a pure Editor and a subtree-limited Editor (the Editors
group's role set): the top tabs Design, Newsletter and Export; the dashboard's quick actions Users, Upload files,
Tags and Layouts; the key figures Users and In the trash; the author links of "All latest content" (user nodes the
editor may not read); "Change password" in the right menu (the Editor role alone has no `user/password`); the context
menu's "Change content class"; the "Stay secure" card with its two Setup-only steps for everyone. The dashboard's
Setup links were checked with `setup/administrate` while their views need `setup/system_info`, `setup/setup` and
`setup/managecache`: a role with only `setup/administrate` saw links it could not open. Anonymous, Member and Partner
cannot sign in to the admin siteaccess at all (their `user/login` has no admin SiteAccess) and got the sign-in page.

**The fix.** `expViewAccess` (kernel/classes/expviewaccess.php) answers whether the current user can open an
address, decided the way `ezpKernelWeb` decides the request: URL alias and wildcard translation (a moved alias is
followed), module and view, `[SiteAccessRules]`, `RequireUserLogin` with `AnonymousAccessList`, `PolicyOmitList`,
the `user/login` SiteAccess limitation, `eZUser::hasAccessToView()` with the view's functions and limitations (a
view without functions needs its module), and the node or object of `content/view`, `content/edit` and `user/edit`.
Templates ask `fetch( 'user', 'can_open', hash( 'uri', 'setup/cache' ) )`. Used by:

- the top tabs (`eZTopMenuOperator`): a tab's URL must open, on top of its `PolicyList`;
- every left menu (`parts/ini_menu.tpl`, admin and admin4): a link the user cannot open is left out
  (`NoAccessLinks=disabled` keeps the 4.x greyed-out name), a menu with no link left is left out;
- the dashboard blocks: `ViewList[]` per block in `dashboard.ini`, checked with `PolicyList[]` in
  `Dashboard::visibleBlocks()` before a block renders;
- the admin4 dashboard: each quick action, key figure, system link and the "Stay secure" card; the author link of
  "All latest content"; "Change password" of the current user tool; the change-class context menu entries
  (`changeclass/convert`, in the expchangeclass extension).

New settings, documented in the files: `menu.ini [MenuAccessSettings] CheckViewAccess` (enabled) and `NoAccessLinks`
(hidden); `dashboard.ini [DashboardBlock_*] ViewList[]`. Corrected entries: `Topmenu_design` reads the design root
node, `Topmenu_dashboard` and the `Leftmenu_my` dashboard link need `content/dashboard`, `edit_profile` lost its
`user/selfedit` PolicyList (the profile is edited through `content/edit`; the object's own `canEdit()` decides). The
cache-block around the footer and context menu of the admin, admin3 and admin4 pagelayouts is keyed by the user's
roles too, since the context menu now differs by policy.

**Permission matrix (A1), Apache, `/admin` and `/admintest_admin4`, 960 px at scale 2.** Test users in a temporary
group, one per role set: Anonymous; Member (+Anonymous, as the Members group); Partner (+Member, +Anonymous, as the
Partners group); Editor; Editor limited to the subtrees of the Editors group (+Member); Administrator; and the
installation's own admin. Every visible link and form target was requested with the user's session (must open:
200, no access-denied page, no sign-in form); every link the test administrator sees that the user does not was
requested directly (must be refused). Both siteaccesses gave the same result:

| User | Dashboard | Visible | open | Hidden (administrator sees) | refused |
|---|---|---|---|---|---|
| Anonymous | sign-in page | – | – | 44 | 44 |
| Member | sign-in page | – | – | 44 | 44 |
| Partner | sign-in page | – | – | 44 | 44 |
| Editor | opens | 26 | 26 | 21 | 21 |
| Editor, subtree-limited | opens | 27 | 27 | 20 | 20 |
| Administrator (test) | opens | 48 | 48 | 0 | 0 |
| admin | opens | 61 | 61 | 0 | 0 |

0 failures (336 checks per siteaccess); before the fix both editors were shown the links listed above. Unit
tests: `tests/tests/kernel/classes/expViewAccessTest.php` (live database, 14 tests). The test users and their group
were removed afterwards. Velocity (port 8080) is checked after its restart with the same matrix.

## Stage 2 — built (2026-10-02)

The event core is in place and **on** (`Audit=enabled` in the shipped `settings/audit.ini`). Every point marked
"Proposed:" above was accepted by the owner and is built as written, with the deviations listed at the end.

### Try it in two minutes

```bash
./console exp:audit status --allow-root-user              # on/off, key fingerprint, files and chain state per channel
./console exp:audit tail --allow-root-user                # the newest 20 records, all channels
./console exp:audit tail --channel=access --follow --allow-root-user    # now log in and out in the admin: the lines appear
./console exp:audit show <event id from tail> --allow-root-user         # one record in full, its hash re-checked
./console exp:audit verify --allow-root-user              # every chain: INTACT / REPAIRED / BROKEN with file and line
```

In the admin, as an administrator: **`/audit/recent`** (`https://edit.alpha.se7enx.com/audit/recent`, or
`https://alpha.se7enx.com:8080/admin/audit/recent` on Velocity). It shows the latest 100 events and the state of each
channel's chain; `/audit/recent/(channel)/access` shows one channel. Every response carries the header
`X-Exp-Request-Id`; the same id is in the `request.id` of that request's records.

To see a break: copy a day file somewhere under `var/tmp/`, change one character of a name in it, and verify the copy
with `expAuditVerifier` (the tests do exactly this, `expAuditChainTest`); `exp:audit verify` on the live directory
must always say INTACT.

### What exists

| Part | Where |
|---|---|
| `expAudit` (event, begin/end, withParent, setJob/setRun, isOn, isEnabled, legacy, settingWrite, flush, flushFinal, flushOnFatal, resetRequest, requestId, responseHeader, checkpoint) | kernel/classes/audit/expaudit.php |
| Settings snapshot per request (test override) | expauditconfig.php |
| Taxonomy registry: the 135 names of the catalogue, extension branches, patterns, routing, immediate events | expaudittaxonomy.php, expaudittaxonomybranch.php |
| Buffer, writer (channel lock, chain head from the file, open/close records, size rotation, torn-line repair, ownership), verifier, reader | expauditbuffer.php, expauditwriter.php, expauditverifier.php, expauditreader.php |
| Keys (installation id, signing key with key id, pseudonym key; generated on first use into `settings/override/audit.ini.append.php`, mode 0640) and the HMAC | expauditkeys.php |
| Privacy (full/truncate/hash/off per field, never-recorded names, secret paths, user agent families) | expauditprivacy.php |
| Canonical JSON | expauditjson.php |
| Interfaces for stage 5 (no implementation yet; their INI entries are commented out so the RAD survey does not count them as broken) | expauditsink.php, expauditalertrule.php, expauditformathandler.php |
| `eZAudit::writeAudit()` → `expAudit::legacy()`: the 15 old names mapped, the call site deciding content-delete, content-hide and order-delete; `$GLOBALS` caches removed | kernel/classes/ezaudit.php |
| Per-request reset (Velocity) and the `X-Exp-Request-Id` header | kernel/private/classes/ezpkernelweb.php |
| `access.session.logout` | kernel/classes/datatypes/ezuser/ezuser.php |
| `system.setting.write` (exp:ini, the debug bar, every write through `expIniEditor`), `system.audit.setting.write`, `system.audit.disable`; `system.setting.undo` | kernel/classes/ini/expinieditor.php, kernel/classes/debugbar/expdebugbarsettings.php |
| `exp:audit` status, channels, tail, show, verify, checkpoint | bin/php/audit.php, kernel/private/classes/commands/audit.php |
| Module `audit`, view `recent`, policy `audit/read` | kernel/audit/, kernel/private/classes/views/audit/recent.php, design/admin4 and design/admin templates/audit/recent.tpl |
| RAD survey registries auditbranches, auditsinks, auditalertrules, auditformats | kernel/setup/expradsurvey.php |

Recorded today: the 36 `writeAudit()` call sites (logins, failed logins, deletes, moves, hide/reveal, roles, sections,
states, orders, the ezmbpaex password events), logouts, settings writes and the audit's own events
(`system.audit.read`, `.verify`, `.chain.broken`, `.key.create`, `.checkpoint`, `.file.open/close`, `.chain.repair`,
`.overflow`) and `system.error.fatal`. Everything else in the catalogue is stage 3.

### Files

`var/site/log/audit/<channel>-<YYYY-MM-DD>[.<part>].jsonl` holds one canonical JSON line per record (keys sorted, null
fields left out). A daily signed checkpoint is written by the first write of each UTC day (the cronjob part of stage 5
will take this over). The directory also holds `.<channel>.lock`, `.keys.lock`, `.checkpoint` and `.checkpoint.lock`.
Directories are 0770 and files 0640, with the owner and group of `var/site/log` even when written by root (Velocity,
commands).

### Tests

`php vendor/bin/phpunit tests/tests/kernel/classes/audit/`: 42 tests, about 4 900 assertions. `expAuditRecordTest`
covers B1, names, patterns, routing, branches and parents. `expAuditChainTest` covers B2 and tamper cases T0–T8.
`expAuditPrivacyTest` covers B5, `expAuditBufferTest` covers B4 (1000 worker requests), and `expAuditCompatTest`
covers B3, keys, ownership and settings writes. They write only into `var/tmp/audit-tests/` with test settings and
keys. They use no database and never touch the live log. The write-failure test writes its unwritable records to
error.log on purpose.

### Measured on alpha

The proof ran on both Apache and Velocity (:8080): a failed login, a login, `audit/recent`, a logout, and a debug
bar setting toggled and undone. All were recorded with the right engine and request id, and `exp:audit verify`
reports every channel INTACT.

Overhead per request, measured in the kernel (a script run through bin/php/ezexec.php, 2 000 requests). A request that
raises no event, which is what the admin pages of stage 2 are, cached or not, costs:

| Case | p50 | p95 |
|---|---|---|
| Request reset, request id header and empty flush (audit on) | 0.090 ms | 0.136 ms |
| The same with `Audit=disabled` | 0.029 ms | 0.051 ms |
| `event()` for a name that is off | 0.001 ms | 0.001 ms |
| One buffered event and its flush (one append) | 0.47 ms | 0.62 ms |
| One immediate event (a login) | 0.43 ms | 0.56 ms |

So audit on versus off adds about 0.06 ms to a page that records nothing (target: under 2 ms), and about 0.5 ms to a
request that writes one channel.

### Deviations from the text above

- The response header is `X-Exp-Request-Id` (`RequestIdHeader=X-Exp-Request-Id`), named like the other `X-Exp-*`
  headers, not `X-Request-Id`.
- `SinkClasses[]`, `RuleClasses[]` and `FormatHandlers[]` of audit.ini are shipped commented out until stage 5
  delivers the classes.
- `TrustedRequestIdHeader` is only trusted from the loopback addresses.
- A failed login with a known account has `reason: credentials`; with an unknown login `reason: not_found` and the
  attempted login hashed.
- The view `audit/recent` uses the `ezsetupnavigationpart`. It has no menu entry yet; the top tab and the
  navigation part are stage 4.

## Stage 5 results (2026-10-02)

Sinks, alerts, rotation, archives, retention, the import of the 4.x logs, the cronjob part and the full `exp:audit`
are built and running on alpha. Every "Proposed:" point of the sections "Sinks", "Alerts" and "Rotation, archives
and retention" is built as written, with the deviations listed at the end.

### Try it

```bash
./console exp:audit sinks list --allow-root-user         # syslog ready (access, system), webhook needs a URL, mail
./console exp:audit alerts list --allow-root-user        # the seven built-in rules, their class, threshold and sinks
./console exp:audit alerts recipients --allow-root-user  # who would get alert mail, per rule
journalctl -t exponential -f                             # log in to the admin: the record appears (RFC 5424 line)
./console exp:audit verify --archives --allow-root-user  # live chains linked to the archives, manifests, HMACs
./console exp:audit archive --dry-run --allow-root-user  # what is older than LiveDays
./console exp:audit purge --dry-run --allow-root-user    # what is older than ArchiveDays
./console exp:audit search --files --name='access.session.*' --limit=10 --allow-root-user
./console exp:audit export --name='access.*' --format=bundle --out=var/tmp/audit-export --allow-root-user
./console exp:audit alerts test brute_force --replay=2026-10-01 --allow-root-user   # runs a rule, records nothing
./console exp:audit cron --allow-root-user               # one run of the cronjob part (--daily: the daily tasks now)
```

### What exists

| Part | Where |
|---|---|
| Sink registry, dispatch after the write (the record is in the file first), spools with `flock()`, retries with a doubling backoff, `system.audit.sink.failed` once per sink and hour, after-response delivery of critical records (PHP-FPM, commands) | kernel/classes/audit/sinks/expauditsinkregistry.php, expauditspool.php, expauditsinkbase.php |
| syslog/journald: RFC 5424 (`exp@32473`, MSGID = channel, escaped values); `Transport=local` uses journald's native socket where journald runs (identifier = `AppName`, fields `EXP_AUDIT_ID/NAME/CHANNEL/SEQ/HASH/RESULT/SEVERITY/REQUEST`), else `/dev/log`; `devlog`, `udp` (no body), `tcp`/`tls` (octet counting) | expauditsyslogsink.php |
| Webhook: batches of `BatchSize` or `BatchSeconds`, `X-Exponential-Timestamp`, `-Batch`, `-Signature: sha256=HMAC(secret, ts.body)`, the receiver's check `expAuditWebhookSink::verify()` (300 s) | expauditwebhooksink.php |
| Mail: through `eZMailTransport::send()` (site.ini mail settings, DebugSending) or `[AuditSink_mail] Transport=<class>`; from the cronjob part only; one mail per recipient, at most one per rule (or event name) and recipient per `Throttle`; recipients resolved by `expAuditMailRecipients` (see "Alert mail recipients" below) | expauditmailsink.php, expauditmailrecipients.php |
| Alert evaluator: at flush (only rules whose `Event` matches a written record read their state) and in the cronjob part (records since a cursor, the `.state` check for Audit=disabled, closed windows); `system.audit.alert` with the rule's severity, sent to its `Sinks[]` | kernel/classes/audit/alerts/expauditalertevaluator.php |
| Rule classes `threshold`, `match` (with `Policies[]`), `schedule` (`BusinessDays`, `BusinessHours`, the site's time zone); window state per rule `<LogDir>/alerts/<rule>.json` under `flock()`, keyed by event id | expauditthresholdrule.php, expauditmatchrule.php, expauditschedulerule.php, expauditalertrulebase.php, expauditalertstate.php |
| Format handlers gzip (zlib), bzip2 (ext-bz2), xz (binary), zstd (ext-zstd, else binary), zip (ext-zip, one file per archive); fallback to gzip | kernel/classes/audit/format/ |
| Archiver: due days, compress, read back and compare sha256, signed manifest per channel and day (`previous_manifest`, `key_id`, `hmac`), live file removed after; archive verification; restore; retention with a purge ledger | kernel/classes/audit/archive/expauditarchiver.php |
| Rotation by day: the first write of a new UTC day closes the day before (`system.audit.file.close`); `expAuditWriter::rotate()` closes a channel nobody wrote to | kernel/classes/audit/expauditwriter.php |
| Verifier: a first live file that starts from an archived file is checked against the manifest (no `no_origin`) | expauditverifier.php (`setOrigins()`) |
| Keys: `rotate()`, `rotatePseudonym()`, `listKeys()`, `verify()` (an id unknown to this process is looked up in the key file) | expauditkeys.php |
| Import of the 4.x logs (both header forms, rotated copies first, the `writeAudit()` mapping through `expAudit::legacyData()`, privacy applied, stable ids, re-import skipped, a grown file continued, originals into `ArchiveDir/legacy/` with a signed manifest) | kernel/classes/audit/archive/expauditimporter.php |
| Search over the files (the console's filters, `--subject-user`) and export as jsonl, csv or a signed bundle | expauditexporter.php |
| The scheduled work (every run: index, spools, alerts; daily after `RotateAfter`: rotate, verify, archive, purge, pseudonymise, checkpoint; `<LogDir>/.cron.lock`, `.cron-daily`) | expauditmaintenance.php |
| Cronjob part `Exponential\Cronjob\Kernel\Audit` | cronjobs/audit.php, kernel/private/classes/cronjobs/audit.php; settings/cronjob.ini `[CronjobPart-audit]` and the frequent group |
| `exp:audit` search, rotate, archive, restore, purge, reindex, pseudonymise, export, import, key, sinks, alerts, cron, verify `--archives` | kernel/private/classes/commands/audit.php |
| Whole INI blocks for the stage 5 settings (`block()`, `value()`, `lists()`, `hash()`, `path()`); `sinksAllowed()` | expauditconfig.php |

On alpha the cronjob part runs every minute in the `publishing` group (`settings/override/cronjob.ini.append.php`,
written with exp:ini, not committed): the first run did the daily tasks (both channels verified INTACT, recorded as
`system.audit.verify` with `via: cronjob`).

### Alert mail recipients (owner decision, 2026-10-02)

Mail alerts stay on on alpha. Who gets them is configured in audit.ini, never taken from the event:

| Recipient | Means |
|---|---|
| `admin` | site.ini `[MailSettings] AdminEmail` |
| `address:ops@example.com` (or a bare address) | that address |
| `group:security` | the named list `[AlertRecipients_security]`: `Addresses[]` and `Recipients[]` (any kind, other groups too; loops cut) |
| `user:14`, `login:editor1` | that user, with the e-mail address it has when the mail is sent |
| `usergroup:12`, `usergroup:<remote id>` | every enabled user below that user group (node id, or the node's or object's remote id), sub-groups included |
| `role:Administrator`, `role:<id>` | every enabled user the role is assigned to, directly or through a user group |

Which list: the rule's `[AlertRule_<rule>] Recipients[]`, else `[AuditAlertSettings] Recipients[]`, else
`[AuditSink_mail] Receivers[]` (the older name, same syntax), else `admin`; on alpha nothing is configured, so
alerts go to AdminEmail as before. Addresses are deduplicated case-insensitively, disabled users and invalid or
multi-line addresses are left out, and each recipient gets its own mail, at most one per rule (or event name) and
recipient within `Throttle`. `./console exp:audit alerts recipients [--rule=<rule>]` shows, per rule, the list in
effect, where it comes from, every address with the entries that produced it, and problems (an unknown group, a
disabled user, a missing role).

### Tests and proof

`php vendor/bin/phpunit tests/tests/kernel/classes/audit/`: the stage 5 tests below (all audit tests: 81, about 5 550 assertions); only
the recipients test uses the database; everything is
in `var/tmp/audit-tests/`.

| Test | Proves |
|---|---|
| `expAuditSinksTest` (E1) | the RFC 5424 line and the journald datagram; two test records under a test identifier found by `journalctl -t`; a webhook receiver on 127.0.0.1 (`ai/bin/one/audit_stage5_webhook_test_receiver.php`, started and stopped by the test) verifying every signature, 7 records in batches of 3+3+1, every id once and in order; a forced outage: the batch stays spooled, backoff 30 s then 60 s, `system.audit.sink.failed` after `Retries`, everything delivered when the receiver is back; a wrong secret and an old timestamp refused; mail through a test transport writing into the test directory (no real mail), 5 alerts spooled, 2 mailed (one per rule and recipient within `Throttle`) |
| `expAuditAlertsTest` (E2) | brute_force: 19 nothing, the 20th once, the 40th again, another network not counted, a new window again; brute_force_user 9/10; admin_role_granted for `*/*` and `setup/*`, not for content/read; settings_out_of_hours: 10:00 Friday nothing, ten writes at 23:00 one alert, Saturday another; mass_delete 499/500 with `children_omitted` counted; audit_disabled found by the cronjob pass and written although audit is off; chain_broken from a tampered file; an INI rule; a broken INI rule reported; the cronjob pass after a flush fires nothing more; replay records nothing |
| `expAuditArchiveTest` (E3, T9–T12) | rotation by day; each of gzip, bzip2, xz, zstd, zip (all available on this server): 3 days archived, live files removed, archives intact, the live chain intact from the archived file, a restored day byte-identical; T0 intact, T9 `archive_sha256`, T10 `hmac_invalid`, T11 `unknown_key`, T12 `previous_manifest`; retention dry run and real run, ledger, still intact; key rotation (k2 active, old manifests verify); the daily run |
| `expAuditImportTest` (E4) | both header forms, rotated copies first, imported/source/no chain, the address truncated, an unknown typed login hashed, HashKey never kept, a secret path cut; dry run; re-import skipped; a grown file continued; the legacy manifest's HMAC; originals kept or removed |
| `expAuditMailRecipientsTest` | live database: the test creates two nested user groups, three users (one disabled) and a role assigned to a user and to a group, and removes them afterwards (no existing user or role is changed). address, a bare address, admin; a line break or a list refused; named groups nested with a loop cut; user and login with the current address, a disabled user left out; usergroup by node id, object remote id and node remote id with sub-groups; role by name and id, directly and through the group; deduplication with every source kept; which list applies, and that an unknown rule name or addresses inside the event count for nothing; mail through a test transport: one mail per rule and recipient |

On alpha: `var/site/log/audit/login.log` (written once by Velocity's old code) was imported first into a test
directory (`ai/bin/one/audit_stage5_import_alpha_legacy_logs_into_test_dir.php`: 1 entry = 1 record, marked imported,
no chain, re-import skipped) and then for real with `--keep-originals`: `var/site/log/audit/imported/` and
`archive/legacy/`; a second run says "imported before". `rotate`, `archive` and `purge` with `--dry-run` report nothing
due (the oldest file is today's). The front page answers 200 on Apache and Velocity.

### Deviations from the text above

- `Transport=local` writes journald's native protocol where journald runs: journald 252 does not parse an RFC 5424
  header on `/dev/log` (the identifier is lost and `journalctl -t` finds nothing). The message is still the RFC 5424
  line; `Transport=devlog` sends it to `/dev/log` as is.
- `[AuditSink_mail] Transport=` (new): a mail transport class, empty for the kernel's. Recipients: `[AuditAlertSettings]
  Recipients[]`, `[AlertRule_*] Recipients[]` and `[AlertRecipients_<name>]` (new, owner decision); mail is throttled per
  rule and recipient, not per rule and group.
- Under test settings (`expAuditConfig::setOverride()`) no sink is used unless the override says `'sinks' => true`.
  One run of the stage 2 tests before that guard existed put 1346 test records (documentation addresses
  203.0.113.0/24, the test installation id, `_CMDLINE` phpunit) into the journal under `exponential`.
- `match` rules fire once per record (`GroupBy=id`, `Window=0` by default); `schedule` rules once per group and window
  (defaults `GroupBy=actor.user_id`, `Window=3600`) and do not fire again when the count doubles.
- Retention never removes a key from the settings: it reports the keys retained archives still need
  (`exp:audit purge`).
- Restored files go to `<LogDir>/restored/`, imported ones to `<LogDir>/imported/`; neither is a live file.
- `exp:audit rotate` also runs retention, as the table says; `purge` additionally prunes the index (stage 4's
  `purgeOld()`) when the index exists.
- The index work of the cronjob part (incremental run, pseudonymisation) calls stage 4's `expAuditIndexer`, which has
  its own lock, so it does not matter whether stage 4's own part runs too.
- Not measured yet: the cost of a syslog write at flush (stage 6, with P1–P4).
