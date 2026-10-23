# Polish (pol-PL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Polish as used in Poland. No other locale falls back to this catalogue. The register is neutral and
polite: the interface does not speak to the user as "ty", nor as "Pan/Pani"; sentences are impersonal. Be concise
on buttons and labels.

## 2. Form of address

- **Buttons, menu items, links and tooltips that name an action** use the imperative: "Zapisz", "Usuń", "Edytuj",
  "Anuluj", "Utwórz nowy", "Zastosuj zmiany", "Wyślij do publikacji", "Usuń zaznaczone", "Wyczyść pamięć
  podręczną", "Zaznacz klasę do usunięcia.", "Kliknij, aby skopiować".
- **Titles, column headings and tabs** are nouns: "Struktura treści", "Konta użytkowników", "Typ układu", "Edycja"
  (a page title, where the button reads "Edytuj").
- **Confirmation questions** use the infinitive: "Czy na pewno usunąć tę klasę?", "Usunąć ten węzeł?"
- **Sentences, hints and error messages** are impersonal:
  - "Należy wybrać lokalizację obiektu '%class', który zostanie z niego utworzony."
  - "Proszę wybrać lokalizację" (Please choose a location)
  - "Brak uprawnień do usuwania klas." (You do not have permission to ...)
  - "Ustawienia historii wersji można zmienić w content.ini ..." (You can ...)
  - "Kliknięcie ikony wyświetla menu kontekstowe."
  - Results: "Usunięto wersje robocze: %count.", "Element zostanie usunięty."
- "Your" is dropped or made impersonal: "Your profile" is "Profil".
- Not allowed: ty and its forms (ty, ci, cię, twój, twoja), the second person singular in a sentence (możesz,
  chcesz, masz, musisz), and Pan, Pani, Państwo.

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
| draft | wersja robocza | Store draft: Zapisz wersję roboczą; Discard draft: Odrzuć wersję roboczą |
| publish / published | opublikuj / opublikowano | Send for publishing: Wyślij do publikacji |
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
| cache | pamięć podręczna | Clear cache: Wyczyść pamięć podręczną |
| template | szablon | |
| override | nadpisanie | override template: szablon nadpisujący |
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
| module / view | moduł / widok | the button "View": Wyświetl |
| search | wyszukaj | noun: wyszukiwanie; search engine: wyszukiwarka |
| search index / reindex | indeks wyszukiwania / ponowne indeksowanie | |
| log / log file | dziennik / plik dziennika | |
| audit / audit trail | audyt / dziennik audytu | |
| job / queue | zadanie / kolejka | job queue: kolejka zadań |
| worker | proces roboczy | |
| cronjob | zadanie cron | |
| preview | podgląd | |
| upload / download | prześlij / pobierz | |
| export / import | eksportuj / importuj | the noun: eksport / import |
| remove / delete | usuń | Remove selected: Usuń zaznaczone |
| edit | edytuj | page title: Edycja |
| save / store | zapisz | Store (shop tab): Sklep |
| discard | odrzuć | |
| apply | zastosuj | Apply changes: Zastosuj zmiany |
| cancel | anuluj | |
| OK / yes / no | OK / tak / nie | |
| back / next / previous | wstecz / następna / poprzednia | a wizard step button: Dalej |
| create / add | utwórz / dodaj | |
| select / browse | wybierz / przeglądaj | ticking for an action: zaznacz; Select all: Zaznacz wszystko; Deselect all: Odznacz wszystko |
| move / copy | przenieś / kopiuj | |
| hide / reveal | ukryj / odkryj | hidden / visible: ukryty / widoczny |
| enable / disable | włącz / wyłącz | enabled / disabled: włączone / wyłączone |
| restore / assign | przywróć / przypisz | |
| approve / reject / pending | zatwierdź / odrzuć / oczekująca | |
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
| log in / log out | zaloguj się / wyloguj | |
| e-mail | e-mail | |
| file / folder / image | plik / folder / obraz | |
| item | element | |
| name / title / description | nazwa / tytuł / opis | |
| type / status | typ / stan | |
| default / required | domyślny / wymagane | |
| error / warning | błąd / ostrzeżenie | |
| filter / details | filtr / szczegóły | |
| run / refresh / update / reset | uruchom / odśwież / zaktualizuj / wyzeruj | |
| dry run | przebieg próbny | |
| debug | debugowanie | |
| editor (software) / editor (person) | edytor / redaktor | template editor: edytor szablonów |
| provider | dostawca | |
| prompt / model / token | prompt / model / token | |
| embedding | osadzenie (embedding) | |
| thread / chain | wątek / łańcuch | |

## 5. Decisions after the translation

- **Imperative on buttons.** The first complete translation used the infinitive on buttons and menu items
  ("Zapisać", "Usunąć zaznaczone"). Polish interfaces use the imperative, so buttons, menu items and action
  tooltips were changed to it ("Zapisz", "Usuń zaznaczone"); confirmation questions keep the infinitive, and the
  impersonal sentences stay as they are. Several extension catalogues still carry the infinitive and follow the
  same rule when they are next revised.
- **Store**: the shop tab of the administration header is "Sklep"; every other "Store" is the verb "Zapisz".
