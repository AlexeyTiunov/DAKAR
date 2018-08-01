

-- Ищем текущий ID для уже имеющихся аналогов ( пара значение-аналог )


UPDATE b_autodoc_analogs_temp  AS t1, b_autodoc_analogs_m AS t2
SET t1.CurAnalogID = t2.id
WHERE t1.I1Code = t2.I1Code
AND t1.B1Code = t2.B1Code
AND t1.I2Code = t2.I2Code
AND t1.B2Code = t2.B2Code
;


-- для тех, которых нет в таблице аналогов, ищем есть ли товар и аналог в таблице товаров

UPDATE b_autodoc_analogs_temp  AS t1, b_autodoc_items_m AS t2
SET t1.isItemFound = 1
WHERE t1.I1Code = t2.ItemCode
AND t1.B1Code = t2.BrandCode
AND t1.curAnalogID IS NULL
;

UPDATE b_autodoc_analogs_temp  AS t1, b_autodoc_items_m AS t2
SET t1.isAnalogFound = 1
WHERE t1.I2Code = t2.ItemCode
AND t1.B2Code = t2.BrandCode
AND t1.curAnalogID IS NULL
;