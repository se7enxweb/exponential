---
title: Intranet-FAQ
audience: businesses, project-manager, admin
category: business-solutions
order: 110
summary: Kurze Antworten mit Quellen auf die Fragen, die Führungskräfte zum Betrieb eines Intranets auf Exponential stellen - Vertraulichkeit von Seiten, Abteilungen, Verzeichnisanmeldung, zweistufige Anmeldung, Dokumente, Suche, Freigabe, Benachrichtigungen, eine gemeinsame Installation mit der öffentlichen Website und Nachweise.
keywords: Intranet-FAQ, Fragen, private Seiten, Abteilungen, LDAP, Active Directory, SAML, Zwei-Faktor, Dokumente, Suche, Freigabe, Benachrichtigungen, Multisite, Audit, mobil, intranet FAQ, questions, private pages, departments, two-factor, documents, search, approval, notifications, multisite, audit, mobile
type: help
---
# Intranet-FAQ

Diese Seite ist für Führungskräfte und Projektleitungen, die schnelle Antworten brauchen. Jede Antwort ist kurz und
verlinkt die Seite mit den Einzelheiten. Wenn Ihre Frage hier nicht steht, behandelt die
[FAQ für Unternehmen](../faq-fuer-unternehmen.md) allgemeine Fragen.

## Inhalt

1. [Zugriff und Vertraulichkeit](#zugriff-und-vertraulichkeit)
2. [Anmeldung](#anmeldung)
3. [Inhalte und Dokumente](#inhalte-und-dokumente)
4. [Veröffentlichen und Kommunikation](#veröffentlichen-und-kommunikation)
5. [Betrieb und Nachweise](#betrieb-und-nachweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Zugriff und Vertraulichkeit

**Kann das ganze Intranet eine Anmeldung verlangen?**
Ja. Ein Siteaccess mit `site.ini [SiteAccessSettings] RequireUserLogin=true` schickt jeden Besucher, der nicht
angemeldet ist, auf die Anmeldeseite; nur die Ansichten in `AnonymousAccessList[]` bleiben offen
([Was Besucher sehen dürfen](../../ersteller/website-ersteller/rechte-fuer-besucher.md#einstellungen-des-siteaccess)).

**Können Abteilungen Seiten haben, die andere Abteilungen nicht sehen?**
Ja. Legen Sie die Inhalte jeder Abteilung in ihre eigene Sektion und geben Sie `content/read` für diese Sektion nur den
Gruppen, die sie lesen dürfen ([Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md)).

**Kann jede Abteilung nur ihre eigenen Seiten bearbeiten?**
Ja. Ordnen Sie der Gruppe jeder Abteilung eine Rolle Redakteur mit einer Sektions-Einschränkung zu
([Sektionen, 13.2](../../../anleitungen/sektionen.md#132-ein-intranet-mit-abteilungen)).

**Können das Intranet und unsere öffentliche Website in einer Installation laufen?**
Ja, als getrennte Siteaccesses, die sich Inhalte, Benutzer und Betrieb teilen. Halten Sie Intranetinhalte in Sektionen,
die die öffentliche Rolle Anonymous nicht lesen kann, und testen Sie abgemeldet
([Mehrere Websites in einer Installation einrichten](../../ersteller/website-ersteller/multisite-einrichten.md)).

**Können wir den Zugriff nur aus dem Büro erlauben?**
Netzwerkbeschränkungen werden im Webserver oder in einem VPN vor der Website eingerichtet, nicht in Exponential
([Die Administration einschränken, Network restrictions](../../hosting/sicherheit/die-administration-einschraenken.md#einschränkungen-im-netzwerk)).

## Anmeldung

**Können sich Mitarbeiter mit ihrem Konto im Firmenverzeichnis anmelden?**
Ja, über LDAP. Der Kernel liefert einen LDAP-Login-Handler mit, der in `settings/ldap.ini` eingestellt wird; er ist
aus, bis er `LoginHandler[]` hinzugefügt wird ([Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md)).

**Gibt es Single Sign-on mit SAML oder OpenID Connect?**
Nicht im Repository. Es gibt einen Erweiterungspunkt für Single-Sign-on-Handler (`SingleSignOnHandlerArray[]`) und für
eigene Login-Handler; ein Handler für Ihren Identitätsanbieter wäre ein Erweiterungsprojekt
([Was nicht eingebaut ist](verzeichnisanmeldung-und-ldap.md#was-nicht-eingebaut-ist)).

**Gibt es eine zweistufige Anmeldung?**
Ja, über die Erweiterung `sevenx_authentication_2fa`: Codes aus einer Authenticator-App oder per E-Mail, freiwillig
oder für alle vorgeschrieben ([Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md#zweistufige-anmeldung)).

**Was geschieht nach mehreren falschen Passwörtern?**
Standardmäßig nichts: `MaxNumberOfFailedLogin=0` bedeutet keine Sperre. Setzen Sie für das Intranet einen Wert
([Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md#ein-konto-nach-fehlgeschlagenen-anmeldungen-sperren)).

## Inhalte und Dokumente

**Kann ein Dokumentlink an jemanden weitergeleitet werden, der ihn nicht sehen soll?**
Die Person kann das Dokument trotzdem nicht herunterladen. Jeder Download über `content/download` prüft, ob die Person
das Objekt lesen darf
([Dokumente und Dateibibliotheken](dokumente-und-dateibibliotheken.md#downloads-beachten-berechtigungen)).

**Behalten wir alte Versionen einer Richtlinie?**
Ja. Jedes Speichern ist eine Version; frühere veröffentlichte Versionen werden archiviert und können verglichen und
wiederhergestellt werden ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).

**Zeigt die Suche Mitarbeitern Dinge, die sie nicht lesen dürfen?**
Nein. Suchergebnisse folgen den Berechtigungen ([Suche im Intranet](suche-im-intranet.md)).

**Kann die Suche in PDF- und Word-Dateien hineinsehen?**
Ja, wenn die in `settings/binaryfile.ini` genannten Werkzeuge zur Textextraktion installiert sind, oder mit Solr
([Suche im Intranet, In Dokumenten suchen](suche-im-intranet.md#in-dokumenten-suchen)).

**Können Mitarbeiter die Bibliothek als Netzlaufwerk einbinden?**
Der Kernel enthält einen WebDAV-Einstiegspunkt, standardmäßig ausgeschaltet. Testen Sie ihn auf Ihrer Installation,
bevor Sie es zusagen ([Dokumente und Dateibibliotheken, WebDAV](dokumente-und-dateibibliotheken.md#webdav)).

## Veröffentlichen und Kommunikation

**Können Texte freigegeben werden, bevor sie erscheinen?**
Ja, mit einem Ereignis Approve, das vor dem Veröffentlichen ausgelöst wird, oder mit Prüfzuständen
([Freigabe-Workflows](freigabe-workflows.md)).

**Kann eine Ankündigung zu einer festgelegten Zeit live gehen?**
Das Workflow-Ereignis *Bis Datum warten* hält die Veröffentlichung bis zu einem Datumsfeld der Klasse zurück; es braucht
den Workflow-Cronjob ([Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md#zu-einer-festgelegten-zeit-veröffentlichen)).

**Wie erfahren Mitarbeiter von neuen Inhalten?**
Sie folgen Ordnern und bekommen sofort oder als Digest eine Mail
([Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md)).

**Können wir einen Tippfehler korrigieren, ohne erneut allen zu mailen?**
Ja, mit *Ohne Benachrichtigung veröffentlichen* für Redakteure, die die Richtlinie haben
([Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md#korrekturen-ohne-zweite-mail)).

## Betrieb und Nachweise

**Können wir sehen, wer etwas geändert oder entfernt hat?**
Ja. Das Prüfprotokoll zeichnet Inhalts-, Zugriffs- und Systemereignisse in einem manipulationssicheren Log auf, das
standardmäßig eingeschaltet ist ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

**Was muss im Hintergrund laufen?**
Cronjobs, vor allem die Gruppe `frequent` mit den Teilen für Workflow, Benachrichtigungen und Audit
([Cronjobs](../../../anleitungen/cronjobs.md)).

**Wie entfernen wir Personen, die gegangen sind?**
Deaktivieren Sie ihre Konten unter **Benutzer > Benutzerkonten prüfen**, oder deaktivieren und verschieben Sie sie; das
Entfernen eines Kontos ist endgültig, daher ist das Deaktivieren der sicherere erste Schritt
([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

**Was kostet der Betrieb eines Intranets?**
Siehe das [durchgerechnete Beispiel für ein Intranet](../kosten/durchgerechnetes-beispiel-intranet.md), dessen Eingabewerte
Beispielannahmen sind, die Sie ersetzen.

**Brauchen Mitarbeiter eine App auf ihren Telefonen?**
Zum Lesen des Intranets ist keine App nötig; es ist eine Website. Die Authenticator-App wird nur gebraucht, wenn die
zweistufige Anmeldung mit App-Codes verwendet wird ([Mobil und headless](../mobil-und-headless/README.md)).

## Verwandte Seiten

- [Intranets](README.md)
- [Checkliste für die Intranet-Einführung](checkliste-fuer-die-intranet-einfuehrung.md)
- [Extranet-FAQ](../extranets/extranet-faq.md)
- [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
