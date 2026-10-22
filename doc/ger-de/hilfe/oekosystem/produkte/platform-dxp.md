---
title: Exponential Platform DXP im Ökosystem
audience: ecosystem, developer, partner, businesses
category: ecosystem
order: 18
summary: Der Platz von Exponential Platform DXP im Ökosystem - die reine Symfony-Digital-Experience-Platform, die die Linie von eZ Platform und Ibexa OSS fortführt -, was ihr Metapaket liefert und was nicht (kein Layout-Editor, keine Workflows, kein Zeitplaner, kein Legacy-Kernel), was sie hinzufügt und wie Sie beginnen.
keywords: Exponential Platform DXP, DXP, Symfony, Ibexa OSS, eZ Platform, REST, GraphQL, SQLite, Metapaket, Skeleton, kein Legacy-Kernel, meta package, skeleton, no legacy kernel
type: help
---
# Exponential Platform DXP im Ökosystem

**"Exponential Platform DXP is a free, open-source Digital Experience Platform built on the Symfony framework, the
community continuation of the eZ Platform and Ibexa OSS line maintained by 7x"**, eine freie, quelloffene Digital
Experience Platform auf Basis von Symfony, die von 7x gepflegte Fortführung der Linie von eZ Platform und Ibexa OSS
durch die Community (`README.md`).

## Inhalt

1. [Was sie liefert](#was-sie-liefert)
2. [Was sie nicht liefert](#was-sie-nicht-liefert)
3. [Was sie hinzufügt](#was-sie-hinzufügt)
4. [Für wen sie ist](#für-wen-sie-ist)
5. [Wie Sie beginnen](#wie-sie-beginnen)

## Was sie liefert

Aus den Anforderungen des Metapakets `se7enxweb/exponential-platform-dxp` gelesen: "the admin UI, content forms, REST,
GraphQL and the GraphiQL console, HTTP cache, core and Solr search, the rich text, matrix and query field types,
notifications, messenger, cron, the standard design, Twig components, JWT authentication and `se7enxweb/layouts-core`",
also die Admin-Oberfläche, Inhaltsformulare, REST, GraphQL mit der GraphiQL-Konsole, HTTP-Cache, Kern- und Solr-Suche,
die Feldtypen für formatierten Text, Matrix und Abfragen, Benachrichtigungen, Messenger, Cron, das Standarddesign,
Twig-Komponenten, JWT-Authentifizierung und `se7enxweb/layouts-core`
([Exponential Platform DXP](../../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)). Das Metapaket
beschreibt sich als "Stable; Open Source", mit der Lizenz "GPL-2.0-or-later or proprietary".

## Was sie nicht liefert

Aus derselben Quelle: "a layouts editor (no layouts UI or standard blocks), an approval workflow, a scheduler, or the
legacy kernel. For Exponential 6 beside Symfony, choose Legacy or Nexus.", also keinen Layout-Editor, keinen
Freigabe-Workflow, keinen Zeitplaner und keinen Legacy-Kernel; für Exponential 6 neben Symfony wählen Sie Legacy oder
Nexus. Beschreiben Sie die DXP nicht mit Funktionen der anderen Distributionen.

## Was sie hinzufügt

Was sie laut README gegenüber Ibexa DXP OSS hinzufügt:

- "The SQLite database driver and framework support", den SQLite-Treiber und die Unterstützung im Framework;
- aktuelles PHP ("PHP 8.3 and later");
- "A path from Exponential 6", einen Weg von Exponential 6 über die Dual-Kernel-Distributionen;
- "A name of its own", einen eigenen Namen.

## Für wen sie ist

"Organizations with a lot of structured content", "Teams leaving eZ Platform or Ibexa OSS", Anwender von Exponential,
"who want a modern Symfony stack", und "Developers and agencies who prefer standard Symfony bundles, Composer and Twig",
also Organisationen mit vielen strukturierten Inhalten, Teams, die eZ Platform oder Ibexa OSS verlassen, Anwender, die
einen modernen Symfony-Stack möchten, und Entwickler und Agenturen, die Standard-Bundles von Symfony, Composer und Twig
bevorzugen (`README.md`, "Who it is for").

## Wie Sie beginnen

- [DXP-Skeleton](../../../funktionen/6.0/plattform-dxp-skeleton.md): `composer create-project` und ein dokumentierter
  Installationsweg.
- [SQLite für die Plattform](../../../funktionen/6.0/plattform-sqlite-installation.md).
- Downloads: <https://platform.exponential.earth>.

## Verwandte Seiten

- [Produkte](README.md)
- [Die Platform-Linie](die-platform-linie.md)
- [Ein Produkt auswählen](ein-produkt-auswaehlen.md)
- [Eine Distribution prüfen, bevor Sie sie beschreiben](../checklisten-zum-oekosystem.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
