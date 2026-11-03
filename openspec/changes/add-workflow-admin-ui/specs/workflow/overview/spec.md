# Spec Delta

## Purpose

Gives administrators one read-only place for everything that happens around publishing: workflow definitions,
kernel workflows and triggers, conflicts and running work.

## ADDED Requirements

### Requirement: Definitions and bindings
The overview SHALL list all definitions with their bindings and warn about ambiguous rules and about definitions
without a binding.

#### Scenario: Definition without binding
- **WHEN** "Translation review" is published but no rule binds it
- **THEN** the overview warns that it never runs

### Requirement: Kernel workflows and conflicts
The overview SHALL list the kernel's workflows and triggers with their event types, mark approval, wait-until-date,
multiplexer and gate events, and report a missing gate, a gate on the trigger twice, and a class with both a kernel
approval and a workflow binding.

#### Scenario: Double approval
- **WHEN** articles are bound to "News article" and a kernel approval event also applies to articles
- **THEN** the overview reports the double approval

### Requirement: Running work
The overview SHALL show running instances per stage, overdue timers, watchers waiting longer than a threshold, the
queue job types of the actions with their dead letter counts, and state drift.

#### Scenario: Overdue timer
- **WHEN** a timer is pending past its due date
- **THEN** the overview lists it as overdue
