---
title: Datentypen wählen
audience: website-creator, developer
category: website-creation
order: 190
summary: Welchen der 35 mitgelieferten Datentypen Sie für welche Art von Feld wählen - Text, Rich Text, Datumsangaben, Zahlen, Auswahlen, Relationen, Medien, Preise, Benutzer - mit dem, was jeder speichert und wie er angezeigt wird.
keywords: Datentyp, Datentypen, Feld, Attribut, Textzeile, Rich Text, Relation, Bild, Preis, Website-Ersteller, datatype, datatypes, ezstring, eztext, ezxmltext, ezdate, ezdatetime, ezboolean, ezinteger, ezfloat, ezselection, ezobjectrelationlist, ezimage, ezbinaryfile, ezurl, ezemail, ezprice, ezkeyword, ezmatrix, ezcountry, website creator
type: help
---
# Datentypen wählen

Diese Seite richtet sich an Website-Ersteller, die Klassen Attribute hinzufügen. Ein Datentyp entscheidet, wie ein Feld
bearbeitet, gespeichert, validiert, durchsucht und angezeigt wird, und er lässt sich später nicht ohne Datenmigration
ändern. Wählen Sie danach, was Sie mit dem Wert tun werden.

![Die Seite einer Klasse: ihre Attribute, jedes mit Datentyp, Bezeichner und der Angabe, ob es Pflichtfeld, durchsuchbar oder übersetzbar ist](../../../../eng-us/help/images/creators/class-view.webp)

## Inhalt

1. [Die mitgelieferten Datentypen](#die-mitgelieferten-datentypen)
2. [Nach Bedarf wählen](#nach-bedarf-wählen)
3. [Datentypen aus Erweiterungen](#datentypen-aus-erweiterungen)
4. [Ein Feld in einem Template anzeigen](#ein-feld-in-einem-template-anzeigen)
5. [Stolperfallen](#stolperfallen)

## Die mitgelieferten Datentypen

Die für neue Attribute verfügbaren Datentypen stehen in `AvailableDataTypes[]` in `settings/content.ini`; die
mitgelieferte Liste hat 35 Einträge:

| Gruppe | Datentypen |
|---|---|
| Text | `ezstring` (Textzeile), `eztext` (Textblock), `ezxmltext` (Rich Text), `ezidentifier`, `ezisbn` |
| Datum und Uhrzeit | `ezdate`, `ezdatetime`, `eztime` |
| Zahlen und Ja/Nein | `ezinteger`, `ezfloat`, `ezboolean` |
| Auswahlen | `ezselection`, `ezenum`, `ezcountry`, `ezkeyword` |
| Relationen | `ezobjectrelation` (ein Objekt), `ezobjectrelationlist` (mehrere) |
| Medien | `ezimage`, `ezbinaryfile`, `ezmedia` |
| Kontakt und Links | `ezemail`, `ezurl`, `ezauthor` |
| Strukturiert | `ezmatrix` (eine Tabelle) |
| Shop | `ezprice`, `ezmultiprice`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`, `ezproductcategory` |
| System | `ezuser`, `ezsubtreesubscription`, `ezinisetting`, `ezpackage` |

Die Implementierungen liegen in `kernel/classes/datatypes/<name>/`.

## Nach Bedarf wählen

| Sie brauchen | Wählen Sie | Nicht |
|---|---|---|
| Einen Titel, einen Namen | `ezstring` | `ezxmltext` |
| Einen Teaser oder eine Zusammenfassung für Listen und Meta-Tags | `eztext` | `ezxmltext` (Markup in Meta-Tags) |
| Einen Haupttext mit Überschriften, Links, eingebetteten Bildern | `ezxmltext` (bearbeitet mit dem Online-Editor) | `eztext` |
| Ein Datum zum Sortieren oder Filtern | `ezdate` oder `ezdatetime` | Text |
| Einen Ja/Nein-Schalter (hervorgehoben, Teaser ausblenden) | `ezboolean` | eine Auswahl mit zwei Einträgen |
| Eine oder mehrere Auswahlen aus einer festen Liste | `ezselection` | freien Text |
| Ein Land | `ezcountry` | `ezselection` mit eigener Liste |
| Ein einmal verwendetes Bild | `ezimage` in der Klasse | |
| Ein an vielen Stellen wiederverwendetes Bild | `ezobjectrelation` auf ein Image-Objekt | eine Kopie in jedem Objekt |
| Mehrere verwandte Elemente (Autoren, verwandte Artikel) | `ezobjectrelationlist` | eine Textliste mit Namen |
| Einen Download | `ezbinaryfile` | |
| Einen Link auf eine andere Website | `ezurl` | Text |
| Eine E-Mail-Adresse | `ezemail` (validiert) | `ezstring` |
| Eine Tabelle mit Werten | `ezmatrix` | Rich-Text-Tabellen, wenn die Werte Daten sind |
| Freie Schlüsselwörter | `ezkeyword` oder besser `eztags` aus der Erweiterung eztags | |
| Einen Preis | `ezprice` (mit Mehrwertsteuer) oder `ezmultiprice` (mehrere Währungen) | `ezfloat` |

## Datentypen aus Erweiterungen

Erweiterungen fügen Datentypen hinzu; sie registrieren sie in ihren eigenen Einstellungen. Hier dokumentierte Beispiele:
[eztags](../../../funktionen/6.0/erweiterungen/eztags.md) (ein Tag-Baum mit Autovervollständigung),
[enhancedselection2](../../../funktionen/6.0/erweiterungen/enhancedselection2.md),
[enhancedezbinaryfile](../../../funktionen/6.0/erweiterungen/enhancedezbinaryfile.md),
[ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md) (ein Kartenstandort),
[ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md). Ein eigener Datentyp ist eine Aufgabe für
Anwendungsentwickler ([Einen eigenen Datentyp schreiben](../anwendungsentwickler/eigene-datentypen.md)).

## Ein Feld in einem Template anzeigen

```text
{attribute_view_gui attribute=$node.data_map.body}
```

rendert das Feld mit dem View-Template seines Datentyps (`content/datatype/view/<datatype>.tpl`, überschreibbar). Für
einen reinen Wert lesen Sie `.content` oder prüfen `.has_content`:

```text
{if $node.data_map.image.has_content}
  {attribute_view_gui attribute=$node.data_map.image image_class='medium'}
{/if}
```

## Stolperfallen

- **Rich Text, wo reiner Text gebraucht wird.** Teaser in Rich Text landen als Markup in Feeds und Meta-Tags.
- **Zahlen als Text.** Text sortiert „10“ vor „9“.
- **Auswahlen für Dinge, die sich ändern.** Eine Option hinzuzufügen ist leicht; eine umzubenennen benennt sie in jedem
  Objekt um, und eine zu entfernen lässt Objekte ohne Wert zurück. Für eine Liste, die die Redaktion pflegt, verwenden
  Sie Tags oder Relationen.
- **Kopien statt Relationen.** Ein kopiertes Bild oder eine kopierte Adresse muss in jeder Kopie aktualisiert werden.
- **Alte Mediendatentypen.** Die Klassen für Flash, QuickTime, Windows Media und RealVideo aus älteren Paketen zeigen
  Inhalte, die moderne Browser nicht abspielen; verwenden Sie Videodateien oder Einbettungen.

## Verwandte Seiten

- [Ihr Inhaltsmodell entwerfen](ihr-inhaltsmodell-entwerfen.md)
- [Relationen und Wiederverwendung](relationen-und-wiederverwendung.md)
- [Bilder und Bildvarianten](bilder-und-bildvarianten.md)
- [Der Lebenszyklus eines Datentyps](../anwendungsentwickler/lebenszyklus-eines-datentyps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
