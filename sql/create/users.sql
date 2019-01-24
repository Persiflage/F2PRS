-- Create table
CREATE TABLE users (
  id int,
  rsn varchar(12),
);

-- Add primary key and unique key
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsn` (`rsn`);

  -- Add auto increment to id
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

