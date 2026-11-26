# Reading mailboxes: the shared rules (expMailReceive)

Exponential reads mail in several places: the kernel's bounce reader (`exp:mail:bounces`,
`mailpreferences.ini [BounceSettings]`), the newsletter's bounce mailboxes (cjw_newsletter), the help desk's mail
intake (ticketsystem) and extensions that import mail. `expMailReceive` (`kernel/classes/mail/expmailreceive.php`)
holds the rules they share, so a reader neither repeats the connection code nor decides on its own what an automatic
reply is.

## The rules

| Rule | Method | What it means |
|---|---|---|
| An automatic message is recognised by its own headers | `isAutomaticHeaders( $headers )` | `Auto-Submitted` other than `no` (RFC 3834), `Precedence` bulk, junk, list or auto_reply, or an `X-Autoreply` / `X-Autorespond` header |
| An automatic reply is no bounce and is never answered | `isAutomaticReply( $raw )` | automatic headers, and neither a report (`multipart/report`) nor a message from `MAILER-DAEMON@` or `postmaster@`, whose bounces carry `Auto-Submitted` too |
| Only the message's own headers count | `topHeaders( $raw )` | up to the first empty line, folded lines joined, the first occurrence of a name; headers quoted in a part or a returned copy are ignored |
| One failure stops one unit, not the run | `isolate( $units, $work, $onError )` | the work runs per mailbox account (or per message); a failure is recorded with its message and the next unit is read |
| A message leaves the server only after it is stored | `deleteMessages( $transport, $numbers )` | deletes exactly the numbers the reader stored or already knew; IMAP expunges them at once, POP3 removes them when the connection is closed |
| One way to connect | `connect( $account )`, `messageNumbers()`, `fetchRaw()`, `disconnect()` | protocol (imap, pop3), server, port, ssl, user, password, folder, timeout; IMAP reads unread messages, POP3 all; `fetchRaw( ..., true )` leaves an IMAP message unread (dry runs) |

A delivery delay notice (a delivery status notification whose action is `delayed`) is not an automatic reply: it is a
report. The bounce reader classifies it as the bounce kind `delay`, which changes nothing (no suppression, no soft
bounce count, no resend); the newsletter does the same from cjw_newsletter 4.3.2 on.

## Who uses it

| Reader | Since | What it takes |
|---|---|---|
| Kernel bounce reader (`expMailBounceReader`) | 6.0.15 | connecting, reading, deleting after the run, closing |
| cjw_newsletter bounce mailboxes | 4.3.3 | the automatic-reply rule (its own copy of the rule stays for older kernels); each account is read on its own, a message is deleted after it is stored |
| ticketsystem mail intake | 1.1.1 | the automatic-message rule (its own additions stay: mailing lists, its own ticket mails, no-reply senders); one message that fails no longer stops the others and stays unread |

Extensions that import mail as content (sqliimport's IMAP source, ezodf's mailbox documents) read mail as documents to
import, not as mail to answer or count; they need neither rule.

An extension that may run on an older kernel checks `class_exists( 'expMailReceive' )` and keeps its own code for
that case.

## Tests

`tests/tests/kernel/classes/mail/expMailReceiveTest.php` runs without a database. It starts fake IMAP and POP3 servers
on 127.0.0.1 (`tests/toolkit/expfakemailserver.php`, `tests/toolkit/extras/fakemailserver/server.php`, started through
`ezpLoopbackServer` so they neither hold the test's descriptors nor outlive it) and checks:

- reading, and deleting only the stored messages (IMAP `STORE \Deleted` and `EXPUNGE`, POP3 `DELE` and `QUIT`), with
  the password never in the server's log;
- no `EXPUNGE` when nothing is deleted, and a dry run that leaves the message unread;
- four accounts, one with a wrong password and one whose server is down: the other two are read;
- the bounce reader against both servers: a delay notice is understood and deleted, an automatic reply stays;
- the cost: reading what a message is takes a few microseconds (measured 3.2 µs per message on PHP 8.5, against 4.1 µs
  for the newsletter's own copy of the rule, which reads every line of the message).

A test of another reader starts the same server:

```php
require_once 'tests/toolkit/expfakemailserver.php';
$server = expFakeMailServer::start( 'pop3', array( $raw1, $raw2 ) );
$account = $server->account();          // for expMailReceive::connect()
// ... read ...
$server->commands();                    // what the client sent
$server->left();                        // the numbers still on the server
$server->stop();
```
