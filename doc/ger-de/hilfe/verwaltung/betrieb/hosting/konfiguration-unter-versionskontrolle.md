---
title: Konfiguration unter Versionsverwaltung
audience: government, hosting, admin, developer
category: public-sector
order: 120
summary: Für IT-Teams im öffentlichen Sektor, die jede Änderung an einer Live-Website steuern und nachvollziehen müssen. Wie Sie Code und Einstellungen von Exponential in der Versionsverwaltung halten, die Administration mit ManagedByDeployment gegen Änderungen an Einstellungen sperren und Geheimnisse heraushalten.
keywords: Versionsverwaltung, Git, ManagedByDeployment, Konfigurationsmanagement, Änderungskontrolle, settings/override, Siteaccess-Einstellungen, composer.lock, Bereitstellung, Nachvollziehbarkeit, Audit, unveränderliche Konfiguration, version control, configuration management, change control, siteaccess settings, deployment, traceability, immutable configuration
type: help
---
# Konfiguration unter Versionsverwaltung

Diese Seite richtet sich an IT-Teams in öffentlichen Stellen, deren Änderungsprozess vorschreibt, dass jede Änderung
an einem Produktionssystem auf einen freigegebenen Antrag zurückführbar sein muss. Sie beantwortet, wie Sie Code und
Einstellungen einer Exponential-Website in der Versionsverwaltung halten, wie Sie verhindern, dass die Administration
an Ihrem Prozess vorbei Einstellungen auf dem Server ändert, und was aus dem Repository herausbleiben muss. Die
Funktionsreferenz ist [Durch die Bereitstellung verwaltete Konfiguration](../../../../funktionen/6.0/durch-deployment-verwaltet.md).

## Inhalt

1. [Was in die Versionsverwaltung gehört](#was-in-die-versionsverwaltung-gehört)
2. [Das Problem mit im Browser bearbeiteten Einstellungen](#das-problem-mit-im-browser-bearbeiteten-einstellungen)
3. [Mit ManagedByDeployment sperren](#mit-managedbydeployment-sperren)
4. [Geheimnisse](#geheimnisse)
5. [Nachvollziehbarkeit](#nachvollziehbarkeit)
6. [Checkliste](#checkliste)

## Was in die Versionsverwaltung gehört

| Pfad | Im Repository | Hinweis |
|---|---|---|
| `composer.json`, `composer.lock` | ja | `vendor/` in denselben Versionen neu aufbauen |
| Ihre eigenen Erweiterungen und Designs | ja | |
| `settings/siteaccess/` | ja | Siteaccess-Einstellungen |
| `settings/override/` | ja, ohne Geheimnisse | siehe [Geheimnisse](#geheimnisse) |
| Umgebungsvarianten wie `site.ini.test.append.php` | ja | Einstellungen pro Umgebung ([Umgebungen für Entwicklung, Test und Produktion](umgebungen-entwicklung-test-produktion.md)) |
| `config.env.php` | nein | benennt die Umgebung der Maschine; `.gitignore` hält sie heraus |
| `var/` | nein | Laufzeitdaten, separat gesichert |
| `vendor/` | nein | von Composer neu aufgebaut |

Wie Code mit Composer und Git auf einen Server gelangt, steht unter
[Composer und Git auf Servern](../../../hosting/grundlagen/composer-und-git-auf-servern.md).

## Das Problem mit im Browser bearbeiteten Einstellungen

Ohne Sperre schreiben der INI-Editor, die Editoren für Werkzeugleiste und Menüs und der Template-Editor der
Administration Dateien `*.ini.append.php` und `.tpl` in das Release-Verzeichnis. Die Versionsverwaltung sieht sie
nicht, und die nächste Bereitstellung verwirft sie
([Durch die Bereitstellung verwaltete Konfiguration](../../../../funktionen/6.0/durch-deployment-verwaltet.md)). Für eine
öffentliche Stelle ist das zudem ein Prüfungsbefund: eine Änderung an der Produktion, die den Änderungsprozess umgangen hat.

## Mit ManagedByDeployment sperren

Ein Schalter pro Siteaccess, standardmäßig ausgeschaltet, seit 6.0.15:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=enabled
```

Ist er eingeschaltet, gilt bei Web-Anfragen:

- die Ansichten, die Konfiguration schreiben, antworten wie eine deaktivierte Ansicht (standardmäßig mit 404):
  `settings/edit`, die Editoren für Werkzeugleiste und Menüs, der Template-Editor, der Einrichtungsassistent, die
  Aktivierung von Erweiterungen (`setup/extensions`), die Codegeneratoren sowie Hochladen, Installieren,
  Deinstallieren, Erstellen und Exportieren von Paketen;
- Menüs und Links zu diesen Ansichten werden ausgeblendet;
- `eZINI::save()` verweigert den Schreibvorgang, was auch schreibende Stellen stoppt, die die Liste nicht kennt.

Was offen bleibt: schreibgeschützte Ansichten (`settings/view`, `setup/cache`, `setup/info`, die Logs),
Kommandozeilenskripte und die Bereitstellung selbst, einschließlich `exp:ini` und `bin/php/*.php`, sowie
Schlüsselmaterial, das der Code bei der ersten Verwendung erzeugt (die Audit-Schlüssel, das Geheimnis für die
E-Mail-Präferenzen). Die Liste der gesperrten Ansichten steht in der Klasse, nicht in einer INI-Datei, sodass eine
Einstellungsdatei sie nicht abschwächen kann. Sie gilt gleichermaßen unter PHP-FPM, Apache, FrankenPHP und Velocity.

Grenzen: Die Installation eines Pakets über die Kommandozeile schreibt weiterhin in die Datenbank, und Dateirechte auf
dem Release-Verzeichnis bleiben die stärkere Maßnahme.

Legen Sie die Einstellung selbst in die Versionsverwaltung, niemals in eine Datei, die die Administration schreiben
könnte. Änderungen an Einstellungen laufen dann über Ihr Repository, Ihre Prüfung und Ihre Bereitstellung:

```bash
./console exp:ini get site.ini/ConfigurationSettings/ManagedByDeployment --allow-root-user
```

## Geheimnisse

Halten Sie Datenbankpasswörter und erzeugte Schlüssel aus dem Repository heraus oder legen Sie sie verschlüsselt darin ab:

| Geheimnis | Wo es liegt |
|---|---|
| Zugangsdaten der Datenbank | `site.ini [DatabaseSettings]` in `settings/override/` |
| Signatur- und Pseudonymschlüssel des Audits | `settings/override/audit.ini.append.php` |
| Geheimnis für die E-Mail-Präferenzen | `settings/override/mailpreferences.ini.append.php` |
| Zertifikatsschlüssel von Velocity | der Konfigurationsbaum von Velocity |

Ein verbreitetes Muster ist eine kleine, nicht versionierte Einstellungsdatei pro Umgebung für die Zugangsdaten, die
aus Ihrem Speicher für Geheimnisse bereitgestellt wird. Sichern Sie diese Dateien separat
([Schlüssel und Geheimnisse in Datensicherungen](../datensicherung-und-wiederherstellung/schluessel-und-geheimnisse-in-datensicherungen.md)).

## Nachvollziehbarkeit

Mit Code und Einstellungen in der Versionsverwaltung sagt die Historie des Repositorys, was sich wann und in welchem
geprüften Commit geändert hat. Exponential steuert seinen Teil bei: Das Prüfprotokoll zeichnet Schreibvorgänge an
Einstellungen, das Leeren von Caches, Bereitstellungen und Paketinstallationen im Kanal `system` auf
([Prüfprotokoll](../../../../funktionen/6.0/pruefprotokoll.md)). Zusammen erlauben sie Ihnen, die Frage einer Prüferin oder
eines Prüfers zu beantworten: „Wer hat diese Einstellung geändert, und war das freigegeben?“ Der Änderungsprozess
selbst ist unter [Änderungsmanagement und Änderungsbeiräte](../aktualisierungen-und-aenderungen/aenderungsmanagement-und-aenderungsbeiraete.md)
beschrieben.

## Checkliste

- [ ] Code, Erweiterungen, Designs und Einstellungen liegen in der Versionsverwaltung; `var/`, `vendor/` und `config.env.php` nicht.
- [ ] Geheimnisse bleiben aus dem Repository heraus oder liegen verschlüsselt darin.
- [ ] `ManagedByDeployment=enabled` in jedem Administrations- und Redaktions-Siteaccess der Produktion.
- [ ] Das Release-Verzeichnis ist für den Benutzer des Webservers nicht beschreibbar, wo nichts hineinschreiben muss.
- [ ] Bereitstellungen laufen aus dem Repository, nicht durch Handbearbeitung auf dem Server.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Durch die Bereitstellung verwaltet](../aktualisierungen-und-aenderungen/verwaltet-durch-bereitstellung.md) in den Seiten zu Änderungen
- [Änderungen bereitstellen](../../../hosting/betrieb/aenderungen-ausrollen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
