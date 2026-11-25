# Spec Delta

## Purpose

Mail providers require OAuth 2.0 bearer tokens instead of passwords for IMAP, POP3 and SMTP. This capability
states how the mail package authenticates with XOAUTH2 on each protocol and how the CMS obtains the token for its
own mail connections without storing it as a password or writing it to a log.

## ADDED Requirements

### Requirement: XOAUTH2 over POP3
The POP3 transport SHALL offer XOAUTH2 authentication: it sends the SASL XOAUTH2 initial response for the user and
token and treats a positive reply as logged in. A negative reply, including the server's error challenge, SHALL end
in an error that names the rejection and never contains the token.

#### Scenario: Accepted token
- **WHEN** a client authenticates with XOAUTH2 and the server accepts the token
- **THEN** the server received `AUTH XOAUTH2` with the base64 initial response and the client is logged in

#### Scenario: Rejected token with challenge
- **WHEN** the server answers the initial response with a continuation challenge and then a negative reply
- **THEN** the client answers the challenge with an empty line, authentication fails with an error naming the
  server's reply, and the error text does not contain the token

### Requirement: XOAUTH2 over IMAP and SMTP verified
XOAUTH2 authentication of the IMAP and SMTP transports SHALL be verified against local servers: accepted token,
rejected token with the challenge, and the mechanism not offered.

#### Scenario: IMAP accepted and rejected
- **WHEN** an IMAP client authenticates with XOAUTH2 against a server that accepts, and against one that rejects
  with a challenge
- **THEN** the first login succeeds and the second fails with an error naming the reply, without the token in it

#### Scenario: SMTP accepted
- **WHEN** an SMTP client with preferred method XOAUTH2 sends a mail to a server that offers AUTH XOAUTH2
- **THEN** the server received `AUTH XOAUTH2` with the initial response and the mail

### Requirement: Token sources
The CMS SHALL resolve a bearer token from a configured source: the configured secret value itself, a file (its
trimmed content), an environment variable, or a class of the installation that answers the token for a user. An
unreadable or empty source SHALL yield no token and one log line that names the source but not any token.

#### Scenario: Token from a file
- **WHEN** the source names a file containing a token followed by a newline
- **THEN** the token is the file content without the newline

#### Scenario: Token from a class
- **WHEN** the source names a class whose token method returns `tok-123` for the user
- **THEN** the token is `tok-123`

#### Scenario: Missing file
- **WHEN** the source names a file that does not exist
- **THEN** no token is returned and the log names the file path, not a token

#### Scenario: Static value
- **WHEN** no source is configured
- **THEN** the configured secret value is the token

### Requirement: Authentication settings of the CMS's mail connections
The SMTP transport, the bounce reader and the newsletter mailboxes SHALL take an authentication method and a
token source from settings. With the default settings every connection SHALL authenticate exactly as before. With
XOAUTH2 configured and no token available, the connection SHALL NOT be attempted and the reason SHALL be reported.

#### Scenario: Bounce reader with XOAUTH2 over IMAP
- **WHEN** the bounce reader is configured for XOAUTH2 with a token file and connects to an IMAP server
- **THEN** the server receives the XOAUTH2 authentication with that token and no LOGIN command

#### Scenario: Bounce reader with XOAUTH2 over POP3 on an old package
- **WHEN** the bounce reader is configured for XOAUTH2 over POP3 and the installed package has no POP3 XOAUTH2
- **THEN** the run reports that the package lacks it and sends no USER or PASS

#### Scenario: Newsletter mailbox with XOAUTH2
- **WHEN** a newsletter mailbox account is configured for XOAUTH2 with a token source
- **THEN** the mailbox connects with XOAUTH2 and the newsletter log of the connection contains neither the token
  nor the mailbox password

#### Scenario: Defaults unchanged
- **WHEN** no authentication setting is configured
- **THEN** SMTP, the bounce reader and the newsletter mailboxes authenticate as before (password, LOGIN, USER/PASS)

#### Scenario: Token missing
- **WHEN** XOAUTH2 is configured and the token source yields nothing
- **THEN** no connection is attempted and the reported error names the token source
