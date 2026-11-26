# Postfächer lesen: die gemeinsamen Regeln (expMailReceive)

Exponential liest an mehreren Stellen E-Mails: der Bounce-Leser des Kernels (`exp:mail:bounces`,
`mailpreferences.ini [BounceSettings]`), die Bounce-Postfächer des Newsletters (cjw_newsletter), der Mail-Eingang des
Helpdesks (ticketsystem) und Erweiterungen, die E-Mails importieren. `expMailReceive`
(`kernel/classes/mail/expmailreceive.php`) enthält die Regeln, die sie gemeinsam haben: Ein Leser wiederholt weder den
Verbindungscode noch entscheidet er allein, was eine automatische Antwort ist.

## Die Regeln

| Regel | Methode | Bedeutung |
|---|---|---|
| Eine automatische Nachricht erkennt man an ihren eigenen Kopfzeilen | `isAutomaticHeaders( $headers )` | `Auto-Submitted` nicht `no` (RFC 3834), `Precedence` bulk, junk, list oder auto_reply, oder eine Kopfzeile `X-Autoreply` / `X-Autorespond` |
| Eine automatische Antwort ist kein Bounce und wird nie beantwortet | `isAutomaticReply( $raw )` | automatische Kopfzeilen, und weder ein Bericht (`multipart/report`) noch eine Nachricht von `MAILER-DAEMON@` oder `postmaster@`, deren Bounces ebenfalls `Auto-Submitted` tragen |
| Nur die Kopfzeilen der Nachricht selbst zählen | `topHeaders( $raw )` | bis zur ersten Leerzeile, gefaltete Zeilen verbunden, das erste Vorkommen eines Namens; Kopfzeilen in einem Teil oder einer zurückgeschickten Kopie zählen nicht |
| Ein Fehler stoppt eine Einheit, nicht den Lauf | `isolate( $units, $work, $onError )` | die Arbeit läuft je Postfach-Konto (oder je Nachricht); ein Fehler wird mit seiner Meldung festgehalten, die nächste Einheit wird gelesen |
| Eine Nachricht verlässt den Server erst, wenn sie gespeichert ist | `deleteMessages( $transport, $numbers )` | löscht genau die Nummern, die der Leser gespeichert hat oder schon kannte; IMAP löscht sie sofort endgültig (EXPUNGE), POP3 beim Schließen der Verbindung |
| Ein Weg zur Verbindung | `connect( $account )`, `messageNumbers()`, `fetchRaw()`, `disconnect()` | Protokoll (imap, pop3), Server, Port, SSL, Benutzer, Passwort, Ordner, Timeout; IMAP liest ungelesene Nachrichten, POP3 alle; `fetchRaw( ..., true )` lässt eine IMAP-Nachricht ungelesen (Probeläufe) |

Eine Verzögerungsmeldung (ein Zustellbericht mit der Aktion `delayed`) ist keine automatische Antwort, sondern ein
Bericht. Der Bounce-Leser ordnet sie der Bounce-Art `delay` zu, die nichts ändert (keine Sperre, kein Soft-Bounce-Zähler,
kein erneuter Versand); der Newsletter tut das ab cjw_newsletter 4.3.2 ebenso.

## Wer sie nutzt

| Leser | Seit | Was er übernimmt |
|---|---|---|
| Bounce-Leser des Kernels (`expMailBounceReader`) | 6.0.15 | Verbinden, Lesen, Löschen nach dem Lauf, Schließen |
| Bounce-Postfächer von cjw_newsletter | 4.3.3 | die Regel für automatische Antworten (die eigene Kopie bleibt für ältere Kernel); jedes Konto wird für sich gelesen, eine Nachricht wird nach dem Speichern gelöscht |
| Mail-Eingang von ticketsystem | 1.1.1 | die Regel für automatische Nachrichten (die eigenen Ergänzungen bleiben: Mailinglisten, eigene Ticket-Mails, No-Reply-Absender); eine fehlschlagende Nachricht hält die anderen nicht mehr auf und bleibt ungelesen |

Erweiterungen, die E-Mails als Inhalt importieren (die IMAP-Quelle von sqliimport, die Postfach-Dokumente von ezodf),
lesen E-Mails als zu importierende Dokumente, nicht als zu beantwortende oder zu zählende Post; sie brauchen keine der
Regeln.

Eine Erweiterung, die auch auf einem älteren Kernel laufen kann, prüft `class_exists( 'expMailReceive' )` und behält für
diesen Fall ihren eigenen Code.

## Tests

`tests/tests/kernel/classes/mail/expMailReceiveTest.php` läuft ohne Datenbank. Er startet nachgebildete IMAP- und
POP3-Server auf 127.0.0.1 (`tests/toolkit/expfakemailserver.php`, `tests/toolkit/extras/fakemailserver/server.php`,
gestartet über `ezpLoopbackServer`, sodass sie weder die Deskriptoren des Tests halten noch ihn überleben) und prüft:

- Lesen und Löschen nur der gespeicherten Nachrichten (IMAP `STORE \Deleted` und `EXPUNGE`, POP3 `DELE` und `QUIT`),
  ohne das Passwort im Protokoll des Servers;
- kein `EXPUNGE`, wenn nichts gelöscht wird, und einen Probelauf, der die Nachricht ungelesen lässt;
- vier Konten, eines mit falschem Passwort und eines, dessen Server nicht läuft: die anderen beiden werden gelesen;
- den Bounce-Leser gegen beide Server: eine Verzögerungsmeldung wird verstanden und gelöscht, eine automatische Antwort
  bleibt;
- die Kosten: zu erkennen, was eine Nachricht ist, dauert wenige Mikrosekunden (gemessen 3,2 µs je Nachricht unter
  PHP 8.5, gegenüber 4,1 µs für die eigene Kopie der Regel im Newsletter, die jede Zeile der Nachricht liest).

Ein Test eines anderen Lesers startet denselben Server:

```php
require_once 'tests/toolkit/expfakemailserver.php';
$server = expFakeMailServer::start( 'pop3', array( $raw1, $raw2 ) );
$account = $server->account();          // für expMailReceive::connect()
// ... lesen ...
$server->commands();                    // was der Client gesendet hat
$server->left();                        // die Nummern, die noch auf dem Server liegen
$server->stop();
```
