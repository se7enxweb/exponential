---
title: Als Lernende übersetzen
audience: student
category: learning
order: 43
summary: Wie Sprachstudierende und Muttersprachler Übersetzungen zu Exponential beitragen - wo die Übersetzungen der Administrationsoberfläche liegen, wie Sie eine Sprache wählen und ihre Vollständigkeit prüfen, der Ablauf beim Übersetzen, Qualitätsregeln, Sprachen mit Schreibrichtung von rechts nach links, das Übersetzen der Hilfe unter der GFDL und warum eine Übersetzung ein guter erster Beitrag ist.
keywords: übersetzung, übersetzen, translation.ts, share/translations, locale, sprache, sprache der oberfläche, rechts nach links, GFDL, übersetzung der dokumentation, beitrag, lernende, translation, translate, language, interface language, right to left, documentation translation, contribution, student
type: help
---
# Als Lernende übersetzen

Wenn Sie eine andere Sprache als Englisch gut beherrschen, ist eine Übersetzung einer der nützlichsten Beiträge, die
Sie leisten können, und einer der einfachsten für den Anfang. „Übersetzungen erreichen alle Benutzer dieser Sprache,
oft Menschen, die nie ein englisches Handbuch lesen würden“ ([Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md)).

## Inhalt

1. [Wo Übersetzungen liegen](#wo-übersetzungen-liegen)
2. [Eine Sprache und einen Teil wählen](#eine-sprache-und-einen-teil-wählen)
3. [Der Ablauf](#der-ablauf)
4. [Qualitätsregeln](#qualitätsregeln)
5. [Sprachen mit Schreibrichtung von rechts nach links](#sprachen-mit-schreibrichtung-von-rechts-nach-links)
6. [Die Hilfe übersetzen](#die-hilfe-übersetzen)
7. [Was Sie lernen](#was-sie-lernen)

## Wo Übersetzungen liegen

Nach [Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#wo-die-übersetzungen-liegen):

- Die Meldungen der Administrationsoberfläche liegen in `share/translations/<locale>/translation.ts`, eine Datei pro
  Sprache, zum Beispiel `ger-DE`, `fre-FR`, `esl-ES`, `ukr-UA`, `heb-IL`.
- `doc/eng-us/languages.md` listet die Locales, die Übersetzungen der Administrationsoberfläche und wie vollständig
  jede ist ([Sprachen](../../sprachen.md)).
- Erweiterungen bringen ihre eigenen Übersetzungen in ihren eigenen Ordnern `translations/` mit.

Inhaltssprachen (die Sprache von Artikeln) sind etwas anderes: Diese übersetzen Redakteure in der
Administrationsoberfläche ([Projekt 5](projekt-05-eine-mehrsprachige-website.md)).

## Eine Sprache und einen Teil wählen

1. Finden Sie Ihre Sprache und ihre Vollständigkeit in `doc/eng-us/languages.md`.
2. Wählen Sie einen kleinen Teil, den Sie fertigstellen können: die Bildschirme, die Sie am häufigsten verwenden, zum
   Beispiel das Dashboard oder das Bearbeitungsformular.
3. Prüfen Sie auf Ihrer Lernwebsite, wie der Bildschirm in Ihrer Sprache aussieht: Der Kasten **Oberfläche** in der
   rechten Spalte wechselt für Sie die Sprache der Administrationsoberfläche und zeigt, wie vollständig jede
   Übersetzung ist ([Sprachen](../sprachen.md#die-sprache-der-administration)).

## Der Ablauf

1. Forken Sie das Repository und öffnen Sie die `translation.ts` der Sprache.
2. Übersetzen Sie Meldungen, die nicht übersetzt oder als unfertig markiert sind. Behalten Sie Platzhalter wie `%1`
   genau so bei, wie sie sind.
3. Testen Sie: Stellen Sie die Administrationsoberfläche auf Ihre Sprache um und sehen Sie sich die Bildschirme an,
   die Sie übersetzt haben.
4. Committen Sie mit einer Betreffzeile wie `Updated: German translation of the help dashboard` und eröffnen Sie
   einen Pull Request.

Quelle: [Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#wie-sie-übersetzen). Die Dateien lassen sich
mit Qt Linguist oder einem XML-Editor bearbeiten ([Einen ersten Beitrag begleiten](../fuersprecher/einen-ersten-beitrag-begleiten.md#weg-b-übersetzung)).
`php bin/php/ezchecktranslation.php <locale>` zeigt, wie vollständig die Übersetzung der Oberfläche einer Locale ist
([Inhaltssprachen](../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell)).

## Qualitätsregeln

- Verwenden Sie die Begriffe, die der Rest der Übersetzung bereits verwendet; Einheitlichkeit ist wichtiger als
  Eleganz.
- Bitten Sie vor dem Einreichen eine zweite muttersprachliche Person um eine Durchsicht. Eine Lerngruppe ist dafür
  ideal.
- Übersetzen Sie nicht maschinell ohne Durchsicht.
- Produktnamen bleiben, wie sie sind: Exponential, 7x, die Exponential Foundation.

Quelle: [Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#qualität).

## Sprachen mit Schreibrichtung von rechts nach links

Die Administrationsoberfläche unterstützt `dir="rtl"` für Sprachen, die von rechts nach links geschrieben werden, wie
Arabisch und Hebräisch. Wenn Sie eine davon übersetzen, prüfen Sie das Layout der Bildschirme, nicht nur die Wörter
([Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#sprachen-mit-schreibrichtung-von-rechts-nach-links)).

## Die Hilfe übersetzen

Hilfeseiten sind freie Dokumentation unter der GFDL 1.3 oder später, Sie dürfen sie also übersetzen
([Freie Dokumentation](../freie-dokumentation.md#übersetzen)). Stimmen Sie sich mit den Maintainern in einem Issue
ab, wie übersetzte Seiten organisiert werden, bevor Sie viele Seiten übersetzen. Wie Lesende die Hilfe in anderen
Sprachen finden: [Die Hilfe in anderen Sprachen lesen](../hilfe-in-anderen-sprachen-lesen.md).

## Was Sie lernen

- Die Administrationsoberfläche in der Tiefe: Sie lesen jede Meldung der Bildschirme, die Sie übersetzen.
- Lokalisierung von Software: Platzhalter, Pluralformen, Kontext, Einheitlichkeit.
- Git und Begutachtung, an einem Beitrag, der sich leicht prüfen lässt.

Eine gemergte Übersetzung ist ein Beitrag, den Sie in Ihrem Portfolio zeigen können ([Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)).

## Verwandte Seiten

- [Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md)
- [Bei Übersetzungen helfen](../fuersprecher/bei-uebersetzungen-helfen.md)
- [Als Lernende beitragen](als-lernende-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
