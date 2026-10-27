---
title: Von der Bereitstellung verwaltete Konfiguration
audience: government, admin, developer, hosting
category: public-sector
order: 230
summary: Für öffentliche Stellen mit Änderungsmanagement - wie der Schalter ManagedByDeployment verhindert, dass die Administration im Produktivbetrieb Einstellungen, Templates, Erweiterungen und Pakete ändert, sodass jede Änderung über Versionsverwaltung und Review läuft.
keywords: Konfigurationsmanagement, Änderungsmanagement, Versionsverwaltung, Bereitstellung, gesperrte Ansichten, INI-Editor, Template-Editor, Einrichtungsassistent, Pakete, Funktionstrennung, Produktivbetrieb, öffentlicher Sektor, ManagedByDeployment, configuration management, change management, version control, deployment, locked views, INI editor, template editor, setup wizard, packages, separation of duties, production, public sector
type: help
---
# Von der Bereitstellung verwaltete Konfiguration

Diese Seite richtet sich an Administratorinnen und Administratoren, Entwicklerinnen und Entwickler und Hosting-Teams
öffentlicher Stellen, deren Änderungsprozess verlangt, dass Änderungen im Produktivbetrieb über Versionsverwaltung,
Review und eine Bereitstellung laufen. Ohne den Schalter schreiben der INI-Editor der Administration, die Editoren für
Werkzeugleisten und Menüs und der Template-Editor Dateien in das Release-Verzeichnis, wo die Versionsverwaltung sie
nicht sieht. Mit dem Schalter können sie das nicht
([Von der Bereitstellung verwaltete Konfiguration](../../../funktionen/6.0/durch-deployment-verwaltet.md)).

## Inhalt

1. [Der Schalter](#der-schalter)
2. [Was er sperrt](#was-er-sperrt)
3. [Was offen bleibt](#was-offen-bleibt)
4. [Einführen](#einführen)
5. [Grenzen](#grenzen)
6. [Verwandte Seiten](#verwandte-seiten)

## Der Schalter

Standardmäßig aus, je Siteaccess, seit 6.0.15:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php (from version control)
[ConfigurationSettings]
ManagedByDeployment=enabled
```

## Was er sperrt

In einer Webanfrage, bei eingeschaltetem Schalter:

- der Einstellungseditor, die Editoren für Werkzeugleisten und Menüs, der Template-Editor, der Einrichtungsassistent, die
  Aktivierung von Erweiterungen, die Codegeneratoren (RAD-Assistenten) sowie Hochladen, Installieren, Deinstallieren,
  Anlegen und Exportieren von Paketen antworten wie eine deaktivierte Ansicht, gleich, was die Rollen sagen; die Liste
  steht im Code, sodass eine Einstellungsdatei sie nicht abschwächen kann;
- Menüs und Links zu diesen Ansichten werden ausgeblendet;
- jeder INI-Schreibvorgang aus einer Webanfrage wird abgewiesen (`eZINI::save()` gibt false zurück), auch Schreibvorgänge
  aus Erweiterungen;
- der Einrichtungsassistent startet nicht, gleich, was `CheckValidity` sagt.

## Was offen bleibt

- Ansichten zum Lesen: Einstellungsansicht, Template- und Paketlisten, Cache-Verwaltung, Systeminformationen,
  Protokolle.
- Kommandozeilenskripte und die Bereitstellung selbst, einschließlich `./console exp:ini`.
- Schlüssel, die der Code bei der ersten Verwendung erzeugt (Audit-Schlüssel, das Geheimnis für die
  E-Mail-Einstellungen).

## Einführen

1. Übernehmen Sie jede Einstellung, die die Administration bisher geschrieben hat, in die Versionsverwaltung
   (`settings/siteaccess/...`, Einstellungen der Erweiterungen); Geheimnisse bleiben in `settings/override` außerhalb
   der Versionsverwaltung.
2. Schalten Sie den Schalter für den Administrations-Siteaccess der Produktion in einer versionierten Datei ein.
3. Stellen Sie bereit und prüfen Sie dann: Setup > INI-Einstellungen zeigt Werte, aber keine Bearbeitung; `/setup/rad`
   antwortet als deaktiviert.
4. Teilen Sie den Administratorinnen und Administratoren mit, dass Änderungen an Einstellungen jetzt Änderungsanträge
   sind.

## Grenzen

- Die Installation eines Pakets von der Kommandozeile schreibt weiterhin in die Datenbank.
- Die Dateirechte am Release-Verzeichnis bleiben die stärkere Maßnahme (Code für den Benutzer des Webservers
  schreibgeschützt); der Schalter ist ein Schutz auf Anwendungsebene.

## Verwandte Seiten

- [Von der Bereitstellung verwaltete Konfiguration](../../../funktionen/6.0/durch-deployment-verwaltet.md)
- [Bereitstellen](../../../anleitungen/bereitstellung.md)
- [Dateirechte und Eigentümer](dateirechte-und-eigentuemer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
