---
title: "Mit Exponential bauen: warum, welches Produkt, erste Schritte"
audience: user, developer, designer, admin, app-creator, website-creator
category: project
order: 50
summary: Die Seite „Build with Exponential“ von exponential.earth, mit der Software abgeglichen - warum darauf aufbauen (einfach, stabil, upgradefähig), was Sie ab Werk bekommen, die Produktfamilie, wie die Repositories sie beschreiben (Exponential 6, Basic, Platform, Platform Legacy, Nexus, DXP), die ersten fünf Schritte vom Download bis zur ersten veröffentlichten Seite und die Konsolenbefehle, mit Korrekturen, wo Website und Code voneinander abweichen.
keywords: bauen, warum Exponential, einfach, stabil, upgradefähig, Inhaltsmodell, Inhaltsklasse, Inhaltsbaum, Siteaccess, Rollen, Suche, Workflow, Erweiterungs-API, Cluster, statischer Cache, Produktfamilie, Download, Setup-Assistent, Design, Überschreibung, Konsole, build, why Exponential, simple, stable, upgradable, content model, content class, content tree, siteaccess, roles, search, workflow, REST, extension API, cluster, static cache, product family, Exponential 6, Exponential Basic, Exponential Platform, Platform Legacy, Platform Nexus, Platform DXP, download, setup wizard, design, override, console, exp:ezcache, exp:preload, exp:updatesearchindex
featured: true
type: help
---
# Mit Exponential bauen: warum, welches Produkt, erste Schritte

Die Seite [Build with Exponential](https://exponential.earth/build) der Website erklärt, warum Sie Websites und
Anwendungen auf Exponential bauen sollten, welches Mitglied der Produktfamilie Sie wählen und wie Sie vom Download
zur ersten veröffentlichten Seite kommen. Diese Seite folgt ihr in den Worten dieser Hilfe und prüft jede Aussage an
der Software in diesem Repository. Wo beide voneinander abweichen, folgt diese Seite dem Repository und sagt das in
einem Hinweis.

## Warum darauf aufbauen

Die Website nennt drei Gründe. Jeder ist in der Software zu sehen:

### Einfach von Grund auf

Alles beruht auf einem einzigen Inhaltsmodell, das Sie an einem Nachmittag lernen können:

- **Inhaltsklassen** legen die Form Ihrer Inhalte fest (ein Artikel hat einen Titel, eine Einleitung, einen
  Haupttext, ein Bild);
- **Inhaltsobjekte** enthalten die Inhalte selbst, mit Versionen und Übersetzungen;
- **der Inhaltsbaum** ordnet sie an, und die Adresse einer Seite folgt dem Baum, lesbar und vorhersehbar.

Zwischen der Änderung eines Templates und der Seite, die sie zeigt, liegt kein Build-Schritt.

### Stabil von Haus aus

Caches sind in den Kernel eingebaut und werden für Sie verwaltet; Sie leeren sie gezielt oder alle auf einmal, unter
**Setup > Cache-Verwaltung** in der Administration oder auf der Kommandozeile. Innerhalb der Linie 6.0 besteht ein
Upgrade aus neuem Code, einer Datenbank-Update-Datei für Ihre Datenbank-Engine und dem Leeren der Caches
([Upgrades, 11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)).

### Langfristig upgradefähig

Jedes Release ist mit einem Tag versehen, und jede Änderung ist im [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)
und in den Hinweisen zur Abwärtskompatibilität unter `doc/eng-us/bc/` festgehalten. Weil die Lizenz die GNU GPL
Version 2 oder später ist, können Sie forken, selbst hosten und nach Ihrem eigenen Zeitplan upgraden; kein Anbieter
kann Sie an sich binden.

## Was Sie ab Werk bekommen

Die Liste der Website, jeweils mit dem Ort, an dem Sie in dieser Hilfe mehr darüber lesen:

| Funktion | Lesen |
|---|---|
| Inhaltsmodellierung mit Inhaltsklassen und Datentypen | [Inhalte bearbeiten](../inhalte-bearbeiten.md) |
| Mehrere Sites, Sprachen oder Zielgruppen aus einer Installation (Siteaccesses) | [Sprachen](../sprachen.md) |
| Rollenbasierte Berechtigungen bis hinunter zu einzelnen Objekten | [Konten und Anmeldung](../konten-und-anmeldung.md) |
| Eingebaute Volltextsuche, mit anderen Suchmaschinen als Plug-ins | [Suchen](../suchen.md) |
| Workflows für Freigaben und zeitgesteuertes Veröffentlichen | [Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md) |
| Eine REST-API und JSON-Ansichten für den Headless-Einsatz | die [README](https://github.com/se7enxweb/exponential/blob/main/README.md) |
| Eine Erweiterungs-API: Module, Datentypen, Template-Operatoren, Workflow-Ereignisse und mehr | [Erweiterungen](../../anleitungen/extensions.md) |
| Clusterfähige Dateiablage | das [Installationsbuch](../../installation/README.md) |
| Statischer Cache für die schnellsten Seiten | das [Installationsbuch](../../installation/README.md) |
| Übersetzung in beliebig viele Sprachen | [Sprachen](../sprachen.md) |

## Die Produktfamilie

Die Website stellt Exponential als eine Familie von Produkten mit gemeinsamer Herkunft vor. Die Repositories der
[Organisation se7enxweb](https://github.com/se7enxweb) beschreiben jedes davon wie folgt; die vollständige
Darstellung mit Quellen finden Sie unter
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick).

| Produkt | Was es laut seinem Repository ist | Wählen Sie es, wenn |
|---|---|---|
| **Exponential 6** (diese Software) | Der Kernel für CMS, Framework und Shop, mit eigener Template-Sprache und Administration | Sie das vollständige CMS allein auf PHP möchten, mit oder ohne Datenbankserver |
| **Exponential Basic** | Ein kleines, einfaches CMS aus einer älteren, getrennten Linie der Codebasis, zum Lernen, für den persönlichen Gebrauch und für kleine Sites | Sie etwas Kleines zum Lernen möchten |
| **Exponential Platform** | Ein CMS auf dem Symfony-Framework aus der letzten freien Version dieser Plattform, ohne Exponential 6 | Sie diese Symfony-Plattform betreiben und dabei bleiben möchten |
| **Exponential Platform Legacy** | Die Symfony-Plattform mit einer vollständigen Kopie von Exponential 6 daneben (zwei Kernel) | Sie eine Exponential-6-Site behalten und Symfony daneben möchten |
| **Exponential Platform Nexus** | Die Symfony-Plattform mit Exponential 6, einem Layout-Editor und einer vollständigen Demo-Mediensite | Sie schnell eine vollständige Site mit Layouts und Demo-Inhalten möchten |
| **Exponential Platform DXP** | Eine reine Symfony-Plattform für digitale Erlebnisse mit REST und GraphQL, ohne Exponential 6 | Sie ein neues Symfony-Projekt ohne Legacy-Kernel beginnen |

> **Hinweis: wo Website und Repositories voneinander abweichen**
> Die Build-Seite der Website beschreibt einige Produkte anders als ihre Repositories. Sie nennt Exponential Basic
> eine vorkonfigurierte Distribution von Exponential CMS, während ihr Repository eine getrennte, ältere Linie der
> Codebasis beschreibt. Sie beschreibt Platform Legacy als Migrationspaket aus Kompatibilitätsschichten und
> Migrationswerkzeugen, während ihr Repository eine Distribution mit zwei Kerneln beschreibt. Sie beschreibt Nexus
> als „Headless first“-Ausgabe, die über die Installation einer Erweiterung erreicht wird, mit einer
> JavaScript-Frontend-Brücke sowie Ereignis- und KI-Pipelines, und die DXP als Satz von Exponential-Erweiterungen,
> während ihre Repositories Symfony-Distributionen beschreiben (Nexus mit Exponential 6 und dem Layout-Editor; die DXP
> ohne den Legacy-Kernel). Diese Hilfe folgt den Repositories. Lesen Sie vor der Wahl die eigene README jedes
> Produkts.

Wie Sie eine vorhandene Site auf eines davon umziehen, zeigt die [Migrationsübersicht](../../installation/migration/README.md).

## Vom Download zur ersten Seite

Die fünf Schritte der Website, mit den Einzelheiten aus dieser Hilfe:

1. **Herunterladen.** Holen Sie sich das neueste stabile Release aus den
   [GitHub-Releases](https://github.com/se7enxweb/exponential/releases), oder klonen Sie das Repository, damit
   spätere Updates nur ein `git pull` entfernt sind. Das Projekt empfiehlt Git-Klone für den Bau von Websites
   ([Mit Git entwickeln](../mit-git-entwickeln.md); [Den Code beschaffen](../../installation/03-den-code-beschaffen.md)).
2. **Installieren.** Legen Sie die Dateien in das Dokumentverzeichnis Ihres Webservers oder liefern Sie sie mit
   Velocity aus, öffnen Sie die Site in einem Browser und folgen Sie dem Setup-Assistenten: Datenbank,
   Administratorkonto, Site und Sprachen ([Der Setup-Assistent](../../installation/05-setup-assistent.md); andere Wege unter
   [Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md)).
3. **Modellieren Sie Ihre Inhalte.** Öffnen Sie in der Administration die Inhaltsklassen und legen Sie die Arten von
   Inhalten fest, die Sie brauchen. Der Rat der Website gilt: Beginnen Sie mit zwei oder drei Klassen und fügen Sie
   weitere hinzu, sobald Sie wissen, was das Projekt braucht; Attribute und Klassen lassen sich später hinzufügen,
   ohne Inhalte zu migrieren.
4. **Bauen Sie Ihr Design.** Legen Sie einen Design-Ordner an (am besten in Ihrer eigenen Erweiterung), fügen Sie
   Templates und Stylesheets hinzu und verweisen Sie Ihren Siteaccess darauf. Überschreiben Sie nur die Templates,
   die Sie brauchen; bearbeiten Sie nie die des Kerns, damit Ihre Arbeit Upgrades übersteht
   ([Exponential: häufige Fragen](faq.md#darf-ich-die-kerndateien-von-exponential-für-meine-site-ändern)).
5. **Veröffentlichen.** Erstellen Sie Inhalte im Baum, veröffentlichen Sie sie sofort oder später, fügen Sie einen
   Freigabe-Workflow hinzu, wenn Ihr Team einen braucht
   ([Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md)), und wärmen Sie die Caches vor.

## Die Konsole

Exponential 6.0.15 bringt `bin/php/console` mit (mit der Abkürzung `./console` im Hauptverzeichnis der
Installation), das jedes Kommandozeilenskript unter einem Namen ausführt. Die Befehle, die die Website zeigt,
abgeglichen mit `console list`:

```bash
php bin/php/console list                        # every command
php bin/php/console exp:ezcache --clear-all     # clear all caches
php bin/php/console exp:preload                 # warm the page caches and report broken links
php bin/php/console exp:updatesearchindex       # rebuild the search index
php bin/php/console exp:ezpgenerateautoloads -e # regenerate the class autoload arrays of the extensions
```

Führen Sie sie als der Benutzer aus, dem die Dateien gehören, im Hauptverzeichnis der Installation. `list` gibt
jeden Befehl mit einer einzeiligen Beschreibung und dem Skript aus, das er ausführt (`exp:preload` führt zum
Beispiel `bin/php/preload.php` aus); die Optionen sind die dieses Skripts.

## Hilfe holen, etwas zurückgeben

Sie kommen nicht weiter? [Unterstützung erhalten](unterstuetzung-erhalten.md). Wenn Exponential Ihnen Zeit spart oder Geld
einbringt, bittet die Website Sie, etwas zurückzugeben: die Arbeit zu sponsern, Code oder Dokumentation beizutragen
oder anderen davon zu erzählen ([Geben und spenden](geben-und-spenden.md),
[Zum Projekt beitragen](zum-projekt-beitragen.md)).

## Verwandte Seiten

- [Erste Schritte](../erste-schritte.md)
- [Das Installationsbuch](../../installation/README.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.
