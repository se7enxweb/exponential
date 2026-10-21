---
title: Wechsel zu einem anderen Anbieter, dieselbe Software
audience: government, project-manager, admin, hosting, partner
category: public-sector
order: 70
summary: Wie eine öffentliche Stelle ihre Exponential-Website zu einem neuen Hosting-Anbieter oder Support-Partner umzieht, ohne die Software zu wechseln - Rollen des bisherigen und des neuen Anbieters, das dokumentierte Umzugsverfahren, Einstellungen, die den alten Rechner nennen, die Umstellung und der Abschluss des alten Vertrags.
keywords: Übergang, Anbieterwechsel, Lieferantenwechsel, Hosting-Umzug, Migration zwischen Hostern, Umstellung, DNS, Übergabe, Offboarding, Onboarding, transition, change of provider, change of supplier, hosting move, migration between hosts, cut-over, handover, offboarding, onboarding
type: help
---
# Wechsel zu einem anderen Anbieter, dieselbe Software

Diese Seite richtet sich an die Projektleitung, die den Hosting-Anbieter oder den Support-Partner einer
Exponential-Website wechselt und die Software behält. Weil die Software frei ist und die Website aus gewöhnlichen Dateien
und einer Datenbank besteht, kann ein neuer Anbieter dieselbe Installation in derselben Version übernehmen. Das
technische Verfahren ist in [Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)
dokumentiert; diese Seite bettet es in die Projektschritte ein, die eine öffentliche Stelle braucht.

## Inhalt
1. [Wer was tut](#wer-was-tut)
2. [Vor dem Umzug](#vor-dem-umzug)
3. [Der Umzug](#der-umzug)
4. [Einstellungen, die den alten Rechner nennen](#einstellungen-die-den-alten-rechner-nennen)
5. [Nach dem Umzug](#nach-dem-umzug)
6. [Nur den Support-Partner wechseln](#nur-den-support-partner-wechseln)

## Wer was tut

| Partei | Aufgaben |
|---|---|
| Sie | verantworten den Ausstieg; genehmigen Termine; halten Ihre eigene Sicherung; informieren die Redaktion; nehmen ab |
| Bisheriger Anbieter | liefert Export, Einstellungen und Code; hält die Website im Wartungsmodus, bis das DNS umgezogen ist; löscht danach die Daten ([Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)) |
| Neuer Anbieter | bereitet das Ziel vor; stellt wieder her; testet; übernimmt Überwachung und Sicherungen ([Livegang einer neuen Kundenwebsite](../../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)) |

## Vor dem Umzug

- [ ] Das Ziel erfüllt die Anforderungen, mit derselben PHP-Hauptversion oder einer neueren unterstützten
      ([Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
- [ ] Dieselbe Datenbank-Engine oder ein geplanter Wechsel ([Die Datenbank-Engine wechseln](../../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)).
- [ ] DNS-Time-to-live der Domains der Website vorab gesenkt.
- [ ] Eine Testwiederherstellung der letzten Sicherung auf dem Ziel hat einmal funktioniert.
- [ ] Redaktion über das Zeitfenster des Bearbeitungsstopps informiert.
- [ ] Ihre eigene vollständige Kopie erstellt ([Alles sichern](alles-sichern.md)).

## Der Umzug

Die dokumentierte Reihenfolge, kurz:

1. Wartungsmodus an, auf dem alten Rechner:
   `php bin/php/maintenance.php on --message="Moving to a new server" --until=1h --allow-root-user`.
2. Die Datenbank mit dem Werkzeug der Engine sichern und die Dateien (`var/storage`, `settings/override`, `settings/siteaccess`).
3. Auf dem Ziel: derselbe Tag des Kernels und `composer install`, dazu die eigenen Erweiterungen aus Git.
4. Datenbank und Dateien wiederherstellen; Eigentümer und Berechtigungen setzen.
5. Die unten aufgeführten Einstellungen anpassen.
6. Autoloads neu erzeugen und Caches leeren:
   `php bin/php/ezpgenerateautoloads.php -e` und `php bin/php/ezcache.php --clear-all --allow-root-user`.
7. Die Engine starten oder PHP-FPM neu laden.
8. Über einen Eintrag in der Hosts-Datei testen, dann das DNS umstellen.
9. Den alten Rechner im Wartungsmodus lassen, bis die DNS-Änderung überall angekommen ist.

## Einstellungen, die den alten Rechner nennen

| Einstellung | Wo |
|---|---|
| Datenbankserver, Benutzer, Passwort | `settings/override/site.ini.append.php`, `[DatabaseSettings]` |
| Adressen der Website und Zuordnung der Siteaccesses | `settings/override/` und `settings/siteaccess/*/` |
| Mail-Transport und Mailserver | `[MailSettings]` |
| Cluster-Speicher | `settings/override/file.ini.append.php`, falls geclustert |
| Ports und Zertifikate von Velocity | `settings/override/velocity.ini.append.php`, sofern vorhanden |

Das Datenbankpasswort ändert sich beim neuen Host; das alte sollte außer Dienst gestellt werden.

## Nach dem Umzug

- [ ] Die Prüfungen der Umzugsseite bestehen; eine Stichprobe von Seiten, Bildern und Formularen funktioniert.
- [ ] Anmeldung, zweistufige Anmeldung und Rollen funktionieren für eine Testredaktion.
- [ ] Cronjobs laufen auf dem neuen Host ([Cronjobs](../../../hosting/betrieb/cronjobs.md)).
- [ ] Überwachung und Sicherungen laufen beim neuen Host; erste Sicherung geprüft.
- [ ] Das Audit-Protokoll läuft weiter; `./console exp:audit verify` meldet eine intakte Kette.
- [ ] Konten der Beschäftigten des bisherigen Anbieters aus der Administration entfernt.
- [ ] Der bisherige Anbieter bestätigt die Löschung schriftlich zum vereinbarten Termin.

## Nur den Support-Partner wechseln

Bleibt der Host und wechselt nur der Support-Partner:

- geben Sie dem neuen Partner Zugang mit persönlichen Konten und Rollen, nie mit gemeinsam genutzten Konten
  ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md));
- entfernen Sie die Konten und Schlüssel des bisherigen Partners und erneuern Sie gemeinsam genutzte Zugangsdaten;
- übergeben Sie den Code in Git und die Dokumentation ([Übergabe der Dokumentation](uebergabe-der-dokumentation.md));
- bitten Sie den neuen Partner, **Setup > Upgrade-Prüfung** auszuführen, um zu sehen, ob mitgelieferte Dateien
  geändert wurden ([Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)).

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Checkliste zum Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
