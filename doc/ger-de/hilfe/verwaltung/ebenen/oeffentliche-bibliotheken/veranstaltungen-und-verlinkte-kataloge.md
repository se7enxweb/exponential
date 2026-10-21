---
title: Veranstaltungen und Kataloge, verlinkt statt nachgebaut
audience: government, website-creator, project-manager, editor
category: public-sector
order: 70
summary: Wie eine Bibliothek ihren Veranstaltungskalender mit den mitgelieferten Klassen Event und Event calendar betreibt - Ansichten, eine Liste kommender Veranstaltungen, Filter nach Zweigstelle, Absagen, Anmeldung - und warum und wie sie auf den Bibliothekskatalog verlinkt, statt ihn im CMS nachzubauen.
keywords: Veranstaltungskalender, Klasse Event, Kalenderansicht, Programmansicht, kommende Veranstaltungen, Fetch, Absage, Anmeldung, Katalog, OPAC, Katalogsuchfeld, Link, events calendar, event class, event calendar, calendar view, program view, upcoming events, fetch, attribute_filter, cancellation, sign-up, catalog, catalog search box, link
type: help
---
# Veranstaltungen und Kataloge, verlinkt statt nachgebaut

Diese Seite richtet sich an Website-Ersteller, Projektverantwortliche und die Redaktion einer Bibliothek. Sie behandelt
die zwei Dinge, die Besucher nach den Öffnungszeiten am meisten nutzen: Veranstaltungen und den Katalog. Veranstaltungen
werden in Exponential mit mitgelieferten Klassen gebaut; der Katalog bleibt im Bibliotheksmanagementsystem, und die
Website verlinkt darauf.

## Inhalt

1. [Der Veranstaltungskalender](#der-veranstaltungskalender)
2. [Eine Liste kommender Veranstaltungen](#eine-liste-kommender-veranstaltungen)
3. [Änderungen, Absagen und Anmeldung](#änderungen-absagen-und-anmeldung)
4. [Der Katalog: verlinken](#der-katalog-verlinken)
5. [Checkliste](#checkliste)

## Der Veranstaltungskalender

| Teil | Was mitgeliefert wird | Lesen |
|---|---|---|
| Klasse Event (`event`) | Short title (Pflicht), Full title, Text, Category (Schlüsselwörter), From Time (Pflicht), To Time | [Inhaltsmodell für öffentliche Bibliotheken](inhaltsmodell.md) |
| Klasse Event calendar (`event_calendar`) | ein Container mit Full Title und einer Auswahl **View** | |
| Ansichten | `event_view_calendar.tpl` (Monatsraster) und `event_view_program.tpl` (Liste) des Designs ezwebin | [ezwebin](../../../../../eng-us/features/6.0/extensions/ezwebin.md) |
| Cache | der Veranstaltungskalender von ezwebin hält seine Seite 400 Sekunden (`cache_ttl`) | [Caching für Designerinnen und Designer](../../../../../eng-us/help/designers/theme-design/caching-for-designers.md) |

Die Ansichten kommen mit dem Design ezwebin. Das Standarddesign `simple` neuer Installationen hat kein Template für den
Veranstaltungskalender; eine Website mit diesem Design braucht einen eigenen Override `full/event_calendar.tpl` oder eine
mit einem Fetch erstellte Liste (nächster Abschnitt)
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)).

Eine Veranstaltung hinzufügen: Öffnen Sie den Veranstaltungskalender, **Neu erstellen** > **Event**, füllen Sie Short
title und From Time (und To Time) aus, den Text mit Ort und Barrierefreiheit, und **Zur Veröffentlichung schicken**
([Inhalte anlegen und bearbeiten](../../../inhalte-bearbeiten.md)).

## Eine Liste kommender Veranstaltungen

Für die Startseite oder die Seite einer Zweigstelle listet ein Template-Fetch die nächsten Veranstaltungen nach ihrer
Anfangszeit:

```text
{def $events = fetch( 'content', 'list', hash(
        'parent_node_id', $calendar_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'event' ),
        'attribute_filter', array( array( 'event/from_time', '>=', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'event/from_time' ),
        'limit', 5 ) )}
<ul>
{foreach $events as $event}
  <li><a href={$event.url_alias|ezurl}>{$event.name|wash}</a>,
      {$event.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}</li>
{/foreach}
</ul>
{undef $events}
```

`$calendar_node_id` ist der Knoten Ihres Veranstaltungskalenders. Für eine Zweigstelle fügen Sie der Klasse Event ein
Beziehungsattribut `branch` hinzu und filtern danach, oder Sie versehen Veranstaltungen mit dem Tag der Zweigstelle und
listen nach Tag ([Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)). Eine
zwischengespeicherte Startseite zeigt eine vergangene Veranstaltung, bis ihr Cache abläuft; geben Sie dem Block einen
Cache-Ablauf oder ein kurzes `cache_ttl`
([Ansichts-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md)).

## Änderungen, Absagen und Anmeldung

| Situation | Tun |
|---|---|
| Uhrzeit oder Ort ändern sich | die Veranstaltung bearbeiten; die Änderung oben in den Text schreiben („Verlegt in <Raum>“) |
| Veranstaltung abgesagt | die Seite behalten, „Abgesagt“ in Short title und Text schreiben; oder ein Attribut Buchung auf *abgesagt* setzen, das das Template anzeigt; nach dem Datum verbergen |
| Veranstaltung ausgebucht | das Attribut Buchung auf *ausgebucht* gesetzt |
| Anmeldung nötig | ein Formular des Informationssammlers an der Veranstaltung (Name, E-Mail, Anzahl der Plätze) oder ein Link zum Buchungssystem, das Sie verwenden |

Anmeldeformulare erheben personenbezogene Daten: Fragen Sie nur, was die Veranstaltung braucht, und entfernen Sie die
Eingaben nach der Veranstaltung unter **Setup > Gesammelte Informationen**
([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)). Der Informationssammler zählt keine Plätze;
setzen Sie die Veranstaltung von Hand auf *ausgebucht*.

## Der Katalog: verlinken

Der Katalog (die öffentliche Suche des Bibliotheksmanagementsystems) enthält Titel, Bestände, Verfügbarkeit, Ausleihen
und Leserkonten. Ihn als Inhaltsobjekte nachzubauen würde Daten verdoppeln, die sich jede Minute ändern, und Leserdaten
dorthin bringen, wo sie nicht hingehören. Stattdessen:

| Bedarf | Muster |
|---|---|
| Suche von der Website aus | ein einfaches Suchformular im Pagelayout, das die Anfrage mit `method="get"` an die Suchadresse des Katalogs schickt |
| Ein Link auf einen Titel in einer Leseliste | der dauerhafte Link des Katalogs auf den Datensatz, in einem Feld Link oder im Rich Text |
| „Mein Konto“, Verlängerungen, Vormerkungen | ein Link zur Anmeldung des Katalogs; keine Kontodaten auf der Website |
| Neuerwerbungen | ein RSS-Feed des Katalogs, falls er einen anbietet, gelesen von einem RSS-Import ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)) |

Ein Katalogsuchformular ist gewöhnliches HTML in Ihrem Design; geben Sie seinem Feld eine sichtbare Beschriftung und
seiner Schaltfläche einen klaren Namen. Ein RSS-Import legt je Eintrag des Feeds ein Objekt an und braucht den Cronjob
`rssimport`; prüfen Sie, was er importiert, weil die Inhalte aus einem anderen System kommen.

## Checkliste

- [ ] Die Kalenderansicht funktioniert mit Tastatur und Screenreader, oder die Programmliste wird daneben angeboten.
- [ ] Jede Veranstaltung nennt Datum, Uhrzeit, Ort und die Barrierefreiheit des Veranstaltungsorts als Text.
- [ ] Vergangene Veranstaltungen verlassen die Liste kommender Veranstaltungen ohne Handarbeit.
- [ ] Das Katalogsuchfeld schickt Besucher zu den Ergebnissen des Katalogs.
- [ ] Im CMS werden keine Daten von Leserkonten gespeichert.

## Verwandte Seiten

- [Öffentliche Bibliotheken](README.md)
- [Inhaltsmodell für öffentliche Bibliotheken](inhaltsmodell.md)
- [RSS-Feeds für Ihre Website](../../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
