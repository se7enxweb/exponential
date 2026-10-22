---
title: Starter-Kits für Agenturen
audience: bundler, developer, project-manager
category: bundling
order: 320
summary: Wie eine Agentur Exponential, ihre Erweiterungen, ein Theme und ein Seitenpaket zu einem Starter-Kit für neue Projekte bündelt - der Aufbau des Repositorys, Composer, eine geprüfte kickstart.ini je Umgebung, ein eigenes Seitenpaket, ein zweites Erweiterungsverzeichnis, ManagedByDeployment, die Übergabe an den Kunden und wie Sie Kits aktuell halten.
keywords: Starter-Kit, Agentur, Projektvorlage, Repository, Seitenpaket, Theme, Übergabe, starter kit, agency, project template, repository, Composer, kickstart.ini, site package, theme, extension_src, AdditionalExtensionDirectories, ManagedByDeployment, handover, bundle
type: help
---
# Starter-Kits für Agenturen

Diese Seite richtet sich an Agenturen, die jedes Kundenprojekt von derselben Basis aus beginnen: Exponential plus
eigene Erweiterungen, ein Theme und ein Seitenpaket. Ein Starter-Kit ist ein Bundle, dessen Empfänger zuerst Ihre
eigenen Projektteams sind und dann Ihre Kunden.

## Inhalt

1. [Was in ein Kit gehört](#was-in-ein-kit-gehört)
2. [Aufbau des Repositorys](#aufbau-des-repositorys)
3. [Ein neues Projekt aus dem Kit installieren](#ein-neues-projekt-aus-dem-kit-installieren)
4. [Disziplin bei der Bereitstellung](#disziplin-bei-der-bereitstellung)
5. [Ein Projekt an den Kunden übergeben](#ein-projekt-an-den-kunden-übergeben)
6. [Das Kit aktuell halten](#das-kit-aktuell-halten)

## Was in ein Kit gehört

| Teil | Form | Seite |
|---|---|---|
| Exponential in einer festgelegten Version | `composer.json` + `composer.lock` | [Bundles auf Composer-Basis](bundles-auf-composer-basis.md) |
| Ihre Erweiterungen | Composer-Pakete aus Ihren Repositorys oder ein zweites Erweiterungsverzeichnis | [Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md) |
| Ihr Theme | eine Erweiterung mit einem Design | [Ein Theme veröffentlichen](../partner/ein-theme-veroeffentlichen.md) |
| Ihr Seitenpaket | eine `.ezpkg` mit Ihren Klassen, Basisinhalten, Rollen und Einstellungen | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| Installationskonfiguration | eine `kickstart.ini`-Vorlage je Umgebung | [kickstart.ini-Vorlagen](kickstart-ini-vorlagen.md) |
| Einstellungen für Webserver und Velocity | Beispieldateien | [Webserver- und Engine-Wahl](webserver-und-engine-wahl.md) |
| Ihre Dokumentation | erste Schritte, Konventionen, Übergabe | [Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md) |

## Aufbau des Repositorys

```text
starter-kit/
    composer.json, composer.lock
    extension_src/            your own extensions (AdditionalExtensionDirectories[])
    packages/                 your site package source and the built .ezpkg
    deploy/
        kickstart.ini.template
        velocity.ini.append.php.example
    docs/
    README.md, LICENSE, NOTICE.txt
```

`extension_src/` folgt dem Aufbau, den
[Zusätzliche Erweiterungsverzeichnisse](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md#eine-erweiterung-in-ein-zweites-wurzelverzeichnis-verschieben)
empfiehlt: "Erweiterungen Dritter und der Community" in `extension/`, "Projekt- und Kundenerweiterungen" in
`extension_src/`, wobei `site.ini [ExtensionSettings] AdditionalExtensionDirectories[]` das zweite Verzeichnis nennt.

## Ein neues Projekt aus dem Kit installieren

```bash
git clone https://git.example.com/agency/starter-kit.git customer-site
cd customer-site
composer install
for f in packages/*.ezpkg; do php ezpm.php import "$f"; done
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

Übergeben Sie dann das erzeugte Passwort aus `var/log/initial-admin-password` an die Projektleitung und löschen Sie
die Datei ([Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)).

## Disziplin bei der Bereitstellung

- Halten Sie `settings/override/` und `settings/siteaccess/` aus der Versionskontrolle heraus: Sie enthalten
  Zugangsdaten ([4.4](../../installation/04-installationsweg-waehlen.md#einstellungen)).
- Aus der Versionskontrolle bereitgestellte Websites "können ihre Einstellungen mit `[ConfigurationSettings]
  ManagedByDeployment=enabled` sperren, das die Administration daran hindert, auf dem Server Einstellungen und
  Templates zu schreiben" (ausgeliefert `disabled`; [ManagedByDeployment](../../funktionen/6.0/durch-deployment-verwaltet.md)).
- Verwenden Sie `config.env.php` mit `EXP_ENV`, um Einstellungsdateien je Umgebung zu führen
  (`site.ini.test.append.php` neben `site.ini.append.php`); kopieren Sie `config.env.php.example` und halten Sie die
  Kopie aus der Versionskontrolle heraus.
- Rollen Sie Codeänderungen in der dokumentierten Reihenfolge aus
  ([Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md)).

## Ein Projekt an den Kunden übergeben

- [ ] Der Kunde hat eigene Administratorkonten; `admin` wird nicht mehr zur Anmeldung verwendet
      ([Kontenhygiene](../standard-administratorpasswort.md#kontenhygiene)).
- [ ] Der Kunde weiß, welche Teile Exponential (GPL) sind, welche Ihre Erweiterungen und unter welcher Lizenz diese
      geliefert werden ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- [ ] Der Kunde hat den Quellcode dessen, was Sie geliefert haben, einschließlich Ihrer Erweiterungen, wenn Sie diese
      weitergegeben haben.
- [ ] Zuständigkeiten für Support und Updates sind schriftlich festgehalten ([Grenzen des Supports](grenzen-des-supports.md)).
- [ ] Die Redakteure haben das Übergabematerial
      ([Die Website an die Redakteure übergeben](../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)).

## Das Kit aktuell halten

- Bauen Sie die Lock-Datei des Kits bei jedem Exponential-Release neu, das Sie übernehmen; testen Sie; taggen Sie das
  Kit ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).
- Projekte, die aus einem älteren Kit gestartet sind, werden wie jede Installation aktualisiert
  ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
- Allgemeine Korrekturen gehen an das Projekt, damit das Kit nicht ewig Patches mitschleppt
  ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)).

## Verwandte Seiten

- [Implementierungspartner](../partner/implementierungspartner.md), [Leitfaden für Implementierungsprojekte](../partner/leitfaden-fuer-implementierungsprojekte.md)
- [Ein eigenes Seitenpaket erstellen](../seitenpakete/eigene/README.md)
- [Bundles auf Composer-Basis](bundles-auf-composer-basis.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
