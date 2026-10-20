---
title: "Teil 8: Referenz der eingebauten Funktionen für Erweiterungsautoren"
audience: developer
category: extension-development
order: 80
summary: Teil 8 des Buchs - eine Referenz der Kernel-Klassen, Template-Funktionen, Einstellungen, Befehle und Verzeichnisse, die Erweiterungsautoren am häufigsten verwenden, mit Signaturen aus dem Quelltext und Links zur vollständigen Dokumentation.
keywords: Buch, Teil 8, Referenz, Kernel-Klassen, Operatoren, Fetch-Funktionen, INI-Dateien, Konsole, var-Verzeichnisse, book, part 8, reference, eZINI, eZDB, eZPersistentObject, eZContentObject, eZTemplate, eZModule, eZScript, eZDataType, operators, fetch functions, ini files, console, var directories
type: help
---
# Teil 8: Referenz der eingebauten Funktionen für Erweiterungsautoren

Dies ist der achte und letzte Teil von [Erweiterungsentwicklung: das Buch](../README.md). Die früheren Teile lehren;
dieser dient zum Nachschlagen. Jede Seite behandelt eine Kernel-Klasse oder einen Bereich, führt auf, was ein
Erweiterungsautor am häufigsten verwendet (mit Methodensignaturen, die aus dem Quelltext mit Stand Oktober 2026
übernommen sind), zeigt ein kurzes Beispiel und verlinkt die vollständige Dokumentation. Sie ist keine vollständige
API-Referenz: Das sind der Quelltext des Kernels und die Seiten, auf die jedes Kapitel verweist.

## Die Seiten

### Klassen des Kernels

| Seite | Wofür |
|---|---|
| [Erweiterungspunkte auf einen Blick](erweiterungspunkte-auf-einen-blick.md) | jede Möglichkeit, sich einzuklinken, auf einer Seite |
| [eZINI](ezini.md) | Einstellungen lesen |
| [eZDebug und Protokollierung](ezdebug-und-protokollierung.md) | Debug-Ausgabe und Logdateien |
| [eZDB](ezdb.md) | die Datenbank |
| [eZPersistentObject](ezpersistentobject.md) | Ihre eigenen Tabellen |
| [eZContentObject](ezcontentobject.md) | Inhaltsobjekte |
| [eZContentObjectTreeNode](ezcontentobjecttreenode.md) | Knoten, der Inhaltsbaum |
| [eZContentClass](ezcontentclass.md) | Inhaltsklassen und ihre Attribute |
| [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md) | der aktuelle Benutzer und Richtlinien |
| [Demomodus für Entwickler](demomodus-fuer-entwickler.md) | `EXP_DEMO_MODE`, Passwortänderungen auf einer öffentlichen Demo |
| [eZHTTPTool](ezhttptool.md) | Request-Variablen, Sessions, Weiterleitungen |
| [eZSys und eZExtension](ezsys-und-ezextension.md) | Pfade, die Umgebung, Erweiterungen |
| [eZTemplate](eztemplate.md) | Templates aus PHP |
| [eZModule](ezmodule.md) | Module und View-Ergebnisse |
| [eZScript und eZCLI](ezscript-und-ezcli.md) | Kommandozeilenskripte |
| [eZFile, eZDir und Cluster-Dateien](ezfile-ezdir-und-cluster-dateien.md) | Dateien |

### APIs für Erweiterungstypen

| Seite | Wofür |
|---|---|
| [Die eZDataType-API](ezdatatype-api.md) | Datentypen |
| [Die eZWorkflowType-API](ezworkflowtype-api.md) | Workflow-Ereignistypen |
| [Inhaltsfunktionen aus PHP](inhaltsfunktionen-aus-php.md) | Inhalte anlegen und veröffentlichen |
| [ezjscore-Serverfunktionen](ezjscore-serverfunktionen-referenz.md) | Ajax-Aufrufe |
| [ezformtoken und CSRF](ezformtoken-und-csrf.md) | Formularschutz |
| [eZMail](ezmail.md) | E-Mails versenden |
| [Cache-Tags und Leeren](cache-tags-und-leeren.md) | Caches |

### Templates, Einstellungen, Befehle

| Seite | Wofür |
|---|---|
| [Eingebaute Template-Operatoren](referenz-der-template-operatoren.md) | Operatoren nach Gruppen |
| [Eingebaute Template-Funktionen](referenz-der-template-funktionen.md) | `{def}`, `{foreach}`, `{include}` und die übrigen |
| [Eingebaute Fetch-Funktionen](referenz-der-fetch-funktionen.md) | `fetch( module, function, ... )` |
| [INI-Dateien, denen ein Erweiterungsautor begegnet](ini-dateien-fuer-erweiterungsautoren.md) | welche Datei was enthält |
| [Konsolenbefehle für Entwickler](konsolenbefehle-fuer-entwickler.md) | `./console` und `bin/php` |
| [Verzeichnisse unter var/](verzeichnisse-unter-var.md) | Caches, Logs, Speicher |
| [Fehlercodes und Modulergebnisse](fehlercodes-und-modulergebnisse.md) | Fehler aus Views |

### Neuerungen von Exponential 6

| Seite | Wofür |
|---|---|
| [Die JavaScript-API von expui](expui-javascript-api.md) | `Exp.*` im Browser |
| [admin4-Komponenten und -Tokens](admin4-komponenten-und-tokens.md) | Administrationsseiten |
| [Das Hilfesystem für Erweiterungen](hilfesystem-fuer-erweiterungen.md) | Hilfeseiten in Ihrer Erweiterung |
| [expservices](expservices.md) | entfernte Dienste |
| [Glossar](glossar-fuer-erweiterungsentwickler.md) | die Begriffe dieses Buchs |

## Verwandte Seiten

- Zurück: [Die Release-Checkliste](../teil-7-veroeffentlichen/die-release-checkliste.md)
- Weiter: [Erweiterungspunkte auf einen Blick](erweiterungspunkte-auf-einen-blick.md)
- [Erweiterungspunkte](../../../../eng-us/bc/6.0/rad-extension-points.md)
- [Die Erweiterungsoberfläche](../../../../eng-us/bc/6.0/rad-extension-surface.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
