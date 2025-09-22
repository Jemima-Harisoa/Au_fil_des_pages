function buildContractDoc(clauses, employeur = {}, employe = {}, values = {}, opts = {}) {

  // --- Fonctions internes ---
  function getByPath(obj, path) {
    if (!path || obj == null) return undefined;
    return String(path).split('.').reduce((cur, p) => (cur && typeof cur === 'object' && p in cur) ? cur[p] : undefined, obj);
  }

  function formatValue(val) {
    if (val == null) return '';
    if (Array.isArray(val)) {
      if (val.length > 0 && typeof val[0] === 'object') {
        return val.map(v => `${v.type || ''}${v.description ? ': ' + v.description : ''}`).join('\n');
      }
      return val.join(', ');
    }
    if (typeof val === 'object') {
      try { return JSON.stringify(val); } catch(e) { return String(val); }
    }
    return String(val);
  }

  function lookup(path) {
    if (!path) return undefined;
    if (path.startsWith('values.')) return getByPath(values, path.slice(7));
    if (path.startsWith('employe.')) return getByPath(employe, path.slice(8));
    if (path.startsWith('employeur.')) return getByPath(employeur, path.slice(10));
    const v = getByPath(values, path); if (v !== undefined) return v;
    const e = getByPath(employe, path); if (e !== undefined) return e;
    return getByPath(employeur, path);
  }

  function lookupWithDureeDefault(path) {
    const val = lookup(path);
    if (val === undefined || val === null || val === '') {
      if (/(^|\.)(duree|durée)$/.test(path)) return 'Indéterminée';
    }
    return val;
  }

  function fillPlaceholders(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/\{\{\s*([^}]+?)\s*\}\}/g, (_, rawKey) => {
      let key = String(rawKey || '').trim();
      if (/^(le|la)\s+/i.test(key)) key = key.replace(/^(le|la)\s+/i, '').trim();
      if (/(^|\.)(duree|durée)$/.test(key)) {
        const v = lookupWithDureeDefault(key);
        return (v === undefined || v === null || v === '') ? 'Indéterminée' : formatValue(v);
      }
      const val = lookup(key);
      return (val === undefined || val === null || val === '') ? '(à renseigner)' : formatValue(val);
    });
  }

  function renderClause(key, val) {
    const out = [];
    if (typeof val === 'string') {
      out.push({ text: fillPlaceholders(val), style: 'p', margin:[0,2,0,6] });
      return out;
    }
    if (Array.isArray(val)) {
      val.forEach(it => out.push(...renderClause(key, it)));
      return out;
    }
    if (typeof val === 'object' && val !== null) {
      Object.keys(val).forEach(k => out.push(...renderClause(k, val[k])));
      return out;
    }
    return out;
  }
  // --- FIN fonctions internes ---

  const titleText = opts.title || 'CONTRAT   DE   TRAVAIL';
  const pageMargins = opts.pageMargins || [40,60,40,60];

  const content = [];
  content.push({ text: titleText, style: 'title' });
  content.push({ text: 'Entre les contractants ci-dessous,', margin:[0,6,0,10], style:'p' });

  // --- EMPLOYEUR ---
  content.push({ text: 'EMPLOYEUR :', style:'section' });
  content.push({ text: `NOM / Dénomination : ${employeur.denomination || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Statut juridique : ${employeur.statut_juridique || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Capital social : ${employeur.capital_social || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Adresse exacte : ${employeur.adresse || '(à renseigner)'}`, style:'p' });
  content.push({ text: `N° d’identification statistique : ${employeur.identification_statique || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Représenté par M ${employeur.representant || '(à renseigner)'} en sa qualité de : ${employeur.qualite_representant || '(à renseigner)'}`, style:'p' });

  // --- EMPLOYE ---
  content.push({ text: '\nTRAVAILLEUR :', style:'section' });
  content.push({ text: `Noms et prénoms : ${employe.noms_prenoms || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Né le : ${employe.ne_le || '(à renseigner)'} à ${employe.ne_a || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Fils ou fille de : ${employe.fils_ou_fille_de || '(à renseigner)'}`, style:'p' });
  content.push({ text: `De nationalité : ${employe.nationalite || '(à renseigner)'}`, style:'p' });
  content.push({ text: `Domicile à Madagascar : ${employe.domicile || '(à renseigner)'}`, style:'p' });
  content.push({ text: 'A établi le présent contrat régi par les dispositions du Code de Travail à Madagascar.', style:'p', margin:[0,4,0,8] });

  if (clauses && typeof clauses.sous_titre === 'string') {
    content.push({ text: String(clauses.sous_titre).toUpperCase(), style:'section', margin:[0,8,0,6] });
  }

  // --- Parcours des clauses ---
  const keys = Object.keys(clauses || {});
  let art = 1;
  keys.forEach(k => {
    if (!k) return;
    if (k.startsWith('_')) return;
    if (k === 'sous_titre' || k === 'signature') return;

    // Traitement modalité
    if (k === 'modalite') {
      const debut = lookup('employe.modalite.debut_contrat') ?? lookup('modalite.debut_contrat') ?? '(à renseigner)';
      const dureeVal = lookupWithDureeDefault('employe.modalite.duree') ?? lookupWithDureeDefault('modalite.duree');
      const essaiVal = lookup('employe.modalite.essai') ?? lookup('modalite.essai') ?? '(à renseigner)';
      const text = `art.${art} : Le présent contrat prend effet à compter du ${debut}. ` +
                   (dureeVal == 0 ? `Pour une durée indéterminée. ` : `Pour une durée de ${dureeVal} mois. `) +
                   `Le travailleur accomplira une période d’essai de ${essaiVal} mois pendant laquelle le contrat peut être rompu sans préavis.`;
      content.push({ text, style:'p', margin:[0,4,0,2] });
      art++;
      return;
    }

    // Remuneration
    if (k === 'remuneration') {
      const txt = typeof clauses[k] === 'string' ? `art.${art} : ${fillPlaceholders(clauses[k])}` : null;
      if (txt) content.push({ text: txt, style:'p' });
      const avantages = getByPath(employe,'remuneration.avantages') ?? getByPath(values,'remuneration.avantages');
      if (Array.isArray(avantages) && avantages.length > 0) {
        content.push({ text: 'Avantages :', style:'p', margin:[0,4,0,4] });
        const isObjects = avantages.every(it => it && typeof it === 'object' && ('type' in it || 'description' in it));
        if (isObjects) {
          const body = [['Type','Description']];
          avantages.forEach(it => body.push([ formatValue(it.type) || '(à renseigner)', formatValue(it.description) || '' ]));
          content.push({ table:{headerRows:1, widths:['30%','*'], body}, layout:'lightHorizontalLines', margin:[0,6,0,12] });
        } else {
          content.push({ ul: avantages.map(a => formatValue(a)), margin:[0,4,0,8] });
        }
      }
      art++;
      return;
    }

    // Résiliation
    if (k === 'resilliation' && typeof clauses[k] === 'object') {
      const chosen = (employe.resilliation || getByPath(values,'type') || '').toString().toLowerCase();
      const text = (chosen && clauses[k][chosen]) ? fillPlaceholders(clauses[k][chosen])
                                                 : Object.keys(clauses[k]).map(sub => fillPlaceholders(clauses[k][sub])).join('\n');
      content.push({ text:`art.${art} : ${text}`, style:'p', margin:[0,4,0,6] });
      art++;
      return;
    }

    if (k === 'edition') return;

    // Générique
    const rendered = renderClause(k, clauses[k]);
    if (rendered.length === 1 && rendered[0].text) {
      content.push({ text:`art.${art} : ${rendered[0].text}`, style:'p', margin:[0,4,0,6] });
    } else if (rendered.length > 0) {
      const first = rendered.shift();
      content.push({ text:`art.${art} : ${first.text}`, style:'p', margin:[0,4,0,2] });
      rendered.forEach(r => content.push(r));
    } else {
      content.push({ text:`art.${art} : (à renseigner)`, style:'p', margin:[0,4,0,6] });
    }
    art++;
  });

  // Edition / signature
  if (clauses && typeof clauses.edition === 'string') {
    content.push({ text: fillPlaceholders(clauses.edition), alignment:'right', margin:[0,8,0,16], style:'p' });
  } else {
    content.push({ text: `Fait à ${getByPath(employe,'lieu_edition') || '(lieu)'} , le ${getByPath(employe,'date_edition') || '(date)'}`, alignment:'right', margin:[0,8,0,16], style:'p' });
  }

  content.push({
    columns: [
      { 
        width:'50%', 
        stack: [
          { text:'SIGNATURE DU TRAVAILLEUR', bold:true }, 
          { text: employe.signature || clauses.signature?.employe || '(nom & qualité)', margin:[0,10,0,0] },
          { text: employe.poste.qualite || '', italics:true, margin:[0,2,0,0] } // poste du représentant
        ], 
        alignment:'center' 
      },
      { 
        width:'50%', 
        stack: [
          { text:"SIGNATURE DE L'EMPLOYEUR", bold:true }, 
          { text: employeur.representant || clauses.signature?.employeur || '(nom & qualité)', margin:[0,10,0,0] },
          { text: employeur.qualite_representant || '', italics:true, margin:[0,2,0,0] } // poste du représentant
        ], 
        alignment:'center' 
      }
    ],
    columnGap: 10
  });

  return {
    pageSize:'A4',
    pageMargins,
    header: {
      columns:[
        { text: employeur.denomination || '', style:'headerLeft' },
        { text: `Adresse : ${employeur.adresse || ''}\nN° ID : ${employeur.identification_statique || ''}`, alignment:'right', fontSize:9 }
      ],
      margin:[40,10,40,0]
    },
    content,
    styles:{
      headerLeft:{ fontSize:12, bold:true },
      title:{ fontSize:18, bold:true, alignment:'center', margin:[0,4,0,10] },
      section:{ fontSize:13, bold:true, margin:[0,8,0,6] },
      p:{ fontSize:11, margin:[0,2,0,6], alignment:'justify', lineHeight:1.15 }
    },
    defaultStyle:{ fontSize:11 }
  };
}
