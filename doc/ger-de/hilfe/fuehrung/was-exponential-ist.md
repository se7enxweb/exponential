---
title: "Was Exponential ist: die Produktfamilie"
audience: project-manager, sales-marketing
category: leadership
order: 20
summary: Exponential und seine Produktfamilie in einfachen Worten – der Kernel für CMS, CMF und Shop, die Platform-Distributionen, Velocity, Intelligence, Layouts, Erweiterungen, Lizenzen und die Support-Zusage, alles mit Quellen belegt.
keywords: Exponential, Produktfamilie, Überblick, CMS, CMF, E-Commerce, Exponential Platform, Legacy, Nexus, DXP, Exponential Basic, Velocity, Exponential Intelligence, expai, Exponential Layouts, AdminUI, Erweiterungen, Lizenz, GPL, Support, 7x, Exponential Foundation, product family, overview, e-commerce, extensions, license, support
type: help
---
# Was Exponential ist: die Produktfamilie

Diese Seite ist für Projektleiter und für Kollegen aus Vertrieb und Marketing, die Exponential mit eigenen Worten
erklären müssen: einem Sponsor, einem Kunden, einem neuen Teammitglied oder einem Lenkungsausschuss. Sie beantwortet,
was das Produkt ist, aus welchen Produkten die Familie besteht und für wen jedes davon gedacht ist, worauf es läuft,
unter welcher Lizenz es steht und wozu sich das Projekt verpflichtet. Jede Aussage stammt aus einer Datei in diesem
Repository oder von den beiden Seiten zur Geschichte in dieser Hilfe und nennt ihre Quelle.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Woher es kommt](#woher-es-kommt)
3. [Wer dahintersteht](#wer-dahintersteht)
4. [Was der Kernel leistet](#was-der-kernel-leistet)
5. [Die Produktfamilie](#die-produktfamilie)
6. [Ein Produkt für ein Projekt wählen](#ein-produkt-für-ein-projekt-wählen)
7. [Worauf es läuft](#worauf-es-läuft)
8. [Sprachen und Barrierefreiheit](#sprachen-und-barrierefreiheit)
9. [Lizenzen](#lizenzen)
10. [Wozu sich das Projekt verpflichtet](#wozu-sich-das-projekt-verpflichtet)
11. [Es erklären: drei Fassungen](#es-erklären-drei-fassungen)
12. [Begriffe, die Sie hören werden](#begriffe-die-sie-hören-werden)

## In einem Absatz

Exponential ist „ein professionelles PHP-Anwendungs-Framework mit fortgeschrittener CMS-Funktionalität
(Content-Management-System). Als CMS ist sein bemerkenswertestes Merkmal sein vollständig anpassbares und erweiterbares
Inhaltsmodell“ (README, zitiert unter
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)).
Es ist Open Source unter der GNU General Public License, Version 2 oder höher, wird mit Composer von Packagist
installiert, und seine aktuelle Linie ist **Exponential 6.0.x**. Ein Content-Management-System, ein
Anwendungs-Framework (CMF) und ein eingebauter Shop kommen in einem Paket
([Wer ist 7x?](../wer-ist-7x.md#cms-cmf-und-shop-in-einem)).

### Für Entscheider: ein Stack, und der Server gehört dazu

Seit 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential: Die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity ~0.0.4.55`, also hat ihn jede Installation, auf PHP 8.0 und
höher. Das Projekt nennt das einen Wendepunkt, einen Grundstein und einen Schlussstein: eine neue Stufe der Integration
von Anwendungssystemen, die im Kernprodukt standardmäßig noch mehr Funktionen ab Werk bietet. Was das für Ihre
Entscheidung bedeutet:

- **Ein Produkt, kein Framework plus Teile.** Exponential ist eine ganze, funktionierende Einheit: ein Stack für die
  schnelle Anwendungsentwicklung (RAD) von Internetanwendungen, der CMS, Digital-Experience-Plattform und
  E-Commerce-System zugleich ist, mit eigenem Server, aus einer Quelle, mit einem Installationsprogramm und einer
  Dokumentation.
- **Weniger Integrationsrisiko.** Vor der ersten Seite muss kein Server ausgewählt und eingebunden werden, und es gibt
  keine PHP-Version, auf der ein Teil des Stacks fehlt. Das räumt ein übliches Hindernis aus dem Weg, Exponential
  irgendwo auf der Welt einzusetzen.
- **Die Freiheit bleibt.** Velocity ist die empfohlene Engine für jede Stufe und FrankenPHP eine produktionsreife
  Alternative, aber Apache und nginx mit PHP-FPM funktionieren weiter wie bisher; die Wahl des Hostings bleibt offen.

Die ganze Geschichte: [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md).

## Woher es kommt

- **Fast dreißig Jahre einer einzigen Codebasis.** Der Urheberrechtsvermerk lautet „Copyright (C) 1998 - 2026 7x &
  Exponential Foundation“ neben „Copyright (C) 1999 - 2014 eZ Systems AS“ (`COPYRIGHT.md`). Der älteste Commit im
  Repository stammt vom 2. September 2002 ([Wer ist 7x?](../wer-ist-7x.md#7x-in-zahlen)).
- **Eine Fortführung, kein Fork um seiner selbst willen.** Exponential führt die Codebasis von eZ Publish fort, einem
  Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat. 7x übernahm 2023 die Führung des Projekts,
  veröffentlichte 6.0.0 am 1. Januar 2024 und benannte das Produkt im August 2025 in Exponential um
  ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#zeitleiste)).
- **Unabhängig.** „Dieses Projekt steht in keiner Verbindung zur ursprünglichen Software eZ Publish oder zu ihrem
  ursprünglichen Entwickler, eZ Systems oder Ibexa“ (README, zitiert unter [Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt)).
- **Jede Änderung seit Dezember 2023 wird erzählt**, in [der Chronik](../../geschichte/README.md), und die vollständige
  Git-Historie bleibt erhalten ([Mit Git entwickeln](../mit-git-entwickeln.md)).

## Wer dahintersteht

| | 7x | Exponential Foundation |
|---|---|---|
| Was es ist | Ein nordamerikanisches Unternehmen für Webdesign, Support, Entwicklung und Hosting | Eine gemeinnützige Körperschaft nach 501(c)(3), gegründet am 19. März 2026 in Seattle |
| Rolle | Leitet Entwicklung, Support, Hosting und Leistungen; „der derzeit führende Beitragende“ | Betreibt das Projekt; finanziert und lenkt es |
| Lesen | [Wer ist 7x?](../wer-ist-7x.md) | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |

Beide Seiten sind ausführlich mit Quellen belegt. Die Quellen nennen weder den Vorstand der Foundation noch ihre
Governance oder ihr Budget; die Seite der Foundation sagt das offen
([Was die Quellen nicht sagen](../wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen)).

## Was der Kernel leistet

Das README führt die wichtigsten Funktionen des Kernels auf: benutzerdefinierte Inhaltsklassen, Versionskontrolle,
Mehrsprachigkeit, eine eingebaute Suchmaschine, rollenbasierte Berechtigungen, Freigabe und zeitgesteuerte
Veröffentlichung, Multisite-Unterstützung, eingebauten E-Commerce, Workflows, eine Template-Engine, eine Headless-REST-API,
eine Datenbank-Abstraktionsschicht, Clustering und eine Plugin-API
([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel)).

Was das für ein Projekt bedeutet, mit der Seite, die jeden Teil erklärt:

| Bereich | Was Sie bekommen | Lesen |
|---|---|---|
| Content-Management | Eigene Inhaltsklassen, Objekte, Versionen, Entwürfe, Freigabe, Sektionen, Zustände, der Inhaltsbaum | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Berechtigungen | Rollen aus Richtlinien, begrenzt nach Sektion, Klasse, Teilbaum, Sprache oder Siteaccess | [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md) |
| Freigabe | Workflows, die eine Version zurückhalten, bis sie freigegeben ist | [Workflows](../../anleitungen/workflows.md) |
| Seitenaufbau | Exponential Layouts: Layouts, Zonen, Blöcke und Regeln | [Der Layout-Editor](../layout-editor.md) |
| Schreibunterstützung | Exponential Intelligence: nur Entwürfe, veröffentlicht nie von sich aus | [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) |
| Multisite | Mehrere Sites aus einer Installation, jede ein Siteaccess | [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md) |
| Sprachen | 368 Gebietsschema-Dateien als Inhaltssprachen; Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite Sprachen) | [Sprachen in Exponential](../../sprachen.md) |
| E-Commerce | Produkte, Warenkörbe, Bestellungen, Mehrwertsteuer, Währungen, PayPal | [Wer ist 7x?](../wer-ist-7x.md#cms-cmf-und-shop-in-einem) |
| Integration | REST-API v1 und v2 | [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) |
| Sicherheit und Prüfung | Content Security Policy, zweistufige Anmeldung, ein manipulationssicheres Audit-Protokoll | [Sicherheitshärtung](../sicherheitshaertung.md), [Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| Hilfe | Diese Hilfe: die Dokumentation des Repositorys innerhalb der Administration | [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md) |

## Die Produktfamilie

Die Foundation nennt „Exponential, Exponential Platform und Exponential Basic“ als die Produkte, für die sie steht.
Rund um diese pflegen 7x und das Projekt einen Webserver, Erweiterungen und Werkzeuge. Die vollständigen
Beschreibungen, mit Quellen und dem Stand jedes Produkts, finden Sie unter
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick). Kurz gefasst:

| Produkt | Was es ist | Typischer Käufer oder Anwender |
|---|---|---|
| **Exponential 6** | Der Kernel für CMS, CMF und Shop, auf dem diese Installation läuft | Organisationen mit strukturierten Inhalten, mehreren Sites oder mehreren Sprachen; Entwickler, die auf seinem Framework aufbauen |
| **Exponential Platform 3.2.9** | Reines Symfony-CMS aus der letzten freien eZ Platform | Teams auf dem Stack von eZ Platform 3, die ein aktuelles PHP möchten |
| **Exponential Platform Legacy** | Die Symfony-Plattform plus Exponential 6 („Dual Kernel“) | Bestehende Sites, die Exponential 6 behalten und Symfony daneben hinzufügen |
| **Exponential Platform Nexus** | Platform plus Exponential 6, Netgen Layouts und Media Site, mit Demo-Inhalten | Agenturen und Teams, die schnell eine vollständige Site möchten |
| **Exponential Platform DXP** | Eine reine Symfony-Plattform für digitale Erlebnisse, die Fortführung der Linie Ibexa DXP OSS | Neue Symfony-Projekte |
| **Exponential Basic** | Das einfache CMS der Linie eZ Publish 2 | Bildung, private Nutzung, kleine Sites |
| **Exponential Velocity** | Ein in PHP geschriebener Web- und Anwendungsserver | Alle, die Exponential ausliefern |
| **Exponential Intelligence** (`expai`) | Ein Schreibassistent in der Administration | Redakteure, Projektleiter und Content-Autoren |
| **Exponential Layouts** (`explayouts`) | Ein visueller Seiteneditor | Redakteure und Designer |
| **AdminUI** (`exp_adminui`) | Eine alternative Administrationsoberfläche | Redakteure, die sie bevorzugen |
| **Die Erweiterungen** | 66 Pakete von `se7enxweb`, die der Kernel voraussetzt | Site-Entwickler |

> **Warnung**
> Exponential Platform DXP enthält **keinen** Layout-Editor, keinen Freigabe-Workflow, keinen Scheduler und nicht den
> Legacy-Kernel; seine Abhängigkeiten enthalten nichts davon. Für Exponential 6 neben Symfony verweisen die Quellen auf
> Legacy oder Nexus ([Wer ist 7x?](../wer-ist-7x.md#die-platform-linie-exponential-platform-legacy-nexus-und-dxp)).
> Prüfen Sie die Distribution, bevor Sie irgendjemandem eine Funktion zusagen.

## Ein Produkt für ein Projekt wählen

| Das Projekt braucht | Beginnen Sie mit | Lesen |
|---|---|---|
| Ein CMS mit Shop, Multisite und vielen Sprachen, nur PHP, ohne Datenbankserver | Exponential 6 mit SQLite | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Redakteure, die Seiten selbst aufbauen | Exponential 6 mit Exponential Layouts | [Der Layout-Editor](../layout-editor.md) |
| Hilfe beim Schreiben und Übersetzen von Inhalten | Exponential Intelligence | [expai](../../funktionen/6.0/erweiterungen/expai.md) |
| Den schnellsten Weg, die Site auszuliefern | Exponential Velocity | [Der Server mit persistenten Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) |
| Ein Symfony-Projekt mit REST und GraphQL, ohne Legacy-Kernel | Exponential Platform DXP | [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md) |
| Eine vollständige Demo-Site mit Layouts auf der Symfony-Plattform | Exponential Platform Nexus | [Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md) |
| Eine Site mit Exponential 6 behalten und Symfony daneben hinzufügen | Exponential Platform Legacy | [Legacy Bridge](../../funktionen/6.0/legacy-bridge.md) |
| Ein kleines, einfaches CMS zum Lernen | Exponential Basic | [Wer ist 7x?](../wer-ist-7x.md#exponential-basic) |

Diese Tabelle folgt „Which product is for you“ unter
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt). Die übrigen
Seiten zur Führung sind für Exponential 6 geschrieben, den Kernel, zu dem diese Hilfe gehört.

## Worauf es läuft

| Schicht | Fakten | Lesen |
|---|---|---|
| PHP | 8.0 bis 8.5, Velocity eingeschlossen (ab seinem Release 0.0.4.54); 7.4 nur bis 6.0.7 | [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Datenbank | In der Reihenfolge der Empfehlung: SQLite 3 (der Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Betriebssystem | „fast jedes, auf dem PHP 8.0 oder höher läuft“, eingeteilt in vier Stufen | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Webserver | Exponential Velocity (empfohlen; seit 6.0.15 Teil von Exponential und mit ihm installiert), FrankenPHP (produktionsreife Alternative), Apache oder nginx mit PHP-FPM und weitere | [Kapitel 8: Die Site ausliefern](../../installation/08-die-website-ausliefern.md) |
| Installation | Composer-Pakete oder Git-Klone mit vollständiger Historie (der empfohlene Weg, Websites zu bauen) | [Mit Git entwickeln](../mit-git-entwickeln.md) |

Die Entscheidung zwischen ihnen ist Thema von
[Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md).

## Sprachen und Barrierefreiheit

- **368 Gebietsschema-Dateien** (361 Sprachen und Länder, die Testsprache Lorem Ipsum und 6 Varianten),
  43 davon von rechts nach links geschrieben, können als Inhaltssprachen gewählt werden; es gibt
  **Übersetzungen der Administration** (die aktuellen Gesamtzahlen stehen am Anfang der Seite Sprachen) ([Sprachen in Exponential](../../sprachen.md)).
- Eine Installation enthält auf 64-Bit-PHP höchstens 62 Inhaltssprachen gleichzeitig
  ([Sprachen](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).
- Die Administration dreht sich für Sprachen der Oberfläche um, die von rechts nach links geschrieben werden, und
  Inhalte in einer solchen Sprache werden entsprechend ausgezeichnet ([Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md)).
- Das Design admin4 wird durch Tests auf WCAG-Regeln geprüft: den Farbkontrast der Tokens im hellen und im dunklen Modus
  und Zielgrößen von mindestens 24 mal 24 Pixeln ([Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md)).

## Lizenzen

| Was | Lizenz | Quelle |
|---|---|---|
| Die Software | GNU General Public License, Version 2 oder jede spätere Version. „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos. Die Professional License ist nicht mehr erhältlich.“ | README; `LICENSE` |
| Beiträge | Unter derselben GPL v2 oder höher veröffentlicht | `CONTRIBUTING.md` |
| Die Dokumentation, einschließlich dieser Seite | GNU Free Documentation License 1.3 oder höher | [Die Lizenz dieser Dokumentation](../../README-LIZENZ.md) |
| Exponential Velocity | MIT | seine `composer.json` ([Wer ist 7x?](../wer-ist-7x.md#open-source-lizenzen)) |

Dies ist eine Zusammenfassung, keine Rechtsberatung. Fragen von Käufern werden unter
[Lizenzierung für Käufer erklärt](../geschaeft/lizenzen-fuer-kaeufer.md) beantwortet.

## Wozu sich das Projekt verpflichtet

Das Projekt hat kein einzelnes Dokument mit einer „Support-Richtlinie“. Die folgenden Zusagen sind jeweils an einer
benannten Stelle festgehalten ([Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)):

- **Die gepflegte Linie** ist Exponential 6.0. Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem
  nächsten Release 6.0.x veröffentlicht; 5.x, 4.x, 3.x und ältere Versionen werden nicht unterstützt (`SECURITY.md`).
- **Sicherheitsmeldungen** gehen vertraulich an die Adresse in `SECURITY.md`, die eine erste Antwort innerhalb eines
  Werktags verspricht.
- **Veröffentlichte Versionen ändern sich nie.** Eine Korrektur erreicht Sie als nächstes Release, nie als korrigierte
  Kopie eines alten (`SECURITY.md`).
- **Aktuelles PHP.** Die Continuous Integration führt die Testsuite auf PHP 8.1 bis 8.5 aus und prüft bei jedem Push
  und jedem Pull-Request, dass PHP 8.0 noch startet ([CI](../../bc/6.0/ci.md)).
- **Die Dokumentation lebt beim Code** und wird in dieser Hilfe angezeigt ([Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)).

## Es erklären: drei Fassungen

Nutzen Sie diese als Ausgangspunkt. Jeder Satz ist nur aus den oben genannten Quellen gebaut.

**Ein Satz.**

> Exponential ist ein Open-Source-Content-Management-System, Anwendungs-Framework und Shop unter der GPL, das eine
> fast dreißig Jahre alte Codebasis fortführt, von 7x geleitet und von der gemeinnützigen Exponential Foundation
> betrieben wird.

**Drei Sätze.**

> Exponential ist ein Open-Source-Content-Management-System in PHP mit einem Inhaltsmodell, das Sie selbst festlegen,
> mit Versionen, Freigabe-Workflows, Multisite, vielen Sprachen und einem eingebauten Shop. Es steht unter der GPL v2
> oder höher, wird mit Composer oder als Git-Klone installiert und läuft auf PHP 8.0 bis 8.5 mit sechs unterstützten
> Datenbanken, allen voran SQLite. Das Unternehmen 7x leitet seine Entwicklung, und die gemeinnützige Exponential
> Foundation betreibt das Projekt.

**Für einen Lenkungsausschuss.** Ergänzen Sie aus dieser Seite: die Tabelle der Produktfamilie, die Reihenfolge der
Datenbanken, die Support-Zusage und die Tabelle der Lizenzen. Verweisen Sie für die Geschichte auf
[Wer ist 7x?](../wer-ist-7x.md) und für die Fragen, die ein Ausschuss zur Langlebigkeit stellen wird, auf
[Risikomanagement](risikomanagement.md).

## Begriffe, die Sie hören werden

| Begriff | Bedeutung |
|---|---|
| **Siteaccess** | Ein Zugang zu einer Installation: eine Site, eine Sprache oder die Administration |
| **Inhaltsklasse** | Eine Art von Inhalt (Artikel, Ordner ...) mit ihren Attributen |
| **Objekt, Knoten** | Ein einzelner Inhalt; sein Platz im Inhaltsbaum |
| **Sektion** | Eine Gruppe von Objekten, die für Berechtigungen und Designs verwendet wird |
| **Rolle, Richtlinie** | Eine Menge von Berechtigungen; eine einzelne Berechtigung |
| **Erweiterung** | Ein Paket, das Module, Klassen, Templates oder Einstellungen hinzufügt |
| **Dual Kernel** | Eine Distribution, die die Symfony-Plattform und Exponential 6 zusammen betreibt |
| **Velocity** | Der eigene PHP-Web- und Anwendungsserver von Exponential |

Alle weiteren Begriffe finden Sie im [Glossar](../../glossar.md) und im Glossar von
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#glossar).

## Verwandte Seiten

- [Projektleiter: Hier beginnen](projektleiter-hier-beginnen.md)
- [Positionierung und Nutzenversprechen](../geschaeft/positionierung-und-nutzenversprechen.md)
- [Funktionshöhepunkte, zugeordnet zu Kundenbedürfnissen](../geschaeft/funktionshighlights-fuer-kunden.md)
- [Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
