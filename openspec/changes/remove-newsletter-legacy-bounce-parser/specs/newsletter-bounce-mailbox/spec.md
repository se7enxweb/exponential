# Spec Delta

## Purpose

What a newsletter mail account records for a returned message, and that one classification — the kernel's where
it exists, the newsletter's fallback where not — decides both the record and the action.

## ADDED Requirements

### Requirement: One classification per returned message
A mail account SHALL classify each stored message exactly once and SHALL use that one result for the code it
records with the mailbox item, for the kind it logs and for the action it takes. The code recorded SHALL be the
enhanced status code of the recipient that decided the kind, or the feedback type of a report, or empty ("no
bounce") when the message is none, a delay notice or an automatic reply.

#### Scenario: Delivery status notification with several recipients
- **WHEN** a notification reports one delivered and two failed recipients, the second permanently
- **THEN** the item records the permanent recipient's status code, the kind is hard, and the newsletter acts on
  it as before

#### Scenario: Feedback report
- **WHEN** a feedback report of type abuse is stored
- **THEN** the item records "abuse" as its code and the kind is complaint

#### Scenario: Delay notice and automatic reply
- **WHEN** a delay notice or an automatic reply quoting a reply code is stored
- **THEN** the item records no bounce code and nothing is done, as before

#### Scenario: Status codes with dots
- **WHEN** a message's text contains "5x1y1" and no real status code
- **THEN** it is not read as the status 5.1.1

### Requirement: The fallback without the kernel reads every recipient
When the kernel's classifier is not available, the newsletter SHALL judge a delivery status notification by the
status or diagnostic code of any of its recipients (not only the first) and, without one, by the first text part;
the position of the status part in the message SHALL NOT matter.

#### Scenario: Status part not in second place
- **WHEN** the kernel's classifier is absent and a notification carries its delivery status as its third part,
  with the first recipient delivered and the second failed with 5.1.1
- **THEN** the item records 5.1.1 and the kind is hard

### Requirement: Headers are still recorded
The item SHALL keep recording the subject, sender, recipient and date of the returned message and the
newsletter's own headers found in it, as before.

#### Scenario: Headers of a bounce
- **WHEN** a bounce quoting the newsletter's send item header is stored
- **THEN** the item carries subject, from, to and date, and the send item is found by its header
