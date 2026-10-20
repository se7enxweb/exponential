---
title: Kurzreferenz für Website-Betreiber
audience: customer, admin
category: customer-success
order: 7
summary: Wo sich die Dinge, die sich ein Website-Betreiber ansieht, in der Administration von Exponential befinden, mit Menüpfad und Adresse: Dashboard, Inhalte, Shop, Benutzer, Workflows, Logs, Prüfprotokoll, Wartung, Upgrade-Prüfung und die Hilfe.
keywords: Kurzreferenz, Administration, Menü, Adresse, URL, Dashboard, Shop-Dashboard, Benutzerkonten prüfen, Workflow-Prozesse, Logdateien, Prüfprotokoll, Wartung, Upgrade-Prüfung, Inhaltsfinder, Hilfe, Meldung, quick reference, administration, menu, address, store dashboard, check user accounts, workflow processes, log files, audit, maintenance, upgrade check, content finder, help, report
type: help
---
# Kurzreferenz für Website-Betreiber

Eine einseitige Liste der Orte, die ein Website-Betreiber in der Administration aufsucht, mit dem Menüpfad und der
Adresse nach der Adresse der Administration (zum Beispiel `https://www.example.com/admin`). Was Sie sehen, hängt von
Ihrer Rolle ab: Ein Redakteur sieht weniger Reiter als ein Administrator ([Ein Rundgang durch die Administration](../../../en/us/help/admin-tour.md)).

Jede Zeile nennt die Seite im Repository, auf der der Ort beschrieben ist, damit Sie mehr lesen oder Ihren Partner
darauf verweisen können.

## Jeden Tag

| Was | Menü | Adresse | Beschrieben in |
|---|---|---|---|
| Das Dashboard: Ihre Entwürfe, ausstehende Elemente, neueste Inhalte | **Dashboard** | `/content/dashboard` | [Rundgang durch die Administration](../../../en/us/help/admin-tour.md#the-dashboard) |
| Der Inhaltsbaum | **Inhaltsstruktur** | | [Rundgang durch die Administration](../../../en/us/help/admin-tour.md#the-left-column) |
| Bilder und Dateien | **Medien** | | [Medien und Uploads](../../../en/us/help/media-and-uploads.md) |
| Ein Element über seine Nummer, seinen Pfad oder seine Adresse öffnen | **Inhaltsfinder** unter dem Baum, oder **Setup > Inhaltsfinder** | `/contentfinder/search` | `doc/en/us/features/6.0/content-finder.md` |
| Der Shop auf einen Blick | **Shop** (erster Eintrag) | `/shop/dashboard` | [Shop-Dashboard](../../../en/us/features/6.0/store-dashboard.md) |
| Alle Inhalte durchsuchen | **Suchen...** in der Kopfzeile (drücken Sie `/`) | | [Suchen](../../../en/us/help/searching.md) |

## Jede Woche oder jeden Monat

| Was | Menü | Adresse | Beschrieben in |
|---|---|---|---|
| Wartende Prozesse (Freigaben, zeitgesteuertes Veröffentlichen) | **Setup > Workflow-Prozesse** | `/workflow/processlist` | [Workflows](../../../en/us/guides/workflows.md) |
| Alte, gesperrte und doppelte Benutzerkonten | **Benutzer > Benutzerkonten prüfen** | `/user/hygiene` | [Benutzerkonten prüfen](../../../en/us/features/6.0/user-hygiene.md) |
| Dateien, die niemand nutzt, kaputte Beziehungen, ungenutzte Dateien | Inhaltsprüfungen | `/contentcheck/unreferenced/<node id>`, `/contentcheck/brokenrelations/<node id>`, `/contentcheck/storage/<node id>` | [Inhaltsprüfungen](../../../en/us/features/6.0/content-checks.md) |
| Wer was getan hat | **Audit** | `/audit/dashboard` | [Prüfprotokoll](../../../en/us/features/6.0/audit-trail.md) |
| Die Logdateien | **Setup > Logdateien** | `/setup/logs` | [Log-Viewer](../../../en/us/features/6.0/log-viewer.md) |
| Druckbare Exporte | **Setup > PDF export** | `/pdf/list` | [PDF-Export](../../../en/us/features/6.0/pdf-export.md) |

## Wenn sich etwas ändert

| Was | Menü | Adresse | Beschrieben in |
|---|---|---|---|
| Die Website für geplante Arbeiten schließen | **Setup > Wartung** | `/setup/maintenance` | [Wartungsmodus](../../../en/us/features/6.0/maintenance-mode.md) |
| Die Installation nach einem Upgrade prüfen | **Setup > Upgrade-Prüfung** | `/setup/systemupgrade` | [Upgrade-Prüfung](../../../en/us/guides/upgrade-check.md) |
| Workflows und ihre Trigger | **Setup > Workflows** | `/workflow/grouplist` | [Workflows](../../../en/us/guides/workflows.md) |

Diese Seiten verwendet meist Ihr Administrator oder Partner. Als Betreiber hilft es zu wissen, dass es sie gibt, damit
Sie die richtige Frage stellen können: „Ist die Upgrade-Prüfung sauber?“, „Ist der Wartungsmodus wieder aus?“.

## Die Hilfe

| Was | Adresse | Beschrieben in |
|---|---|---|
| Das Dashboard der Hilfe | `/exphelp/dashboard` | [Hilfesystem](../../../en/us/features/6.0/help-system.md) |
| Die Einstiegsseite einer Zielgruppe, etwa diese | `/exphelp/audience/customer` | ebenda |
| Wie man einen Fehler meldet | `/exphelp/report` | [Die Meldeseite der Hilfe](gesund-halten/die-meldeseite-der-hilfe.md) |

## Ihr eigenes Konto

Die Personen-Schaltfläche in der Kopfzeile öffnet Ihr Benutzermenü: **Informationen ändern**, **Passwort ändern** und
**Meine E-Mail-Einstellungen** ([Rundgang durch die Administration](../../../en/us/help/admin-tour.md#the-header)). Die linke Spalte des
Dashboards listet **Meine Warteliste**, **Meine Entwürfe**, **Meine Lesezeichen** und **Meine Benachrichtigungseinstellungen**.

## Verwandte Seiten

- [Ein Rundgang durch die Administration](../../../en/us/help/admin-tour.md)
- [Kunden: hier beginnen](kunden-hier-beginnen.md)
- [Tastenkürzel](../../../en/us/help/keyboard-shortcuts.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
