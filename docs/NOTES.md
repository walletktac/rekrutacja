# Podsumowanie: Refactor + Phoenix API

## 1) Refactor architektury (SymfonyApp)

### Cel
- **Wydzielenie warstw** (kierunek **DDD**)
- Rozdzielenie na moduły: **Identity**, **Photo**, **Like**

### Wprowadzony podział
- **Domain**
- **Application**
- **Infrastructure**

### Kluczowe zmiany
- Usunięcie zależności między warstwami
    - np. **brak bezpośrednich wywołań repozytoriów w kontrolerach** → logika przeniesiona do **serwisów aplikacyjnych**
- **Kontrolery**: tylko orkiestracja `request → use-case → response`
- Repozytoria: **Infrastructure**
- Use-case’y: **Application**

### ✅ Uporządkowanie odpowiedzialności
- **GalleryService** jako use-case do budowy widoku galerii
- **LikeService** jako serwis aplikacyjny do `toggle like`
- Repozytoria w warstwie **Infrastructure**
- Kontrolery minimalne, bez logiki domenowej

### ✅ Stabilizacja bazy i migracji
- Schema **zsynchronizowana** z mappingiem
- Poprawione migracje
- Seed działa
    - zidentyfikowany problem: **duplikaty `username`**


---

### `Testy`
- Dodane testy jednostkowe dla serwisów aplikacyjnych, Myślę, że na potrzeby rekrutacji będzie ok ;)

### `Moje podsumowanie`
Jest to moja propozycja zmian w aplikacji i jest dosyć dobrym fundamentem pod przyszłą rozbudowę. 
zawsze można coś dodać/zmienić :)

Dodałbym jeszcze np. phpstan do statycznej analizy czy php-cs-fixer do automatycznego poprawiania kodu. 





