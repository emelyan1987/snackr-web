SELECT 
	d.id, d.title AS dish, r.title AS restaurant, d.email AS email, t.email AS temail, t.action AS action, Distance('42.921076,129.528810', r.location) AS away, d.created_time
FROM 
	tbl_dish AS d 
LEFT JOIN 
	tbl_restaurant AS r ON r.id=d.restaurant_id 
LEFT JOIN 
	tbl_treatment AS t ON t.dish_id=d.id AND t.email='matko@asdf.com'
WHERE 
	(r.location IS NOT NULL AND r.location<>'') AND 
	Distance('50.448525,30.525089',r.location)<50 AND
	(t.id IS NULL OR t.action<>'N') AND
	ViewCount(d.id)>=500 AND
	DATEDIFF('2015-10-11', d.created_time)<=15
	 
ORDER BY 
	away ASC, d.created_time DESC