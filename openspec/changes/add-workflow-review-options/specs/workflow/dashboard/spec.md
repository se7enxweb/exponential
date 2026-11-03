# Spec Delta

## Purpose

An editorial board that shows editors what is in which stage of a workflow and what they can do about it.

## ADDED Requirements

### Requirement: Board per definition
Users with the dashboard permission SHALL see one board per definition with a column per stage and a card per
running instance, showing name, class, author, assignees, time in the stage, a due timer and votes.

#### Scenario: Review column
- **WHEN** an editor opens the board of "News article"
- **THEN** the column "In review" shows the waiting articles with their time in review and their votes

### Requirement: Filters and paging
The board SHALL filter by class, subtree, language, "assigned to me" and overdue, and SHALL be paginated over
instances.

#### Scenario: Assigned to me
- **WHEN** a reviewer chooses "assigned to me"
- **THEN** only instances whose current stage assigns the reviewer are shown

### Requirement: Only readable content
The board SHALL show and count only content the user may read.

#### Scenario: Hidden subtree
- **WHEN** an instance belongs to content the editor may not read
- **THEN** it is neither shown nor counted

### Requirement: Acting from a card
A card SHALL offer the transitions the user may fire as buttons and open the content's Workflow tab; the board
SHALL NOT move instances by drag and drop.

#### Scenario: Approve from the board
- **WHEN** a reviewer chooses "approve" on a card
- **THEN** the transition fires as from the Workflow tab
