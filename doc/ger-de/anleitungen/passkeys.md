# Passkeys: Anmelden mit Fingerabdruck, Gesicht oder Sicherheitsschlüssel

Diese Anleitung erklärt die Passkeys von Exponential 6.0.15. Ein Passkey ersetzt bei der Anmeldung das Passwort: Der
Browser oder das Gerät fragt nach Fingerabdruck, Gesicht, der PIN der Bildschirmsperre oder einer Berührung des
Sicherheitsschlüssels, und die Site prüft eine Signatur, die mit einem Schlüssel erstellt wurde, der das Gerät nie
verlässt. Die Site speichert nur die öffentliche Hälfte dieses Schlüssels; nichts auf dem Server taugt zum Anmelden.

Sie richtet sich an zwei Lesergruppen. **Benutzer** lesen die Abschnitte 1 bis 4. **Administratoren** lesen die
Abschnitte 5 bis 12.

[Anleitungen](README.md) · Siehe auch: [Identität bestätigen](identitaet-bestaetigen.md) ·
[Zweistufige Anmeldung](zwei-faktor-anmeldung.md) · [Sicherheit und Audit](sicherheit-und-audit.md) ·
Änderungshinweis: [Passkeys und „Identität bestätigen“](../bc/6.0/passkeys-und-identitaet-bestaetigen.md)

## Kurz gesagt

- **Mein Konto > Passkeys** (`/user/passkeys`): einen Passkey hinzufügen (der Passkey-Dialog des Browsers, danach ein
  selbst gewählter Name), sehen, welcher Authentifikator ihn hält („iCloud-Schlüsselbund“, „Google Password
  Manager“, „YubiKey 5 Series“), ob er synchronisiert oder an ein Gerät gebunden ist, wann er hinzugefügt und zuletzt
  benutzt wurde; Passkeys umbenennen und entfernen.
- Das Anmeldeformular hat neben dem Passwort **Mit einem Passkey anmelden**. Wo der Browser es anbietet, listet auch
  das Feld für den Benutzernamen die eigenen Passkeys (Passkey-Autofill); die Auswahl meldet an.
- Ein Passkey beantwortet auch den zweiten Schritt der zweistufigen Anmeldung und ist die Hälfte von „Identität
  bestätigen“ (Passwort und Passkey).
- Eine Richtlinie je Site, Benutzergruppe oder Konto macht Passkeys **optional**, **empfohlen** (einmal je Sitzung
  vorgeschlagen) oder **verpflichtend** und kann die Anmeldung mit Passwort für Konten mit Passkey abschalten.
- Administratoren sehen und widerrufen die Passkeys anderer Konten (`/user/passkeys/<Benutzer-ID>`, Richtlinie
  `user/passkeys_manage`).
- Sie bekommen eine E-Mail, wenn Ihrem Konto ein Passkey hinzugefügt oder einer entfernt wird.
- Passkeys gelten nur auf den Webseiten. Die REST-Schnittstelle, SOAP, WebDAV und die `expservices`-Anmeldung melden
  an wie bisher, und die Passkey-Richtlinie gilt für sie nie.

## Inhalt

1. [Was ein Passkey ist](#1-was-ein-passkey-ist)
2. [Passkeys hinzufügen, benennen und entfernen](#2-passkeys-hinzufügen-benennen-und-entfernen)
3. [Mit einem Passkey anmelden](#3-mit-einem-passkey-anmelden)
4. [Ein verlorenes oder ersetztes Gerät](#4-ein-verlorenes-oder-ersetztes-gerät)
5. [Einstellungen](#5-einstellungen)
6. [Richtlinie: optional, empfohlen, verpflichtend](#6-richtlinie-optional-empfohlen-verpflichtend)
7. [Die Passkeys eines anderen Kontos](#7-die-passkeys-eines-anderen-kontos)
8. [Die Tabelle und das Upgrade](#8-die-tabelle-und-das-upgrade)
9. [Zweistufige Anmeldung und die anderen Anmeldewege](#9-zweistufige-anmeldung-und-die-anderen-anmeldewege)
10. [Wie ein Passkey geprüft wird](#10-wie-ein-passkey-geprüft-wird)
11. [Audit und Sicherheitsmeldungen](#11-audit-und-sicherheitsmeldungen)
12. [Für Erweiterungen](#12-für-erweiterungen)

---

## 1. Was ein Passkey ist

Ein Passkey ist ein Schlüsselpaar, das Ihr Gerät (ein Telefon, ein Laptop, ein Passwortmanager oder ein
Hardware-Sicherheitsschlüssel) nur für diese Site erzeugt. Der private Schlüssel bleibt auf dem Gerät; viele
Passwortmanager synchronisieren ihn zwischen Ihren Geräten („Synchronisiert“ in der Liste). Die Site speichert den
öffentlichen Schlüssel und prüft jede Anmeldung damit. Ein Passkey lässt sich nicht abfischen: Der Browser benutzt ihn
nur auf der Site, für die er erstellt wurde.

## 2. Passkeys hinzufügen, benennen und entfernen

1. Öffnen Sie **Mein Konto** und die Karte **Passkeys**, oder gehen Sie zu `/user/passkeys`.
2. Die Seite fragt zuerst, ob Sie es sind (siehe [Identität bestätigen](identitaet-bestaetigen.md)).
3. Geben Sie unter **Einen Passkey hinzufügen** einen Namen ein, den Sie später wiedererkennen (etwa „Arbeitslaptop“
   oder „Passwortmanager“); ohne Namen heißt der Passkey „Passkey vom <Datum>“.
4. Wählen Sie **Einen Passkey hinzufügen** und folgen Sie dem Browser: Wählen Sie, wo er gespeichert wird, und
   bestätigen Sie mit Fingerabdruck, Gesicht, PIN oder Schlüssel.

Die Liste zeigt für jeden Passkey den Namen, den Authentifikator, der ihn hält (wenn der Browser der Site gesagt hat,
welcher es ist), wann er hinzugefügt und zuletzt benutzt wurde, ob er synchronisiert oder an ein Gerät oder einen
Schlüssel gebunden ist, und sein Verfahren (ES256, RS256 ...). **Umbenennen** ändert den Namen; **Entfernen** fragt
noch einmal und löscht den Passkey dann hier. Entfernen Sie ihn auch auf dem Gerät oder im Passwortmanager. Ein Konto
hat höchstens 20 Passkeys (`MaxPerUser`).

Ihren letzten Passkey können Sie nicht entfernen, solange Ihr Konto ohne ihn nicht anmelden könnte: wenn es kein
Passwort hat, wenn die Site für Sie die Anmeldung mit Passwort abgeschaltet hat oder wenn Ihr Konto einen Passkey haben
muss. Fügen Sie zuerst einen weiteren hinzu. Wann immer Ihrem Konto ein Passkey hinzugefügt oder einer entfernt wird,
bekommen Sie eine E-Mail; ein Passkey, den jemand anders hinzugefügt hat, bleibt so nicht unbemerkt.

Kann der Browser keine Passkeys erstellen, bleibt die Schaltfläche verborgen und die Seite sagt es; Ihr Passwort gilt
weiter.

## 3. Mit einem Passkey anmelden

Wählen Sie im Anmeldeformular **Mit einem Passkey anmelden** und bestätigen Sie auf dem Gerät. Wo der Browser
Passkey-Autofill kann, klicken Sie in das Feld für den Benutzernamen: Ihre Passkeys für die Site erscheinen dort, und
die Auswahl meldet an, ohne etwas zu tippen. „Angemeldet bleiben“ gilt wie beim Passwort.

Ein Passkey wird abgelehnt, wenn das Konto deaktiviert oder nach Fehlversuchen gesperrt ist, wenn das Konto den
Siteaccess nicht benutzen darf oder wenn der Passkey entfernt wurde. Das Formular sagt dann, warum.

## 4. Ein verlorenes oder ersetztes Gerät

Melden Sie sich mit Ihrem Passwort oder einem anderen Passkey an, öffnen Sie **Mein Konto > Passkeys** und entfernen
Sie den Passkey des verlorenen Geräts. Ein Passkey allein nützt ohne die Bildschirmsperre oder Biometrie des Geräts
nichts, aber nur das Entfernen stellt sicher, dass er nie wieder anmeldet. Können Sie sich nicht mehr anmelden, fragen
Sie einen Administrator: Er kann den Passkey widerrufen (Abschnitt 7) und ein neues Passwort setzen.

## 5. Einstellungen

`site.ini [PasskeySettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Passkeys` | `enabled` | `disabled`: Kein Passkey kann hinzugefügt oder benutzt werden; die gespeicherten bleiben |
| `SignInForm` | `enabled` | Die Passkey-Schaltfläche und Autofill im Anmeldeformular anbieten |
| `RelyingPartyID` | leer | Der Hostname, an den Passkeys gebunden sind. Leer: der Host der Anfrage. Eine übergeordnete Domain (`example.com`) lässt die Passkeys von `www.example.com` auch auf `admin.example.com` gelten; sie muss der Host oder eine übergeordnete Domain davon sein, sonst gilt der Host |
| `RelyingPartyIDMap[]` | leer | Eine Installation mit mehreren Hosts: die Relying-Party-ID je Host, `RelyingPartyIDMap[shop.example.org]=example.org`. Ein nicht genannter Host benutzt `RelyingPartyID`; eine ID, die nicht der Host oder eine übergeordnete Domain davon ist, wird übergangen |
| `RelyingPartyName` | leer | Der Name, den der Browser zeigt; leer: `[SiteSettings] SiteName` |
| `AllowedOrigins[]` | leer | Die Origins (`schema://host[:port]`), von denen Antworten kommen dürfen. Leer: der Origin der Anfrage. Nennen Sie jeden Host, auf dem die Passkeys benutzt werden, wenn die Relying-Party-ID eine übergeordnete Domain ist |
| `UserVerification` | `required` | `required`, `preferred` oder `discouraged` für „Identität bestätigen“. Die Anmeldung mit Passkey verlangt sie immer |
| `Algorithms[]` | `-7`, `-257`, `-8`, `-35`, `-36` | Angebotene COSE-Verfahren, bevorzugte zuerst: ES256, RS256, Ed25519 (braucht PHPs sodium-Erweiterung), ES384, ES512 |
| `ChallengeSeconds` | `300` | Sekunden, in denen eine Challenge beantwortet werden kann (30 bis 3600) |
| `MaxPerUser` | `20` | Passkeys je Konto |
| `Attestation` | `none` | Was ein neuer Passkey über seinen Hersteller sagen soll: `none`, `indirect` oder `direct`. Mit `indirect` behält jeder Browser die Modellkennung des Authentifikators (AAGUID), die ihn in der Liste benennt; der Attestierungsnachweis selbst wird nie herangezogen |
| `AuthenticatorNames[]` | leer | Namen von Authentifikatormodellen, die die eingebaute Liste nicht kennt oder anders nennt: `AuthenticatorNames[<aaguid>]=<Name>` |
| `Policy`, `PasswordSignIn`, `GroupPolicy[]`, `GroupPasswordSignIn[]`, `UserPolicy[]`, `UserPasswordSignIn[]`, `PolicyHandlers[]`, `ExemptViews[]` | siehe Abschnitt 6 | Die Passkey-Richtlinie |
| `ConsoleSlots[]` | leer | Templates unter der Liste auf `user/passkeys` (Abschnitt 12) |

Passkeys brauchen eine sichere Seite: HTTPS oder beim Entwickeln `http://localhost`. Eine IP-Adresse ist keine
gültige Relying-Party-ID.

**Die Namen der Authentifikatoren.** Die Liste nennt die verbreiteten Passkey-Anbieter und Sicherheitsschlüssel nach
der Modellkennung (AAGUID), die ein neuer Passkey mitbringt: iCloud-Schlüsselbund, Google Password Manager, Chrome
auf dem Mac, Windows Hello, 1Password, Bitwarden, Dashlane, Samsung Pass, KeePassXC, Proton Pass und die YubiKey-5-
Serie. Firefox leert die Modellkennung, wenn die Site die Attestierung `none` erbittet; solche Passkeys zeigen
„Unbekannt“, außer mit `Attestation=indirect`. Die Modellkennung nennt ein Produkt, nie ein Gerät oder eine Person.

**Site-Designs mit eigenem Anmelde-Template.** Die Anmeldeformulare von `design/standard`, `design/admin` und admin4
haben die Passkey-Schaltfläche; Designs, die `user/login.tpl` überschreiben, fügen sie nach ihrem eigenen Formular ein:

```
{include uri='design:user/parts/passkey_login.tpl'}
```

Dieser Teil zeichnet nichts, solange keine Passkeys angeboten werden. Das Passkey-Autofill des Browsers braucht
zusätzlich die Attribute `data-exp-passkey` und `autocomplete="username webauthn"` am eigenen Formular des Designs,
wie `design/standard/templates/user/login.tpl` zeigt.

## 6. Richtlinie: optional, empfohlen, verpflichtend

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Policy` | `optional` | Der Standard der Site: `optional` (Benutzer dürfen Passkeys hinzufügen), `preferred` (ein Benutzer ohne Passkey wird nach der Anmeldung einmal je Sitzung zu `user/passkeys` geschickt, mit **Nicht jetzt** zum Weitermachen), `required` (ein Benutzer ohne Passkey fügt vor jeder anderen Seite einen hinzu) |
| `PasswordSignIn` | `enabled` | `disabled`: Das Anmeldeformular lehnt das Passwort eines Kontos ab, das einen Passkey **hat**. Ein Konto ohne Passkey meldet sich weiter mit dem Passwort an, niemand wird ausgesperrt, und mit `required` fügt es dann einen hinzu |
| `GroupPolicy[<Gruppen-ID>]` | leer | Je Benutzergruppe (Content-Objekt-ID). Die strengste Angabe der Gruppen des Benutzers und `Policy` gilt |
| `GroupPasswordSignIn[<Gruppen-ID>]` | leer | `disabled` in irgendeiner Gruppe des Benutzers gilt |
| `UserPolicy[<Benutzer-ID>]`, `UserPasswordSignIn[<Benutzer-ID>]` | leer | Je Konto; gelten vor den Gruppen und dem Standard |
| `PolicyHandlers[]` | leer | Klassen, die `expPasskeyPolicyHandler` umsetzen, zuletzt gefragt (Abschnitt 12) |
| `ExemptViews[]` | `user/passkeys`, `user/logout`, `user/login`, `user/password`, `user2fa`, `ezjscore`, `user/preferences` | Ansichten (`modul` oder `modul/ansicht`), die offen bleiben, solange ein Passkey verlangt ist |

Zum Beispiel müssen Administratoren und Redakteure Passkeys benutzen und dürfen sich, sobald sie einen haben, nicht mehr
mit dem Passwort anmelden:

```ini
# settings/override/site.ini.append.php
[PasskeySettings]
GroupPolicy[12]=required
GroupPasswordSignIn[12]=disabled
GroupPolicy[13]=required
GroupPasswordSignIn[13]=disabled
```

Ein abgelehntes Passwort wird genau wie ein falsches beantwortet, niemand erfährt also, ob ein Konto existiert oder
Passkeys hat; wo irgendeine Einstellung die Anmeldung mit Passwort abschalten kann, ergänzt das Formular nach jeder
abgelehnten Anmeldung gleichermaßen, dass Konten, die sich nur mit Passkeys anmelden, die Passkey-Schaltfläche
benutzen. Ein abgelehntes Passwort zählt nicht als Fehlversuch des Kontos. Die Richtlinie gilt nur auf den Webseiten
(index.php und Velocity); REST, SOAP, WebDAV und die `expservices`-Anmeldung behalten ihre eigene Anmeldung.

Die Richtlinie greift gleich nach „Passwort bei der nächsten Anmeldung ändern“ und vor „Identität bestätigen“. Auch
der erste Passkey fragt „Identität bestätigen“; eine Anmeldung, die schon nach dem fragte, was gefragt würde (das
Passwort eines Kontos, das nichts weiter hat, das Passwort und der Code der Authenticator-App), zählt als diese
Bestätigung, ein gerade angemeldeter Benutzer wird also nicht noch einmal gefragt.

## 7. Die Passkeys eines anderen Kontos

`/user/passkeys/<Benutzer-ID>` zeigt die Passkeys eines anderen Kontos einem Betrachter mit der Richtlinie
`user/passkeys_manage` (die Kontoübersicht verlinkt dorthin). **Widerrufen** entfernt einen, auch den letzten: für ein
verlorenes oder gestohlenes Gerät. Der Inhaber bekommt eine E-Mail, das Audit verzeichnet `access.passkey.revoke` mit
dem Administrator, und der Inhaber meldet sich dann mit dem Passwort an, das der Administrator setzen kann. Dort kann
nichts hinzugefügt oder umbenannt werden: Ein Passkey entsteht auf dem Gerät des Inhabers. Das Öffnen der Seite fragt
„Identität bestätigen“ (Gruppe Benutzer).

## 8. Die Tabelle und das Upgrade

`exppasskey` hat eine Zeile je Passkey: `id`, `user_id`, `name`, `credential_id` (base64url), `credential_hash`
(SHA-256, eindeutig, danach sucht eine Anmeldung den Passkey), `public_key` (der COSE-Schlüssel, base64url),
`algorithm`, `sign_count`, `user_handle` (zufällig je Konto, nie die Benutzer-ID), `aaguid` (das Modell des
Authentifikators), `transports`, `backup_eligible`, `backup_state`, `created`, `last_used`, `last_ip`. Nichts darin ist
geheim.

- Neue Installationen bekommen sie aus `share/db_schema.dba` (jede Datenbank) oder den Kernel-Schemas von MySQL,
  PostgreSQL und SQLite.
- Upgrades: Das Ende von `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` legt sie nur an, wenn sie fehlt,
  oder auf jeder Datenbank, Oracle und MongoDB eingeschlossen:

```bash
php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user   # sagt exists oder missing
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Bis die Tabelle existiert, sagt die Passkey-Seite das, und das Anmeldeformular bietet keinen Passkey an. Erzeugen Sie
nach dem Upgrade die Kernel-Autoloads neu, leeren Sie die Caches für INI, Templates und Template-Overrides, laden Sie
PHP-FPM neu und starten Sie Velocity neu. Wird ein Konto entfernt, werden seine Passkeys mit entfernt.

## 9. Zweistufige Anmeldung und die anderen Anmeldewege

Ein Passkey mit Benutzerprüfung beweist zweierlei auf einmal: etwas, das Sie haben (das Gerät), und etwas, das Sie sind
oder wissen (Biometrie oder PIN). Daher:

- Eine Anmeldung mit Passkey ist die ganze Anmeldung; der zweite Schritt von `sevenx_authentication_2fa` wird nicht
  noch einmal gefragt.
- Nach einer Anmeldung mit Passwort bietet der zweite Schritt von `sevenx_authentication_2fa` neben dem Code der
  Authenticator-App **Einen Passkey benutzen** an. Ein Konto, das einen zweiten Schritt haben muss
  (`Enforce2FA`) und einen Passkey hat, braucht keine App: Sein zweiter Schritt ist der Passkey.

Auf den anderen Wegen ändert sich nichts: REST, SOAP, WebDAV und die `expservices`-Anmeldung melden mit dem Passwort an
(und mit dem zweiten Schritt, wo eine Installation sie schützt), genau wie bisher.

## 10. Wie ein Passkey geprüft wird

Der Kernel prüft WebAuthn selbst (`kernel/classes/webauthn`), ohne Bibliothek, auf PHP 8.0 bis 8.5:

- Jede Anfrage nach Optionen bekommt eine neue zufällige Challenge, in der Sitzung mit ihrem Zweck (Registrieren,
  Anmelden, Bestätigen), dem Benutzer und einem Ablauf gespeichert; die erste Antwort, die sie nennt, verbraucht sie,
  keine Antwort lässt sich also wiederholen.
- `clientDataJSON`: der Typ (`webauthn.create` oder `webauthn.get`), die Challenge, ein erlaubter Origin, nicht
  cross-origin.
- Authentifikatordaten: das SHA-256 der Relying-Party-ID, Benutzer anwesend, Benutzer geprüft, wo verlangt, ein
  Sicherungszustand nur für einen sicherbaren Schlüssel.
- Registrierung: der bestätigte Schlüssel (eine ID von höchstens 1023 Bytes) und ein öffentlicher COSE-Schlüssel eines
  angebotenen Verfahrens.
- Anmeldung: die Signatur über die Authentifikatordaten und den Hash von `clientDataJSON` mit dem gespeicherten
  Schlüssel (ES256, ES384, ES512, RS256, RS384, RS512 mit OpenSSL, Ed25519 mit sodium) und der Signaturzähler: Ein
  zählender Authentifikator muss hochzählen, sonst könnte der Passkey kopiert sein und wird abgelehnt.
- Attestierungsnachweise werden nicht geprüft: Die Site vertraut keinem bestimmten Authentifikatormodell.

## 11. Audit und Sicherheitsmeldungen

| Ereignis | Wann |
|---|---|
| `access.passkey.add` | ein Passkey wird hinzugefügt (oder abgelehnt: Ergebnis failed mit dem Grund) |
| `access.passkey.rename` | ein Passkey wird umbenannt (vorher: der alte Name) |
| `access.passkey.remove` | der Inhaber entfernt einen Passkey |
| `access.passkey.revoke` | ein Administrator widerruft einen Passkey eines anderen Kontos (nachher: by) |
| `access.passkey.use` | ein Passkey meldet an, beantwortet den zweiten Schritt oder „Identität bestätigen“ (nachher: Zweck login oder reauth) |
| `access.passkey.use.failed` | ein Passkey wird abgelehnt (Grund: challenge, origin, signature, counter, unknown, disabled, locked ...) |
| `access.passkey.password_refused` | das Passwort eines Kontos, das sich nur mit Passkeys anmeldet, wurde abgelehnt |
| `access.passkey.required`, `access.passkey.suggested` | die Richtlinie schickte einen Benutzer zum Hinzufügen eines Passkeys |

Die Einträge nennen den Passkey mit ID, Name, Verfahren und Modell, nie mit Schlüssel oder Credential-ID. Hinzufügen,
Entfernen und Widerrufen eines Passkeys schicken dem Inhaber eine Sicherheitsmeldung per E-Mail („Ihrem Konto wurde
ein Passkey hinzugefügt“); die Site schaltet die Meldungen mit `[UserSessionSettings] SecurityAlertMail=disabled` ab.

## 12. Für Erweiterungen

- `expPasskeyPolicyHandler` (`[PasskeySettings] PolicyHandlers[]`): `passkeyPolicy( eZUser $user, array $policy )`
  bekommt die Entscheidung der Einstellungen (`policy`, `password_sign_in`) und gibt sie geändert zurück oder null.
- `[PasskeySettings] ConsoleSlots[]=design:myext/passkeys_slot.tpl`: ein Template unter der Liste auf
  `user/passkeys`, mit `$account` und `$own`. Ein Abschnitt für Sitzungen oder Wiederherstellung hängt sich hier ein.
- `expPasskeyService` (`verifyForUser()`, `reauthOptions()`) prüft einen Passkey eines angemeldeten oder wartenden
  Benutzers, wie es der zweite Schritt von `sevenx_authentication_2fa` tut.
- `design/standard/javascript/exp_passkey.js` erledigt die Browserseite für ein Formular mit
  `data-exp-passkey="register"`, `"login"` oder `"reauth"` und seinen Optionen in `data-exp-passkey-options`.
