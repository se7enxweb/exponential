# Spec Delta

## Purpose

How the newsletter reads its bounce mailboxes and acts on returned mail: what it writes to its log, which messages
it removes from the server, how one failing mailbox affects the others, and which returned messages count as bounces.

## ADDED Requirements

### Requirement: The mailbox password never reaches the log
The newsletter SHALL NOT write a mailbox password to its log, in any log level and in any outcome of a connection
(failed login, failed connect, successful connect). Where the connection settings are logged, the password SHALL
appear only as a fixed mask; the other settings (server, port, type, user name, encryption, delete flag) stay.

#### Scenario: Failed login
- **WHEN** the login to a bounce mailbox fails
- **THEN** the error entry names the mailbox, server and user name and shows the password only as the mask

#### Scenario: Successful connect with debug logging
- **WHEN** the connection succeeds and debug entries are written
- **THEN** no entry contains the password

### Requirement: Only stored messages are deleted from the server
When a mailbox is set to delete mails from the server, the newsletter SHALL delete a message only after it is stored
as a mailbox item, or when an item with the same identifier is already stored. A message that could not be stored,
or that came back empty, SHALL stay on the server and be reported as failed. When the mailbox is not set to delete
mails, no message is deleted, as before.

#### Scenario: Storing fails
- **WHEN** a message is fetched from a mailbox set to delete mails and storing it as an item fails
- **THEN** the message is still on the server after the run, and the run reports it as failed

#### Scenario: Stored and known messages
- **WHEN** one fetched message is stored as a new item and another is already stored under its identifier
- **THEN** both are deleted from the server after the run

#### Scenario: IMAP and POP3
- **WHEN** the mailbox is an IMAP mailbox, or a POP3 mailbox
- **THEN** the same messages are deleted in both cases, and messages not marked by this run are left untouched

### Requirement: One failing mailbox does not stop the others
When reading one bounce mailbox fails (connection, listing or fetching), the newsletter SHALL log the failure with
the mailbox id and the step that failed, report that mailbox as failed in the run's result, and go on with the next
active mailbox.

#### Scenario: The first of two mailboxes fails while fetching
- **WHEN** two mailboxes are active and fetching from the first one raises an error
- **THEN** the second mailbox is read, and the result shows the first as failed with its error and the second with
  its messages

### Requirement: Delay notices and automatic replies are not bounces
The newsletter SHALL NOT treat a delay notice (delivery is still being tried) or an automatic reply as a bounce: it
SHALL NOT mark the send item or the newsletter user as bounced, SHALL NOT raise the soft bounce count and SHALL NOT
schedule a resend.

#### Scenario: Delay notice for a sent edition
- **WHEN** a delay notice arrives for a send item of an edition
- **THEN** the item and its user are unchanged and no resend is scheduled

#### Scenario: Out-of-office reply quoting a reply code
- **WHEN** an automatic reply without a delivery status contains "450" in its text and the newsletter's own headers
  of the original mail
- **THEN** it is not counted as a soft bounce and nothing is resent

### Requirement: What counts as an automatic reply
A returned message SHALL count as an automatic reply when it carries no delivery status and no feedback report and
its headers mark it as automatically generated or as bulk, junk or list mail. A message with a delivery status or
feedback report SHALL be judged by that report, whatever its headers say.

#### Scenario: A real reply marked as not automatic
- **WHEN** a reply without a delivery status says in its headers that it was not sent automatically
- **THEN** it is not an automatic reply

#### Scenario: A bounce that carries an automatic-reply header
- **WHEN** a delivery status notification with a permanently failed recipient also carries an automatic-submission
  header
- **THEN** it is handled as a hard bounce
