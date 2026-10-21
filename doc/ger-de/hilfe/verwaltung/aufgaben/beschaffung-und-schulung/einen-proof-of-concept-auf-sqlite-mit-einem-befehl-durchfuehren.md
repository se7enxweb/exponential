---
title: Einen Proof of Concept auf SQLite mit einem Befehl durchführen
audience: government, project-manager, admin
category: public-sector
order: 70
summary: Schritt für Schritt Exponential auf einer Testmaschine mit einem Befehl auf SQLite installieren, sich anmelden, die Aufgaben ausprobieren, die Ihre Anforderungen nennen, und es danach entfernen - damit eine Behörde das Produkt vor oder während einer Bewertung selbst sieht.
keywords: Proof of Concept, PoC, Testinstallation, SQLite, exp:install, ein Befehl, Bewertung, Exponential ausprobieren, Aufgabe zur Beschaffung, proof of concept, test installation, one command, evaluation, try Exponential, procurement task
type: help
---
# Einen Proof of Concept auf SQLite mit einem Befehl durchführen

**Wer:** eine IT-Fachkraft im Bewertungsteam oder jeder mit einem Terminal. **Wann:** vor der Ausschreibung (um das
Produkt kennenzulernen) oder während der Bewertung. Der Befehl stammt aus
[Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md) und
[7. Die Installation auf der Konsole](../../../../installation/07-installation-ueber-die-konsole.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was ausprobiert werden sollte](#was-ausprobiert-werden-sollte)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Testmaschine (nicht die Produktion) mit PHP 8.0 oder neuer, Composer 2 und Git
  ([2. Anforderungen](../../../../installation/02-voraussetzungen.md#21-die-checkliste)).
- Der Code, geklont und mit installierten Composer-Paketen ([3. Den Code beschaffen](../../../../installation/03-den-code-beschaffen.md)).
- Kein Datenbankserver nötig: SQLite ist die Voreinstellung.

## Schritte

1. Prüfen Sie zuerst, ohne etwas zu installieren:

   ```bash
   ./console exp:install --dry-run
   ```

2. Installieren Sie. Ohne Optionen installiert das das Multisite-Paket auf SQLite in `eng-US`, mit den Siteaccesses
   `site` und `admin`:

   ```bash
   ./console exp:install
   ```

3. Lesen Sie die Zusammenfassung: Sie gibt die Adressen der Site und der Administration und den Benutzernamen aus. Das
   Administratorpasswort wird erzeugt (24 Zeichen), einmal ausgegeben und nach `var/log/initial-admin-password`
   geschrieben, nur für den Eigentümer lesbar.
4. Liefern Sie die Site aus, zum Beispiel mit Velocity oder dem eingebauten Server von PHP
   ([8. Die Site ausliefern](../../../../installation/08-die-website-ausliefern.md)).
5. Melden Sie sich in der Administration an und ändern Sie das Passwort; löschen Sie dann
   `var/log/initial-admin-password`.
6. Wenn etwas fehlschlägt, lesen Sie `var/log/setup.log`, das jeden Schritt und seine Fehler listet.

## Was ausprobiert werden sollte

Nehmen Sie die Aufgaben aus Ihren Anforderungen, zum Beispiel:

- einen Artikel anlegen, in der Vorschau ansehen und veröffentlichen; zwei Versionen vergleichen
  ([Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md));
- eine Übersetzung hinzufügen; die Sprache der Oberfläche umschalten ([Sprachen](../../../sprachen.md));
- einen Freigabe-Workflow einrichten ([Workflows](../../../../anleitungen/workflows.md));
- eine auf eine Sektion eingeschränkte Rolle anlegen ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md));
- auf das Design admin4 umschalten und es nur mit der Tastatur ausprobieren
  ([Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md));
- sich das Audit-Protokoll und **Setup > Systeminformationen** ansehen;
- einen Export ausführen ([Eine Ausstiegsübung durchführen](eine-ausstiegsuebung-durchfuehren.md)).

Der Plan für den Proof of Concept im Teil zur Bewertung hat eine strukturierte Fassung
([Plan für den Proof of Concept](../../../../../eng-us/help/government/procurement/evaluation/proof-of-concept-plan.md)).

## Ergebnis

Eine funktionierende Testinstallation, die Ihr Team selbst genutzt hat, und Notizen je Aufgabe: erledigt, wie und
etwaige Lücken.

## Fallstricke

- Echte personenbezogene Daten in einer Testinstallation verwenden.
- Das öffentliche Design des Demopakets als Ihre künftige Site beurteilen: Das Design Ihrer Site wird für Sie gebaut.
- Die Testinstallation mit einem bekannten Passwort aus dem Internet erreichbar lassen.
- `exp:install` über eine bestehende Installation ausführen: Es verweigert, sofern Sie nicht `--force` hinzufügen.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Erste Schritte](../../../../anleitungen/erste-schritte.md)
- [Installation von null (Demo-Skript)](../../../../../eng-us/help/press/demo-script-install-from-zero.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
