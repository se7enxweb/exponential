# Spec Delta

## Purpose

Delivery status notifications (RFC 3464, international form RFC 6533) and feedback reports (RFC 5965) are the
machine-readable answers a mail system gets about mail it sent. This capability makes them structured parts of a
parsed mail, with the recipient addresses decoded, so that a bounce reader can act on them without a parser of its
own.

## ADDED Requirements

### Requirement: Feedback report part
A part of type `message/feedback-report` SHALL be parsed into a feedback-report part holding its fields by name.
Fields that occur more than once (such as the original recipients and reported URIs) SHALL all be kept. The part
SHALL answer its feedback type in lower case and the list of original recipient addresses.

#### Scenario: Abuse report
- **WHEN** a multipart/report with report-type feedback-report carries a feedback report of type `abuse` with two
  Original-Rcpt-To fields
- **THEN** the machine-readable part of the report is a feedback-report part, its feedback type is `abuse`, and its
  original recipients are the two addresses in order

#### Scenario: Report without recipients
- **WHEN** a feedback report carries no Original-Rcpt-To field
- **THEN** the original recipient list is empty and the feedback type is still answered

### Requirement: International report parts
`message/global-delivery-status` SHALL be parsed exactly like `message/delivery-status`, `message/global` exactly
like `message/rfc822`, and `message/global-headers` like `text/rfc822-headers`. A parsed part SHALL keep the
content type it was parsed from when it is generated again.

#### Scenario: Global delivery status
- **WHEN** a report carries a `message/global-delivery-status` part with one recipient whose fields are UTF-8
- **THEN** the part is a delivery-status part with one recipient and the fields hold the UTF-8 text unchanged

#### Scenario: Global original message
- **WHEN** a report carries the original message as `message/global`
- **THEN** that part is parsed as an embedded message with its own headers and body

#### Scenario: Content type kept
- **WHEN** a parsed `message/global-delivery-status` part is generated again
- **THEN** its Content-Type header is still `message/global-delivery-status`

### Requirement: Recipient helpers of a delivery status
For each recipient of a delivery-status part the part SHALL answer the action in lower case (empty when absent),
the status as a `d.d.d` code (empty when the field has none) and the recipient address, taken from Final-Recipient
and, when that is absent, from Original-Recipient.

#### Scenario: Action and status
- **WHEN** a recipient block carries `Action: Failed` and `Status: 5.1.1 (user unknown)`
- **THEN** the action is `failed` and the status is `5.1.1`

#### Scenario: Original recipient only
- **WHEN** a recipient block carries Original-Recipient but no Final-Recipient
- **THEN** the recipient address is the Original-Recipient address

### Requirement: Typed recipient address decoding
A recipient value SHALL be decoded by its address type: for `rfc822` the RFC 3461 xtext form (`+HH` with two
upper-case hex digits) is decoded, for `utf-8` the RFC 6533 `\x{HH}` form is decoded, and the type prefix is
removed. Any other type SHALL yield the value after the prefix, trimmed. A plus sign that is not an xtext sequence
SHALL stay.

#### Scenario: xtext encoded plus
- **WHEN** the value is `rfc822; user+2Bshop@example.com`
- **THEN** the address is `user+shop@example.com`

#### Scenario: Plus address kept
- **WHEN** the value is `rfc822;user+shop@example.com`
- **THEN** the address is `user+shop@example.com`

#### Scenario: UTF-8 address with escape
- **WHEN** the value is `utf-8; j\x{FC}rgen@example.org`
- **THEN** the address is `jürgen@example.org`

#### Scenario: Unknown address type
- **WHEN** the value is `x-local; mailbox 42`
- **THEN** the address is `mailbox 42`

### Requirement: Bounce reader uses the package parser when available
The bounce reader SHALL classify a message with the package's report parts when the installed package provides
them, and with its own parser otherwise. Both ways SHALL give the same kind, addresses and detail for the same
message.

#### Scenario: Same result on the bundled bounces
- **WHEN** every bundled bounce fixture is classified once through the package parts and once through the reader's
  own parser
- **THEN** kind, addresses and detail are equal for each fixture

#### Scenario: Package without the report parts
- **WHEN** the installed package has no feedback-report part
- **THEN** the reader classifies with its own parser and the result is unchanged

#### Scenario: Encoded recipient in a hard bounce
- **WHEN** a hard bounce names its recipient as `rfc822; user+2Bshop@example.com`
- **THEN** the reader's hard-bounce address is `user+shop@example.com`
