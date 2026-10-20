---
title: Eine Erweiterung aktivieren
audience: developer, app-creator, admin
category: extension-development
order: 40
summary: Eine Erweiterung für die ganze Installation oder für einen Siteaccess einschalten, was dabei geschrieben wird, und die Schritte, mit denen die laufende Website sie sieht - Autoloads, Caches, PHP-FPM und Velocity.
keywords: aktivieren, deaktivieren, Erweiterung einschalten, Autoloads, Cache leeren, Bereitstellung, activate, ActiveExtensions, ActiveAccessExtensions, setup extensions, exp:ini add, ezpgenerateautoloads, clear cache, velocity deploy, deploy, deactivate
type: help
---
# Eine Erweiterung aktivieren

Teil 3, Kapitel 4. Eine Erweiterung auf der Festplatte bewirkt nichts, solange sie nicht **aktiv** ist. Dieses
Kapitel zeigt die zwei Wege, eine Erweiterung zu aktivieren, was jeder davon schreibt, und die Schritte nach der
Aktivierung, mit denen die laufende Website Ihren Code sieht. Benutzer der Administration finden dasselbe unter
[Die Seite Extensions](../../../anleitungen/extensions-seite.md).

## Für die ganze Installation oder für einen Siteaccess

| Einstellung (`site.ini [ExtensionSettings]`) | Wo | Geltungsbereich |
|---|---|---|
| `ActiveExtensions[]` | `settings/override/site.ini.append.php` | jeder Siteaccess |
| `ActiveAccessExtensions[]` | `settings/siteaccess/<sa>/site.ini.append.php` | nur dieser Siteaccess |

Verwenden Sie `ActiveAccessExtensions[]` für eine Erweiterung, die nur zu einem Gesicht der Website gehört: ein
Werkzeug der Administration, das Design einer einzelnen Website. Alles andere gilt für die ganze Installation.

## Im Browser

**Setup > Extensions**: Drücken Sie **Aktivieren** auf der Karte, dann **Änderungen prüfen** (die Seite zeigt die
Risiken und die entstehenden Zeilen) und **Änderungen anwenden**. Die Seite schreibt die Override-Datei als Text,
behält Kommentare bei, legt eine Sicherungskopie an und leert die Caches, die von der Liste abhängen.

## Auf der Kommandozeile

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global --dry-run
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
```

Für einen einzelnen Siteaccess:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveAccessExtensions[]' mysite_hello siteaccess:admin
```

`exp:ini add` hängt den Eintrag an das Ende der Liste an. Wo in der Liste eine Erweiterung steht, ist wichtig; siehe
[Ladereihenfolge der Erweiterungen](ladereihenfolge-der-erweiterungen.md).

## Nach dem Aktivieren: dafür sorgen, dass das System sie sieht

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

1. **Autoloads**: Ihre Klassen werden in `var/autoload/ezp_extension.php` aufgenommen. Erwartete Ausgabe:
   `Found <n> classes, added <n> of them to the autoload array.` (`-n` zeigt die Anzahl, ohne zu schreiben.)
2. **Caches**: der INI-Cache (Ihre Einstellungen), die Caches für Template-Overrides und Designs (Ihre Templates), der
   Inhalts-Cache (zuvor erzeugte Seiten).
3. **Die PHP-Prozesse**: PHP-FPM-Worker mit OPcache können alte Dateien noch einige Sekunden behalten;
   Velocity-Worker behalten die Klassen und einige Einstellungen, die sie beim Start geladen haben, und brauchen einen
   Neustart.

Auf einer Installation, die von Velocity ausgeliefert wird, erledigt ein einziger Befehl all das in der richtigen
Reihenfolge:

```bash
./console exp:velocity deploy
```

Er erzeugt die Autoloads der Erweiterungen neu, leert die Caches für INI, Templates, Overrides, Übersetzungen und
Designs, sorgt dafür, dass PHP-FPM den neuen Code sieht, startet Velocity neu, leert dann den Inhalts- und den
HTTP-Cache und gibt für jeden Schritt PASS oder FAIL aus. Siehe [Bereitstellung](../../../anleitungen/bereitstellung.md).

## Prüfen, ob sie aktiv ist

```bash
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
php bin/php/checkclasses.php --quiet-ok
```

`checkclasses.php` lädt jede Klasse der Installation und listet die auf, die PHP ablehnt; keine Ausgabe ist ein gutes
Zeichen. Die RAD-Zustandsprüfung unter `/setup/rad` listet Einstellungen Ihrer Erweiterung auf, die eine Klasse
nennen, die sich nicht laden lässt.

## Deaktivieren

Entfernen Sie die Zeile (`exp:ini rem 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global`) oder
drücken Sie **Deaktivieren**, erzeugen Sie dann die Autoloads neu und leeren Sie die Caches wie oben. Das Deaktivieren
entfernt weder die Datenbanktabellen noch die Inhaltsklassen oder Inhalte der Erweiterung: Eine Erweiterung, die sie
angelegt hat, sollte dokumentieren, wie man sie entfernt.

## Was schiefgehen kann

| Symptom | Ursache |
|---|---|
| Ein Modul der Erweiterung meldet "not found" | Caches nicht geleert, oder `ExtensionRepositories[]` fehlt in ihrer `module.ini.append.php` |
| `Class ... not found` | Autoloads nicht neu erzeugt, oder die PHP-Prozesse führen noch alten Code aus |
| Funktioniert auf der Kommandozeile, nicht im Browser | PHP-FPM oder Velocity nicht neu geladen |
| Die Website bricht direkt nach der Aktivierung zusammen | eine Klasse, die sich unter PHP 8 nicht laden lässt; führen Sie `checkclasses.php` aus, deaktivieren Sie die Erweiterung, beheben Sie den Fehler ([Teil 4](../teil-4-aktualisieren/README.md)) |

## Verwandte Seiten

- Zurück: [extension.xml: Metadaten und Abhängigkeiten](extension-xml.md)
- Weiter: [Ladereihenfolge der Erweiterungen](ladereihenfolge-der-erweiterungen.md)
- [Die Seite Extensions](../../../anleitungen/extensions-seite.md), [Bereitstellung](../../../anleitungen/bereitstellung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
