---
title: Demo-Skripte für Bewertungsgremien
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 60
summary: Eine nach Skript ablaufende Vorführung, die jeder Bieter, der Exponential (oder ein anderes CMS) anbietet, in derselben Reihenfolge durchführt, aufgebaut aus den dokumentierten Demo-Skripten der Hilfe, mit den zu zeigenden Aufgaben, den Nachweisen, die jede Aufgabe erbringt, und den Bildschirmen, die keine personenbezogenen Daten zeigen dürfen.
keywords: Demo-Skript, Vorführung nach Skript, Herstellerdemo, Bewertungsdemo, CMS-Demo, Ausschreibung, Bewertende, Rundgang für die Redaktion, Rundgang für die Administration, Installationsdemo, demo script, scripted demonstration, vendor demo, evaluation demo, CMS demo, tender, evaluator, editor tour, administrator tour, install demo
type: help
---
# Demo-Skripte für Bewertungsgremien

Diese Seite richtet sich an Beschaffungsstellen, die den Vorführungsteil einer Ausschreibung schreiben, und an Partner,
die eine Vorführung von Exponential für ein Bewertungsgremium vorbereiten. Eine Demo nach Skript verlangt von jedem
Bieter, dieselben Aufgaben in derselben Reihenfolge zu zeigen, sodass das Gremium Antworten auf die Anforderungen
vergleicht statt Verkaufspräsentationen. Die Schritte verwenden die dokumentierten Demo-Skripte des Pressebereichs dieser
Hilfe wieder, die die genauen Schaltflächen und die Quelle jeder Aussage nennen.

## Inhalt
1. [Regeln für die Demo nach Skript](#regeln-für-die-demo-nach-skript)
2. [Teil 1 - Installation](#teil-1---installation)
3. [Teil 2 - Aufgaben der Redaktion](#teil-2---aufgaben-der-redaktion)
4. [Teil 3 - Aufgaben der Administration](#teil-3---aufgaben-der-administration)
5. [Teil 4 - Barrierefreiheit](#teil-4---barrierefreiheit)
6. [Teil 5 - Ausstieg](#teil-5---ausstieg)
7. [Bildschirme, die Sorgfalt brauchen](#bildschirme-die-sorgfalt-brauchen)
8. [Notizblatt für Bewertende](#notizblatt-für-bewertende)

## Regeln für die Demo nach Skript

- Schicken Sie das Skript allen Bietern gleichzeitig, mit der für jeden Teil erlaubten Zeit.
- Der Bieter verwendet eine Demo-Installation ausschließlich mit **erfundenen Inhalten und erfundenen Konten**. Wie Sie
  eine solche Installation aufbauen, beschreibt
  [Eine Demo-Installation einrichten](../../../presse/eine-demo-installation-einrichten.md).
- Bewertende dürfen die vorführende Person bitten, einen Schritt zu wiederholen oder ihn nur mit der Tastatur
  auszuführen.
- Alles Gezeigte muss in der angebotenen Version existieren. Funktionen aus einem anderen Produkt oder einem künftigen
  Release werden als „nicht gezeigt“ festgehalten.
- Zeichnen Sie die Sitzung auf, wenn Ihre Regeln es erlauben, damit die Konsenssitzung eine Minute der Demo prüfen kann.

## Teil 1 - Installation

Quellskript: [Demo-Skript: Installation von null an](../../../presse/demo-skript-installation-von-null.md).

1. Zeigen Sie die Anforderungen des verwendeten Rechners ([Anforderungen](../../../../installation/02-voraussetzungen.md)).
2. Führen Sie die Installation mit einem Befehl mit SQLite aus: `./console exp:install --apply`
   ([Installation mit einem Befehl](../../../../funktionen/6.0/installation-mit-einem-befehl.md)).
3. Zeigen Sie, dass das generierte Administratorpasswort einmal ausgegeben und nach `var/log/initial-admin-password`
   geschrieben wird, und ändern Sie es bei der ersten Anmeldung.

Nachweis: die Zeit, die die Installation gedauert hat, und ob das Gremium sie im Proof of Concept wiederholen konnte.

## Teil 2 - Aufgaben der Redaktion

Quellskripte: [Demo-Skript: Rundgang für die Redaktion](../../../presse/demo-skript-rundgang-fuer-redakteure.md) und
[Überblick in fünf Minuten](../../../presse/demo-skript-ueberblick-in-fuenf-minuten.md).

1. Legen Sie eine Nachrichtenseite mit Titel, Text und einem Bild mit Alternativtext an und speichern Sie sie als
   Entwurf.
2. Sehen Sie sie in der Vorschau an, auch in Telefonbreite.
3. Schicken Sie sie zur Freigabe (wenn die Ausschreibung Freigaben verlangt; [Workflows](../../../../anleitungen/workflows.md)).
4. Veröffentlichen Sie sie, bearbeiten Sie sie dann erneut und vergleichen Sie die beiden Versionen
   ([Versionen](../../../../anleitungen/inhaltsverlauf.md)).
5. Übersetzen Sie sie in eine zweite Sprache; wenn Sie eine Sprache von rechts nach links brauchen, zeigen Sie eine
   ([Rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md)).
6. Finden Sie die Seite mit der Suche wieder.

## Teil 3 - Aufgaben der Administration

Quellskript: [Demo-Skript: Rundgang für die Administration](../../../presse/demo-skript-rundgang-fuer-administratoren.md).

1. Zeigen Sie einen Inhaltstyp (**Setup > Klassen**) und erklären Sie, welche Felder er hat.
2. Zeigen Sie die Rolle einer Redakteurin oder eines Redakteurs (**Benutzerkonten**, Rollen) und schränken Sie sie auf
   eine Sektion ein ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).
3. Zeigen Sie das Audit-Protokoll der Handlungen aus Teil 2 (`/audit/dashboard`, dann die Konsole;
   [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)).
4. Zeigen Sie die Einrichtung der zweistufigen Anmeldung, ohne sie abzuschließen
   ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).
5. Zeigen Sie **Setup > Upgrade-Prüfung** und **Setup > Systeminformationen** (nur den oberen Teil).
6. Zeigen Sie den Wartungsmodus ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)).

## Teil 4 - Barrierefreiheit

1. Wiederholen Sie die Schritte 1 und 4 aus Teil 2 nur mit der Tastatur. Das Design admin4 hat als ersten
   Tabulatorstopp einen Sprunglink und einen sichtbaren Fokus
   ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)).
2. Bitten Sie den Bieter, die öffentliche Seite mit der Tastatur und bei 200 % Zoom zu zeigen. Öffentliche Designs prüft
   das Projekt nicht: Was Sie sehen, ist die eigene Designarbeit des Bieters.
3. Fragen Sie, wo die Redaktion Alternativtext eingibt und was geschieht, wenn er leer ist: Nichts zwingt die Redaktion,
   ihn auszufüllen, wie die Seite zur Barrierefreiheit unter ihren bekannten Lücken sagt.

## Teil 5 - Ausstieg

1. Erstellen Sie ein Website-Archiv: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>`.
2. Öffnen Sie seine `manifest.json` und eine CSV-Datei einer Klasse
   ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)).
3. Zeigen Sie, dass das Archiv keine Passwort-Hashes enthält.

## Bildschirme, die Sorgfalt brauchen

Der Rundgang für die Administration listet sie auf: Systeminformationen (Pfade, Hostnamen), Logdateien, die
Audit-Konsole, Benutzerlisten, die Einrichtung der zweistufigen Anmeldung (QR-Code) und alles unterhalb von
`settings/override/`. Auf einer Demo-Installation mit erfundenen Daten sind sie unbedenklich; auf der Kopie einer echten
Website nicht. Akzeptieren Sie nie eine Demo auf der Kopie einer Produktionswebsite mit echten personenbezogenen Daten.
Mehr dazu: [Geheimnisse vom Bildschirm fernhalten](../../../presse/geheimnisse-vom-bildschirm-fernhalten.md).

## Notizblatt für Bewertende

```text
Bidder <name>     Segment <n>     Step <n>     Minute <mm:ss>
Requirement(s) shown: <ids>
Shown as asked: yes / partly / no / not shown
Keyboard only: yes / no / not tried
Notes (facts only):
```

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Presse: eine Demo-Installation einrichten](../../../presse/eine-demo-installation-einrichten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
