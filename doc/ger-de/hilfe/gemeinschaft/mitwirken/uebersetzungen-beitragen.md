---
title: Übersetzungen als Community-Mitglied beitragen
audience: community, supporter, editor, admin
category: community
order: 27
summary: Wie Community-Mitglieder Exponential in ihre Sprache bringen - wo die Übersetzungen der Oberfläche liegen, wie vollständig jede Sprache ist, Hilfeseiten unter der GNU FDL übersetzen, maschinengestützte Übersetzungen prüfen und mit anderen Sprechern Ihrer Sprache zusammenarbeiten.
keywords: Übersetzung, übersetzen, Sprache, Locale, translation.ts, share/translations, ger-de, rechts nach links, Übersetzung der Dokumentation, Prüfung, Muttersprachler, translation, translate, language, locale, right-to-left, documentation translation, review, native speaker
type: help
---
# Übersetzungen als Community-Mitglied beitragen

Eine Übersetzung erreicht alle Nutzer einer Sprache, oft Menschen, die nie ein englisches Handbuch lesen würden. Sie
ist einer der Beiträge, bei denen eine einzelne Person den größten Unterschied macht.

## Inhalt

1. [Was übersetzt werden kann](#was-übersetzt-werden-kann)
2. [Die Oberfläche übersetzen](#die-oberfläche-übersetzen)
3. [Die Hilfe übersetzen](#die-hilfe-übersetzen)
4. [Als Sprachteam arbeiten](#als-sprachteam-arbeiten)
5. [Qualität](#qualität)

## Was übersetzt werden kann

| Was | Wo | Seite |
|---|---|---|
| Die Meldungen der Administration | `share/translations/<locale>/translation.ts`, eine Datei pro Sprache | [Übersetzungen beitragen (Unterstützer)](../../unterstuetzer/uebersetzungen-beitragen.md) |
| Die Meldungen einer Erweiterung | Das eigene Verzeichnis `translations/` der Erweiterung | ebenda |
| Die Dokumentation und diese Hilfe | `doc/<locale>/`, zum Beispiel `doc/ger-de/` für Deutsch | [Die Hilfe in anderen Sprachen lesen](../../hilfe-in-anderen-sprachen-lesen.md) |

Welche Locales es gibt und wie vollständig jede ist: `doc/eng-us/languages.md` und die Hilfeseite
[Sprachen](../../sprachen.md).

## Die Oberfläche übersetzen

1. Prüfen Sie die Vollständigkeit Ihrer Sprache.
2. Forken Sie das Repository und öffnen Sie die Datei `translation.ts` der Sprache.
3. Übersetzen Sie die unübersetzten oder unfertigen Meldungen. Lassen Sie Platzhalter wie `%1` genau so, wie sie sind.
4. Stellen Sie die Administration auf Ihre Sprache um und sehen Sie sich die Bildschirme an, die Sie übersetzt haben.
5. Committen Sie mit einer Nachricht mit Präfix, etwa `Updated: German translation of the help dashboard`, und
   eröffnen Sie einen Pull Request ([Code beitragen](code-beitragen.md)).

Bei Sprachen, die von rechts nach links geschrieben werden, wie Arabisch und Hebräisch, prüfen Sie neben den Wörtern
auch das Layout ([Von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md)).

## Die Hilfe übersetzen

Die Hilfe ist freie Dokumentation, Sie dürfen sie also übersetzen ([Freie Dokumentation](../../freie-dokumentation.md)).
Eine übersetzte Seite liegt unter `doc/<locale>/` mit demselben relativen Pfad wie die englische Seite oder mit einem
übersetzten Dateinamen, der in `doc/<locale>/.paths.json` eingetragen ist; eine Seite, die es in der Sprache der
Lesenden noch nicht gibt, wird mit einem Hinweis auf Englisch angezeigt (`settings/exphelp.ini`, `[DocLanguages]`).
Stimmen Sie sich in einem Issue mit den Maintainern ab, bevor Sie viele Seiten übersetzen.

## Als Sprachteam arbeiten

- Finden Sie andere Sprecher in den Foren oder Chats und einigen Sie sich auf Begriffe, bevor Sie beginnen.
- Führen Sie ein kurzes Glossar der Begriffe, die Ihre Übersetzung verwendet.
- Teilen Sie die Arbeit nach Bildschirm oder Hilfebereich auf, nicht nach dem Alphabet.
- Prüfen Sie gegenseitig Ihre Arbeit, bevor sie eingereicht wird.

## Qualität

- Einheitlichkeit zählt mehr als Eleganz; verwenden Sie die Begriffe, die schon in der Übersetzung stehen.
- Produktnamen bleiben, wie sie sind: Exponential, 7x, die Exponential Foundation.
- Reichen Sie keine Maschinenübersetzungen ohne Prüfung ein. Der Verhaltenskodex verlangt, KI-gestützte Arbeit zu
  prüfen und zu sagen, wenn ein wesentlicher Teil generiert wurde
  ([Eine maschinengestützte Übersetzung prüfen](../../fuersprecher/maschinell-unterstuetzte-uebersetzung-gegenlesen.md)).

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Bei Übersetzungen helfen](../../fuersprecher/bei-uebersetzungen-helfen.md)
- [Übersetzungen und Sprachen](../../../funktionen/6.0/uebersetzungen-und-sprachen.md)
- [Sprachen](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
