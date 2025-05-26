# 📊 Algorytm Grafikowania Call Center

Diagram ilustrujący dwa główne etapy algorytmu grafikowania agentów Call Center.

---

## 1. Obliczanie Efektywności

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

---

## 2. Generowanie Tygodniowego Grafiku

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
| Zapisz wygenerowane zmiany |
+-----------------------------+
```

---

# 🧠 Algorytm grafikowania Call Center – Call Center Recruitment Task

## 🔁 Etapy procesu

Cały algorytm podzielony jest na dwa główne etapy:

1. **Obliczenie efektywności agentów** (`CalculateEfficiency`)
2. **Wygenerowanie grafiku** (`ScheduleGenerate`)

---

## 1. 📊 Obliczanie efektywności agentów

**Plik:** `CalculateEfficiency.php`  
**Cel:** Ustalić, jak efektywny jest każdy agent w każdej kolejce, na podstawie danych historycznych.

### Krok po kroku:

1. 📆 **Ustal okres analizy:**
    - Jeśli nie podano dat – domyślnie: od początku poprzedniego miesiąca do teraz.

2. 👥 **Pobierz agentów:**
    - Jeśli `agentIds` są puste → pobierz wszystkich agentów z repozytorium.

3. 🔁 **Dla każdego agenta:**
    - Pobierz historię połączeń (`CallHistory`) z ostatnich tygodni.
    - Przelicz efektywność za pomocą `EfficiencyCalculator`.

4. ➗ **Dla każdej kolejki agenta:**
    - Zsumuj liczbę połączeń w danych godzinach.
    - Policz średnią liczbę połączeń na godzinę.
    - Zapisz wynik jako `score` efektywności.

5. 💾 **Zapisz wyniki:**
    - Zmapuj dane do `EfficiencyCreateContract`.
    - Zapisz do repozytorium (`upsert`).

---

## 2. 🧠 Wygenerowanie grafiku

**Plik:** `ScheduleGenerate.php`  
**Cel:** Na podstawie predykcji i efektywności wygenerować optymalny grafik agentów.

### Krok po kroku:

1. 📦 **Pobierz dane:**
    - Wszystkie obliczone wcześniej efektywności agentów.
    - Predykcje zapotrzebowania (ile połączeń oczekiwanych w danym czasie i kolejce).

2. 🔁 **Dla każdej godziny i kolejki (prediction):**
    - Znajdź agentów, którzy mają przypisaną efektywność dla tej kolejki.

3. 📈 **Posortuj agentów wg efektywności:**
    - Najpierw ci, którzy mają najwyższy `score`.

4. 👨‍💻 **Dla każdego agenta:**
    - Sprawdź, czy agent ma już 8h pracy danego dnia.
    - Sprawdź, czy już 3 agentów przypisano do tej predykcji.
    - Przypisz agenta do zmiany (`ShiftCreateContract`).

5. ⛔ **Zakończ przypisywanie agentów, jeśli:**
    - Zapotrzebowanie zostało pokryte (`diffOccupancy <= 0`).

6. 💾 **Zapisz wszystkie zmiany:**
    - Użyj `shiftRepository->upsert()`.

---

## ✅ Efekt końcowy

Zoptymalizowany grafik agentów na podstawie:
- historycznych danych i efektywności,
- przewidywanego zapotrzebowania,
- ograniczeń:
    - max 8h pracy agenta dziennie,
    - max 3 agentów przypisanych do jednej predykcji (kolejka + godzina).

---

## 📌 Skrócone kroki

### I. `CalculateEfficiency`

1. Określ przedział czasu.
2. Pobierz agentów.
3. Dla każdego agenta:
    - Pobierz historię połączeń.
    - Oblicz efektywność (średnia połączeń na godzinę).
    - Zapisz wynik.

---

### II. `ScheduleGenerate`

1. Pobierz efektywności i predykcje.
2. Dla każdej predykcji:
    - Znajdź agentów o najwyższej efektywności.
    - Sprawdź limity (godzinowe i dzienne).
    - Przypisz agentów aż do pokrycia zapotrzebowania.
3. Zapisz grafik.