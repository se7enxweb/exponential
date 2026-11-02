# Spec Delta

## Purpose

Shows the stage of each content object as an object state, so lists, search and permissions can use it, while
the instance stays the source of truth.

## ADDED Requirements

### Requirement: One state per stage
Each definition SHALL have one object state group with one state per stage: a group named after the definition,
or an existing group the definition names. Entering a stage SHALL set the matching state.

#### Scenario: Entering review
- **WHEN** an instance of "News article" enters "In review"
- **THEN** the object's state in the group of "News article" is "In review"

#### Scenario: Existing group reused
- **WHEN** a definition names an existing state group with a state for each of its stages
- **THEN** no new group is created and the existing states are set

### Requirement: States set by the system user
Stage states SHALL be set as a configured system user, so job and timer transitions set them although no person
with the right to assign states fires them. The installer's check SHALL report a system user who lacks that right.

#### Scenario: Timer at night
- **WHEN** a timer moves an instance to "Expired"
- **THEN** the object's state is "Expired" although no user is signed in

### Requirement: Manual changes are drift
A stage state changed by hand SHALL NOT change the instance. The change SHALL be recorded as drift and shown to
administrators, and SHALL NOT be corrected automatically.

#### Scenario: Administrator sets a state
- **WHEN** an administrator sets the object's state to "Published" while its instance waits in "In review"
- **THEN** the instance stays in "In review" and the overview reports the drift
