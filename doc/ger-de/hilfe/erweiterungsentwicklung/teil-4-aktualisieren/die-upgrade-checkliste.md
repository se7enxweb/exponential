---
title: Die Upgrade-Checkliste
audience: developer, app-creator
category: extension-development
order: 20
summary: Die ganze Arbeit, eine alte Erweiterung auf Exponential 6 zu bringen, auf einer Seite - Vorbereitung, PHP, Metadaten, Browser, Administration, Tests und Release, jeder Schritt mit dem Kapitel, das ihn erklärt.
keywords: Upgrade-Checkliste, alte Erweiterung, Metadaten, Tests, Release, upgrade checklist, old extension, PHP 8, CSP, jQuery 4, ezinfo, extension.xml, composer.json, admin4, tests, release
type: help
---
# Die Upgrade-Checkliste

Teil 4, Kapitel 2. Dies ist das ganze Upgrade auf einer Seite. Drucken Sie es aus, kopieren Sie es in ein Issue Ihres
Repositorys oder haken Sie es in Ihren Notizen ab. Jede Zeile verlinkt das Kapitel, das sie erklärt. Die Reihenfolge
ist die, die am wenigsten Zeit verschwendet: zuerst das, was das Laden der Erweiterung verhindert, zuletzt das, was
nur falsch aussieht.

## 0. Bevor Sie etwas ändern

- [ ] Lesen Sie die Lizenz. Sie dürfen die Erweiterung nur so aktualisieren und weitergeben, wie sie es erlaubt
      ([Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)).
- [ ] Stellen Sie die Erweiterung unter Git, falls sie es noch nicht ist, und committen Sie sie unverändert. Dieser
      erste Commit ist Ihr „Vorher“ ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
- [ ] Wenn sie aus dem Repository eines anderen stammt, forken Sie sie auf GitHub, statt sie zu kopieren, damit die
      Historie und die Nennung der Urheber erhalten bleiben ([Forken auf GitHub, Schritt für Schritt](../teil-5-erweitern-forken-zurueckgeben/auf-github-forken.md)).
- [ ] Arbeiten Sie auf einer Entwicklungsinstallation, nie auf einer Live-Website.
- [ ] Machen Sie die Bestandsaufnahme ([Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md)).

## 1. Sorgen Sie dafür, dass sie lädt

- [ ] `php -l` für jede PHP-Datei, mit PHP 8.0, wenn Sie das älteste PHP unterstützen wollen, auf dem Exponential 6
      läuft, und mit dem neuesten PHP, das Sie verwenden ([PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md)).
- [ ] Korrigieren Sie `ezinfo.php`, wenn seine Schlüssel nackte Wörter sind ([ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md)).
- [ ] Aktivieren Sie die Erweiterung und erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`).
- [ ] Leeren Sie die Caches (`php bin/php/ezcache.php --clear-all`).
- [ ] Öffnen Sie die Startseite und die Administration. Beide müssen weiterhin laden.

## 2. Sorgen Sie dafür, dass sie ohne Fehler läuft

- [ ] Schalten Sie auf Ihrer Entwicklungskopie die Debug-Ausgabe ein
      ([Debug-Ausgabe und Logs für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)).
- [ ] Öffnen Sie jede Seite, rufen Sie jede View, jeden Cronjob-Teil und jedes CLI-Skript der Erweiterung auf.
- [ ] Beheben Sie jeden fatalen Fehler und jede Warnung, die eine Datei der Erweiterung nennt:
  - [ ] Konstruktoren mit dem Namen der Klasse ([Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md))
  - [ ] statisch aufgerufene Instanzmethoden (dasselbe Kapitel)
  - [ ] entfernte Funktionen ([Entfernte PHP-Funktionen und ihr Ersatz](entfernte-php-funktionen.md))
  - [ ] `count()` auf Dingen, die nicht zählbar sind, String-Funktionen, die `null` erhalten
        ([PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md))
  - [ ] Datenbankcode ([Datenbankcode für PHP 8 und jede Engine](datenbankcode-fuer-php-8.md))

## 3. Sorgen Sie dafür, dass sie ruhig ist

- [ ] Beheben Sie jede „Deprecated“-Zeile aus den Dateien der Erweiterung
      ([Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)):
  - [ ] implizit nullable Parameter ([Nullable-Parameter und Typdeklarationen](nullable-parameter-und-typen.md))
  - [ ] dynamische Eigenschaften ([Dynamische Eigenschaften](dynamische-eigenschaften.md))
  - [ ] SPL-Methoden ohne Rückgabetypen
  - [ ] in 8.2 bis 8.5 als veraltet markierte Funktionen (`utf8_decode()`, `curl_close()`, `imagedestroy()` und andere)

## 4. Sorgen Sie dafür, dass sie sich selbst beschreibt

- [ ] `ezinfo.php`: Schlüssel in Anführungszeichen, `Name`, `Version`, `Copyright`, `License`, `Info_url`
      ([ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md)).
- [ ] `extension.xml`: dieselbe Version, Lizenz und Website.
- [ ] `composer.json`: Typ, PHP-Bedingung, Lizenz, der Installer-Name ([composer.json modernisieren](composer-json-modernisieren.md)).

## 5. Sorgen Sie dafür, dass sie im Browser funktioniert

- [ ] Öffnen Sie jede Seite mit den Entwicklerwerkzeugen des Browsers; lesen Sie die Konsole und den Netzwerk-Tab.
- [ ] Verschieben Sie Inline-`<script>`-Blöcke in Dateien ([CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md)).
- [ ] Ersetzen Sie `onclick=` und die anderen Event-Attribute ([CSP: von Event-Handler-Attributen zu data-Attributen](csp-event-handler-zu-data-attributen.md)).
- [ ] Ersetzen Sie `href="javascript:..."`, `eval()` und String-Timer ([CSP: javascript:-Links, eval und String-Timer](csp-javascript-links-und-eval.md)).
- [ ] Verschieben Sie `<style>`-Blöcke und `style=""`-Attribute in Stylesheets ([Von Inline-CSS zu Stylesheets](inline-css-in-stylesheets.md)).
- [ ] Ersetzen Sie YUI ([Von YUI zu expui](yui-zu-expui.md)).
- [ ] Verwenden Sie das jQuery, das ezjscore lädt, und ersetzen Sie Aufrufe, die jQuery 4 entfernt hat ([Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md)).

## 6. Sorgen Sie dafür, dass sie in die Administration passt

- [ ] Gestalten Sie Administrationsseiten mit den gemeinsamen Komponenten und Tokens, im hellen und im dunklen Modus
      ([admin4-Styling für Erweiterungsseiten](admin4-styling-fuer-erweiterungsseiten.md)).
- [ ] Einträge im Kontextmenü, Einträge im linken Menü und Tabs ohne Inline-Code
      ([Kontextmenüs und Integration in die Administration](kontextmenues-und-admin-integration.md)).
- [ ] Übersetzungen für jeden Text der Oberfläche (`i18n` mit Ihrem eigenen Kontext).

## 7. Namen und Nennung der Urheber

- [ ] Produktnamen in Texten und Dokumentation („Exponential“), niemals Kernel-Bezeichner umbenennen
      ([Namen und Marken bei einem Upgrade](namen-und-marken-bei-einem-upgrade.md)).
- [ ] Behalten Sie die Copyright-Hinweise der ursprünglichen Autoren bei; fügen Sie Ihre eigenen daneben hinzu
      ([Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)).

## 8. Sorgen Sie dafür, dass sie funktionsfähig bleibt

- [ ] Ein Test, der ohne Datenbank läuft: Jede PHP-Datei besteht den Lint, keine Inline-Skripte oder -Styles in
      Templates ([Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)).
- [ ] Ein GitHub-Actions-Workflow, der ihn bei jedem Push ausführt.

## 9. Veröffentlichen Sie sie

- [ ] Wählen Sie die nächste Version anhand der vorhandenen Tags ([Die nächste Versionsnummer wählen](../teil-7-veroeffentlichen/die-naechste-version-waehlen.md)).
- [ ] Erhöhen Sie die Version in `ezinfo.php`, `extension.xml` (und `composer.json`, falls vorhanden) in einem Commit
      vor dem Tag.
- [ ] Schreiben Sie das Changelog: was sich geändert hat und was an der vorherigen Version falsch war, falls etwas.
- [ ] Taggen, pushen, das GitHub-Release erstellen; prüfen Sie, ob Packagist es übernommen hat.
- [ ] Informieren Sie den ursprünglichen Autor: Öffnen Sie einen Pull Request oder ein Issue mit Ihren Korrekturen
      ([Mit Pull Requests zurückgeben](../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)).

Das ganze Release-Verfahren steht in [Teil 7: Ihre Erweiterung veröffentlichen](../teil-7-veroeffentlichen/README.md).

## Wie lange dauert es?

Die Quellen nennen keine Durchschnittswerte, und jede Erweiterung ist anders. Aus den Upgrades, die in den
Fallstudien dieses Teils erzählt werden:

| Art der Erweiterung | Was die Fallstudien zeigen |
|---|---|
| Nur Einstellungen und Templates | oft nichts über Schritt 4 hinaus |
| Einige PHP-Klassen | ein Nachmittag für die Schritte 1 bis 4 |
| Administrationsseiten mit JavaScript | die Schritte 5 und 6 sind der größte Teil der Arbeit |
| Eine eigene Kopie einer JavaScript-Anwendung | ein eigenes Projekt ([Fallstudie: der Layout-Editor auf jQuery 4](fallstudie-der-layout-editor-auf-jquery-4.md)) |

## Verwandte Seiten

- Zurück: [Warum die meisten Änderungen klein sind](warum-die-meisten-aenderungen-klein-sind.md)
- Weiter: [Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md)
- [Verhaltensänderungen für Erweiterungen](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
