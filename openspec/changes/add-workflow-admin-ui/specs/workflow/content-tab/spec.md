# Spec Delta

## Purpose

Shows, for one content object, where its versions are in their workflow and what the user can do next.

## ADDED Requirements

### Requirement: Workflow tab
The content view SHALL have a Workflow tab showing a small read-only graph with the current stage highlighted,
the history, the pending timers and the job the instance waits for, with a link to that job.

#### Scenario: Waiting for a job
- **WHEN** an editor opens the Workflow tab of an article whose instance waits for its AI draft
- **THEN** "AI draft" is highlighted and the waiting job is linked

### Requirement: Acting from the tab
The tab SHALL offer the transitions the user may fire, with the comment field each asks for, and "Start workflow"
for a draft without an instance when the user may start workflows. Actions SHALL be protected against cross-site
requests.

#### Scenario: Reviewer approves
- **WHEN** a reviewer fires "approve" with a comment from the tab
- **THEN** the instance moves on and the tab shows the new stage and the history entry

#### Scenario: Request without token
- **WHEN** a request to fire a transition arrives without the form token
- **THEN** it is refused
