# Ihre Geräte: angemeldete Sitzungen je Benutzer

Seit 6.0.15 wird jede Anmeldung als Sitzung ihres Benutzers aufgezeichnet. Ein Benutzer sieht auf der Seite
**Ihre Geräte** (`user/sessions`), wo das Konto gerade angemeldet ist, und meldet eine Sitzung oder alle anderen
Sitzungen ab; ein Administrator mit der Richtlinie `user/sessions` tut dasselbe für jeden Benutzer
(`user/sessions/<Benutzer-ID>`). Eine Anmeldung von einem Gerät, das das Konto in letzter Zeit nicht benutzt hat,
schickt dem Benutzer eine E-Mail. Die Aufbewahrung läuft als Cronjob, und jedes Abmelden auf der Seite steht im
Audit-Protokoll.

Die Anleitung für Benutzer und Administratoren ist [Ihre Geräte und Sitzungen](../../anleitungen/ihre-geraete-und-sitzungen.md).
Die Seite über den Session-Handler und die Sitzungstabelle der Administration ist [Sitzungen](../../anleitungen/sitzungen.md).

## Was eine Sitzung zeigt

| Angezeigt | Woher es kommt |
|---|---|
| Browser und Version, System und Version, Geräteart (Computer, Telefon, Tablet, Programm oder App, automatischer Client) | Der Header `User-Agent` der Anmeldung, gelesen von `expUserAgent::parse()`; nichts wird anderswo nachgeschlagen |
| Adresse | Die Adresse der letzten Anfrage (`eZSys::clientIP()`), als ihr Netz angezeigt: `203.0.113.x` bei IPv4, `2001:db8:85a3::/48` bei IPv6. Die Adresse der Anmeldung steht dabei, wenn sie abweicht. Die vollständige Adresse wird gespeichert, und die Seite des Administrators zeigt sie |
| Angemeldet | Zeitpunkt der Anmeldung und ihre Methode: Passwort, Passwort und Code der Zwei-Schritt-Anmeldung (der zweite Schritt von `user2fa/verify`), REST, SOAP oder WebDAV (nach dem Einstiegsskript), Single Sign-on (ein Login-Handler, der auf einer anderen Seite anmeldet) oder „bevor Sitzungen aufgezeichnet wurden“ |
| Zuletzt aktiv | Die letzte Anfrage, höchstens alle `TouchInterval` Sekunden geschrieben oder wenn die Adresse wechselt |
| Bleibt angemeldet | „Angemeldet bleiben, bis <Datum>“, wenn bei der Anmeldung „Angemeldet bleiben“ angekreuzt war (`[Session] RememberMeTimeout`, 360 Tage, standardmäßig angekreuzt), sonst „bis der Browser geschlossen wird“ |
| Dieses Gerät | Die Sitzung, mit der die Seite angesehen wird; sie kann in der Liste nicht abgemeldet werden (stattdessen abmelden) |

Unter den laufenden Sitzungen listet **Kürzlich beendet** die Sitzungen, die innerhalb von `RetentionDays` geendet
haben: abgemeldet, auf der Seite abgemeldet, von einem Administrator, mit „alle anderen Sitzungen“, durch eine neue
Anmeldung im selben Browser ersetzt, durch eine Passwortänderung beendet oder abgelaufen.

## Wie eine Sitzung beendet wird, mit jedem Session-Handler

Eine Sitzung wird bei ihrer nächsten Anfrage beendet, gleich welcher Handler die Sitzungen speichert (die Dateien von
PHP, der Standard; der Datenbank-Handler; Symfony):

1. Bei einer Anmeldung (`eZUser::setCurrentlyLoggedInUser()`) kommt ein zufälliges Token aus 32 Bytes in die Sitzung
   (`expUserSessionToken`) und nur sein SHA-256 in die Tabelle. Die Session-ID selbst wird nie gespeichert: Sie
   wechselt (eine neue ID bei der Anmeldung und beim zweiten Schritt der Zwei-Schritt-Anmeldung) und wäre ein
   Schlüssel zur Sitzung.
2. Bei jeder Anfrage eines angemeldeten Benutzers sucht `eZUser::currentUser()` die Zeile über den Hash des Tokens
   (eine Abfrage über einen eindeutigen Index, einmal je Anfrage). Ist die Zeile beendet oder weg, wird die Anfrage
   sofort abgemeldet: Daten, ID und Cookie der Sitzung werden entfernt (`eZUser::logoutCurrent()`), und der Browser
   sieht die Anmeldeseite.
3. **„Angemeldet bleiben“ ist das Session-Cookie selbst.** Es wird `RememberMeTimeout` lang aufbewahrt und bringt die
   Sitzung zurück, nachdem der Browser geschlossen war. Das Cookie einer abgemeldeten Sitzung meldet niemanden wieder
   an, weder im Browser, in dem es gesetzt wurde, noch dort, wohin es kopiert wurde: Die Sitzung, die es nennt, hat
   keine laufende Zeile.
4. Mit dem Datenbank-Session-Handler löscht „Alle anderen Sitzungen abmelden“ zusätzlich sofort die Zeilen der
   anderen Sitzungen in `ezsession` (`deleteOtherSessionsOfUser()`), und „Überall abmelden“ des Administrators
   löscht alle (`deleteByUserIDs()`).

Eine Sitzung, die angemeldet wurde, bevor es die Tabelle gab, hat kein Token; bei ihrer nächsten Anfrage bekommt sie
eines und eine Zeile mit der Methode „bevor Sitzungen aufgezeichnet wurden“. Von da an steht sie in der Liste und kann
abgemeldet werden wie jede andere.

Eine Passwortänderung ([PasswordSettings] `EndOtherSessions`) beendet auch die anderen Zeilen des Benutzers (Grund
`password_change`), neben dem Passwortstempel, der diese Sitzungen schon abmeldet.

Nicht aufgezeichnet: eine REST-, WebDAV- oder SOAP-Anfrage, die sich nur für sich selbst anmeldet, ohne Sitzung (ein
App-Passwort, ein persönlicher API-Schlüssel): Sie hat kein Cookie, das abgemeldet werden könnte. Die Prüfung des
zweiten Schritts auf diesen Kanälen bleibt, wie sie ist: Nur das Anmeldeformular im Web fragt nach dem zweiten Schritt.

## E-Mail zu einer neuen Anmeldung

Kommt eine Anmeldung von einer Kombination aus Browser, System und Geräteart, die das Konto innerhalb von
`RetentionDays` nicht benutzt hat, bekommt der Benutzer **Neue Anmeldung bei Ihrem Konto** (Template
`design:user/new_signin_mail.tpl`, Mail-Kategorie `security`): das Gerät, die Zeit, die ungefähre Adresse und den Link
zu Ihre Geräte. Die allererste Anmeldung eines Kontos ist nicht neu: Es gibt nichts, womit sie verglichen werden könnte
(`FirstSignInMail=enabled` ändert das).

- Standardmäßig an (`NewDeviceMail=enabled`). Jeder Benutzer schaltet sie mit dem Kästchen **Mir eine E-Mail schicken,
  wenn mein Konto sich von einem neuen Gerät oder Browser anmeldet** auf Ihre Geräte ab oder an; die Wahl ist die
  Benutzereinstellung `expsession_signin_mail` (`0` aus). Die Seite **E-Mail-Einstellungen** verweist dorthin.
- `NewDeviceAdminMail=enabled` schickt zusätzlich einen Hinweis an `AdminMailReceivers[]` (sonst
  `[MailSettings] AdminEmail`), Kategorie `admin`, Template `design:user/new_signin_admin_mail.tpl`, mit dem Link zur
  Sitzungsseite des Benutzers.
- Die Mail geht wie jede andere durch das Mail-Gate; mit `[MailSettings] Transport=file` wird sie nach `var/log/mail`
  geschrieben und nie verschickt.

## Einstellungen: site.ini [UserSessionSettings]

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Tracking` | `enabled` | `disabled`: Es werden keine Zeilen geschrieben, keine Sitzung wird geprüft, die Seite sagt es |
| `TouchInterval` | `300` | Sekunden zwischen zwei Schreibvorgängen der letzten Benutzung (oder wenn die Adresse wechselt) |
| `RetentionDays` | `90` | Tage, die eine beendete Sitzung aufbewahrt wird (Kürzlich beendet, die Prüfung auf neue Geräte) |
| `NewDeviceMail` | `enabled` | Die E-Mail an den Benutzer über eine Anmeldung von einem neuen Gerät |
| `NewDeviceAdminMail` | `disabled` | Derselbe Hinweis an die Administratoren |
| `AdminMailReceivers[]` | (leer) | Wer diesen Hinweis bekommt; leer: `[MailSettings] AdminEmail` |
| `FirstSignInMail` | `disabled` | Ob die erste Anmeldung eines Kontos als neues Gerät zählt |

`[Session] RememberMeTimeout` (360 Tage) und `SessionTimeout` (360 Tage) bleiben unverändert.

## Berechtigungen

- **Die eigenen Geräte**: jeder angemeldete Benutzer (`user/login`). Der anonyme Benutzer bekommt „Zugriff verweigert“.
- **Die Sitzungen eines anderen Benutzers**: die Richtlinienfunktion **user/sessions** („sehen, wo andere Benutzer
  angemeldet sind, und sie abmelden“). Die Kontoseite eines Benutzers (`user/edit/<id>`) zeigt einem Betrachter, der sie
  hat, die Karte **Sitzungen**. Administratoren mit allen Rechten haben sie.

Jede Änderung ist ein POST mit einem Token der Seite (ein HMAC des Tokens der Sitzung), neben dem Formular-Token von
ezformtoken; danach leitet die Seite auf sich selbst um und zeigt das Ergebnis einmal.

## Audit

| Ereignis | Wann |
|---|---|
| `access.session.login` | Eine Anmeldung (wie bisher), jetzt mit `after.session`: der Zeilen-ID |
| `access.session.logout` | Eine Abmeldung (wie bisher), mit `after.session` |
| `access.session.revoke` | Eine auf der Seite abgemeldete Sitzung: `after.count`, `after.sessions` (Zeilen-IDs), `after.scope` (one, others, all), `after.by` (user, admin), `after.actor_id`; Grund `user_request` oder `admin` |
| `access.session.new_device` | Eine Anmeldung von einem neuen Gerät (die E-Mail geht hinaus): Browser, System, Gerät, Methode |

## Aufbewahrung: der Cronjob usersessions.php

Im Teil `infrequent` und für sich (`php runcronjobs.php usersessions`):

- eine Sitzung, die `[Session] SessionTimeout` lang nicht benutzt wurde, wird als **abgelaufen** beendet (auch der
  Server hat sie vergessen);
- eine beendete Sitzung, die älter als `RetentionDays` ist, wird entfernt;
- die Zeilen von Benutzern, die es nicht mehr gibt, werden entfernt.

## Die Tabelle expuser_session

Eine Zeile je Anmeldung: `id`, `user_id`, `token_hash` (eindeutig), `created`, `last_active`, `first_ip`, `last_ip`,
`user_agent` (höchstens 512 Zeichen), `browser`, `os`, `device_type`, `method`, `remember_until`, `siteaccess`,
`ended`, `ended_by`, `end_reason`; Indizes auf (`user_id`, `ended`) und (`ended`, `last_active`).

- Neue Installationen bekommen sie aus `share/db_schema.dba` (MySQL/MariaDB, PostgreSQL, SQLite, Oracle, MongoDB).
- Ein Upgrade von 6.0.0: `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` legt sie an, wenn sie fehlt (MySQL,
  PostgreSQL, SQLite), oder, auf jeder Engine, `php update/common/scripts/6.0/createusersessiontable.php`
  (`--dry-run` sagt, ob sie fehlt).
- Ohne die Tabelle wird nichts aufgezeichnet, und jede Seite arbeitet wie bisher; die Seite Ihre Geräte sagt, was
  auszuführen ist.

## Entwickler

- `expUserSessionTracker::setMethodHint( 'single_sign_on' )` vor `eZUser::setCurrentlyLoggedInUser()` nennt die
  Methode einer Anmeldung, die ein Login-Handler vornimmt.
- `expUserSessionTracker::revoke( $userID, $sessionID, $actorID )`, `revokeOthers()`, `revokeAll()`,
  `endOtherRows( $userID, $reason, $actorID )`, `listForUser( $userID )`, `cleanup()`.
- `expUserAgent::parse()`, `label()`, `approximateIP()`: einfache Funktionen ohne Zustand.
- Tests: `tests/tests/kernel/classes/user/expUserSessionTest.php`.
