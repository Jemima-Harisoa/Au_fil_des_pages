INSERT INTO parametre (libelle, pourcentage) VALUES
('CNAPS', 0.01),
('OSTIE', 0.01);


INSERT INTO irsa (min, max, pourcentage) VALUES
(0,       350000, 0),      -- exonéré
(350001,  400000, 0.05),      -- 5%
(400001,  500000, 0.1),     -- 10%
(500001,  600000, 0.15),     -- 15%
(600001,4000000, 0.20), -- 20% au-delà de 600 000 Ar
(4000000, NULL,0.25);   -- 20% au-delà de 600 000 Ar


INSERT INTO type_prime (libelle) VALUES
('Prime de rendement'),
('Prime d’ancienneté'),
('Prime de risque'),
('Prime de panier'),
('Prime de transport');

INSERT INTO prime (id_type_prime, pourcentage, id_employe, date_creation) VALUES
-- Directeur
(1, 0.10, 1, '2025-11-05'), -- rendement 10%
(2, 0.05, 1, '2025-11-05'), -- ancienneté 5%

-- Assistant Comptable
(1, 0.06, 2, '2025-11-06'), -- rendement 6%
(4, 0.03, 2, '2025-11-06'), -- panier 3%
(5, 0.02, 2, '2025-11-06'), -- transport 2%

-- Vendeur
(1, 0.08, 3, '2025-11-07'), -- rendement 8%
(3, 0.04, 3, '2025-11-07'), -- risque 4%
(5, 0.02, 3, '2025-11-07'), -- transport 2%

-- Magasinier
(3, 0.05, 4, '2025-11-10'), -- risque 5%
(4, 0.03, 4, '2025-11-10'), -- panier 3%

-- Assistant RH
(1, 0.07, 5, '2025-11-12'), -- rendement 7%
(2, 0.03, 5, '2025-11-12'), -- ancienneté 3%
(5, 0.02, 5, '2025-11-12'); -- transport 2%
