---
title: "Lektion 7.3: Ein Valkey- oder Redis-Cache"
audience: trainer, hosting, admin
category: training
order: 73
summary: Die Teilnehmenden erklären, was die Erweiterung sevenx_valkey_cache nach Valkey oder Redis verlagert, richten sie auf einem Schulungsrechner mit einem eindeutigen Schlüsselpräfix ein, prüfen die Schlüssel nach zwei Seitenaufrufen und planen die Trennung für mehrere Kunden. 40 Minuten.
keywords: hosting und betrieb, Valkey, Redis, sevenx_valkey_cache, valkeycache.ini, KeyPrefix, InstallationHash, inhalts-view-cache, INI-cache, template-blöcke, redis-cli, ezvalkeycacheclear, mandantenfähig, hosting and operations, content view cache, INI cache, template blocks, multi-tenant
type: help
---
# Lektion 7.3: Ein Valkey- oder Redis-Cache

Hoster, die bereits Redis oder Valkey betreiben, können es Exponential-Websites anbieten: für Geschwindigkeit und um
Caches für Websites sicher zu machen, die von mehreren Webknoten ausgeliefert werden. Die Erweiterung
`sevenx_valkey_cache` verlagert drei Caches dorthin. Diese Lektion behandelt, was sie speichert, wie man sie einrichtet,
wie man sie prüft und wie sich mehrere Kunden einen Server sicher teilen können.

> **Hinweis**
> Die Übung braucht einen Valkey- oder Redis-Server und die PHP-Erweiterung `redis` auf der eigenen virtuellen Maschine
> der Person. Hat der Kurs keinen solchen Rechner, führen Sie die Schritte 1 bis 3 aus und zeigen Sie den Rest vor.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 7: Leistung und Skalierung](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Systemadministratoren |
| Voraussetzungen | [Lektion 7.2](cache-schichten-proxys-und-cdn.md); Composer auf dem Schulungsrechner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Caches nennen, die die Erweiterung speichert (Template-Blöcke, den Inhalts-View-Cache, den kompilierten
  INI-Cache), und die, die sie nicht speichert;
- die Erweiterung mit einem eindeutigen `KeyPrefix` installieren und konfigurieren;
- prüfen, dass nach zwei Seitenaufrufen Schlüssel erscheinen;
- `InstallationHash` für eine Website auf mehreren Knoten und für mehrere Websites auf einem Server erklären;
- erklären, warum ein Schlüsselpräfix Schlüssel trennt, aber keine Zugriffskontrolle ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Valkey und Redis](../../../hosting/leistung/valkey-und-redis.md) und
[sevenx_valkey_cache](../../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md).

1. **Was sie speichert** (3 Minuten). Template-Blöcke `{valkey-block ...}`, den Inhalts-View-Cache und den kompilierten
   INI-Cache. Einträge haben ein Ablaufdatum, Generierungssperren gegen Stampedes sowie Knoten- und Teilbaumindizes,
   sodass Veröffentlichen genau das löscht, was es betrifft. Ohne die Erweiterung oder bei einem Fehltreffer verwendet
   der Kernel wie bisher Dateien.
2. **Voraussetzungen** (2 Minuten). Exponential 6.0.15 oder neuer; die PHP-Erweiterung `redis` in jedem PHP, das die
   Website ausliefert, und im PHP der Kommandozeile; ein erreichbarer Server.
3. **Einrichtung** (4 Minuten). `composer require se7enxweb/sevenx_valkey_cache`, `ActiveExtensions[]`, die
   Einstellungsdatei `settings/override/valkeycache.ini.append.php`, Autoloads, Caches, Neustart von Velocity.
4. **Mehrere Kunden** (4 Minuten). Ein eindeutiges `KeyPrefix` pro Installation; `InstallationHash=auto`, sofern nicht
   eine Website auf mehreren Knoten läuft (dann eine feste Zeichenkette); `Persistent=disabled`, wo Pools gemeinsam
   genutzt werden. Ein gemeinsam genutzter Server ist gemeinsamer Speicher: Jede Website kann jeden Schlüssel lesen,
   sofern die eigene Zugriffskontrolle des Servers das nicht verhindert.
5. **Was sie nicht abdeckt** (2 Minuten). Sitzungen, der Antwort-Cache von Velocity, der SQL-Abfrage-Cache, kompilierte
   Templates und Bildvarianten bleiben, wo sie sind.

### Häufige Fragen

**Was passiert, wenn der Valkey-Server ausgefallen ist?** Ein Serverausfall wird zu Cache-Fehltreffern; testen Sie das
Verhalten auf Staging.

**Wie leere ich alles?** `bin/php/ezvalkeycacheclear.php` leert alle Caches über dieselben Handler und löscht die
Schlüssel der Erweiterung; es ist der Ausweg, wenn **Den kompletten Cache leeren** nach dem Entfernen der Erweiterung
nicht aufräumen kann.

**Wie lange wird ein Eintrag des View-Caches behalten?** 3600 Sekunden, ein Wert, der in der Inhaltsansicht des Kernels
gesetzt ist, nicht in einer INI-Datei.

### Stolperfallen

- **Die Erweiterung `redis` nur im PHP des Webservers.** Cronjobs und Cache-Leerungen von der Kommandozeile verfehlen
  sie dann.
- **Zwei Teilnehmende auf einem Server mit demselben Präfix.** Vergeben Sie vor der Übung ein Präfix pro Person.
- **Den ganzen Server leeren.** Verwenden Sie das Schlüsselmuster mit dem Präfix; leeren Sie nie einen Server, den
  andere verwenden.

## Praktische Übung

Verwenden Sie Ihre eigene virtuelle Maschine mit einem Valkey- oder Redis-Server auf `127.0.0.1:6379` und der
PHP-Erweiterung `redis` sowie eine Schulungsinstallation, die Sie ändern dürfen. Ihr Präfix ist `training-<your name>:`.

1. Prüfen Sie den Server: `redis-cli ping` (oder `valkey-cli ping`).
2. Prüfen Sie die PHP-Erweiterung für den Webserver und die Kommandozeile: `php -m | grep -i redis` und **Setup**,
   **Systeminformationen** in der Administration.
3. Fügen Sie die Erweiterung hinzu: `composer require se7enxweb/sevenx_valkey_cache`.
4. Aktivieren Sie sie in `settings/override/site.ini.append.php` unter `[ExtensionSettings]` mit
   `ActiveExtensions[]=sevenx_valkey_cache`.
5. Legen Sie `settings/override/valkeycache.ini.append.php` mit dem Block `[ValkeyCacheSettings]` von der Seite für
   Hoster an und setzen Sie `KeyPrefix=training-<your name>:`.
6. Erzeugen Sie die Autoloads neu, leeren Sie die Caches und starten Sie neu:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   php bin/php/console exp:velocity restart
   ```

7. Fragen Sie eine Inhaltsseite zweimal an, ohne sich anzumelden.
8. Listen Sie Ihre Schlüssel auf: `redis-cli --scan --pattern 'training-<your name>:*' | head`.
9. Bearbeiten und veröffentlichen Sie die Seite in der Administration, fragen Sie sie erneut an und listen Sie die
   Schlüssel noch einmal auf.
10. Zum Abschluss entfernen Sie die Erweiterungszeile und die Einstellungsdatei (oder behalten Sie sie, wenn die nächste
    Lektion sie verwendet), leeren die Caches und starten neu.

## Erwartetes Ergebnis

- Schritt 1 gibt `PONG` aus; Schritt 2 listet `redis` für beide PHP auf.
- Schritt 8 listet Schlüssel auf, die mit Ihrem Präfix beginnen.
- Schritt 9: Einträge für die veröffentlichte Seite wurden gelöscht und neu geschrieben.
- Nach Schritt 10 läuft die Website wieder mit Datei-Caches.
- Der Trainer prüft die Schlüsselliste einer Person am Beamer.

## Quiz

1. Welche drei Caches speichert die Erweiterung?
2. Welches PHP braucht die Erweiterung `redis`?
   A. nur PHP-FPM B. nur das PHP von Velocity C. jedes PHP, das die Website ausliefert, und das PHP der
   Kommandozeile D. keines
3. Eine Website läuft auf drei Webknoten. Was setzen Sie für `InstallationHash`?
4. Verhindert ein eindeutiges `KeyPrefix`, dass der Code eines Kunden die Schlüssel eines anderen liest?
5. Was passiert mit Seitenaufrufen, wenn der Valkey-Server stoppt?

<details>
<summary>Antworten</summary>

1. Template-Blöcke, den Inhalts-View-Cache und den kompilierten INI-Cache.
2. **C.** ([Valkey und Redis](../../../hosting/leistung/valkey-und-redis.md#anforderungen))
3. Eine feste Zeichenkette, damit jeder Knoten dieselben Einträge liest.
4. Nein. Ein Präfix trennt Schlüssel; Zugriffskontrolle zwischen Kunden braucht die eigenen Funktionen des Servers oder
   getrennte Server.
5. Sie werden zu Cache-Fehltreffern; der Kernel fällt auf das Darstellen zurück.

</details>

## Weiterführende Informationen

- [Valkey und Redis](../../../hosting/leistung/valkey-und-redis.md)
- [sevenx_valkey_cache](../../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md)
- [Mandantentrennung](../../../hosting/sicherheit/trennung-von-mandanten.md)
- [Caches](../../../../anleitungen/caches.md): die Sicht der Administration.

## Verwandte Seiten

- [Modul 7: Leistung und Skalierung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
