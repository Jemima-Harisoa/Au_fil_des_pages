<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<!-- 
"div (recherche)(meme que CV)
div (aprecu CV):
-anarana
-sary
-postactuel" -->
    <h1>Liste des employe</h1>
    
    <!-- Filtres de recherche et tri -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Recherche généralisée">
        
        <select name="sort_name">
            <option value="">Trier par nom</option>
            <option value="asc">A-Z</option>
            <option value="desc">Z-A</option>
        </select>
        
        <select name="departement">
            <option value="">Par département</option>
            <!-- Ajouter dynamiquement les départements ici -->
            <!-- <option value="informatique">Informatique</option> -->
        </select>
        
        <select name="poste">
            <option value="">Par poste</option>
            <!-- Ajouter dynamiquement les postes ici -->
            <!-- <option value="manager">Manager</option> -->
        </select>
        
        <button type="submit">Filtrer</button>
    </form>
    

</body>
</html>