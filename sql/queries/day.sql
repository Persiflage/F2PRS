-- Select max value distinct by user
SELECT name, MAX(wealth) as wealth
FROM test
GROUP BY name
ORDER BY MAX(wealth) DESC;


-- Select max value distinct by user limited by timestamp
SELECT name, MAX(wealth)
FROM test_time
WHERE `time` > NOW() + INTERVAL -7 DAY
GROUP BY name
ORDER BY MAX(wealth) DESC
