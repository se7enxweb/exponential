# Ein Benutzerpasswort auf der Kommandozeile zurücksetzen

Diese Seite ist für Administratoren, die das Administratorpasswort verloren, eine Installation übernommen oder Benutzer
importiert haben. `bin/php/resetuserpassword.php` setzt für jeden Benutzer ein neues Passwort. Es wurde am 11. Juli 2026
hinzugefügt (`ea77927c23`).

Das neue Passwort wird mit demselben Code gehasht wie im Formular zum Bearbeiten von Benutzern
(`eZUser::setInformation`), und es muss `site.ini [UserSettings] MinPasswordLength` erfüllen.

## Das Admin-Passwort jetzt zurücksetzen

Erzeugen Sie als root-Benutzer des Betriebssystems ein neues Passwort mit 20 Zeichen für `admin`:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g -l 20
```

Das erzeugte Passwort wird einmal ausgegeben. Notieren Sie es, melden Sie sich an und ändern Sie es auf Wunsch in der
Administrationsoberfläche.

## Zwei Arten der Autorisierung

**Als root-Benutzer des Betriebssystems** (keine Anmeldung bei Exponential nötig):

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -p 'NEW_PASSWORD'
```

`--allow-root-user` (kurz `-r`) verlangt, dass Sie wirklich der Betriebssystembenutzer `root` sind; andernfalls bricht
das Skript mit einem Fehler ab.

**Als Administrator der Website**:

```bash
php bin/php/resetuserpassword.php -a admin -ap 'YOUR_ADMIN_PASSWORD' -u editor1 -p 'NEW_PASSWORD'
```

Die mit `-a` und `-ap` angegebene Anmeldung muss sich authentifizieren und die Rolle `Administrator` haben.

## Optionen

| Option | Bedeutung | Standard |
|---|---|---|
| `-a <login>` | Administrator-Anmeldung, die das Zurücksetzen autorisiert | keine |
| `-ap <password>` | Passwort dieses Administrators | keine |
| `-r`, `--allow-root-user` | Die Authentifizierung als Administrator überspringen; nur root des Betriebssystems | aus |
| `-u <login>` | Anmeldename des zu ändernden Benutzers | `admin` |
| `-p <password>` | Das neue Passwort | keine |
| `-g`, `--generate` | Ein zufälliges Passwort statt `-p` erzeugen | aus |
| `-l <length>` | Länge eines erzeugten Passworts | `16` |
| `-s`, `--siteaccess` | Zu verwendender Siteaccess (und damit die Datenbank) | Standardzugang |
| `-q`, `--quiet` | Nur Fehler werden ausgegeben | aus |
| `-h`, `--help` | Hilfe | |

## Fehler, die Ihnen begegnen können

| Meldung | Ursache |
|---|---|
| `Admin authentication required` | Weder `-a`/`-ap` noch `--allow-root-user` wurde angegeben. |
| `Admin authentication failed.` | Falscher Anmeldename oder falsches Passwort nach `-a`. |
| `... does not have the Administrator role.` | Der autorisierende Benutzer ist kein Administrator. |
| `Target user not found: <login>` | `-u` nennt einen Anmeldenamen, den es nicht gibt. |
| `Target password does not validate. It must be at least N characters long.` | Kürzer als `MinPasswordLength`. |

## Gute Praxis

Ein auf der Kommandozeile angegebenes Passwort ist in der Prozessliste und in Ihrer Shell-Historie sichtbar. Verwenden
Sie lieber `-g` und ändern Sie das Passwort nach der ersten Anmeldung in der Administrationsoberfläche, oder leeren Sie
danach die Historie.

**Demo-Modus.** Mit der Umgebungsvariablen `EXP_DEMO_MODE=1` (eine öffentliche Demo) verweigert der Befehl das Zurücksetzen des Passworts eines Administrators, auch mit `--allow-root-user`: Er gibt „This installation is a public demo: the passwords of administrators cannot be changed or reset.“ aus und endet mit Status 1. Andere Konten werden wie gewohnt zurückgesetzt. Eine Demo braucht ihn nach der Installation nicht: `exp:install` installiert im Demo-Modus das veröffentlichte Passwort wie angegeben. [Demo-Modus](../../bc/6.0/demomodus.md).

## Verwandte Seiten

- [Die Sicherheitspatches vom August 2026 (Prüfungen bei der Anmeldung)](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md)
- [Sicherheitsstandards vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Exponential-Konsole](../../bc/6.0/konsole.md)
- [Prüfprotokoll](pruefprotokoll.md)
- [Chronik: Juli 2026](../../geschichte/2026/2026-07.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
