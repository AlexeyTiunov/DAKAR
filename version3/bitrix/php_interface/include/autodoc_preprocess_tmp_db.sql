

-- Ищем текущий ID для всех товаров во временной таблице


UPDATE b_autodoc_import_temp AS t1, b_autodoc_items_m AS t2
SET t1.CurItemID = t2.id
WHERE t1.ItemCode = t2.ItemCode
AND t1.BrandCode = t2.BrandCode
;




-- Работа с ценами

-- Ищем текущий ID цены для всех товаров во временной таблице

UPDATE b_autodoc_import_temp AS t1, b_autodoc_prices_m AS t2
SET t1.CurPriceID = t2.id
WHERE t1.ItemCode = t2.ItemCode AND t1.BrandCode = t2.BrandCode AND t1.RegionCode = t2.RegionCode
;




