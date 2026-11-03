# Spec Delta

## Purpose

Review rules for person transitions out of a waiting stage: several approvers, a veto, reviewers chosen by the
author, and reviewers who may edit the version they review.

## ADDED Requirements

### Requirement: Votes
For a transition with a quorum, each user allowed to fire it SHALL cast at most one vote per instance and stage;
the votes SHALL be shown with the count needed. Re-entering the stage SHALL clear its votes.

#### Scenario: One of two votes
- **WHEN** "approve" needs two votes and the first reviewer chooses it
- **THEN** the stage shows 1/2 and the instance stays in review

#### Scenario: Second round
- **WHEN** the version is sent back and submitted again
- **THEN** the review starts with no votes

### Requirement: Veto
A competing transition marked as veto SHALL fire at once on a single vote, regardless of the votes cast for other
transitions.

#### Scenario: Send back with veto
- **WHEN** one reviewer has approved and another chooses "send_back", marked as veto
- **THEN** the version goes back to draft at once

### Requirement: Reviewers chosen by the author
For a stage with author-selected reviewers, the author SHALL choose between the minimum and maximum number of
reviewers from the configured groups when submitting; the chosen users SHALL become the stage's assignees for this
instance instead of the stage's default assignees.

#### Scenario: Author picks two
- **WHEN** the author submits and picks two members of the group Reviewers
- **THEN** only those two get the review task

#### Scenario: Too few chosen
- **WHEN** the stage needs at least one reviewer and the author picks none
- **THEN** submitting is refused with a message

### Requirement: Reviewers may edit
Where allowed, assignees SHALL be able to edit the pending version while the stage waits. An edit by a reviewer
SHALL clear the votes cast so far, so the approved content is what every voter saw.

#### Scenario: Reviewer corrects a typo
- **WHEN** one reviewer has approved and another reviewer edits the pending version
- **THEN** the approval is cleared and both must vote again
