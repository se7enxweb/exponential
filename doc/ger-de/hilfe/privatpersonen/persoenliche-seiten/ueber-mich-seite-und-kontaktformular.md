---
title: Über-mich-Seite und Kontaktformular
audience: individual, website-creator, editor
category: personal-sites
order: 1603
summary: Wie eine Person eine Über-mich-Seite schreibt, die funktioniert, und mit der mitgelieferten Klasse Feedback form ein Kontaktformular einrichtet - wo Nachrichten gespeichert und gemailt werden, wie Sie Ihre E-Mail-Adresse privat halten, wie Sie Spam stoppen und was Sie Besuchern über die vom Formular erfassten Daten sagen.
keywords: Über-mich-Seite, Kontaktformular, Feedback form, Informationssammler, Empfänger, E-Mail-Adresse, Spam, CAPTCHA, Formular-Token, Datenschutzhinweis, gesammelte Informationen, persönliche Website, about page, contact form, recipient, privacy notice
type: help
---
# Über-mich-Seite und Kontaktformular

Diese Seite ist für Privatpersonen, deren Besucher wissen sollen, wer hinter der Website steht, und schreiben können
sollen, ohne dass Ihre E-Mail-Adresse auf einer Seite steht. Beides ist schnell gebaut; beides zählt mehr als jede
andere Seite einer persönlichen Website.

## Inhalt

1. [Die Über-mich-Seite](#die-über-mich-seite)
2. [Das Kontaktformular: was mitgeliefert wird](#das-kontaktformular-was-mitgeliefert-wird)
3. [Das Formular einrichten](#das-formular-einrichten)
4. [Wohin Nachrichten gehen](#wohin-nachrichten-gehen)
5. [Spam fernhalten](#spam-fernhalten)
6. [Besuchern sagen, was Sie speichern](#besuchern-sagen-was-sie-speichern)
7. [Checkliste](#checkliste)

## Die Über-mich-Seite

Ein *Article* mit Titel, Einleitung und Text genügt. Ein Aufbau, der für die meisten funktioniert:

| Teil | Inhalt | Länge |
|---|---|---|
| Einleitung | Wer Sie sind und was Sie tun, in einem Satz | Eine Zeile |
| Bild | Ein Foto oder ein Bild, das für Ihre Arbeit steht, mit Alternativtext | Ein Bild |
| Text: jetzt | Woran Sie arbeiten | Ein Absatz |
| Text: vorher | Der Weg, der hierher geführt hat | Ein Absatz oder eine kurze Liste |
| Text: als Nächstes | Was Sie suchen (Arbeit, Mitstreiter, Leser) | Ein Satz |
| Link | „Schreiben Sie mir", mit Verweis auf das Kontaktformular | Ein Link |

Schreiben Sie in der ersten Person, halten Sie den Text aktuell und lassen Sie weg, was Sie einem Fremden nicht
erzählen würden ([Eine persönliche Website planen](eine-persoenliche-website-planen.md#was-sie-über-sich-veröffentlichen)).

## Das Kontaktformular: was mitgeliefert wird

Das Standard-Seitenpaket hat eine Klasse *Feedback form* mit diesen Feldern: Name, Beschreibung, Name des Absenders,
Betreff, Nachricht, E-Mail, Empfänger und Metadaten (die Klassendefinition im Paket `sevenx_classes`). Die Felder für
Besucher sind Felder des *Informationssammlers*: Was ein Besucher eintippt, wird von der Website gespeichert und kann
gemailt werden ([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

## Das Formular einrichten

1. Legen Sie ein Objekt *Feedback form* dort an, wo Ihre Kontaktseite sein soll, zum Beispiel unter dem Ordner der
   Startseite.
2. Geben Sie ihm einen Namen („Kontakt") und eine Beschreibung, die sagt, worüber man schreiben kann und wie schnell
   Sie antworten.
3. Setzen Sie den **Empfänger** auf die Adresse, die Nachrichten erhalten soll. Sie wird am Objekt gespeichert und
   Besuchern nicht angezeigt.
4. Veröffentlichen Sie es und öffnen Sie die Seite als Besucher (in einem privaten Browserfenster). Anonyme Besucher
   müssen die Seite des Formulars lesen können, um es abzusenden; halten Sie es also in einer Sektion, die sie lesen
   dürfen. Wenn das Formular nicht erscheint oder nicht sendet, siehe
   [Besucherberechtigungen](../../ersteller/website-ersteller/rechte-fuer-besucher.md).
5. Schicken Sie sich selbst eine Testnachricht und prüfen Sie, dass sie ankommt.

Die ausführliche Fassung für Website-Ersteller, einschließlich eines Formulars in eigenem Design, ist
[Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md).

## Wohin Nachrichten gehen

| Ort | Was Sie dort finden |
|---|---|
| Ihr Postfach | Eine E-Mail pro Nachricht, wenn ausgehende E-Mail auf Ihrem Server funktioniert |
| Die Administration | Jede Einsendung, gespeichert, auch wenn die E-Mail fehlschlug ([Formulareinsendungen](../../ersteller/website-ersteller/formulareinsendungen.md), [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |

Wenn Testnachrichten nicht ankommen, ist meist das Formular in Ordnung und die E-Mail nicht: Der Server kann nicht
senden, oder die E-Mail landet im Spam. Das ist eine Frage an das Hosting
([E-Mail-Einstellungen für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)). Sehen Sie trotzdem
ab und zu in die gespeicherten Einsendungen.

## Spam fernhalten

Ein öffentliches Formular erhält innerhalb weniger Tage Spam. Nutzen Sie die mitgelieferten Ebenen:

- **Formular-Token** (`ezformtoken`, Teil der Standardinstallation) stoppen gefälschte Einsendungen von anderen
  Websites.
- **Ein CAPTCHA-Feld**: `recaptcha` oder `hcaptcha` fügen einen Datentyp hinzu, den Sie an die Klasse setzen; jeder
  braucht Schlüssel seines Dienstes.
- **Beschränken Sie, was anonyme Besucher erstellen dürfen**, auf das Formular allein.

Siehe [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md) und
[Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md).

## Besuchern sagen, was Sie speichern

Das Formular speichert Namen, Adresse und Nachricht des Absenders auf Ihrem Server. Sagen Sie das in ein oder zwei
Sätzen neben dem Formular oder auf einer Datenschutzseite: was gespeichert wird, warum und wann Sie es löschen.
Löschen Sie alte Einsendungen dann tatsächlich nach diesem Plan
([Aufbewahrung und Löschung von Daten](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)). Das sind
allgemeine Informationen, keine Rechtsberatung.

## Checkliste

- [ ] Über-mich-Seite: Einleitung, Bild mit Alternativtext, aktueller Text, Link zu Kontakt.
- [ ] Feedback form mit dem richtigen Empfänger veröffentlicht.
- [ ] Testnachricht per E-Mail angekommen und in der Administration gespeichert.
- [ ] Formular-Token aktiv; CAPTCHA ergänzt, wenn Spam ankommt.
- [ ] Ein Satz dazu, was das Formular speichert und wie lange.
- [ ] Eine Erinnerung, alte Einsendungen zu löschen.

## Verwandte Seiten

- [Persönliche Websites](README.md)
- [Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)
- [Formulare, Umfragen und Anmeldungen](../vereinsseiten/formulare-umfragen-und-anmeldungen.md)
- [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
