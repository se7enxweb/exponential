---
title: Checklisten zum Ökosystem
audience: ecosystem, developer, partner, advocate
category: ecosystem
order: 8
summary: Checklisten für die Arbeit im Ökosystem von Exponential - bevor Sie ein Produkt oder eine Distribution beschreiben, bevor Sie sich auf ein Paket stützen, bevor Sie ein Paket veröffentlichen und bevor Sie eine Zahl über das Ökosystem zitieren.
keywords: Checkliste, Distribution, Produkt beschreiben, von einem Paket abhängen, Paket veröffentlichen, Zahlen, Fakten, composer.json, checklist, describe product, depend on package, publish package, numbers, facts
type: help
---
# Checklisten zum Ökosystem

## Inhalt

1. [Bevor Sie ein Produkt oder eine Distribution beschreiben](#bevor-sie-ein-produkt-oder-eine-distribution-beschreiben)
2. [Bevor Sie sich auf ein Paket stützen](#bevor-sie-sich-auf-ein-paket-stützen)
3. [Bevor Sie ein Paket veröffentlichen](#bevor-sie-ein-paket-veröffentlichen)
4. [Bevor Sie eine Zahl zitieren](#bevor-sie-eine-zahl-zitieren)

## Bevor Sie ein Produkt oder eine Distribution beschreiben

```text
[ ] Ich habe seine composer.json gelesen (require), nicht nur seine Beschreibung
[ ] Funktionen, die ich nenne, stehen in seinen Anforderungen oder seiner Dokumentation
[ ] Ich leihe mir keine Funktionen einer anderen Distribution (die DXP hat keinen Layout-Editor,
    keinen Workflow, keinen Zeitplaner und keinen Legacy-Kernel)
[ ] Ich nenne die Version, die ich beschreibe
[ ] Ich verlinke die Quelle
```

Seiten: [Exponential Platform DXP](produkte/platform-dxp.md), [Ein Produkt auswählen](produkte/ein-produkt-auswaehlen.md).

## Bevor Sie sich auf ein Paket stützen

```text
[ ] Lizenz passt zu meiner Nutzung
[ ] PHP- und Kernel-Versionen in seiner composer.json passen zu meiner Installation
[ ] Neue Releases oder Commits; Issues werden beantwortet
[ ] Versionsfelder stimmen mit dem Tag überein
[ ] Auf einer Kopie meiner Website getestet
[ ] Ich weiß, wer es pflegt, oder ich kann es selbst pflegen
```

Seiten: [Eine Erweiterung finden](pakete/eine-erweiterung-finden.md), [Kompatibilität und Abhängigkeiten](pakete/kompatibilitaet-und-abhaengigkeiten.md).

## Bevor Sie ein Paket veröffentlichen

```text
[ ] Keine Geheimnisse, Kundendaten oder websitespezifischen Einstellungen
[ ] Datei LICENSE; README mit Zweck, unterstützten Versionen, Installation
[ ] composer.json; Eintrag auf Packagist; Webhook
[ ] ezinfo.php und extension.xml tragen die Release-Version
[ ] Nächste Version aus den veröffentlichten, nach Version sortierten Tags gewählt
[ ] Tag erst nach den Prüfungen gepusht; danach nie verschoben
[ ] Unter Projects aufgeführt und angekündigt
```

Seiten: [Im Ökosystem veröffentlichen](personen/im-oekosystem-veroeffentlichen.md),
[Die Release-Checkliste](../erweiterungsentwicklung/teil-7-veroeffentlichen/die-release-checkliste.md).

## Bevor Sie eine Zahl zitieren

```text
[ ] Ich kenne die Quelle und ihr Datum
[ ] Ich nenne das Datum oder zähle mit dem Befehl neu, den die Quelle nennt
[ ] Es ist keine Vermutung, die als Tatsache dargestellt wird
```

Seite: [Hilfe genau halten](../gemeinschaft/anderen-helfen/die-hilfe-korrekt-halten.md).

## Verwandte Seiten

- [Ökosystem: hier beginnen](oekosystem-hier-beginnen.md)
- [Checklisten für die Community](../gemeinschaft/checklisten-fuer-die-community.md)
- [Wo die Fakten stehen](../fuersprecher/wo-die-fakten-stehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
