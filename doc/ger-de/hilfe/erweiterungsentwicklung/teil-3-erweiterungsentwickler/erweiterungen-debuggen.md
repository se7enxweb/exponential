---
title: Erweiterungen debuggen
audience: developer, app-creator
category: extension-development
order: 350
summary: Ein Vorgehen für eine Erweiterung, die nichts tut oder eine Seite kaputt macht - ist sie aktiv, werden ihre Einstellungen gelesen, laden ihre Klassen, sind Caches oder Worker veraltet, mit der RAD-Zustandsprüfung, checkclasses und den Protokollen.
keywords: Debuggen, Fehlersuche, Erweiterung funktioniert nicht, Klasse nicht gefunden, Modul nicht gefunden, Protokolle, debugging, troubleshooting, extension not working, class not found, module not found, template not found, RAD health check, checkclasses, exp:ini where, cache, Velocity restart, error.log, debug output
type: help
---
# Erweiterungen debuggen

Teil 3, Kapitel 35. Die meisten Probleme mit Erweiterungen liegen nicht in dem Code, den Sie geschrieben haben,
sondern darin, wie er eingebunden ist: eine Einstellung, die nicht gelesen wird, eine Klasse, die nicht in der
Autoload-Zuordnung steht, ein Cache, ein Worker, der noch den Code von gestern ausführt. Dieses Kapitel ist ein
Vorgehen, das solche Ursachen schnell findet, gefolgt von den Werkzeugen für echte Fehler.

## Das Vorgehen

| Schritt | Befehl | Wonach Sie suchen |
|---|---|---|
| 1. Ist sie aktiv? | `./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions` (ergänzen Sie `-s <siteaccess>`) | Ihre Erweiterung in der Liste |
| 2. Werden ihre Einstellungen gelesen? | `./console exp:ini where <file>/<Block>/<Variable>` | Ihre Datei in der Kette, und nichts Späteres, das sie überschreibt |
| 3. Laden ihre Klassen? | `php bin/php/ezpgenerateautoloads.php -e -v`, dann `php bin/php/checkclasses.php --quiet-ok` | Ihre Klassen gefunden; nichts, was PHP ablehnt |
| 4. Was sagt die RAD-Zustandsprüfung? | **Setup > RAD** (`/setup/rad`) | Einstellungen, die eine Klasse nennen, die sich nicht laden lässt, ein angebotener, aber nicht gefundener Datentyp, eine View ohne Skript, eine Design-Erweiterung ohne Design |
| 5. Caches? | `php bin/php/ezcache.php --clear-all` | verschwindet das Problem, war es ein Cache |
| 6. Worker? | `./console exp:velocity deploy` (oder PHP-FPM neu laden) | verschwindet das Problem, war alter Code im Speicher |
| 7. Protokolle | `tail -n 50 var/log/error.log` und die Debug-Ausgabe | der eigentliche Fehler |

Schritt 4 wird unterschätzt: `expRADHealth::findings()` (`kernel/setup/expradhealth.php`) prüft die gesamte
Installation auf Konfiguration, die nicht funktionieren kann, und nennt für jeden Befund die Lösung.

## Typische Symptome

| Symptom | Übliche Ursache |
|---|---|
| "The requested module does not exist" | `ExtensionRepositories[]` fehlt, oder Caches |
| Eine Seite mit Layout, aber leerem Inhaltsbereich | eine neue Template-Datei und ein alter Cache der Template-Overrides |
| `Class '...' not found` | Autoloads nicht neu erzeugt, oder Worker nicht neu gestartet |
| `Operator '...' is not registered` | `ExtensionAutoloadPath[]` fehlt oder die Erweiterung ist nicht aktiv |
| `Object problem with operator '...'` | die Operatorklasse hat einen Konstruktor im alten Stil oder lässt sich nicht erzeugen |
| Ein Datentyp fehlt im Klasseneditor | `AvailableDataTypes[]` oder `ExtensionDirectories[]` fehlt; der Dateipfad |
| Funktioniert mit `php` auf der Kommandozeile, nicht im Browser | ein anderes PHP, oder PHP-FPM / Velocity nicht neu geladen |
| Eine Schaltfläche tut in der Administration nichts | ein Verstoß gegen die Content Security Policy: Öffnen Sie die Konsole des Browsers |
| Zugriff auf Ihre Seite verweigert | die `'functions'` der View und die Rollen des Benutzers: `./console exp:accesscheck` |

## Echte Fehler

- **Debug-Ausgabe**: Fehler mit ihrer Template-Datei und Zeile, Zeitmessung, SQL
  ([Debug-Ausgabe und Protokolle für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)).
- **`eZDebug::writeDebug()`**-Zeilen rund um den verdächtigen Code; entfernen Sie sie danach wieder.
- **Ein Skript mit geladenem Kernel**, um Ihre Klasse direkt aufzurufen: `php bin/php/ezexec.php <script.php>`. Die
  meisten Beispiele in diesem Buch wurden so geprüft.
- **Ein Test**, der den Fehler reproduziert, bevor Sie ihn beheben
  ([Tests für Erweiterungen](tests-fuer-erweiterungen.md)).
- **`git bisect`** im Repository Ihrer Erweiterung findet den Commit, der sie kaputt gemacht hat.
- **Die Fehlerreferenz** auf einer Fehlerseite führt zu jeder Protokollzeile dieser Anfrage
  ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

["HOWTO Troubleshoot eZ Publish Extensions"](https://ezpedia.exponential.earth/en/learning/howto_troubleshoot_ez_publish_extensions)
in eZpedia behandelt dasselbe Thema aus den früheren Jahren.

## Verwandte Seiten

- Zurück: [Tests für Erweiterungen](tests-fuer-erweiterungen.md)
- Weiter: [Leistung in Erweiterungen](leistung-in-erweiterungen.md)
- [RAD-Werkzeuge](../../../funktionen/6.0/rad-werkzeuge.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
