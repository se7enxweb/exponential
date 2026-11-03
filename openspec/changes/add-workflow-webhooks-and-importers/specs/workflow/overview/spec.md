# Spec Delta

## ADDED Requirements

### Requirement: Replaceable extensions
The overview SHALL list other workflow extensions that are still active (ezapprove2, OWEditorial, xrowworkflow,
hideuntildate, ezapprovetanta, ocwebhookserver, oceditorialstuff), each with the importer or the feature that
replaces it, and webhooks with failing deliveries.

#### Scenario: OWEditorial still active
- **WHEN** OWEditorial is active
- **THEN** the overview lists it with the OWEditorial importer
