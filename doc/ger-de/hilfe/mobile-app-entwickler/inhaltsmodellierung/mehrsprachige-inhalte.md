---
title: Mehrsprachige Inhalte
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 430
summary: Eine App aus Exponential-Inhalten in mehreren Sprachen bedienen - Locales wie eng-US und ger-DE, der Parameter Translation bei REST-Lesezugriffen, languageLocale beim Anlegen, die Sprachen der Website aus den explanguage-Diensten, Übersetzungen eines Objekts, Ersatzsprachen und nicht übersetzte Elemente und die Zuordnung von Gerätesprachen zu Locales.
keywords: mehrsprachig, Sprachen, Übersetzung, Locale, Parameter Translation, languageLocale, eng-US, ger-DE, explanguage, prioritized, exptranslation, Ersatzsprache, immer verfügbar, Siteaccess, Gerätesprache, Mobile App, multilingual, languages, translation, locale, fallback, always available, siteaccess, device language, mobile app
type: help
---
# Mehrsprachige Inhalte

Diese Seite richtet sich an App-Entwickler, deren App Inhalte in mehr als einer Sprache zeigt. Sie erklärt, wie
Exponential Sprachen benennt, wie Sie die APIs nach einer Sprache fragen und was mit Elementen geschieht, die nicht
übersetzt sind.

## Inhalt

1. [Wie Exponential Sprachen speichert](#wie-exponential-sprachen-speichert)
2. [Eine Sprache über REST lesen](#eine-sprache-über-rest-lesen)
3. [Die Sprachen der Website](#die-sprachen-der-website)
4. [In einer Sprache schreiben](#in-einer-sprache-schreiben)
5. [Ersatzsprachen in der App](#ersatzsprachen-in-der-app)

## Wie Exponential Sprachen speichert

Jedes Objekt kann Übersetzungen in mehreren Inhaltssprachen enthalten und hat eine Hauptsprache. Sprachen werden mit
Locale-Codes wie `eng-US`, `eng-GB`, `ger-DE`, `fre-FR`, `nor-NO` benannt. Eine Website (ein Siteaccess) zeigt jedes
Objekt in der ersten Sprache ihrer `SiteLanguageList[]`, die das Objekt hat; Objekte in keiner dieser Sprachen sind
ausgeblendet, sofern sie nicht „immer verfügbar“ sind
([Eine mehrsprachige Website bauen](../../ersteller/website-ersteller/mehrsprachige-websites.md#wie-sprachen-funktionieren)).

## Eine Sprache über REST lesen

Ergänzen Sie `Translation=<locale>` bei einem Lesezugriff auf einen Knoten, ein Objekt oder ein Feld:

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?ResponseGroups=Fields&Translation=ger-DE"
```

Der Controller setzt die aktive Sprache des Inhalts auf diese Locale, bevor er die Antwort aufbaut. Die `links` in der
Antwort tragen den Parameter weiter, sodass Sie in der Sprache bleiben, wenn Sie ihnen folgen. Drei Vorbehalte aus dem
Code:

- Der Parser übernimmt den Wert so, wie er kommt; ein Kommentar in `http_parser.php` vermerkt, dass die Locale noch
  nicht geprüft wird. Senden Sie nur Locales, die die Installation hat.
- Die Route `list` setzt für ihre Unterknoten keine Sprache; deren Namen und Felder kommen in der Standardsprache. Der
  `link` jedes Unterknotens trägt `Translation`, sodass ein Detail-Lesezugriff auf den Unterknoten in der richtigen
  Sprache erfolgt.
- Ohne den Parameter wird die Sprache nach den Einstellungen der Installation für die Anfrage gewählt; prüfen Sie, was
  Ihre Installation liefert, bevor Sie sich darauf verlassen.

## Die Sprachen der Website

| Dienst | Liefert | Zugriff |
|---|---|---|
| `explanguage::prioritized` | die Sprachen des Siteaccess in Prioritätsreihenfolge | öffentlich |
| `explanguage::top` | die erste davon | öffentlich |
| `explanguage::languages` | jede Inhaltssprache mit der Anzahl der Objekte | öffentlich |
| `exptranslation::ofObject::<object id>` | die Übersetzungen eines Objekts | `content/read` |
| `exptranslation::missing::<object id>` | die Sprachen der Website, die einem Objekt fehlen | `content/read` |
| `expnode::names::<node id>` | den Namen des Knotens in jeder Sprache | `content/read` |

Bauen Sie damit das Sprachmenü der App vom Server aus, statt einer Liste, die in die App kompiliert ist.

## In einer Sprache schreiben

- Das Anlegen über REST braucht `languageLocale` ([Inhalte anlegen und entfernen](../rest-api/inhalte-anlegen-und-entfernen.md)).
- `expnode::create` und `expnode::update` nehmen ein POST-Feld `language`.
- Eine Übersetzung eines vorhandenen Elements, mit der eigenen Richtlinie `content/translate`, ist
  `exptranslation::translate`.

## Ersatzsprachen in der App

1. Ordnen Sie die Gerätesprache einer Locale zu, die die Website hat (`de` zu `ger-DE`); halten Sie die Zuordnung in der
   Konfiguration, nicht im Code.
2. Lesen Sie mit `Translation=<locale>`. Kommt ein Feld leer zurück, während die Hauptsprache es hat, entscheiden Sie je
   Feld: die Hauptsprache mit einem Hinweis zeigen oder das Element ausblenden.
3. Speichern Sie je Locale zwischen: Derselbe Knoten in zwei Sprachen ergibt zwei Cache-Einträge
   ([Caching in der App](../offline-und-leistung/caching-in-der-app.md)).
4. Rechts-nach-links-Sprachen werden von den Inhalten in Exponential unterstützt; das Layout der App muss sich für sie
   spiegeln ([Rechts-nach-links-Websites](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md)).

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen in Exponential](../../sprachen.md)
- [In mehreren Sprachen und Märkten verkaufen](../../unternehmen/mehrsprachig/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
