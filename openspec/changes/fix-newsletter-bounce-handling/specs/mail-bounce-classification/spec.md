# Spec Delta

## Purpose

How the bounce reader judges a returned message, so that only a real failure of an address leads to a suppression,
a bounce mark or a resend, and a notice that delivery is still being tried leads to none of them.

## ADDED Requirements

### Requirement: A delayed delivery is its own kind
The bounce reader SHALL classify a delivery status notification in which no recipient failed and at least one
recipient is reported as delayed as the kind "delay", not as a soft bounce. A notification that reports a failed
recipient SHALL keep its current classification (hard or soft by its status code), whatever other recipients report.
A recipient reported as delivered, relayed or expanded SHALL count for nothing, as before.

#### Scenario: Only delayed recipients
- **WHEN** a delivery status notification reports one recipient with action "delayed" and status 4.2.2
- **THEN** the message is classified as "delay" with that status as its detail and no address

#### Scenario: Delayed and failed in one notification
- **WHEN** one recipient of a notification is "delayed" and another "failed" with a permanent address status
- **THEN** the message is classified as a hard bounce of the failed address

#### Scenario: Delayed and temporarily failed
- **WHEN** one recipient is "delayed" and another "failed" with a status that is not a permanent address failure
- **THEN** the message is classified as a soft bounce

### Requirement: A delay changes nothing about an address
A message classified as "delay" SHALL NOT put an address on the suppression list and SHALL NOT be recorded as a
bounce. The bounce reader SHALL count it separately from soft bounces in its run counts and status, and SHALL hand it
to the bounce listeners with its kind, as it does with every other message.

#### Scenario: A run with a delay notice
- **WHEN** the bounce reader processes a mailbox holding one delay notice and one hard bounce
- **THEN** its counts show one delayed and one hard message, and only the hard bounce's address is suppressed

#### Scenario: A listener sees the kind
- **WHEN** a bounce listener is configured and a delay notice is processed
- **THEN** the listener receives the message with the kind "delay"
