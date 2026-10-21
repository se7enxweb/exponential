---
title: "Lektion 4.5: Sicherheit und Datenschutz im Kurs"
audience: trainer, admin
category: training
order: 45
summary: Halten Sie einen Kurs sicher: Schulungsinstallationen an den lokalen Rechner gebunden, keine gemeinsamen Administratorkonten, Testadressen unter example.invalid, keine echten Daten, Passwörter, die die Regeln erfüllen, Sicherheitsprobleme vertraulich gemeldet und die Übungsumgebung danach entfernt. 40 Minuten.
keywords: sicherheit, datenschutz, kursraum, passwörter, example.invalid, personenbezogene daten, SECURITY.md, übungsumgebung, aufräumen, zwei-faktor, security, privacy, classroom, passwords, personal data, training lab, cleanup, two-factor
type: help
---
# Lektion 4.5: Sicherheit und Datenschutz im Kurs

Schulungsinstallationen sind oft die am schlechtesten geschützten Websites, die eine Organisation betreibt:
Standardports, einfache Passwörter, danach vergessen. Die Teilnehmenden übernehmen, was sie im Kurs sehen, in ihre
echte Arbeit. Diese Lektion macht Sicherheit und Datenschutz zu einem Teil jedes Kurses, von der Übungsumgebung bis zum
Aufräumen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 4: Einen Kurs durchführen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer und Administratoren von Übungsumgebungen |
| Voraussetzungen | [Modul 2](../modul-02-die-uebungsumgebung-aufbauen/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Sicherheitsregeln für den Kursraum anwenden: lokale Bindung, eigene Konten, echte Passwörter, keine echten Daten;
- erklären, warum Testkonten `example.invalid` verwenden;
- den Teilnehmenden sagen, wohin Sicherheitsprobleme gehören (vertraulich, nie in ein öffentliches Issue);
- eine Übungsumgebung nach einem Kurs vollständig entfernen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Die Regeln (8 Minuten):
   - Übungsumgebungen an `127.0.0.1` gebunden (die Voreinstellung von Velocity) oder hinter einem richtigen Webserver
     mit HTTPS;
   - ein Konto pro Person, niemals ein gemeinsamer Administrator;
   - Passwörter, die die Mindestlänge der Website erfüllen; erzeugte Passwörter bei der ersten Anmeldung geändert;
   - nur erfundene Inhalte und Testadressen unter `example.invalid`; keine E-Mails an echte Menschen;
   - keine echten Kundendaten, auch nicht „nur für die Übung“.
2. Sicherheitsmeldungen (3 Minuten): Die Seite **Fehler melden** des Hilfe-Centers hat einen Teil
   **Sicherheitsprobleme**; eine Schwachstelle wird vertraulich gemeldet, wie `SECURITY.md` es beschreibt, niemals in
   einem Forum oder einem öffentlichen Issue.
3. Aufräumen (4 Minuten): Server stoppen, Schulungskonten von gemeinsamen Websites entfernen, Verzeichnisse der
   Übungsumgebung löschen, die Sie angelegt haben.

### Häufige Fragen

**Sollen wir im Kurs die zweistufige Anmeldung einschalten?** Auf einer gemeinsamen Schulungs-Website ist es gute
Praxis, sie zu zeigen; der Kurs Website-Administration hat eine Lektion dazu, und die Anleitung ist
[Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md).

**Was ist mit den eigenen Laptops der Teilnehmenden?** Die Übungsumgebung liegt in einem Verzeichnis, das ihnen gehört;
sagen Sie ihnen, wie sie nach dem Kurs den Server stoppen und das Verzeichnis entfernen.

### Stolperfallen

- **Offene Ports in einem Konferenznetz.** Binden Sie eine Übungsumgebung in einem öffentlichen Netz niemals an alle
  Adressen.
- **„Vorübergehend“ echte Daten.** Sie bleiben nie vorübergehend.
- **Vergessene gemeinsame Websites.** Ein Schulungsserver, der mit schwachen Konten online bleibt, ist ein Risiko;
  schalten Sie ihn ab oder räumen Sie ihn auf.

## Praktische Übung

1. Führen Sie auf Ihrer Schulungsinstallation `php bin/php/console exp:velocity status` aus und prüfen Sie die Adresse,
   auf der die Website lauscht (für eine Übungsumgebung auf einem Laptop sollte es `127.0.0.1` sein).
2. Öffnen Sie **Benutzer** und sehen Sie jede Benutzergruppe durch. Notieren Sie jedes Konto mit einer Adresse, die
   nicht unter `example.invalid` liegt, und jedes Konto, das sich mehrere Personen teilen.
3. Öffnen Sie die Seite **Fehler melden** des Hilfe-Centers und lesen Sie **Sicherheitsprobleme**.
4. Schreiben Sie eine Aufräum-Checkliste für das Ende Ihres Kurses: Server stoppen; Konten der Teilnehmenden auf
   gemeinsamen Websites entfernen; Verzeichnisse der Übungsumgebung löschen; alle Kopien von
   `var/log/initial-admin-password` löschen.
5. Proben Sie das Aufräumen an einer Übungsumgebung: Stoppen Sie den Server mit
   `php bin/php/console exp:velocity stop`.

## Erwartetes Ergebnis

- Die Übungsumgebung lauscht nur auf `127.0.0.1`.
- Die Benutzerliste enthält keine echte E-Mail-Adresse und kein gemeinsames Konto (oder die Teilnehmenden haben
  aufgeschrieben, was zu korrigieren ist).
- Eine Aufräum-Checkliste mit mindestens vier Punkten.

## Quiz

1. Warum sollte eine Übungsumgebung auf einem Laptop auf `127.0.0.1` lauschen?
2. Wo muss eine während eines Kurses gefundene Schwachstelle gemeldet werden?
3. Warum verwendet man `example.invalid` für Testkonten?
4. Nennen Sie zwei Punkte des Aufräumens am Kursende.

<details>
<summary>Antworten</summary>

1. Sie ist nur vom Rechner selbst aus erreichbar; der Entwicklungsserver hat keine eigene Authentifizierung.
2. Vertraulich, wie `SECURITY.md` es beschreibt ([Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md)).
3. Die Domain kann nie E-Mails empfangen, sodass kein Test eine E-Mail an eine echte Person schicken kann.
4. Zwei beliebige von: Server stoppen, Konten der Teilnehmenden entfernen, Verzeichnisse der Übungsumgebung löschen,
   Passwortdateien löschen.

</details>

## Weiterführende Literatur

- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Sicherheitsmeldungen bleiben vertraulich](../../../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md)
- [Die Privatsphäre von Menschen schützen](../../../fuersprecher/privatsphaere-schuetzen.md)

## Verwandte Seiten

- [Modul 4: Einen Kurs durchführen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
