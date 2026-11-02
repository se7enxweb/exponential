# Spec Delta

## Purpose

Defines what an editorial workflow is: a versioned document of stages and transitions, the rules
that make it valid, and how its versions move from draft to published to archived.

## ADDED Requirements

### Requirement: Definition document
A workflow definition SHALL be a JSON document with `identifier`, `name`, `start`, `stages`,
`transitions` and optional `layout` and `state_group`. The stored document SHALL NOT depend on
the editor library that produced it.

#### Scenario: Minimal definition is accepted
- **WHEN** a definition has identifier `news_article`, stages `draft` (kind `draft`) and `published`
  (kind `published`), start `draft` and one person transition `submit` from `draft` to `published`
  with `submit: true`
- **THEN** validation reports no error

#### Scenario: Layout does not affect validity
- **WHEN** two definitions differ only in `layout`
- **THEN** both validate with the same result

### Requirement: Identifiers
Stage ids and transition ids SHALL match `[a-z][a-z0-9_]{0,29}` and SHALL be unique within their
kind of element. The definition identifier SHALL follow the same pattern.

#### Scenario: Duplicate stage id
- **WHEN** two stages have the id `review`
- **THEN** validation reports an error on the second stage `review`

#### Scenario: Invalid transition id
- **WHEN** a transition has the id `Send-Back`
- **THEN** validation reports an error on that transition naming the allowed pattern

### Requirement: Stage kinds
Every stage SHALL have a `kind` of `draft`, `waiting`, `published` or `closed`.

#### Scenario: Unknown kind
- **WHEN** a stage has kind `pending`
- **THEN** validation reports an error on that stage

### Requirement: Start stage
`start` SHALL name an existing stage, and that stage SHALL NOT be of kind `published` or `closed`.

#### Scenario: Start names a missing stage
- **WHEN** `start` is `intake` and no stage `intake` exists
- **THEN** validation reports an error on the definition

#### Scenario: Start is a published stage
- **WHEN** `start` names a stage of kind `published`
- **THEN** validation reports an error on that stage

### Requirement: Reachability
Every stage SHALL be reachable from `start` along transitions, and at least one stage of kind
`published` SHALL be reachable.

#### Scenario: Orphan stage
- **WHEN** stage `legal` has no incoming transition and is not `start`
- **THEN** validation reports an error on stage `legal`

#### Scenario: No published stage reachable
- **WHEN** no stage of kind `published` can be reached from `start`
- **THEN** validation reports an error on the definition

### Requirement: Transition endpoints and trigger
Every transition SHALL have `from` and `to` naming existing stages and `by` set to `person`,
`job` or `timer`.

#### Scenario: Transition to a missing stage
- **WHEN** transition `approve` has `to` set to `approvd`
- **THEN** validation reports an error on transition `approve`

#### Scenario: Unknown trigger
- **WHEN** a transition has `by` set to `cron`
- **THEN** validation reports an error on that transition

### Requirement: Waiting stages lead somewhere
A stage of kind `waiting` SHALL have at least one outgoing transition.

#### Scenario: Dead-end waiting stage
- **WHEN** stage `review` (kind `waiting`) has no outgoing transition
- **THEN** validation reports an error on stage `review`

### Requirement: Submit transition
A person transition MAY carry `submit: true`. Each stage of kind `draft` SHALL have at most one
outgoing submit transition, and only transitions leaving a `draft` stage SHALL carry it.

#### Scenario: Two submit transitions from one draft stage
- **WHEN** stage `draft` has two outgoing transitions with `submit: true`
- **THEN** validation reports an error on the second of them

#### Scenario: Submit on a transition leaving a waiting stage
- **WHEN** a transition from a `waiting` stage carries `submit: true`
- **THEN** validation reports an error on that transition

### Requirement: Person transition options
A person transition SHALL accept `roles` (role ids, empty means the policy alone decides) and
`comment` of `none`, `optional` or `required` (default `none`). Every role id SHALL exist.

#### Scenario: Unknown role
- **WHEN** transition `approve` lists role id 9999 and no role 9999 exists
- **THEN** validation reports an error on transition `approve` naming role 9999

#### Scenario: Invalid comment mode
- **WHEN** a transition has `comment` set to `always`
- **THEN** validation reports an error on that transition

### Requirement: Review option shape
On person transitions leaving a `waiting` stage, `quorum` (`min` ≥ 1), `veto` (boolean),
`author_selects` (`from_groups` of existing user groups, 1 ≤ `min` ≤ `max`) and
`reviewers_may_edit` (boolean) SHALL be accepted when well-formed and reported otherwise.

#### Scenario: Quorum on a transition leaving a draft stage
- **WHEN** a transition from a `draft` stage carries `quorum`
- **THEN** validation reports an error on that transition

#### Scenario: Author selection with min above max
- **WHEN** `author_selects` has `min` 3 and `max` 2
- **THEN** validation reports an error on that transition

### Requirement: Job transitions
A job transition SHALL have `on` set to `done` or `failed` and SHALL only leave a stage whose
`on_enter` holds exactly one job action. A stage with a job action SHALL have both a `done` and a
`failed` transition.

#### Scenario: Job transition without a job action
- **WHEN** transition `ai_ok` (by `job`) leaves stage `draft`, whose `on_enter` holds no job action
- **THEN** validation reports an error on transition `ai_ok`

#### Scenario: Missing failure path
- **WHEN** stage `ai_draft` has a job action and only a `done` transition
- **THEN** validation reports an error on stage `ai_draft`

### Requirement: Timer transitions
A timer transition SHALL have `at` as either `{ "attribute": <identifier> }` with optional
`fallback` `now` or `never`, or `{ "after": <ISO 8601 duration> }`. An optional `remind` SHALL
have `before` as an ISO 8601 duration and `to` naming a recipient role.

#### Scenario: Invalid duration
- **WHEN** a timer transition has `at` `{ "after": "2 days" }`
- **THEN** validation reports an error on that transition

#### Scenario: Both attribute and duration
- **WHEN** `at` holds both `attribute` and `after`
- **THEN** validation reports an error on that transition

### Requirement: Timer attributes match bound classes
For a timer transition on an attribute, every class bound to the definition's identifier SHALL
have an attribute of that identifier with a date or datetime datatype.

#### Scenario: Bound class lacks the attribute
- **WHEN** the definition is bound to class `article` and timer `expire` uses attribute
  `expiry_date`, which `article` does not have
- **THEN** validation reports an error on transition `expire` naming class `article`

#### Scenario: Attribute of the wrong type
- **WHEN** `expiry_date` on a bound class is a text line
- **THEN** validation reports an error on transition `expire`

### Requirement: Actions and assignees
Action names in `on_enter` and `actions` SHALL be configured action names. Group ids in a stage
`assignee` SHALL exist.

#### Scenario: Unknown action
- **WHEN** a stage's `on_enter` holds action `tweet`, which is not configured
- **THEN** validation reports an error on that stage naming `tweet`

#### Scenario: Unknown assignee group
- **WHEN** stage `review` has `assignee.groups` `[ 123 ]` and no user group 123 exists
- **THEN** validation reports an error on stage `review`

### Requirement: Complete error report
Validation SHALL report every finding in one pass. Each finding SHALL name its target (the
definition, a stage id or a transition id) and a message.

#### Scenario: Several errors at once
- **WHEN** a definition has an unknown `start` and a transition to a missing stage
- **THEN** validation reports both findings, one targeting the definition and one targeting the
  transition

### Requirement: Definition versions
Saving a definition SHALL store a new draft version numbered one above the highest existing
version of its identifier. A draft version SHALL be stored even when it has validation errors,
and the errors SHALL be returned with it.

#### Scenario: First save
- **WHEN** a definition `news_article` is saved for the first time
- **THEN** version 1 exists with status draft

#### Scenario: Save with errors
- **WHEN** a definition with an unknown `start` is saved
- **THEN** a draft version is stored and the save returns the validation error

### Requirement: Publishing a version
Publishing SHALL be refused for a version with validation errors. When a version is published,
the previously published version of the same identifier SHALL become archived, so that at most one
version per identifier is published. Published and archived versions SHALL NOT be changed.

#### Scenario: Publish replaces the published version
- **WHEN** version 2 of `news_article` is published while version 1 is published
- **THEN** version 2 is published and version 1 is archived

#### Scenario: Invalid version cannot be published
- **WHEN** publishing a version that has validation errors
- **THEN** publishing is refused with those errors and the published version is unchanged

#### Scenario: Published version is immutable
- **WHEN** a change to the body of a published version is saved
- **THEN** a new draft version is created and the published version is unchanged
