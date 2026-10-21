---
title: Durch die Bereitstellung verwaltete Konfiguration
audience: government, admin, developer, hosting
category: public-sector
order: 60
summary: Wie der Schalter ManagedByDeployment die Bildschirme für Einstellungen, Templates und Pakete eines Live-Siteaccess sperrt, sodass jede Konfigurationsänderung die Versionsverwaltung und Ihren Änderungsbeirat durchläuft, was offen bleibt und wie Sie ihn einschalten.
keywords: ManagedByDeployment, Konfigurationsmanagement, Änderungskontrolle, Versionsverwaltung, gesperrte Einstellungen, INI-Editor, Template-Editor, Pakete, Bereitstellung, Audit, unveränderliche Konfiguration, configuration management, change control, version control, locked settings, INI editor, template editor, packages, deployment, immutable configuration
type: help
---
# Durch die Bereitstellung verwaltete Konfiguration

Diese Seite richtet sich an Administratoren und Change-Manager, die wollen, dass jede Konfigurationsänderung an einer
öffentlichen Website über die Versionsverwaltung und einen Änderungsbeirat läuft, nicht über einen Browser auf dem
Live-Server. Exponential hat dafür einen Schalter, `[ConfigurationSettings] ManagedByDeployment`, beschrieben unter
[Durch die Bereitstellung verwaltete Konfiguration](../../../../../eng-us/features/6.0/managed-by-deployment.md).

## Inhalt
1. [Warum öffentliche Stellen ihn wollen](#warum-öffentliche-stellen-ihn-wollen)
2. [Was der Schalter sperrt](#was-der-schalter-sperrt)
3. [Was offen bleibt](#was-offen-bleibt)
4. [Einschalten](#einschalten)
5. [Damit arbeiten](#damit-arbeiten)
6. [Grenzen](#grenzen)
7. [Verwandte Seiten](#verwandte-seiten)

## Warum öffentliche Stellen ihn wollen

Ohne den Schalter schreiben der INI-Editor, die Editoren für Werkzeugleiste und Menüs und der Template-Editor Dateien
`*.ini.append.php` und `.tpl` in das Release-Verzeichnis, wo die Versionsverwaltung sie nicht sieht und die nächste
Bereitstellung sie verwirft. Eine so vorgenommene Änderung hat kein Ticket, keine Prüfung und keine Historie außer dem
Audit-Trail. Mit eingeschaltetem Schalter kann die Live-Website nur ausführen, was bereitgestellt wurde, und genau das
gibt ein Änderungsbeirat frei.

## Was der Schalter sperrt

Bei einer Web-Anfrage mit `ManagedByDeployment=enabled` für den Siteaccess gilt:

- **Ansichten** antworten wie eine ausgeschaltete Ansicht (standardmäßig mit 404), was auch immer die Rollen sagen:
  `settings/edit`, die Werkzeugleiste für Einstellungen, die Editoren für Werkzeugleiste und Menüs, der
  Template-Editor, der Einrichtungsassistent, die Aktivierung von Erweiterungen (`setup/extensions`), die
  Codegeneratoren (`setup/rad` und seine Assistenten) sowie Hochladen, Installieren, Deinstallieren, Erstellen und
  Exportieren von Paketen. Die Liste steht in der Klasse `expManagedConfiguration`, sodass eine Einstellungsdatei sie
  nicht abschwächen kann.
- **Menüs und Links** zu diesen Ansichten werden ausgeblendet.
- **Schreibende Stellen** verweigern: `eZINI::save()` gibt false zurück und `expIniEditor::save()` wirft eine
  Ablehnung, was auch schreibende Stellen stoppt, die die Liste nicht nennt (Module von Erweiterungen, die
  Einstellungen der Debug-Leiste, die Einstellungsseiten für Audit und Absender von E-Mails).
- Der **Einrichtungsassistent** startet nicht.
- **Pakete**: Vergleichen funktioniert weiterhin, Importieren und Entfernen werden nicht angeboten.

PHP-FPM, Apache, FrankenPHP, der eingebaute Server und die Worker von Velocity verweigern alle auf dieselbe Weise.

## Was offen bleibt

- Schreibgeschützte Ansichten: `settings/view` (**Setup > INI-Einstellungen**), die Listen der Templates und Pakete,
  **Setup > Cache-Verwaltung**, **Setup > Systeminformationen**, die Logs.
- Kommandozeilenskripte und die Bereitstellung selbst, einschließlich `./console exp:ini` und `bin/php/*.php`.
- Schlüsselmaterial, das der Code bei der ersten Verwendung erzeugt (Audit-Schlüssel, das Geheimnis für die
  E-Mail-Präferenzen): Laufzeitzustand, wird weiterhin geschrieben.

## Einschalten

Der Schalter gilt pro Siteaccess und wird nie aus der Administration geschrieben:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=enabled
```

Legen Sie ihn mit den übrigen Einstellungen in die Versionsverwaltung, stellen Sie bereit und leeren Sie den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`). Prüfung: **Setup > INI-Einstellungen** zeigt die
Einstellungen, bietet aber keine Bearbeitung an; `/settings/edit` antwortet wie eine deaktivierte Ansicht.

## Damit arbeiten

| Änderung | Weg |
|---|---|
| Eine Einstellung | die Datei im Repository bearbeiten, prüfen, freigeben, bereitstellen; `./console exp:ini` auf einer Testkopie, um Werte auszuprobieren |
| Ein Template | dasselbe; der Template-Editor bleibt auf Entwicklungs- und Test-Siteaccesses verfügbar, wo der Schalter ausgeschaltet ist |
| Eine Erweiterung | `composer.json` und Einstellungen im Repository; Aktivierung durch die Bereitstellung |
| Inhalte, Rollen, Workflows | unverändert: Sie sind Daten, werden in der Administration bearbeitet und im Audit-Trail aufgezeichnet |

Die Einstellung kann sich mit Variantendateien für `EXP_ENV` je Umgebung unterscheiden, sodass Test und Entwicklung
bearbeitbar bleiben ([Umgebungen: Entwicklung, Test und Produktion](../hosting/umgebungen-entwicklung-test-produktion.md)).

## Grenzen

- Die Installation eines Pakets über die Kommandozeile schreibt weiterhin in die Datenbank, und Pakete können Inhalte
  mitbringen.
- Dateirechte auf dem Release-Verzeichnis bleiben die stärkere Maßnahme; der Schalter ist ein Schutz auf
  Anwendungsebene.
- Rollen, Workflows, Sektionen und Zustände sind Konfiguration in der Datenbank: Sie werden nicht gesperrt. Steuern
  Sie sie über Rollen (wer sie ändern darf) und den Audit-Trail.

## Verwandte Seiten
- [Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md)
- [Staging und Freigabe](staging-und-freigabe.md)
- [Konfiguration unter Versionsverwaltung](../hosting/konfiguration-unter-versionskontrolle.md)
- [Konfiguration mit ManagedByDeployment sperren](../../../../../eng-us/help/government/tasks/operations/lock-configuration-with-managed-by-deployment.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
