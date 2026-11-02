# Spec Delta

## Purpose

Lets the stage kind of an instance decide whether the publish operation of a content version goes ahead, waits
or is cancelled, in the same way the kernel's approval event does.

## ADDED Requirements

### Requirement: No binding, no change
A version without a matching binding SHALL publish exactly as without the workflow extension.

#### Scenario: Unbound class
- **WHEN** a folder is published and no rule binds a workflow to folders
- **THEN** the folder is published at once

### Requirement: Waiting stages defer publishing
While the instance is in a waiting stage, the publish operation SHALL be deferred and the version SHALL stay
pending; publishing SHALL be retried by the regular workflow cron run.

#### Scenario: In review
- **WHEN** an article version is sent for publishing and its instance enters "In review"
- **THEN** the version is pending and not published

### Requirement: Published stage completes publishing
When the instance reaches a published stage, the deferred publish operation SHALL complete on the next run of
the workflow cron part. The documentation SHALL state this latency.

#### Scenario: Approved and live
- **WHEN** the instance enters "Published" and the workflow cron part runs
- **THEN** the version becomes the published version

### Requirement: Send back cancels publishing
When the instance returns to a draft stage while publishing waits, the version SHALL return to draft status and
the publish operation SHALL be cancelled, as the kernel's approval does on a denial.

#### Scenario: Sent back
- **WHEN** a reviewer fires "send_back" while the version is pending
- **THEN** the version is a draft again, the author can edit it, and nothing is published

### Requirement: Gate installation
An installer SHALL put the gate on the "before publish" trigger: it SHALL create a workflow with the gate when
the trigger has none, append the gate to the existing workflow otherwise, never add it twice, and report without
changing anything in check mode.

#### Scenario: Existing approval workflow
- **WHEN** the "before publish" trigger already runs a workflow with an approval event
- **THEN** the installer appends the gate to that workflow and a second run changes nothing
