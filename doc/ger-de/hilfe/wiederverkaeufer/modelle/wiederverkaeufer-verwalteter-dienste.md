---
title: Wiederverkäufer verwalteter Dienste
audience: reseller, partner, hosting
category: reselling
order: 80
summary: Wie Sie Exponential-Websites als verwalteten Dienst verkaufen - Sie betreiben, aktualisieren, sichern und überwachen die Website gegen eine Monatsgebühr -, mit den Aufgaben, die der Dienst abdeckt, den Werkzeugen, die Exponential für jede bereitstellt, einer Zuständigkeitsmatrix für den Vertrag und den Kosten, die Sie einpreisen müssen.
keywords: verwalteter Dienst, verwaltete Website, Wartungsvertrag, Wartungsplan, Monatsgebühr, Updates, Datensicherungen, Überwachung, ezupdate, Upgrade-Prüfung, Wartungsmodus, Zuständigkeitsmatrix, managed service, managed site, care plan, maintenance plan, monthly fee, updates, backups, monitoring, upgrade check, maintenance mode, responsibility matrix
type: help
---
# Wiederverkäufer verwalteter Dienste

Diese Seite ist für Agenturen und Hosting-Unternehmen, die eine **verwaltete Website** verkaufen: Der Kunde
bearbeitet die Inhalte, und Sie halten die Plattform gegen eine Monatsgebühr in Betrieb, aktuell und sicher. Es ist
das häufigste Wiederverkaufsmodell rund um Exponential, weil der Bedarf des Kunden dauerhaft ist und damit auch Ihr
Umsatz.

## Inhalt

1. [Was der Dienst abdeckt](#was-der-dienst-abdeckt)
2. [Die Werkzeuge, die Sie nutzen](#die-werkzeuge-die-sie-nutzen)
3. [Eine Zuständigkeitsmatrix für den Vertrag](#eine-zuständigkeitsmatrix-für-den-vertrag)
4. [Kosten, die Sie einpreisen](#kosten-die-sie-einpreisen)
5. [Den Dienst verkaufen](#den-dienst-verkaufen)
6. [Checkliste](#checkliste)

## Was der Dienst abdeckt

| Bereich | Typischer verwalteter Dienst | Meist dem Kunden überlassen |
|---|---|---|
| Server, PHP, Engine, Zertifikate | Ja | |
| Updates von Exponential und Erweiterungen | Ja, nach Plan, mit einem Testschritt | Das Zeitfenster genehmigen |
| Datensicherungen und Wiederherstellungstests | Ja | |
| Überwachung und Prüfung der Logs | Ja | |
| Sicherheitshärtung und Reaktion auf Vorfälle | Ja, für die Plattform | Inhalte und Benutzerkonten |
| Inhalte, Benutzer, Rollen | | Ja |
| Eigener Code (Erweiterungen, Designs) | Ihn ausrollen | Ihn schreiben, sofern nicht gesondert verkauft |

Quelle: [Managed Hosting](../../hosting/modelle/managed-hosting.md#was-verwaltet-abdecken-kann).

## Die Werkzeuge, die Sie nutzen

| Aufgabe | Werkzeug in Exponential | Seite |
|---|---|---|
| Updates | Die Erweiterung `ezupdate`: **Setup > Updates and packages**, mit einer Prüfung, die nichts ändert, einem Probelauf und einer Bestätigung der Datensicherung | [ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md) |
| Vor einem Upgrade | **Setup > Upgrade-Prüfung** (nur lesende Konsistenzprüfung von Dateien und Datenbank) | [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Geplante Arbeiten | **Setup > Wartung** oder `php bin/php/maintenance.php` | [Wartungsmodus](../../../anleitungen/wartungsmodus.md) |
| Zustand | **Setup > Systeminformationen** und `exp:system:info` | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| Logs | **Setup > Logdateien** | [Log-Viewer](../../../funktionen/6.0/log-betrachter.md) |
| Geplante Aufgaben | **Setup > Cronjobs** | [Cronjobs](../../../anleitungen/cronjobs.md) |
| Wer was getan hat | Das Audit-Protokoll | [Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md) |

Die vollständige Tabelle mit den Formen für die Kommandozeile steht unter [Managed Hosting](../../hosting/modelle/managed-hosting.md#die-werkzeuge-für-jede-aufgabe).

## Eine Zuständigkeitsmatrix für den Vertrag

Kopieren Sie sie in die Leistungsbeschreibung und tragen Sie die Namen ein.

```text
Task                                  | Your company | Customer | 7x / project
--------------------------------------|--------------|----------|---------------------------
Server, PHP, engine, TLS              |      R       |          |
Exponential and extension updates     |      R       |    A     | publishes releases
Backups, restore tests                |      R       |    I     |
Monitoring, log review                |      R       |    I     |
Security incident on the platform     |      R       |    I     | fixes in the next release
Content, users, roles                 |              |    R     |
Custom extensions and designs         |   R or -     |  R or -  |
Bug in Exponential itself             | reports it   |    I     | fixes in a release
R = responsible, A = approves, I = informed
```

Fehler in Exponential werden im nächsten Release 6.0.x behoben; „a published tag is never changed“ (ein
veröffentlichter Tag wird nie geändert, `SECURITY.md`). Ihr Dienst umfasst das Einspielen dieses Release, keine
private Fehlerbehebung von 7x.

## Kosten, die Sie einpreisen

- Hosting (eigenes oder eingekauftes), Datensicherungen und deren Speicher.
- Arbeitszeit je Website und Monat: Updates, Prüfungen, Berichte. Messen Sie sie an Ihren ersten Websites.
- Einen Bereitschaftsplan, wenn Sie irgendetwas außerhalb der Geschäftszeiten versprechen.
- Ihr Partner-Abonnement, wenn Ihre Rückendeckung der Partner-Support ist ([Eine Partnerstufe wählen](../grundlagen/eine-partnerstufe-waehlen.md)).
- Eine Reserve für Upgrades, die Codeänderungen brauchen, oder bepreisen Sie diese gesondert.

Rechnen Sie die Zahlen mit dem [Arbeitsblatt zu Marge und Kosten](../angebote/arbeitsblatt-zu-marge-und-kosten.md) durch.

## Den Dienst verkaufen

- Stellen Sie Ergebnisse voran: „Ihre Website bleibt aktuell und gesichert; Sie erhalten jeden Monat einen kurzen
  Bericht“.
- Zeigen Sie, was der Kunde sehen wird: den Monatsbericht, die Update-Hinweise, das Ergebnis des
  Wiederherstellungstests.
- Erklären Sie den Ausstieg: Der Kunde kann jederzeit gehen, mit einer vollständigen Übergabe
  ([An einen Kunden oder einen neuen Anbieter übergeben](../kunden/uebergabe.md)).

## Checkliste

- [ ] Leistungsbeschreibung mit der Zuständigkeitsmatrix.
- [ ] Update-Rhythmus und Zeitfenster vereinbart ([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).
- [ ] Vorlage für den Monatsbericht ([Monatliche Checkliste](../checklisten/monatliche-checkliste.md)).
- [ ] Service-Levels, die Sie mit Ihrem eigenen Personal einhalten können ([Service-Levels gestalten](../angebote/service-levels-gestalten.md)).
- [ ] Vertrag mit Bedingungen zur Auftragsverarbeitung ([Datenschutz für Wiederverkäufer](../verantwortlichkeiten/datenschutz.md)).

## Verwandte Seiten

- [Managed Hosting](../../hosting/modelle/managed-hosting.md)
- [Vorlage für einen Wartungsvertrag](../../kunden/partner-und-hoster/vorlage-wartungsvereinbarung.md)
- [Wiederverkäufer von Supportverträgen](wiederverkaeufer-von-supportvertraegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
