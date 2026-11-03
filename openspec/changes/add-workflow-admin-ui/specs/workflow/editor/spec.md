# Spec Delta

## Purpose

Lets administrators list, draw, check and save workflow definitions in the administration.

## ADDED Requirements

### Requirement: Workflow list
Users with the design permission SHALL see a list of definitions with their published version, their bindings and
the running instances per stage, and SHALL be able to edit, duplicate and archive a definition.

#### Scenario: Running instances shown
- **WHEN** an administrator opens the workflow list
- **THEN** "News article" shows its published version, its bindings and how many instances wait in each stage

### Requirement: Drawing a definition
The editor SHALL show stages as boxes and transitions as labelled arrows coloured by what fires them, with a side
panel for the selected stage or transition, a palette to add stages and person, job and timer transitions, and
the bindings above the canvas.

#### Scenario: Add a transition
- **WHEN** an administrator adds a timer transition from "Approved" to "Published"
- **THEN** an arrow appears in the timer color and the side panel asks for the date attribute

### Requirement: Saving a new version
Saving SHALL store the drawing as a new definition version in the stored format, with the positions of the boxes,
independent of the drawing library. Discard SHALL drop unsaved changes.

#### Scenario: Save as version 3
- **WHEN** an administrator changes version 2 and chooses "Save as v3"
- **THEN** version 3 is stored as a draft and version 2 stays published

### Requirement: Errors where they are
Validation SHALL run on the server, on demand and on save, and every finding SHALL be shown at the stage or arrow
it concerns; a definition with errors SHALL NOT be published.

#### Scenario: Unreachable stage
- **WHEN** a stage has no incoming arrow from the start
- **THEN** the stage is marked with the reachability error and publishing is refused

### Requirement: Content security
The editor SHALL run under the administration's content security policy without inline scripts or styles, and the
drawing library SHALL be served from the site itself.

#### Scenario: Strict policy
- **WHEN** the content security policy is enforced in Chrome, Firefox and Safari
- **THEN** stages can be added, moved and connected without policy violations
