---
title: Staging und Freigabe
audience: government, admin, project-manager, developer
category: public-sector
order: 50
summary: Wie eine öffentliche Stelle mit Exponential eine Änderung von der Entwicklung über den Test (Staging) in die Produktion bringt - eine Codebasis pro Umgebung, Varianten von Einstellungen mit EXP_ENV, eine Kopie der Daten zum Testen, die Prüfungen auf dem Staging und die Abnahme vor der Produktion.
keywords: Staging, Testumgebung, Abnahme, Abnahmetest, UAT, Freigabe, Abnahme durch Unterschrift, EXP_ENV, Umgebungen, Bereitstellungspipeline, Release, Testkopie, Produktion, staging, test environment, acceptance, user acceptance testing, approval, sign-off, environments, deployment pipeline, test copy, production
type: help
---
# Staging und Freigabe

Diese Seite richtet sich an Projektleitungen, Administratoren und Entwicklerinnen und Entwickler, die zeigen müssen,
dass eine Änderung getestet wurde, bevor sie die öffentliche Website erreicht hat. Sie beschreibt einen Weg über drei
Umgebungen (Entwicklung, Test oder Staging, Produktion) mit den Funktionen, die Exponential dafür bietet, und die
Abnahmepunkte, nach denen ein Änderungsbeirat üblicherweise fragt.

## Inhalt
1. [Der Weg einer Änderung](#der-weg-einer-änderung)
2. [Umgebungen nur dort unterscheiden, wo sie es sollen](#umgebungen-nur-dort-unterscheiden-wo-sie-es-sollen)
3. [Daten auf dem Staging](#daten-auf-dem-staging)
4. [Was auf dem Staging zu testen ist](#was-auf-dem-staging-zu-testen-ist)
5. [Die Abnahme](#die-abnahme)
6. [Verwandte Seiten](#verwandte-seiten)

## Der Weg einer Änderung

| Schritt | Wo | Ergebnis |
|---|---|---|
| Entwickeln | Entwicklermaschine, `EXP_ENV=dev` | ein Commit in der Versionsverwaltung |
| Prüfen | Ihr Code-Review | ein freigegebener Merge |
| Auf dem Staging bereitstellen | Testserver, `EXP_ENV=test` | die Änderung läuft auf einer Kopie der Website |
| Testen und abnehmen | Staging | Testnotizen, Bericht der Upgrade-Prüfung, Abnahme durch die Verantwortlichen für den Dienst |
| Freigeben | Änderungsbeirat | ein freigegebener Änderungsdatensatz ([Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md)) |
| In der Produktion bereitstellen | Live-Server, `EXP_ENV=prod` | derselbe Commit bereitgestellt; Nachweise gesammelt |

Stellen Sie auf dem Staging und in der Produktion denselben Commit bereit, niemals eine von Hand bearbeitete Kopie; mit
eingeschaltetem [ManagedByDeployment](verwaltet-durch-bereitstellung.md) in der Produktion gibt es keinen anderen Weg.

## Umgebungen nur dort unterscheiden, wo sie es sollen

Seit Oktober 2026 benennt die Konstante `EXP_ENV` in `config.env.php` die Umgebung einer Maschine, und jede
Einstellungsdatei kann dafür eine Variante haben, zum Beispiel `settings/override/site.ini.test.append.php`
([Einstellungen pro Umgebung](../../../../funktionen/6.0/umgebungseinstellungen.md)). Typische Unterschiede:

| Einstellung | Staging | Produktion |
|---|---|---|
| `[MailSettings] Transport` | `file` (keine echten E-Mails an Bürgerinnen und Bürger) | Ihr Mailserver |
| `[DebugSettings] DebugOutput` | für die Adressen der Testenden eingeschaltet | ausgeschaltet oder nur für aufgeführte Adressen |
| Umgebungsabzeichen der Administration | `site.ini [AdminEnvironment] Level=auto` zeigt die Umgebung in admin4 | dasselbe, mit Anzeige der Produktion |
| Datenbank | die Staging-Datenbank | die Produktionsdatenbank |

`dev` und `test` zeigen außerdem PHP-Fehler im Browser an; `prod` lässt `display_errors` so, wie `php.ini` es setzt.
Templates können mit `{exp_environment()}` ein Banner anzeigen, damit niemand das Staging mit der Live-Website verwechselt.

## Daten auf dem Staging

Tests brauchen realistische Inhalte, und Produktionsinhalte können personenbezogene Daten enthalten (Benutzerkonten,
Formulareinsendungen, Newsletter-Abonnenten). Entscheiden Sie mit Ihrer oder Ihrem Datenschutzbeauftragten, ob das
Staging eine vollständige Kopie, eine Kopie ohne personenbezogene Daten oder nur Testinhalte enthalten darf. Die
Sicherungsverwaltung `git_manager` kann einen „AGPL-kompatiblen Datenbank-Dump“ erzeugen, aus dem Passwörter,
E-Mail-Adressen, Schlüssel und Pfade entfernt sind (nur MySQL und MariaDB)
([git_manager](../../../../funktionen/6.0/erweiterungen/git_manager.md)). Abläufe zur Wiederherstellung:
[Auf einem Testserver wiederherstellen](../datensicherung-und-wiederherstellung/wiederherstellung-auf-einem-testserver.md).

## Was auf dem Staging zu testen ist

1. Die Änderung selbst, durch die Personen, die sie angefordert haben.
2. `./console exp:velocity deploy --dry-run --allow-root-user` und die echte Bereitstellung, mit der Dauer jedes Schritts.
3. **Setup > Upgrade-Prüfung**: **Überprüfe Dateikonsistenz** und **Überprüfe Datenbankkonsistenz**
   ([Upgrade-Prüfung vorher und nachher](upgrade-pruefung-vorher-und-nachher.md)).
4. **Setup > Systeminformationen**: Zustandsprüfungen grün; **Bericht herunterladen (.txt)** für die Akte.
5. **Setup > Logdateien**, **Fehlerübersicht**: keine neuen Fehler nach der Änderung.
6. Eine Veröffentlichung über den Freigabe-Workflow, eine Suche, eine Formulareinsendung.
7. Barrierefreiheit und Layout geänderter Templates, im hellen und dunklen Modus der Administration, wo sich die
   Administration geändert hat.

## Die Abnahme

Halten Sie fest, wer die Änderung auf dem Staging abgenommen hat, auf welchem Commit und mit welchen Nachweisen, und
hängen Sie das an den Änderungsdatensatz an. Die Produktion stellt denselben Commit im freigegebenen Wartungsfenster
bereit ([Wartungsfenster](wartungsfenster.md)).

## Verwandte Seiten
- [Umgebungen: Entwicklung, Test und Produktion](../hosting/umgebungen-entwicklung-test-produktion.md)
- [Umgebungsspezifische Einstellungen einrichten](../../aufgaben/betrieb/umgebungsspezifische-einstellungen-einrichten.md)
- [Rücknahme planen](planung-der-ruecknahme.md)
- [Leitfaden zum Bereitstellen](../../../../anleitungen/bereitstellung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
