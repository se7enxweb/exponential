# Spec Delta

## Purpose

Editors see which fields still hold AI-written text that nobody has checked, and the AI service never overwrites
what a person wrote.

## ADDED Requirements

### Requirement: AI-written fields are marked
When the AI service writes the value of a field, the field SHALL carry an "AI — check" mark in the edit form and
in the administration's full view, for that object, language and version.

#### Scenario: Teaser written by AI
- **WHEN** an editor lets the AI service fill the teaser of an article
- **THEN** the teaser shows the mark in the edit form and, after publishing, in the full view

#### Scenario: Other language unaffected
- **WHEN** the AI service fills the German teaser only
- **THEN** the English teaser shows no mark

### Requirement: A person clears the mark
A person's change to a marked field, or an explicit "Checked" on it, SHALL remove the mark.

#### Scenario: Edited by hand
- **WHEN** an editor changes one word in the marked teaser and stores the draft
- **THEN** the teaser no longer shows the mark

#### Scenario: Checked without change
- **WHEN** an editor reads the teaser and clicks "Checked"
- **THEN** the mark disappears and the text is unchanged

### Requirement: Human input is protected
A later AI run SHALL replace only fields that still carry the mark or are empty; it SHALL NOT replace a field a
person has written or checked.

#### Scenario: Refill after a manual edit
- **WHEN** an editor asks the AI service to fill all fields of an article whose teaser a person has edited
- **THEN** the teaser stays as the person wrote it and the other marked or empty fields are filled

### Requirement: The mark follows the version
A mark SHALL be carried to a new version as long as the field is not changed in it.

#### Scenario: New version for another field
- **WHEN** an editor creates a new version and changes only the title
- **THEN** the AI-written teaser still shows the mark in the new version

### Requirement: AI field actions need edit access
AI actions that fill or rewrite fields SHALL be offered only to users who may edit that version; a user who may
only read it SHALL NOT be able to start them.

#### Scenario: Reader
- **WHEN** a user with read access only opens an article in the administration
- **THEN** no AI action that fills or rewrites fields is offered
