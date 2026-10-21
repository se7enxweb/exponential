---
title: "Teil 3: Der Erweiterungsentwickler"
audience: developer, app-creator
category: extension-development
order: 30
summary: Teil 3 des Buchs - der Aufbau einer Erweiterung, ein Mechanismus pro Kapitel, von Metadaten und Einstellungen über Module, Operatoren, Datentypen, Ereignisse, Sicherheit und Tests, gefolgt von Schritt-für-Schritt-Tutorials.
keywords: Buch, Teil 3, Erweiterungsentwickler, Aufbau, Modul, View, Operator, Fetch-Funktion, Datentyp, Workflow-Ereignis, Cronjob, Console-Befehl, Ajax, persistentes Objekt, Sicherheit, Tests, book, part 3, extension developer, anatomy, ezinfo, extension.xml, module, view, operator, fetch function, datatype, workflow event, cronjob, console command, ajax, persistent object, security, CSP, tests
type: help
---
# Teil 3: Der Erweiterungsentwickler

Dies ist der dritte und umfangreichste Teil von
[Erweiterungsentwicklung: das Buch](../README.md). Er erklärt, ein
Mechanismus pro Kapitel, wie eine Erweiterung PHP-Code in den Kernel einbindet: wohin der Code gehört, welche
Einstellung ihn registriert, den Vertrag (Klasse und Methoden, aus dem Quellcode des Kernels übernommen), ein
minimales Beispiel, das mit `php -l` geprüft und, wo es gefahrlos möglich war, mit geladenem Kernel ausgeführt wurde;
wie Sie sehen, dass es funktioniert; die häufigen Fehler; und den RAD-Assistenten, der den Code für Sie schreibt. Die
[Tutorials](tutorials/README.md) bauen
anschließend Schritt für Schritt echte kleine Erweiterungen.

## Der Aufbau

### Die Hülle einer Erweiterung

| Kapitel | |
|---|---|
| [Der Verzeichnisaufbau einer Erweiterung](verzeichnisaufbau-einer-erweiterung.md) | jeder Ordnername und was der Kernel damit macht |
| [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) | wer sie gemacht hat, welche Version, welche Lizenz, Abhängigkeiten |
| [Eine Erweiterung aktivieren](eine-erweiterung-aktivieren.md), [Ladereihenfolge der Erweiterungen](ladereihenfolge-der-erweiterungen.md) | sie einschalten, und wer gewinnt |
| [composer.json für eine Erweiterung](composer-json-fuer-erweiterungen.md) | sie mit Composer installieren |
| [Einstellungen in einer Erweiterung](einstellungen-in-einer-erweiterung.md) | Ihre eigene INI-Datei und wie Sie sie lesen |
| [Autoloads und Klassennamen](autoloads-und-klassennamen.md) | wie Ihre Klassen gefunden werden |

### Seiten und Berechtigungen

| Kapitel | |
|---|---|
| [Module und Views](module-und-views.md), [View-Parameter](view-parameter.md) | Seiten mit eigener Adresse |
| [Richtlinienfunktionen und Zugriffsprüfungen](richtlinienfunktionen-und-zugriff.md) | wer sie öffnen darf |
| [View-Ergebnisse und das Pagelayout](view-ergebnisse-und-das-pagelayout.md) | was eine View zurückgibt |

### Templates

| Kapitel | |
|---|---|
| [Template-Operatoren](template-operatoren.md), [Fetch-Funktionen](fetch-funktionen.md), [Template-Funktionen](template-funktionen.md) | Templates neue Fähigkeiten geben |

### Inhalte und Abläufe

| Kapitel | |
|---|---|
| [Datentypen](datentypen.md), [Datentypen: Speicherung, Validierung und Templates](datentyp-speicherung-validierung-und-templates.md) | neue Arten von Feldern |
| [Workflow-Ereignistypen](workflow-ereignistypen.md), [Trigger und Workflows für Entwickler](trigger-und-workflows-fuer-entwickler.md) | Schritte rund um das Veröffentlichen |
| [Cronjob-Teile](cronjob-teile.md), [CLI-Skripte und Console-Befehle](cli-skripte-und-console-befehle.md) | Arbeit außerhalb einer Anfrage |
| [Fakten, Themes und Logos für die Systemkarte](fakten-themes-und-logos-fuer-die-systemkarte.md) | Ihre Erweiterung in `exp:expfetch`, `exp:info` und `exp:about` aufnehmen |

### Frontend

| Kapitel | |
|---|---|
| [Ein Design in einer Erweiterung](design-in-einer-erweiterung.md), [CSS und JavaScript in einer Erweiterung](css-und-javascript-in-einer-erweiterung.md) | Templates und Assets |
| [Ajax mit ezjscore-Serverfunktionen](ajax-mit-ezjscore-serverfunktionen.md) | dynamische Teile |
| [Übersetzungen in einer Erweiterung](uebersetzungen-in-einer-erweiterung.md) | jeder Text in jeder Sprache |

### Daten und das System

| Kapitel | |
|---|---|
| [Persistente Objekte und eigene Tabellen](persistente-objekte-und-tabellen.md), [Datenbankschema, .dba-Dateien und Upgrades](datenbankschema-und-dba-dateien.md) | Speicherung |
| [Ereignisse, Listener und Filter](ereignisse-und-filter.md), [Handler, die über Einstellungen benannt werden](handler-ueber-einstellungen-benennen.md), [Kernel-Klassen-Overrides: der letzte Ausweg](kernel-overrides-der-letzte-ausweg.md) | das Verhalten des Systems ändern |

### Qualität

| Kapitel | |
|---|---|
| [Sicherheit in Erweiterungen](sicherheit-in-erweiterungen.md), [Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md) | von Haus aus sicher |
| [Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md) | einheitliche Seiten in der Administration |
| [Tests für Erweiterungen](tests-fuer-erweiterungen.md), [Erweiterungen debuggen](erweiterungen-debuggen.md), [Leistung in Erweiterungen](leistung-in-erweiterungen.md) | funktionierend und schnell |
| [Ihre Erweiterung dokumentieren](ihre-erweiterung-dokumentieren.md) | einschließlich Hilfeseiten in diesem Hilfe-Center |

## Tutorials

[Tutorials: echte Erweiterungen Schritt für Schritt bauen](tutorials/README.md):
der Rundgang durch die RAD-Werkzeuge, dann jede Art von Erweiterung, einmal mit dem RAD-Assistenten und einmal von
Hand gebaut, zum Abschluss eine Abschlusserweiterung von der Idee bis zum Release.

## Bevor Sie beginnen

Sie sollten [Teil 1](../teil-1-vom-benutzer-zum-entwickler/README.md) gelesen
haben und die Werkzeuge des Builders aus [Teil 2](../teil-2-builder/README.md)
kennen. Sie brauchen eine Entwicklungsinstallation mit eingeschalteter Debug-Ausgabe und grundlegende PHP-Kenntnisse.

## Verwandte Seiten

- Zurück: [Wenn Einstellungen nicht ausreichen](../teil-2-builder/wenn-einstellungen-nicht-reichen.md)
- Weiter: [Der Verzeichnisaufbau einer Erweiterung](verzeichnisaufbau-einer-erweiterung.md)
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../anleitungen/extensions.md)
- [Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
