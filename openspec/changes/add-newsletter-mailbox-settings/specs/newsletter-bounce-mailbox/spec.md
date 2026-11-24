# Spec Delta

## Purpose

Which folder a newsletter mail account reads, how long it waits for the server, and what it does with a message
once the message is stored: keep it, delete it, or move it to a folder of the account.

## ADDED Requirements

### Requirement: The folder an IMAP account reads is a setting of the account
A newsletter mail account of type IMAP SHALL read the folder named in its settings; an account without a named
folder SHALL read the inbox, as before. The folder SHALL be editable on the account's form. A POP3 account has no
folders; the form SHALL say that the folder settings do not apply to it.

#### Scenario: A named folder
- **WHEN** an IMAP account names the folder "Bounces" and the run reads it
- **THEN** the messages of "Bounces" are read, and the inbox is not

#### Scenario: No folder named
- **WHEN** an IMAP account leaves the folder empty
- **THEN** the inbox is read

#### Scenario: The folder does not exist
- **WHEN** the named folder cannot be selected on the server
- **THEN** the account is reported as failed for this run with the server's answer, and the other accounts are read

### Requirement: The connection timeout is configurable
The time a mail account waits for the server to answer a connection SHALL be a setting of the newsletter in
seconds, with the default of 3 seconds; values below 1 second SHALL count as 1.

#### Scenario: A slow server
- **WHEN** the timeout is set to 20 seconds
- **THEN** the account's connection waits up to 20 seconds for the server before it gives up

### Requirement: Processed messages can be moved to a folder
An IMAP account MAY name a folder for processed messages. When it does, every message that was stored as a mailbox
item in this run, or that was already stored under its identifier, SHALL be moved to that folder after the run has
stored its messages, whether or not the account is set to delete messages; a message that could not be stored
SHALL stay where it is. Without a processed folder the account SHALL delete, or keep, as before.

#### Scenario: Stored messages are moved
- **WHEN** an account names the processed folder "Done", one message is stored and one is already known
- **THEN** both are moved to "Done" and neither is in the read folder afterwards

#### Scenario: A failed message stays
- **WHEN** storing one message fails
- **THEN** that message is neither moved nor deleted and is reported as failed

#### Scenario: Without a processed folder
- **WHEN** the account names no processed folder and is set to delete messages
- **THEN** the stored and known messages are deleted, as before

### Requirement: A missing processed folder is created, a failed move deletes nothing
When the processed folder does not exist, the account SHALL create it once and then move. When the move fails, the
messages SHALL stay in the folder that was read, nothing SHALL be deleted, and the failure SHALL be logged for the
account.

#### Scenario: The processed folder is missing
- **WHEN** the processed folder does not exist on the server
- **THEN** it is created and the messages are moved into it

#### Scenario: The move fails
- **WHEN** the server refuses the move
- **THEN** the messages stay in the read folder, nothing is deleted, and the account's log names the failure

#### Scenario: A mail package with and without the move command
- **WHEN** the mail package offers the IMAP move command, or does not
- **THEN** the messages end up in the processed folder in both cases

### Requirement: The folder settings are validated on the form
The account's form SHALL refuse a processed folder that equals the folder that is read, and SHALL store the folder
names as entered, trimmed.

#### Scenario: Same folder twice
- **WHEN** the folder read and the processed folder are both "INBOX"
- **THEN** the form reports the error and stores nothing
