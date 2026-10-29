---
title: Sprachen auf einen Blick
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: Was Exponential einer öffentlichen Stelle bietet, die Menschen in mehreren Sprachen bedient - Inhaltssprachen, ein Siteaccess und eine Adresse pro Sprache, Ersatzregeln, Rollen für Übersetzer, Schriften von rechts nach links, Oberflächenübersetzungen und die bekannten Lücken, in einer Übersicht.
keywords: Sprachen, mehrsprachig, Amtssprachen, Übersetzung, Inhaltssprachen, Oberflächensprache, von rechts nach links, Locales, Siteaccess, Ersatzsprache, Übersicht, languages, multilingual, official languages, translation, content languages, interface language, right to left, fallback, overview
type: help
---
# Sprachen auf einen Blick

Diese Seite richtet sich an Führungskräfte und Administratoren, die mit Exponential eine mehrsprachige öffentliche
Website oder Administration planen. Sie listet auf, was das Produkt kann, mit der Dokumentation hinter jedem Punkt,
und die Lücken, die Sie einplanen sollten. Die anderen Seiten dieses Bereichs erklären jedes Thema für öffentliche
Stellen.

## Inhalt

1. [Was Exponential mitbringt](#was-exponential-mitbringt)
2. [Die Zahlen](#die-zahlen)
3. [Bekannte Lücken](#bekannte-lücken)
4. [Wo Sie anfangen](#wo-sie-anfangen)

## Was Exponential mitbringt

| Bedarf | Funktion | Dokumentation |
|---|---|---|
| Inhalte in mehreren Sprachen | jedes Objekt enthält eine Übersetzung pro Inhaltssprache | [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| Eine Website pro Sprache | ein Siteaccess pro Sprache mit seiner `SiteLanguageList[]` | [Eine mehrsprachige Website bauen](../../ersteller/website-ersteller/mehrsprachige-websites.md) |
| Eine Adresse pro Sprache | ein Pfad (`/de/`), ein Host oder beides | [Sprachumschalter und Adressen pro Sprache](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md) |
| Die Startseite in der Sprache des Besuchers | Weiterleitung nach Browsersprache mit `DefaultHostUriMatchMapItems[]` | [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md) |
| Ein Sprachumschalter | `TranslationSA[]` und der Operator `language_switcher` | dieselbe Seite |
| Was Besucher sehen, wenn eine Übersetzung fehlt | die Reihenfolge von `SiteLanguageList[]`, „Immer verfügbar“ | [Inhaltssprachen, Abschnitt 6](../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar) |
| Übersetzer, beschränkt auf ihre Sprache | Richtlinien mit der Einschränkung **Sprache** | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Schriften von rechts nach links | Schreibrichtung aus der Locale-Datei; die Administration spiegelt sich selbst | [Sprachen mit Schreibrichtung von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md) |
| Die Administration in der Sprache der Mitarbeitenden | Oberflächenübersetzungen und eine Wahl pro Benutzer | [Sprachen (für Redakteure)](../../sprachen.md) |
| Datum, Zahlen, Währung pro Sprache | Locale-Dateien | [Sprachen in Exponential](../../../sprachen.md) |

## Die Zahlen

- **Locales**: 368 Locale-Dateien werden in `share/locale/` mitgeliefert; jede kann zu einer Inhaltssprache werden
  ([Sprachen in Exponential](../../../sprachen.md)).
- **Von rechts nach links**: 43 davon tragen `TextDirection=rtl` (gezählt in `share/locale/`; die Liste steht in
  Abschnitt 9 von [Sprachen in Exponential](../../../sprachen.md#9-sprachen-die-von-rechts-nach-links-geschrieben-werden)).
- **Inhaltssprachen pro Installation**: höchstens 62 unter 64-Bit-PHP (siehe „Die Grenze von 62 Sprachen“).
- **Übersetzungen der Administration**: die Verzeichnisse von `share/translations/`, mit sehr unterschiedlicher
  Vollständigkeit; die aktuelle Tabelle steht in
  [Sprachen in Exponential, Abschnitt 4](../../../sprachen.md#4-alle-übersetzungen-der-administrationsoberfläche).

## Bekannte Lücken

Aus den [Standards zur Internationalisierung](../../../standards/internationalisierung.md#bekannte-lücken):

- Eine Standardverbindung zu MySQL oder MariaDB verwendet das 3-Byte-`utf8`; manche Zeichen (viele historische
  Schriften, Emoji) brauchen `utf8mb4`, das die Administration einrichtet.
- Keine Pluralformen im Übersetzungssystem der Oberfläche.
- Eine Zeitzone pro Installation oder Siteaccess, keine pro Benutzer.
- Einige Stylesheets von Erweiterungen berücksichtigen die Schreibrichtung von rechts nach links noch nicht.
- `hreflang`-Links zwischen Sprachfassungen werden von den mitgelieferten Designs nicht ausgegeben; Ihr Pagelayout
  fügt sie hinzu.
- Kein Übersetzungsspeicher und keine maschinelle Übersetzung im Kernel; der optionale Assistent `expai` bietet eine
  Aktion zum Übersetzen von Feldern ([expai](../../../funktionen/6.0/erweiterungen/expai.md)).

## Wo Sie anfangen

Lesen Sie zuerst „Inhaltssprachen und Oberflächensprachen“, dann „Zweite und dritte Amtssprachen“ für die typische
Einrichtung im öffentlichen Sektor und die Checkliste Sprachen am Ende dieses Bereichs.

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen in Exponential](../../../sprachen.md)
- [Standards zur Internationalisierung](../../../standards/internationalisierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
