---
title: "Benutzerverwaltung von der Konsole: FAQ"
audience: admin, developer, hosting
category: administration
order: 20
summary: Kurze Antworten zu den Befehlen exp:user - Berechtigungen, Passwörter, Probeläufe, geschützte Konten, Entfernen oder Deaktivieren, das Audit, MongoDB, und was bewusst nicht angeboten wird - Konten zusammenführen, Passwörter in Importdateien und eine Option --password.
keywords: FAQ, Fragen, Konten zusammenführen, Passwort in CSV, Probelauf, geschütztes Konto, wer darf es ausführen, Audit, deaktivieren oder entfernen, deaktivieren und verschieben, exp:user, merge accounts, --password, dry run, --apply, resetuserpassword, MongoDB, siteaccess
type: help
---
# Benutzerverwaltung von der Konsole: FAQ

## Was Sie am Ende haben

Eine Antwort auf Ihre Frage, oder einen Link zu der Seite, die sie hat.

## Die Befehle benutzen

**Ändert ein Befehl ohne `--apply` etwas?** Nein. Ohne `--apply` listet er auf, was er tun würde und welche Prüfungen
fehlschlagen. `--dry-run` bedeutet dasselbe; beide zusammen sind ein Bedienfehler.

**Wer darf die Befehle ausführen?** Wer `./console` auf dem Server ausführen darf. Die Befehle prüfen die Richtlinien
des Kontos nicht, als das sie handeln; geben Sie Shell-Zugang also nur den Personen, die die Website verwalten.

**Wessen Name steht im Audit?** Der des Benutzers aus `[UserSettings] UserCreatorID`, oder der des Benutzers, den Sie
mit `--as` nennen. `exp:user:whoami` zeigt ihn.

**Laufen Workflows und Freigaben?** Ja. Die Befehle nutzen dieselben Operationen wie die Administration; Workflows,
die Freigabe neuer Konten, Mails und Audit-Ereignisse verhalten sich also wie bei einem Klick.

**Wie nenne ich ein Konto?** Mit seinem Anmeldenamen, seiner E-Mail-Adresse (wenn nur ein Konto sie hat) oder seiner
ID.

**Funktioniert es mit jeder Datenbank?** Die Listen werden mit SQL gelesen. Auf einer MongoDB-Installation melden
`exp:user:list` und die darauf aufbauenden Befehle, dass sie nicht auflisten können.

## Passwörter

**Warum gibt es keine Option `--password`?** Ein Passwort auf der Befehlszeile bleibt im Verlauf der Shell und
erscheint in der Prozessliste des Servers. Nehmen Sie `--password-stdin`, ein erzeugtes Passwort oder eine Mail, mit
der die Person eines wählt.

**Warum verweigert der Import eine Datei mit einer Passwortspalte?** Eine solche Datei würde Passwörter im Klartext
über Mails, geteilte Ordner und Sicherungen verbreiten. Importieren Sie mit `--send-activation` oder `--disabled` und
lassen Sie jede Person ein Passwort wählen.

**Wird ein erzeugtes Passwort irgendwo gespeichert?** Nur sein Hash, wie bei jedem Passwort. Es wird einmal
ausgegeben und nie wieder, auch nicht im Audit.

**Und `bin/php/resetuserpassword.php`?** Es funktioniert weiterhin. `exp:user:reset-password` bringt den Link zum
Zurücksetzen, den Probelauf, das Audit und die JSON-Ausgabe dazu.

## Deaktivieren und entfernen

**Deaktivieren oder entfernen?** Deaktivieren. Ein deaktiviertes Konto behält den Namen im Verlauf von allem, was die
Person getan hat, und `exp:user:enable` macht es rückgängig. Entfernen Sie ein Konto nur, wenn nichts darauf
verweist.

**Kann ich ein entferntes Konto zurückholen?** Nein. Benutzerkonten kommen nie in den Papierkorb.

**Warum kann ich das Konto `admin` nicht deaktivieren?** Es ist ein geschütztes Konto, wie der anonyme Benutzer, der
Benutzer aus `UserCreatorID`, Sie selbst und der letzte aktivierte Administrator.
[Geschützte Konten](konten-deaktivieren-aktivieren-und-entfernen.md#geschützte-konten).

**Kann die Konsole „Deaktivieren und verschieben“?** Nein. Sie deaktiviert nur. Nutzen Sie die Administration, um ein
Konto in die Gruppe für deaktivierte Konten zu verschieben.

## Bewusst nicht angeboten

**Kann ich zwei Konten zusammenführen?** Nein. Das Zusammenführen würde das Eigentum an Inhalten, Versionen,
Rollenzuweisungen und den Verlauf von einem Konto auf ein anderes übertragen. Der Kernel hat dafür keinen sicheren
Weg, deshalb bietet kein Befehl es an. Deaktivieren Sie das Konto, das nicht gebraucht wird.

**Passwörter in Importdateien** und **eine Option `--password`** werden aus den oben genannten Gründen nicht
angeboten.

## Verwandte Seiten

- [Wenn ein Befehl fehlschlägt](wenn-ein-befehl-fehlschlaegt.md)
- [Benutzerverwaltung von der Konsole](README.md)
