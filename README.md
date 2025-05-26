# 📊 Call Center Scheduler – Algorytm Grafikowania

Rozwiązanie zadania rekrutacyjnego polegającego na optymalnym grafikowaniu agentów Call Center, na podstawie ich efektywności oraz zapotrzebowania.

---

## 🔁 Etapy algorytmu

Cały proces podzielony został na dwa główne etapy:

1. **Obliczanie efektywności agentów** (`CalculateEfficiency`)
2. **Generowanie grafiku** (`ScheduleGenerate`)

---

## 1. 📈 Obliczanie efektywności agentów

**Plik:** `CalculateEfficiency.php`  
**Cel:** Wyliczenie średniej liczby połączeń obsługiwanych przez agenta na godzinę w ramach konkretnej kolejki.

### Algorytm – ASCII diagram

```plaintext
+--------------------------------+
|1. Oblicz Efektywność           |
+--------------------------------+
            |
            v
+---------------------------+
| Ustal zakres dat          |
+---------------------------+
            |
            v
+---------------------------+
| Pobierz agentów           |
+---------------------------+
            |
            v
+--------------------------------------+
| Dla każdego agenta:                  |
|  - Pobierz historię połączeń         |
|  - Filtruj po zakresie dat           |
|  - Zlicz połączenia na godzinę       |
|  - Oblicz średnią/h                  |
|  - Zapisz efektywność do DB          |
+--------------------------------------+
```

### Szczegóły działania:

1. 📅 **Ustalenie okresu analizy:**
   - Jeśli brak parametrów, analizowany jest okres od początku poprzedniego miesiąca do chwili obecnej.

2. 👥 **Pobranie agentów:**
   - Jeżeli `agentIds` są puste, pobierani są wszyscy agenci z repozytorium.

3. 🔄 **Przetwarzanie danych:**
   - Dla każdego agenta i jego historii połączeń:
      - Obliczana jest liczba połączeń na godzinę dla każdej kolejki.
      - Wyliczana jest średnia (`score`).
      - Wynik zapisywany jest do bazy danych.

---

## 2. 🧠 Generowanie tygodniowego grafiku

**Plik:** `ScheduleGenerate.php`  
**Cel:** Przypisanie agentów do zmian, zgodnie z efektywnością i zapotrzebowaniem na daną godzinę i kolejkę.

### Algorytm – ASCII diagram

```plaintext
+--------------------------------+
|  2. Generuj Tygodniowy Grafik  |
+--------------------------------+
            |
            v
+-------------------------------------+
| Pobierz efektywności i predykcje    |
+-------------------------------------+
            |
            v
+-------------------------------------------+
| Dla każdej predykcji:                     |
|  - Znajdź agentów dla kolejki             |
|  - Posortuj po efektywności               |
|  - Dla każdego agenta:                    |
|     - Sprawdź limit godzin (<= 8h)        |
|     - Sprawdź limit agentów (<= 3)        |
|     - Przypisz zmianę                     |
|     - Jeśli zapotrzebowanie pokryte       |
|       (diffOccupancy <= 0) → zakończ      |
+-------------------------------------------+
            |
            v
+-----------------------------+
| Zapisz wygenerowane zmiany  |
+-----------------------------+
```

### Szczegóły działania:

1. 📥 **Pobranie danych wejściowych:**
   - Wszystkie obliczone efektywności.
   - Predykcje zapotrzebowania (`Prediction`).

2. 📊 **Przetwarzanie predykcji:**
   - Dla każdej godziny i kolejki:
      - Pobierani są agenci przypisani do tej kolejki.
      - Sortowani są według efektywności malejąco.

3. 👤 **Przypisywanie agentów:**
   - Dla każdego agenta sprawdzane są ograniczenia:
      - Maks. 8 godzin pracy dziennie.
      - Maks. 3 agentów przypisanych do jednej predykcji.
   - Jeżeli zapotrzebowanie zostało pokryte (`diffOccupancy <= 0`), pętla się kończy.

4. 💾 **Zapis zmian:**
   - Wszystkie zmiany zapisywane są do repozytorium (`shiftRepository->upsert()`).

---

## ✅ Efekt końcowy

Wynikiem działania algorytmu jest zoptymalizowany tygodniowy grafik agentów, uwzględniający:

- efektywność na podstawie danych historycznych,
- przewidywane zapotrzebowanie,
- ograniczenia:
   - maksymalnie 8 godzin pracy dziennie,
   - maksymalnie 3 agentów przypisanych do jednej godziny/kolejki.

---

## 🛠️ Instalacja i uruchomienie

1. Zainstaluj **Docker** i **Docker Compose**.
2. Upewnij się, że port **80** jest odblokowany.
3. Dodaj do pliku `/etc/hosts`:

```
127.0.0.1 api.scheduler
127.0.0.1 frontend.scheduler
```

4. Uruchom skrypt:

```bash
./run
```

5. Wybierz opcję **nr 6** z menu, aby uruchomić wszystkie serwisy.

---

## 📋 Menu zarządzania (skrypt `./run`)

```plaintext
🐳 Menu zarządzania kontenerami Docker Compose
---------------------------------------------
1) Zbuduj wszystkie serwisy
2) Restartuj wszystkie serwisy
3) Wymuś przebudowę i restart
4) Zbuduj i restartuj konkretny serwis
5) Zbuduj wszystkie i restartuj
6) Uruchom wszystkie serwisy
7) Podgląd logów wszystkich serwisów
8) Podgląd logów konkretnego serwisu
9) Sprawdź status serwisów
10) Zatrzymaj i usuń wszystkie serwisy
11) Wyjście
---------------------------------------------
```

---

## 📌 Podsumowanie

Projekt zawiera:

- Kompletny backend i frontend w jednym repozytorium.
- Diagram algorytmu w formie ASCII art.
- Testy jednostkowe dla warstwy domenowej.
- Zgodność z PHPStan i PHP Code Beautifier.
- Obsługę asynchronicznego generowania grafików przez Symfony Messenger.