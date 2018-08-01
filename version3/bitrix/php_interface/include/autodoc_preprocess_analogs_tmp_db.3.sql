
UPDATE b_autodoc_analogs_temp  AS t1, b_autodoc_items_m AS t2
SET t1.isAnalogFound = 1
WHERE t1.I2Code = t2.ItemCode
AND t1.B2Code = t2.BrandCode
AND t1.curAnalogID IS NULL
;