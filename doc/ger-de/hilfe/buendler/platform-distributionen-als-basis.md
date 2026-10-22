---
title: Platform-Distributionen als Basis eines Bundles
audience: bundler, developer
category: bundling
order: 50
summary: Was Bündler wissen müssen, wenn sie auf den Distributionen Exponential Platform Legacy oder Nexus aufbauen - welche Linien Exponential 6 enthalten, wie sie installiert werden, die Versionsfallen auf Packagist, das Standardkonto in den Demodaten und wie Sie ein Bundle auf Distributionsbasis aktuell halten.
keywords: Legacy, Nexus, Exponential Platform, Distribution, Legacy-Bridge, Symfony, Versionen, Demokonto, legacy bridge, exponential:install, exponential-media, Packagist, versions, create-project, demo account
type: help
---
# Platform-Distributionen als Basis eines Bundles

Diese Seite richtet sich an Bündler, die auf einer Symfony-basierten Distribution von Exponential Platform aufbauen,
die Exponential 6 enthält: **Legacy** oder **Nexus**. Sie fasst zusammen, was die Dokumentation über sie sagt, und
verweist für alles andere auf die READMEs der Repositorys. Wenn Sie auf dem reinen Exponential 6 aufbauen, lesen Sie
stattdessen [Exponential 6 als Basis](exponential-6-als-basis.md).

## Inhalt

1. [Legacy und Nexus in Kürze](#legacy-und-nexus-in-kürze)
2. [Welche Nexus-Linie Exponential 6 enthält](#welche-nexus-linie-exponential-6-enthält)
3. [Versionsfallen](#versionsfallen)
4. [Ein Bundle auf Nexus-Basis installieren](#ein-bundle-auf-nexus-basis-installieren)
5. [Das Demokonto](#das-demokonto)
6. [Aktuell halten](#aktuell-halten)

## Legacy und Nexus in Kürze

| | Legacy | Nexus |
|---|---|---|
| Was | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6": ein Symfony-CMS mit zwei Kerneln, das eine vollständige Kopie von Exponential 6 enthält | ein fertiges Website-Projekt mit Demo-Inhalten, Netgen Layouts, einem Twig-Frontend, einer Administration, REST und GraphQL |
| Erfordert | `se7enxweb/exponential` und `se7enxweb/legacy-bridge` | je Linie; siehe unten |
| Für | bestehende Exponential-Websites, die Symfony daneben wollen | Agenturen und Teams, die schnell eine vollständige Website wollen |
| Quelle | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-legacy) | [Exponential Platform Nexus](../../funktionen/6.0/plattform-nexus-starter.md) |

## Welche Nexus-Linie Exponential 6 enthält

Aus der `composer.json` und der `composer.lock` jedes Branches und Tags
([Nexus](../../funktionen/6.0/plattform-nexus-starter.md#was-es-ist)):

| Linie | Läuft auf | Legacy-Kernel |
|---|---|---|
| 1.0.0.x | Stack von eZ Platform 2.5, Symfony 3.4, PHP 7.1.3 bis 8.5 | ja |
| 1.1.0.x | Generation 3.3, Symfony 5.4, PHP `^8.0` | ja |
| 1.2.0.x | Generation 4.6, Symfony 5.4, PHP 8.2 oder neuer | ja |
| 1.3.0.x und der v5-Starter | Platform v5, Symfony 7.4, PHP 8.4 oder neuer | **nein** |
| 2.5 (`v2.5.0.0` bis `v2.5.0.6`) | die Legacy-2.5-Distribution, als Nexus paketiert | ja |

Ein Bundle, das "Exponential 6 inklusive" verspricht, darf nicht auf 1.3.0.x oder dem v5-Starter gebaut werden.

## Versionsfallen

- **Fünfteilige Tags gibt es nicht auf Packagist.** `v1.0.0.0.1` bis `v1.0.0.0.3` und `1.3.0.0.0` "existieren nur
  als Git-Tags".
- **Nennen Sie immer eine Version.** `composer create-project se7enxweb/exponential-platform-nexus` ohne Version
  wählt `3.1.6`, die Upstream-Media-Site, "kein Nexus-Release". Der Standard-Branch, `master`, ist die Linie 2.5.
- **Legen Sie in Ihrem Bundle einen Tag fest**, nie einen Branch. Es gilt dieselbe Regel wie für den Kernel: Ein
  veröffentlichtes Release ist dauerhaft ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md)).

## Ein Bundle auf Nexus-Basis installieren

Die dokumentierten Befehle für den v5-Starter enden mit `php bin/console exponential:install exponential-media
--no-interaction`; die Branches 1.1.0.x und 1.2.0.x "installieren auf dieselbe Weise (`exponential:install
exponential-media`) auf ihrer eigenen Generation". `php bin/console help exponential:install` zeigt die
Installationstypen Ihres Checkouts. Die Linie 1.0.0.x installiert aus SQL-Dumps und braucht zusätzliche symbolische
Links; die Schritte stehen unter
[Nexus 1.0.0.x installieren](../../funktionen/6.0/plattform-nexus-starter.md#nexus-100x-installieren-die-linie-25).

Führen Sie diese Schritte für ein Bundle zur Build-Zeit oder beim ersten Start genau so aus, wie die README Ihrer
Linie sie angibt, und behandeln Sie das Ergebnis dann wie jedes andere Bundle: Rechte, Caches, das Demokonto, die
Checkliste für die Veröffentlichung.

## Das Demokonto

"Die öffentlichen Installationsanleitungen in den Repositorys enthalten ein Standard-Administratorkonto für die
Demodaten. Betrachten Sie es als öffentlich bekannt und ändern Sie es"
([Nexus, Grenzen](../../funktionen/6.0/plattform-nexus-starter.md#grenzen)). Ein Bundle darf dieses Konto nicht
unverändert an jemanden ausliefern, der die Website in ein Netzwerk stellt, es sei denn, es ist bewusst eine
öffentliche Demo ([Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)).

## Aktuell halten

- Eine Distribution hat zwei Update-Ströme: die Symfony-Seite (Composer) und das darin enthaltene Exponential 6. Beide
  kommen in diesen Distributionen über Composer.
- Lesen Sie vor jedem Update das Änderungsprotokoll der Distribution
  ([Änderungsprotokoll der Platform](../../../changelogs/extensions/exponential-platform.md)).
- Testen Sie das Update an einer Kopie einer gebündelten Installation, bevor Sie es veröffentlichen
  ([Ein Bundle testen](ein-bundle-testen.md)).

## Verwandte Seiten

- [Die Basis wählen](die-basis-waehlen.md)
- [Legacy-Bridge](../../funktionen/6.0/legacy-bridge.md)
- [Site-Bundles, Metadaten und die Twig-Werkzeuge der Platform](../../funktionen/6.0/plattform-site-bundles.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
