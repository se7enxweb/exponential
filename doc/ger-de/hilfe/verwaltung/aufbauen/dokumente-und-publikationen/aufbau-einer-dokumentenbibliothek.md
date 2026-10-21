---
title: Aufbau einer Dokumentenbibliothek
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Wo Dokumente auf einer Exponential-Site des öffentlichen Sektors liegen - die Medienbibliothek oder neben den Seiten, die sie verwenden, eine Klasse Publication als Einstiegsseite, Beziehungen statt Kopien, Ordner nach Typ und Jahr und eine Liste der Publikationen, die Besucher filtern können.
keywords: Dokumentenbibliothek, Medienbibliothek, Publikationen, Einstiegsseite, Klasse Publication, Beziehungen, Ordner, Dokumenttypen, Jahresordner, Liste der Publikationen, Filter, document library, media library, publications, landing page, publication class, relations, folders, document types, year folders, publications list, filter
type: help
---
# Aufbau einer Dokumentenbibliothek

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die entscheiden, wo Dokumente liegen und wie Besucher sie
finden. Exponential lässt Dateien überall im Baum liegen; die Wahl beeinflusst Berechtigungen, Menüs und Pflege.
Quellen: [Die Medienbibliothek ordnen](../../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md) und
[Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md).

## Inhalt

1. [Zwei Orte für Dateien](#zwei-orte-für-dateien)
2. [Publikationen mit einer Einstiegsseite](#publikationen-mit-einer-einstiegsseite)
3. [Ordner](#ordner)
4. [Eine Liste der Publikationen](#eine-liste-der-publikationen)
5. [Regeln](#regeln)

## Zwei Orte für Dateien

| Ort | Gut für | Hinweis |
|---|---|---|
| Die Medienbibliothek (Sektion Media) | Dateien, die viele Seiten verwenden: Formulare, Logos, Standarddokumente | im Standardpaket liest die Rolle Anonymous die Sektion Media; prüfen Sie Ihres |
| Neben der Seite, die sie verwendet | Dokumente, die zu einer Seite gehören (Sitzungsunterlagen unter der Sitzung) | sie übernehmen die Sektion und die Berechtigungen der Seite |

In beiden Fällen **verknüpfen** Seiten File-Objekte (ein Beziehungsfeld oder eine Einbettung im Rich Text), statt jedes
Mal eine Kopie hochzuladen.

## Publikationen mit einer Einstiegsseite

Für Berichte, Pläne und Leitfäden ist ein Objekt **Publication** die Seite, die Menschen finden und zitieren; die
Datei ist daran angehängt. Eine Klasse Publication mit Titel, Zusammenfassung, Veröffentlichungsdatum, Aktenzeichen,
Typ, Themen und zugehörigen Dateien wird in
[Inhaltsmodell für die Bundesebene](../../../../../eng-us/help/government/levels/national/content-model.md#class-to-add-publication) vorgeschlagen.

| Nutzen | Warum |
|---|---|
| Eine HTML-Zusammenfassung | lesbar ohne Download; von der Suche gefunden |
| Eine stabile Adresse | die Datei kann ersetzt werden, ohne die zitierte Adresse zu ändern |
| Mehrere Dateien (Sprachfassungen, Anlagen, Daten) | eine Beziehungsliste |
| Metadaten | Datum, Typ und Themen für Listen und Filter |

## Ordner

```text
Publications                      Publication objects, sorted by publication date
├── Reports
├── Strategies and plans
├── Guidance
└── Data
Media
└── Files
    └── Publications <year>       the File objects of that year's publications
```

Typordner für Besucher; Jahresordner in der Medienbibliothek für die Menschen, die die Dateien pflegen. Eine
Publikation, die unter zwei Typen aufgeführt wird, erhält einen zweiten Ort.

## Eine Liste der Publikationen

Ein Template-Fetch listet die Publication-Objekte unter dem Ordner Publications, die neuesten zuerst, und ein Filter
über eine Auswahl oder Schlagworte grenzt sie ein (nach Typ, Thema oder Jahr). Mit eZ Find erledigen das Facetten für
Suchergebnisse ([Suche mit eZ Find und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)). Die
Tabelle der Unterelemente in der Administration exportiert dieselbe Liste als CSV für ein Verzeichnis
([Inhaltsmodell und Bearbeitung, 5](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#5-die-tabelle-der-unterelemente)).

## Regeln

| Regel | Grund |
|---|---|
| Ein File-Objekt je Dokument; nie zwei Kopien | ein Ort zum Korrigieren |
| Lesbare Dateinamen (`annual-report-2025.pdf`) | sie erscheinen in der Download-Adresse |
| Keine Dokumente in Sektionen, die Besucher nicht lesen können, außer sie sollen eingeschränkt sein | defekte Downloads |
| Prüfen Sie umgekehrte Beziehungen, bevor Sie eine Datei entfernen | Seiten, die auf sie verlinken ([Prüfung umgekehrter Beziehungen](../../../../../eng-us/features/6.0/reverse-relation-check.md)) |

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Metadaten und Verschlagwortung](metadaten-und-verschlagwortung.md)
- [Den Inhaltsbaum strukturieren](../../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
