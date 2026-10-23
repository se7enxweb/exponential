# Polish (pol-PL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Polish as used in Poland. No other locale falls back to this catalogue. The register is neutral and
polite: the interface does not speak to the user as "ty", nor as "Pan/Pani"; sentences are impersonal. Be concise
on buttons and labels.

## 2. Form of address

- **Buttons, menu items, links and tooltips that name an action** use the infinitive: "Zapisać", "Usunąć",
  "Edytować", "Anulować", "Utworzyć nowy", "Zastosować zmiany", "Wysłać do publikacji", "Usunąć zaznaczone",
  "Wyczyścić pamięć podręczną", "Zaznaczyć klasę do usunięcia.", "Kliknąć, aby skopiować". Not the imperative
  ("Zapisz", "Usuń").
- **Titles, column headings and tabs** are nouns: "Struktura treści", "Konta użytkowników", "Typ układu", "Edycja"
  (a page title, where the button reads "Edytować").
- **Confirmation questions** use the infinitive: "Czy na pewno usunąć tę klasę?", "Usunąć ten stan?"
- **Sentences, hints and error messages** are impersonal:
  - "Należy wybrać lokalizację obiektu '%class', który zostanie z niego utworzony."
  - "Proszę wybrać lokalizację" (Please choose a location)
  - "Brak uprawnień do usuwania klas." (You do not have permission to ...)
  - "Ustawienia historii wersji można zmienić w content.ini ..." (You can ...)
  - "Kliknięcie ikony wyświetla menu kontekstowe."
  - Results: "Usunięto wersje robocze: %count.", "Element zostanie usunięty."
- An instruction inside a sentence is impersonal too, not an infinitive and not an imperative: "Należy wybrać blok,
  aby zobaczyć jego właściwości."
- "Your" is dropped or made impersonal: "Your profile" is "Profil".
- Not allowed: ty and its forms (ty, ci, cię, twój, twoja), the second person singular in a sentence (możesz,
  chcesz, masz, musisz), the imperative on buttons and in sentences (zapisz, usuń, kliknij), and Pan, Pani, Państwo.

## 3. Script and typography

- Latin script with ą ć ę ł ń ó ś ź ż. Sentence case: "Struktura treści", "Użytkownicy i grupy".
- Quotation marks in running text: „…”. Inside HTML attributes and JavaScript the straight quotes of the source
  stay. A button named in a sentence is quoted as the button reads: „Anuluj”.
- Polish has three plural forms (1, 2–4, 5+), the source only "item(s)". Use a label form that reads well for every
  number: "Elementy: %count", "Obiekty oznaczone tagiem '%keyword': %count".
- A placeholder is never declined; place it after a noun: "ID węzła: %node_id".

## 4. Glossary

| English | Polish | Note |
|---|---|---|
| content | treść | |
| content object / object | obiekt treści / obiekt | |
| node | węzeł | node ID: ID węzła; main node: węzeł główny |
| location / main location | lokalizacja / lokalizacja główna | |
| content class / class | klasa treści / klasa | class group: grupa klas |
| attribute | atrybut | |
| datatype | typ danych | |
| version | wersja | |
| draft | wersja robocza | Store draft: Zapisać wersję roboczą; Discard draft: Odrzucić wersję roboczą |
| publish / published | opublikować / opublikowano | Send for publishing: Wysłać do publikacji |
| siteaccess | dostęp do witryny | pl. dostępy do witryny |
| site | witryna | sitemap: mapa witryny |
| section | sekcja | |
| object state / state | stan obiektu / stan | |
| role | rola | |
| policy | zasada | pl. zasady |
| limitation | ograniczenie | |
| permission / access denied | uprawnienie / odmowa dostępu | Permission denied: Brak uprawnień |
| user / user group | użytkownik / grupa użytkowników | |
| user accounts | konta użytkowników | |
| owner / creator | właściciel / twórca | |
| created / modified | utworzono / zmodyfikowano | column headings |
| cache | pamięć podręczna | Clear cache: Wyczyścić pamięć podręczną |
| template | szablon | |
| override | nadpisanie | override template: szablon nadpisujący; the verb: nadpisać |
| fallback | tłumaczenie zastępcze | fallback chain: łańcuch tłumaczeń zastępczych |
| design | projekt graficzny | |
| workflow | przepływ pracy | |
| trigger / event | wyzwalacz / zdarzenie | |
| trash | kosz | |
| bookmarks | zakładki | |
| settings | ustawienia | |
| Setup (tab) | Konfiguracja | |
| Dashboard | Pulpit | |
| Content structure | Struktura treści | |
| Media library | Biblioteka multimediów | |
| layout | układ | layout type: typ układu; the name Exponential Layouts stays |
| block / zone | blok / strefa | |
| extension | rozszerzenie | |
| package | pakiet | |
| module / view | moduł / widok | the button "View": Wyświetlić |
| search | wyszukać | noun: wyszukiwanie; search engine: wyszukiwarka |
| search index / reindex | indeks wyszukiwania / ponowne indeksowanie | |
| log / log file | dziennik / plik dziennika | |
| audit / audit trail | audyt / dziennik audytu | |
| job / queue | zadanie / kolejka | job queue: kolejka zadań |
| worker | proces roboczy | |
| cronjob | zadanie cron | |
| preview | podgląd | |
| upload / download | przesłać / pobrać | |
| export / import | wyeksportować / zaimportować | the noun: eksport / import |
| remove / delete | usunąć | Remove selected: Usunąć zaznaczone |
| edit | edytować | page title: Edycja |
| save / store | zapisać | Store (shop tab): Sklep |
| discard | odrzucić | |
| apply | zastosować | Apply changes: Zastosować zmiany |
| cancel | anulować | |
| OK / yes / no | OK / tak / nie | |
| back / next / previous | wstecz / następna / poprzednia | a wizard step button: Dalej |
| create / add | utworzyć / dodać | |
| select / browse | wybrać / przeglądać | ticking for an action: zaznaczyć; Select all: Zaznaczyć wszystko; Deselect all: Odznaczyć wszystko |
| move / copy | przenieść / skopiować | |
| hide / reveal | ukryć / odkryć | hidden / visible: ukryty / widoczny |
| enable / disable | włączyć / wyłączyć | enabled / disabled: włączone / wyłączone |
| restore / assign | przywrócić / przypisać | |
| approve / reject / pending | zatwierdzić / odrzucić / oczekująca | |
| archive | archiwum | |
| order (shop) | zamówienie | order status: stan zamówienia |
| order (sorting) | kolejność | |
| shop / basket / customer / product | sklep / koszyk / klient / produkt | |
| VAT / discount / price / currency | VAT / rabat / cena / waluta | |
| wish list | lista życzeń | |
| newsletter / subscription | newsletter / subskrypcja | |
| tag / keywords | tag / słowa kluczowe | pl. tagi |
| URL alias / wildcard | alias URL / symbol wieloznaczny | |
| translation / language | tłumaczenie / język | main language: język główny; interface language: język interfejsu |
| locale | ustawienia regionalne | |
| relation / related objects | relacja / obiekty powiązane | reverse related: obiekty powiązane zwrotnie |
| subtree / parent / sub items | poddrzewo / element nadrzędny / elementy podrzędne | |
| notification / collaboration | powiadomienie / współpraca | |
| identifier / remote ID | identyfikator / zdalne ID | |
| login (field) / password | login / hasło | username: nazwa użytkownika |
| log in / log out | zalogować się / wylogować się | |
| e-mail | e-mail | |
| file / folder / image | plik / folder / obraz | |
| item | element | |
| name / title / description | nazwa / tytuł / opis | |
| type / status | typ / stan | |
| default / required | domyślny / wymagane | |
| error / warning | błąd / ostrzeżenie | |
| filter / details | filtr / szczegóły | |
| run / refresh / update / reset | uruchomić / odświeżyć / zaktualizować / wyzerować | |
| dry run | przebieg próbny | |
| debug | debugowanie | |
| editor (software) / editor (person) | edytor / redaktor | template editor: edytor szablonów |
| provider | dostawca | |
| prompt / model / token | prompt / model / token | |
| embedding | osadzenie (embedding) | |
| thread / chain | wątek / łańcuch | |

## 5. Decisions after the translation

- **Infinitive on buttons.** Buttons, menu items and action tooltips use the infinitive ("Zapisać", "Usunąć
  zaznaczone", "Wyczyścić pamięć podręczną"), as the first complete translation did. This is the decision for this
  catalogue: a later change of the buttons to the imperative ("Zapisz", "Usuń zaznaczone") was taken back, and texts
  added since then follow the infinitive as well. Confirmation questions keep the infinitive ("Usunąć ten stan?"),
  and sentences stay impersonal. Every catalogue, the kernel's and each extension's, follows the same rule.
- **Store**: the shop tab of the administration header is "Sklep"; every other "Store" is the verb "Zapisać".
- **Order** in the shop (an order, its receipt, the order column of the shop dashboard) is "Zamówienie"; "Kolejność"
  is only the sort order.
