# Spec Delta

## Purpose

Runs a published workflow definition for one content version: when an instance starts, which definition
version it follows, how running instances move to a newer version, and when an instance is finished.

## ADDED Requirements

### Requirement: Start through the publish gate
When a content version with a matching binding reaches the publish gate and has no running instance, an
instance SHALL start at the definition's start stage. If the start stage is a draft stage, its submit transition
SHALL fire at once, as if the author had sent the version for publishing.

#### Scenario: Send for publishing starts the workflow
- **WHEN** an author sends a new article version for publishing and a rule binds "News article" to articles whose start stage is a draft stage with a submit transition to "In review"
- **THEN** an instance starts and the version is in "In review"

### Requirement: Explicit start
A user with the permission to start workflows SHALL be able to start an instance for a draft version before it
is sent for publishing, from the content's Workflow tab or through the API. The start SHALL use the same binding.

#### Scenario: Start before writing
- **WHEN** an editor starts the workflow on a new draft whose start stage runs an AI draft
- **THEN** an instance starts at that stage and the draft is not sent for publishing

#### Scenario: No permission to start
- **WHEN** an editor without the start permission opens the Workflow tab of a draft
- **THEN** no "Start workflow" action is offered

### Requirement: One running instance per version
A content version SHALL have at most one running instance. A second start for the same version SHALL be refused.

#### Scenario: Second start refused
- **WHEN** a version already has a running instance and a start is requested again
- **THEN** the request is refused and the running instance is unchanged

### Requirement: Pinned definition version
An instance SHALL follow the definition version it started with. Publishing a newer version of the definition
SHALL NOT change running instances.

#### Scenario: New version published
- **WHEN** version 2 of "News article" is published while an instance of version 1 waits in "In review"
- **THEN** that instance keeps following version 1

### Requirement: Moving running instances
An administrator SHALL be able to move the running instances of a definition to a newer version. Stages SHALL
be mapped by their id; an instance whose current stage no longer exists SHALL be refused and listed, and the
move SHALL be recorded in each moved instance's history.

#### Scenario: Stage removed in the new version
- **WHEN** instances are moved to version 3 and one of them is in stage `legal`, which version 3 no longer has
- **THEN** that instance stays on its version, the others move, and the refused one is reported

### Requirement: Finishing an instance
An instance SHALL be finished when it enters a stage without outgoing transitions, or a closed stage after a
published stage. A finished instance SHALL keep its history.

#### Scenario: Last stage reached
- **WHEN** an instance enters "Expired", which has no outgoing transition
- **THEN** the instance is finished and its history stays readable
