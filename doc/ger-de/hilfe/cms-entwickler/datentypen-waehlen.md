---
title: Datentypen wählen
audience: cms-developer
category: cms-development
order: 11
summary: Für CMS-Entwickler, die Inhalte modellieren, welcher Datentyp für welche Art von Feld passt. Die Seite behandelt die 35 Kernel-Datentypen und die Datentypen der Erweiterungen in der Standarddistribution, die Entscheidungen, die sich schwer rückgängig machen lassen, und wann sich ein eigener Datentyp lohnt.
keywords: Datentyp, Feldtyp, ezstring, eztext, ezxmltext, ezimage, ezbinaryfile, ezobjectrelationlist, ezselection, eztags, ezkeyword, ezdatetime, ezmatrix, ezurl, ezemail, ezboolean, ezprice, eigener Datentyp, Felder wählen, datatype, field type, custom datatype, choosing fields
type: help
---
# Datentypen wählen

Diese Seite richtet sich an Entwickler, die eine Liste von Feldern in Klassenattribute umsetzen. Ein Datentyp
bestimmt Speicherung, Prüfung, Bearbeitungsformular, View-Template, Suche und wie Importe Werte lesen; die Wahl ist
also eine Minute pro Feld wert. Einen Datentyp später zu ändern bedeutet ein neues Attribut und ein Migrationsskript
([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#eine-klasse-ändern-die-inhalte-hat)).

## Inhalt

1. [Woher die Liste kommt](#woher-die-liste-kommt)
2. [Die Kernel-Datentypen nach Zweck](#die-kernel-datentypen-nach-zweck)
3. [Datentypen aus der Standarddistribution](#datentypen-aus-der-standarddistribution)
4. [Entscheidungen, die sich schwer rückgängig machen lassen](#entscheidungen-die-sich-schwer-rückgängig-machen-lassen)
5. [Datentypen, die Sie in neuen Modellen meiden sollten](#datentypen-die-sie-in-neuen-modellen-meiden-sollten)
6. [Wann Sie einen eigenen Datentyp schreiben](#wann-sie-einen-eigenen-datentyp-schreiben)

## Woher die Liste kommt

Die im Klasseneditor angebotenen Datentypen stehen in `settings/content.ini [DataTypeSettings] AvailableDataTypes[]`:
35 im Kernel (`kernel/classes/datatypes/`). Aktive Erweiterungen fügen ihre in ihrer eigenen
`content.ini.append.php` hinzu. So sehen Sie, was Ihre Installation anbietet:

```bash
./console exp:ini get content.ini/DataTypeSettings/AvailableDataTypes --allow-root-user
```

## Die Kernel-Datentypen nach Zweck

Die Namen in Klammern zeigt der Klasseneditor an.

| Zweck | Datentyp | Hinweise |
|---|---|---|
| Ein Titel, ein Name, eine kurze Zeile | `ezstring` (Textzeile) | Höchstlänge je Attribut; meist die Quelle des Objektnamens |
| Reiner Text über mehrere Zeilen | `eztext` (Textabschnitt) | kein Markup; gut für Teaser, Adressen, Meta-Beschreibungen |
| Formatierter Text: Überschriften, Listen, Links, Einbettungen | `ezxmltext` (XML Block) | wird im Online Editor bearbeitet; siehe [Rich Text](rich-text.md) |
| Ganze Zahlen, Dezimalzahlen | `ezinteger` (Ganzzahl), `ezfloat` (Fließkommawert) | Minimum und Maximum je Attribut |
| Ja oder nein | `ezboolean` (Kontrollkästchen) | |
| Datum und Uhrzeit | `ezdate`, `ezdatetime`, `eztime` | `ezdatetime` für alles, was Sie nach Uhrzeit sortieren oder filtern |
| Eine oder mehrere Optionen aus einer festen Liste | `ezselection` (Auswahlliste) | Einfach- oder Mehrfachauswahl; die Optionen liegen in der Klasse |
| Freie Schlüsselwörter | `ezkeyword` (Schlüsselwörter) | flach, durch Kommas getrennt; Abruf nach Schlüsselwort mit `fetch( 'content', 'keyword', ... )` |
| Ein Bild | `ezimage` (Bild) | über Bildvarianten skaliert; Alternativtext; siehe [Bilder und Bildvarianten](bilder-und-bildvarianten.md) |
| Eine herunterladbare Datei | `ezbinaryfile` (Datei) | die Datei liegt im Speicherverzeichnis, außerhalb der Datenbank |
| Audio- oder Videodateien | `ezmedia` (Mediendatei) | |
| Ein Link zu einer anderen Website | `ezurl` (URL) | URL und Text; die Linkliste prüft Links ([Links und URL-Aliase](../../anleitungen/urls-und-aliase.md)) |
| Eine E-Mail-Adresse | `ezemail` (E-Mail) | wird geprüft |
| Verweise auf andere Inhalte | `ezobjectrelationlist` (Objekt Vernüpfungsliste), `ezobjectrelation` (Objekt Verknüpfung) | siehe [Relationen und Einbettungen](relationen-und-einbettungen.md) |
| Eine kleine Wertetabelle | `ezmatrix` (Matrix) | Spalten werden in der Klasse festgelegt |
| Autoren als Namen und E-Mail-Adressen | `ezauthor` (Autoren) | |
| Ein Land | `ezcountry` (Land) | |
| Eine ISBN | `ezisbn` (ISBN) | |
| Eine fortlaufende Nummer | `ezidentifier` (Bezeichner) | Präfix, Suffix und Stellen je Klasse |
| Ein Benutzerkonto | `ezuser` (Benutzerkonto) | nur in Benutzerklassen |
| Preise und Produktoptionen | `ezprice`, `ezmultiprice`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`, `ezproductcategory` | für den eingebauten Shop ([Der eingebaute Shop](der-eingebaute-shop.md)) |
| Einstellungen und Pakete aus Inhalten | `ezinisetting`, `ezpackage` | Sonderzwecke im Setup-Baum |
| Abonnement von Benachrichtigungen für einen Teilbaum | `ezsubtreesubscription` | |

Das Template und das Bearbeitungsformular jedes Datentyps erklärt
[Lektion 4.2: Datentypen und ihre Templates](../trainer/entwickler/modul-04-inhaltsklassen-und-datentypen/datentypen-und-ihre-templates.md);
das Werteformat, das ein Import verwendet (`fromString()`), steht in
[Import- und Exportwerkzeuge, Abschnitt 4](../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).

## Datentypen aus der Standarddistribution

Exponential 6 installiert über seine `composer.json` eine Reihe von Erweiterungen. Für Inhaltsmodelle zählen diese:

| Bedarf | Datentyp | Erweiterung |
|---|---|---|
| Hierarchische Tags, Synonyme, ein kontrolliertes Vokabular | `eztags` | `se7enxweb/eztags` |
| Ein Ort auf einer Karte | `ezgmaplocation` | `se7enxweb/ezgmaplocation` |
| Eine Sternebewertung durch Besucher | `ezsrrating` | `se7enxweb/ezstarrating` |
| Metadaten für Suchmaschinen (Titel, Schlüsselwörter, Beschreibung) | `xrowmetadata` | `se7enxweb/xrowmetadata` |
| Ein Link mit internem oder externem Ziel | `ngenhancedlink` | `se7enxweb/exp_enhanced_link` |
| Auswahllisten mit mehr Optionen als `ezselection` | `sckenhancedselection` | `se7enxweb/enhancedselection2` |
| Eine Datei mit mehr Optionen als `ezbinaryfile` | `enhancedezbinaryfile` | `se7enxweb/enhancedezbinaryfile` |
| Spam-Schutz für Formulare | `recaptcha`, `hcaptcha` | `se7enxweb/recaptcha`, `se7enxweb/hcaptcha` |

Dieselbe Liste, ausgehend von Feldern anderer Systeme, steht in
[Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md#2-inhaltsklassen-und-datentypen).
Prüfen Sie, ob eine Erweiterung auf Ihren Siteaccesses aktiv ist, bevor Sie ihren Datentyp verwenden
(`./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions --allow-root-user`).

## Entscheidungen, die sich schwer rückgängig machen lassen

- **Textzeile oder Rich Text.** Eine Überschrift, die später einen Link oder Kursivschrift braucht, braucht Rich Text.
  Wählen Sie im Zweifel für einen Haupttext `ezxmltext`, für einen Titel `ezstring`.
- **Auswahlliste, Tags oder verknüpfte Objekte.** Eine feste kurze Liste, die nur Sie ändern: `ezselection`. Ein
  Vokabular, das Redakteure erweitern: `eztags`. Kategorien mit eigenen Seiten: verknüpfte Objekte oder Platzierungen.
- **Bildattribut oder Bildobjekt.** Ein `ezimage`-Attribut gehört zu einem Objekt. Ein Bild, das an mehreren Stellen
  verwendet wird oder das Redakteure einmal für alle Seiten austauschen, gehört in ein Bildobjekt in der
  Medienbibliothek, verknüpft oder eingebettet.
- **Einfache Relation oder Liste.** Bevorzugen Sie `ezobjectrelationlist` auch für ein einziges Ziel: Es schränkt die
  Klassen ein, hat einen Standardort für neue Ziele und kann später mehr aufnehmen.
- **Übersetzbar oder nicht.** Ein Preis, eine E-Mail-Adresse oder ein Datum soll sich oft nicht je Sprache
  unterscheiden: Entfernen Sie das Häkchen bei *übersetzbar*, damit Übersetzer den Wert nicht kopieren müssen.

## Datentypen, die Sie in neuen Modellen meiden sollten

| Datentyp | Warum | Stattdessen |
|---|---|---|
| `ezenum` | ein alter Auswahl-Datentyp; der Kernel liefert `bin/php/convertezenumtoezselection.php` mit, um davon wegzukommen | `ezselection` |
| `ezobjectrelation` | nur ein Ziel, weniger Optionen | `ezobjectrelationlist` |
| Viele kleine Textfelder für Absätze | Redakteure kämpfen mit dem Formular | ein `ezxmltext`-Feld |

## Wann Sie einen eigenen Datentyp schreiben

Schreiben Sie einen nur, wenn der Wert eigene Speicherung, Prüfung und Bearbeitung hat, die kein vorhandener Datentyp
und keine Kombination von Attributen abdeckt: ein Wert aus einem externen Dienst, ein strukturierter Wert mit eigenen
Regeln. Versuchen Sie zuerst eine Kombination von Attributen, eine Unterklasse oder ein eigenes Tag in Rich Text. Ist
ein Datentyp die Antwort, beschreibt das Buch den ganzen Weg:
[Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md) und die Tutorials
[ein Datentyp von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-datentyp.md) und
[mit dem RAD-Assistenten](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/rad-datentyp.md).

## Verwandte Seiten

- [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md)
- [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md)
- [Die eZDataType-API](../erweiterungsentwicklung/teil-8-referenz/ezdatatype-api.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
