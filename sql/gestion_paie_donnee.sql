INSERT INTO parametre (libelle, pourcentage) VALUES
('CNAPS', 0.01),
('OSTIE', 0.05);


INSERT INTO irsa (min, max, pourcentage) VALUES
(0,       350000, 0),      -- exonéré
(350001,  400000, 0.05),      -- 5%
(400001,  500000, 0.1),     -- 10%
(500001,  600000, 0.15),     -- 15%
(600001,4000000, 0.20); -- 20% au-delà de 600 000 Ar
(4000000, NULL,0.25);   -- 20% au-delà de 600 000 Ar


INSERT INTO type_prime (libelle) VALUES
('Prime de rendement'),
('Prime d’ancienneté'),
('Prime de risque'),
('Prime de panier'),
('Prime de transport');
