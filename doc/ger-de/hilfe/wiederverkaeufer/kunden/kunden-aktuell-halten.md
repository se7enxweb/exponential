---
title: Kunden aktuell halten
audience: reseller, partner, hosting
category: reselling
order: 390
summary: Wie ein Wiederverkäufer jede Kunden-Website auf dem gepflegten Exponential-Release und einer unterstützten PHP-Version hält - was das Projekt unterstützt, ein Inventar der Websites, die vier Teile eines Updates, die Werkzeuge (ezupdate, die Upgrade-Prüfung, der Wartungsmodus), ein Update-Rhythmus, die Preisgestaltung von Upgrades und das Gespräch mit Kunden darüber.
keywords: Upgrades, Updates, aktuell halten, Release-Linie, 6.0.x, SECURITY.md, PHP-Versionen, Inventar, ezupdate, git_manager, Upgrade-Prüfung, Wartungsmodus, Rhythmus, Preisgestaltung, alte Websites, upgrades, updates, keeping current, release line, PHP versions, inventory, upgrade check, maintenance mode, rhythm, pricing, old sites
type: help
---
# Kunden aktuell halten

Diese Seite ist für technische Leiter bei Wiederverkäufern, die viele Kunden-Websites pflegen. Ein Wiederverkäufer,
der Websites aktuell hält, verkauft Sicherheit; einer, der das nicht tut, verkauft Risiko. Die Fassung für Partner ist
[Kunden-Websites aktuell halten](../../partner/kunden-websites-aktuell-halten.md).

## Inhalt

1. [Was das Projekt unterstützt](#was-das-projekt-unterstützt)
2. [Führen Sie ein Inventar](#führen-sie-ein-inventar)
3. [Die vier Teile eines Updates](#die-vier-teile-eines-updates)
4. [Werkzeuge](#werkzeuge)
5. [Ein Update-Rhythmus](#ein-update-rhythmus)
6. [Preisgestaltung von Upgrades](#preisgestaltung-von-upgrades)
7. [Das Gespräch mit Kunden](#das-gespräch-mit-kunden)

## Was das Projekt unterstützt

| Version | Unterstützt (laut `SECURITY.md`) |
|---|---|
| 6.0.x, die aktuelle Linie (`main` und der nächste daraus erstellte Tag) | Ja |
| 6.0.x, veröffentlichte Tags (`v6.0.0` bis `v6.0.14`) | Behoben durch ein Upgrade auf das neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | Nein |

„A published tag is never changed“ (ein veröffentlichter Tag wird nie geändert): Eine Fehlerbehebung erreicht eine
Website nur über ein Update. PHP 7.4 bis 8.5 werden für die Linie 6.0.15 unterstützt; PHP 7.4 wieder ab 6.0.15
([Release-Linie und Unterstützung](../../kunden/gesund-halten/release-linie-und-support.md)). Das Projekt veröffentlicht
keinen Release-Kalender und kein Ende der Unterstützung für 6.0.

## Führen Sie ein Inventar

```text
Customer site inventory
Customer | Site | Exponential version | PHP | Database | Engine | Extensions (versions) | Last update | Next planned
---------|------|---------------------|-----|----------|--------|-----------------------|-------------|-------------
```

Die Version wird unter **Setup > Systeminformationen** und unter **Info > Über** angezeigt; letztere Seite listet
auch jede Erweiterung mit ihrer Version auf ([Systeminformationen](../../../anleitungen/systeminformationen.md)). Prüfen
Sie das Inventar monatlich: Jede Website unter dem neuesten Release 6.0.x oder auf einer PHP-Version kurz vor ihrem
Ende bekommt einen Plan.

## Die vier Teile eines Updates

| Teil | Was sich ändert |
|---|---|
| Der Code des Kernels | Die Dateien von Exponential, aus einem Release-Tag oder einem Branch |
| Die Erweiterungen | Mit Composer passend zum neuen Kernel aktualisiert |
| Die Datenbank | Ein Release kann eine SQL-Datei mitbringen, die einmal angewendet wird, nach einer Datensicherung |
| Caches und der laufende Server | Caches geleert, PHP-FPM neu geladen oder Exponential Velocity neu gestartet |

Quelle: [Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md); das vollständige Verfahren steht
unter [Kunden-Websites aktualisieren](../../hosting/betrieb/kunden-websites-aktualisieren.md).

## Werkzeuge

| Werkzeug | Verwendung |
|---|---|
| **Setup > Upgrade-Prüfung** | Vergleicht die Dateien mit dem Release und die Datenbank mit ihrem Schema; nur lesend ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)) |
| Erweiterung `ezupdate` | **Setup > Updates and packages**, mit einer Prüfung, die nichts ändert, einem Probelauf und einer Bestätigung der Datensicherung ([ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md)) |
| Erweiterung `git_manager` | Git-Operationen aus der Administration ([git_manager](../../../funktionen/6.0/erweiterungen/git_manager.md)) |
| Wartungsmodus | **Setup > Wartung** oder `php bin/php/maintenance.php` ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)) |
| Automatisierte Prüfungen | Viele Websites prüfen, „without updating anything unattended“ (ohne unbeaufsichtigt etwas zu aktualisieren) ([Automatisierte Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md)) |

## Ein Update-Rhythmus

| Das Release enthält | Einspielen innerhalb von | Schritte |
|---|---|---|
| Eine Sicherheitskorrektur | Der von Ihnen zugesagten Anzahl von Tagen ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)) | Test auf Staging, Datensicherung, Update, Prüfung, Bericht |
| Fehlerbehebungen und Funktionen | Dem nächsten monatlichen Zeitfenster | Dieselben |
| Einen Wechsel der PHP-Version | Ein Jahr im Voraus geplant | Auf Staging mit dem neuen PHP testen, dann umstellen |

Halten Sie nach jedem Update drei Ergebnisse fest: die neue Version unter **Setup > Systeminformationen**, eine
Upgrade-Prüfung ohne Befund und ein veröffentlichtes Testelement ([Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md#woran-sie-erkennen-dass-ein-update-funktioniert-hat)).

## Preisgestaltung von Upgrades

- Routinemäßige Updates von 6.0.x gehören in den Wartungsvertrag.
- Wechsel der PHP-Version: im Vertrag für Websites, die Sie gebaut haben, gesondert bepreist für Websites mit Code,
  den Sie nicht geschrieben haben.
- Upgrades von 5.x, 4.x oder älter sind Projekte; die Migrationskapitel des Installationshandbuchs beschreiben die
  Wege ([Upgrades](../../../anleitungen/aktualisieren.md)).
- Eine Erweiterung, die nicht mehr gepflegt wird, kann ein Upgrade aufhalten; bepreisen Sie ihren Ersatz gesondert.

## Das Gespräch mit Kunden

- Erklären Sie Updates als Schutz, nicht als Verkauf.
- Fassen Sie jedes Release in den Worten des Kunden zusammen: was sich für Redakteure und Besucher ändert und was er
  gegebenenfalls tun muss ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).
- Planen Sie große Schritte ein Jahr im Voraus ([Jährliche Checkliste](../checklisten/jaehrliche-checkliste.md)).
- Websites aus der Zeit vor der Umbenennung des Produkts sind naheliegende Kunden für ein Upgrade-Projekt.

## Verwandte Seiten

- [Kunden-Websites aktuell halten](../../partner/kunden-websites-aktuell-halten.md)
- [Kunden-Websites aktualisieren](../../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Upgrades mit Ihrem Anbieter](../../kunden/gesund-halten/upgrades-mit-ihrem-anbieter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
