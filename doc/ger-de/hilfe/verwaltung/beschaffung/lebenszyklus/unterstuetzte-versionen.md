---
title: Unterstützte Versionen
audience: government, project-manager, admin, hosting
category: public-sector
order: 30
summary: Welche Versionen von Exponential laut SECURITY.md Sicherheitskorrekturen erhalten, was „unterstützt“ für eine öffentliche Stelle bedeutet, was mit älteren Linien geschieht und wie Sie in einem Vertrag eine unterstützte Version verlangen.
keywords: unterstützte Versionen, Sicherheitsunterstützung, Ende der Unterstützung, 6.0.x, 5.x, 4.x, SECURITY.md, gepflegte Linie, Upgrade, Vertrag, öffentlicher Sektor, supported versions, security support, end of life, maintained line, upgrade, contract, public sector
type: help
---
# Unterstützte Versionen

Diese Seite richtet sich an Projektleitungen, Vertragsmanagement und Sicherheitsbeauftragte einer öffentlichen Stelle.
Sie beantwortet: Welche Versionen von Exponential erhalten Sicherheitskorrekturen, was „unterstützt“ in der Praxis
bedeutet und wie Sie eine Website für die Laufzeit eines Vertrags auf einer unterstützten Version halten. Die Quelle ist
[`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) im Wurzelverzeichnis des Repositorys.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [Was „unterstützt“ bedeutet](#was-unterstützt-bedeutet)
3. [Ältere Linien](#ältere-linien)
4. [Erweiterungen](#erweiterungen)
5. [In einem Vertrag](#in-einem-vertrag)

## Die Tabelle

Aus `SECURITY.md`:

| Version | Unterstützt |
|---|---|
| 6.0.x, aktuelle Linie: `main` und der nächste davon erstellte Tag | ja |
| Veröffentlichte Tags 6.0.x (`v6.0.0` bis `v6.0.14`) | korrigiert durch Upgrade auf das neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | nein |

„Exponential 6.0 ist die gepflegte Linie. Sicherheitskorrekturen werden auf dem Zweig `main` vorgenommen (die Linie
6.0.15, `dev-main`) und mit dem nächsten Release 6.0.x veröffentlicht.“

## Was „unterstützt“ bedeutet

- **Eine Korrektur ist ein neues Release.** Es gibt keine zurückportierten Patches für ältere Tags 6.0.x. Um eine
  Korrektur zu erhalten, wechselt die Website auf das neueste 6.0.x. Planen Sie dafür: Jede Website muss innerhalb der
  Linie 6.0 ohne eigenes Projekt aktualisiert werden können.
- **Meldungen werden beantwortet.** Eine vertrauliche Meldung an die Adresse in `SECURITY.md` erhält „eine erste Antwort
  innerhalb eines Werktags“. `SECURITY.md` verspricht keine Frist für die Korrektur.
- **Es werden keine End-of-Life-Daten veröffentlicht.** `SECURITY.md` sagt, welche Linien heute unterstützt werden; es
  sagt nicht, wie lange. Fragen Sie Ihren Lieferanten, wie er Sie auf dem Laufenden hält.

Die weiteren Unterstützungszusagen des Projekts (PHP-Versionen, Releases, Dokumentation) sind in
[Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) zusammengefasst.

## Ältere Linien

Eine Website auf 5.x, 4.x oder älter erhält keine Korrekturen. Die Migrationskapitel des Installationsbuchs bringen
solche Websites auf 6.0: [14. Migration von der Linie 4.x](../../../../installation/14-migration-von-4x.md),
[15. Migration vom Legacy-Stack 5.x](../../../../installation/15-migration-von-5x-legacy.md) und
[16. Migration von eZ Platform und Ibexa](../../../../installation/16-migration-von-ez-platform-und-ibexa.md). Websites
jener Zeit liefen auf der Codebasis von eZ Publish, die ihr Hersteller nach fast achtzehn Jahren eingestellt hat;
Exponential führt diese Codebasis fort ([Wer ist 7x?](../../../wer-ist-7x.md)).

Eine Website, die auf PHP 7.4 bleiben muss, kann höchstens 6.0.7 betreiben, das nicht mehr die gepflegte Linie ist
([2. Anforderungen](../../../../../eng-us/install/02-requirements.md#versions)).

## Erweiterungen

Erweiterungen sind eigene Pakete mit eigenen Versionen. Jede hält ihre Version in `ezinfo.php` und `extension.xml` fest
([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#extension-releases)), und die
About-Seite listet sie auf. `SECURITY.md` deckt das Repository des Kernels ab; prüfen Sie für jede Erweiterung, die Ihre
Website verwendet, ihr eigenes Repository und ihr Changelog in `doc/changelogs/extensions/`. Erweiterungen, die ein
Lieferant für Sie geschrieben hat, werden nur unterstützt, wie es der Vertrag sagt.

## In einem Vertrag

```text
Supported versions
1. The Supplier keeps the Site on the newest released 6.0.x version of Exponential, or on a version
   no older than <n> releases behind it, as listed in SECURITY.md of the Exponential repository.
2. When a release fixes a security issue, the Supplier upgrades the Site within <time> of the release
   (see the security update clause).
3. The Supplier reports the kernel version and every extension version in each <period> report,
   as shown by Setup > System information and the about page.
4. If the Site uses a line that SECURITY.md no longer lists as supported, the Supplier proposes a
   migration plan within <time>.
```

Tragen Sie die Fristen selbst ein; nichts im Projekt legt sie fest. Der Teil zu Ausschreibungsvorlagen dieses Abschnitts
zur Beschaffung hat ausführlichere Klauseln.

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Release-Richtlinie und Versionsnummern](release-richtlinie-und-versionsnummern.md)
- [Sicherheitsupdates und Patch-Rhythmus](sicherheitsupdates-und-patch-rhythmus.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
