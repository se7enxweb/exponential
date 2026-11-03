# Spec Delta

## Purpose

Runs translations through their own workflow per language, so that the source language never waits for its
translations.

## ADDED Requirements

### Requirement: One instance per language version
Every language version SHALL get its own instance, bound by a rule with that language; an instance SHALL NOT run
parallel paths for several languages.

#### Scenario: Two target languages
- **WHEN** English and French versions of an approved German article are created
- **THEN** each gets its own "Translation review" instance

### Requirement: The source never waits
Publishing the source language version SHALL NOT wait for any translation, and a translation's review SHALL NOT
change the source's instance.

#### Scenario: French still in review
- **WHEN** the German source is approved while the French translation waits in review
- **THEN** the German version is published and the French one keeps waiting

### Requirement: Machine translation then human review
A translation produced by the AI service SHALL enter its language's workflow at the review stage, whether it was
published through the publish operation or written another way.

#### Scenario: Machine translation arrives
- **WHEN** the English machine translation of an article is stored
- **THEN** its instance waits in "Review" for a human reviewer
