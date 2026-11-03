# Tasks

## 1. Extension skeleton

- [ ] 1.1 Create `extension/expworkflow/` with `extension.xml`, `ezinfo.php`, `composer.json`, `LICENSE`, `settings/expworkflow.ini.append.php` (`[Actions] List[]` with the six built-in names, D9) and docs stubs `README.md`, `INSTALL.md`, `USAGE.md`, `AGENTS.md`, `doc/index.md` in the expqueue layout; verify `php -l` on every PHP and INI file and that the extension activates without errors (`php bin/php/ezpgenerateautoloads.php -e`, then `./console list` shows no error)
- [ ] 1.2 Add `tests/phpunit/bootstrap.php` (kernel-free, stand-ins guarded by `class_exists( …, false )`) and the suite `expworkflow` to the root `phpunit.xml` and to the CI workflow; verify `vendor/bin/phpunit --no-coverage --fail-on-risky --fail-on-warning --testsuite expworkflow` runs (zero tests is fine)

## 2. Definition format and validation

- [ ] 2.1 Implement `expWorkflowDefinition` (parse, `toArray()`, layout kept but ignored for validity) and `expWorkflowFinding`; verify unit tests for Scenario: Minimal definition is accepted and Scenario: Layout does not affect validity
- [ ] 2.2 Implement `expWorkflowEnvironment` (interface, array-backed test implementation) and `expWorkflowValidator` for identifiers, stage kinds, start stage and reachability; verify Scenarios: Duplicate stage id, Invalid transition id, Unknown kind, Start names a missing stage, Start is a published stage, Orphan stage, No published stage reachable
- [ ] 2.3 Validate transitions: endpoints and trigger, waiting stages, submit, person options; verify Scenarios: Transition to a missing stage, Unknown trigger, Dead-end waiting stage, Two submit transitions from one draft stage, Submit on a transition leaving a waiting stage, Unknown role, Invalid comment mode
- [ ] 2.4 Validate review option shape, job transitions and timer transitions; verify Scenarios: Quorum on a transition leaving a draft stage, Author selection with min above max, Job transition without a job action, Missing failure path, Invalid duration, Both attribute and duration
- [ ] 2.5 Validate actions and assignees, and report all findings in one pass; verify Scenarios: Unknown action, Unknown assignee group, Several errors at once
- [ ] 2.6 Add message keys and texts to `translations/untranslated/`, `eng-GB` and `ger-DE` (context `extension/expworkflow`); document the format and every validation rule in `doc/index.md` ("Definition format"); verify a test that every finding key the validator can emit has an `eng-GB` text

## 3. Storage

- [ ] 3.1 Implement `expWorkflowSchema` (`definition()` with all nine tables, `handler()`, `mongoDescription()`, `ensure()`, `upgradeStatements()`); verify a unit test for Scenario: Names fit every engine (length ≤ 30, reserved words of all five engines)
- [ ] 3.2 Implement `expWorkflowCommand` and `bin/php/install-schema.php` with `--check`, `--print=<engine>`, `--write-files`, `-s <siteaccess>`; generate and commit `share/db_schema.dba`, `sql/{mysql,postgresql,sqlite,oracle}/schema.sql`, `sql/mongodb/schema.json`; verify Scenario: Shipped files are current (unit test regenerates into a temp dir and compares)
- [ ] 3.3 Database tests (group `database`, throwaway database) for Scenarios: Fresh install, Second run changes nothing, Missing column added, Check on an incomplete database, Check on a complete database, Duplicate definition version, Empty optional value on Oracle (skipped with reason when no Oracle is configured); verify they pass on SQLite and MySQL
- [ ] 3.4 Document installation and upgrade in `INSTALL.md` (activate, `./console ext:expworkflow:install-schema -s <siteaccess>`, `--check` in monitoring) and the tables in `doc/index.md` ("Database"); verify the documented commands run as written on a throwaway installation

## 4. Definition versions

- [ ] 4.1 Implement the kernel `expWorkflowEnvironment` (roles, user groups, class attributes and datatypes, `[Actions] List[]`, classes bound to an identifier); verify database tests with a role, a group and a class with a date attribute
- [ ] 4.2 Implement `expWorkflowDefinitionStore` (save draft, publish in one transaction, archive, fetch); verify Scenarios: First save, Save with errors, Publish replaces the published version, Invalid version cannot be published, Published version is immutable, plus a test that two concurrent publishes leave exactly one published version (D4)
- [ ] 4.3 Timer attribute check on publish against bound classes; verify Scenarios: Bound class lacks the attribute, Attribute of the wrong type

## 5. Binding

- [ ] 5.1 Implement `expWorkflowBinding` and `expWorkflowBindingResolver` matching (class, subtree, section, initial language, enabled); verify Scenarios: Disabled rule is ignored, Language rule matches a translation, Language rule does not match the source language, Unbound class
- [ ] 5.2 Implement the specificity comparator (D5) in one function; verify Scenarios: Class and subtree beat class, Class beats subtree, Deeper subtree wins, Language adds specificity, Priority breaks a tie, Unresolved tie
- [ ] 5.3 Skip rules without a published definition and implement `check()` warnings; verify Scenario: Rule for a draft-only definition and the tie warning of Scenario: Unresolved tie
- [ ] 5.4 Refuse adding or enabling a rule whose class lacks a timer attribute (D7); verify Scenario: Binding a class without the timer attribute
- [ ] 5.5 Document binding rules and the specificity order in `doc/index.md` ("Binding") and the invariants in `AGENTS.md`; verify the examples in the doc match the test fixtures

## 6. Integration

- [ ] 6.1 Run the suite `expworkflow` (unit and `--group database`) and `deploy/ci/lint-extensions.sh`; verify both green and record the run lines for verify.md

## Workflow follow-up

- Upstream PR to se7enxweb with the extension; archive the change after the merge.
