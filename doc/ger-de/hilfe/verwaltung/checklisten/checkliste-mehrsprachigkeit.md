---
title: Checkliste Mehrsprachigkeit
audience: government, admin, editor, project-manager
category: public-sector
order: 70
summary: Eine Checkliste für öffentliche Stellen, die mit Exponential in mehreren Sprachen veröffentlichen - Inhaltssprachen, Adressen, der Sprachumschalter, Schreibrichtung von rechts nach links, die Oberflächensprache der Administration, Übersetzungsablauf und Qualität.
keywords: Mehrsprachigkeit, Sprachen, Übersetzung, Amtssprachen, Sprachumschalter, von rechts nach links, RTL, Oberflächensprache, hreflang, Übersetzungsablauf, öffentlicher Sektor, multilingual, languages, translation, official languages, language switcher, right to left, interface language, translation workflow, public sector
type: help
---
# Checkliste Mehrsprachigkeit

Diese Checkliste richtet sich an die Website-Administration und die leitende Redaktion öffentlicher Stellen, die in
mehr als einer Sprache veröffentlichen müssen, zum Beispiel in Amts- oder Minderheitensprachen. Sie folgt
[Inhaltssprachen](../../../anleitungen/inhaltssprachen.md),
[Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md) und
[Mehrsprachige Websites](../../../../eng-us/help/creators/website-creators/multilingual-sites.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Sprachliche
> Pflichten hängen von Ihrer Rechtsordnung ab; prüfen Sie sie mit Ihrer Rechtsberatung.

## Inhalt

1. [Planen](#planen)
2. [Einrichten](#einrichten)
3. [Sprachen von rechts nach links](#sprachen-von-rechts-nach-links)
4. [Bearbeiten und übersetzen](#bearbeiten-und-übersetzen)
5. [Prüfen](#prüfen)

## Planen

- [ ] Listen Sie die Sprachen auf, in denen Sie veröffentlichen müssen, und welche Inhalte in jeder existieren müssen.
- [ ] Entscheiden Sie, was Besucher sehen, wenn eine Übersetzung fehlt: eine andere Sprache oder nichts.
- [ ] Legen Sie das Adressmuster pro Sprache fest (Pfadpräfix, Host) ([Sprachumschalter und Sprach-URLs](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md), [Zuordnung von Siteaccess zu Host und Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).
- [ ] Entscheiden Sie, wer übersetzt: Mitarbeitende, ein Dienstleister oder beide, und wie Übersetzungen freigegeben werden.

## Einrichten

- [ ] Inhaltssprachen unter **Setup > Sprachen** hinzugefügt ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).
- [ ] Wo nötig ein Siteaccess pro Sprache, mit der richtigen Locale.
- [ ] Ein Sprachumschalter im Design ([Sprachumschalter und Sprach-URLs](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md)).
- [ ] Oberflächensprachen für Mitarbeitende: **Setup > Oberflächensprachen** ([Einstellungen zum Umschalten der Oberfläche](../../../anleitungen/ui-umschalter-einstellungen.md)); prüfen Sie, welche Übersetzungen der Administration vollständig sind ([Sprachen](../../sprachen.md)).
- [ ] Die Testsprache Lorem Ipsum ist in der Produktion nicht eingeschaltet geblieben ([Die Testsprache Lorem Ipsum](../../../anleitungen/testsprache-lorem-ipsum.md)).

## Sprachen von rechts nach links

- [ ] Seiten in Sprachen mit Schreibrichtung von rechts nach links setzen `dir="rtl"` ([Sprachen mit Schreibrichtung von rechts nach links](../../../../eng-us/features/6.0/right-to-left.md)).
- [ ] Das öffentliche Design spiegelt sein Layout korrekt ([Websites von rechts nach links](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)).
- [ ] Gemischter Text (Namen, Zahlen, Adressen) wird in der richtigen Reihenfolge angezeigt.

## Bearbeiten und übersetzen

- [ ] Die Redaktion weiß, wie sie eine Übersetzung hinzufügt und sieht, welche existieren (der Reiter Übersetzungen eines Objekts) ([Redaktion 201, Modul 1](../../../../eng-us/help/trainers/editors-201/module-01-translations-and-languages/README.md)).
- [ ] Übersetzungen durchlaufen eine Freigabe, wo Ihre Regeln es verlangen ([Workflows](../../../anleitungen/workflows.md)).
- [ ] Bilder mit Text haben in jeder Sprache einen Alternativtext.
- [ ] Dokumente sind in jeder Sprache angehängt, oder es gibt einen Hinweis, wo nur eine Sprache existiert.
- [ ] Maschinell unterstützte Entwürfe, falls verwendet, werden von einem Menschen durchgesehen; Exponential Intelligence schreibt nur Entwürfe und veröffentlicht nie ([das Glossar von Exponential](../../../../eng-us/glossary.md#e)).

## Prüfen

- [ ] Startseite, Navigation und Suche jeder Sprache funktionieren.
- [ ] `<html lang>` passt zur Sprache jeder Seite.
- [ ] URL-Aliasse existieren in jeder Sprache ([URL-Aliasse und sprechende URLs](../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md)).
- [ ] Fehlende Übersetzungen sind aufgelistet und zugewiesen; jedes Quartal überprüft.
- [ ] Barrierefreiheit in jeder Sprache geprüft, einschließlich von rechts nach links ([Checkliste Barrierefreiheit](checkliste-barrierefreiheit.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md)
- Der Bereich Sprachen dieser Hilfe für Verwaltungen (von einem anderen Autor geschrieben)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
