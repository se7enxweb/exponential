---
title: Anforderungen an Sprache und Mehrsprachigkeit
audience: government, project-manager, editor, admin
category: public-sector
order: 70
summary: Formulierungen für Amts- und Minderheitensprachen in einer Ausschreibung für ein CMS - Übersetzungen von Inhalten, Schriften von rechts nach links, Sprachen der Administration, Sprachadressen und Kodierungen - mit dem, was Exponential dokumentiert.
keywords: Sprachen, Mehrsprachigkeit, Übersetzung, Amtssprachen, Minderheitensprachen, rechts nach links, RTL, Arabisch, Hebräisch, Unicode, UTF-8, Locale, Sprache der Administration, Sprachumschalter, Ausschreibung, languages, multilingual, translation, official languages, minority languages, right to left, administration language, language switcher, tender
type: help
---
# Anforderungen an Sprache und Mehrsprachigkeit

Diese Seite richtet sich an öffentliche Stellen, die in mehr als einer Sprache veröffentlichen: in Amtssprachen,
Minderheitensprachen oder den Sprachen der Gemeinschaften, denen sie dienen. Sie gibt Formulierungen für Anforderungen
und nennt die Seiten von Exponential, die jede Fähigkeit dokumentieren, aus den [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md) und
[Sprachen in Exponential](../../../../../eng-us/languages.md).

## Inhalt
1. [Fragen, die Sie zuerst beantworten sollten](#fragen-die-sie-zuerst-beantworten-sollten)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Was Exponential dokumentiert](#was-exponential-dokumentiert)
4. [Bekannte Lücken, nach denen Sie fragen sollten](#bekannte-lücken-nach-denen-sie-fragen-sollten)
5. [Fallstricke](#fallstricke)

## Fragen, die Sie zuerst beantworten sollten

- Welche Sprachen muss jede Seite haben, und welche nur manche Seiten?
- Wird eine Sprache von rechts nach links geschrieben (zum Beispiel Arabisch, Hebräisch, Persisch, Urdu)?
- Braucht das Personal die **Administration** selbst in seiner Sprache oder nur die Inhalte?
- Wer übersetzt: das Personal, ein Übersetzungsbüro oder eine Maschine mit menschlicher Prüfung?
- Wie wählen Besucher eine Sprache: über die Adresse (`example.gov/fr/...`), über den Host (`fr.example.gov`) oder über
  einen Umschalter?

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| LAN-01 | Jedes Inhaltselement kann in jeder der Sprachen <Liste> existieren; jede Übersetzung wird für sich veröffentlicht. |
| LAN-02 | Die Redaktion sieht, welche Übersetzungen eines Elements existieren und welche fehlen. |
| LAN-03 | Eine Übersetzung kann vom Text einer anderen Sprache ausgehen. |
| LAN-04 | Sprachen, die von rechts nach links geschrieben werden, werden auf der öffentlichen Website und im Editor in der richtigen Richtung angezeigt. |
| LAN-05 | Die Sprache spiegelt sich in der Adresse oder dem Host der Seite wider, und Seiten geben ihre Sprache gegenüber Browsern und assistiven Technologien an. |
| LAN-06 | Die Administrationsoberfläche ist in <Sprachen> verfügbar; jeder Benutzer kann sie wählen. |
| LAN-07 | Aller Text wird als Unicode (UTF-8) gespeichert und ausgeliefert, einschließlich Zeichen außerhalb der Basic Multilingual Plane. |
| LAN-08 | Datumsangaben, Zahlen und Währungen folgen den Konventionen jeder Sprache und jedes Landes. |

## Was Exponential dokumentiert

| Id | Nachweis |
|---|---|
| LAN-01, LAN-02, LAN-03 | [Sprachen](../../../sprachen.md) (Übersetzungsmarkierungen, „Übersetzen basierend auf“), [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| LAN-04 | [Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md): `dir="rtl"` in Inhalten und Administration; [Websites von rechts nach links](../../../../../eng-us/help/creators/website-creators/right-to-left-sites.md). Unicode Bidirectional Algorithm: **Erfüllt** (über den Browser) |
| LAN-05 | [Zuordnung von Siteaccesses nach Host und Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md), [Sprachumschalter und Sprach-URLs](../../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md); an Browser gesendete BCP-47-Tags: **Erfüllt**; die Pagelayouts der Administration setzen `<html lang>` ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)) |
| LAN-06 | [UI-Umschalter: Sprachen der Oberfläche](../../../../../eng-us/features/6.0/ui-switch-languages.md); [Sprachen in Exponential](../../../../../eng-us/languages.md) zählt die mitgelieferten Übersetzungen der Administration |
| LAN-07 | Interne Kodierung UTF-8 **Erfüllt**; 4-Byte-UTF-8 unter MySQL/MariaDB **Teilweise erfüllt** ([Zusätzliche Zeichen im Rich Text](../../../../../eng-us/features/6.0/rich-text-supplementary-characters-and-tex-source.md)) |
| LAN-08 | Locale-Dateien aus den Daten von Unicode CLDR erzeugt, 268 Locale-Dateien mit Stand Oktober 2026 ([Sprachen in Exponential](../../../../../eng-us/languages.md)) |

## Bekannte Lücken, nach denen Sie fragen sollten

Aus den [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md):

- **Pluralformen** (CLDR-Pluralregeln): **Nicht erfüllt**. Sprachen mit mehr als zwei Pluralformen lesen sich in der
  Oberfläche bei manchen Anzahlen holprig.
- **Verbindungen zu MySQL und MariaDB** verwenden Drei-Byte-`utf8`; bevorzugen Sie SQLite oder PostgreSQL, oder fragen
  Sie, wie der Bieter Zeichen wie Emoji und seltene Schriften behandelt ([Datenbanken](../../../../installation/09-datenbanken.md)).
- **Übersetzungen der Administration** sind nur für manche Sprachen vollständig; prüfen Sie die benötigte Sprache in
  [Sprachen in Exponential](../../../../../eng-us/languages.md), bevor Sie LAN-06 zu einem „muss“ machen.
- **64-Bit-PHP** wird für mehr als 30 Inhaltssprachen benötigt ([Anforderungen](../../../../installation/02-voraussetzungen.md)).

## Fallstricke

- „Alle Seiten in allen Sprachen“ zu einem „muss“ machen, wenn Ihr Übersetzungsbudget das nicht leisten kann.
- Die Sprache von PDFs, Newslettern und System-E-Mails vergessen.
- Maschinelle Übersetzung als fertige Übersetzung behandeln. Gehört KI-gestütztes Entwerfen zum Umfang, zeigt der
  Trainerkurs [Exponential Intelligence](../../../../../eng-us/help/trainers/exponential-intelligence/README.md), wie Entwürfe geprüft werden.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zur Mehrsprachigkeit](../ausschreibungsvorlagen/klausel-mehrsprachigkeit.md)
- [Mehrsprachige Websites](../../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [Übersetzungen und Sprachen](../../../../../eng-us/features/6.0/translations-and-languages.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
