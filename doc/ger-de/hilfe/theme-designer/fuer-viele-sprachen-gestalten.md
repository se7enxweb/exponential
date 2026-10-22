---
title: "Für viele Sprachen gestalten"
audience: theme-designer
category: theme-design
order: 24
summary: Die Designentscheidungen hinter einer mehrsprachigen Exponential-Website - ein Siteaccess pro Sprache, Textausdehnung, Schriftsysteme und Schriften, von rechts nach links, Daten und Zahlen, der Sprachumschalter, fehlende Übersetzungen und Bilder mit Text - mit Links zum Tutorial und zu den Seiten über Rechts-nach-links-Designs.
keywords: Theme-Designer, mehrsprachig, Sprachen, Übersetzung, Textausdehnung, Schriftsysteme, Schriften, von rechts nach links, RTL, Sprachumschalter, Locale, Daten, Internationalisierung, theme designer, multilingual, languages, translation, text expansion, scripts, fonts, right to left, language switcher, locale, dates, internationalization
type: help
---
# Für viele Sprachen gestalten

Diese Seite ist für Theme-Designer, deren Website oder Theme in mehr als einer Sprache verwendet wird. Exponential
speichert eine Übersetzung pro Objekt und liefert jede Sprache meist über einen eigenen Siteaccess mit eigener Locale
aus. Die technischen Schritte stehen in [Tutorial: ein Theme internationalisieren](../designer/theme-design/tutorial-ein-theme-internationalisieren.md),
[Template-Texte übersetzen](../designer/vorlagen/template-texte-uebersetzen.md) und, für Arabisch, Hebräisch und
andere Schriften, die von rechts nach links laufen, in [Rechts-nach-links-Designs](../designer/theme-design/rechts-nach-links-designs.md).
Diese Seite listet die Entscheidungen auf, die ein Designer vor diesen Schritten trifft.

## Inhalt

1. [Wie Sprachen auf die Seite gelangen](#wie-sprachen-auf-die-seite-gelangen)
2. [Textausdehnung](#textausdehnung)
3. [Schriftsysteme und Schriften](#schriftsysteme-und-schriften)
4. [Von rechts nach links](#von-rechts-nach-links)
5. [Daten, Zahlen und Namen](#daten-zahlen-und-namen)
6. [Der Sprachumschalter](#der-sprachumschalter)
7. [Fehlende Übersetzungen](#fehlende-übersetzungen)
8. [Bilder und Symbole mit Bedeutung](#bilder-und-symbole-mit-bedeutung)
9. [Eine Checkliste für Sprachen](#eine-checkliste-für-sprachen)

## Wie Sprachen auf die Seite gelangen

| Text | Kommt aus | Übersetzt von |
|---|---|---|
| Inhalte (Titel, Haupttext) | der Übersetzung des Objekts in dieser Sprache | Redakteuren |
| Oberflächentext in Templates ("Weiterlesen", "Suche") | Übersetzungsdateien des Designs, über `i18n` | Übersetzern |
| Daten und Zahlen | der Locale des Siteaccess | automatisch |

Ihr Design muss alle drei in ihrer längsten Form aufnehmen. Hintergrund zu Übersetzungen von Inhalten:
[Übersetzungen und Sprachen](../../../eng-us/help/cms-developers/translations-and-languages.md).

## Textausdehnung

Übersetzter Oberflächentext ist oft länger als der englische; deutsche und finnische Beschriftungen sind lang, und
manche Sprachen brauchen mehr Höhe. Kurze Beschriftungen wachsen relativ am stärksten. Deshalb:

- Schaltflächen, Reiter und Menüeinträge wachsen mit ihrem Text; keine festen Breiten;
- vermeiden Sie Designs, die nur funktionieren, wenn eine Beschriftung in eine Zeile passt;
- testen Sie mit der längsten Sprache der Website oder, wo es noch keine Übersetzung gibt, mit englischem Text, der um
  etwa ein Drittel verlängert ist.

## Schriftsysteme und Schriften

- Prüfen Sie, dass die gewählten Schriften jedes Schriftsystem der Website abdecken. Eine Schrift ohne griechische oder
  kyrillische Glyphen fällt für diese Zeichen mitten im Satz auf eine Systemschrift zurück.
- Planen Sie für Schriftsysteme wie Chinesisch, Japanisch, Koreanisch, Arabisch oder Devanagari einen eigenen
  Schriftstapel, pro Sprache mit `:lang()`-Selektoren gewählt. Die Administration macht das mit eigenen Tokens für
  internationale und CJK-Stapel ([Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md#systemschriften)).
- Manche Schriftsysteme brauchen mehr Zeilenhöhe oder eine größere Schrift, um ebenso gut lesbar zu sein
  ([Typografische Skala](typografische-skala.md#andere-schriften-und-sprachen)).
- Kursiv und Großbuchstaben gibt es nicht in jedem Schriftsystem; lassen Sie sie nicht allein eine Bedeutung tragen.

## Von rechts nach links

Eine Sprache, die von rechts nach links läuft, spiegelt das Layout: Navigation, Symbole mit Richtung (Pfeile, "weiter"),
die Reihenfolge von Spalten. Ein Design, das mit logischen CSS-Eigenschaften gebaut ist, braucht sehr wenig zusätzliche
Arbeit ([Logische CSS-Eigenschaften](../designer/theme-design/logische-css-eigenschaften.md)). Zeichnen Sie die wichtigen
Bildschirme mindestens einmal gespiegelt, und kennzeichnen Sie Symbole, die nicht gespiegelt werden dürfen (eine
Abspielschaltfläche, ein Logo, ein Häkchen).
[Tutorial: ein Theme für rechts nach links](../designer/theme-design/tutorial-ein-rechts-nach-links-theme.md) und
[Checkliste: von rechts nach links](../designer/theme-design/checkliste-rechts-nach-links.md) behandeln den Rest.

## Daten, Zahlen und Namen

Daten, Uhrzeiten und Zahlen werden von der Locale des Siteaccess formatiert
([Daten, Zahlen und l10n](../designer/vorlagen/datum-zahlen-und-l10n.md)). Gestalten Sie Datumsbereiche für das
längste Format ("Donnerstag, 24. September 2026" ist weit länger als "24.09.2026", und manche Sprachen schreiben es noch
länger). Namen von Menschen und Orten unterscheiden sich in Länge und Reihenfolge; gestalten Sie keine Autorenzeile, die
Vor- und Nachnamen in einer festen Reihenfolge braucht.

## Der Sprachumschalter

- Jede Sprache in sich selbst benannt ("Deutsch", "English", "Français"), nicht in der aktuellen Sprache und nicht als
  Flagge: Flaggen stehen für Länder, nicht für Sprachen.
- Die aktuelle Sprache markiert; die anderen verlinken auf dieselbe Seite in dieser Sprache, wenn es sie gibt.
- Mitgelieferte Designs bauen ihn mit dem Template-Operator `language_switcher`
  ([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md#umschalter-sprache-und-farbschema)).

## Fehlende Übersetzungen

Nicht jedes Objekt existiert in jeder Sprache. Je nach Einstellungen wird eine fehlende Übersetzung entweder gar nicht
gezeigt oder in einer anderen Sprache aus der Sprachliste des Siteaccess. Fragen Sie den Entwickler, was gilt, und
gestalten Sie dafür: Eine Liste kann in einer Sprache kürzer sein, und eine Seite kann Inhalte in einer anderen Sprache
als der des Rahmens zeigen. Die Seite sollte dann die Sprache des Inhalts kennzeichnen (`lang`-Attribut), damit
Screenreader ihn richtig aussprechen.

## Bilder und Symbole mit Bedeutung

- Vermeiden Sie Text in Bildern: Er lässt sich ohne neue Bilder nicht übersetzen. Setzen Sie Text als HTML über oder unter
  das Bild.
- Gesten, Farben und Symbole bedeuten in verschiedenen Kulturen Verschiedenes; bevorzugen Sie schlichte Symbole mit
  Beschriftung.

## Eine Checkliste für Sprachen

- [ ] Jede Sprache und jedes Schriftsystem der Website aufgeführt, mit geprüfter Abdeckung der Schriften.
- [ ] Wichtige Bildschirme in der längsten Sprache.
- [ ] Gespiegelte Bildschirme für Sprachen, die von rechts nach links laufen.
- [ ] Datums- und Zahlenbereiche für das längste Format bemessen.
- [ ] Sprachumschalter mit jeder Sprache in sich selbst.
- [ ] Kein Text fest in Bilder eingebaut.

## Verwandte Seiten

- [Tutorial: ein Theme internationalisieren](../designer/theme-design/tutorial-ein-theme-internationalisieren.md)
- [Der Operator i18n](../designer/vorlagen/der-i18n-operator.md)
- [Sprachen](../sprachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
