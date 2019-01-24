-- Create table
CREATE TABLE `recent` (
  `id` int(11) NOT NULL,
  `skill` varchar(15) NULL,
  `achievement` varchar(15),
  `time` timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Add primary key
ALTER TABLE `recent`
  ADD PRIMARY KEY (`id`);

-- Add foreign key for id
ALTER TABLE `recent`
  ADD CONSTRAINT `recent_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`);
