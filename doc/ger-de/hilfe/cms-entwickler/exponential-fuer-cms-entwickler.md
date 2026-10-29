---
title: Exponential für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 2
summary: Für CMS-Entwickler, die Exponential zum ersten Mal begegnen - was das System ist, woher es kommt, wie eine Installation aufgebaut ist, was bei einer Anfrage geschieht, was mitgeliefert wird und wie Website-Projekte üblicherweise darauf gebaut werden.
keywords: Exponential, Exponential 6, Überblick, Architektur, Kernel, Erweiterungen, Designs, Einstellungen, var, Ablauf einer Anfrage, Siteaccess, Modul, View, Template, PHP, Datenbanken, Velocity, Geschichte, 7x, Exponential Foundation, Website-Projekt, overview, architecture, request lifecycle, site project
type: help
---
# Exponential für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die Websites bauen und Exponential noch nicht kennen. Sie zeigt das System im
Überblick: was es ist, wie eine Installation organisiert ist, was eine Anfrage durchläuft und wie ein Website-Projekt
üblicherweise aufgebaut ist. Die folgenden Seiten gehen auf jeden Teil im Detail ein.

## Inhalt

1. [Was Exponential ist](#was-exponential-ist)
2. [Woher es kommt](#woher-es-kommt)
3. [Eine Installation auf der Festplatte](#eine-installation-auf-der-festplatte)
4. [Eine Anfrage, Schritt für Schritt](#eine-anfrage-schritt-für-schritt)
5. [Was mitgeliefert wird](#was-mitgeliefert-wird)
6. [Wie ein Website-Projekt gebaut wird](#wie-ein-website-projekt-gebaut-wird)
7. [Was Sie zuerst lernen](#was-sie-zuerst-lernen)

## Was Exponential ist

Exponential 6 ist ein Content-Management-System in PHP, für PHP 7.4 bis 8.5, lizenziert unter GPL 2.0 oder später
(`composer.json` im Hauptverzeichnis). Seine Kernideen:

- ein **Inhaltsmodell, das Sie selbst festlegen**: Klassen mit typisierten Attributen; jedes Element ist ein Objekt
  mit Versionen und Übersetzungen, platziert in einem Inhaltsbaum ([Das Inhaltsmodell](das-inhaltsmodell.md));
- **Templates, die nach Regeln gewählt werden**, in einer eigenen Template-Sprache, innerhalb von Designs, die in
  einer Kette aufeinander zurückfallen
  ([Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md));
- **Siteaccesses**: viele Websites, Sprachen und Oberflächen aus einer Installation und einer Datenbank
  ([Siteaccesses und Multisite](siteaccesses-und-multisite.md));
- **Erweiterungen** für alles, was ein Projekt hinzufügt, damit der Kernel unverändert bleibt
  ([Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md));
- **Exponential Layouts** für Seiten, die Redakteure aus Blöcken zusammenstellen ([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md)).

## Woher es kommt

Exponential führt die Codebasis eines CMS fort, das sein Hersteller nach fast 18 Jahren eingestellt hat. 7x, das
Unternehmen, das Exponential führt, veröffentlicht die Releases 6.0.x seit Anfang 2024, und die Exponential
Foundation ist die gemeinnützige Organisation hinter dem Projekt; keine der beiden steht in Verbindung mit eZ Systems
oder Ibexa ([Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).
Entwickler, die eZ Platform oder Ibexa kennen, werden das Inhaltsmodell wiedererkennen:
[Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md).

## Eine Installation auf der Festplatte

```text
index.php                 the front controller
kernel/                   the kernel: content, user, shop, workflow, setup ... modules and classes
lib/                      libraries: template engine, database, INI, utilities
design/                   shipped designs (standard, admin designs)
settings/                 shipped default settings; never edited
  override/               this machine's settings (database, mail, hosts); never committed
  siteaccess/<name>/      settings of one siteaccess
extension/                every extension: shipped ones from Composer, and yours
var/                      storage (uploaded files), caches, logs
bin/php/                  command line scripts; bin/php/console is the console
```

Die Übersicht mit jedem Verzeichnis und seinem Zweck: [Die Landkarte für Entwickler](../trainer/entwickler/modul-01-die-entwicklungsinstallation/die-landkarte-fuer-entwickler.md)
und [Verzeichnisse unter var](../erweiterungsentwicklung/teil-8-referenz/verzeichnisse-unter-var.md).

## Eine Anfrage, Schritt für Schritt

1. Der Webserver (Velocity, FrankenPHP oder Apache bzw. nginx mit PHP-FPM) übergibt die Anfrage an Exponential.
2. Der **Siteaccess** wird anhand des Hosts, des Ports oder des ersten Pfadelements gewählt; ab jetzt gelten seine
   Einstellungen.
3. Der **URL-Alias** wird in ein Modul und eine View übersetzt, für Inhalte `content/view/full/<node id>`.
4. Die View prüft die **Berechtigungen** und rendert dann: bei Inhalten das View-Template des Knotens, das die
   **Override-Regeln** wählen, oder die gespeicherte Kopie aus dem **View-Cache**.
5. Das Ergebnis wird in das **Pagelayout** gesetzt, das ein **Layout** von Exponential Layouts darum zeichnen kann.
6. Die Antwort geht hinaus und wird eventuell vom HTTP-Cache oder vom Antwort-Cache von Velocity für den nächsten
   Besucher aufbewahrt.

Jeder Schritt hat seine Seite: [URL-Aliase und Routing](url-aliase-und-routing.md),
[Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md), [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md).

## Was mitgeliefert wird

| Bereich | Eingebaut oder in der Standarddistribution |
|---|---|
| Inhalte | 35 Kernel-Datentypen, Versionen, Übersetzungen, Relationen, Objektzustände, Papierkorb, Rich Text mit dem Online Editor |
| Website-Bau | Designs und Override-Regeln, Ansichtsmodi, Exponential Layouts, Seitenpakete mit Demo-Websites |
| Benutzer | Benutzerkonten als Inhalte, Gruppen, Rollen und Richtlinien, Registrierung, Zwei-Faktor-Anmeldung |
| Abläufe | Workflows (Freigabe, zeitgesteuertes Veröffentlichen), Cronjobs, Benachrichtigungen, Informationssammlung, ein Shop |
| Auslieferung | die REST-Schnittstelle (`ezprestapi`), `expservices`, RSS, Sitemaps, Suche (eingebaut oder Solr über eZ Find) |
| Betrieb | die Konsole (`./console`), Velocity, Caches auf mehreren Ebenen, Audit-Trail, Wartungsmodus |
| Datenbanken | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL ([Datenbankunterstützung](../datenbankunterstuetzung.md)) |

Die Liste aller Funktionen der Versionsreihe 6.0: [Funktionen von 6.0](../../funktionen/6.0/README.md).

## Wie ein Website-Projekt gebaut wird

| Schritt | Seite |
|---|---|
| Eine Entwicklungsinstallation einrichten | [Entwicklungsumgebung](entwicklungsumgebung.md) |
| Das Inhaltsmodell entwerfen, dann die Klassen anlegen | [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md), [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md) |
| Design und Templates in Ihrer Erweiterung bauen | [Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md) |
| Entscheiden, welche Seiten Redakteure mit Layouts zusammenstellen | [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md) |
| Rollen, Sprachen und Siteaccesses konfigurieren | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md), [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md) |
| Inhalte importieren | [Inhalte importieren](inhalte-importieren.md) |
| Bereitstellen und starten | [Eine Website bereitstellen](eine-website-bereitstellen.md), [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md) |

In jedem Projekt treffen sich zwei Arten zu bauen: Templates in einer Design-Erweiterung und der Layout-Editor ohne
Code ([Zwei Wege zum Bauen](../zwei-wege-zum-bauen/zwei-wege-zum-bauen.md)).

## Was Sie zuerst lernen

Drei Gewohnheiten machen alles andere leichter: in Klassen und Knoten denken, nie mitgelieferte Dateien ändern und
wissen, welcher Cache zu welcher Änderung gehört. Die Seite [Lesepfade](lesepfade.md) ordnet den Rest nach der
Zeit, die Sie haben.

## Verwandte Seiten

- [CMS-Entwickler: hier beginnen](cms-entwickler-hier-beginnen.md)
- [Die Template-Sprache: was sie ist](../template-sprache/was-die-template-sprache-ist.md)
- [Erste Schritte](../erste-schritte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
