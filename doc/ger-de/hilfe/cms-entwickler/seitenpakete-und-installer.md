---
title: Seitenpakete und Installer
audience: cms-developer
category: cms-development
order: 41
summary: Für CMS-Entwickler, wie Exponential-Websites aus Paketen installiert werden und wie ein Projekt auf dieselbe Weise ausgeliefert werden kann. Die Seite behandelt Pakete und Seitenpakete, den Paketserver, die drei Installer, das Standardpaket sevenx_multisite, eigene Pakete für Klassen und Inhalte und wann sich ein eigenes Seitenpaket lohnt.
keywords: Seitenpakete, Pakete, ezpkg, package.xml, Installer-Paket für Websites, Paketserver, sevenx_multisite, cjw_multisite, Einrichtungsassistent, Kickstarter, exp:install, ezpm, Klassenexport, Inhaltsexport, Installationsskripte, Starter-Website, reproduzierbare Installationen, site packages, packages, package server, setup wizard, kickstarter, class export, content export, install scripts, starter site, reproducible installs
type: help
---
# Seitenpakete und Installer

Diese Seite richtet sich an Entwickler, die eine Website wollen, die sich erneut installieren lässt: auf dem Rechner
eines Kollegen, auf Staging, für den nächsten Kunden. Exponential installiert Websites aus **Paketen**, und ein Projekt
kann denselben Mechanismus für seine eigenen Klassen und Starterinhalte nutzen, sodass eine neue Installation aus
Dateien statt aus Klicks entsteht.

## Inhalt

1. [Pakete in einem Absatz](#pakete-in-einem-absatz)
2. [Die drei Installer](#die-drei-installer)
3. [Das Standard-Seitenpaket](#das-standard-seitenpaket)
4. [Pakete in Ihrem Projekt](#pakete-in-ihrem-projekt)
5. [Ein eigenes Seitenpaket](#ein-eigenes-seitenpaket)
6. [Testen](#testen)
7. [Was Pakete nicht übertragen](#was-pakete-nicht-übertragen)

## Pakete in einem Absatz

Ein Paket ist ein Ordner mit einer `package.xml` und den Dateien, die sie nennt, ausgeliefert als ein `.ezpkg`-Archiv.
Zu den Typen gehören `contentclass`, `contentobject`, `extension`, `sitestyle` und `site`. Ein **Seitenpaket** (Typ
`site`) verlangt andere Pakete und kann Installationsskripte mitbringen, die Siteaccesses, Erweiterungen, Rollen und
Voreinstellungen einrichten. Installationen finden Pakete auf einem Paketserver (`settings/package.ini
[RepositorySettings] RemotePackagesIndexURL`) und bewahren heruntergeladene in `var/storage/packages/` auf
([Seitenpakete: hier beginnen](../seitenpakete/README.md), [Pakettypen](../seitenpakete/grundlagen/pakettypen.md)).

## Die drei Installer

| Installer | Verwenden Sie ihn für |
|---|---|
| `./console exp:install` | Entwicklung und skriptgesteuerte Installationen; standardmäßig SQLite und `sevenx_multisite` ([Die Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)) |
| Der Kickstarter (`bin/php/kickstarter.php`, `kickstart.ini`) | unbeaufsichtigte Installationen, gesteuert durch eine Einstellungsdatei ([Kickstarter](../../installation/06-kickstarter.md)) |
| Der Einrichtungsassistent im Browser | Installationen beim Hosting ohne Shell ([Einrichtungsassistent](../../installation/05-setup-assistent.md)) |

Alle drei lesen dieselben Pakete
([Wie Kickstarter und Konsole Pakete verwenden](../seitenpakete/grundlagen/wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)).

## Das Standard-Seitenpaket

`sevenx_multisite` ist der Standard von Exponential 6.0.15. Es legt die Klassen an, Demo-Inhalte für zwei Websites,
die Siteaccesses (öffentliche Websites, Sprachen, `admin`, `editor`), Rollen und die Layouts von Exponential Layouts aus
den Daten der Theme-Erweiterung ([sevenx_multisite](../seitenpakete/sevenx-multisite/README.md),
[Was Sie nach der Installation haben](../seitenpakete/sevenx-multisite/was-sie-nach-der-installation-von-sevenx-multisite-haben.md)). Es zu
studieren ist der schnellste Weg, eine vollständige Einrichtung wie in der Produktion zu sehen. Beginnen Sie ein Projekt
damit, entfernen Sie, was Sie nicht brauchen, und verwenden Sie seine Klassen wieder, wo sie passen
([Die sevenx-Klassen](../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)).

## Pakete in Ihrem Projekt

Auch ohne Seitenpaket sollten Sie die datenbankseitigen Teile Ihres Projekts in Paketen in Ihrem Repository ablegen:

| Teil | Paket | Wie |
|---|---|---|
| Ihre Klassen | `contentclass` | **Setup** > **Pakete** > **Neues Paket erstellen** > Export von Inhaltsklassen oder `ezpm.php` |
| Starterinhalte (Ordner, eine Startseite, Demo-Elemente) | `contentobject` | Export von Inhaltsobjekten |
| Ihre Erweiterung, für andere | `extension` | Export der Erweiterung oder Composer ([Pakete (.ezpkg)](../erweiterungsentwicklung/teil-7-veroeffentlichen/pakete-ezpkg.md)) |

`ezpm.php` baut Pakete in einer Shell, sodass derselbe Befehl für jedes Release läuft
([Pakete mit ezpm erstellen](../seitenpakete/eigene/pakete-mit-ezpm-erstellen.md)); der Assistent ist unter
[Der Paketassistent](../seitenpakete/admin-oberflaeche/der-paketassistent.md) beschrieben.

## Ein eigenes Seitenpaket

Ein eigenes Seitenpaket lohnt sich, wenn Sie dieselbe Art von Website viele Male installieren (der Starter einer
Agentur, ein Franchise, Schulungslabore) oder eine Installation exakt neu aufbauen müssen. Der Weg: ein vorhandenes
Seitenpaket forken, seine Klassen, Inhalte und sein Installer-Skript ändern, die Archive neu bauen, sie versionieren,
auf einem Paketserver veröffentlichen und Installationen darauf verweisen
([Ein eigenes Seitenpaket erstellen](../seitenpakete/eigene/README.md)). Die Anforderungen an die Dokumentation, die
ein veröffentlichtes Paket erfüllen muss, stehen in
[Die Checkliste der Anforderungen](../seitenpakete/anforderungen/die-checkliste-der-anforderungen.md), und ein Paket, das
anderen nützt, kann zum gemeinsamen Server beigetragen werden
([Ihr Seitenpaket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md)).

## Testen

Testen Sie ein Paket dort, wo nichts verloren gehen kann: ein eigener Checkout mit eigener SQLite-Datenbank, ein
Probelauf, eine vollständige Installation, ein Blick auf das Ergebnis, dann die Sandbox entfernen
([Testen in einer Sandbox](../seitenpakete/eigene/in-einer-sandbox-testen.md)). Führen Sie einen Installer nie gegen
eine Datenbank aus, die etwas enthält, das Ihnen wichtig ist; die Installation über die Konsole leert die Datenbank, die
sie erhält.

## Was Pakete nicht übertragen

- **Layouts.** Layouts und ihre Regeln werden mit `exp:layouts export` und `import` oder in den Datendateien einer
  Theme-Erweiterung übertragen ([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md#layouts-zwischen-installationen),
  [Layout-Daten und Share-DBA](../seitenpakete/sevenx-multisite/layoutdaten-und-share-dba-dateien.md)).
- **Rechnereinstellungen.** `settings/override/` wird je Rechner geschrieben.
- **Ihr Code.** Erweiterungen kommen aus Composer oder Git, nicht aus Inhaltspaketen.

## Verwandte Seiten

- [Pakete](../../anleitungen/pakete.md): die Anleitung für Administratoren
- [Glossar zu Seitenpaketen](../seitenpakete/glossar-der-seitenpakete.md)
- [Kurs zu Multisite und Paketen](../trainer/multisite-und-pakete/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
