---
title: Unveränderliche Releases und Versionen
audience: government, admin, project-manager, developer
category: public-sector
order: 30
summary: Warum sich eine veröffentlichte Exponential-Version nach der Veröffentlichung nie ändert, wie Versionen nummeriert und verglichen werden, wie Sie die laufende Version festlegen und prüfen und was das für Beschaffungsunterlagen, Prüfungen und reproduzierbare Bereitstellungen bedeutet.
keywords: unveränderliches Release, dauerhafte Tags, Versionsnummer, Versionierung, Composer, Packagist, Version festlegen, reproduzierbar, Softwareinventar, SBOM, Beschaffungsunterlagen, Prüfung, composer.lock, immutable release, permanent tags, version number, versioning, pin version, reproducible, software inventory, procurement record, audit
type: help
---
# Unveränderliche Releases und Versionen

Diese Seite richtet sich an Administratoren, Projektleitungen und Beschäftigte in Beschaffung oder Prüfung, die genau
wissen müssen, welche Software eine öffentliche Website betreibt, und sicher sein wollen, dass es derselbe Code ist,
den alle anderen unter dieser Version kennen. Die Richtlinie steht im
[Standard für Versionsverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#releases).

![Die Seite „Über“ zeigt das Release, das die Installation betreibt](../../../../../eng-us/help/images/government/gov-about.webp)

## Inhalt
1. [Die Regel](#die-regel)
2. [Wie Versionen nummeriert werden](#wie-versionen-nummeriert-werden)
3. [Wissen und festlegen, was Sie betreiben](#wissen-und-festlegen-was-sie-betreiben)
4. [Was das für öffentliche Stellen bedeutet](#was-das-für-öffentliche-stellen-bedeutet)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Regel

„Ein Tag und das daraus erstellte GitHub-Release sind unveränderlich, sobald sie gepusht wurden.“ Composer und
Packagist lesen ein Tag einmal und speichern es zwischen; ein verschobenes Tag ergäbe zwei verschiedene Pakete unter
einer Versionsnummer. Daher gilt:

- ein gepushtes Tag wird niemals gelöscht, verschoben oder neu erstellt, und die Dateien eines veröffentlichten
  Releases werden niemals ersetzt;
- auf ein fehlerhaftes Release folgt die nächste Version, deren Hinweise sagen, was an der vorherigen falsch war.

Dasselbe gilt für Erweiterungen, die das Projekt veröffentlicht; sie tragen ihre Version passend zum Release in
`ezinfo.php` und `extension.xml`.

## Wie Versionen nummeriert werden

- Die Kernlinie ist 6.0.x; die veröffentlichten Tags reichen von `v6.0.0` bis `v6.0.14`, und 6.0.15 ist auf `main` in
  Entwicklung (`6.0.x-dev`) ([Standard, „Releases“](../../../../../eng-us/standards/source-control-and-releases.md#releases)).
- Die letzte Stelle zählt weiter: Nach `.9` kommt `.10`. Composer und Packagist vergleichen jede Stelle als Zahl. Eine
  einfache alphabetische Sortierung macht das falsch; verwenden Sie in Skripten `--sort=version:refname` oder `sort -V`.
- Die `composer.json` im Wurzelverzeichnis hat kein Feld `version`; Composer übernimmt die Version aus dem Tag.

## Wissen und festlegen, was Sie betreiben

| Frage | Wo |
|---|---|
| Welche Version von Exponential? | **Setup > Systeminformationen**; die Zeile `ezpublish-version` in `ezsite_data` |
| Welche Versionen der Erweiterungen? | **Setup > Extensions** (jede Karte), **Setup > Updates und Pakete**, Installierte Pakete |
| Welche Pakete genau? | `composer.lock` in der Installation; `composer show se7enxweb/exponential` |
| Hat jemand den Code geändert? | **Setup > Upgrade-Prüfung**, **Überprüfe Dateikonsistenz**; `php bin/php/checkmanifest.php --all` |

Halten Sie `composer.lock` in Ihrer Versionsverwaltung: Sie legt jedes Paket auf eine exakte Version und einen exakten
Commit fest, sodass Staging und Produktion denselben Code installieren.

## Was das für öffentliche Stellen bedeutet

- **Beschaffung und Inventar**: Eine Versionsnummer in einem Vertrag oder einem Softwareinventar benennt genau einen
  festen Satz von Dateien. Halten Sie die Version und das Datum der Bereitstellung fest.
- **Prüfung**: Die Prüfung der Dateikonsistenz vergleicht Ihre Dateien mit dem Manifest des Releases
  (`share/filelist.md5`), sodass Sie zeigen können, dass der Code unverändert ist, oder auflisten können, was
  absichtlich geändert wurde.
- **Sicherheitskorrekturen** kommen als neue Versionen; planen Sie regelmäßige kleine Aktualisierungen statt Patches
  für alte Versionen ([Sicherheitsupdates und Offenlegung](sicherheitsaktualisierungen-und-offenlegung.md)).
- **Signierte Tags**: Der Standard hält fest, dass Commits und Tags überwiegend nicht signiert sind („Nicht erfüllt“);
  verlangt Ihre Richtlinie eine Prüfung von Signaturen, vermerken Sie diese Lücke und stützen Sie sich stattdessen auf
  Prüfsummen und das Manifest.

## Verwandte Seiten
- [Versionshinweise lesen](versionshinweise-lesen.md)
- [Upgrade-Prüfung vorher und nachher](upgrade-pruefung-vorher-und-nachher.md)
- [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md)
- [Lizenzierung für Hoster](../../../hosting/geschaeft/lizenzierung-fuer-hoster.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
