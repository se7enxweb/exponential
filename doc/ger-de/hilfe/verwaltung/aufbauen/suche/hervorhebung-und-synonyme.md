---
title: Hervorhebung und Synonyme
audience: government, admin, editor
category: public-sector
order: 70
summary: Wie eine Behörde für häufige Suchen die richtige Seite nach vorn bringt und verschiedene Wörter dieselben Inhalte finden lässt - das Modul zur Hervorhebung von ezfind (nur mit Solr), Synonyme in Solr, Synonyme mit eztags, und was die eingebaute Suchmaschine stattdessen bietet.
keywords: Hervorhebung, Top-Treffer, hervorgehobene Ergebnisse, Synonyme, Schlüsselwörter, Feinabstimmung der Suche, elevation, elevate, best bets, promoted results, synonyms, synonyms.txt, ezfind elevate, eztags synonyms, keywords, search tuning
type: help
---
# Hervorhebung und Synonyme

Diese Seite richtet sich an Administratoren und Redakteure, die die Suche abstimmen: „Bewohnerparkausweis“ soll zuerst
zur Seite des Ausweises führen, und „Müll“, „Abfall“ und „Kehricht“ sollen dieselbe Leistung finden. Was möglich ist,
hängt von der Suchmaschine ab.

## Inhalt

1. [Hervorhebung mit ezfind](#hervorhebung-mit-ezfind)
2. [Synonyme in Solr](#synonyme-in-solr)
3. [Synonyme mit eztags](#synonyme-mit-eztags)
4. [Mit der eingebauten Suchmaschine](#mit-der-eingebauten-suchmaschine)
5. [Finden, was abzustimmen ist](#finden-was-abzustimmen-ist)

## Hervorhebung mit ezfind

Die Erweiterung `ezfind` hat ein Modul zur Hervorhebung (`ezfind/elevate`, Views `elevate`, `elevation_detail`,
`remove_elevation`, Richtlinie `ezfind/elevate`). Seine Seite erklärt: Ein Objekt für eine bestimmte Suchanfrage
„hervorzuheben“ bedeutet, dass dieses Objekt unter den allerersten Suchergebnissen erscheint, wenn mit dieser
Suchanfrage gesucht wird. Sie wählen das Objekt (**Durchsuchen**), die Suchanfrage, die **Sprache** und ob es ein
**Fuzzy match** ist, dann **Elevate**; bestehende Konfigurationen lassen sich **By object** oder **By search query**
auflisten und aufheben.

Das funktioniert nur, wenn `ezfind` mit einem Solr-Server aktiv ist; auf einer Installation ohne es antwortet die
Adresse, dass das Modul nicht verfügbar ist.

## Synonyme in Solr

Solr liest Synonyme aus seiner Konfiguration (`synonyms.txt` im Verzeichnis `conf` des Cores). Die mit der
Solr-Konfiguration von `ezfind` gelieferte Datei enthält nur Testzuordnungen, Sie schreiben also Ihre eigenen, je
Sprach-Core, wenn Sie einen Core je Sprache verwenden, und laden Solr neu. Das ist eine Änderung der
Serverkonfiguration; halten Sie sie in der Dokumentation Ihrer Installation fest.

## Synonyme mit eztags

Mit `eztags` kann ein Schlagwort Synonyme haben; Inhalte, die mit dem Hauptschlagwort versehen sind, werden über seine
Schlagwortseite unter jedem Synonym gefunden ([Verschlagworten mit eztags](../../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)).
Das hilft beim Stöbern nach Thema mit jeder Suchmaschine.

## Mit der eingebauten Suchmaschine

Es gibt keine Hervorhebung und keine Synonymliste. Stattdessen:

- fügen Sie die Wörter, die Menschen verwenden, dem Schlüsselwortfeld der Seite hinzu (als durchsuchbar markiert);
- schreiben Sie in der Zusammenfassung eine kurze Zeile „Auch bekannt als“;
- verlinken Sie von der Suchseite eine Liste der „Häufigsten Anliegen“.

## Finden, was abzustimmen ist

**Setup > Suchstatistiken** listet Suchbegriffe danach, wie oft sie gesucht wurden, und die durchschnittliche Zahl der
Ergebnisse; die Begriffe ohne Treffer sind die ersten Kandidaten
([Suchstatistik](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).

## Verwandte Seiten

- [Solr mit ezfind](solr-mit-ezfind.md)
- [Suchstatistik und Datenschutz](suchstatistik-und-datenschutz.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
