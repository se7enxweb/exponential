# Spec Delta

## Purpose

A program that processes a mailbox over IMAP (bounces, mail-in addresses) must see when the mailbox was recreated,
move or remove only the messages it handled, pick up where it stopped, and address mailboxes by any name. This
capability states those operations of the mail package's IMAP transport.

## ADDED Requirements

### Requirement: Mailbox state after selection
After a mailbox is selected or examined, the transport SHALL expose the mailbox's UIDVALIDITY, its UIDNEXT and its
message count as the server announced them. Before a selection, and after the selection failed, the values SHALL
be null.

#### Scenario: Values of a selected mailbox
- **WHEN** a mailbox announcing UIDVALIDITY 1700000000, UIDNEXT 42 and 3 messages is selected
- **THEN** the transport answers 1700000000, 42 and 3

#### Scenario: Recreated mailbox
- **WHEN** the same mailbox is selected again and the server now announces a different UIDVALIDITY
- **THEN** the transport answers the new value, so a caller comparing it with the stored one sees the change

#### Scenario: No mailbox selected
- **WHEN** no mailbox is selected, or the selection failed
- **THEN** the three values are null

### Requirement: Moving messages
The transport SHALL move a set of messages to another mailbox: with the server's MOVE command when the server
lists the MOVE capability, otherwise by copying them and flagging the originals deleted. The caller's choice of
sequence numbers or UIDs SHALL apply to the move as to every other command.

#### Scenario: Server with MOVE
- **WHEN** the server lists MOVE and two messages are moved by UID
- **THEN** the server receives one UID MOVE command, the messages are in the destination and gone from the source

#### Scenario: Server without MOVE
- **WHEN** the server does not list MOVE and a message is moved
- **THEN** the server receives a COPY and a STORE +FLAGS \Deleted for it, the copy is in the destination, and the
  original is flagged deleted until expunged

#### Scenario: Destination does not exist
- **WHEN** the destination mailbox does not exist
- **THEN** the move fails with an error naming the destination and nothing is flagged

### Requirement: Expunging only own messages
The transport SHALL expunge a given set of UIDs with UID EXPUNGE when the server lists UIDPLUS. Without UIDPLUS it
SHALL fall back to a plain EXPUNGE and say so in its documentation. An expunge without a set SHALL behave as before.

#### Scenario: UID EXPUNGE with UIDPLUS
- **WHEN** two messages are flagged deleted, one of them by this client, and the client expunges its own UID
- **THEN** only that message is removed and the other stays flagged deleted in the mailbox

#### Scenario: Server without UIDPLUS
- **WHEN** the server lists no UIDPLUS and the client expunges a UID set
- **THEN** the server receives a plain EXPUNGE

### Requirement: Fetching after a known UID
The transport SHALL return the messages whose UID is greater than a given UID, referenced by UID, and an empty
result when there are none, including when the given UID is beyond the highest UID of the mailbox.

#### Scenario: New messages since the last run
- **WHEN** the mailbox holds UIDs 5, 7 and 9 and the caller asks for messages after UID 5
- **THEN** the result holds UIDs 7 and 9, in that order

#### Scenario: Nothing new
- **WHEN** the mailbox holds UIDs 5, 7 and 9 and the caller asks for messages after UID 9, or after UID 20
- **THEN** the result is empty

### Requirement: Mailbox names on the wire
Every command that takes a mailbox name SHALL send it as a quoted string with backslash and double quote escaped,
and SHALL encode a name with characters outside US-ASCII in modified UTF-7. Names returned by a mailbox listing
SHALL be decoded to UTF-8.

#### Scenario: Name with a quote
- **WHEN** the mailbox `Reports "2026"` is selected
- **THEN** the server receives `SELECT "Reports \"2026\""`

#### Scenario: Non-ASCII name
- **WHEN** the mailbox `Entwürfe` is created
- **THEN** the server receives `CREATE "Entw&APw-rfe"`

#### Scenario: Listing decodes
- **WHEN** the server lists the mailbox `Entw&APw-rfe`
- **THEN** the transport returns `Entwürfe`

#### Scenario: Round trip
- **WHEN** a name containing `&`, a quote and non-ASCII letters is encoded and decoded
- **THEN** the result equals the original name

### Requirement: Transports tested against local fake servers
The package SHALL carry a fake IMAP server and a fake POP3 server for its tests that run on a local socket, need
no network and record the commands they received; the existing IMAP and POP3 transport operations SHALL pass
against them.

#### Scenario: IMAP smoke test
- **WHEN** a client logs in to the fake IMAP server, selects a mailbox with two messages, fetches all, deletes one
  and expunges
- **THEN** both messages parse, the recorded commands are the expected ones in order, and the server state holds one
  message

#### Scenario: POP3 smoke test
- **WHEN** a client logs in to the fake POP3 server with user and password and fetches all messages
- **THEN** the messages parse and the recorded commands are USER, PASS, STAT or LIST, RETR per message and QUIT
