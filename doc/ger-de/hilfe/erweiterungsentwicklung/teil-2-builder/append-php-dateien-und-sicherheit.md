---
title: .ini.append.php-Dateien und sichere Einstellungen
audience: developer, designer, app-creator, admin
category: extension-development
order: 70
summary: Warum Override-Dateien PHP sind, welche Einstellungsdateien Geheimnisse enthalten, Dateirechte, was nie in die Versionskontrolle oder in eine Erweiterung gehört und wie Sie prüfen, dass Einstellungen nicht über das Web lesbar sind.
keywords: ini.append.php, Sicherheit, Geheimnisse, settings/override, Dateirechte, 0640, Versionskontrolle, gitignore, Audit-Schlüssel, Datenbankpasswort, Webzugriff, security, secrets, file permissions, version control, audit key, database password, web access
type: help
---
# .ini.append.php-Dateien und sichere Einstellungen

Teil 2, Kapitel 7. Einstellungsdateien sind Konfiguration, und manche von ihnen sind Geheimnisse. Dieses Kapitel
erklärt, wie das System sie schützt und was Sie tun müssen, damit Ihre Erweiterungen und Ihre Repositories sie nie
preisgeben. Die Fakten stammen aus [Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md) und
[INI-Dateien behalten ihre Berechtigungen](../../../bc/6.0/dateirechte-beim-speichern-von-ini-dateien.md).

## Warum der PHP-Rahmen

Eine Override-Datei heißt `<file>.ini.append.php` und schließt ihren INI-Text in einen PHP-Kommentar ein:

```php
<?php /* #?ini charset="utf-8"?

[DatabaseSettings]
Database=mysite

*/ ?>
```

Ist ein Webserver einmal falsch konfiguriert und liefert die Datei direkt aus, führt PHP sie aus, der Kommentar erzeugt
keine Ausgabe, und der Besucher sieht eine leere Seite statt Ihrer Einstellungen. Verwenden Sie diese Form immer für
Override-Dateien und lassen Sie das schließende `*/ ?>` in der letzten Zeile.

## Die Dateien mit Geheimnissen

| Datei | Geheimnis |
|---|---|
| `settings/override/site.ini.append.php` | Datenbankbenutzer und -passwort (`[DatabaseSettings]`) |
| `settings/override/audit.ini.append.php` | der Signaturschlüssel und der Pseudonymschlüssel des Audits |
| `settings/override/mailpreferences.ini.append.php` | das Website-Geheimnis `[SecretSettings] TokenSecret` |
| `settings/override/velocity.ini.append.php` | `[DashboardSettings] Token`, wenn gesetzt |
| `config.env.php` | Maschineneinstellungen |
| `var/log/initial-admin-password` | das erste Administratorpasswort einer Installation über die Konsole oder Kickstart |

Einige davon werden bei der ersten Verwendung erzeugt, sodass auch eine Entwicklungsinstallation sie bekommt. Genau
deshalb liegen sie alle unter `settings/override/` oder außerhalb von `settings/`.

## Regeln für Builder und Autoren von Erweiterungen

1. **Committen Sie `settings/override/` nie.** In einem Git-Klon des Kernels führt `git status` das Verzeichnis als
   nicht verfolgt auf; lassen Sie es so, tragen Sie es in Ihre eigene `.gitignore` oder in `.git/info/exclude` ein und
   stagen Sie Dateien namentlich statt mit `git add -A`.
2. **Legen Sie nie ein Geheimnis in eine Erweiterung.** Erweiterungen werden geteilt, gepackt und veröffentlicht. Eine
   Erweiterung, die einen Schlüssel braucht, sollte ihn aus einer Einstellung lesen, die die Installation in ihrem
   eigenen `settings/override/` ausfüllt, und die Einstellung leer mit einem erklärenden Kommentar ausliefern.
3. **Kopieren Sie Einstellungen nie pauschal zwischen Servern.** Kopieren Sie die Blöcke, die Sie brauchen; Datenbank-
   und Geheimnis-Blöcke bleiben Sache jedes einzelnen Servers.
4. **Maskieren Sie Geheimnisse in Ausgaben.** `exp:ini` und die Einstellungsseite maskieren Variablen, deren Name
   `Password`, `Secret`, `Token` oder `Salt` enthält oder auf `Key` endet. Benennen Sie Ihre eigenen geheimen
   Einstellungen genauso, dann werden auch sie maskiert.
5. **Protokollieren Sie keine Einstellungen.** Eine Debug-Zeile, die einen ganzen Block ausgibt, kann ein Passwort
   ausgeben.

Der Paket-Download unter **Setup > Extensions** weigert sich, eine Erweiterung zu packen, die einen geheimen Wert oder
Schlüsselmaterial enthält, und nennt die gefundenen Dateien und Einstellungen; siehe
[die Geheimnisprüfung](../../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md).

## Dateirechte

`eZINI::save()` gibt einer **neuen** Einstellungsdatei die Rechte `0640` sowie Eigentümer und Gruppe ihres
Verzeichnisses; eine vorhandene Datei behält ihre Rechte. Prüfen Sie, dass nichts für alle schreibbar ist:

```bash
find settings extension/*/settings -name '*.ini*' -perm -o+w
```

Der Befehl darf nichts ausgeben. Korrigieren Sie, was er auflistet, mit `chmod 0640` (oder `0644`, wo ein anderer
Benutzer die Datei lesen muss).

## Kann das Web Ihre Einstellungen lesen?

Das Document Root von Exponential ist das Wurzelverzeichnis der Installation, und die Rewrite-Regeln schicken Anfragen
an `settings/`, `var/` und die Quelltexte an `index.php`, das mit einer Seite oder einem 404 antwortet, nie mit der
Datei. Prüfen Sie Ihren Server:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/settings/site.ini
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/settings/override/site.ini.append.php
```

Keine der beiden Adressen darf den Inhalt der Datei liefern. Die vollständige Liste der zu prüfenden Adressen steht in
[Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md).

## Eine sichere Einstellungserweiterung, zusammengefasst

| In der Erweiterung (committet) | In `settings/override/` (nie committet) |
|---|---|
| Design, Sprachen, Bild-Aliase, Overrides, Regeln für den View-Cache | Datenbank, Zugangsdaten des Mailservers |
| die eigenen Optionen Ihrer Erweiterung, mit sicheren Standardwerten | API-Schlüssel, Tokens, Geheimnisse |
| Kommentare, die jede Einstellung erklären | Hostnamen, Pfade, Debug-Schalter je Server |

## Verwandte Seiten

- Zurück: [Einstellungen in der Administration](einstellungen-in-der-administration.md)
- Weiter: [Templates für Builder](templates-fuer-builder.md)
- [Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md)
- [Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
