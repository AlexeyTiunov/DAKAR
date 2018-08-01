-- Обновляем Caption уже имеющихся товаров, если задан

UPDATE b_autodoc_items_m AS t1, b_autodoc_import_temp AS t2
SET t1.Caption = t2.Caption, t1.Weight = t2.Weight
WHERE t1.Id = t2.CurItemID
AND t2.Caption IS NOT NULL
AND t2.Caption <> t1.Caption
AND t1.Weight <> t2.Weight
;


-- Добавляем новый товар

INSERT INTO b_autodoc_items_m
(ItemCode,BrandCode,Caption,Weight)
SELECT ItemCode,BrandCode,Caption,Weight FROM b_autodoc_import_temp
WHERE ItemCode IS NOT NULL
AND BrandCode IS NOT NULL
AND CurItemID IS NULL
;

-- Ищем текущий ID для всех сежедобавленных товаров во временной таблице

UPDATE b_autodoc_import_temp AS t1, b_autodoc_items_m AS t2
SET t1.CurItemID = t2.id
WHERE t1.ItemCode = t2.ItemCode
AND t1.BrandCode = t2.BrandCode
AND t1.CurItemID IS NULL
;




-- Работа с ценами

-- Добавляем новые цены

INSERT INTO b_autodoc_prices_m
(ItemCode,BrandCode,RegionCode,Price, DATE_UPDATED, userID)
SELECT ItemCode,BrandCode,RegionCode,Price, Now() as DATE_UPDATED, userID FROM b_autodoc_import_temp
WHERE ItemCode IS NOT NULL
AND BrandCode IS NOT NULL
AND CurPriceID IS NULL
AND Price IS NOT NULL
;

--  Обновляем старые цены

UPDATE b_autodoc_prices_m AS t1, b_autodoc_import_temp AS t2
SET t1.Price = t2.Price, DATE_UPDATED = Now(), t1.userID = t2.userID
WHERE t1.ID = t2.CurPriceID
AND t2.CurPriceID IS NOT NULL
AND t2.Price IS NOT NULL
AND t1.Price <> t2.Price
;

-- Очищаем временную таблицу

TRUNCATE b_autodoc_import_temp ;
