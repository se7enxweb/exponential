---
title: "Erweiterungsentwicklung: das Buch (hier beginnen)"
audience: developer, designer, app-creator, user
category: extension-development
order: 0
summary: Ein vollständiges Buch über den Weg vom Benutzer zum Builder, Erweiterungsentwickler und Theme-Entwickler mit Exponential - Ideen finden, Erweiterungen mit und ohne RAD-Werkzeuge bauen, aktualisieren, ethisch forken, veröffentlichen.
keywords: Buch, Erweiterungsentwicklung, hier beginnen, Entwickler, Builder, Theme-Entwickler, Designer, Anwendungsentwickler, Tutorial, RAD, Upgrade, Release, beitragen, Referenz, book, extension development, start here, developer, builder, theme developer, designer, application creator, tutorial, upgrade, release, contribute, reference
featured: true
type: help
---
# Erweiterungsentwicklung: das Buch (hier beginnen)

Dieses Buch zeigt Ihnen Schritt für Schritt, wie Sie verändern, was Exponential kann. Es beginnt dort, wo Sie stehen,
als Benutzer der Administration, und führt Sie durch vier Stufen: **Builder** (Einstellungen, Templates, Overrides),
**Erweiterungsentwickler** (Module, Operatoren, Datentypen, Workflow-Ereignisse, Cronjobs und mehr),
**Theme-Entwickler** (Design-Erweiterungen) und **Maintainer** (Ihre Arbeit veröffentlichen und zur Arbeit anderer
beitragen). Unterwegs zeigt es Ihnen, wo die vorhandenen Erweiterungen der Community zu finden sind, damit Sie mit
dem beginnen, was andere gebaut haben, statt bei null, und wie Sie deren Arbeit fair wiederverwenden.

Jeder Befehl und jedes Codebeispiel in diesem Buch wurde am Kernel geprüft, den es beschreibt: Die genannten Klassen,
Methoden und Einstellungen existieren, die PHP-Beispiele bestehen `php -l`, und die Template-Beispiele wurden von der
Template-Engine gerendert. Wenn eine Seite eine Tatsache nennt, gibt sie die Datei oder die Seite an, aus der diese
Tatsache stammt.

Offizielle Exponential-Zertifizierungen sind geplant, als Teil der Zukunftspläne des Projekts für die Community,
Partner, Exponential-Entwickler und Lernende; sie sind noch nicht verfügbar
([Zertifizierungen und Zertifikate](../lernende/zertifizierungen-und-zertifikate.md)).

## Auf dem ganzen Stack entwickeln: Velocity ist Teil von Exponential 6.0.15

Seit 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential: Die `composer.json` des
Hauptpakets verlangt `se7enxweb/exponential-velocity ~0.0.4.55`, sodass Ihre Entwicklungsinstallation denselben Server
hat, auf dem die Produktion läuft, auf PHP 8.0 und neuer. Für einen Erweiterungsentwickler ändert das drei Dinge:

- **Sie entwickeln gegen die Engine, die die Website ausliefert.** Der eigene Server von Velocity ist die empfohlene
  Engine für jede Stufe, vom Laptop bis zur Produktion (FrankenPHP ist die produktionsreife Alternative). Starten Sie
  ihn mit `./console exp:velocity start`, nachdem Sie `[ServerSettings] Engine=qbix` gesetzt haben.
- **Worker halten Ihren Code geladen.** Eine geänderte Klasse oder Einstellung erreicht die Website nach
  `./console exp:velocity deploy` (oder `restart`), das die Autoloads neu erzeugt, die Caches leert und die Worker in
  der richtigen Reihenfolge neu startet; `./console exp:velocity cache clear` leert den Antwort-Cache. Schreiben Sie
  Code, der keinen Zustand pro Anfrage in statischen Variablen oder Globals hält, damit er sich unter persistenten
  Workern genauso verhält wie unter PHP-FPM.
- **Es ist mehr im Lieferumfang.** Die RAD-Werkzeuge schreiben in Minuten eine funktionierende Erweiterung, und
  Velocity bringt WebSockets und Server-Sent Events, einen Scheduler und einen Antwort-Cache ohne weiteres Paket mit.
  Exponential ist ein einziger funktionierender Stack (Server, RAD-Plattform, CMS, Digital Experience, E-Commerce), nicht
  nur ein Framework, das Sie selbst vervollständigen.

Die Geschichte: [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md). Die Engines:
[Velocity-Engines](../../bc/6.0/velocity-engines.md). Die RAD-Werkzeuge: [RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md).

## Die Teile

| Teil | Für | Was Sie lernen |
|---|---|---|
| [Teil 1: Vom Benutzer zum Entwickler](teil-1-vom-benutzer-zum-entwickler/README.md) | alle | Was eine Erweiterung ist, die Werkzeuge, eine Entwicklungsinstallation, Code und Logs lesen, vorhandene Erweiterungen auf GitHub, Packagist, Share Exponential, Exponential Projects und eZpedia finden und sie im Rahmen ihrer Lizenzen wiederverwenden |
| [Teil 2: Der Builder](teil-2-builder/README.md) | Builder, Designer, Anwendungsentwickler | Einstellungen und ihre Geltungsbereiche, Templates, die Design-Kette, Overrides, View-Modi, Fetch, Inhaltsklassen, Rollen, Workflows, Cronjobs, alles ohne PHP |
| [Teil 3: Der Erweiterungsentwickler](teil-3-erweiterungsentwickler/README.md) | Entwickler | Der Aufbau einer Erweiterung, Kapitel für Kapitel: Metadaten, Einstellungen, Autoloads, Module und Views, Richtlinien, Operatoren, Fetch-Funktionen, Datentypen, Workflow-Ereignisse, Cronjobs, Befehle, Designs, Ajax, Übersetzungen, Tabellen, Events, Handler, Sicherheit, CSP, Administrationsseiten, Tests, Dokumentation |
| [Tutorials](teil-3-erweiterungsentwickler/tutorials/README.md) | Entwickler | Echte kleine Erweiterungen, Schritt für Schritt gebaut, mit den RAD-Werkzeugen und von Hand |
| [Teil 4: Eine alte Erweiterung aktualisieren](teil-4-aktualisieren/README.md) | Entwickler | Eine Erweiterung, die für ältere PHP-Versionen und ältere Releases geschrieben wurde, auf Exponential 6 bringen, mit Vorher-nachher-Beispielen aus Erweiterungen, die dieses Projekt modernisiert hat |
| [Teil 5: Erweitern, forken und beitragen](teil-5-erweitern-forken-zurueckgeben/README.md) | Entwickler, Designer | Erweitern ohne zu forken, ethisch forken und umbenennen, Lizenzen, Namensnennung, Pull Requests, verwaiste Erweiterungen übernehmen, der Weg zum Maintainer |
| [Teil 6: Theme-Entwickler](teil-6-theme-entwickler/README.md) | Designer | Themes als Design-Erweiterungen, mit dem RAD-Assistenten und von Hand; Assets, der Packer, CSP, Dark Mode, Verteilung |
| [Teil 7: Ihre Erweiterung veröffentlichen](teil-7-veroeffentlichen/README.md) | Maintainer | Versionsnummern, Metadaten, Composer und Packagist, Changelogs, GitHub-Releases, Pakete, die Release-Checkliste |
| [Teil 8: Referenz](teil-8-referenz/README.md) | Entwickler | Die Kernel-Klassen, APIs, Operatoren, Funktionen, Einstellungen und Befehle, die ein Erweiterungsautor am häufigsten braucht |

## Wo Sie beginnen

| Sie sind | Beginnen Sie mit |
|---|---|
| Redakteur oder Administrator und möchten bauen | [Teil 1](teil-1-vom-benutzer-zum-entwickler/README.md), dann [Teil 2](teil-2-builder/README.md) |
| PHP-Entwickler und neu bei Exponential | [Was eine Erweiterung ist](teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md), dann [Teil 3](teil-3-erweiterungsentwickler/README.md) und die [Tutorials](teil-3-erweiterungsentwickler/tutorials/README.md) |
| Designer | [Teil 2](teil-2-builder/README.md), dann [Teil 6](teil-6-theme-entwickler/README.md) |
| Anwendungsentwickler | [Teil 2](teil-2-builder/README.md), dann das [Abschluss-Tutorial](teil-3-erweiterungsentwickler/tutorials/abschlussprojekt-eine-vollstaendige-erweiterung.md) |
| Jemand mit einer alten Erweiterung, die auf den neuesten Stand muss | [Teil 4](teil-4-aktualisieren/README.md) |
| Kurz davor, eine Erweiterung zu veröffentlichen | [Teil 7](teil-7-veroeffentlichen/README.md) und [Teil 5](teil-5-erweitern-forken-zurueckgeben/README.md) |

[Lesepfade durch das Buch](lesepfade.md) nennt vollständige Lesereihenfolgen für jede Rolle, und der
[Index des Buchs](themenindex.md) führt jedes Thema von A bis Z auf.

## Schnell: Was kann ich bauen, und wie schnell?

| Sie möchten | Schnellster Weg | Kapitel |
|---|---|---|
| Einen neuen Wert oder eine neue Funktion für Templates | einen Template-Operator, mit dem RAD-Assistenten in wenigen Minuten | [Mit RAD: ein Template-Operator in fünf Minuten](teil-3-erweiterungsentwickler/tutorials/rad-template-operator.md) |
| Eine Seite mit eigener Adresse | ein Modul mit einer View | [Von Hand: ein Modul mit einer View](teil-3-erweiterungsentwickler/tutorials/von-hand-modul-mit-view.md) |
| Eine neue Art von Feld für Inhalte | einen Datentyp | [Mit RAD: ein Datentyp](teil-3-erweiterungsentwickler/tutorials/rad-datentyp.md) |
| Dass etwas geschieht, wenn Inhalte veröffentlicht werden | ein Workflow-Ereignis | [Mit RAD: ein Workflow-Ereignis](teil-3-erweiterungsentwickler/tutorials/rad-workflow-ereignis.md) |
| Eine Aufgabe, die jede Nacht läuft | einen Cronjob-Teil | [Von Hand: ein Cronjob-Teil](teil-3-erweiterungsentwickler/tutorials/von-hand-cronjob-teil.md) |
| Ein neues Aussehen | eine Design-Erweiterung | [Teil 6](teil-6-theme-entwickler/README.md) |
| Die Einstellungen der Website unter Versionskontrolle | eine reine Einstellungs-Erweiterung | [Mit RAD: eine reine Einstellungs-Erweiterung](teil-3-erweiterungsentwickler/tutorials/rad-einstellungs-erweiterung.md) |

Die RAD-Werkzeuge finden Sie unter **Setup > RAD** (`/setup/rad`); sie listen jede Stelle auf, an der der Kernel
erweitert werden kann, und schreiben für die meisten davon eine funktionierende Erweiterung. Jedes Tutorial zeigt
dieselbe Erweiterung auch von Hand geschrieben, damit Sie verstehen, was der Assistent geschrieben hat, und auch ohne
ihn arbeiten können.

## Konventionen in diesem Buch

- Befehle werden im Wurzelverzeichnis der Installation ausgeführt. `./console` ist eine Abkürzung für
  `bin/php/console` ([wie Sie sie anlegen](teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)).
  Fügen Sie `--allow-root-user` hinzu, wenn Sie einen Befehl als root ausführen.
- Beispielerweiterungen heißen `mysite_hello` und ähnlich, und neue Klassen beginnen mit `exp` oder Ihrem eigenen
  Präfix, nie mit `ez`: Dieses Präfix gehört den vorhandenen Klassen des Kernels.
- Pfade in der Administration werden als `Setup > RAD` geschrieben, Adressen als `/setup/rad`.
- Das Produkt heißt **Exponential**. Es führt die Codebasis von eZ Publish fort, das sein Hersteller nach fast
  18 Jahren eingestellt hat; Kernel-Bezeichner wie `eZINI` und Dateinamen wie `ezinfo.php` behalten ihre historischen
  Namen.

**Demomodus.** Code, der Benutzerpasswörter setzt oder ändert, sollte den Demomodus beachten (`EXP_DEMO_MODE=1` auf öffentlichen Demos): [Demomodus für Entwickler](teil-8-referenz/demomodus-fuer-entwickler.md).

## Verwandte Seiten

- [Mit Git entwickeln: vom Benutzer zum Entwickler wachsen](../mit-git-entwickeln.md)
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../anleitungen/extensions.md)
- [RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)
- [Geben Sie Ihre Erweiterungen zurück](../erweiterungen-zurueckgeben.md)
- [Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Dieses Buch darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
