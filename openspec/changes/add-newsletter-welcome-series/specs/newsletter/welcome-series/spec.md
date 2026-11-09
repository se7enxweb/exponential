# Spec Delta

## Purpose

New subscribers of a list receive a fixed sequence of mails at fixed intervals once the list sends to them, and
receive none while the list does not.

## ADDED Requirements

### Requirement: Series per list
An editor with the newsletter editorial right SHALL be able to define a welcome series for a newsletter list as
an ordered list of steps, each with an edition and a delay after the previous step.

#### Scenario: Three steps
- **WHEN** an editor defines the steps "Welcome" after 0 days, "Getting started" after 3 days and "Tips" after 14 days
- **THEN** the series page lists the three steps with their delays

### Requirement: Entering the series
A subscriber SHALL enter the list's series when the subscription to that list becomes approved. A subscription
that waits for confirmation or for an administrator's approval SHALL NOT enter. Entering again while already in
the series SHALL change nothing.

#### Scenario: Not yet confirmed
- **WHEN** somebody subscribes and has not yet clicked the confirmation link
- **THEN** no welcome mail is sent

#### Scenario: List approves automatically
- **WHEN** the subscriber confirms on a list that approves confirmed subscriptions automatically
- **THEN** the step "Welcome" is sent within the next send run

#### Scenario: List needs approval
- **WHEN** the subscriber confirms on a list whose subscriptions an administrator approves
- **THEN** no welcome mail is sent until the administrator approves, and "Welcome" is sent in the next send run after that

### Requirement: Steps in order and once
Each step SHALL be sent in the first send run after its delay has passed since the previous step was sent to
that subscriber, and at most one step SHALL be sent to a subscriber per send run. Each subscriber SHALL receive
each step at most once.

#### Scenario: Normal course
- **WHEN** "Welcome" was sent to a subscriber on 1 March
- **THEN** "Getting started" is sent in the first send run on or after 4 March

### Requirement: Not approved means no step
While a subscription is not approved (unsubscribed, removed, blacklisted, soft- or hard-bounced), no step SHALL be
sent to it. Its progress SHALL be kept; when the subscription becomes approved again, the series SHALL continue
with the next step not yet received, sent in the next send run, and later steps keep their delays from there.

#### Scenario: Unsubscribed after step one
- **WHEN** a subscriber unsubscribes one day after "Welcome"
- **THEN** "Getting started" and "Tips" are not sent

#### Scenario: Back a week later
- **WHEN** the same subscriber is approved again a week later
- **THEN** "Welcome" is not sent again, "Getting started" is sent in the next send run, and "Tips" 14 days after that

### Requirement: Audience
A series MAY be limited to a segment of its list. Then a subscriber SHALL enter only while a member of the segment,
and SHALL receive a step only while still a member; a subscriber who leaves the segment keeps the progress and
continues with the next step not yet received when a member again.

#### Scenario: Leaves the segment
- **WHEN** a subscriber received "Welcome" and then removes the interest that the audience segment requires
- **THEN** "Getting started" is not sent while the subscriber is not a member

#### Scenario: Not a member when approved
- **WHEN** a subscription becomes approved while the subscriber is not a member of the audience segment
- **THEN** no step is sent, and "Welcome" follows in the next send run after the subscriber becomes a member

### Requirement: Sent like any edition
Each step SHALL be sent with the list's skin, language and tracking setting, and SHALL count in the statistics
like any edition.

#### Scenario: Statistics
- **WHEN** an editor opens the statistics of the step "Welcome"
- **THEN** opens and clicks are shown as for an edition sent to the list

### Requirement: Pause and test
An editor SHALL be able to pause and resume a series and to send any step to their own address as a test. While
paused, no step SHALL be sent; on resume, each subscriber SHALL continue with the next step not yet received,
under the rule of one step per send run.

#### Scenario: Paused for three weeks
- **WHEN** a series is paused for three weeks while a subscriber was due for "Getting started" and "Tips"
- **THEN** after resuming, "Getting started" is sent in the next send run and "Tips" 14 days later

#### Scenario: Step tested
- **WHEN** an editor sends "Tips" as a test
- **THEN** only the editor receives it, and the progress of the subscribers is unchanged

### Requirement: Workflow action
A workflow definition SHALL be able to start a series with an action that subscribes an address to a list
through the list's double opt-in. The subscriber SHALL enter the series under the rules above; an address that
is already subscribed and approved SHALL enter the series if not already in it, and SHALL NOT receive a second
confirmation mail.

#### Scenario: New address from a workflow
- **WHEN** an instance runs the action for an address that is not subscribed to "Events"
- **THEN** the address receives the confirmation mail of "Events" and no step of the series yet

#### Scenario: Known subscriber
- **WHEN** the action runs for an address that is subscribed and approved but not in the series
- **THEN** "Welcome" is sent in the next send run and no confirmation mail is sent
