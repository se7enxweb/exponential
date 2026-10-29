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
| Adresse | Die Adresse der letzten Anfrage (`eZSys::clientIP()`), als ihr Netz angezeigt: `203.0.113.x` bei IPv4, `2001:db8:85a3::/48` bei IPv6. Die Adresse der Anmeldung steht dabei, wenn sie abweicht. Was gespeichert wird, bestimmt `RecordIP` (siehe [Adressen und Datenschutz](#adressen-und-datenschutz)); die Seite des Administrators zeigt die gespeicherte Adresse |
| Ort | Der ungefähre Ort der Anmeldung („Berlin, Land Berlin, Deutschland“), nur mit einer lokalen GeoIP-Datenbank (`GeoIPDatabase`, siehe [Ort](#ort-aus-einer-lokalen-geoip-datenbank)) |
| Angemeldet | Zeitpunkt der Anmeldung und ihre Methode: Passwort, Passwort und Code der Zwei-Schritt-Anmeldung (der zweite Schritt von `user2fa/verify`), REST, SOAP oder WebDAV (nach dem Einstiegsskript), Single Sign-on (ein Login-Handler, der auf einer anderen Seite anmeldet) oder „bevor Sitzungen aufgezeichnet wurden“; dazu der zweite Schritt, wenn einer benutzt wurde: eine Authentifizierungs-App, ein Code per E-Mail, ein Wiederherstellungscode, ein Passkey |
| Angemeldet bei | Der Host, auf dem die Anmeldung geschah (`www.example.com`), und der Siteaccess |
| Zuletzt aktiv | Die letzte Anfrage, höchstens alle `TouchInterval` Sekunden (60) geschrieben oder wenn die Adresse wechselt |
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

## Adressen und Datenschutz

`RecordIP` bestimmt, wie die Adressen einer Sitzung gespeichert werden:

| Wert | Gespeichert |
|---|---|
| `audit` (Standard) | Wie das Audit sie speichert: keine bei audit.ini `[UserAuditSettings] RecordIP=disabled` oder `[AuditPrivacySettings] Field[actor.ip]=off`, das Netz bei `truncate` (dort der Standard) oder `hash`, die vollständige Adresse bei `full` |
| `full` | Die vollständige Adresse |
| `truncated` | Nur das Netz: `203.0.113.x`, `2001:db8:85a3::/48` |
| `disabled` | Keine Adresse; die Seite sagt „Nicht gespeichert“ |

Der Ort (unten) wird aus der vollständigen Adresse im Speicher nachgeschlagen, bevor sie gekürzt wird; die
vollständige Adresse wird dann nicht aufbewahrt.

## Ort aus einer lokalen GeoIP-Datenbank

Ist `GeoIPDatabase` auf eine Datenbankdatei im Format MaxMind DB gesetzt (`.mmdb`, etwa GeoLite2 City oder Country
oder DB-IP Lite), relativ zur Installation oder absolut, und ist ein Leser installiert (die PHP-Erweiterung
`maxminddb` oder das Composer-Paket `maxmind-db/reader`, beide liefern `MaxMind\Db\Reader`), behält jede Anmeldung ihr
Land und einen ungefähren Ort. Kein anderer Server wird je gefragt, und Adressen privater und reservierter Netze werden
nicht nachgeschlagen. `GeoIPLanguage` wählt die Sprache der Namen (Englisch, wenn ein Name darin fehlt). Ohne Datenbank
wird nichts nachgeschlagen, und nichts ändert sich.

Eine Anmeldung aus einem Land, aus dem sich das Konto innerhalb von `RetentionDays` nicht angemeldet hat, ist ein
**neuer Ort**: das Audit-Ereignis `access.session.new_location` und dieselbe E-Mail wie bei einem neuen Gerät (eine
E-Mail, wenn beides neu ist). Die erste Anmeldung mit einem Land ist nicht neu.

## E-Mail zu einer neuen Anmeldung

Kommt eine Anmeldung von einer Kombination aus Browser, System und Geräteart, die das Konto innerhalb von
`RetentionDays` nicht benutzt hat, oder von einem neuen Ort, bekommt der Benutzer **Neue Anmeldung bei Ihrem Konto**
(Template `design:user/new_signin_mail.tpl`, Mail-Kategorie `security`): das Gerät, die Zeit, den ungefähren Ort und
die ungefähre Adresse und die Links zu Ihre Geräte und zur Sicherheitsaktivität („Konto absichern“). Die allererste Anmeldung eines Kontos ist nicht neu: Es gibt nichts, womit sie verglichen werden könnte
(`FirstSignInMail=enabled` ändert das).

- Standardmäßig an (`NewDeviceMail=enabled`). Jeder Benutzer schaltet sie mit dem Kästchen **Mir eine E-Mail schicken,
  wenn mein Konto sich von einem neuen Gerät oder Browser anmeldet** auf Ihre Geräte ab oder an; die Wahl ist die
  Benutzereinstellung `expsession_signin_mail` (`0` aus). Die Seite **E-Mail-Einstellungen** verweist dorthin.
- `NewDeviceAdminMail=enabled` schickt zusätzlich einen Hinweis an `AdminMailReceivers[]` (sonst
  `[MailSettings] AdminEmail`), Kategorie `admin`, Template `design:user/new_signin_admin_mail.tpl`, mit dem Link zur
  Sitzungsseite des Benutzers.
- Die Mail geht wie jede andere durch das Mail-Gate; mit `[MailSettings] Transport=file` wird sie nach `var/log/mail`
  geschrieben und nie verschickt.

## Sicherheitsaktivität

**Sicherheitsaktivität** (`user/security`) ist die „letzte Sicherheitsaktivität“ des Kontos: eine Zeitleiste seiner
Sicherheitsereignisse, die neuesten zuerst, oben die Anmeldungen, fehlgeschlagenen Versuche und Kontoänderungen der
letzten 30 Tage. Jeder angemeldete Benutzer hat sie für das eigene Konto; `user/security/<Benutzer-ID>` zeigt einem
Betrachter mit der Richtlinie `user/sessions` ein anderes Konto. Verlinkt von Ihre Geräte, der Kontoseite (die Karte
**Sicherheitsaktivität** mit den fehlgeschlagenen Anmeldungen der letzten 30 Tage), dem Benutzermenü der Kopfzeile und
dem Kasten „Aktueller Benutzer“. Nur lesend.

| Angezeigt | Ereignisse |
|---|---|
| Anmeldungen und Sitzungen | `access.session.login`, `.login.failed`, `.logout`, `.revoke`, `.new_device`, `.new_location`, `.reauth`, `.reauth.failed`, `access.passkey.use`, `.use.failed`, `access.user.second_factor.refused`, `access.user.two_factor.verify.failed` |
| Kontoänderungen | `access.user.lock`, `.unlock`, `.password.change`, `.password.change.failed`, `.password.reset`, `.password.reset.request`, `.email.change`, `.login.change`, `.two_factor.enroll`, `.two_factor.remove`, `.two_factor.recovery_used`, `.two_factor.recovery_issued`, `.app_password.create`, `.app_password.revoke`, `.enable`, `.disable`, `access.passkey.add`, `.remove` |
| Braucht Aufmerksamkeit | Fehlschläge, Ablehnungen, Sperren, neue Geräte und Orte sowie Änderungen an Passwort, E-Mail, Zwei-Schritt-Anmeldung und Passkeys |

Für die Seite wird nichts aufgezeichnet: Sie liest das [Benutzer-Auditprotokoll](benutzer-auditprotokoll.md)
(`expuser_audit`), das die Senke `useraudit` des Audits aus denselben Ereignissen füllt (`expUserSecurityActivity`).
Ohne diese Tabelle und auf MongoDB sagt die Seite das. Die Ereignisse bleiben `audit.ini [UserAuditSettings]
RetentionDays` (730) Tage. Jede Zeile zeigt die Zeit, wer gehandelt hat (der Benutzer, ein Administrator mit Login
oder niemand Angemeldetes bei einem fehlgeschlagenen Versuch), die ungefähre Adresse, den Host und was das Ereignis
nennt (Methode, Browser, System, Ort, Zahl der Sitzungen, verbleibende Codes); nie ein Geheimnis.

Die Zwei-Schritt-Ereignisse `access.user.two_factor.*` zeichnet die Zwei-Schritt-Erweiterung über
`expSignInSecondFactor::audit()` auf.

## Sicherheitswarnungen

Neben „Neue Anmeldung“ (oben) und „Ihr Passwort wurde geändert“ (`[PasswordSettings] ChangeNotificationMail`) bekommt
der Inhaber eines Kontos eine **Sicherheitswarnung** (Template `design:user/security_alert_mail.tpl`, Mail-Kategorie
`security`), wenn:

| Warnung | Ereignis |
|---|---|
| Die Zwei-Schritt-Anmeldung wurde ein- / ausgeschaltet | `access.user.two_factor.enroll`, `.remove` |
| Ein Wiederherstellungscode wurde benutzt (mit den verbleibenden Codes) | `access.user.two_factor.recovery_used` |
| Fehlgeschlagene Anmeldeversuche | `access.session.login.failed`, einmal, wenn die Fehlschläge in Folge `FailedSignInAlertThreshold` (5) erreichen |
| Das Konto wurde gesperrt | `access.user.lock` (eine E-Mail, auch wenn die Fehlschläge gleichzeitig die Schwelle erreichten) |

Jede Warnung sagt, was geschah, wann und aus welchem Netz, und verlinkt auf **Konto absichern** (die
Sicherheitsaktivität) und Ihre Geräte; die Sperr-Warnung auch auf „Neues Passwort anfordern“. Ein Benutzer kann diese
Warnungen nicht abschalten; die Website kann es (`SecurityAlertMail=disabled`). Sie werden aus dem
Benutzer-Auditprotokoll gesendet (`expUserSecurityAlerts`): Wenn die Senke `useraudit` die Ereignisse einer Anfrage
speichert, werden die neuen Zeilen geprüft; ein erneut aus dem Spool zugestellter Stapel warnt niemanden doppelt, und
Zeilen, die älter als eine Stunde sind, warnen nie. Ohne Benutzer-Auditprotokoll wird keine Warnung gesendet. Die
E-Mail geht durch das Mail-Gate; mit `[MailSettings] Transport=file` wird sie nach `var/log/mail` geschrieben.

## Datenexport und Löschung

- **Meine Anmeldedaten herunterladen** auf der Sicherheitsaktivität (nur das eigene Konto, zuerst „Bestätigen Sie,
  dass Sie es sind“, `[ReauthGroup_export]`): eine JSON-Datei mit jeder Sitzung des Kontos und ihren Details (nie dem
  Hash des Tokens) und jedem Sicherheitsereignis des Benutzer-Auditprotokolls dazu; aufgezeichnet als
  `data.export.json` mit der Zahl der Zeilen.
- **Ein Konto entfernen** (`eZUser::removeUser()`) löscht seine Sitzungen und ihre Details (`eraseForUser()`); seine
  Sicherheitsereignisse bleiben im Benutzer-Auditprotokoll bis zu dessen `RetentionDays`, als Nachweis der Entfernung.
- **Aufbewahrung**: Der Cronjob `usersessions.php` beendet Sitzungen, die `[Session] SessionTimeout` lang nicht benutzt
  wurden (eine mit „Angemeldet bleiben“ bleibt bis zu ihrem Datum, 360 Tage), entfernt beendete Sitzungen nach
  `RetentionDays` und die Sitzungen entfernter Benutzer; der Cronjob-Teil `useraudit` entfernt Sicherheitsereignisse nach
  `audit.ini [UserAuditSettings] RetentionDays`.

## Anmeldebericht

**Benutzer > Anmeldebericht** (`user/sessionreport`, die neue Richtlinienfunktion **user/sessions_manage**) ist die
Sicht des Administrators auf die ganze Website:

- die jetzt laufenden Sitzungen und die Zahl der Benutzer, denen sie gehören, die Anmeldungen und fehlgeschlagenen
  Versuche von heute;
- **Einen Benutzer überall abmelden**: eine Benutzer-ID, ein Benutzername oder eine E-Mail-Adresse; jede Sitzung dieses
  Kontos endet bei ihrer nächsten Anfrage, auch die mit „Angemeldet bleiben“ (`revokeAll()`, aufgezeichnet als
  `access.session.revoke`, scope `all`; mit dem Datenbank-Session-Handler werden seine Sitzungen sofort gelöscht);
- die laufenden Sitzungen aller Benutzer, die zuletzt benutzten zuerst (die 100 neuesten), mit dem Benutzer (Link zu
  seiner Sitzungsseite), Gerät, gespeicherter Adresse und Ort, Anmeldezeit und -methode, letzter Benutzung und einer
  Schaltfläche zum Abmelden;
- die letzten 14 Tage: Anmeldungen und fehlgeschlagene Versuche (aus dem Benutzer-Auditprotokoll, wenn es da ist) und
  neue Sitzungen, mit Balken.

Der Bericht liest `expuser_session` und arbeitet deshalb mit jedem Session-Handler, auch mit den PHP-Dateien des
Standard-Handlers. **Setup > Sitzungen** (`setup/session`) behält seine eigenen Listen für den Datenbank-Handler; mit
einem anderen Handler verlinkt es jetzt auf den Bericht. „Bestätigen Sie, dass Sie es sind“ fragt, bevor hier eine
Sitzung oder ein Benutzer abgemeldet wird (`[ReauthGroup_users]`).

## Einstellungen: site.ini [UserSessionSettings]

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Tracking` | `enabled` | `disabled`: Es werden keine Zeilen geschrieben, keine Sitzung wird geprüft, die Seite sagt es |
| `TouchInterval` | `60` | Sekunden zwischen zwei Schreibvorgängen der letzten Benutzung (oder wenn die Adresse wechselt) |
| `RecordIP` | `audit` | Wie Adressen gespeichert werden: `audit`, `full`, `truncated`, `disabled` |
| `GeoIPDatabase` | (leer) | Eine lokale `.mmdb`-Datenbank für den Ort einer Anmeldung; leer: keine |
| `GeoIPLanguage` | `en` | Die Sprache der Ortsnamen |
| `SecurityAlertMail` | `enabled` | Die Sicherheitswarnungen (Zwei-Schritt-Anmeldung, Wiederherstellungscode, Fehlschläge, Sperre); ein Benutzer kann sie nicht abschalten |
| `FailedSignInAlertThreshold` | `5` | Fehlgeschlagene Anmeldungen in Folge, die eine Warnung senden; `0`: nie |
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
- **Der Anmeldebericht** und das Abmelden eines beliebigen Benutzers überall: die Richtlinienfunktion
  **user/sessions_manage**.
- **Alle anderen Sitzungen abmelden** im eigenen Konto fragt zuerst „Bestätigen Sie, dass Sie es sind“
  (`[ReauthGroup_account]`), mit dem Passwort, einem Passkey oder dem Code der Authentifizierungs-App; ein einzelnes
  anderes Gerät abzumelden fragt nicht.

Jede Änderung ist ein POST mit einem Token der Seite (ein HMAC des Tokens der Sitzung), neben dem Formular-Token von
ezformtoken; danach leitet die Seite auf sich selbst um und zeigt das Ergebnis einmal.

## Audit

| Ereignis | Wann |
|---|---|
| `access.session.login` | Eine Anmeldung (wie bisher), jetzt mit `after.session`: der Zeilen-ID |
| `access.session.logout` | Eine Abmeldung (wie bisher), mit `after.session` |
| `access.session.revoke` | Eine auf der Seite abgemeldete Sitzung: `after.count`, `after.sessions` (Zeilen-IDs), `after.scope` (one, others, all), `after.by` (user, admin), `after.actor_id`; Grund `user_request` oder `admin` |
| `access.session.new_device` | Eine Anmeldung von einem neuen Gerät (die E-Mail geht hinaus): Browser, System, Gerät, Methode |
| `access.session.new_location` | Eine Anmeldung aus einem neuen Land (nur mit GeoIP-Datenbank): Land, Ort, Methode |

## Aufbewahrung: der Cronjob usersessions.php

Im Teil `infrequent` und für sich (`php runcronjobs.php usersessions`):

- eine Sitzung, die `[Session] SessionTimeout` lang nicht benutzt wurde, wird als **abgelaufen** beendet (auch der
  Server hat sie vergessen), außer sie bleibt mit „Angemeldet bleiben“ erhalten: dann bleibt sie bis zu diesem Datum
  (`[Session] RememberMeTimeout`, 360 Tage);
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

## Die Tabelle expuser_session_info

Eine Zeile je Sitzung, mit dem Schlüssel `session_id` (die Zeile von `expuser_session`): `user_id`, `host`, `country`
(ISO-Code), `location`, `auth_detail` (`totp`, `email`, `recovery`, `passkey`, `oauth`); ein Index auf `user_id`. Eine
eigene Tabelle, damit eine Installation, die `expuser_session` schon hat, weiter aufzeichnet, bis sie angelegt ist:
dieselben Update-Dateien und dasselbe Skript (`createusersessiontable.php`) legen sie an. Ihre Zeilen werden mit ihren
Sitzungen entfernt.

## Entwickler

- `expUserSessionTracker::setMethodHint( 'single_sign_on' )` vor `eZUser::setCurrentlyLoggedInUser()` nennt die
  Methode einer Anmeldung, die ein Login-Handler vornimmt; ein zweites Argument nennt den zweiten Schritt
  (`setMethodHint( 'two_factor', 'totp' )`). Der Hinweis gilt nur in der Anfrage, die ihn gesetzt hat, so dass ein
  dauerhafter Worker (Velocity) ihn nie weitergibt.
- `expUserSessionTracker::exportForUser( $userID )` (jede Zeile mit ihren Details, ohne den Hash des Tokens) und
  `eraseForUser( $userID )` dienen einem Datenexport und einer Löschung.
- `expUserGeoIP::lookup( $ip )`, `fromRecord()`; `expUserGeoIP::setLookup()` ersetzt die Datenbank in Tests.
- `expUserSessionTracker::revoke( $userID, $sessionID, $actorID )`, `revokeOthers()`, `revokeAll()`,
  `endOtherRows( $userID, $reason, $actorID )`, `listForUser( $userID )`, `cleanup()`.
- `expUserAgent::parse()`, `label()`, `approximateIP()`: einfache Funktionen ohne Zustand.
- Tests: `tests/tests/kernel/classes/user/expUserSessionTest.php`, `expUserSessionInfoTest.php`.
