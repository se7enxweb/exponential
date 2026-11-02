# Spec Delta

## Purpose

Moves an instance on by the result of the job its stage started, for success and for failure, without a person.

## ADDED Requirements

### Requirement: Waiting for the job
When an instance enters a stage whose on-enter actions hold a job action, it SHALL queue the job, remember it as
the job it waits for, and watch it until the job is finished.

#### Scenario: AI draft queued
- **WHEN** an instance enters "AI draft"
- **THEN** the AI draft job is queued and the Workflow tab shows the instance waiting for it

### Requirement: Done fires the done transition
When the job finishes successfully, the stage's transition for "done" SHALL fire without user action.

#### Scenario: Draft written
- **WHEN** the AI draft job finishes successfully
- **THEN** the instance moves to "Draft"

### Requirement: Failure fires the failed transition
When the job fails for good or is cancelled, the stage's transition for "failed" SHALL fire without user action.
A failed parent job SHALL NOT be able to cancel the failure path.

#### Scenario: Draft failed
- **WHEN** the AI draft job ends in the dead letter queue
- **THEN** the instance moves to "Write manually"

#### Scenario: Job cancelled by an administrator
- **WHEN** an administrator cancels the waiting job
- **THEN** the instance moves along its "failed" transition
