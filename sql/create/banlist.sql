-- Create table
CREATE TABLE `banlist` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Add primary key
ALTER TABLE `banlist`
  ADD PRIMARY KEY (`id`);

-- Add foreign key for id
ALTER TABLE `banlist`
  ADD CONSTRAINT `banlist_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`);
