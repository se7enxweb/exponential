---
title: Umzug von einer anderen Blogplattform
audience: individual, website-creator
category: personal-sites
order: 1626
summary: Ein ehrlicher Leitfaden für Privatpersonen, die ein Blog von WordPress oder einer anderen Plattform zu Exponential umziehen möchten - es gibt keinen Ein-Klick-Importer; der dokumentierte Weg besteht aus einem Export, einem Umwandlungsskript und einem Importwerkzeug - mit drei realistischen Möglichkeiten für eine Person, den Aufwandstreibern und einer Checkliste.
keywords: Migration, Blog umziehen, Import, WordPress, WXR, Blogger, Medium, Baukasten, Export, xrowextract, ezcsvimport, RSS-Import, Weiterleitungen, Permalinks, Kommentare, Bilder, Importer, move blog, redirects, site builder
type: help
---
# Umzug von einer anderen Blogplattform

Diese Seite ist für Privatpersonen, deren Blog heute woanders liegt, bei WordPress, einem gehosteten Blogdienst oder
einem Website-Baukasten, und die es zu Exponential umziehen möchten.

> **Hinweis**
> In Exponential gibt es **keinen Ein-Klick-Importer** für WordPress oder eine andere Blogplattform. Ein Blog
> umzuziehen ist ein kleines Projekt: exportieren, umwandeln, importieren, weiterleiten. Die Schritte sind
> dokumentiert; das Umwandlungsskript schreiben Sie oder ein Helfer. Diese Seite sagt Ihnen, was das für eine
> einzelne Person bedeutet.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Drei Möglichkeiten für eine Person](#drei-möglichkeiten-für-eine-person)
3. [Was mehr oder weniger Arbeit macht](#was-mehr-oder-weniger-arbeit-macht)
4. [Ihre alten Adressen behalten](#ihre-alten-adressen-behalten)
5. [Kommentare, Bilder und Autoren](#kommentare-bilder-und-autoren)
6. [Checkliste](#checkliste)

## Was es gibt

| Werkzeug | Was es tut | Quelle |
|---|---|---|
| Der Migrationsleitfaden für WordPress | Der ganze Weg: WXR-Export, Zuordnung, Umwandlung, Import, Weiterleitungen, Umstellung | [WordPress und Exponential](../../../installation/migration/wordpress.md) |
| `xrowextract` | Importiert CSV-, XML- oder JSON-Dateien als Inhalte, mit vorherigem Probelauf | [Die Import- und Exportwerkzeuge von Exponential](../../../installation/migration/exponential-tools.md) |
| `ezcsvimport.php` | Das CSV-Importskript des Kernels | ebenda |
| Der ZIP-Upload von `ezmultiupload` | Bringt viele Bilder auf einmal hinein | [Viele Bilder hochladen](../portfolios/viele-bilder-hochladen.md) |
| RSS-Import (Kernel) | Liest den Feed einer anderen Website als Inhalte ein, mit einem Cronjob | [RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md#einen-feed-importieren) |
| Exporte von Website-Baukästen | Manche Baukästen exportieren WordPress-XML | [Website-Baukästen](../../../installation/migration/website-baukaesten.md) |
| Die Erweiterung 7x SQLI Import | Liest die WordPress-Exportdatei (WXR) selbst: Beiträge und Seiten mit Autor, Datum, Kategorien, Schlagwörtern, Auszug und Beitragsbild, dazu die Medien; Sie ordnen sie in der Administration Ihren Klassen zu und sehen vorher einen Probelauf | [7x SQLI Import: hier beginnen](../../sqli-import/README.md) |

Was es in einer einfachen Installation nicht gibt: einen fertigen Konverter von der WordPress-Exportdatei (WXR) zu
Exponential-Inhalten. Wo Ihr Hoster oder Ihr Helfer die Erweiterung 7x SQLI Import installiert, ist sie dieser
Konverter: Importieren Sie zuerst die Medien, dann die Beiträge, damit die Bilder erhalten bleiben. In jedem Fall
fehlen: ein Import von WordPress-Benutzern samt Passwörtern (Exponential kann WordPress-Passwort-Hashes nicht lesen)
und eine automatische Umwandlung von Shortcodes und Page-Builder-Layouts.

## Drei Möglichkeiten für eine Person

| Möglichkeit | Wie | Aufwand | Passt zu |
|---|---|---|---|
| **Von Hand kopieren** | Beiträge im Editor neu anlegen, die neuesten oder besten zuerst | Wächst mit jedem Beitrag | Bis zu ein paar Dutzend Beiträgen; eine Gelegenheit zum Ausmisten |
| **RSS-Import für neuere Beiträge** | Einen RSS-Import auf den Feed Ihres alten Blogs richten; er legt Objekte für die Einträge an, die der Feed enthält | Schnell eingerichtet; braucht den Cronjob `rssimport` | Die neuesten Beiträge übernehmen; ein Feed enthält nur die Einträge, die sein Herausgeber hineinstellt, meist die neuesten |
| **Skriptgesteuerte Migration** | Dem WordPress-Leitfaden folgen: WXR exportieren, per Skript in CSV oder JSON umwandeln, mit `xrowextract` importieren | Ein Wochenende oder mehr, wenn Sie selbst skripten; ein klar umrissener Auftrag für einen Helfer | Hunderte Beiträge, oder wenn Sie alles mitnehmen möchten |

Viele kombinieren sie: Archiv per Skript oder RSS-Import übernehmen und dann die zwanzig meistgelesenen Beiträge von
Hand nachbessern.

Ist die Erweiterung 7x SQLI Import installiert, braucht die skriptgesteuerte Migration kein Skript: Legen Sie einen
neuen Import im Format *WordPress export (WXR)* an, laden Sie die Datei hoch, ordnen Sie die Beiträge Ihrer
Artikelklasse zu, sehen Sie sich den Probelauf an und starten Sie ihn dann. Das dauert einen Nachmittag statt eines
Wochenendes, und ein missglückter Lauf lässt sich rückgängig machen.

Wenn Skripten nichts für Sie ist, ist das eine klar umrissene Aufgabe, die Sie einem Partner übergeben können
([Wann Sie sich Hilfe holen sollten](../im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)).

## Was mehr oder weniger Arbeit macht

| Leichter | Schwerer |
|---|---|
| Reine Textbeiträge mit wenigen Bildern | Page Builder, Shortcodes, eingebettete Widgets |
| Einige hundert Beiträge | Tausende Beiträge und Anhänge |
| Nur Kategorien und Tags | Eigene Felder, Plugins, die Inhalte speichern |
| Kommentare bleiben zurück | Kommentare sollen erhalten bleiben |
| Ein Autor | Mehrere Autoren mit Konten |

## Ihre alten Adressen behalten

Leser und Suchmaschinen kennen Ihre alten Adressen. Halten Sie sie funktionsfähig:

- Wenn die Domain gleich bleibt, ordnen Sie alte Permalinks per Weiterleitung den neuen Seiten zu
  ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).
- Adressen mit Query-Strings (`/?p=123`) muss der Webserver weiterleiten, nicht Exponential
  ([WordPress und Exponential, Adressen und Weiterleitungen](../../../installation/migration/wordpress.md#4-adressen-und-weiterleitungen)).
- Die alte Feed-Adresse (`/feed/`) sollte auf Ihren neuen RSS-Export zeigen, damit Abonnenten bleiben
  ([RSS-Feeds für Ihr Blog](rss-feeds-fuer-ihr-blog.md)).
- Prüfen Sie nach dem Umzug die 404-Fehler und ergänzen Sie die fehlenden Weiterleitungen.

## Kommentare, Bilder und Autoren

- **Kommentare** lassen sich bei einer skriptgesteuerten Migration als *Comment*-Objekte unter den Beiträgen
  importieren oder auf einer archivierten Kopie des alten Blogs belassen. Denken Sie daran, dass sie
  personenbezogene Daten anderer Menschen enthalten.
- **Bilder**: Importieren Sie zuerst die Bilder, dann die Beiträge, damit Links auf die neuen Bildobjekte zeigen
  können.
- **Autoren**: Wenn ein Freund für Sie geschrieben hat, legen Sie sein Konto an und bitten Sie ihn, ein neues
  Passwort zu setzen; sein altes lässt sich nicht übernehmen.

## Checkliste

- [ ] Export des alten Blogs gesichert, einschließlich der Uploads.
- [ ] Möglichkeit gewählt: von Hand, RSS-Import, per Skript oder gemischt.
- [ ] *Blog* und Klassen in Exponential bereit ([Ein Blog beginnen](ein-blog-beginnen.md)).
- [ ] Test mit zehn Beiträgen, darunter Ihr kompliziertester.
- [ ] Bilder vor den Beiträgen importiert.
- [ ] Liste der Weiterleitungen von alten zu neuen Adressen.
- [ ] Alte Feed-Adresse weitergeleitet.
- [ ] Altes Blog schreibgeschützt behalten, bis das neue einige Wochen gelaufen ist.

## Verwandte Seiten

- [Blogs](README.md)
- [WordPress und Exponential](../../../installation/migration/wordpress.md)
- [Migration zu und von Exponential](../../../installation/migration/README.md)
- [Wechsel zu Exponential](../../unternehmen/auswahl/zu-exponential-wechseln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
