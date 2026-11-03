# Spec Delta

## Purpose

The actions a definition can run when a stage is entered or a transition fires, which actions exist, and where
their side effects happen.

## ADDED Requirements

### Requirement: Configured actions
Actions SHALL be configured by name, each with its implementation; extensions SHALL be able to add actions by
configuration alone. An action SHALL check its own parameters and say whether it runs as a job.

#### Scenario: Action added by an extension
- **WHEN** an extension configures an action "slack.post"
- **THEN** definitions can use "slack.post" and the editor offers it

### Requirement: Order of actions
On a transition, the transition's actions SHALL run first and then the target stage's on-enter actions, each in
the order the definition lists them.

#### Scenario: Notify, then translate
- **WHEN** "approve" has the actions notify and translation check and the target stage has none
- **THEN** the notification is queued before the translation check

### Requirement: Side effects only in workers
A job action SHALL run in a queue worker, never in a web request. Running it twice for the same instance, stage
and position SHALL NOT queue it twice.

#### Scenario: Double click
- **WHEN** an editor fires a transition into a stage with a job action and the request is sent twice
- **THEN** one job is queued
