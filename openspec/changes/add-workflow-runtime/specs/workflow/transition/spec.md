# Spec Delta

## Purpose

Moves an instance from one stage to the next, the same way whether a person, a job result, a timer, the system
or a migration fires the transition.

## ADDED Requirements

### Requirement: One way to fire
Every transition SHALL be fired the same way regardless of what fires it (person, job, timer, system,
migration): checks first, then the history entry, then entering the target stage.

#### Scenario: Timer and person take the same path
- **WHEN** a timer fires "go_live" and later a person fires "send_back" on another instance
- **THEN** both instances have a history entry and entered their target stage in the same order of steps

### Requirement: Person transitions need permission
A person SHALL fire a transition only with the transition permission, narrowed by its limitations (workflow,
transition, class, section, subtree), and, when the transition names roles, only while holding one of them.
Without roles the permission alone SHALL decide. A transition the user may not fire SHALL NOT be offered.

#### Scenario: Role missing
- **WHEN** "approve" names the roles Editor-in-chief and Administrator and an author without them views the stage
- **THEN** "approve" is not offered and a direct request to fire it is refused

#### Scenario: Limited to one workflow
- **WHEN** a user's permission is limited to the workflow "Translation review"
- **THEN** the user cannot fire transitions of "News article"

### Requirement: Comments
A person transition SHALL ask for a comment according to its setting: none, optional or required. A required
comment SHALL be enforced on the server.

#### Scenario: Send back without a comment
- **WHEN** a reviewer fires "send_back", whose comment is required, with an empty comment
- **THEN** the transition does not fire and the reviewer is asked for a comment

### Requirement: Stale fire is refused
Firing SHALL only succeed when the instance is still in the stage and state the user saw. A transition fired
after another transition already moved the instance SHALL be refused without effect.

#### Scenario: Two reviewers at once
- **WHEN** two reviewers fire "approve" and "send_back" on the same instance at the same moment
- **THEN** exactly one transition takes effect and the other is refused as outdated

### Requirement: Machine-fired transitions check their source
A job transition SHALL fire only for the job the instance waits for; a timer transition SHALL fire only for a
pending timer of the current stage.

#### Scenario: Old job result
- **WHEN** the result of a job arrives after the instance already left the stage that started it
- **THEN** no transition fires

### Requirement: History
Every fired transition SHALL be recorded with the instance, the stage before and after, the transition, what
fired it, the user, the comment and, for job transitions, the job. The history SHALL be readable on the content.

#### Scenario: Send back recorded
- **WHEN** a reviewer sends a version back with the comment "Add sources"
- **THEN** the history shows the reviewer, "In review" → "Draft", "send_back" and the comment

### Requirement: Leaving a stage cancels its timers
When an instance leaves a stage, the open timers of that stage SHALL be cancelled.

#### Scenario: Approved before the reminder
- **WHEN** a stage has a pending reminder timer and the instance leaves the stage
- **THEN** the timer is cancelled and the reminder is never sent
