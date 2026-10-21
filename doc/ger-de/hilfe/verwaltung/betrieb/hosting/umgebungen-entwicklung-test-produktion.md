---
title: Umgebungen für Entwicklung, Test und Produktion
audience: government, hosting, admin, developer
category: public-sector
order: 80
summary: Für IT-Teams im öffentlichen Sektor, die getrennte Entwicklungs-, Test- (Abnahme-) und Produktionskopien einer Exponential-Website führen. Wie EXP_ENV jeder Maschine ihre eigenen Einstellungen gibt, wie Testkopien keine E-Mails versenden und keine Jobs ausführen und wie die Administration anzeigt, in welcher Umgebung Sie sich befinden.
keywords: Umgebungen, Entwicklung, Test, Abnahme, Staging, Produktion, EXP_ENV, config.env.php, Varianten von Einstellungen, Dateitransport, Testkopie, Umgebungsabzeichen, OTAP, DTAP, environments, development, acceptance, production, settings variants, file transport, test copy, environment badge
type: help
---
# Umgebungen für Entwicklung, Test und Produktion

Diese Seite richtet sich an IT-Teams in öffentlichen Stellen, die dieselbe Website auf mehreren Maschinen betreiben:
Entwicklung, Test oder Abnahme und Produktion (in der IT des öffentlichen Sektors oft DTAP oder OTAP genannt). Sie
beantwortet, wie Exponential jeder Maschine eigene Einstellungen erlaubt, ohne nach jeder Kopie Dateien zu bearbeiten,
wie Sie verhindern, dass sich eine Testkopie wie die Live-Website verhält, und wie Redakteurinnen und Redakteure sehen,
in welcher Umgebung sie arbeiten. Die Funktionsseite ist
[Einstellungen pro Umgebung](../../../../../eng-us/features/6.0/environment-settings.md).

## Inhalt

1. [Warum getrennte Umgebungen](#warum-getrennte-umgebungen)
2. [Die Umgebung benennen](#die-umgebung-benennen)
3. [Einstellungen, die sich je Umgebung unterscheiden](#einstellungen-die-sich-je-umgebung-unterscheiden)
4. [Testkopien still halten](#testkopien-still-halten)
5. [Die Umgebung in der Administration anzeigen](#die-umgebung-in-der-administration-anzeigen)
6. [Daten in Testumgebungen](#daten-in-testumgebungen)
7. [Checkliste](#checkliste)

## Warum getrennte Umgebungen

Änderungsbeiräte im öffentlichen Sektor verlangen meist, dass eine Änderung auf einer Testkopie funktionierend gesehen
wird, bevor sie die Produktion erreicht. Exponential unterstützt das mit denselben Code- und Einstellungsdateien auf
jeder Maschine, ergänzt um eine kleine Variante pro Maschine für das, was sich unterscheiden muss (Datenbank, E-Mail,
Debug-Ausgabe). Die Schritte der Freigabe stehen unter [Staging und Freigabe](../aktualisierungen-und-aenderungen/staging-und-freigabe.md).

## Die Umgebung benennen

Kopieren Sie `config.env.php.example` im Wurzelverzeichnis der Installation nach `config.env.php` und setzen Sie den Namen:

```php
<?php
define( 'EXP_ENV', 'test' );
```

`config.env.php` wird von den Web-Einstiegspunkten und den Kommandozeilenskripten vor `config.php` gelesen, und
`.gitignore` hält die Datei aus der Versionsverwaltung heraus. Ein Name besteht aus Kleinbuchstaben, Ziffern, `-` oder
`_`, beginnt mit einem Buchstaben und hat höchstens 32 Zeichen. Mit `dev` oder `test` zeigt `index.php` PHP-Fehler im
Browser an; mit `prod`, einem anderen Namen oder keinem bleibt `display_errors` so, wie `php.ini` es setzt
([Einstellungen pro Umgebung](../../../../../eng-us/features/6.0/environment-settings.md)).

## Einstellungen, die sich je Umgebung unterscheiden

Jede Einstellungsdatei kann eine Variante mit dem Namen der Umgebung direkt nach `.ini` haben:

| Standarddatei | Variante für `test` |
|---|---|
| `settings/override/site.ini.append.php` | `settings/override/site.ini.test.append.php` |
| `settings/siteaccess/admin/site.ini.append.php` | `settings/siteaccess/admin/site.ini.test.append.php` |

Eine Variante wird in jeder Ebene direkt nach ihrer Standarddatei gelesen, daher hat `settings/override` weiterhin
Vorrang vor allem anderen. Jede Umgebung hat ihre eigenen INI-Cache-Dateien; leeren Sie nach dem Anlegen von Varianten
einmal den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Typische Unterschiede:

| Einstellung | Entwicklung | Test | Produktion |
|---|---|---|---|
| Verbindung in `[DatabaseSettings]` | lokal | Testdatenbank | Produktionsdatenbank |
| `[MailSettings] Transport` | `file` | `file` | Ihr E-Mail-Transport |
| `[DebugSettings] DebugOutput` | für aufgeführte Adressen eingeschaltet | nach Bedarf | ausgeschaltet |
| Velocity-Ports und Hostnamen | lokal | Testnamen | Produktionsnamen |

Halten Sie die Varianten in der Versionsverwaltung, außer wo sie Geheimnisse enthalten (siehe
[Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md)).

## Testkopien still halten

Eine Testkopie, die aus einer Datensicherung der Produktion erstellt wurde, enthält echte Konten und echte
E-Mail-Adressen. Verhindern Sie, dass sie sich wie die Live-Website verhält
([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)):

- **E-Mail:** `Transport=file` schreibt jede E-Mail nach `var/log/mail/`, statt sie zu versenden. Oder behalten Sie den
  Transport bei und setzen Sie `DebugSending=enabled` mit `DebugReceiverEmail`, um alles an eine einzige Testadresse
  zu senden.
- **Cron:** Installieren Sie die Crontab auf einer Testmaschine nicht, außer Sie testen Cronjobs; sonst laufen
  Benachrichtigungen, Newsletter und Inhaltsjobs gegen echte Adressen.
- **Suchmaschinen:** Halten Sie Test-Hostnamen vom öffentlichen Internet fern oder hinter Ihrer Netzwerkbeschränkung.

## Die Umgebung in der Administration anzeigen

Die Designs admin4 und admin4l können die Umgebung als Abzeichen im Kopfbereich, auf der Anmeldeseite und vor dem
Seitentitel anzeigen. `site.ini [AdminEnvironment] Level=auto` übernimmt die Stufe aus `EXP_ENV`
([Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md)). Templates lesen den Namen mit
`{exp_environment()}`, sodass eine öffentliche Test-Website ein Banner „Test site“ anzeigen kann:

```
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

Redakteurinnen und Redakteure, die ein deutliches Abzeichen sehen, veröffentlichen seltener auf der falschen Kopie.

## Daten in Testumgebungen

Wer Produktionsdaten in den Test kopiert, nimmt personenbezogene Daten mit. Ob das erlaubt ist und mit welchen
Schutzmaßnahmen, entscheiden Ihre Datenschutzregeln. Möglichkeiten, die die Software unterstützt: der oben genannte
Dateitransport für E-Mails; die Beschränkung des Test-Hosts auf Ihr Netzwerk; und das Entfernen oder Ersetzen von
Daten auf der Kopie, bevor Testende Zugriff erhalten. Wenn Testdaten überhaupt keine personenbezogenen Daten enthalten
dürfen, erstellen Sie Testinhalte separat, statt die Produktion zu kopieren.

## Checkliste

- [ ] Jede Maschine hat eine `config.env.php` mit ihrem Umgebungsnamen; die Produktion verwendet `prod` oder keinen.
- [ ] Datenbank-, E-Mail- und Debug-Einstellungen unterscheiden sich über Variantendateien, nicht über Handbearbeitung.
- [ ] Testkopien schreiben E-Mails in Dateien und führen keine unbeabsichtigten Cronjobs aus.
- [ ] Die Administration zeigt auf Kopien außerhalb der Produktion das Umgebungsabzeichen an.
- [ ] Die Nutzung von Produktionsdaten auf Testkopien ist nach Ihren Datenschutzregeln freigegeben.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Auf einem Testserver wiederherstellen](../datensicherung-und-wiederherstellung/wiederherstellung-auf-einem-testserver.md)
- [Staging und Freigabe](../aktualisierungen-und-aenderungen/staging-und-freigabe.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
