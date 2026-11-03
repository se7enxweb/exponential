# Design

## Context

See proposal.md for the motivation. The build spec (`openspec/handoff/expworkflow-16702-spec.md`) fixes
the definition format (section 3), the tables (section 4) and the binding idea (section 5). This
change builds the parts that need no runtime: format, validation, versions, binding resolution and
storage. The extension `expqueue` already solves multi-engine storage with
`expQueueSchema` (`definition()`, `handler()`, `ensure()`, `upgradeStatements()`) and console
commands with `expQueueCommand` (`./console ext:expqueue:<name>`, files under `bin/php/` that only
call `main( '<name>' )`); this change follows both patterns instead of inventing new ones.

## Goals / Non-Goals

**Goals:**
- One PHP representation of a definition that the runtime, the editor converter and the importers
  of later milestones all use.
- Validation as a pure function of (definition, environment lookups), testable without a database.
- All nine tables installed once, so later milestones only add columns if they must.

**Non-Goals:**
- No action classes (only the configured list of names), no instances, no policies, no UI.

## Architecture rules this change keeps

- **No kernel changes.** Everything lives in `extension/expworkflow/`.
- **Tests never touch a site database.** Unit tests are kernel-free; database tests use the
  throwaway databases of `tests/toolkit/` (`ezpDatabaseTestCase`, group `database`).
- **Fail visibly.** Validation returns every finding; nothing is silently dropped or defaulted
  except values the format defines a default for (`comment` = `none`, `priority` = 0).

## Decisions

### D1 Classes

| Class | Responsibility |
|---|---|
| `expWorkflowDefinition` | value object of one version: parse JSON, expose stages/transitions, `toArray()` |
| `expWorkflowValidator` | `validate( expWorkflowDefinition, expWorkflowEnvironment ) : expWorkflowFinding[]` |
| `expWorkflowEnvironment` | interface for lookups the validator needs: role exists, user group exists, class attribute datatype, configured action names, classes bound to an identifier. One kernel implementation, one array-backed implementation for tests |
| `expWorkflowFinding` | target type (`definition`, `stage`, `transition`), target id, message key, parameters |
| `expWorkflowDefinitionStore` | save draft version, publish, archive, fetch published/by version, list |
| `expWorkflowBinding` | persistent object of one rule |
| `expWorkflowBindingResolver` | `resolve( $object, $version ) : ?expWorkflowDefinition` plus `check() : expWorkflowFinding[]` (ties, rules without published definition) |
| `expWorkflowSchema` | like `expQueueSchema` |
| `expWorkflowCommand` | `./console ext:expworkflow:<name>`, first command `install-schema` |

Alternative considered: validation inside `expWorkflowDefinition`. Rejected: the validator needs
kernel lookups, and the definition object should load in kernel-free tests and in the converter
without them.

### D2 Findings carry message keys, not text

A finding holds a key and parameters (`unknown_role`, `{ role: 9999 }`); text comes from the
translation context `extension/expworkflow`. The editor of milestone 4 places findings by target;
the CLI prints them. Alternative: plain English strings. Rejected: the admin is translated into
many languages, and tests compare keys, not wording.

### D3 Drafts may be invalid, publishing may not

Save stores a draft version even with findings and returns them; publish refuses a version with
findings. This lets an editor save work in progress. The build spec says only "validate on the
server, and show errors"; this is the interpretation chosen.

### D4 Publishing is one transaction

Publish runs in one DB transaction: re-validate, set the previous published version of the
identifier to archived, set this one to published. Exactly one published version per identifier
cannot be a portable unique index (it is conditional on status), so the transaction plus a
re-read check after commit enforce it; a test publishes twice concurrently on SQLite and MySQL.

### D5 Binding specificity is lexicographic

The build spec ranks class + subtree > class > subtree > default and says section and language
"add specificity on top". Chosen order: (1) base level, (2) subtree depth for subtree rules,
(3) count of section/language qualifiers, (4) priority, (5) lowest rule id. So a class rule with a
language never beats a class + subtree rule. Alternative: a summed score. Rejected: a sum makes two
qualifiers outweigh a subtree, which contradicts "the same idea as override.ini matching".
**Decided 2026-11-02: the recommended lexicographic order stands; no confirmation on the ticket is needed.**

### D6 Language means the version's initial language

`eZContentObjectVersion::initialLanguageCode()` decides a language rule, as the build spec says
for case 2. A rule without language matches every language.

### D7 Timer attribute checks run in both directions

On publish of a definition: against the classes of all enabled rules for its identifier. On adding
or enabling a rule: against the published version of its identifier. Class `*` rules are not
checked against every class (that would forbid any timer on a default workflow); a timer on a
class without the attribute behaves as `fallback` at runtime (milestone 3), and the overview
(milestone 4) lists such classes.

### D8 Storage

- Tables (section 4 of the build spec): `expwf_definition`, `expwf_binding`, `expwf_instance`,
  `expwf_history`, `expwf_timer`, `expwf_vote`, `expwf_reviewer`, `expwf_hook`, `expwf_delivery`,
  all created by this change.
- `expWorkflowSchema::definition()` in the kernel's DBA format; `handler()` maps the engine to the
  kernel schema class, as `expQueueSchema::handler()` does (including the Oracle class loaded from
  the ezoracle extension when present). MongoDB gets `sql/mongodb/schema.json` from a
  `mongoDescription()` like `expQueueSchema::mongoDescription()`; the kernel's `expMongoSchema`
  creates the collections and indexes.
- Nullable columns of the build spec (`subtree_path`, `section_id`, `language_code`) are stored as
  `''`/0 with "not set" meaning, the expqueue convention, so Oracle's `'' = NULL` makes no
  difference to reads.
- `body` (JSON) is `longtext`.
- On an existing installation the tables are created by `./console ext:expworkflow:install-schema
  -s <siteaccess>` after activating the extension; INSTALL.md says so.

### D9 Settings

`extension/expworkflow/settings/expworkflow.ini.append.php`:
- `[Actions] List[]` — the action names validation accepts. Milestone 1 ships the names of the
  built-in actions (`notify`, `hide`, `unhide`, `webhook`, `expai.draft`,
  `expai.translation.check`) without classes; milestone 3 adds `[Action_<name>] Class=`.
- `[Runtime]` and `[Webhook]` sections are created by the milestones that read them.

No policy is added in this change; it has no web surface.

## Risks / Trade-offs

- [D5 is an interpretation of the build spec] → decided for the lexicographic order; the resolver keeps
  the order in one comparator, so revisiting it changes one function and its tests.
- [Nine tables before their code exists] → later milestones may still need columns;
  `upgradeStatements()` adds them on existing installations, which the storage spec requires.
- [D4 on MongoDB] → expqueue enforces its idempotency there under a lock instead of a
  transaction; publish does the same. Measure before relying on it; documented in `doc/index.md`.

## Migration Plan

New extension, nothing to migrate. Activate, run `install-schema`, done. Rollback: deactivate; the
tables stay and do nothing.
