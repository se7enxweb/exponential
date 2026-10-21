---
title: Fehler-Templates
audience: designer, developer
category: templates
order: 730
summary: Die Fehlerseiten gestalten (Zugriff verweigert, nicht gefunden, Modul nicht gefunden, Formular-Token abgelehnt ...) - die Templates error/kernel/<number>.tpl und was jede Nummer bedeutet, der gesendete HTTP-Status, das bei verweigertem Zugriff eingebettete Anmeldeformular, lesbare Titel und eine hilfreiche Seite „nicht gefunden“.
keywords: Fehler-Templates, Fehlerseiten, 404-Seite, Seite Zugriff verweigert, error/kernel, error.ini, HTTPError, EmbedURL, nicht gefunden, Kernel-Fehler, Fehlernummern, error templates, error pages, 404 page, access denied page, not found, kernel errors, error numbers
type: help
---
# Fehler-Templates

Diese Seite ist für Designer, die die Seiten gestalten, die Besucher sehen, wenn etwas nicht verfügbar ist. Fehlerseiten
sind häufig (jeder vertippte Link) und verdienen dieselbe Sorgfalt wie Inhaltsseiten.

## Inhalt

1. [Die Templates und ihre Nummern](#die-templates-und-ihre-nummern)
2. [Statuscodes und Handler](#statuscodes-und-handler)
3. [Eine hilfreiche Seite „nicht gefunden“](#eine-hilfreiche-seite-nicht-gefunden)
4. [Titel von Fehlerseiten](#titel-von-fehlerseiten)

## Die Templates und ihre Nummern

`design/standard/templates/error/kernel/<number>.tpl`, wobei die Nummer ein Fehlercode des Kernels ist (Konstanten von
`eZError` in `lib/ezutils/classes/ezerror.php`):

| Nummer | Bedeutung | Template |
|---|---|---|
| 1 | Zugriff verweigert | `error/kernel/1.tpl` |
| 2 | nicht gefunden | `error/kernel/2.tpl` |
| 3 | nicht verfügbar | `error/kernel/3.tpl` |
| 4 | verschoben | `error/kernel/4.tpl` |
| 5 | Sprache nicht gefunden | `error/kernel/5.tpl` |
| 6 | Formular-Token abgelehnt | `error/kernel/6.tpl` |
| 20, 21, 22 | Modul nicht gefunden, View nicht gefunden, Modul deaktiviert | `error/kernel/20.tpl` ... |

Kopieren Sie die, die Sie umgestalten, an denselben Pfad in Ihr Design. Das Design media hat sein eigenes `templates/error/`
mit `kernel/`, `shop/` und gemeinsamen `parts/`.

## Statuscodes und Handler

`settings/error.ini [ErrorSettings-kernel]`:

- `HTTPError[<number>]` setzt den HTTP-Status (mitgeliefert: 1 ist 401, 2, 3 und 20 bis 22 sind 404, 4 ist 301, 6 ist 403);
- `ErrorHandler[1]=embed` mit `EmbedURL[1]=/user/login`: Ein anonymer Besucher, dem der Zugriff verweigert wird, sieht das
  Fehler-Template mit eingebettetem Anmeldeformular und kann sich sofort anmelden.

Das Pagelayout rahmt Fehlerseiten wie jede andere Seite.

## Eine hilfreiche Seite „nicht gefunden“

- Sagen Sie in einfachen Worten, was passiert ist, und behalten Sie Kopfbereich und Navigation der Website.
- Bieten Sie ein Suchformular und einen Link zur Startseite an.
- Geben Sie die angeforderte URL nicht unmaskiert aus.

## Titel von Fehlerseiten

Der Pfad einer Fehlerseite endet auf den Fehlercode („kernel (20)“), was Besuchern nichts sagt. `$module_result` trägt
`errorType` und `errorNumber`; die `pagelayout/head/title.tpl` des Designs media verwendet ein switch darauf, um jeder
Fehlerseite einen lesbaren `<title>` zu geben ([Module result und content info](module-result-und-content-info.md)).

## Verwandte Seiten

- [Benutzer- und Anmelde-Templates](benutzer-und-anmelde-templates.md)
- [Suchergebnis-Template](suchergebnis-template.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
