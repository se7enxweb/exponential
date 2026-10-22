---
title: Hosting-Anforderungen für Ihre Angebote
audience: reseller, hosting
category: reselling
order: 210
summary: Die Serveranforderungen, die ein Wiederverkäufer prüfen muss, bevor er ein Exponential-Angebot verkauft - PHP 8.0 bis 8.5 und seine Erweiterungen, Werte in der php.ini, die sechs Datenbanken in der offiziellen Reihenfolge, Composer, die Engine, Bildkonvertierung, beschreibbare Verzeichnisse - und die Fragen an einen Hosting-Lieferanten.
keywords: Anforderungen, Hosting-Anforderungen, PHP 8, Erweiterungen, php.ini, memory_limit, Datenbank, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Composer, Velocity, ImageMagick, GD, Fragen an Lieferanten, requirements, hosting requirements, extensions, database, supplier questions
type: help
---
# Hosting-Anforderungen für Ihre Angebote

Diese Seite ist für Wiederverkäufer, die Hosting verkaufen, das sie selbst betreiben oder bei einem Lieferanten
einkaufen. Bevor ein Angebot auf Ihre Preisliste kommt, prüfen Sie, dass jeder Server, auf dem es läuft, diese
Anforderungen erfüllt. Die vollständige Referenz ist [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md).

## Inhalt

1. [Das Minimum](#das-minimum)
2. [Datenbanken](#datenbanken)
3. [Was nicht erforderlich ist](#was-nicht-erforderlich-ist)
4. [Fragen an einen Hosting-Lieferanten](#fragen-an-einen-hosting-lieferanten)
5. [Einen Server prüfen](#einen-server-prüfen)

## Das Minimum

| Bedarf | Minimum | Empfohlen | Geprüft durch |
|---|---|---|---|
| PHP | 8.0 | Das neueste 8.x, das das System anbietet; 8.5 ist das neueste getestete | `composer.json` und den Setup-Assistenten (`settings/setup.ini`, `[phpversion] MinimumVersion=8.0.0`) |
| PHP-Erweiterungen | `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick | Dazu `curl`, `gd`, `openssl`, `pcntl`, `opcache` | Composer und die kritischen Tests des Setup-Assistenten |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` gesetzt, `file_uploads` an, `allow_url_fopen` an | Mehr Speicher für Importe und Bildbearbeitung | Die kritischen Tests des Setup-Assistenten |
| Composer | 2.x | Ein aktuelles 2.x | Sie |
| Webserver | Keiner (Exponential Velocity oder der eingebaute Server von PHP) | Velocity oder Apache bzw. nginx mit PHP-FPM | Sie |
| Bildkonvertierung | GD oder `convert` von ImageMagick | Beides | Kritischer Test `image_conversion` |
| 64-Bit-PHP | Empfohlen | 64 Bit; `composer.json` schlägt es vor „for support of more than 30 languages“ (für die Unterstützung von mehr als 30 Sprachen) | Sie |
| Kommandozeile | Eine Shell mit demselben PHP wie im Web | Ja, für Composer, Cron und die Konsole | Sie |

Quelle: [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md). Die Unterstützung von PHP
8.0 ist neu in der Linie 6.0.15; die Tags `v6.0.8` bis `v6.0.14` verlangen PHP 8.1.

## Datenbanken

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (die
> Voreinstellung) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit
> bekanntermaßen mit zusätzlichem Aufwand möglich (kein Treiber in 6.0.x).

Für ein Angebot bedeutet das:

- **SQLite 3** braucht keinen Datenbankserver und ist eine vollwertige Produktivdatenbank für eine Website, die von
  einer Maschine ausgeliefert wird: der günstigste Tarif im Betrieb ([Datenbankunterstützung](../../datenbankunterstuetzung.md)).
- **PostgreSQL** zuerst, wenn der Tarif mehrere Web-Knoten oder einen separaten Datenbankserver hat.
- Bieten Sie die Datenbanken an, die Ihr Team sichern, wiederherstellen und optimieren kann; siehe
  [Datenbanken in der offiziellen Reihenfolge](../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md).

## Was nicht erforderlich ist

- Ein bestimmtes Betriebssystem: Exponential läuft überall, wo PHP 8 läuft ([Betriebssysteme für Hoster](../../hosting/grundlagen/betriebssysteme-fuer-hoster.md)).
- Ein Datenbankserver, wenn SQLite 3 verwendet wird.
- Ein separater Webserver, wenn Velocity die Website ausliefert.
- Solr, Valkey oder Redis: optional, für Suche und Caching.

Das sind gute Verkaufsargumente für günstige Tarife.

## Fragen an einen Hosting-Lieferanten

Wenn Sie die Server eines anderen weiterverkaufen, senden Sie diese Fragen, bevor Sie unterschreiben:

- [ ] Welche PHP-Versionen bieten Sie an, und wann nehmen Sie jede aus dem Angebot?
- [ ] Haben das Web-PHP und das Kommandozeilen-PHP dieselbe Version mit denselben Erweiterungen?
- [ ] Können wir Cronjobs und eine Shell als Benutzer der Website ausführen?
- [ ] Darf ein lang laufender Prozess (Exponential Velocity) laufen, oder nur PHP-FPM?
- [ ] Welche Datenbanken, und wer sichert sie?
- [ ] Ein Systembenutzer und ein PHP-FPM-Pool je Website oder gemeinsam genutzt? ([Benutzer und Pools je Kunde](../../hosting/sicherheit/benutzer-und-pools-pro-kunde.md))
- [ ] Wo werden die Daten gespeichert, und wer sind Ihre Unterauftragsverarbeiter? ([Datenschutz für Wiederverkäufer](../verantwortlichkeiten/datenschutz.md))
- [ ] Wie erhalten wir eine vollständige Kopie einer Website, wenn wir gehen?

## Einen Server prüfen

```bash
php -v                          # 8.0 or later, for Velocity too
php -m                          # the extensions listed above
php -i | grep "Integer Size"    # 64 bits recommended
composer --version              # Composer 2, and the PHP it runs with
```

Die Seite **Systemtest** des Setup-Assistenten, der Kickstarter und `exp:install` führen die kritischen Tests aus
`settings/site.ini [SetupSettings] CriticalTests` aus und halten mit Ursache und Lösung an, wenn einer fehlschlägt
([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md#was-der-installer-für-sie-prüft)).

## Verwandte Seiten

- [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)
- [Checkliste für einen neuen Server](../../hosting/checklisten/checkliste-fuer-einen-neuen-server.md)
- [Hosting-Wiederverkäufer](../modelle/hosting-wiederverkaeufer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
