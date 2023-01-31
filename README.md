# Semestrální práce z předmetu KIV/PIA-E
- Systém pro správu projektů

## Uživatelé
- všichni mají heslo 1234
- login: jarda - Role pracovník
- login: dep - Role manažer oddělení
- login: pman1 - Role projektový manažer, Nadřízený
- login: pman2 - Role projektový manažer, pracovník
- login: kuta - Role pracovník, sekretariát


## Spuštění projektu
1. Pokud Vaše platforma není Linux, tak je projekt třeba umístit do filesystému wsl pod Windows
2. Nastavíme přístupová práva adresářům pro funkčnosti Nette Frameworku
   3. Pro jednoduchost všude 777 (opravdu pouze vývojové nastavení)
   4. chmod -R 777 log
   5. chmod -R 777 temp
3. Zapneme docker a spustíme aplikaci příkazem
   3. docker-compose up
4. Po rozběhnutí PHP serveru a databáze se dostaneme do konzole serveru pomocí příkazu
   5. docker-compose run web bash
   6. V konzoli serveru spustíme composer příkazem
      7. composer install
8. Pokud vše proběhlo v pořádku, aplikace by měla běžet na localhost

## Spouštění testů
1. Po spuštění projektu je možné pustit testy příkazem z konzole serveru stejně, jako composer
   2. Použijeme příkaz ./vendor/bin/tester . -C   (parametr -C určuje, že se bude používat php.ini z image)

- Testy jsou pro demonstraci napojeny na stejnou databázi, procházet budou proto všechny vždy jen při prvním spuštění.
- Pro další spuštění je třeba smazat celý docker kontejner (lze v Docker desktop ve windows)
