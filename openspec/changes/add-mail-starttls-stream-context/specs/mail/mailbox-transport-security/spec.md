# Spec Delta

## Purpose

Mail read from a mailbox over IMAP or POP3 carries credentials and private messages; the connection must be
encrypted and the server verified the way the operator configures it. This capability states how the mail
package's mailbox transports and the bounce reader of the CMS establish and verify such connections.

## ADDED Requirements

### Requirement: Stream context for mailbox connections
A mailbox transport SHALL accept connection options that are applied as the stream context of an implicit TLS
connection and of a TLS upgrade: CA file or path, peer name, peer verification switches and the crypto method.
Without connection options the connection SHALL behave as before.

#### Scenario: Verified implicit TLS
- **WHEN** a client connects with implicit TLS giving the server's certificate as CA file and the certificate's
  name as peer name
- **THEN** the connection is established and the login succeeds

#### Scenario: Wrong peer name
- **WHEN** a client connects with implicit TLS giving a peer name the certificate does not carry
- **THEN** the connection fails with an error that names the server, and no command is sent

#### Scenario: No context
- **WHEN** a client connects without connection options
- **THEN** the commands sent and the behaviour are those of the previous version

### Requirement: STARTTLS and STLS
A mailbox transport SHALL offer an option to upgrade a plain connection to TLS before authentication: IMAP with the
STARTTLS command, POP3 with the STLS command. A refused upgrade or a failed handshake SHALL fail the connection
with a clear error before any credential is sent. The option SHALL NOT be combined with implicit TLS.

#### Scenario: IMAP STARTTLS
- **WHEN** a client with the upgrade option connects to a server that accepts STARTTLS
- **THEN** the server receives STARTTLS before LOGIN, the LOGIN travels encrypted, and the login succeeds

#### Scenario: POP3 STLS
- **WHEN** a client with the upgrade option connects to a POP3 server that accepts STLS
- **THEN** the server receives STLS before USER, and the login succeeds

#### Scenario: Server refuses the upgrade
- **WHEN** the server answers STARTTLS or STLS negatively
- **THEN** the connection fails with an error naming the refusal, and the server receives no LOGIN, USER or PASS

#### Scenario: Both options set
- **WHEN** implicit TLS and the upgrade option are both set
- **THEN** the options are refused with an error before a connection is made

### Requirement: LOGINDISABLED is honoured
When an IMAP server announces LOGINDISABLED, a plain LOGIN authentication SHALL fail before the credentials are
sent, with an error that says the server disables LOGIN on this connection. After a TLS upgrade the capabilities
SHALL be read again, so a server that disables LOGIN only in the clear accepts it afterwards.

#### Scenario: LOGIN refused in the clear
- **WHEN** the server lists LOGINDISABLED and the client authenticates with LOGIN without an upgrade
- **THEN** authentication fails with that reason and the server receives no LOGIN command

#### Scenario: LOGIN after the upgrade
- **WHEN** the server lists LOGINDISABLED in the clear and not after STARTTLS, and the client upgrades first
- **THEN** the LOGIN is sent and succeeds

### Requirement: Bounce reader connection settings
The bounce reader SHALL offer settings for the TLS upgrade, a minimum TLS version and further context options,
with defaults that change nothing. When the installed package does not know these options, the reader SHALL log
one warning and connect as before.

#### Scenario: Defaults
- **WHEN** the shipped settings are in effect
- **THEN** the transport options built by the reader equal those of the previous version

#### Scenario: Upgrade and CA file configured
- **WHEN** the upgrade is enabled, the minimum version is 1.2 and a CA file is set
- **THEN** the transport options carry the upgrade flag and a context with that CA file and a crypto method of at
  least TLS 1.2

#### Scenario: Package without the options
- **WHEN** the settings are set but the installed package has no such options
- **THEN** the reader logs one warning naming the setting and builds the options of the previous version
