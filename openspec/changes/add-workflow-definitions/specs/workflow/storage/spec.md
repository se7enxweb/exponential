# Spec Delta

## Purpose

Installs, checks and upgrades the tables of the workflow extension on every database engine the
kernel supports, from one schema definition, without ever removing data.

## ADDED Requirements

### Requirement: One schema for every engine
The extension SHALL define its tables once and SHALL install them on MySQL/MariaDB, PostgreSQL,
SQLite, Oracle and MongoDB. Table, column and index names SHALL be at most 30 characters and
SHALL NOT be reserved words of any of these engines.

#### Scenario: Names fit every engine
- **WHEN** the schema is inspected
- **THEN** every table, column and index name has at most 30 characters and none is a reserved
  word of a supported engine

### Requirement: Install command
`./console ext:expworkflow:install-schema` SHALL create every missing table, column and index in
the database of the selected siteaccess and SHALL be idempotent.

#### Scenario: Fresh install
- **WHEN** the command runs against a database without expworkflow tables
- **THEN** all expworkflow tables exist afterwards and the command exits with 0

#### Scenario: Second run changes nothing
- **WHEN** the command runs again
- **THEN** no statement is executed and the command exits with 0

### Requirement: Upgrade never removes
Installing over an older schema SHALL add missing columns and indexes and SHALL NOT drop or
alter existing tables, columns or data.

#### Scenario: Missing column added
- **WHEN** a table lacks a column the schema defines
- **THEN** the command adds the column and keeps every existing row

### Requirement: Check mode
With `--check` the command SHALL report missing tables, columns and indexes without changing the
database, and SHALL exit non-zero when something is missing.

#### Scenario: Check on an incomplete database
- **WHEN** `--check` runs against a database that lacks one index
- **THEN** the report names that index, nothing is changed, and the exit code is non-zero

#### Scenario: Check on a complete database
- **WHEN** `--check` runs against a fully installed database
- **THEN** it reports nothing missing and exits with 0

### Requirement: Shipped schema files
With `--print=<engine>` the command SHALL print the schema for that engine without a database
connection. With `--write-files` it SHALL regenerate `share/db_schema.dba`, the SQL files per
engine and the MongoDB schema. The shipped files SHALL equal what the command generates.

#### Scenario: Shipped files are current
- **WHEN** the files are generated and compared with the shipped files
- **THEN** they are identical

### Requirement: Stored constraints
The schema SHALL enforce that a definition identifier and version pair is unique. (One running
instance per content version is a runtime rule of milestone 2, not a stored constraint: a partial
unique index is not portable across the engines.)

#### Scenario: Duplicate definition version
- **WHEN** a second row with the same identifier and version is inserted
- **THEN** the database refuses it

### Requirement: Empty strings on Oracle
Values the extension stores as empty strings SHALL read back the same on Oracle, which stores
`''` as NULL.

#### Scenario: Empty optional value on Oracle
- **WHEN** a binding without subtree is stored and read back on Oracle
- **THEN** the binding has no subtree, the same as on the other engines
