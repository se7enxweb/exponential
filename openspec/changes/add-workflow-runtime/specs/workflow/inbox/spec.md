# Spec Delta

## Purpose

Puts the work a stage asks of people into the administration's collaboration inbox, where they can act on it.

## ADDED Requirements

### Requirement: A task per waiting stage
When an instance enters a stage with assignees, a collaboration item SHALL be created for the author and the
stage's assignee users and groups, and they SHALL be notified. The item SHALL be closed when the instance leaves
the stage.

#### Scenario: Review task
- **WHEN** an instance enters "In review", assigned to the group Editors
- **THEN** every member of Editors and the author see the task in their inbox and get a notification

### Requirement: Inbox state
The inbox SHALL show the item as waiting while the stage waits, and as approved or denied after the instance
moved on, like the kernel's approval items.

#### Scenario: After approval
- **WHEN** a reviewer fires "approve"
- **THEN** the item is shown as approved

### Requirement: Transitions in the inbox
The item SHALL offer the person transitions of the current stage that the viewing user may fire, with the comment
field the transition asks for. Firing from the inbox SHALL behave as firing from the content's Workflow tab.

#### Scenario: Author sees no approval
- **WHEN** the author opens the review task
- **THEN** the author sees the task but no "approve" or "send_back" action
