INSERT INTO parametre (libelle, pourcentage) VALUES
('CNAPS', 0.01),   -- 1% cotisation CNAPS
('OSTIE', 0.0125);   -- 1,25% cotisation OSTIE

INSERT INTO irsa (min, max, pourcentage) VALUES
(0, 350000, 0.00),       -- 0% jusqu'à 350 000 Ar
(350001, 400000, 5.00),  -- 5% entre 350 001 et 400 000 Ar
(400001, 500000, 10.00), -- 10% entre 400 001 et 500 000 Ar
(500001, 600000, 15.00), -- 15% entre 500 001 et 600 000 Ar
(600001, NULL, 20.00);   -- 20% au-delà de 600 000 Ar
