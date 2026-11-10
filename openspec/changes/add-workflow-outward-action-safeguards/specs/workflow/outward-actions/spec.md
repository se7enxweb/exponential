# Spec Delta

## Purpose

Deliveries that reach people or systems outside the site run only when the site allows it, never twice for the
same recipient, never during bulk operations, and can be tried out without reaching anyone else.

## ADDED Requirements

### Requirement: Outward actions are declared
Every action SHALL declare whether it is outward. Every delivery of a subscribed webhook SHALL count as outward.
An outward action SHALL be shown with a distinct mark in the definition editor, and the overview SHALL list every
published definition that contains one.

#### Scenario: Webhook marked in the editor
- **WHEN** an administrator adds a webhook action to a transition
- **THEN** the transition shows the outward mark

#### Scenario: Notify is not outward
- **WHEN** an administrator adds a notify action to the author on a transition
- **THEN** the transition shows no outward mark

### Requirement: Site-wide switch
Outward deliveries SHALL be made only when outward delivery is allowed for the site; it SHALL be off on a new
installation. While it is off, an outward delivery SHALL be recorded in the instance history as not delivered,
and the workflow SHALL continue as if it had succeeded.

#### Scenario: Switch off
- **WHEN** outward delivery is off and an instance enters a stage with a webhook action
- **THEN** no request is sent, the history says "not delivered (outward delivery off)", and the instance moves on

#### Scenario: Switch on
- **WHEN** outward delivery is on and an instance enters the same stage
- **THEN** the webhook delivery is queued

#### Scenario: Subscribed hook while off
- **WHEN** outward delivery is off and a transition fires that a webhook is subscribed to
- **THEN** no request is sent and the hook's delivery log records it as not delivered

### Requirement: Test run leaves the content alone
A user with the right to design workflows SHALL be able to run a definition on a chosen content object as a
test. A test run SHALL NOT change the object, its versions, its object states or its publishing; actions that
change content (hide, unhide) SHALL be recorded but not executed.

#### Scenario: Test of an expiry stage
- **WHEN** an administrator test-runs a definition whose last stage hides the content
- **THEN** the test result lists the hide action and the content stays visible

### Requirement: Test run reaches only the tester
In a test run, outward actions that send mail SHALL deliver to the testing user only, marked as a test; all
other outward deliveries SHALL be recorded with the payload they would send and SHALL NOT be sent. This SHALL
hold whether outward delivery is on or off.

#### Scenario: Test of a series start
- **WHEN** an administrator test-runs a definition whose published stage starts a newsletter welcome series
- **THEN** only the administrator receives the first mail of the series, with a test mark, and no subscriber is added

#### Scenario: Test of a channel post
- **WHEN** an administrator test-runs a definition that posts to a channel
- **THEN** the test result shows the text that would be posted, and nothing is posted

### Requirement: Bulk suppression
Outward deliveries SHALL NOT be made while content is created or published by a bulk operation (an import, a
restore, a content job). The instance history SHALL record each suppressed delivery with the operation that
suppressed it.

#### Scenario: Import of many articles
- **WHEN** an import publishes 500 articles bound to a definition that calls a webhook on publishing
- **THEN** no request is sent and each instance's history names the import as the reason

#### Scenario: Single publish after the import
- **WHEN** an editor later publishes one of those articles by hand through the same definition
- **THEN** the webhook is called

### Requirement: Once per recipient
An outward delivery SHALL be made at most once per instance, action and recipient or destination, even when the
event repeats, the job is retried or a person fires the transition twice.

#### Scenario: Retried job
- **WHEN** the job of a series start for 200 subscribers is retried after a worker crash
- **THEN** subscribers who were already added are not added again and receive no second mail

### Requirement: Many recipients
An outward job action with several recipients SHALL record a failure per recipient and continue with the others.
When it is the job action of a stage's on-enter, the stage's failed transition SHALL fire only when every
recipient failed; otherwise the done transition SHALL fire. When it runs on a transition, failures SHALL be
recorded in the instance history and the transition SHALL stay fired.

#### Scenario: One destination fails
- **WHEN** the on-enter job action of a stage posts to three channels and one of them refuses the post
- **THEN** two posts are made, the failure is recorded for that channel, and the done transition fires

#### Scenario: Every destination fails
- **WHEN** the same action fails for all three channels
- **THEN** the failed transition fires
