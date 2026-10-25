# Passkeys: Anmelden mit Fingerabdruck, Gesicht oder Sicherheitsschlüssel

Diese Anleitung erklärt die Passkeys von Exponential 6.0.15. Ein Passkey ersetzt bei der Anmeldung das Passwort: Der
Browser oder das Gerät fragt nach Fingerabdruck, Gesicht, der PIN der Bildschirmsperre oder einer Berührung des
Sicherheitsschlüssels, und die Site prüft eine Signatur, die mit einem Schlüssel erstellt wurde, der das Gerät nie
verlässt. Die Site speichert nur die öffentliche Hälfte dieses Schlüssels; nichts auf dem Server taugt zum Anmelden.

Sie richtet sich an zwei Lesergruppen. **Benutzer** lesen die Abschnitte 1 bis 4. **Administratoren** lesen die
Abschnitte 5 bis 10.

[Anleitungen](README.md) · Siehe auch: [Identität bestätigen](identitaet-bestaetigen.md) ·
[Zweistufige Anmeldung](zwei-faktor-anmeldung.md) · [Sicherheit und Audit](sicherheit-und-audit.md) ·
Änderungshinweis: [Passkeys und „Identität bestätigen“](../bc/6.0/passkeys-und-identitaet-bestaetigen.md)

## Kurz gesagt

- **Mein Konto > Passkeys** (`/user/passkeys`): einen Passkey hinzufügen (der Passkey-Dialog des Browsers, danach ein
  selbst gewählter Name), sehen, wann jeder hinzugefügt und zuletzt benutzt wurde, Passkeys umbenennen und entfernen.
- Das Anmeldeformular hat neben dem Passwort **Mit einem Passkey anmelden**. Wo der Browser es anbietet, listet auch
  das Feld für den Benutzernamen die eigenen Passkeys (Passkey-Autofill); die Auswahl meldet an.
- Eine Anmeldung mit Passkey ist die ganze Anmeldung: Der zweite Schritt der zweistufigen Anmeldung wird nicht noch
  einmal gefragt, denn der Passkey prüft schon auf dem Gerät, wer Sie sind.
- Passkeys gelten nur auf den Webseiten. Die REST-Schnittstelle, SOAP, WebDAV und die `expservices`-Anmeldung melden
  an wie bisher.
- Neu in 6.0.15: die Tabelle `exppasskey`, die Ansicht `user/passkeys`, der Block `site.ini [PasskeySettings]`, fünf
  Audit-Ereignisse `access.passkey.*`.

## Inhalt

1. [Was ein Passkey ist](#1-was-ein-passkey-ist)
2. [Passkeys hinzufügen, benennen und entfernen](#2-passkeys-hinzufügen-benennen-und-entfernen)
3. [Mit einem Passkey anmelden](#3-mit-einem-passkey-anmelden)
4. [Ein verlorenes oder ersetztes Gerät](#4-ein-verlorenes-oder-ersetztes-gerät)
5. [Einstellungen](#5-einstellungen)
6. [Die Tabelle und das Upgrade](#6-die-tabelle-und-das-upgrade)
7. [Zweistufige Anmeldung und die anderen Anmeldewege](#7-zweistufige-anmeldung-und-die-anderen-anmeldewege)
8. [Wie ein Passkey geprüft wird](#8-wie-ein-passkey-geprüft-wird)
9. [Audit](#9-audit)
10. [Grenzen](#10-grenzen)

---

## 1. Was ein Passkey ist

Ein Passkey ist ein Schlüsselpaar, das Ihr Gerät (ein Telefon, ein Laptop, ein Passwortmanager oder ein
Hardware-Sicherheitsschlüssel) nur für diese Site erzeugt. Der private Schlüssel bleibt auf dem Gerät; viele
Passwortmanager synchronisieren ihn zwischen Ihren Geräten („Synchronisiert“ in der Liste). Die Site speichert den
öffentlichen Schlüssel und prüft jede Anmeldung damit. Ein Passkey lässt sich nicht abfischen: Der Browser benutzt ihn
nur auf der Site, für die er erstellt wurde.

## 2. Passkeys hinzufügen, benennen und entfernen

1. Öffnen Sie **Mein Konto** und die Karte **Passkeys**, oder gehen Sie zu `/user/passkeys`.
2. Die Seite fragt zuerst, ob Sie es sind (siehe [Identität bestätigen](identitaet-bestaetigen.md)): Geben Sie Ihr
   Passwort ein oder benutzen Sie einen Passkey, den Sie schon haben.
3. Geben Sie unter **Einen Passkey hinzufügen** einen Namen ein, den Sie später wiedererkennen (etwa „Arbeitslaptop“
   oder „Passwortmanager“); ohne Namen heißt der Passkey „Passkey vom <Datum>“.
4. Wählen Sie **Einen Passkey hinzufügen** und folgen Sie dem Browser: Wählen Sie, wo er gespeichert wird, und
   bestätigen Sie mit Fingerabdruck, Gesicht, PIN oder Schlüssel.

Die Liste zeigt für jeden Passkey den Namen, wann er hinzugefügt und zuletzt benutzt wurde, ob er synchronisiert oder
an ein Gerät oder einen Schlüssel gebunden ist, und seinen Algorithmus (ES256, RS256 ...). **Umbenennen** ändert den
Namen; **Entfernen** fragt noch einmal nach und löscht den Passkey dann hier. Entfernen Sie ihn auch auf dem Gerät
oder im Passwortmanager. Ein Konto hat höchstens 20 Passkeys (`MaxPerUser`).

Kann der Browser keine Passkeys erstellen, bleibt die Schaltfläche verborgen und die Seite sagt es; Ihr Passwort
funktioniert weiter.

## 3. Mit einem Passkey anmelden

Wählen Sie im Anmeldeformular **Mit einem Passkey anmelden** und bestätigen Sie auf dem Gerät. Wo der Browser
Passkey-Autofill kann, klicken Sie in das Feld für den Benutzernamen: Ihre Passkeys für die Site erscheinen dort, und
die Auswahl meldet an, ohne dass Sie etwas tippen. „Angemeldet bleiben“ gilt wie beim Passwort.

Ein Passkey wird abgewiesen, wenn das Konto deaktiviert oder nach Fehlversuchen gesperrt ist, wenn das Konto den
Siteaccess nicht benutzen darf oder wenn der Passkey entfernt wurde. Das Formular sagt dann, warum; das Passwort
funktioniert weiter.

## 4. Ein verlorenes oder ersetztes Gerät

Melden Sie sich mit dem Passwort (oder einem anderen Passkey) an, öffnen Sie **Mein Konto > Passkeys** und entfernen
Sie den Passkey des verlorenen Geräts. Ohne die Bildschirmsperre oder Biometrie des Geräts ist ein Passkey wertlos,
aber nur das Entfernen stellt sicher, dass er nie wieder anmeldet. Ein Administrator kann den Passkey eines anderen
Benutzers in der Administration noch nicht entfernen (siehe [Grenzen](#10-grenzen)); wird das Konto entfernt,
verschwinden seine Passkeys.

## 5. Einstellungen

`site.ini [PasskeySettings]`:

| Einstellung | Vorgabe | Bedeutung |
|---|---|---|
| `Passkeys` | `enabled` | `disabled`: Kein Passkey kann hinzugefügt oder benutzt werden; die gespeicherten bleiben |
| `SignInForm` | `enabled` | Schaltfläche und Autofill im Anmeldeformular anbieten |
| `RelyingPartyID` | leer | Der Hostname, an den Passkeys gebunden sind. Leer: der Host der Anfrage. Eine übergeordnete Domain (`example.com`) lässt die Passkeys von `www.example.com` auch auf `admin.example.com` gelten; sie muss der Host oder eine übergeordnete Domain davon sein, sonst gilt der Host |
| `RelyingPartyName` | leer | Der Name, den der Browser zeigt; leer: `[SiteSettings] SiteName` |
| `AllowedOrigins[]` | leer | Die Ursprünge (`scheme://host[:port]`), von denen Antworten kommen dürfen. Leer: der Ursprung der Anfrage. Ist `RelyingPartyID` eine übergeordnete Domain, jeden Host eintragen, etwa `https://www.example.com` und `https://admin.example.com` |
| `UserVerification` | `required` | `required`, `preferred` oder `discouraged` für „Identität bestätigen“. Die Anmeldung mit Passkey verlangt sie immer |
| `Algorithms[]` | `-7`, `-257`, `-8`, `-35`, `-36` | Angebotene COSE-Algorithmen, der bevorzugte zuerst: ES256, RS256, Ed25519 (braucht PHPs Erweiterung sodium), ES384, ES512 |
| `ChallengeSeconds` | `300` | Sekunden, in denen eine Challenge beantwortet werden darf (30 bis 3600) |
| `MaxPerUser` | `20` | Passkeys je Konto |

Passkeys brauchen eine sichere Seite: HTTPS, oder `http://localhost` bei der Entwicklung. Eine IP-Adresse ist keine
gültige Relying-Party-ID.

**Site-Designs mit eigenem Anmelde-Template.** Die Anmeldeformulare von `design/standard`, `design/admin` und admin4
haben die Passkey-Schaltfläche. Ein Design, das `user/login.tpl` überschreibt (etwa das Media-Theme), zeigt sie nicht
von selbst; es fügt nach seinem eigenen Anmeldeformular ein:

```
{include uri='design:user/parts/passkey_login.tpl'}
```

Dieser Teil (`design/standard/templates/user/parts/passkey_login.tpl`) ist ein eigenes Formular mit der Schaltfläche
und zeichnet nichts, solange keine Passkeys angeboten werden. Das Passkey-Autofill des Browsers braucht zusätzlich die
Attribute `data-exp-passkey` und `autocomplete="username webauthn"` am eigenen Formular des Designs, wie
`design/standard/templates/user/login.tpl` zeigt.

## 6. Die Tabelle und das Upgrade

`exppasskey` hat eine Zeile je Passkey: `id`, `user_id`, `name`, `credential_id` (base64url), `credential_hash`
(SHA-256, eindeutig, darüber sucht eine Anmeldung den Passkey), `public_key` (der COSE-Schlüssel, base64url),
`algorithm`, `sign_count`, `user_handle` (zufällig je Konto, nie die Benutzer-ID), `aaguid` (das Modell des
Authenticators), `transports`, `backup_eligible`, `backup_state`, `created`, `last_used`, `last_ip`. Nichts davon ist
geheim.

- Neue Installationen erhalten sie aus `share/db_schema.dba` (jede Datenbank) oder den Kernel-Schemas von MySQL,
  PostgreSQL und SQLite.
- Upgrades: Das Ende von `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` legt sie nur an, wenn sie fehlt,
  oder auf jeder Datenbank, Oracle und MongoDB eingeschlossen:

```bash
php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user   # sagt exists oder missing
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Solange die Tabelle fehlt, sagt die Passkey-Seite das, und das Anmeldeformular bietet keinen Passkey an. Nach dem
Upgrade die Kernel-Autoloads neu erzeugen, die Caches INI, template und template-override leeren, PHP-FPM neu laden und
Velocity neu starten. Wird ein Konto entfernt, werden seine Passkeys entfernt.

## 7. Zweistufige Anmeldung und die anderen Anmeldewege

Ein Passkey mit Benutzerprüfung belegt zwei Dinge auf einmal: etwas, das Sie haben (das Gerät), und etwas, das Sie
sind oder wissen (Biometrie oder PIN). Deshalb fragt eine Anmeldung mit Passkey den zweiten Schritt von
`sevenx_authentication_2fa` nicht; das Audit verzeichnet sie als `access.session.login` mit dem Handler
`expPasskeyLogin`.

Auf den anderen Wegen ändert sich nichts: REST, SOAP, WebDAV und die `expservices`-Anmeldung melden mit dem Passwort
(und dem zweiten Schritt, wo eine Installation sie schützt) an wie bisher.

## 8. Wie ein Passkey geprüft wird

Der Kernel prüft WebAuthn selbst (`kernel/classes/webauthn`), ohne Bibliothek, auf PHP 8.0 bis 8.5:

- Jede Anfrage nach Optionen erhält eine neue zufällige Challenge, in der Sitzung mit ihrem Zweck (register, login,
  reauth), dem Benutzer und einem Ablauf gespeichert; die erste Antwort, die sie nennt, verbraucht sie, so lässt sich
  keine Antwort wiederholen.
- `clientDataJSON`: der Typ (`webauthn.create` oder `webauthn.get`), die Challenge, ein erlaubter Ursprung, nicht
  ursprungsübergreifend.
- Authenticator-Daten: der SHA-256 der Relying-Party-ID, Benutzer anwesend, Benutzer geprüft, wo verlangt, ein
  Sicherungsstatus nur bei einem sicherbaren Credential.
- Registrierung: das bescheinigte Credential (eine ID von höchstens 1023 Bytes) und ein öffentlicher COSE-Schlüssel
  eines angebotenen Algorithmus.
- Anmeldung: die Signatur über die Authenticator-Daten und den Hash von `clientDataJSON` mit dem gespeicherten
  Schlüssel (ES256, ES384, ES512, RS256, RS384, RS512 mit OpenSSL, Ed25519 mit sodium) und der Signaturzähler: Ein
  Authenticator, der zählt, muss hochzählen, sonst könnte der Passkey kopiert sein und wird abgewiesen.
- Attestation wird als `none` angefragt: Die Site vertraut keinem bestimmten Authenticator-Modell und prüft daher keine
  Attestation-Zertifikate.

## 9. Audit

| Ereignis | Wann |
|---|---|
| `access.passkey.add` | ein Passkey hinzugefügt wird (oder abgewiesen: result failed mit dem Grund) |
| `access.passkey.rename` | ein Passkey umbenannt wird (before: der alte Name) |
| `access.passkey.remove` | ein Passkey entfernt wird |
| `access.passkey.use` | ein Passkey anmeldet oder „Identität bestätigen“ beantwortet (after: purpose login oder reauth) |
| `access.passkey.use.failed` | ein Passkey abgewiesen wird (Grund: challenge, origin, signature, counter, unknown, disabled, locked ...) |

Die Einträge nennen den Passkey über ID, Namen, Algorithmus und Modell, nie über seinen Schlüssel oder seine
Credential-ID.

## 10. Grenzen

- Administratoren können die Passkeys eines anderen Benutzers in der Administration noch nicht sehen oder entfernen.
- Attestation wird nicht geprüft (siehe oben).
- Passkeys gelten nur auf den Webseiten, nicht für REST, SOAP oder WebDAV.
- Designs mit eigenem `user/login.tpl` zeigen keine Passkey-Schaltfläche, bis sie
  `design:user/parts/passkey_login.tpl` einbinden (siehe [Einstellungen](#5-einstellungen)); die sevenx-Themes media
  und simple und andere Extension-Designs liegen in eigenen Repositories und brauchen diese Einbindung dort.
