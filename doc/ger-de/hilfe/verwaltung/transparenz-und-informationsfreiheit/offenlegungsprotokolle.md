---
title: Offenlegungsprotokolle
audience: government, editor, website-creator
category: public-sector
order: 50
summary: Wie Sie in Exponential ein Offenlegungsprotokoll veröffentlichen, die öffentliche Liste der Informationen, die auf Anträge hin herausgegeben wurden - eine Klasse für Protokolleinträge ohne Daten der Antragsteller, eine Listenseite, ein Feed und die Reihenfolge der Schritte, damit nichts Persönliches veröffentlicht wird.
keywords: Offenlegungsprotokoll, herausgegebene Informationen, Auskünfte nach Informationsfreiheitsrecht, Antragsprotokoll, Transparenz, veröffentlichte Auskünfte, Register, disclosure log, released information, FOI responses, FOIA reading room, request log, transparency, published responses, register
type: help
---
# Offenlegungsprotokolle

Diese Seite richtet sich an Redaktionen und Websiteersteller, die ein Protokoll der Informationen veröffentlichen, die
auf Anträge hin herausgegeben wurden. Manche Stellen veröffentlichen ein solches Protokoll, weil das Gesetz es
verlangt, andere aufgrund einer eigenen Richtlinie. Das Protokoll ist in Exponential Inhalt: ein Eintrag pro
herausgegebener Auskunft, aufgelistet auf einer Seite, mit den herausgegebenen Dokumenten als Anhang.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was ein Eintrag enthält und was er nie enthält](#was-ein-eintrag-enthält-und-was-er-nie-enthält)
2. [Eine Klasse für Protokolleinträge](#eine-klasse-für-protokolleinträge)
3. [Die Protokollseite](#die-protokollseite)
4. [Einen Eintrag veröffentlichen](#einen-eintrag-veröffentlichen)
5. [Feed und Suche](#feed-und-suche)
6. [Checkliste](#checkliste)

## Was ein Eintrag enthält und was er nie enthält

| Enthält | Enthält nie |
|---|---|
| ein eigenes Aktenzeichen | Name, E-Mail-Adresse oder Anschrift der antragstellenden Person |
| das Datum der Auskunft | den Text des Antrags, wenn er die antragstellende Person erkennbar macht |
| eine neutrale Zusammenfassung dessen, was gefragt wurde | interne Vermerke oder Beratungen |
| das Ergebnis (herausgegeben, teilweise herausgegeben, verweigert) und die Gründe | ungeschwärzte Dokumente |
| die herausgegebenen Dokumente als Dateien | Dokumente, deren Schwärzung noch geprüft werden muss |

Der Antrag selbst, mit personenbezogenen Daten, bleibt in Ihrem Vorgangssystem oder unter **Setup > Gesammelte
Informationen**, die nicht öffentlich sind (siehe „Anträge aus gesammelten Informationen bearbeiten“).

## Eine Klasse für Protokolleinträge

| Attribut | Datentyp | Hinweis |
|---|---|---|
| Aktenzeichen | Textzeile | Ihre eigene Nummer, zum Beispiel „FOI-2026-0123“ |
| Titel | Textzeile | eine neutrale Zusammenfassung als Titel |
| Datum der Auskunft | Datum | |
| Thema | Tags oder Auswahl | Thema des Antrags |
| Ergebnis | Auswahl | Herausgegeben, Teilweise herausgegeben, Verweigert, Information nicht vorhanden |
| Zusammenfassung des Antrags | Textabschnitt | keine personenbezogenen Daten |
| Auskunft | Formatierter Text | die Antwort oder eine Zusammenfassung davon |
| Gründe für die Verweigerung | Textabschnitt | die angewandten Ausnahmetatbestände |

Herausgegebene Dokumente kommen als Dateiobjekte unter den Eintrag, sodass der Eintrag ein Container ist. Muster für
den Objektnamen: `<reference> <title>`.

## Die Protokollseite

Ein Ordner „Offenlegungsprotokoll“ mit den Einträgen als Unterobjekten, sortiert nach dem Datum der Auskunft, die
neuesten zuerst. Geben Sie der Klasse der Einträge eine Zeilenansicht, die Aktenzeichen, Titel, Datum und Ergebnis
zeigt ([Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)). Bei einem großen Protokoll
gruppieren Sie die Einträge in einem Ordner pro Jahr.

## Einen Eintrag veröffentlichen

1. Vergewissern Sie sich, dass die Dokumente die Prüfung der Schwärzung bestanden haben (siehe „Schwärzen ist
   Handarbeit“).
2. Klicken Sie unter **Unterelemente** des Jahresordners auf **Neu erstellen**, wählen Sie die Klasse der Einträge und
   füllen Sie die Felder aus.
3. Fügen Sie die herausgegebenen Dateien unter dem Eintrag hinzu.
4. **Zur Veröffentlichung schicken**; mit einem Freigabe-Workflow gibt eine zweite Person frei.
5. Öffnen Sie den Eintrag abgemeldet und prüfen Sie, ob sich jede Datei öffnen lässt und nur zeigt, was herausgegeben
   werden sollte.

## Feed und Suche

- Ein RSS-Export des Protokollordners lässt Journalistinnen, Journalisten und Forschende neuen Herausgaben folgen
  ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).
- Machen Sie Titel, Zusammenfassung und Auskunft **Durchsuchbar**, damit die Suche der Website frühere Herausgaben
  findet; mit Handlern in `binaryfile.ini` kann auch der Text von PDFs indexiert werden
  ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#in-dateien-suchen)).
- Die Suchstatistiken zeigen, wonach Menschen suchen und was sie nicht finden
  ([Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).

## Checkliste

- [ ] Kein Eintrag und kein Dateiname enthält personenbezogene Daten der Antragsteller.
- [ ] Die Prüfung der Schwärzung ist vor dem Veröffentlichen dokumentiert.
- [ ] Einträge sind nach Datum sortiert; ein Feed existiert.
- [ ] Auch Verweigerungen werden protokolliert, mit Gründen.

## Verwandte Seiten

- [Eine Auskunft nach Informationsfreiheitsrecht veröffentlichen](eine-auskunft-nach-informationsfreiheitsrecht-veroeffentlichen.md)
- [Schwärzen ist Handarbeit](schwaerzen-ist-handarbeit.md)
- [Suche und Auffindbarkeit von Offenlegungen](suche-und-auffindbarkeit-von-offenlegungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
