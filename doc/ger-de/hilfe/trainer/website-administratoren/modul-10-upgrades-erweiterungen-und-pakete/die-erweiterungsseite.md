---
title: "Lektion 10.4: Die Seite der Erweiterungen"
audience: trainer, admin
category: training
order: 104
summary: Die Teilnehmenden lesen die Karten der Erweiterungen unter Setup > Extensions, erklären die geschriebene Reihenfolge und die Ladereihenfolge, planen eine Aktivierung und eine riskante Deaktivierung, lesen die Überprüfung mit ihren Risiken und verwerfen den Plan, ohne etwas zu schreiben. 35 Minuten.
keywords: website-administration, erweiterungen, setup extensions, ActiveExtensions, ladereihenfolge, ExtensionOrdering, aktivieren, deaktivieren, änderungen prüfen, änderungen anwenden, änderungen verwerfen, ezformtoken, ezjscore, velocity neu starten, site administration, extensions, loading order, Activate, Deactivate, Review changes, Apply changes, Discard changes, Velocity restart
type: help
---
# Lektion 10.4: Die Seite der Erweiterungen

Das meiste, was eine Installation kann, kommt aus Erweiterungen, und ihre Reihenfolge entscheidet, welche
Einstellungen und Templates gewinnen. **Setup > Extensions** zeigt jede Erweiterung, die die Installation sieht,
welche aktiv sind und in welcher Reihenfolge, und ändert diese Liste mit einer Überprüfung, die die Risiken nennt,
bevor irgendetwas geschrieben wird. Diese Lektion vermittelt die Seite mit einem Plan, der überprüft und dann verworfen
wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 10: Upgrades, Erweiterungen und Pakete](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Lektion 10.1](ein-upgrade-planen-und-durchfuehren.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Karte einer Erweiterung lesen: Position, Version, Lizenz, Aktivierungszustand, Git-Checkout, Abhängigkeiten und
  Probleme;
- erklären, warum eine Erweiterung weiter oben in der Liste gewinnt und warum die Ladereihenfolge von der geschriebenen
  Reihenfolge abweichen kann;
- eine Aktivierung, eine Deaktivierung und eine Verschiebung planen und **Änderungen prüfen** lesen;
- zwei Deaktivierungen nennen, die die Überprüfung als riskant kennzeichnet, und warum;
- sagen, was nach dem Anwenden zu tun ist, wenn die Website auch von Velocity ausgeliefert wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Seite der Erweiterungen: Erweiterungen aktivieren, deaktivieren und ordnen](../../../../anleitungen/extensions-seite.md).

1. **Eine Liste** (3 Minuten). Zuerst die aktiven Erweiterungen in der Reihenfolge von
   `[ExtensionSettings] ActiveExtensions[]` in `settings/override/site.ini.append.php`, ab 1 nummeriert; danach die
   inaktiven, nach Namen. Weiter oben in der Liste bedeutet höhere Priorität: Liefern zwei Erweiterungen dieselbe
   Einstellung oder dasselbe Template, gewinnt die früher geladene.
2. **Die Karte** (3 Minuten). Nummer, Name und Version, Lizenz und Website, Aktivierungszustand, Zugriffserweiterung
   pro Siteaccess, **git** (mit Git aktualisieren, nicht mit einem Paket), Benötigt, Verwendet, Erweitert,
   Benötigt von, Lädt als Nummer *n*, Probleme und Details.
3. **Ladereihenfolge** (2 Minuten). Mit `ExtensionOrdering=enabled` (die Voreinstellung) sortiert das System die
   Erweiterungen nach ihren deklarierten Abhängigkeiten; die geschriebene Reihenfolge zählt nur dort, wo nichts
   deklariert ist.
4. **Planen, prüfen, anwenden** (5 Minuten). Während Sie arbeiten, wird nichts geschrieben: Die Leiste sagt, was sich
   ändern wird. **Änderungen prüfen** listet Aktivierungen, Deaktivierungen und Verschiebungen auf, die Risiken (eine
   Erweiterung, die eine andere benötigt; das einzige Design, das ein Siteaccess verwendet; `ezformtoken`, der Schutz
   jedes Formulars gegen Cross-Site Request Forgery; `ezjscore`, die Skripte der Administration) und die
   resultierenden Zeilen. Das Anwenden bewahrt eine Kopie der Override-Datei auf, schreibt nur die
   `ActiveExtensions[]`-Zeilen neu, leert die Caches und erzeugt die Autoloads neu.
5. **Velocity** (2 Minuten). Seine Worker behalten die Liste der Erweiterungen, mit der sie gestartet sind: Starten
   Sie Velocity nach dem Anwenden neu.

### Häufige Fragen

**Was bedeutet „Listed in ActiveExtensions, but its directory is missing“?** Die Einstellung nennt eine Erweiterung,
deren Verzeichnis nicht mehr existiert. Installieren Sie sie erneut, oder verwenden Sie **Aus der Liste entfernen**.

**Zwei Administratoren ändern die Liste gleichzeitig.** Die Seite bemerkt, dass sich die Datei geändert hat, und
beginnt von der Datei aus neu, statt die andere Änderung zu überschreiben.

**Warum kann ich keine Erweiterung herunterladen?** Der Export von Erweiterungen ist standardmäßig ausgeschaltet; er
braucht die Erweiterung in `package.ini [ExtensionExportSettings] AllowedExtensions[]` und die Richtlinie
`package/export_extension`.

### Stolperfallen

- **`ezformtoken` deaktivieren.** Jedes Formular verliert seinen Schutz gegen Cross-Site Request Forgery. Die
  Überprüfung kennzeichnet das rot; bestätigen Sie es auf einer echten Website nie.
- **Verschieben, wo Abhängigkeiten entscheiden.** Die Datei ändert sich, die Ladereihenfolge aber nicht; die
  Überprüfung sagt das.
- **Eine sofortige Änderung auf Velocity erwarten.** Starten Sie es neu.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, als Administrator angemeldet. Diese Übung schreibt nichts.

1. Öffnen Sie **Setup > Extensions**. Lesen Sie **Übersicht**: aktiv, inaktiv, nur für Siteaccesses, Git-Checkouts,
   Probleme.
2. Geben Sie unter **Extensions finden** `ezjscore` ein und klicken Sie auf **Anzeigen**. Lesen Sie seine Karte:
   Nummer, Version, Lizenz, **Benötigt von**.
3. Leeren Sie die Suche. Suchen Sie eine Karte, bei der **Lädt als Nummer** von ihrer geschriebenen Nummer abweicht,
   und erklären Sie, warum.
4. Suchen Sie eine inaktive Erweiterung und klicken Sie auf **Aktivieren**. Lesen Sie die Leiste, die sagt, was noch
   nicht angewendet ist.
5. Suchen Sie `ezformtoken` und klicken Sie auf **Deaktivieren**.
6. Klicken Sie auf **Änderungen prüfen**. Lesen Sie die Aktivierungen und Deaktivierungen, die rot gekennzeichneten
   Risiken und die resultierenden `ActiveExtensions[]`-Zeilen.
7. Bestätigen Sie nicht. Klicken Sie auf **Änderungen verwerfen** und prüfen Sie, dass die Leiste verschwunden ist.
8. Vergleichen Sie in einem Terminal mit der Datei:
   `grep -n "ActiveExtensions" settings/override/site.ini.append.php`.

## Erwartetes Ergebnis

- Schritt 3 findet eine Erweiterung, deren Ladeposition durch ihre deklarierten Abhängigkeiten bestimmt wird.
- Schritt 6 zeigt die Deaktivierung von `ezformtoken` als rot gekennzeichnetes Risiko, das bestätigt werden muss, bevor
  **Änderungen anwenden** akzeptiert wird.
- Nach Schritt 7 wurde nichts geschrieben: Die Liste in Schritt 8 ist dieselbe wie vor der Übung.
- Der Trainer prüft die Datei bei zwei Teilnehmenden mit dem Befehl aus Schritt 8.

![Die Seite der Erweiterungen: Zähler, Suche, Filter und eine Karte pro Erweiterung](../../../../../eng-us/help/images/trainers/sa-extensions.webp)

## Quiz

1. Zwei aktive Erweiterungen liefern dasselbe Template. Welche gewinnt?
2. Warum weicht die Ladereihenfolge auf den meisten Installationen von der geschriebenen Reihenfolge ab?
3. Nennen Sie zwei Deaktivierungen, die die Überprüfung als riskant kennzeichnet.
4. Was schreibt **Änderungen anwenden**?
   A. Die ganze Override-Datei B. Nur die `ActiveExtensions[]`-Zeilen C. `settings/site.ini` D. Die eigenen
   Einstellungen der Erweiterungen
5. Sie haben eine Änderung angewendet, und der Port von Velocity zeigt sie nicht. Was tun Sie?

<details>
<summary>Antworten</summary>

1. Die weiter oben in der Liste, die früher geladen wird.
2. Mit `ExtensionOrdering=enabled` sortiert das System die Erweiterungen nach ihren deklarierten Abhängigkeiten
   ([Die Seite der Erweiterungen, Abschnitt 4](../../../../anleitungen/extensions-seite.md#4-geschriebene-reihenfolge-und-ladereihenfolge)).
3. Zwei beliebige von: eine Erweiterung, die eine andere aktive Erweiterung benötigt; die einzige Erweiterung, die ein
   Design liefert, das ein Siteaccess verwendet; `ezformtoken`; `ezjscore`.
4. **B.** Es bewahrt zuerst eine Kopie der Datei auf und lässt jede andere Zeile, wie sie war.
5. Velocity neu starten (`exp:velocity restart`): Seine Worker behalten die Liste der Erweiterungen, mit der sie
   gestartet sind.

</details>

## Weiterführende Literatur

- [Die Seite der Erweiterungen](../../../../anleitungen/extensions-seite.md): Karten, Überprüfung, Anwenden und häufige
  Probleme.
- [Ladereihenfolge der Erweiterungen](../../../../funktionen/6.0/ladereihenfolge-der-extensions.md).
- [Liste der Erweiterungen und Downloads](../../../../funktionen/6.0/extension-liste-und-downloads.md).
- [Erweiterungen](../../../../anleitungen/extensions.md): die Anleitung für Entwickler.

## Verwandte Seiten

- [Modul 10: Upgrades, Erweiterungen und Pakete](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
