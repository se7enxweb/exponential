# Spec Delta

## ADDED Requirements

### Requirement: Action parameters are checked
When a definition is saved, every action SHALL check its parameters, and each finding SHALL be reported at the
stage or transition that uses the action, together with the other validation errors.

#### Scenario: Notify without a recipient
- **WHEN** a transition's notify action has no recipient
- **THEN** saving reports the missing recipient at that transition
