---
title: "Lektion 2.3: Host und Pfad zusammen und Zuordnung nach Port"
audience: trainer, admin, developer
category: training
order: 23
summary: Die Teilnehmenden ordnen einen Host und ein Pfadsegment mit HostUriMatchMapItems einem Siteaccess zu, bringen die Einträge in die richtige Reihenfolge und lesen, wie die Port-Zuordnung und die Port-Voreinstellungen der Installer funktionieren. 50 Minuten.
keywords: multisite und seitenpakete, host und pfad, port-zuordnung, reihenfolge, multisite and site packages, host_uri, HostUriMatchMapItems, HostUriMatchMethodDefault, port matching, PortAccessSettings, AccessPort, AdminAccessPort, EditorAccessPort, servervar, ordering
type: help
---
# Lektion 2.3: Host und Pfad zusammen und Zuordnung nach Port

Eine Marke mit mehreren Sprachen behält meist einen Host und gibt jeder Sprache einen Pfad: `www.example.com/de` und
`www.example.com/en`. Die Prüfung `host_uri` ordnet Host und Pfad zusammen zu. Diese Lektion richtet sie für die
zweite Website der Übungsumgebung ein und sieht sich dann die Port-Prüfung an, die Installer für lokale Umgebungen und
Container verwenden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 2: Anfragen Siteaccesses zuordnen](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 2.2](zuordnung-nach-hostname.md): `MatchOrder` enthält `host`, `curl` ist zur Hand |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Einträge in `HostUriMatchMapItems[]` schreiben (`host;uri;siteaccess[;method]`) und `host_uri` zu `MatchOrder`
  hinzufügen;
- Einträge vom spezifischsten bis zu Einträgen mit leerem Pfad ordnen;
- die Port-Prüfung und `[PortAccessSettings]` erklären;
- die Voreinstellungen für Port, Host und Pfad nennen, die `exp:install` und der Kickstarter für jede Zugangsart
  schreiben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Kommentare von `settings/site.ini [SiteAccessSettings]` und `[PortAccessSettings]`,
[Siteaccess nach Host-Präfix](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md) und
[7.8 Der Editor-Siteaccess mit exp:install](../../../../installation/07-installation-ueber-die-konsole.md#78-der-editor-siteaccess-mit-expinstall).

1. **Ein Eintrag, drei Felder** (5 Minuten). `HostUriMatchMapItems[]=www.example.com;de;site_de` ordnet den Host und
   das erste Pfadelement einem Siteaccess zu. Ein viertes Feld wählt die Host-Methode (`strict`, `start`, `end`,
   `part`); ohne es gilt `HostUriMatchMethodDefault` (`strict` im Auslieferungszustand). Ein leeres Pfadfeld trifft
   den Host allein, sodass `host_uri` auch reine Host-Zuordnung kann; ein leerer Host ist nicht möglich.
2. **Reihenfolge** (5 Minuten). Der Kernel durchsucht `HostUriMatchMapItems[]` von oben nach unten. Der mitgelieferte
   Kommentar sagt: spezifischere Einträge nach oben, Einträge mit leeren Pfadteilen nach unten. Zeigen Sie, was in der
   umgekehrten Reihenfolge schiefgeht: Der Eintrag ohne Pfad fängt auch `/de` ab.
3. **Der statische Cache** (2 Minuten). Der statische Cache unterstützt für `host_uri` nur die Zuordnung `strict`,
   weil seine Rewrite-Regeln einen exakten Host und Pfad brauchen. Auf neuen Installationen ist der statische Cache
   standardmäßig ausgeschaltet.
4. **Die Port-Prüfung** (5 Minuten). `[PortAccessSettings]` enthält Zeilen `port=siteaccess` (`settings/site.ini`
   liefert `1337=user` und `1338=admin` als Beispiele mit). Die Zugangsart Port der Installer schreibt sie: Website
   `8080`, Administration `8081`, Editor `8082` als Voreinstellung (`--port`, `--admin-port`, `--editor-port` von
   `exp:install`).
5. **`servervar`** (3 Minuten). Die Prüfung liest eine Servervariable (`ServerVariableName`, `SITEACCESS` im
   Auslieferungszustand), die der Webserver setzt. Sie ist die Wahl, wenn der Webserver bereits weiß, für welche
   Website eine Anfrage bestimmt ist.

### Häufige Fragen

**Wann `host_uri` und wann `host;uri`?** `host_uri`, wenn dasselbe Pfadsegment auf verschiedenen Hosts verschiedene
Siteaccesses bedeutet (`/de` bei zwei Marken). `host;uri` genügt, wenn jeder Siteaccess einen eigenen Host oder einen
eigenen eindeutigen Pfad hat.

**Ist die Port-Zuordnung für den Produktivbetrieb geeignet?** Der Hosting-Leitfaden empfiehlt sie für die lokale
Arbeit; Produktivwebsites werden über 80 und 443 erreicht und verwenden Host- oder Pfad-Zuordnung.

**Kann ich Prüfungen mischen?** Ja, `MatchOrder` nimmt jede Liste, zum Beispiel `uri;host;host_uri`. Die erste, die
einen verfügbaren Siteaccess findet, gewinnt.

**Was geschieht mit `/` auf `www.example.com`, wenn es nur Einträge für `/de` und `/en` gibt?** Keine Prüfung passt,
deshalb antwortet der Standard-Siteaccess, oder die Weiterleitung zur Sprache aus Lektion 2.4 schickt den Browser zu
einer Sprache.

### Stolperfallen

- **Falsche Reihenfolge der Einträge.** Ein Eintrag mit leerem Pfad über den spezifischen fängt jede Adresse seines
  Hosts ab.
- **Überlappende Prüfungen.** Mit `uri` zuerst wird ein Pfadelement, das ein Siteaccess-Name ist (`/site_b_ger`), von
  der URI-Prüfung zugeordnet, bevor `host_uri` gefragt wird. Verwenden Sie für `host_uri` Segmentnamen, die keine
  Siteaccess-Namen sind (`de`, `en`).
- **Ports in Containern.** Ein von einem Container weitergeleiteter Port (`8080` außen, `80` innen) ist nicht der
  Port, den PHP sieht; ordnen Sie den inneren Port zu.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 2.2. Ersetzen Sie `http://127.0.0.1:8087` durch die Adresse
Ihrer Übungsumgebung.

1. Fügen Sie die Prüfung `host_uri` am Ende der Reihenfolge hinzu:
   `php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host;host_uri' global`.
2. Fügen Sie zwei Einträge für einen Host hinzu, den spezifischen zuerst:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'club.localhost;de;site_b_ger' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'club.localhost;;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Testen Sie:

   ```bash
   curl -s -H 'Host: club.localhost' http://127.0.0.1:8087/ | grep -i '<title>'
   curl -s -H 'Host: club.localhost' http://127.0.0.1:8087/de | grep -i '<title>'
   ```

4. Lesen Sie die Einträge in ihrer Reihenfolge: `php bin/php/console exp:ini list site.ini/SiteAccessSettings global`,
   und suchen Sie `HostUriMatchMapItems`.
5. Vertauschen Sie die Reihenfolge: Entfernen Sie beide Einträge mit `exp:ini rem` und fügen Sie sie wieder hinzu,
   den Eintrag mit leerem Pfad zuerst. Leeren Sie den INI-Cache und wiederholen Sie Schritt 3. Notieren Sie, was `/de`
   jetzt zeigt.
6. Stellen Sie die richtige Reihenfolge der Einträge wieder her und leeren Sie den INI-Cache.
7. Lesen Sie die Port-Beispiele, die der Kernel mitliefert: `php bin/php/console exp:ini list site.ini/PortAccessSettings default`.
8. Geben Sie aus, was `exp:install` für die Port-Zuordnung schreiben würde, ohne etwas zu installieren:

   ```bash
   php bin/php/console exp:install --print --access=port --port=8080 --admin-port=8081
   ```

   Suchen Sie `Access`, `AccessPort`, `AdminAccessPort` und `EditorAccessPort` in der Ausgabe.

## Erwartetes Ergebnis

- Schritt 3: `/` auf `club.localhost` zeigt *Site B*, `/de` zeigt *Seite B* (den deutschen Siteaccess aus Lektion 1.4).
- Schritt 5: Mit dem Eintrag mit leerem Pfad zuerst landet auch `/de` in `site_b` (als Seite `de`, meist "nicht
  gefunden").
- Schritt 8 gibt die Konfiguration mit `Access=port` und den Ports `8080`, `8081` und `8082` aus und installiert
  nichts.
- Der Trainer prüft das, indem er eine Person bittet, das Ergebnis von Schritt 5 mit dem mitgelieferten Kommentar zur
  Reihenfolge zu erklären.

## Quiz

1. Schreiben Sie einen Eintrag, der `www.example.com/en` zu `site_en` schickt.
2. Welcher Eintrag muss als letzter kommen?
   A. `example.com;nor;example_nor` B. `example.com;;example` C. `example.com;eng;example_eng;start` D. beliebige
   Reihenfolge
3. Welche Host-Methode ist die einzige, die der statische Cache unterstützt?
4. Wo werden Einträge von Port zu Siteaccess gespeichert?
5. Welchen Port bekommt der Editor bei `exp:install --access=port` ohne weitere Port-Option?

<details>
<summary>Antworten</summary>

1. `HostUriMatchMapItems[]=www.example.com;en;site_en`, mit `host_uri` in `MatchOrder`.
2. **B.** Einträge mit leerem Pfadteil gehören nach unten, weil die Liste von oben durchsucht wird
   (`settings/site.ini`).
3. `strict`.
4. In `site.ini [PortAccessSettings]`, je eine Zeile `port=siteaccess`.
5. `8082`, der öffentliche Port plus 2
   ([7.8](../../../../installation/07-installation-ueber-die-konsole.md#78-der-editor-siteaccess-mit-expinstall)).

</details>

## Weiterführende Informationen

- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).
- [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md#73-optionen): die Zugangsoptionen.
- [Sprachumschalter und Adressen pro Sprache](../../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md).
- [Statischer Cache und Velocity](../../../ersteller/website-ersteller/statischer-cache-und-velocity.md).

## Verwandte Seiten

- [Modul 2: Anfragen Siteaccesses zuordnen](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
