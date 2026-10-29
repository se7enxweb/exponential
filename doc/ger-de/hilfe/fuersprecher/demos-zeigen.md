---
title: Demos als Fürsprecher zeigen
audience: advocate
category: advocacy
order: 40
summary: Wie ein Fürsprecher der Community eine Vorführung von Exponential vorbereitet und hält - auf einer Installation, die Sie kontrollieren, mit einem Ziel, einem kurzen Weg durch das Produkt, bereitliegenden Quellen und einem Plan für Pannen - mit einer Checkliste zur Vorbereitung und drei fertigen Demo-Wegen.
keywords: Demo, Vorführung, zeigen, Präsentation, Fürsprecher, Installation, Vorbereitung, Checkliste, Redakteure, Layouts, Sprachen, Hilfe, Pannen, demo, demonstration, show, presentation, advocate, installation, preparation, checklist, editors, layouts, languages, help, recovery
type: help
---
# Demos als Fürsprecher zeigen

Diese Seite ist für Fürsprecher, die Exponential Kollegen, Benutzergruppen, Klassen oder Freunden zeigen. Eine gute Demo
zeigt zwei oder drei Dinge gut, auf einer Installation, die Sie kontrollieren, und endet damit, wohin die Menschen als
Nächstes gehen können. Die Skripte Klick für Klick für 5, 15 und 45 Minuten stehen in [Demo-Skripte](../geschaeft/demo-drehbuecher.md);
diese Seite passt sie an die Situation eines Fürsprechers an.

## Inhalt

1. [Die drei Regeln](#die-drei-regeln)
2. [Bauen Sie Ihre Demo-Installation](#bauen-sie-ihre-demo-installation)
3. [Wählen Sie ein Ziel und einen Weg](#wählen-sie-ein-ziel-und-einen-weg)
4. [Drei fertige Demo-Wege](#drei-fertige-demo-wege)
5. [Die Checkliste zur Vorbereitung](#die-checkliste-zur-vorbereitung)
6. [Die Demo beenden](#die-demo-beenden)

## Die drei Regeln

1. **Ihre Installation, Ihre Daten.** Zeigen Sie nie Demos auf der Website eines Kunden, der Produktiv-Website eines
   Arbeitgebers oder einer gemeinsam genutzten Installation, deren Einstellungen jemand anderem gehören
   ([Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)).
2. **Weniger ist mehr.** Zwei oder drei gut gezeigte Funktionen schlagen zehn gehetzte.
3. **Jede Aussage hat eine Quelle.** Halten Sie die Links in einem zweiten Fenster offen.

## Bauen Sie Ihre Demo-Installation

Ein Befehl installiert eine vollständige Website auf SQLite
([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)):

```bash
./console exp:install --dry-run
./console exp:install --apply
```

Dann:

- ändern Sie das erzeugte Administratorpasswort und löschen Sie `var/log/initial-admin-password`
  ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md));
- legen Sie ein Demo-Konto für die Redaktion mit der Rolle Editor an;
- fügen Sie eine zweite Inhaltssprache hinzu (**Setup > Sprachen**);
- legen Sie einen Ordner mit einigen erfundenen Inhalten an.

Eine vollständige Checkliste steht in [Demo-Skripte, Vor jeder Demo](../geschaeft/demo-drehbuecher.md#vor-jeder-demo-die-checkliste-zur-vorbereitung).

## Wählen Sie ein Ziel und einen Weg

| Zielgruppe | Ziel | Weg |
|---|---|---|
| Redakteure | „Damit könnte ich arbeiten.“ | Weg Bearbeiten |
| Designer | „Seiten lassen sich visuell bauen.“ | Weg Layouts |
| Mehrsprachige Teams | „Unsere Sprachen sind abgedeckt.“ | Weg Sprachen |
| Entwickler | „Das könnte ich erweitern.“ | Weg Bearbeiten, dann **Setup > RAD** und ein Template-Override ([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)) |
| Gemischte Gruppe | „Es ist ausgereift, offen und gut dokumentiert.“ | Weg Bearbeiten, dann die Hilfe |

## Drei fertige Demo-Wege

### Weg Bearbeiten (5 Minuten)

1. Das Dashboard: „Ihre Entwürfe und offenen Vorgänge.“
2. Einen Ordner öffnen, **Bearbeiten**, den Kurznamen ändern.
3. **Vorschau** in Telefonbreite.
4. **Zur Veröffentlichung schicken**; dann **Versionen verwalten**, **Vergleichen**.
5. Das „?“ in der Kopfzeile.

### Weg Layouts (5 Minuten)

1. Reiter **Layouts**, ein Layout **Öffnen**.
2. **Block hinzufügen**, einen Block **Titel** in eine Zone ziehen.
3. Ihn in eine andere Zone verschieben; auf eine verknüpfte Zone zeigen.
4. **Verwerfen**.

### Weg Sprachen (4 Minuten)

1. Einen Inhalt öffnen; auf die Sprachkennzeichen zeigen.
2. Auf Grundlage der vorhandenen Sprache übersetzen; veröffentlichen.
3. **Oberfläche**: Hebräisch oder Arabisch wählen; die Administration dreht sich um.
4. Zurück zu Englisch.

Quellen für das, was Sie sagen: [Der Layouts-Editor](../layout-editor.md), [Sprachen](../sprachen.md),
[Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md).

## Die Checkliste zur Vorbereitung

```text
ONE DAY BEFORE
[ ] Demo installation runs; admin password changed; initial password file deleted
[ ] Demo editor account; second language; a folder with invented items
[ ] At least one layout exists
[ ] Links open in a second window: talking points, sources, channels
[ ] Tried the whole path once, timed

TEN MINUTES BEFORE
[ ] Signed in as the account you will use
[ ] No drafts left from the last run
[ ] Browser: private window, zoom readable, notifications off, other tabs closed
[ ] Nothing on screen with real personal data, keys or passwords
```

## Die Demo beenden

Schließen Sie mit drei Dingen auf einer Folie oder Karte:

```text
Try it:          ./console exp:install --apply        (one command, SQLite)
Learn:           the help in the admin ("?"), doc.exponential.earth
Ask and share:   share.exponential.earth/forums   t.me/exponentialcms
```

Beantworten Sie dann Fragen. Wenn Sie etwas nicht wissen: „Ich suche die Quelle und veröffentliche sie.“

**Demos, bei denen sich das Publikum anmeldet.** Lassen Sie sie im Demomodus laufen (`EXP_DEMO_MODE=1`): Das Administratorpasswort, das Sie zeigen, kann von Teilnehmenden nicht geändert werden. Bauen Sie die Installation danach neu auf oder installieren Sie neu, denn der Demomodus schützt nichts anderes. [Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md), [Häufige Fragen zum Demomodus](../demomodus-faq.md).

## Verwandte Seiten

- [Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)
- [Skript für einen Lightning Talk](skript-lightning-talk.md)
- [Demo-Skripte](../geschaeft/demo-drehbuecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
