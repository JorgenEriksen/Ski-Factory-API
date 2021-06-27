-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 25. Mai, 2021 18:30 PM
-- Tjener-versjon: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_test`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `auth_token`
--

CREATE TABLE `auth_token` (
  `token` varchar(255) COLLATE latin1_nopad_bin NOT NULL,
  `permission` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `auth_token`
--

INSERT INTO `auth_token` (`token`, `permission`) VALUES
('4BAC27393BDD9777CE02453256C5577CD02275510B2227F473D03F533924F877', 'customer'),
('50AD41624C25E493AA1DC7F4AB32BDC5A3B0B78ECC35B539936E3FEA7C565AF7', 'production_planner'),
('7A130FDF73064886BF6F6ECBB92A1E4850E252759478107D30E2DDEF0D6D7766', 'customer_rep'),
('AD0A5EECDAD5BC4B6102A8CED84CBCA4CD664BFCFDFC65D7B53B46CB6362AD42', 'transporter'),
('CA7E3F3F3391B594650E7BA0FA4787C90BCD4A3ABE5224C50C1D255A0A67A891', 'storekeeper');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `start_date`, `end_date`) VALUES
(1, 'SkiStore', '2008-02-11', '2022-01-11'),
(2, 'Sport2', '2009-02-05', '2025-02-03'),
(3, 'Thea Johaug', '2012-04-09', '2023-05-08'),
(5, 'Gjøvik skiklubb', '1999-04-02', '2022-06-06'),
(6, 'CC Gjøvik BMAX', '2004-07-07', '2022-08-08'),
(7, 'Peder Nortug', '2006-11-11', '2024-01-01'),
(8, 'Bjørnar Dæhli', '0000-00-00', '2024-03-05'),
(9, 'Ingvild Fluxber', '2011-11-02', '2025-01-01'),
(10, 'XXL', '2018-10-03', '2022-05-01'),
(11, 'Oslo sports lager', '2020-01-01', '2023-04-04');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer_rep`
--

CREATE TABLE `customer_rep` (
  `employee_nr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `customer_rep`
--

INSERT INTO `customer_rep` (`employee_nr`) VALUES
(2),
(5),
(9),
(10);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `employee`
--

CREATE TABLE `employee` (
  `employee_nr` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `department_affiliation` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `employee`
--

INSERT INTO `employee` (`employee_nr`, `name`, `department_affiliation`) VALUES
(1, 'Kjartan Holdsbakk', 'Administration'),
(2, 'Gyrde Myredal', 'Sales'),
(3, 'Olav Løbåt', 'Production planner'),
(4, 'Nils Heiberg', 'Administration'),
(5, 'Åsmund Holien', 'Sales'),
(6, 'Filip Munch', 'Production planner'),
(7, 'Kolbjørn Boger', 'Administration'),
(8, 'Filip Gilbertsen', 'Administration'),
(9, 'Helene Eide', 'Sales'),
(10, 'Gerda Tharaldson', 'Sales'),
(11, 'Annette Mosby', 'Production planner'),
(12, 'Filip Staff', 'Production planner');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `franchises`
--

CREATE TABLE `franchises` (
  `customer_id` int(11) NOT NULL,
  `shipping_address` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `negotiated_price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `franchises`
--

INSERT INTO `franchises` (`customer_id`, `shipping_address`, `negotiated_price`) VALUES
(2, 'Norvegen 1 2821 Gjøvik', 50),
(10, 'Oslo bakken 99 1337 Oslo', 60);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `history`
--

CREATE TABLE `history` (
  `transition_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `history`
--

INSERT INTO `history` (`transition_id`, `start_date`, `end_date`) VALUES
(1, '2020-06-06', '2020-06-07'),
(2, '2020-06-07', '0000-00-00'),
(3, '2020-09-09', '2020-10-11'),
(4, '2020-10-11', '2020-10-16'),
(5, '2020-10-16', '0000-00-00'),
(6, '2021-01-11', '2021-01-14'),
(7, '2021-01-14', '2021-02-03'),
(8, '2021-02-03', '2021-02-05'),
(9, '2021-02-05', '2021-02-05'),
(10, '2021-05-25', NULL),
(11, '2021-05-25', '2021-05-25'),
(13, '2021-05-25', NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `individual_stores`
--

CREATE TABLE `individual_stores` (
  `customer_id` int(11) NOT NULL,
  `shipping_address` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `negotiated_price` int(11) DEFAULT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `can_order_independently` tinyint(1) DEFAULT NULL,
  `recive_shipments_directly` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `individual_stores`
--

INSERT INTO `individual_stores` (`customer_id`, `shipping_address`, `negotiated_price`, `franchise_id`, `can_order_independently`, `recive_shipments_directly`) VALUES
(1, 'Sorvegen 99 2821 Gjøvik', 50, 2, 1, 1),
(5, 'Gjøvikvegen 66 1010 Gjøvik', 35, 2, 1, 0),
(6, 'sesevegen 1 1013 Gjøvik', 55, 10, 0, 0),
(11, 'grønnbakken 1 1337 Oslo', 45, 10, 1, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `orders`
--

CREATE TABLE `orders` (
  `order_nr` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `orders`
--

INSERT INTO `orders` (`order_nr`, `customer_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `production_planner`
--

CREATE TABLE `production_planner` (
  `employee_nr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `production_planner`
--

INSERT INTO `production_planner` (`employee_nr`) VALUES
(3),
(6),
(11),
(12);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `production_plans`
--

CREATE TABLE `production_plans` (
  `plan_name` varchar(255) COLLATE latin1_nopad_bin NOT NULL,
  `responsible_employee_nr` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `production_plans`
--

INSERT INTO `production_plans` (`plan_name`, `responsible_employee_nr`, `start_date`, `end_date`) VALUES
('Plan for the easter', 3, '2021-05-02', '2021-05-30'),
('plan for week 44-48', 3, '2021-05-25', '2021-06-22'),
('super plan4', 3, '2021-05-23', '2021-06-20');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `production_plans_on_skiis`
--

CREATE TABLE `production_plans_on_skiis` (
  `plan_name` varchar(255) COLLATE latin1_nopad_bin NOT NULL,
  `product_id` int(11) NOT NULL,
  `number_of_skiis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `production_plans_on_skiis`
--

INSERT INTO `production_plans_on_skiis` (`plan_name`, `product_id`, `number_of_skiis`) VALUES
('Plan for the easter', 2, 20),
('plan for week 44-48', 4, 3),
('plan for week 44-48', 5, 3),
('super plan4', 1, 7),
('super plan4', 2, 8);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `shipments`
--

CREATE TABLE `shipments` (
  `shipment_nr` int(11) NOT NULL,
  `order_nr` int(11) NOT NULL,
  `transporter_name` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `franchise_name` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `shipping_address` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `pickup_date` date DEFAULT NULL,
  `state` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `shipments`
--

INSERT INTO `shipments` (`shipment_nr`, `order_nr`, `transporter_name`, `franchise_name`, `shipping_address`, `pickup_date`, `state`, `driver_id`) VALUES
(1, 3, 'Pring', 'Thea Johaug', 'teknologiveien 22 2815 gjøvik', '2021-06-06', 'picked up', 1);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `skiis`
--

CREATE TABLE `skiis` (
  `product_id` int(11) NOT NULL,
  `model` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `type_of_skiing` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `temperature` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `grip_system` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `weight_class` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `description` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `historical` bit(1) NOT NULL DEFAULT b'0',
  `photo_url` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `msrpp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `skiis`
--

INSERT INTO `skiis` (`product_id`, `model`, `type_of_skiing`, `temperature`, `grip_system`, `size`, `weight_class`, `description`, `historical`, `photo_url`, `msrpp`) VALUES
(1, 'Active', 'skate', 'warm', 'wax', 197, '60-70', 'For active persons.', b'1', 'https://i1.adis.ws/i/madshus/madshus_1920_race-speed-skate?w=412&fmt=webp&bg=white&protocol=https&dpi=72', 4500),
(2, 'Race Pro', 'classic', 'warm', 'IntelliGrip', 192, '70-80', 'for people with style.', b'1', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKmqhn2yubXLTfZDr3RQLON7a9NGKHSNu6AQ&usqp=CAU', 5600),
(3, 'Active Pro', 'double pole', 'cold', 'wax', 147, '70-80', 'not so fast, but very cool ski', b'1', 'https://sportenbeitostolen.no/content/uploads/2021/01/madshus-racingski-324x324.jpg', 8200),
(4, 'Endurance', 'skate', 'warm', 'IntelliGrip', 157, '70-80', 'Designed by famouse designers', b'1', 'https://images-ext-2.discordapp.net/external/exoBX3r5R7iZ5MCzzgIzNInYsXJfdsD8hI9OUQgoBb0/https/img.prisguiden.no/2907/2907640/original.320x257m.jpg', 8000),
(5, 'Intrasonic', 'classic', 'cold', 'IntelliGrip', 177, '60-70', 'One of the best', b'1', 'https://cdn.skatepro.com/product/440/madshus-voss-classic-cross-country-skis-ly.jpg', 8500);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `skiorders`
--

CREATE TABLE `skiorders` (
  `order_nr` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `skiis_ordered` int(11) NOT NULL,
  `skiis_ready` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `skiorders`
--

INSERT INTO `skiorders` (`order_nr`, `product_id`, `skiis_ordered`, `skiis_ready`) VALUES
(1, 1, 5, 0),
(2, 1, 6, 3),
(2, 2, 6, 4),
(3, 1, 2, 2),
(4, 3, 2, 0),
(4, 4, 4, 0),
(4, 5, 9, 0),
(5, 4, 15, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `state`
--

CREATE TABLE `state` (
  `state_name` varchar(255) COLLATE latin1_nopad_bin NOT NULL,
  `state_value` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `state`
--

INSERT INTO `state` (`state_name`, `state_value`) VALUES
('new', 1),
('open', 2),
('ready to be shipped', 4),
('shipped', 5),
('skis available', 3);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `storekeeper`
--

CREATE TABLE `storekeeper` (
  `employee_nr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `storekeeper`
--

INSERT INTO `storekeeper` (`employee_nr`) VALUES
(1),
(4),
(7),
(8),
(11),
(12);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `team_skiers`
--

CREATE TABLE `team_skiers` (
  `customer_id` int(11) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `club` varchar(255) COLLATE latin1_nopad_bin DEFAULT NULL,
  `skies_pr_year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `team_skiers`
--

INSERT INTO `team_skiers` (`customer_id`, `birth_date`, `club`, `skies_pr_year`) VALUES
(3, '1985-11-03', 'Team Norway', 2),
(7, '1979-02-03', 'Trondheim laget', 5),
(8, '1982-10-07', 'Trondheim laget', 6),
(9, '1983-11-02', 'Raske Briller', 5);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `transition`
--

CREATE TABLE `transition` (
  `transition_id` int(11) NOT NULL,
  `created_employee_nr` int(11) DEFAULT NULL,
  `state_name` varchar(255) COLLATE latin1_nopad_bin NOT NULL,
  `order_nr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `transition`
--

INSERT INTO `transition` (`transition_id`, `created_employee_nr`, `state_name`, `order_nr`) VALUES
(1, 2, 'new', 1),
(2, 2, 'open', 1),
(3, 2, 'new', 2),
(4, 2, 'open', 2),
(5, 2, 'skis available', 2),
(6, 2, 'new', 3),
(7, 2, 'open', 3),
(8, 2, 'skis available', 3),
(9, 1, 'shipped', 3),
(10, NULL, 'new', 4),
(11, NULL, 'new', 5),
(13, 2, 'open', 5);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `transporter`
--

CREATE TABLE `transporter` (
  `transporter_name` varchar(255) COLLATE latin1_nopad_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_nopad_bin;

--
-- Dataark for tabell `transporter`
--

INSERT INTO `transporter` (`transporter_name`) VALUES
('Bosten'),
('LHD'),
('Post-sør-vest'),
('Pring'),
('SPU');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_rep`
--
ALTER TABLE `customer_rep`
  ADD PRIMARY KEY (`employee_nr`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_nr`);

--
-- Indexes for table `franchises`
--
ALTER TABLE `franchises`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`transition_id`,`start_date`);

--
-- Indexes for table `individual_stores`
--
ALTER TABLE `individual_stores`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_nr`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `production_planner`
--
ALTER TABLE `production_planner`
  ADD PRIMARY KEY (`employee_nr`);

--
-- Indexes for table `production_plans`
--
ALTER TABLE `production_plans`
  ADD PRIMARY KEY (`plan_name`),
  ADD UNIQUE KEY `plan_name` (`plan_name`),
  ADD KEY `responsible_employee_nr` (`responsible_employee_nr`);

--
-- Indexes for table `production_plans_on_skiis`
--
ALTER TABLE `production_plans_on_skiis`
  ADD PRIMARY KEY (`plan_name`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`shipment_nr`),
  ADD KEY `order_nr` (`order_nr`),
  ADD KEY `transporter_name` (`transporter_name`);

--
-- Indexes for table `skiis`
--
ALTER TABLE `skiis`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `skiorders`
--
ALTER TABLE `skiorders`
  ADD PRIMARY KEY (`order_nr`,`product_id`),
  ADD KEY `ski_id` (`product_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`state_name`);

--
-- Indexes for table `storekeeper`
--
ALTER TABLE `storekeeper`
  ADD PRIMARY KEY (`employee_nr`);

--
-- Indexes for table `team_skiers`
--
ALTER TABLE `team_skiers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `transition`
--
ALTER TABLE `transition`
  ADD PRIMARY KEY (`transition_id`),
  ADD KEY `created_employee_nr` (`created_employee_nr`),
  ADD KEY `state_name` (`state_name`),
  ADD KEY `order_nr` (`order_nr`);

--
-- Indexes for table `transporter`
--
ALTER TABLE `transporter`
  ADD PRIMARY KEY (`transporter_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_nr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_nr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `shipment_nr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `skiis`
--
ALTER TABLE `skiis`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `skiorders`
--
ALTER TABLE `skiorders`
  MODIFY `order_nr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `transition`
--
ALTER TABLE `transition`
  MODIFY `transition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `customer_rep`
--
ALTER TABLE `customer_rep`
  ADD CONSTRAINT `customer_rep_ibfk_1` FOREIGN KEY (`employee_nr`) REFERENCES `employee` (`employee_nr`);

--
-- Begrensninger for tabell `franchises`
--
ALTER TABLE `franchises`
  ADD CONSTRAINT `franchises_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Begrensninger for tabell `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`transition_id`) REFERENCES `transition` (`transition_id`);

--
-- Begrensninger for tabell `individual_stores`
--
ALTER TABLE `individual_stores`
  ADD CONSTRAINT `individual_stores_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `individual_stores_ibfk_2` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`customer_id`);

--
-- Begrensninger for tabell `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Begrensninger for tabell `production_planner`
--
ALTER TABLE `production_planner`
  ADD CONSTRAINT `production_planner_ibfk_1` FOREIGN KEY (`employee_nr`) REFERENCES `employee` (`employee_nr`);

--
-- Begrensninger for tabell `production_plans`
--
ALTER TABLE `production_plans`
  ADD CONSTRAINT `production_plans_ibfk_1` FOREIGN KEY (`responsible_employee_nr`) REFERENCES `production_planner` (`employee_nr`);

--
-- Begrensninger for tabell `production_plans_on_skiis`
--
ALTER TABLE `production_plans_on_skiis`
  ADD CONSTRAINT `production_plans_on_skiis_ibfk_1` FOREIGN KEY (`plan_name`) REFERENCES `production_plans` (`plan_name`),
  ADD CONSTRAINT `production_plans_on_skiis_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `skiis` (`product_id`);

--
-- Begrensninger for tabell `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`order_nr`) REFERENCES `orders` (`order_nr`),
  ADD CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`transporter_name`) REFERENCES `transporter` (`transporter_name`);

--
-- Begrensninger for tabell `skiorders`
--
ALTER TABLE `skiorders`
  ADD CONSTRAINT `skiorders_ibfk_1` FOREIGN KEY (`order_nr`) REFERENCES `orders` (`order_nr`),
  ADD CONSTRAINT `skiorders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `skiis` (`product_id`);

--
-- Begrensninger for tabell `storekeeper`
--
ALTER TABLE `storekeeper`
  ADD CONSTRAINT `storekeeper_ibfk_1` FOREIGN KEY (`employee_nr`) REFERENCES `employee` (`employee_nr`);

--
-- Begrensninger for tabell `team_skiers`
--
ALTER TABLE `team_skiers`
  ADD CONSTRAINT `team_skiers_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Begrensninger for tabell `transition`
--
ALTER TABLE `transition`
  ADD CONSTRAINT `transition_ibfk_1` FOREIGN KEY (`created_employee_nr`) REFERENCES `employee` (`employee_nr`),
  ADD CONSTRAINT `transition_ibfk_2` FOREIGN KEY (`state_name`) REFERENCES `state` (`state_name`),
  ADD CONSTRAINT `transition_ibfk_3` FOREIGN KEY (`order_nr`) REFERENCES `orders` (`order_nr`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
