---
title: Sicherheitsupdates und Offenlegung
audience: businesses, admin, project-manager
category: business-solutions
order: 70
summary: Was ein Unternehmen über Sicherheitskorrekturen für Exponential wissen muss - welche Versionen unterstützt werden, wie Korrekturen veröffentlicht werden, wie Sie erkennen, ob eine Website gepatcht ist, wer Updates einspielt und wie schnell, und wie Sie eine Schwachstelle vertraulich melden.
keywords: Sicherheitsupdates, Patches, unterstützte Versionen, Schwachstelle, verantwortungsvolle Offenlegung, vertrauliche Meldung, Release, Upgrade-Prüfung, Update-Richtlinie, security updates, patches, supported versions, SECURITY.md, vulnerability, responsible disclosure, private report, security@se7enx.com, release, 6.0.x, composer audit, upgrade check, update policy
type: help
---
# Sicherheitsupdates und Offenlegung

Diese Seite richtet sich an Inhaber und Führungskräfte, die dafür sorgen müssen, dass ihre Website Sicherheitskorrekturen
bekommt, und die wissen müssen, was zu tun ist, wenn jemand eine Schwachstelle findet. Sie erklärt die Richtlinie des
Projekts aus `SECURITY.md`, was sie für Ihr Budget und Ihre Verträge bedeutet und wie Sie ein Problem richtig melden.

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Wenn eine Schwachstelle personenbezogene Daten offengelegt hat, können für Sie
> Meldepflichten gelten; fragen Sie sofort einen Anwalt oder Ihren Datenschutzbeauftragten.

## Inhalt

1. [Welche Versionen Korrekturen bekommen](#welche-versionen-korrekturen-bekommen)
2. [Wie Korrekturen veröffentlicht werden](#wie-korrekturen-veröffentlicht-werden)
3. [Ist meine Website gepatcht?](#ist-meine-website-gepatcht)
4. [Wer Updates einspielt und wie schnell](#wer-updates-einspielt-und-wie-schnell)
5. [Eine Schwachstelle melden](#eine-schwachstelle-melden)
6. [Erweiterungen und alles rund um die Website](#erweiterungen-und-alles-rund-um-die-website)
7. [Checkliste](#checkliste)

## Welche Versionen Korrekturen bekommen

Aus `SECURITY.md`:

| Version | Unterstützt |
|---|---|
| 6.0.x, die aktuelle Linie (`main` und das nächste daraus erstellte Tag) | ja |
| Veröffentlichte Tags von 6.0.x (`v6.0.0` bis `v6.0.14`) | korrigiert durch ein Upgrade auf das neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | nein |

Wenn Ihre Website noch eine Version aus den Jahren von eZ Publish (3.x, 4.x oder 5.x) betreibt, bekommt sie keine
Sicherheitskorrekturen. Der Umstieg auf Exponential 6 ist ein Migrationsprojekt; siehe
[Zu Exponential wechseln](../auswahl/zu-exponential-wechseln.md).

## Wie Korrekturen veröffentlicht werden

- „Sicherheitskorrekturen werden auf dem Branch `main` vorgenommen ... und mit dem nächsten Release 6.0.x
  veröffentlicht.“ (`SECURITY.md`)
- „Ein veröffentlichtes Tag wird nie geändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag
  erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten.“ (`SECURITY.md`)

Für ein Unternehmen ist das eine gute Nachricht: Eine Versionsnummer bedeutet immer denselben Code, also hat die Frage
„Sind wir gepatcht?“ eine klare Antwort, und eine Version, die Sie getestet haben, ändert sich nicht unter Ihnen
([Sicherheitsupdates und Offenlegung für Hoster](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md#wie-korrekturen-veröffentlicht-werden)).

## Ist meine Website gepatcht?

Bitten Sie Ihren Administrator oder Hoster, Ihnen zu sagen:

1. Welche Version die Website betreibt (`composer show se7enxweb/exponential`), verglichen mit dem neuesten Release
   6.0.x.
2. Ob eine fixierte Abhängigkeit eine bekannte Schwachstelle hat (`composer audit`).
3. Ob **Setup > Upgrade-Prüfung** geänderte oder fehlende Dateien meldet; sie liest nur
   ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
4. Ob die Erweiterungen aktuell sind; **Setup > „Updates and packages“** von `ezupdate` prüft, ohne etwas zu ändern
   ([Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md)).

Eine geänderte Kernel-Datei, die niemand absichtlich geändert hat, verdient einen genaueren Blick
([Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)).

## Wer Updates einspielt und wie schnell

Das Projekt legt keinen Zeitrahmen für das Einspielen einer Korrektur fest; das entscheiden Sie oder Ihr Hoster
([SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#sicherheitsupdates)). Entscheiden Sie und schreiben Sie auf:

| Frage | Beispielantwort zum Anpassen |
|---|---|
| Wer achtet auf Releases? | unser Administrator, monatlich und wenn das Projekt eines ankündigt |
| Wer spielt sie ein? | unser Hosting-Partner, im Rahmen des Vertrags |
| Innerhalb welcher Zeit nach einem Sicherheits-Release? | [Anzahl] Werktage, in einem geplanten Zeitfenster |
| Was, wenn es nicht sofort eingespielt werden kann? | die Lücke wird mit einem Datum für ihre Schließung dokumentiert |
| Wer genehmigt? | der Verantwortliche der Website, oder für Sicherheits-Releases vorab genehmigt |

> **Tipp**
> Versprechen Sie, eine veröffentlichte Korrektur innerhalb einer bestimmten Zeit einzuspielen, nicht, dass eine
> Schwachstelle innerhalb einer bestimmten Zeit behoben wird: Das Zweite hängt vom Projekt ab, das Erste von Ihnen
> ([SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#sicherheitsupdates)).

Wie ein Update abläuft, kurz gefasst: Changelog lesen, eine Staging-Kopie aktualisieren, dann die Live-Website, die
Dateikonsistenz prüfen und die Startseite und die Anmeldung testen
([Sicherheitsupdates und Offenlegung für Hoster](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md#updates-ausrollen)).
Budgetieren Sie die Stunden mit [Budget für Aktualisierungen und Wartung](../kosten/budget-fuer-aktualisierungen-und-wartung.md).

## Eine Schwachstelle melden

Wenn Sie, ein Kunde oder ein Forscher ein Sicherheitsproblem in Exponential findet:

1. Veröffentlichen Sie es **nicht** im öffentlichen Issue-Tracker, in den Foren oder in sozialen Netzwerken.
2. Melden Sie es vertraulich an **security@se7enx.com** (`SECURITY.md`) oder als privates Advisory auf GitHub
   (<https://github.com/se7enxweb/exponential/security/advisories/new>).
3. Nennen Sie die Version, die PHP-Version, die Engine (Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM), die
   Datenbank, was ein Angreifer tun kann, und die Schritte, um es nachzuvollziehen.
4. Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.

„Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen.“ Sie erfahren, ob die Meldung angenommen wird,
und werden auf dem Laufenden gehalten, während eine Korrektur vorbereitet wird (`SECURITY.md`). Probleme in einer
Erweiterung Dritter gehen an deren Betreuer
([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md#sicherheitsprobleme)).

Sagen Sie Ihren eigenen Mitarbeitern und Partnern, wohin solche Meldungen gehen, zum Beispiel in Ihrer
[Richtlinie für Konten und Passwörter](benutzerkonten-und-passwoerter.md#ein-richtlinientext-zum-anpassen). Partner haben ihren
eigenen Eskalationsweg ([Eskalationen und Sicherheitsprobleme](../../partner/eskalationen-und-sicherheitsprobleme.md)).

## Erweiterungen und alles rund um die Website

Eine Website ist nur so aktuell wie ihr schwächster Teil:

- [ ] Jede Erweiterung ist ein eigenes Composer-Paket mit eigenen Releases.
- [ ] PHP, der Datenbankserver und das Betriebssystem brauchen ebenfalls Updates.
- [ ] Die Engine von Velocity hat eigene Releases.
- [ ] Ihre eigenen Erweiterungen und Templates müssen auf unsichere Muster geprüft werden; die Härtungshinweise des
      Projekts listen sie auf
      ([Sicherheitsupdates und Offenlegung für Hoster](../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md#alles-andere-was-updates-braucht)).

## Checkliste

- [ ] Die Website betreibt eine unterstützte Version 6.0.x.
- [ ] Eine namentlich benannte Person achtet auf Releases.
- [ ] Die Zeit zum Einspielen eines Sicherheits-Releases steht in Ihrem Vertrag oder Ihrer Richtlinie.
- [ ] Updates gehen zuerst auf eine Staging-Kopie.
- [ ] Die Upgrade-Prüfung ist nach jedem Update sauber.
- [ ] Die Mitarbeiter wissen, dass sie Schwachstellen vertraulich an security@se7enx.com melden.
- [ ] Es gibt einen Plan, wie Kunden informiert werden, wenn ihre Daten betroffen waren.

## Verwandte Seiten

- [Release-Linie und Support-Richtlinie](../zuverlaessigkeit/release-linie-und-support-richtlinie.md)
- [Upgrades ohne Überraschungen](../zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)
- [Checkliste für Sicherheitsprüfungen für Hoster](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
