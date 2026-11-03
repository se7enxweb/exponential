# Spec Delta

## Purpose

Fires transitions at a date: from a date attribute of the content or after a duration in the stage, with
reminders before and a safety net for lost timers.

## ADDED Requirements

### Requirement: Scheduling
When an instance enters a stage with a timer transition, a timer SHALL be scheduled: at the date of the named
date or datetime attribute, or after the given duration counted from entering the stage. An empty attribute SHALL
use the fallback: now, or never.

#### Scenario: Publish date set
- **WHEN** an instance enters "Approved" and the article's publish date is 1 December 09:00
- **THEN** "go_live" is due on 1 December 09:00

#### Scenario: Empty date with fallback now
- **WHEN** the publish date is empty and the fallback is "now"
- **THEN** "go_live" fires at once

### Requirement: Firing
A due timer SHALL fire its transition without user action, but only while the instance is still in the stage and
the timer is still pending.

#### Scenario: Expiry
- **WHEN** the expiry date of a published article passes
- **THEN** the instance moves to "Expired" and the article is hidden

### Requirement: Reminders
A timer transition with a reminder SHALL send it the given time before the timer is due, to the named recipient.

#### Scenario: Two days before expiry
- **WHEN** "expire" reminds the owner two days before
- **THEN** the owner is notified two days before the expiry date

### Requirement: Safety net
A sweep SHALL fire overdue pending timers whose scheduled job is lost, and recompute timers whose date attribute
changed in a newer published version.

#### Scenario: Expiry date moved
- **WHEN** a newer version of a published article moves the expiry date by a week
- **THEN** the timer is due at the new date
