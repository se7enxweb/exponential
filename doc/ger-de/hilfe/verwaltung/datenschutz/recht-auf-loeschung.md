---
title: Das Recht auf Löschung
audience: government, admin, project-manager
category: public-sector
order: 170
summary: Für öffentliche Stellen, die Löschanträge beantworten - was das Entfernen eines Benutzerkontos in Exponential automatisch löscht (E-Mail-Einstellungen, anonymisiertes Einwilligungsprotokoll, Newsletter-Daten über die Lösch-Hooks), was von Hand entfernt werden muss (Formulareinsendungen, Inhalte, Suchbegriffe), was bewusst aufbewahrt wird (Audit-Einträge), und die Schritte.
keywords: Recht auf Löschung, Recht auf Vergessenwerden, DSGVO Artikel 17, Benutzer löschen, eraseForRemovedAccount, exp:mail:preferences erase, anonymisiertes Einwilligungsprotokoll, aufbewahrte Audit-Einträge, Artikel 17 Absatz 3, Formulareinsendungen, Papierkorb, Datensicherungen, öffentlicher Sektor, right to erasure, right to be forgotten, delete user
type: help
---
# Das Recht auf Löschung

Diese Seite richtet sich an Datenschutzbeauftragte und Administratoren. Öffentliche Stellen haben oft Gründe, Daten
aufzubewahren (eine rechtliche Verpflichtung, eine öffentliche Aufgabe, Archivierung); Löschanträge brauchen zuerst eine
rechtliche Bewertung. Wenn Daten entfernt werden müssen, löscht Exponential einen Teil davon automatisch und überlässt
den Rest einzelnen Werkzeugen ([Datenschutzstandards: DSGVO](../../../../eng-us/standards/privacy.md#gdpr)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was das Entfernen eines Kontos bewirkt](#was-das-entfernen-eines-kontos-bewirkt)
2. [E-Mail-Daten ohne Konto löschen](#e-mail-daten-ohne-konto-löschen)
3. [Was Sie von Hand entfernen](#was-sie-von-hand-entfernen)
4. [Was aufbewahrt wird, und warum](#was-aufbewahrt-wird-und-warum)
5. [Schritte](#schritte)
6. [Verwandte Seiten](#verwandte-seiten)

## Was das Entfernen eines Kontos bewirkt

- Das Entfernen eines Benutzerkontos ist **endgültig**: Ein Benutzerobjekt kommt nie in den Papierkorb
  (`eZContentObjectTreeNode::isNodeTrashAllowed()`), und es geschieht ein Konto nach dem anderen mit dem eigenen
  **Entfernen** des Knotens ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
- `eZUser::removeUser()` ruft `expMailPreferences::eraseForRemovedAccount()` auf
  (`kernel/classes/datatypes/ezuser/ezuser.php`): E-Mail-Einstellungen und ausstehende Bestätigungen werden entfernt,
  und das Einwilligungsprotokoll wird anonymisiert. (Ein älterer Satz im Leitfaden für Administratoren sagt, dass das
  noch nicht geschieht; der Code tut es.)
- Die Newsletter-Erweiterung (cjw_newsletter 4.2) klinkt sich in die Löschung ein: Newsletter-Benutzer, Interessen,
  SMS-Daten und Statistiken pro Person einer gelöschten Person werden entfernt
  ([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

## E-Mail-Daten ohne Konto löschen

Für eine Adresse ohne Konto (ein Newsletter-Abonnent):

```bash
./console exp:mail:preferences erase --email=<address> --yes --allow-root-user
```

Der Befehl antwortet mit der Zahl der entfernten Einstellungen und Bestätigungen und der anonymisierten Zeilen des
Einwilligungsprotokolls. Die anonymisierten Zeilen behalten Zeit, Kategorie und Änderung, ohne Adresse, IP oder
Benutzer, sodass Sie weiterhin nachweisen können, dass der Versand eingestellt wurde. Ohne `--yes` verweigert der
Befehl die Ausführung. Wenn der Person nie wieder geschrieben werden darf, fügen Sie die Adresse mit dem Grund `legal`
der Sperrliste hinzu (es wird nur ein Hash gespeichert).

## Was Sie von Hand entfernen

| Daten | Werkzeug |
|---|---|
| Formulareinsendungen | Setup > Gesammelte Informationen: das Formular öffnen, die Sammlungen der Person markieren, entfernen |
| Inhalte, in denen die Person vorkommt | Redakteure; danach den Papierkorb für diese Einträge leeren und bei Bedarf alte Versionen entfernen |
| Suchbegriffe, die den Namen enthalten | Setup > Suchstatistiken, Statistik zurücksetzen |
| Shop-Bestellungen | die Shop-Werkzeuge, vorbehaltlich der buchhalterischen Aufbewahrung |
| Früher erstellte Exporte | die Dateien löschen |

## Was aufbewahrt wird, und warum

- **Audit-Einträge** werden für ihre Aufbewahrungsfrist aufbewahrt: Das Löschen eines Eintrags würde die Hash-Kette
  unterbrechen. Der Audit-Leitfaden verweist auf Art. 17 Abs. 3 Buchst. b und e; antworten Sie mit der
  Aufbewahrungsfrist und der bereits angewandten Pseudonymisierung
  ([Audit-Leitfaden 4.12](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).
- **Protokolle** laufen mit ihrer Aufbewahrungsfrist aus (siehe „Aufbewahrung von Protokollen“).
- **Datensicherungen** laufen mit Ihrer Sicherungsrotation aus; die meisten Organisationen bearbeiten Sicherungen
  nicht, stellen aber sicher, dass eine Wiederherstellung gelöschte Daten nicht wieder in Gebrauch bringt.

## Schritte

1. Bewerten Sie den Antrag (Gründe für die Aufbewahrung?).
2. Exportieren Sie zuerst, was die Person möglicherweise haben möchte, wenn sie danach gefragt hat.
3. Entfernen Sie Formulareinsendungen und Erwähnungen in Inhalten.
4. Entfernen Sie das Konto (Knoten, Entfernen) oder, wenn das Konto stillgelegt, sein Verlauf aber aufbewahrt werden
   muss, siehe „Einschränkung und ‚Deaktivieren und verschieben‘“.
5. Löschen Sie die E-Mail-Daten für Adressen ohne Konto; fügen Sie auf Wunsch einen Sperreintrag mit dem Grund `legal`
   hinzu.
6. Halten Sie mit Datum fest, was Sie getan haben, und antworten Sie der Person, mit dem Hinweis, was aufbewahrt wird
   und bis wann.

## Verwandte Seiten

- [E-Mail-Einstellungen für Administratoren: Anträge](../../../anleitungen/mail-einstellungen-administrator.md#6-anfragen-nach-dem-datenschutzrecht)
- [Einschränkung und „Deaktivieren und verschieben“](einschraenkung-deaktivieren-und-verschieben.md)
- [Die Sperrliste](sperrliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
