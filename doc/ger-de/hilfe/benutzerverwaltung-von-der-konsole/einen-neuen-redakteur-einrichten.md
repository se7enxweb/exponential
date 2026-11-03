---
title: "Einen neuen Redakteur einrichten"
audience: admin, hosting
category: administration
order: 10
summary: Das Konto eines neuen Redakteurs von der Konsole in der Gruppe Editors mit einem einmaligen Passwort anlegen, bei der ersten Anmeldung ein neues Passwort verlangen, die Rollen prüfen und die Anmeldung sicher übergeben.
keywords: Einarbeitung, neuer Redakteur, neue Mitarbeiterin, Redakteurskonto anlegen, Gruppe Editors, erzeugtes Passwort, Passwort übergeben, erste Anmeldung, onboarding, new editor, exp:user:create, --require-change, exp:user:require-password-change, exp:user:roles
type: help
---
# Einen neuen Redakteur einrichten

Legen Sie das Konto eines neuen Redakteurs mit einem Passwort an, das niemand getippt hat, und lassen Sie ihn bei der
ersten Anmeldung ein neues wählen.

## Was Sie am Ende haben

Ein Redakteurskonto in der Gruppe Editors, mit der Rolle Editor, das bei der ersten Anmeldung nach einem neuen
Passwort fragt.

## Bevor Sie beginnen

- Shell-Zugang zum Server, im Wurzelverzeichnis der Installation.
- Die E-Mail-Adresse und der Name des Redakteurs. Die Beispiele verwenden `jane@example.com` und Jane Doe.

## Schritte

1. Prüfen Sie, ob der Anmeldename frei ist.

   ```bash
   ./console exp:user:list --search=jane
   ```

2. Führen Sie den Befehl zum Anlegen als Probelauf aus.

   ```bash
   ./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
       --group=Editors --generate-password
   ```

   Der Befehl listet auf, was er anlegen würde. Eine fehlgeschlagene Prüfung wird genannt.
3. Führen Sie denselben Befehl mit `--apply` aus.
   Das Konto wird veröffentlicht. Das erzeugte Passwort wird einmal ausgegeben.
4. Verlangen Sie bei der ersten Anmeldung ein neues Passwort.

   ```bash
   ./console exp:user:require-password-change jane --apply
   ```

5. Prüfen Sie die Gruppen und Rollen.

   ```bash
   ./console exp:user:roles list jane
   ```

   Die Rolle Editor ist aufgelistet, über die Gruppe Editors.
6. Übergeben Sie Anmeldename und Passwort auf zwei verschiedenen Wegen, zum Beispiel den Anmeldenamen per E-Mail und
   das Passwort per Telefon.

Sagt der Befehl, dass die Veröffentlichung wartet, gibt die Installation neue Konten frei. Geben Sie es mit
`./console exp:user:approve jane --apply` frei.

## Ergebnis

Der Redakteur meldet sich mit dem Passwort an, wählt ein neues und beginnt zu arbeiten.

## Varianten

- **Der Redakteur wählt das Passwort selbst**: nehmen Sie `--send-activation` statt `--generate-password`. Der
  Redakteur erhält die Aktivierungsmail und nutzt dann **Passwort vergessen**. Lassen Sie die Schritte 4 und 6 aus.
- **Redakteure für einen Teil der Website**: legen Sie eine Gruppe an und schränken Sie die Rolle auf einen Teilbaum
  ein ([Gruppen und Rollen](gruppen-und-rollen.md)).

## Verwandte Seiten

- [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md):
  dasselbe in der Administration.
- [Den Zugang beim Ausscheiden schließen](den-zugang-beim-ausscheiden-schliessen.md)
