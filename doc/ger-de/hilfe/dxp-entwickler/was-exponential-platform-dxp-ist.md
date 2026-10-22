---
title: Was Exponential Platform DXP ist
audience: dxp-developer
category: dxp-development
order: 2
summary: Die DXP auf einer Seite für Entwickler - ihre Architektur, der eine Symfony-Kernel, die Einstiegspunkte, die Pakete, aus denen sie besteht, wer sie pflegt und wo sie in der Produktfamilie von Exponential steht.
keywords: Exponential Platform DXP, was ist, Architektur, Symfony 7.4, PHP 8.3, ein Kernel, Platform v5, Ibexa OSS, eZ Platform, Produktfamilie, 7x, what is, architecture, single kernel, product family
type: help
---
# Was Exponential Platform DXP ist

Diese Seite richtet sich an Entwickler, die Exponential Platform DXP zum ersten Mal begegnen. Sie beschreibt das Produkt
so, wie seine eigenen Pakete es beschreiben, zeigt seine Architektur und stellt es neben die anderen Produkte der
Familie.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Die Architektur](#die-architektur)
3. [Woraus ein Projekt besteht](#woraus-ein-projekt-besteht)
4. [Wer sie pflegt](#wer-sie-pflegt)
5. [Wo sie in der Familie steht](#wo-sie-in-der-familie-steht)
6. [Was der Name bedeutet](#was-der-name-bedeutet)

## In einem Absatz

Exponential Platform DXP ist "eine freie, quelloffene Digital Experience Platform auf Basis des Symfony-Frameworks, die
von 7x gepflegte Fortführung der Linie eZ Platform und Ibexa OSS durch die Gemeinschaft" (README, zitiert unter
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)). Das Skeleton
nennt Platform v5 "das Standard-Release mit einem Kernel": der Inhaltskernel von Platform v5 auf **Symfony 7.4 LTS** mit
**PHP 8.3+**, mit "keiner LegacyBridge, keinem klassischen Legacy-Kernel" (`README.md` und `INSTALL.md` des Skeletons).

## Die Architektur

Die Installationsanleitung des Skeletons (Abschnitt 2) zeichnet den Weg einer Anfrage so:

```text
Browser request
  -> web server (Apache or Nginx, with PHP-FPM)
    -> public/index.php (the Symfony entry point)
      -> Symfony kernel (Platform v5, Symfony 7.4 LTS)
           |- administration interface        /adminui/
           |- REST API v2                     /api/ibexa/v2/ (the guide shows /api/ezp/v2/)
           |- GraphQL API                     /graphql
           '- Symfony and Twig site controllers  /
```

Eine Symfony-Anwendung, ein Kernel, ein Web-Einstiegspunkt. Inhalte werden über Doctrine DBAL in einer relationalen
Datenbank gespeichert (MySQL, MariaDB, PostgreSQL oder SQLite für die Entwicklung) und über Twig-Templates ausgegeben.

| Schicht | Was sie in der DXP ist | Lesen |
|---|---|---|
| Inhalts-Repository | der Kernel von Platform v5, `se7enxweb/exponential-platform-dxp-core` (der Fork des Upstream-Core) | [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md) |
| Administration | `se7enxweb/admin-ui` unter `/adminui/` | [Die Admin-Oberfläche](die-admin-oberflaeche.md) |
| Frontend | Twig mit `ibexa/design-engine` und `ibexa/standard-design` | [Twig und die Design-Engine](twig-templates-und-die-design-engine.md) |
| APIs | `ibexa/rest`, `ibexa/graphql`, JWT über `lexik/jwt-authentication-bundle` | [Die REST-API](die-rest-api.md), [GraphQL](graphql-und-graphiql.md) |
| Suche | `ibexa/core-search`, `ibexa/search`, `ibexa/solr` | [Suche](suche-legacy-und-solr.md) |
| Betrieb | `ibexa/cron`, `ibexa/messenger`, `ibexa/http-cache`, Doctrine-Migrationen | [Cron](cron.md), [HTTP-Cache](http-cache-und-varnish.md) |

## Woraus ein Projekt besteht

Ein DXP-Projekt ist ein Symfony-Projekt, das aus dem Skeleton `se7enxweb/exponential-platform-dxp-skeleton` angelegt
wird. Die eigene `composer.json` des Skeletons verlangt kaum mehr als das Metapaket `se7enxweb/exponential-platform-dxp`,
`symfony/flex` und `symfony/runtime`; das Metapaket bringt alles andere mit ([Das Metapaket lesen](das-metapaket-lesen.md)).
Rezepte von Symfony Flex schreiben die Konfiguration unter `config/`, wenn die Pakete installiert werden
([Der Verzeichnisaufbau des Projekts](der-verzeichnisaufbau-des-projekts.md)).

## Wer sie pflegt

Die DXP wird von **7x** und der Gemeinschaft gepflegt. Das README des Skeletons beginnt mit einem Projekthinweis: Das
Projekt steht weder mit der ursprünglichen Software von eZ Systems noch mit eZ Systems selbst in Verbindung. Viele seiner
Pakete sind se7enxweb-Forks von Upstream-Paketen, die die Upstream-Namen unter `replace` deklarieren
([Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md)); andere werden unverändert von Upstream
verlangt (`ibexa/rest`, `ibexa/graphql` und weitere).

## Wo sie in der Familie steht

| Produkt | Kernel | Verwenden Sie es, wenn |
|---|---|---|
| **Exponential Platform DXP** | nur Platform v5, Symfony 7.4 | ein neues Symfony-Projekt, ohne den Kernel von Exponential 6 |
| Exponential Platform Legacy | Symfony-Plattform plus der Kernel von Exponential 6 (zwei Kernel) | Sie eine Exponential-6-Website behalten und Symfony daneben ergänzen |
| Exponential Platform Nexus | eine fertige Website mit Netgen Layouts; die Linien v5 ohne den Legacy-Kernel, die früheren Linien mit ihm | Sie schnell eine vollständige Demo-Website möchten |
| Exponential 6 | der PHP-CMS-Kernel selbst, ohne Symfony | ein CMS nur auf PHP |

Der vollständige Vergleich steht unter [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md).

## Was der Name bedeutet

"DXP" steht für Digital Experience Platform, den Begriff, den die Upstream-Linie für eine Inhaltsplattform mit APIs für
mehrere Kanäle verwendete. In dieser Familie benennt er eine Distribution mit einer festgelegten Paketliste. Er ist kein
Versprechen jeder Funktion, die andere unter der Bezeichnung DXP verkaufte Produkte bieten
([Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)).

## Verwandte Seiten

- [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)
- [DXP-Skeleton: create-project und eine dokumentierte Installation](../../funktionen/6.0/plattform-dxp-skeleton.md)
- [Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
