---
title: Faire Vergleiche
audience: press, advocate
category: press-media
order: 140
summary: Wie Journalisten und Rezensenten Exponential fair mit anderen Content-Management-Systemen vergleichen - gleiche Kriterien und Versionen auf beiden Seiten, jedes Produkt nach seinen eigenen Quellen beschrieben, keine Benchmarks ohne veröffentlichte Methode, Offenlegung von Interessen und eine Vorlage für eine Vergleichstabelle.
keywords: Vergleich, vergleichen, gegen, versus, Alternativen, anderes CMS, WordPress, Drupal, Ibexa, Benchmark, fairer Vergleich, Kriterien, Rezension, Methodik, comparison, compare, vs, alternatives, other CMS, fair comparison, criteria, review, methodology
type: help
---
# Faire Vergleiche

Diese Seite ist für Rezensenten, Journalisten und Creator, die Exponential mit einem anderen CMS vergleichen. Vergleiche
helfen Lesern bei der Entscheidung, und sie gehen auf vorhersehbare Weise schief. Das Projekt selbst vergleicht nur, was
es belegen kann ([Exponential sachlich vergleichen](../geschaeft/exponential-sachlich-vergleichen.md)); diese Seite wendet
dieselbe Disziplin auf Ihre Berichterstattung an.

## Inhalt

1. [Die Regeln](#die-regeln)
2. [Kriterien, die sich prüfen lassen](#kriterien-die-sich-prüfen-lassen)
3. [Leistungsvergleiche](#leistungsvergleiche)
4. [Eine Vorlage für eine Vergleichstabelle](#eine-vorlage-für-eine-vergleichstabelle)
5. [Wörter, die Sie vermeiden sollten](#wörter-die-sie-vermeiden-sollten)

## Die Regeln

1. **Gleiche Kriterien auf beiden Seiten.** Jede Zeile Ihrer Tabelle hat einen Wert für jedes Produkt.
2. **Jedes Produkt nach seinen eigenen Quellen.** Beschreiben Sie das andere Produkt anhand seiner eigenen
   Dokumentation, so wie Sie Exponential anhand dieser beschreiben.
3. **Gleiches Datum, vergleichbare Versionen.** Nennen Sie die Versionen und das Datum, an dem Sie geprüft haben.
4. **Fakten in die Tabelle, Meinungen in den Text.** Sagen Sie, was was ist.
5. **Legen Sie Interessen offen.** Wenn Sie mit einem der Produkte arbeiten, von ihm bezahlt werden oder es sponsern,
   sagen Sie es ([Offenlegung und Ethik beim Sponsoring](offenlegung-und-ethik-beim-sponsoring.md)).
6. **Fragen Sie beide Seiten**, wenn eine Zeile unklar ist.

## Kriterien, die sich prüfen lassen

| Kriterium | Quelle für Exponential |
|---|---|
| Lizenz | [Lizenzen für Reporter erklärt](lizenzen-fuer-reporter-erklaert.md) |
| PHP-Versionen | README, „What version of PHP is required“ |
| Datenbanken | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Betriebssysteme | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Sprachen, Rechts-nach-links | [Sprachen in Exponential](../../../eng-us/languages.md) |
| Inhaltsmodell | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Multisite | [Thema: Multisite](thema-multisite.md) |
| Eingebauter Shop | README, „Main Exponential features“ |
| Sicherheitsprozess | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Release-Historie | [Die 6.0.x-Releases auf einen Blick](die-6-0-x-releases-auf-einen-blick.md) |

## Leistungsvergleiche

Das Projekt veröffentlicht Zahlen für seinen eigenen Stack und keinen Vergleich mit anderen Produkten. Wenn Sie die
Geschwindigkeit vergleichen:

- lassen Sie beide Produkte auf derselben Hardware laufen, mit vergleichbaren Seiten und Caches so konfiguriert, wie
  jedes Projekt es empfiehlt;
- veröffentlichen Sie Ihre Methode, Versionen, Konfiguration und Rohdaten;
- sagen Sie, was Sie nicht getestet haben.

Ohne das schreiben Sie nicht „schneller als“ ([Zahlen prüfen](zahlen-pruefen.md)).

## Eine Vorlage für eine Vergleichstabelle

```text
VERGLEICH: Exponential <Version> und <anderes CMS> <Version>        geprüft am <Datum>

KRITERIUM            EXPONENTIAL                         ANDERES CMS                  QUELLEN
Lizenz               GPL v2 oder später                  <seine Lizenz>               <Links>
PHP-Versionen        8.0 - 8.5                           <...>                        <Links>
Datenbanken          SQLite, PostgreSQL, MongoDB,        <...>                        <Links>
                     MariaDB, Oracle, MySQL
Multisite            eingebaut                           <...>                        <Links>
Shop                 eingebaut                           <...>                        <Links>
Rechts-nach-links    ja                                  <...>                        <Links>
Sicherheitsmeldungen vertraulich, erste Antwort binnen   <...>                        <Links>
                     eines Arbeitstags
Offenlegung          <Ihre Beziehung zu jedem Produkt>
```

## Wörter, die Sie vermeiden sollten

| Vermeiden Sie | Verwenden Sie |
|---|---|
| „besser“, „am besten“, „Killer“ | Was es tut, mit der Quelle |
| „veraltet“ für das andere Produkt | Seine Version und sein Datum, mit seiner eigenen Beschreibung |
| „die Alternative zu eZ Publish“ | Exponential ist dieselbe Codebasis, umbenannt ([Über die eZ-Publish-Jahre schreiben](ueber-die-ez-publish-jahre-schreiben.md)) |
| „wie Ibexa, nur kostenlos“ | Getrennte Produkte; Exponential ist nicht mit Ibexa verbunden ([Nicht verbunden mit eZ Systems oder Ibexa](nicht-verbunden-mit-ez-systems-oder-ibexa.md)) |

## Verwandte Seiten

- [Eine faire Rezension schreiben](eine-faire-rezension-schreiben.md)
- [Einwand: „Warum nicht ein anderes CMS?“](../fuersprecher/einwand-warum-nicht-ein-anderes-cms.md)
- [Exponential sachlich vergleichen](../geschaeft/exponential-sachlich-vergleichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
