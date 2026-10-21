---
title: "Lektion 6.2: Weg A: zu Exponential Platform Nexus"
audience: trainer, developer
category: training
order: 62
summary: Entwickler planen einen Umzug innerhalb derselben Generation von eZ Platform oder Ibexa zu Exponential Platform Nexus - die Quelle auf die letzte Version ihrer Linie bringen, die Pakete austauschen, Konsolenbefehle umbenennen, Landing Pages mit Netgen Layouts neu aufbauen und überprüfen. 45 Minuten.
keywords: migration zu exponential, weg a, paketwechsel, konsolenbefehle, landing pages, versionsfallen, überprüfen, migrating to exponential, path A, Exponential Platform Nexus, package swap, composer, se7enxweb forks, replace, console commands, exponential:reindex, Page Builder, Netgen Layouts, version traps, verify
type: help
---
# Lektion 6.2: Weg A: zu Exponential Platform Nexus

Innerhalb einer Generation ist der Wechsel zu Nexus überwiegend eine Änderung in Composer. Diese Lektion vermittelt die
Reihenfolge, die Kapitel 16.5 vorgibt: die Quelle auf der letzten Version ihrer Linie, der Paketwechsel, die Namen der
Konsolenbefehle, der Seitenaufbau und die Prüfungen danach. Es ist eine Planungslektion; im Kurs installiert niemand
Nexus.

> **Hinweis**
> Führen Sie `exponential:install` niemals gegen eine Produktionsdatenbank aus: Es legt ein neues Repository an
> (Kapitel 16.5.1).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 6: Von den Symfony-basierten Plattformen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Symfony-Entwickler |
| Voraussetzungen | [Lektion 6.1](zwei-ziele-und-die-wahl.md); Grundlagen von Composer |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Nexus-Linie für eine Quellgeneration auswählen und sagen, was in das neue Projekt übernommen wird;
- die Upstream-Update-Schritte aufzählen, die eine Quelle auf die letzte Version ihrer Linie bringen;
- den Paketwechsel in `composer.json` für eine Generation schreiben;
- Namen von Konsolenbefehlen in Cron-Einträgen und Deployment-Skripten finden und umschreiben;
- Landing Pages des Page Builders als Inhaltsaufgabe mit Netgen Layouts planen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 16.5](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#165-weg-a-zu-exponential-platform-nexus).

1. **Die Generation wählen** (4 Minuten). Zeigen Sie die Tabelle aus 16.5.1. Eine Nexus-Linie bringt eine
   Demo-Website mit; für eine bestehende Website legen Sie das Projekt aus der Linie (oder dem passenden Skeleton) an
   und übernehmen Ihre eigenen `src/`, `templates/`, `translations/`, `assets/`, die Konfiguration und den Speicher.
2. **Zuerst die letzte Version** (4 Minuten). Die Forks folgen der letzten Upstream-Version jeder Linie. Folgen Sie auf
   der Quelle den Update-Schritten des Herstellers; übergeben Sie `mysql` oder `psql` niemals `--force`.
3. **Der Wechsel** (4 Minuten). Zeigen Sie die `composer.json` vorher und nachher aus 16.5.3: `ibexa/oss` wird zu
   `se7enxweb/oss`. Versionsfallen aus 16.1: Fünfteilige Tags sind nicht auf Packagist, und eine Nexus-Installation
   ohne Version kann einen Upstream-Tag erwischen.
4. **Befehlsnamen** (4 Minuten). Seit dem 2026-04-07 verwenden die Forks Namen mit `exponential:`; auf Platform v5
   existieren die alten `ibexa:`-Namen umbenannter Befehle nicht. Schreiben Sie Cron-Einträge und Deployment-Skripte
   vor der Umstellung um.
5. **Seitenaufbau** (4 Minuten). Der kommerzielle Page Builder hat in Nexus keine Entsprechung; kein Werkzeug wandelt
   Landing Pages in Layouts um. Planen Sie das als Inhaltsaufgabe (16.5.9).

### Häufige Fragen

**Muss unser eigener Code neu geschrieben werden?** Code, der für dieselbe Generation geschrieben wurde, läuft
unverändert: Die Namespaces bleiben die von Upstream (16.1). Was sich ändert, sind Befehlsnamen, Branding,
Lizenztexte und die Datenbank-Installer.

**Wie prüfen wir das Ergebnis?** Mit den Befehlen aus 16.8: `php bin/console list exponential`, `debug:router`,
`composer why ibexa/core` und den Statuscodes der Startseite und der Administration; dann die Anzahl der Inhalte
vorher und nachher.

**Können wir zurück?** Innerhalb einer Generation: `composer.json` und `composer.lock` wiederherstellen, installieren,
Caches leeren (16.9).

### Stolperfallen

- **Ein nacktes `composer create-project` von Nexus.** Nennen Sie immer eine Version.
- **Upstream und Fork nebeneinander.** Die Checkliste aus 16.11 sagt: Forks installiert, keine doppelten
  Upstream-Pakete.
- **Cron ruft auf v5 noch alte Befehlsnamen auf.** Die umbenannten Befehle sind dort nicht registriert; die Jobs
  scheitern unbemerkt, wenn Sie nicht nachsehen.

## Praktische Übung

Eine Planungsübung zu zweit, mit geöffnetem Kapitel 16.5. Das Szenario: ein Ibexa-OSS-4.6-Projekt mit Netgen
Layouts, zwei eigenen Bundles, Cron-Einträgen in `/etc/cron.d/site` und einem Deployment-Skript `deploy/release.sh`
sowie 35 Landing Pages, die mit dem Page Builder gebaut wurden.

1. Schreiben Sie anhand von [16.5.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1651-die-generation-wählen)
   die Ziel-Nexus-Linie und die Alternativen derselben Generation auf.
2. Schreiben Sie anhand von [16.5.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen)
   auf, was zuerst auf der Quelle geschehen muss.
3. Schreiben Sie den Abschnitt `require` der neuen `composer.json`, ausgehend vom Beispiel „nachher“ aus
   [16.5.3](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1653-die-pakete-austauschen).
4. Schreiben Sie den `grep`-Befehl aus [16.5.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1654-namen-der-konsolenbefehle),
   der alte Befehlsnamen im Cron-Verzeichnis und im Deployment-Skript findet, und notieren Sie, welche Zeile der
   Tabelle für Nexus `1.2.0.x` gilt.
5. Schreiben Sie anhand von
   [16.5.9](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1659-seitenaufbau-page-builder-gegenüber-netgen-layouts)
   einen Plan in drei Schritten für die 35 Landing Pages, einschließlich des SQL, das sie zählt.
6. Schreiben Sie die Prüfbefehle aus [16.8](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#168-die-migration-prüfen)
   auf, die Sie nach dem Wechsel ausführen werden, und die drei Zählabfragen.

## Erwartetes Ergebnis

- Schritt 1: Nexus `1.2.0.x`; Alternativen `se7enxweb/exponential-platform-v4x-dxp-skeleton` und Platform Legacy
  `v4.6.23.x`.
- Schritt 2: die Quelle auf 4.6 LTS und deren neuesten Patch, mit den Schritten des Herstellers und ohne `--force`.
- Schritt 3: `se7enxweb/oss` `~4.6.0` ersetzt `ibexa/oss`, mit der PHP-Einschränkung der Nexus-Linie.
- Schritt 4: die Zeile `grep -rnE` für Befehle mit `ibexa|ezplatform|ezpublish`; für Nexus `1.2.0.x` bleiben die
  Hauptnamen `ibexa:*`, weil dessen Lock-Datei Upstream-`ibexa/core` installiert.
- Schritt 5: mit `SELECT COUNT(*) ... WHERE data_type_string = 'ezlandingpage'` auflisten, jede als Layout mit einer
  Regel neu aufbauen, dann das Feld entfernen oder ignorieren.
- Der Trainer prüft die Schritte 3 und 5.

## Quiz

1. Was übernehmen Sie aus dem alten Projekt in ein neues Projekt, das aus einer Nexus-Linie angelegt wurde?
2. Warum muss eine Nexus-Installation immer eine Version nennen?
   A. Composer ist ohne Version langsam B. Das Repository behält die Upstream-Tags, sodass eine Upstream-Version
   installiert werden kann C. Packagist verlangt es D. Nexus hat nur eine Version
3. Welche Option darf `mysql` oder `psql` beim Ausführen von Update-Skripten niemals übergeben werden?
4. Gibt es `ibexa:reindex` auf Platform v5 noch?
5. Welches Werkzeug wandelt Landing Pages des Page Builders in Layouts von Netgen Layouts um?

<details>
<summary>Antworten</summary>

1. Ihre eigenen `src/`, `templates/`, `translations/`, `assets/`, die Konfiguration und den Speicher, auf Ihre
   Datenbank ausgerichtet
   ([Kapitel 16.5.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1651-die-generation-wählen)).
2. **B** ([Kapitel 16.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele)).
3. `--force`: Eine fehlschlagende Anweisung muss den Lauf anhalten
   ([Kapitel 16.5.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen)).
4. Nein. Auf v5 sind keine Aliase registriert; der Befehl heißt `exponential:reindex`
   ([Kapitel 16.5.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1654-namen-der-konsolenbefehle)).
5. Keines; planen Sie es als Inhaltsaufgabe
   ([Kapitel 16.5.9](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1659-seitenaufbau-page-builder-gegenüber-netgen-layouts)).

</details>

## Weiterführende Informationen

- [Kapitel 16.5: Weg A](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#165-weg-a-zu-exponential-platform-nexus):
  jeder Schritt mit den Details der Generation.
- [Exponential Platform Nexus](../../../../installation/migration/exponential-platform-nexus.md): die Anleitung zur
  Familie.
- [Paket-Forks und umbenannte Befehle](../../../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md): was sich in
  den Forks geändert hat.

## Verwandte Seiten

- [Modul 6: Von den Symfony-basierten Plattformen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
