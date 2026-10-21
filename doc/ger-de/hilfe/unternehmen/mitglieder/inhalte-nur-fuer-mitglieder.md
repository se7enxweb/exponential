---
title: Inhalte nur für Mitglieder
audience: businesses, website-creator, admin
category: business-solutions
order: 30
summary: Wie eine Exponential-Mitgliederwebsite Seiten, Dokumente und Diskussionen für Mitglieder bereithält - eine Mitgliedersektion, Mitgliedschaftsstufen, öffentliche Teaser, geschützte Downloads, Suchergebnisse nur für Mitglieder, Foren und wie Sie es als Besucher testen.
keywords: Inhalte nur für Mitglieder, Mitgliederbereich, Mitgliedersektion, Mitgliedschaftsstufen, geschützte Downloads, Teaser, Rolle Anonymous, content/read, Sektionen, Foren, als Besucher testen, RequireUserLogin, members-only content, members area, members section, membership levels, protected downloads, teasers, Anonymous role, sections, forums, testing as a visitor
type: help
---
# Inhalte nur für Mitglieder

Diese Seite ist für die Menschen, die eine Mitgliederwebsite betreiben und entscheiden, was für Mitglieder ist. Sie
erklärt, wie Exponential Mitgliederinhalte von der Öffentlichkeit fernhält, wie Sie manchen Mitgliedern mehr bieten als
anderen, wie Sie Besucher zum Beitritt locken, ohne Inhalte zu verschenken, und wie Sie prüfen, dass nichts nach außen
dringt.

## Inhalt

1. [Das Prinzip](#das-prinzip)
2. [Ein Mitgliederbereich in fünf Schritten](#ein-mitgliederbereich-in-fünf-schritten)
3. [Stufen der Mitgliedschaft](#stufen-der-mitgliedschaft)
4. [Teaser für Besucher](#teaser-für-besucher)
5. [Mitgliederdokumente](#mitgliederdokumente)
6. [Suche, Menüs und Feeds](#suche-menüs-und-feeds)
7. [Foren und Beiträge von Mitgliedern](#foren-und-beiträge-von-mitgliedern)
8. [Als Besucher testen](#als-besucher-testen)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Das Prinzip

Jeder Besucher, der nicht angemeldet ist, ist der Benutzer **Anonymous** und darf nur tun, was die Rolle Anonymous
erlaubt. Die Standardrolle Anonymous hat `content/read`, beschränkt auf die Sektion Standard. Inhalte in jeder anderen
Sektion sind für Besucher daher unsichtbar und ergeben „Zugriff verweigert“ oder eine Aufforderung zur Anmeldung
([Was Besucher sehen dürfen](../../ersteller/website-ersteller/rechte-fuer-besucher.md#was-die-rolle-standardmäßig-erlaubt)).

Inhalte nur für Mitglieder sind einfach Inhalte in einer Sektion, die die Rolle Mitglied liest und die Rolle Anonymous
nicht.

## Ein Mitgliederbereich in fünf Schritten

Die Anleitung zu Sektionen beschreibt genau dieses Verfahren
([Sektionen, 13.1](../../../anleitungen/sektionen.md#131-ein-mitgliederbereich)):

1. Legen Sie eine Sektion *Mitgliederbereich* (`members_area`) an.
2. Legen Sie einen Ordner *Mitglieder* mit den Mitgliederseiten darunter an.
3. **Inhalt zuweisen**: Geben Sie dem Ordner *Mitglieder* und allem darunter die Sektion *Mitgliederbereich*.
4. Prüfen Sie abgemeldet: Der Ordner und seine Seiten sind aus den Menüs verschwunden und verlangen eine Anmeldung.
5. Geben Sie der Rolle Mitglied `content/read` für Section( Mitgliederbereich ).

Neue Seiten, die im Ordner angelegt werden, übernehmen seine Sektion automatisch; Redakteure müssen also nicht daran
denken.

Eine ganze Website kann stattdessen eine Anmeldung verlangen (`RequireUserLogin=true` in ihrem Siteaccess), was zu
geschlossenen Communitys ohne öffentlichen Teil passt
([A members-only area](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#ein-bereich-nur-für-mitglieder)).

## Stufen der Mitgliedschaft

| Stufe | Sektion, die sie hinzufügt | Rolle |
|---|---|---|
| Alle Mitglieder | Mitgliederbereich | Mitglied: `content/read` Section( Mitgliederbereich ) |
| Ordentliche Mitglieder | Ordentliche Mitglieder | Ordentliches Mitglied: `content/read` Section( Ordentliche Mitglieder ) |
| Vorstand | Vorstand | Vorstand: `content/read` Section( Vorstand ) |

Ordnen Sie jede Rolle der Benutzergruppe der Stufe zu. Richtlinien mehrerer Rollen addieren sich; ein ordentliches
Mitglied, das auch in der Gruppe Mitglieder ist, liest also beide Sektionen
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

Wenn derselbe Eintrag nach einiger Zeit von „nur für Mitglieder“ zu „öffentlich“ wechseln soll (zum Beispiel ein
Bericht, der nach einem Jahr öffentlich wird), kann eine Gruppe von Objektzuständen das ausdrücken, statt den Eintrag zu
verschieben ([Objektzustände](../../../anleitungen/objektzustaende.md)).

## Teaser für Besucher

Ein Besucher, der eine Seite nicht lesen kann, sieht nichts von ihr. Um für Mitgliederinhalte zu werben, veröffentlichen
Sie in der Sektion Standard einen kurzen öffentlichen Teaser („Mitglieder können den vollständigen Bericht lesen“), der
auf die Mitgliederseite verlinkt; der Link führt Nichtmitglieder zur Anmeldung, wo die Registrierung angeboten wird.
Halten Sie den Teaser als eigenes Objekt, damit er öffentlich sein kann, während der vollständige Text es nicht ist.

## Mitgliederdokumente

Dokumente sind File-Objekte; jeder Download läuft über `content/download`, das prüft, ob die Person die Datei lesen
darf. Eine Datei in der Mitgliedersektion können nur Mitglieder herunterladen, selbst wenn der Link weitergegeben wird
([Dateien und Downloads, Protected downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)).
Verlinken Sie Dateien nicht direkt aus dem Speicherverzeichnis. Upload-Grenzen, Angaben zu Format und Größe und das
Hochladen vieler Dateien behandelt [Dokumente und Dateibibliotheken](../intranets/dokumente-und-dateibibliotheken.md).

## Suche, Menüs und Feeds

- Die Suche zeigt nur, was die Person lesen darf: Besucher finden Teaser, Mitglieder finden die vollständigen Texte
  ([Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)).
- Menüs zeigen nur Seiten, die die Person lesen darf.
- Ein RSS-Export listet die neuesten Objekte unterhalb seiner Quellen ([RSS-Feeds](../../../anleitungen/rss-feeds.md));
  wählen Sie einen Mitgliederordner nicht als Quelle eines öffentlichen Feeds, es sei denn, Sie wollen seine Titel
  veröffentlichen. Testen Sie den Feed abgemeldet.

## Foren und Beiträge von Mitgliedern

`ezwebin` liefert die Klassen `forums`, `forum`, `forum_topic` und `forum_reply` mit ihren Templates
([ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)); sie sind auf dem Demonstrationsserver vorhanden. Mitglieder
schreiben Beiträge, wenn ihre Rolle `content/create` für die Klassen der Themen und Antworten hat, beschränkt auf den
Ort des Forums. Kommentare unter Artikeln funktionieren genauso
([Was Besucher sehen dürfen, Giving visitors more](../../ersteller/website-ersteller/rechte-fuer-besucher.md#besuchern-mehr-erlauben)).
Planen Sie, wer moderiert, und geben Sie dieser Gruppe `content/edit` und `content/remove` nur in der Forensektion.

Sternbewertungen für Inhalte gibt es mit `ezstarrating`
([ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md)).

## Als Besucher testen

Verwenden Sie ein privates Browserfenster, nicht Ihre Sitzung in der Administration
([Als Besucher testen](../../ersteller/website-ersteller/rechte-fuer-besucher.md#als-besucher-testen)):

| Angemeldet als | Öffnen | Erwartet |
|---|---|---|
| niemand | eine Mitgliederseite | Anmeldung verlangt |
| niemand | die Adresse eines Mitgliederdokuments | abgelehnt |
| niemand | Suche nach einem Wort, das nur in Mitgliedertexten steht | kein Ergebnis |
| ein Mitglied | dieselbe Seite und dasselbe Dokument | angezeigt |
| ein Mitglied | eine Seite des Vorstands | abgelehnt |

## Checkliste

- [ ] Eine Mitgliedersektion, dem Mitgliederordner zugewiesen.
- [ ] Die Rolle Mitglied liest sie; Anonymous nicht.
- [ ] Stufen als eigene Sektionen und Rollen, falls nötig.
- [ ] Öffentliche Teaser für wichtige Mitgliederinhalte.
- [ ] Mitgliederdokumente innerhalb der Mitgliedersektion.
- [ ] Feeds abgemeldet geprüft.
- [ ] Moderatoren der Foren benannt und auf die Forensektion beschränkt.
- [ ] Die Testtabelle oben bestanden.

## Verwandte Seiten

- [Registrierung von Mitgliedern](registrierung-von-mitgliedern.md)
- [Sektionen und Objektzustände für Website-Ersteller](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Kunden- und Partnerbereiche](../extranets/kunden-und-partnerbereiche.md)
- [Berechtigungen für Besucher](../../ersteller/website-ersteller/rechte-fuer-besucher.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
