# Spec Delta

## Purpose

Editors send to a chosen part of a list, defined by profile, interests, subscription and reading behavior, without
using behavior data of subscribers who did not consent to it and without guessing where no data exists.

## ADDED Requirements

### Requirement: Defining a segment
An editor with the newsletter editorial right SHALL be able to define a segment of one list as a set of conditions
combined with all or any. Conditions SHALL cover the subscription (date, language, output format), the profile
fields, the interests, and reading behavior.

#### Scenario: Interested organisations
- **WHEN** an editor defines the segment "Event partners" of the list "News" as interest "Events" picked and organisation set
- **THEN** the segment is saved and lists both conditions

### Requirement: Size and sample
The segment page SHALL show the current number of members and a sample of them to users who may see the list's
subscribers; users without that right SHALL see the number only.

#### Scenario: Preview while editing
- **WHEN** an editor adds the condition "subscribed in the last 30 days" to "Event partners"
- **THEN** the page shows the new number of members before the segment is saved

### Requirement: Behavior only with consent
A behavior condition SHALL match only subscribers who consented to newsletter statistics. The segment page SHALL
say how many subscribers of the list have not consented and are therefore never matched by a behavior condition.

#### Scenario: Consent missing
- **WHEN** an editor adds a behavior condition on a list where 400 of 1000 subscribers consented
- **THEN** the page says that 600 subscribers are never matched by this condition

#### Scenario: Consent withdrawn
- **WHEN** a member of a behavior segment withdraws the statistics consent
- **THEN** the subscriber is no longer a member

### Requirement: Behavior counts only tracked sends
A behavior condition over the last N sends or days SHALL consider, for each subscriber, only sends of the list that
were tracked per person, that went to that subscriber after the consent was given, and whose per-person data the
statistics still keep. Test sends and SMS sends SHALL never count. Where fewer than N such sends exist for a
subscriber, a "did not open" or "did not click" condition SHALL NOT match that subscriber.

#### Scenario: Untracked sends in between
- **WHEN** of the last three sends of "News" one was sent with tracking off, and a consenting subscriber opened neither of the other two
- **THEN** "did not open any of the last three sends" does not match, because only two sends are known

#### Scenario: New subscriber
- **WHEN** a subscriber consented one week ago and has received one tracked send since
- **THEN** "did not open any of the last three sends" does not match that subscriber

#### Scenario: Older than the retention period
- **WHEN** the per-person data of a send has been removed after the retention period
- **THEN** that send counts as unknown for every subscriber, not as "not opened"

### Requirement: Sending to a segment
The send form SHALL offer the segments of the list as an alternative to the whole list. A send to a segment SHALL go
to the members whose subscription is approved at the moment its mail queue is made.

#### Scenario: Send to a segment
- **WHEN** an editor sends an edition to "Event partners" with 120 members, 3 of whom unsubscribed after the preview
- **THEN** 117 mails are queued

#### Scenario: Segment changed after queuing
- **WHEN** an editor changes the segment after the send was queued
- **THEN** the queued send keeps its recipients

### Requirement: Segment as series audience
A segment SHALL be usable as the audience of a welcome series under the rules of the welcome series.

#### Scenario: Series for one segment
- **WHEN** the welcome series of "News" has the audience "Event partners"
- **THEN** only subscribers who are members of "Event partners" receive its steps
