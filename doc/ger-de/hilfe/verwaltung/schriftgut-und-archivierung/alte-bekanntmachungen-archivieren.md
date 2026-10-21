---
title: Alte Bekanntmachungen archivieren
audience: government, editor, admin, project-manager
category: public-sector
order: 60
summary: Für öffentliche Stellen, die viele befristete Bekanntmachungen veröffentlichen (Beteiligungsverfahren, Ausschreibungen, Ankündigungen) - die Wege, sie von der aktuellen Website zu nehmen und trotzdem aufzubewahren, mit Verschieben, Sektionen, Objektzuständen, Verstecken und dem zeitgesteuerten Cronjob hide von Exponential, mit ihren Wirkungen im Vergleich.
keywords: Archiv, Archivierung, alte Bekanntmachungen, Beteiligungsverfahren, Ankündigungen, verschieben, Archivordner, Sektion, Objektzustand, verstecken, Versteckdatum, HideSettings, Cronjob hide.php, unpublish, Papierkorb, öffentlicher Sektor, Lebenszyklus von Inhalten, archive, archiving, old notices, consultations, announcements, move, archive folder, section, object state, hide, hide date, trash, public sector, content lifecycle
type: help
---
# Alte Bekanntmachungen archivieren

Diese Seite richtet sich an Redaktionen, Administratoren und Projektleitungen. Eine öffentliche Website veröffentlicht
Bekanntmachungen, die ablaufen: Beteiligungsverfahren, Ausschreibungen, Baustellen, Veranstaltungen. Besucher sollen
zuerst aktuelle Informationen finden, doch die alten Bekanntmachungen müssen oft verfügbar bleiben oder zumindest
aufbewahrt werden. Exponential hat keine Schaltfläche „Archivieren“; es hat Bausteine, die sich gut kombinieren lassen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Möglichkeiten im Vergleich](#die-möglichkeiten-im-vergleich)
2. [Muster: ein öffentlicher Archivordner](#muster-ein-öffentlicher-archivordner)
3. [Muster: an einem Datum verstecken](#muster-an-einem-datum-verstecken)
4. [Muster: ein Archivzustand](#muster-ein-archivzustand)
5. [Was Sie nicht zum Archivieren verwenden sollten](#was-sie-nicht-zum-archivieren-verwenden-sollten)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Möglichkeiten im Vergleich

| Möglichkeit | Für Besucher sichtbar | URL | Aufwand | Seite |
|---|---|---|---|---|
| In einen Archivordner verschieben | ja, unter dem Archiv | die alte URL leitet auf die neue weiter | ein Verschieben pro Eintrag oder Teilbaum | diese Seite |
| In eine Archivsektion verschieben | soweit die Leserichtlinie der Sektion es erlaubt | leitet nach dem Verschieben weiter | Sektion zuweisen | „Sektionen für Archive“ |
| Einen Objektzustand „Archiviert“ setzen | soweit die nach Zustand beschränkte Leserichtlinie es erlaubt | unverändert | Zustand setzen, auch für Teilbäume | „Objektzustände für Schriftgut“ |
| Verstecken | nein (außer versteckte Knoten werden angezeigt) | antwortet Besuchern mit „nicht gefunden“ | ein Klick; oder zeitgesteuert | „Inhalte verstecken“ |
| Zeitgesteuertes Verstecken über ein Datumsfeld | nein, ab dem Datum | wie Verstecken | Klassenfeld und Einstellungen | unten |

## Muster: ein öffentlicher Archivordner

1. Legen Sie unter den Bekanntmachungen einen Ordner „Archiv“ an (auf Wunsch einen pro Jahr).
2. Verschieben Sie am Ende jedes Zeitraums die abgelaufenen Bekanntmachungen dorthin: auswählen, **Verschieben**, den
   Ordner wählen.
3. Alte Adressen funktionieren weiter: Der alte URL-Alias eines verschobenen Knotens leitet auf seinen neuen weiter
   ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md#was-automatisch-geschieht)).
4. Schließen Sie das Archiv in Ihren Templates von den Hauptlisten aus (Abruf nach übergeordnetem Knoten) und lassen Sie
   es in der Suche.

Verschiebungen werden im Audit als `content.node.move` aufgezeichnet.

## Muster: an einem Datum verstecken

Das Cronjob-Skript `hide.php` des Kernels (im Standardteil der Cronjobs) versteckt Objekte ausgewählter Klassen, wenn
ein Datumsattribut verstrichen ist. Es tut nichts, bis es eingerichtet ist:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]
RootNodeList[]=<node id of your notices folder>
HideDateAttributeList[]
HideDateAttributeList[notice]=hide_date
```

Fügen Sie der Klasse `notice` (Ihrer Klasse) ein Datumsattribut `hide_date` hinzu und lassen Sie die Redaktion es
ausfüllen. Jeder Lauf versteckt die Knoten, deren Datum verstrichen ist (und aktualisiert den Suchindex). Versteckte
Knoten bleiben im Baum und können wieder angezeigt werden.

Hinweis: Ein ähnliches Skript `unpublish.php` (ebenfalls im Standardteil, eingerichtet mit `[UnpublishSettings]
RootNodeList[]` und `ClassList[]`, liest ein Attribut `unpublish_date`) **entfernt** die Objekte an dem Datum in den
Papierkorb. Für Schriftgut bevorzugen Sie das Verstecken oder das Verschieben in ein Archiv; das Entfernen ist der
Anfang des Weges zur Löschung.

## Muster: ein Archivzustand

Legen Sie eine Zustandsgruppe an, zum Beispiel `lifecycle` mit Aktuell (Voreinstellung) und Archiviert; beschränken Sie
`content/read` der Rolle Anonymous auf Aktuell, wo archivierte Einträge nicht öffentlich sein dürfen, oder zeigen Sie in
den Templates bei archivierten Einträgen einen Hinweis an ([Objektzustände](../../../anleitungen/objektzustaende.md)). Siehe
„Objektzustände für Schriftgut“.

## Was Sie nicht zum Archivieren verwenden sollten

- **Den Papierkorb**: Einträge darin sind auf dem Weg zur Löschung; ein Cronjob zum Leeren des Papierkorbs kann ihn
  leeren.
- **Das Entfernen alter Versionen** eines Objekts zum „Aufräumen“: Die Versionen können das Schriftgut sein.
- **Entwurfskopien**: Entwürfe sind persönliche, laufende Arbeit.

## Verwandte Seiten

- [Objektzustände](../../../anleitungen/objektzustaende.md)
- [Sektionen](../../../anleitungen/sektionen.md)
- [Den Inhaltsbaum strukturieren](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
