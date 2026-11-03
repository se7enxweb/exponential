# Spec Delta

## Purpose

Moves existing approval, scheduling and editorial workflow setups into workflow definitions, so a site can switch
to one workflow extension.

## ADDED Requirements

### Requirement: One source per run
An importer SHALL exist for the kernel approval, the kernel wait-until-date, ezapprove2, OWEditorial, and
xrowworkflow with hideuntildate; each run SHALL import from one source of one siteaccess, reading only settings and
the kernel's tables, also when the source extension is not active.

#### Scenario: Source extension removed
- **WHEN** OWEditorial is no longer active but its settings file and state group still exist
- **THEN** the OWEditorial importer reads them and creates definitions

### Requirement: Draft definitions only
An importer SHALL create draft definitions and bindings for review in the editor; it SHALL NOT publish them and
SHALL NOT remove or change the old setup.

#### Scenario: Approval imported
- **WHEN** a kernel approval setup for articles in section Standard is imported
- **THEN** a draft definition draft → review → published with send-back exists, bound to articles in that section, and the approval event is unchanged

### Requirement: Dry run
A dry run SHALL print the definitions and bindings an import would create, without writing anything.

#### Scenario: Look first
- **WHEN** an administrator runs the importer with the dry-run option
- **THEN** the definitions are printed and nothing is stored

### Requirement: Nothing guessed
A setting that a definition cannot express SHALL be reported with its source and SHALL NOT be guessed.

#### Scenario: Unsupported setting
- **WHEN** an ezapprove2 event uses a setting without an equivalent
- **THEN** the import report names that setting and the event it comes from

### Requirement: Existing state groups reused
An OWEditorial import SHALL reuse its existing state group instead of creating a new one.

#### Scenario: States kept
- **WHEN** an OWEditorial workflow with its state group is imported
- **THEN** the definition names that state group and objects keep their states
