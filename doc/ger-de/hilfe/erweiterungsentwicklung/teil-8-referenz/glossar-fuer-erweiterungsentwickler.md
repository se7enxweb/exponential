---
title: Glossar für Erweiterungsentwickler
audience: developer, designer, app-creator, user
category: extension-development
order: 380
summary: Die Begriffe dieses Buchs in einer alphabetischen Liste - vom Zugriffswort, der Append-Datei und dem Attribut bis zum Ansichtsmodus, dem View-Parameter und dem Workflow -, jeder mit einer kurzen Definition und dem Kapitel, das ihn erklärt.
keywords: Glossar, Begriffe, Definitionen, Erweiterung, Siteaccess, Design, Override, Datentyp, Knoten, Objekt, Klasse, Modul, View, Operator, Workflow, Trigger, glossary, terms, definitions, extension, siteaccess, design, override, datatype, node, object, class, module, view, operator, fetch, workflow, trigger, packer, CSP, token
type: help
---
# Glossar für Erweiterungsentwickler

Teil 8, Referenz 34, die letzte Seite des Buchs. Die Begriffe der Entwicklung mit Exponential, so wie dieses Buch sie
verwendet. Der englische Begriff steht in Klammern, weil Sie ihm in Code, Einstellungen und englischen Quellen
begegnen.

| Begriff | Bedeutung | Siehe |
|---|---|---|
| Zugriffswort (access word) | die Antwort von `hasAccessTo()`: `yes`, `no` oder `limited` | [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md) |
| admin4 | das aktuelle Design der Administration, mit gemeinsamen Komponenten und Design-Tokens | [admin4-Komponenten und -Tokens](admin4-komponenten-und-tokens.md) |
| Append-Datei (append file) | die `<name>.ini.append.php` einer Erweiterung oder eines Siteaccess, die eine INI-Datei des Kernels ergänzt | [INI-Dateien, denen ein Erweiterungsautor begegnet](ini-dateien-fuer-erweiterungsautoren.md) |
| Attribut (attribute) | ein Feld eines Inhaltsobjekts, mit einem Datentyp, den sein Klassenattribut festlegt | [eZContentObject](ezcontentobject.md) |
| Autoload-Array (autoload array) | die erzeugte Zuordnung von Klassennamen zu Dateien (`var/autoload/`, `autoload/`) | [Autoloads und Klassenbenennung](../teil-3-erweiterungsentwickler/autoloads-und-klassennamen.md) |
| Cache-Block (cache block) | ein Teil eines Templates, dessen Ausgabe gecacht wird (`cache-block`) | [Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md) |
| Klasse, Inhaltsklasse (content class) | der Typ eines Inhaltsobjekts: sein Bezeichner und seine Attribute | [eZContentClass und ihre Attribute](ezcontentclass.md) |
| Cluster-Datei-Handler (cluster file handler) | die Schicht, über die Dateien unter `var/` gelesen und geschrieben werden, damit mehrere Server sie gemeinsam nutzen | [eZFile, eZDir und Cluster-Datei-Handler](ezfile-ezdir-und-cluster-dateien.md) |
| Content Security Policy (CSP) | ein Header, der dem Browser sagt, welche Skripte und Styles er ausführen darf | [CSP: Inline-Skripte in Dateien verlagern](../teil-4-aktualisieren/csp-inline-skripte-auslagern.md) |
| Kontextmenü (context menu) | das Rechtsklickmenü des Inhaltsbaums, erweitert über `admininterface.ini` | [Kontextmenüs und Integration in die Administration](../teil-4-aktualisieren/kontextmenues-und-admin-integration.md) |
| Cronjob-Teil (cronjob part) | eine Gruppe von Cronjob-Skripten, die zusammen ausgeführt werden | [Cronjob-Teile](../teil-3-erweiterungsentwickler/cronjob-teile.md) |
| Datentyp (datatype) | der Code hinter einer Art von Attribut: Bearbeitung, Speicherung, Anzeige | [Die eZDataType-API](ezdatatype-api.md) |
| Design | ein benannter Satz von Templates und Dateien, der in der Design-Kette durchsucht wird | [Themes sind Erweiterungen](../teil-6-theme-entwickler/themes-sind-erweiterungen.md) |
| Design-Kette (design chain) | die geordnete Liste von Designs (und ihren Erweiterungsordnern), die ein Siteaccess durchsucht | [Die Design-Kette](../teil-2-builder/die-design-kette.md) |
| Design-Erweiterung (design extension) | eine Erweiterung, die Design-Ordner beisteuert (`DesignExtensions[]`) | [Der Aufbau einer Design-Erweiterung](../teil-6-theme-entwickler/aufbau-einer-design-erweiterung.md) |
| Design-Token (design token) | ein benannter Wert (`--a4-ink`), der statt einer festen Farbe oder Größe verwendet wird | [Dunkelmodus und Design-Tokens in Themes](../teil-6-theme-entwickler/dunkler-modus-und-tokens-in-themes.md) |
| expui (Exponential UI) | die JavaScript-Bibliothek der Administration, `Exp` | [Die JavaScript-API von expui](expui-javascript-api.md) |
| Erweiterung (extension) | ein Ordner unter `extension/`, der den Kernel ergänzt oder verändert, ohne ihn zu bearbeiten | [Was eine Erweiterung ist](../teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md) |
| Erweiterungspunkt (extension point) | ein registrierter Weg, über den sich eine Erweiterung einklinkt | [Erweiterungspunkte auf einen Blick](erweiterungspunkte-auf-einen-blick.md) |
| Fetch-Funktion (fetch function) | eine Leseoperation eines Moduls, aus Templates mit `fetch()` aufgerufen | [Eingebaute Fetch-Funktionen nach Modul](referenz-der-fetch-funktionen.md) |
| Fork | eine Kopie eines Repositorys, die Sie pflegen | [Wann forken und wann nicht](../teil-5-erweitern-forken-zurueckgeben/wann-forken.md) |
| Formular-Token (form token) | das Geheimnis je Session, das jeder POST eines angemeldeten Benutzers mitführen muss | [ezformtoken und CSRF-Schutz](ezformtoken-und-csrf.md) |
| Handler | eine durch eine Einstellung benannte Klasse, die einen Dienst bereitstellt (Mail-Transport, Bild-Handler) | [Durch Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Kernel | der Kern von Exponential: `kernel/`, `lib/`, `settings/`, die mitgelieferten Designs | |
| Kernel-Override (kernel override) | der Ersatz einer Kernel-Klasse durch eine Erweiterung: das letzte Mittel | [Kernel-Overrides: das letzte Mittel](../teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md) |
| Modul, View (module, view) | eine Gruppe von Seiten unter einem URL-Präfix und eine Seite daraus | [eZModule und Modulergebnisse](ezmodule.md) |
| Knoten (node) | ein Standort eines Inhaltsobjekts im Inhaltsbaum | [eZContentObjectTreeNode](ezcontentobjecttreenode.md) |
| Objekt, Inhaltsobjekt (content object) | ein einzelner Inhalt, mit Versionen und Übersetzungen | [eZContentObject](ezcontentobject.md) |
| Override, Template-Override (template override) | eine Regel, die für bestimmte Inhalte ein anderes Template wählt | [Template-Overrides](../teil-2-builder/grundlagen-der-template-overrides.md) |
| Packer | das Bündeln von Stylesheets und Skripten durch ezjscore | [Theme-Ressourcen, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md) |
| Paket (package, `.ezpkg`) | ein Archiv mit Klassen, Inhalten oder einer Erweiterung für das Paketsystem | [Pakete (.ezpkg) und der Paketersteller](../teil-7-veroeffentlichen/pakete-ezpkg.md) |
| Pagelayout | das Template, das jede Seite einrahmt | [View-Ergebnisse, der Pfad und das Pagelayout](../teil-3-erweiterungsentwickler/view-ergebnisse-und-das-pagelayout.md) |
| persistentes Objekt (persistent object) | eine Klasse, die auf eine Tabellenzeile abgebildet wird (`eZPersistentObject`) | [eZPersistentObject](ezpersistentobject.md) |
| Richtlinie, Rolle, Einschränkung (policy, role, limitation) | eine Berechtigung (Modul und Funktion), ein Satz davon, der Benutzern zugewiesen wird, und deren Beschränkung | [Richtlinienfunktionen und Zugriff](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md) |
| RAD-Werkzeuge (RAD tools) | die Assistenten unter **Setup > RAD**, die Erweiterungscode schreiben | [Rundgang durch die RAD-Werkzeuge](../teil-3-erweiterungsentwickler/tutorials/rundgang-durch-die-rad-werkzeuge.md) |
| Remote-ID (remote ID) | eine stabile, installationsunabhängige ID eines Objekts oder Knotens | [eZContentObject](ezcontentobject.md) |
| Serverfunktion (server function) | eine PHP-Methode, die über `/ezjscore/call/` aufrufbar ist | [Referenz der ezjscore-Serverfunktionen](ezjscore-serverfunktionen-referenz.md) |
| Siteaccess | ein Einstiegspunkt einer Installation, mit eigenen Einstellungen und Designs | [Siteaccess-Einstellungen](../teil-2-builder/siteaccess-einstellungen.md) |
| Tag (Git) | ein Name für einen Commit, als Release-Version verwendet; nach dem Pushen dauerhaft | [Tags und Releases sind dauerhaft](../teil-7-veroeffentlichen/tags-sind-dauerhaft.md) |
| Template-Operator, Template-Funktion (template operator, template function) | `{$x\|operator}` und `{function}...{/function}` | [Eingebaute Template-Operatoren](referenz-der-template-operatoren.md) |
| Trigger | die Verbindung zwischen einer Operation (Veröffentlichen) und einem Workflow | [Trigger und Workflows für Entwickler](../teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md) |
| Velocity | der optionale Anwendungsserver von Exponential | |
| Ansichtsmodus (view mode) | wie Inhalte angezeigt werden: full, line, embed und eigene | [Ansichtsmodi für Builder](../teil-2-builder/view-modi-fuer-builder.md) |
| View-Parameter (view parameter) | ein Paar `(name)/value` in einer URL | [View-Parameter](../teil-3-erweiterungsentwickler/view-parameter.md) |
| Workflow, Workflow-Ereignis (workflow, workflow event) | eine Liste von Schritten, die ein Trigger ausführt, und ein einzelner Schritt | [Die eZWorkflowType-API](ezworkflowtype-api.md) |

**Demomodus (demo mode)** - `EXP_DEMO_MODE=1` in der Umgebung: Administratorpasswörter lassen sich nicht ändern oder
zurücksetzen (die `expDemoMode`-API, der Speicherschutz in `eZUser::store()`), und die Installer akzeptieren das
veröffentlichte Demo-Passwort. [Demomodus für Entwickler](demomodus-fuer-entwickler.md).

## Verwandte Seiten

- Zurück: [expservices: entfernte Dienste für Erweiterungen](expservices.md)
- Weiter: [Stichwortverzeichnis des Buchs: jedes Thema von A bis Z](../themenindex.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
