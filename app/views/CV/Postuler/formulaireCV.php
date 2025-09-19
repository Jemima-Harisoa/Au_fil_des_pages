<?php
  $idUser = $_SESSION['utilisateur']['id_utilisateur'];
  $idAnnonce = $idAnnonce;
  $idProfil = $idProfil;
  echo "Utilisateur: " . $idUser . " Annonce: " . $idAnnonce . " || Profil: " . $idProfil;

  // Connexion via Flight (si tu as déjà configuré Flight::db())
  $db = Flight::db();

  // Préparation et exécution de la requête
  $sql = "SELECT * FROM diplomes";
  $stmt = $db->prepare($sql);
  $stmt->execute();

  // Récupération de toutes les lignes
  $diplomes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de CV - Jobseeker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fieldset-container { 
            background: #ffffff; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
            transition: all 0.3s ease; 
        }
        .fieldset-container:hover { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .preview-container { 
            background: #fafafa; 
            border: 2px solid #e5e7eb; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
            padding: 24px; 
        }
        .btn { 
            transition: all 0.3s ease; 
            transform: translateY(0); 
        }
        .btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); 
        }
        .preview ul { 
            list-style-type: disc; 
            padding-left: 24px; 
        }
        .preview li { 
            margin-bottom: 8px; 
            color: #374151; 
        }
        .input-group { 
            animation: fadeIn 0.3s ease-in-out; 
        }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        .form-control:focus { 
            outline: none; 
            border-color: #2563eb; 
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2); 
        }
        .select-custom { 
            appearance: none; 
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center; 
            background-repeat: no-repeat; 
            background-size: 1.5em 1.5em; 
            padding-right: 2.5rem; 
        }
        .file-input { 
            border: 2px dashed #d1d5db; 
            border-radius: 8px; 
            padding: 2rem; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.3s ease; 
        }
        .file-input:hover { 
            border-color: #2563eb; 
            background-color: #eff6ff; 
        }
        .file-input input[type="file"] { 
            display: none; 
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900">Créer Votre CV</h1>
            <p class="mt-2 text-lg text-gray-600">Construisez un CV professionnel pour maximiser vos chances.</p>
        </div>

        <form id="cv-form" action="/<?=$idUser?>/Annonce/<?=$idAnnonce?>/<?=$idProfil?>/fillCV/postulationCV" method="post" enctype="multipart/form-data">
            <!-- Champ caché pour combineValuesMap -->
            <input type="hidden" name="combineValuesMap" id="combineValuesMap">

            <!-- Personne - début -->
            <fieldset class="fieldset-container p-8 mb-8">
                <legend class="text-xl font-semibold text-gray-900 mb-4">Informations Personnelles</legend>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="col-span-2">
                        <label for="id_photo_identite" class="block text-sm font-medium text-gray-700 mb-2">Photo d'identité (requis)</label>
                        <div class="file-input">
                            <input type="hidden" name="MAX_FILE_SIZE">
                            <input type="file" name="photo_identite" id="id_photo_identite" required accept="image/*">
                            <p class="text-gray-500">Glissez-déposez ou cliquez pour sélectionner une photo (formats JPG, PNG)</p>
                        </div>
                    </div>
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom (requis)</label>
                        <input type="text" name="nom" id="nom" placeholder="Votre nom" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 form-control" required>
                    </div>
                    <div>
                        <label for="prenoms" class="block text-sm font-medium text-gray-700 mb-2">Prénoms (requis)</label>
                        <input type="text" name="prenoms" id="prenoms" placeholder="Vos prénoms" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 form-control" required>
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance (requis)</label>
                        <input type="date" name="date" id="date" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 form-control" required>
                    </div>
                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700 mb-2">Contact (requis)</label>
                        <input type="text" name="contact" id="contact" placeholder="Email ou téléphone" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 form-control" required>
                    </div>
                    <div>
                        <label for="id_diplome" class="block text-sm font-medium text-gray-700 mb-2">Diplôme (requis)</label>
                        <select name="id_diplome" id="id_diplome" class="select-custom mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" required>
                            <option value="" disabled selected>Sélectionnez un diplôme</option>
                            <?php if ($diplomes): ?>
                                <?php foreach ($diplomes as $diplome): ?>
                                    <option value="<?= htmlspecialchars($diplome['id_diplome']) ?>">
                                        <?= htmlspecialchars($diplome['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>Aucun diplôme disponible</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </fieldset>
            <!-- Personne - fin -->

            <!-- Profil - début -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Inputs à gauche -->
                <div class="w-full lg:w-1/2 space-y-6">
                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Compétences</legend>
                        <div class="input-container" data-fieldset="competences">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="competences[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('competences', this)" placeholder="Ex: Gestion de projet" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'competences')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'competences')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'competences')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'competences')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Skills</legend>
                        <div class="input-container" data-fieldset="skills">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="skills[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('skills', this)" placeholder="Ex: JavaScript" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'skills')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'skills')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'skills')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'skills')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Loisirs</legend>
                        <div class="input-container" data-fieldset="loisirs">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="loisirs[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('loisirs', this)" placeholder="Ex: Lecture" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'loisirs')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'loisirs')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'loisirs')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'loisirs')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Filière</legend>
                        <div class="input-container" data-fieldset="filiere">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="filiere[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('filiere', this)" placeholder="Ex: Informatique" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'filiere')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'filiere')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'filiere')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'filiere')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Expérience Professionnelle</legend>
                        <div class="input-container" data-fieldset="experience-pro">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="experience_pro[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('experience-pro', this)" placeholder="Ex: Développeur chez XYZ" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'experience-pro')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'experience-pro')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'experience-pro')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'experience-pro')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Certifications</legend>
                        <div class="input-container" data-fieldset="certification">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="certification[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('certification', this)" placeholder="Ex: AWS Certified" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'certification')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'certification')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'certification')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'certification')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset-container p-6">
                        <legend class="text-lg font-semibold text-gray-900">Langues</legend>
                        <div class="input-container" data-fieldset="langues">
                            <div class="input-group flex items-center mb-4">
                                <input type="text" name="langues[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600" oninput="updateList('langues', this)" placeholder="Ex: Français (C1)" required>
                                <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, 'langues')">+</button>
                                <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, 'langues')" disabled>−</button>
                                <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, 'langues')" disabled>↑</button>
                                <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, 'langues')" disabled>↓</button>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Aperçu à droite -->
                <div class="w-full lg:w-1/2 preview-container">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Aperçu du CV</h2>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Compétences</h3>
                        <ul id="list-competences" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Skills</h3>
                        <ul id="list-skills" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Loisirs</h3>
                        <ul id="list-loisirs" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Filière</h3>
                        <ul id="list-filiere" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Expérience Professionnelle</h3>
                        <ul id="list-experience-pro" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Certifications</h3>
                        <ul id="list-certification" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                    <div class="preview mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Langues</h3>
                        <ul id="list-langues" class="text-gray-600">
                            <li class="italic text-gray-500">(Vide)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Profil - fin -->

            <div class="mt-8 text-center">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 btn text-lg font-semibold">Postuler</button>
            </div>
        </form>
    </div>

    <script>
    // Tableau global pour stocker les chaînes combinées
    const combineValuesMap = {
      competences: '',
      skills: '',
      loisirs: '',
      filiere: '',
      'experience-pro': '',
      certification: '',
      langues: ''
    };

    function addInput(button, fieldsetId) {
      const container = button.closest('.input-container');
      const newInputGroup = document.createElement('div');
      newInputGroup.className = 'input-group flex items-center mb-4';
      newInputGroup.innerHTML = `
        <input type="text" name="${fieldsetId}[]" class="form-control flex-1 mr-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-600 focus:border-blue-600 additional-input" oninput="updateList('${fieldsetId}', this)" placeholder="Ajouter une entrée">
        <button type="button" class="add-btn bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 btn" onclick="addInput(this, '${fieldsetId}')">+</button>
        <button type="button" class="remove-btn bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 btn ml-2" onclick="removeInput(this, '${fieldsetId}')">−</button>
        <button type="button" class="up-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputUp(this, '${fieldsetId}')">↑</button>
        <button type="button" class="down-btn bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 btn ml-2" onclick="moveInputDown(this, '${fieldsetId}')">↓</button>
      `;
      container.appendChild(newInputGroup);
      updateButtons(fieldsetId);
      updateList(fieldsetId);
    }

    function removeInput(button, fieldsetId) {
      button.parentElement.remove();
      updateButtons(fieldsetId);
      updateList(fieldsetId);
    }

    function moveInputUp(button, fieldsetId) {
      const inputGroup = button.parentElement;
      const previous = inputGroup.previousElementSibling;
      if (previous) {
        inputGroup.parentElement.insertBefore(inputGroup, previous);
      }
      updateButtons(fieldsetId);
      updateList(fieldsetId);
    }

    function moveInputDown(button, fieldsetId) {
      const inputGroup = button.parentElement;
      const next = inputGroup.nextElementSibling;
      if (next) {
        inputGroup.parentElement.insertBefore(next, inputGroup);
      }
      updateButtons(fieldsetId);
      updateList(fieldsetId);
    }

    function updateButtons(fieldsetId) {
      const container = document.querySelector(`.input-container[data-fieldset="${fieldsetId}"]`);
      const inputGroups = container.querySelectorAll('.input-group');
      inputGroups.forEach((group, index) => {
        const removeBtn = group.querySelector('.remove-btn');
        const upBtn = group.querySelector('.up-btn');
        const downBtn = group.querySelector('.down-btn');
        removeBtn.disabled = inputGroups.length === 1;
        upBtn.disabled = index === 0;
        downBtn.disabled = index === inputGroups.length - 1;
      });
    }

    function updateList(fieldsetId, inputElement = null) {
      const container = document.querySelector(`.input-container[data-fieldset="${fieldsetId}"]`);
      const inputs = container.querySelectorAll('.form-control');
      const list = document.getElementById(`list-${fieldsetId}`);
      list.innerHTML = '';
      const values = Array.from(inputs).map(input => input.value).filter(val => val.trim() !== '');

      // Afficher les valeurs dans la console
      console.log(`${fieldsetId.charAt(0).toUpperCase() + fieldsetId.slice(1).replace('-', ' ')}:`);
      values.forEach((value, index) => {
        console.log(`Input ${index + 1}: ${value}`);
      });

      // Combiner les valeurs et stocker dans combineValuesMap
      const combinedValues = values.join(' || ');
      combineValuesMap[fieldsetId] = combinedValues || '';
      console.log(`${fieldsetId}_combiner: "${combinedValues || '(Vide)'}"`);

      // Supprimer les inputs supplémentaires vides
      if (inputElement && inputElement.classList.contains('additional-input') && inputElement.value.trim() === '') {
        const inputGroup = inputElement.closest('.input-group');
        if (inputGroup && container.querySelectorAll('.input-group').length > 1) {
          inputGroup.remove();
        }
      }

      // Mettre à jour la liste d'aperçu
      if (values.length === 0) {
        list.innerHTML = '<li class="italic text-gray-500">(Vide)</li>';
      } else {
        values.forEach(value => {
          const li = document.createElement('li');
          li.textContent = value;
          list.appendChild(li);
        });
      }

      // Mettre à jour le champ caché combineValuesMap
      updateCombineValuesMap();
      updateButtons(fieldsetId);
    }

    function updateCombineValuesMap() {
      const combineValuesMapInput = document.getElementById('combineValuesMap');
      combineValuesMapInput.value = JSON.stringify(combineValuesMap);
      console.log('combineValuesMap updated:', combineValuesMapInput.value);
    }

    // Ajout d'un événement pour l'upload d'image
    document.querySelector('.file-input').addEventListener('click', () => {
      document.getElementById('id_photo_identite').click();
    });
    </script>
</body>
</html>