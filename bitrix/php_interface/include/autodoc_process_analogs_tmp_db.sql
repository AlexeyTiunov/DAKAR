

--  грузим аналоги

INSERT INTO b_autodoc_analogs_m
(I1Code,B1Code,I2Code,B2Code)
SELECT I1Code,B1Code,I2Code,B2Code FROM b_autodoc_analogs_temp
WHERE curAnalogID IS NULL
AND isAnalogFound = 1
AND isItemFound = 1