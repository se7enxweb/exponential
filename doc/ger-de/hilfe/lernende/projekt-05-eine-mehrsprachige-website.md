---
title: "Projekt 5: eine mehrsprachige Website"
audience: student
category: learning
order: 29
summary: Lernprojekt 5 - eine zweite Inhaltssprache hinzufügen, einen Teil Ihrer Vereinswebsite übersetzen, die Hauptsprache und die immer verfügbaren Elemente festlegen, einen Siteaccess die Sprache mit SiteLanguageList anzeigen lassen, prüfen, was Besucher sehen, und einen Sprachplan mit Nachweisen abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, mehrsprachig, inhaltssprache, übersetzung, Setup Sprachen, SiteLanguageList, siteaccess, hauptsprache, immer verfügbar, ger-DE, rechts nach links, lernende, study project, multilingual, content language, translation, Setup Languages, main language, always available, right to left, learner
type: help
---
# Projekt 5: eine mehrsprachige Website

Exponential speichert Inhalte in vielen Sprachen, auch in Schriften, die von rechts nach links laufen. In diesem
Projekt fügen Sie Ihrer Lernwebsite eine zweite Sprache hinzu, übersetzen einen Teil Ihrer Vereinswebsite aus
[Projekt 2](projekt-02-eine-kleine-website-struktur.md) und lassen einen Siteaccess sie anzeigen. Die Schritte folgen
[Inhaltssprachen](../../anleitungen/inhaltssprachen.md).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 3 Stunden |
| Pfade | Redakteur, Administrator |
| Voraussetzungen | [Projekt 2](projekt-02-eine-kleine-website-struktur.md); [Siteaccesses und Einstellungen verstehen](siteaccesses-und-einstellungen-verstehen.md); ein Terminal für Aufgabe 5 |

## Ziel

Eine zweite Inhaltssprache mit mindestens fünf übersetzten Elementen, ein Siteaccess, der diese Sprache zuerst und
Englisch als Rückfall zeigt, und ein Sprachplan, der erklärt, was Besucher bei einem nicht übersetzten Element sehen.

## Bevor Sie beginnen

Lesen Sie [Inhaltssprachen](../../anleitungen/inhaltssprachen.md), Abschnitte 1 bis 7, und [Sprachen](../sprachen.md).
Wählen Sie eine Sprache, die Sie schreiben können, oder verwenden Sie Deutsch (`ger-DE`) mit kurzen erfundenen Sätzen.

Kennen Sie diese Fakten aus der Anleitung:

- Das Hinzufügen einer Sprache **übersetzt nichts und ändert keinen Siteaccess**: Noch sieht kein Besucher etwas
  Neues ([Abschnitt 4](../../anleitungen/inhaltssprachen.md#4-eine-sprache-hinzufügen)).
- Was Besucher sehen, wird je Siteaccess durch `SiteLanguageList[]` entschieden, die bevorzugte Sprache zuerst
  ([Abschnitt 5](../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)).
- Ein Element, das **immer verfügbar** ist, wird in seiner Hauptsprache gezeigt, wenn keine der aufgeführten Sprachen
  vorhanden ist ([Abschnitt 6](../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).

## Aufgaben

1. **Fügen Sie die Sprache hinzu.** **Setup > Sprachen**, **Sprache hinzufügen**, suchen Sie sie (die Eingabe von
   `german` oder `ger` funktioniert), drücken Sie **Sprache hinzufügen**. Erwartet: eine Meldung wie *German (ger-DE)
   was added*. Wenn Ihre Website sie bereits hat, notieren Sie das stattdessen.
2. **Übersetzen Sie fünf Elemente.** Klicken Sie bei jedem Element auf das gestrichelte Abzeichen der Sprache (oder
   wählen Sie sie unter **Neue Übersetzung**), übersetzen Sie auf Grundlage der englischen Fassung und **Zur
   Veröffentlichung schicken** ([Sprachen](../sprachen.md#ein-objekt-übersetzen)).
3. **Sehen Sie sich die Abzeichen an.** Gefüllte Abzeichen sind vorhandene Übersetzungen, gestrichelte fehlen.
4. **Legen Sie die immer verfügbaren Elemente fest.** Ordner und Bilder sind meist immer verfügbar, sodass eine
   deutsche Website sie trotzdem findet. Prüfen Sie die Einstellung im Fenster **Übersetzungen** eines Ordners.
5. **Zeigen Sie die Sprache an.** Wählen Sie den Siteaccess, der sie zeigen soll. Wenn Ihre Installation für diese
   Sprache bereits einen hat (das Standardpaket legt für jede zusätzliche Sprache, die Sie bei der Installation
   gewählt haben, einen an, [Site-Pakete für Lernende](site-pakete-fuer-lernende.md)), lesen Sie seine
   `SiteLanguageList` mit `php bin/php/console exp:ini where site/RegionalSettings/SiteLanguageList <siteaccess>`.
   Andernfalls setzen Sie sie in `settings/siteaccess/<siteaccess>/site.ini.append.php` wie im Beispiel der
   Anleitung: zuerst eine leere Zeile `SiteLanguageList[]`, dann Ihre Sprache, dann `eng-US`.
6. **Leeren Sie die Caches** für Einstellungen und Inhalte: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`
   und `--clear-tag=content` ([Inhaltssprachen, Abschnitt 10](../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell)).
7. **Prüfen Sie als Besucher.** Öffnen Sie in diesem Siteaccess ein übersetztes Element, ein nicht übersetztes
   Element und einen nicht übersetzten, aber immer verfügbaren Ordner. Schreiben Sie auf, was Sie jeweils sehen.
8. **Wechseln Sie die Sprache der Administrationsoberfläche** für sich selbst im Kasten **Oberfläche** und wieder
   zurück ([Sprachen](../sprachen.md#die-sprache-der-administration)). Notieren Sie, was sich ändert und was
   nicht.

## Abgabe

- Ein Sprachplan (eine halbe Seite): welche Sprachen, welche die Hauptsprache ist, welche Elemente immer verfügbar
  sind und warum.
- Die Ausgabe von `exp:ini where` für `SiteLanguageList` Ihres Siteaccess.
- Screenshots: die Seite der Sprachen; ein Element mit beiden Abzeichen gefüllt; die drei Besucherprüfungen aus
  Aufgabe 7.
- Ein kurzer Bericht darüber, was Sie überrascht hat.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (7):

1. Eine zweite Inhaltssprache existiert.
2. Mindestens fünf Elemente sind übersetzt und veröffentlicht.
3. Der Siteaccess führt die neue Sprache zuerst und `eng-US` an zweiter Stelle, gezeigt mit `exp:ini where`.
4. Einstellungen wurden in einer Datei `.ini.append.php` geändert, nicht in `settings/site.ini`.
5. Die drei Besucherprüfungen sind dokumentiert, mit dem Grund für jedes Ergebnis.
6. Der Bericht unterscheidet Inhaltssprachen von der Sprache der Oberfläche.
7. Keine maschinelle Übersetzung wurde veröffentlicht, ohne dass die lernende Person sie geprüft hat.

## Zusatzziele

- Finden Sie, was noch nicht übersetzt ist ([Wie finde ich, was nicht übersetzt ist?](../handbuch/wie-finde-ich-was-noch-nicht-uebersetzt-ist.md)).
- Fügen Sie eine Sprache mit Schreibrichtung von rechts nach links hinzu und sehen Sie sich die
  Administrationsoberfläche damit an ([Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md#sprachen-mit-schreibrichtung-von-rechts-nach-links)).
- Legen Sie eine Übersetzerrolle an, die nur Ihre Sprache bearbeiten darf, mit der Einschränkung **Sprache**
  ([Inhaltssprachen, Abschnitt 7](../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)), und testen
  Sie sie in [Projekt 9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md).

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Als Lernende übersetzen](als-lernende-uebersetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
