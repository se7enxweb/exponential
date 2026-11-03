# Spec Delta

## Purpose

Tells external systems about workflow events through signed HTTP requests that administrators manage and can
follow in a delivery log.

## ADDED Requirements

### Requirement: Managing webhooks
Users with the hooks permission SHALL list, create, edit, enable, disable and test webhooks; a webhook subscribes
to transitions or stages of one definition or of all.

#### Scenario: Subscribe to approvals
- **WHEN** an administrator creates a hook for the transition "approve" of "News article"
- **THEN** every approval of a news article queues a delivery to that hook

### Requirement: Payload
A delivery SHALL carry a JSON body with the instance, the definition and its version, the transition, the stages
before and after, what fired it, the user, the object, version and language, and a timestamp; the comment only
where the definition allows sharing it. A payload template SHALL replace the body where configured.

#### Scenario: Comment not shared
- **WHEN** a send-back with a comment fires and the definition does not share comments
- **THEN** the delivered body has no comment

### Requirement: Signed and restricted
Every delivery SHALL be signed with an HMAC of the body using a secret kept in the configuration, never in the
database. Only https addresses with a host SHALL be accepted (http only on loopback for tests), and redirects SHALL
NOT be followed.

#### Scenario: Plain http refused
- **WHEN** an administrator enters an http address on a public host
- **THEN** the hook cannot be saved

### Requirement: Retries and delivery log
A failed delivery (an error or a status of 400 or more) SHALL be retried with the queue's backoff and end in the
dead letter queue; every attempt SHALL be logged with its status and the start of the response.

#### Scenario: Receiver down
- **WHEN** the receiver answers 503 three times and then 200
- **THEN** the log shows four attempts and the last one succeeded

### Requirement: Test delivery
The test action SHALL send a sample payload to the hook and show the response.

#### Scenario: Test button
- **WHEN** an administrator tests a hook
- **THEN** the page shows the receiver's status code and response
