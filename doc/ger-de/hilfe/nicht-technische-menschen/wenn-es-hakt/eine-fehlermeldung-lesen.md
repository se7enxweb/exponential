---
title: Eine Fehlermeldung lesen
audience: non-technical, editor, user
category: plain-language
order: 52
summary: Was die Meldungen von Exponential in einfachen Worten bedeuten - Meldungen zu Berechtigungen, die keine Fehler sind, "nicht gefunden", Bearbeitungskonflikte, Meldungen im Formular und echte Fehler mit einer ERR-Referenz - und genau, was Sie notieren und weitergeben.
keywords: Fehlermeldung, ERR-Referenz, Zugriff verweigert, nicht gefunden, konnte nicht angezeigt werden, etwas ist schiefgegangen, Formularfehler, was es bedeutet, nicht technisch, error message, ERR reference, access denied, not found, could not be displayed, something went wrong, form error, what it means, non-technical
type: help
---
# Eine Fehlermeldung lesen

Viele Meldungen, die beunruhigend aussehen, sind gar keine Fehler: Die Website sagt Ihnen, dass etwas nicht erlaubt
oder nicht vorhanden ist. Diese Seite sortiert sie.

## Inhalt

1. [Meldungen, die keine Fehler sind](#meldungen-die-keine-fehler-sind)
2. [Meldungen im Bearbeitungsformular](#meldungen-im-bearbeitungsformular)
3. [Ein echter Fehler](#ein-echter-fehler)
4. [Was Sie notieren](#was-sie-notieren)

## Meldungen, die keine Fehler sind

| Die Meldung sagt | In einfachen Worten | Was Sie tun |
|---|---|---|
| *Die angeforderte Seite konnte nicht angezeigt werden. (1)*, *Ihr Benutzerkonto hat nicht die notwendigen Privilegien ...* | Sie dürfen diese Seite nicht öffnen | Fragen Sie Ihren Administrator, wenn Sie sie brauchen |
| *Die angeforderte Seite konnte nicht angezeigt werden. (20)*, *Die angeforderte Adresse oder das Modul konnte nicht gefunden werden.* | Die Adresse ist falsch, oder dieser Teil existiert auf dieser Website nicht | Prüfen Sie die Adresse; verwenden Sie die Menüs |
| *Zugriff verweigert!* | Ihr Konto ist in Ordnung, aber Ihre Rolle darf das nicht verwenden | Fragen Sie Ihren Administrator |
| *Möglicher Bearbeitungs-Konflikt* | Es gibt schon einen Entwurf dieses Objekts | [Wenn jemand anderes bearbeitet](../../inhalte-bearbeiten.md#wenn-jemand-anderes-bearbeitet) |
| *Dieser Link ist abgelaufen.* (Passwort zurücksetzen) | Der Link zum Zurücksetzen ist älter als erlaubt | Fordern Sie einen neuen an |
| *This installation is a public demo* (diese Installation ist eine öffentliche Demo) | Eine Testwebsite, deren Administratorpasswort öffentlich ist | Geben Sie dort nie etwas Privates ein ([Demomodus](../../demomodus.md)) |

([Zuerst: Ist es ein Fehler?](../../wenn-etwas-schiefgeht.md#zuerst-ist-es-ein-fehler))

## Meldungen im Bearbeitungsformular

Wenn Sie **Zur Veröffentlichung schicken** drücken und etwas nicht stimmt, kommt das Formular mit einer Meldung oben
und am Feld zurück. Typische Gründe:

- ein **Pflichtfeld** (roter Stern) ist leer;
- ein Wert hat die falsche Form, zum Beispiel eine E-Mail-Adresse ohne `@` oder ein Datum im falschen Format;
- einem Bild fehlt der **Alternativtext**, wo die Website einen verlangt.

Korrigieren Sie das Feld und veröffentlichen Sie erneut. Ihre übrigen Änderungen bleiben erhalten.

## Ein echter Fehler

Ein echter Fehler der Website zeigt eine Seite wie **Something went wrong on our side** (bei uns ist etwas
schiefgegangen), mit einer **Referenz** ganz unten, zum Beispiel `ERR-0123456789`. Der Fehler ist bereits protokolliert;
mit der Referenz findet ein Administrator genau diesen Moment
([Wenn wirklich etwas schiefgegangen ist](../../wenn-etwas-schiefgeht.md#wenn-wirklich-etwas-schiefgegangen-ist)).

1. Kopieren oder fotografieren Sie die Referenz.
2. Versuchen Sie es noch einmal. Funktioniert es jetzt, erwähnen Sie trotzdem, dass es einmal geschehen ist.
3. Geben Sie die Referenz mit Ihren Notizen weiter.

## Was Sie notieren

| Notieren Sie | Beispiel |
|---|---|
| Die Referenz, falls vorhanden | `ERR-0123456789` |
| Die Uhrzeit | Dienstag, 10:42 Uhr |
| Die Adresse der Seite | aus der Adressleiste kopiert |
| Was Sie getan haben, Schritt für Schritt | Artikel *Sommerfest* geöffnet, Bearbeiten gedrückt, ein Foto hochgeladen, Zur Veröffentlichung schicken gedrückt |
| Was Sie erwartet haben und was geschah | Erwartet, dass der Artikel veröffentlicht wird; bekam die Fehlerseite |

Ein Bildschirmfoto hilft, solange es keine Passwörter oder personenbezogenen Daten anderer zeigt. Folgen Sie dann
[Wie Sie um Hilfe bitten](wie-sie-um-hilfe-bitten.md).

## Verwandte Seiten

- [Wenn es hakt](README.md)
- [Etwas sieht falsch aus](etwas-sieht-falsch-aus.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
