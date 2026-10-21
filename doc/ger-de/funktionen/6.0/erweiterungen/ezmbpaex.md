# ezmbpaex: Ablauf und Prüfung von Passwörtern

Diese Seite ist für Administratoren, die Passwörter ablaufen lassen oder an eine Regel binden müssen. `ezmbpaex` („eZ MB
Password Expiry“) tut drei Dinge:

- Es legt fest, wie lange ein Passwort gilt. Ist es abgelaufen, muss der Benutzer bei der nächsten Anmeldung ein neues
  anlegen.
- Es kann den Benutzer vor dem Ablauf des Passworts per E-Mail warnen.
- Es kann neue Passwörter gegen einen regulären Ausdruck prüfen.

Sein Modul ist `userpaex` (Ansichten `password`, `forgotpassword` und `editpaex`), und es hat zwei Cronjob-Teile.

## Einrichten

1. Aktivieren Sie die Extension.
2. Legen Sie die Regeln in `settings/override/mbpaex.ini.append.php` fest (siehe Tabelle).
3. Tragen Sie die beiden Cronjob-Teile in cron ein:

```bash
php runcronjobs.php ezmbpaex_send_expiry_notifications
php runcronjobs.php ezmbpaex_updatechildren
```

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `mbpaex.ini` | `mbpaexSettings` | `PasswordValidationRegexp` | leer | Regulärer Ausdruck, dem ein neues Passwort entsprechen muss; leer nimmt jedes an |
| `mbpaex.ini` | `mbpaexSettings` | `DefaultPasswordLifeTime` | `3` | Tage, die ein Passwort gilt, sofern Benutzergruppe oder Benutzer nichts anderes sagen; `0` bedeutet, dass Passwörter nie ablaufen |
| `mbpaex.ini` | `mbpaexSettings` | `ExpirationNotification` | `172800` | Wie lange vor dem Ablauf die Benachrichtigung gesendet wird; der mitgelieferte Wert sind zwei Tage in Sekunden (prüfen Sie `eZPaEx::fetchExpiryNotificationPendingList()`, bevor Sie ihn ändern) |
| `mbpaex.ini` | `mbpaexSettings` | `ForgotPasswordHashLifeTime` | `86400` | Sekunden, die ein Link „Passwort vergessen“ gilt (ein Tag) |
| `mbpaex.ini` | `mbpaexSettings` | `UpdateChildrenUser` | `admin` | Anmeldename, unter dem der Cronjob zur Aktualisierung der Kinder läuft |

Die Extension schreibt außerdem in das Audit-Protokoll (`audit.ini`); siehe [Prüfprotokoll](../../../bc/6.0/audit.md).

## Sicherheitskorrekturen in den Releases von Exponential 6

**6.0.2 (27. September 2026): Die Seite „Passwort vergessen“ verrät nicht mehr, ob zu einer Adresse ein Konto gehört.**
Die Ansicht `userpaex/forgotpassword` antwortete auf eine unbekannte Adresse mit „There is no registered user with that
email address“, sodass jeder Adressen einzeln testen konnte. Jetzt arbeitet sie wie `user/forgotpassword` des Kernels:

- eine unbekannte, aber gültige Adresse erhält dieselbe Seite wie eine bekannte („wenn zu der Adresse ein Konto
  registriert ist, wurde eine E-Mail an sie gesendet“);
- eine Eingabe, die keine E-Mail-Adresse ist, erhält „Please enter a valid email address.“;
- eine als Array gesendete Adresse wird als nicht angegeben behandelt, statt einen Fehler auszulösen;
- der fehlgeschlagene Versuch wird weiterhin ins Audit-Protokoll geschrieben;
- das Template verwendet die Texte und den Kontext `design/standard/user/forgotpassword` des Kernels, sodass vorhandene
  Übersetzungen greifen, und es maskiert jede Adresse, die es ausgibt, und den Hash-Schlüssel, den es mitführt.

**6.0.3 (29. September 2026): Der Schlüssel in einem Link „Passwort vergessen“ trägt 128 Zufallsbits** aus der sicheren
Quelle des Systems (32 Hexadezimalzeichen, sodass gespeicherte Schlüssel und Links ihre Form behalten). Vorher war er ein
`md5` der aktuellen Zeit und von `mt_rand()`; da die Zeit jedem bekannt ist, der den Link angefordert hat, ließ sich der
Schlüssel durch Ausprobieren finden.

**6.0.4:** Befehle und Cronjob-Teile führen eine Beschreibung; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x &
Exponential Foundation.

Vor der Aktualisierung ausgestellte Links funktionieren weiter, bis sie ablaufen.

## Verwandte Seiten

- [ezwebin und ezdemo](ezwebin.md): Die Designs erhielten dieselbe Korrektur für „Passwort vergessen“
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#2-seiten-für-vergessene-passwörter-verraten-nicht-mehr-ob-eine-adresse-ein-konto-hat)
- [Das Passwort eines Benutzers zurücksetzen](../benutzerpasswort-zuruecksetzen.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Chronik](../../../geschichte/erweiterungen/ezmbpaex.md) und [Versionshinweise](../../../../changelogs/extensions/ezmbpaex.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezmbpaex.md)
- Monate: [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
