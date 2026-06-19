-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `proof_file` varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(10) UNSIGNED NOT NULL,
  `country` varchar(100) NOT NULL,
  `method` varchar(100) NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `method_id` varchar(255) NOT NULL,
  `type` enum('bank','momo','crypto') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region_settings`
--

CREATE TABLE `region_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `country` varchar(100) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(20) DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `convert_currency` enum('yes','no') NOT NULL DEFAULT 'no',
  `method` varchar(100) DEFAULT NULL,
  `method_name` varchar(100) DEFAULT NULL,
  `method_id` varchar(100) DEFAULT NULL,
  `method_value` varchar(255) DEFAULT NULL,
  `method_name_value` varchar(255) DEFAULT NULL,
  `method_id_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ignore_location` enum('yes','no') NOT NULL DEFAULT 'no',
  `alternate_country` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `state_allowances`
--

CREATE TABLE `state_allowances` (
  `id` int(11) NOT NULL,
  `region` varchar(50) NOT NULL,
  `state` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `state_allowances`
--

INSERT INTO `state_allowances` (`id`, `region`, `state`, `amount`, `created_at`) VALUES
(1, 'Northeast', 'Connecticut', '4000.00', '2026-06-15 06:14:15'),
(2, 'Northeast', 'Maine', '3500.00', '2026-06-15 06:14:15'),
(3, 'Northeast', 'Massachusetts', '4500.00', '2026-06-15 06:14:15'),
(4, 'Northeast', 'New Hampshire', '3500.00', '2026-06-15 06:14:15'),
(5, 'Northeast', 'Rhode Island', '3000.00', '2026-06-15 06:14:15'),
(6, 'Northeast', 'Vermont', '3000.00', '2026-06-15 06:14:15'),
(7, 'Northeast', 'New Jersey', '4500.00', '2026-06-15 06:14:15'),
(8, 'Northeast', 'New York', '6000.00', '2026-06-15 06:14:15'),
(9, 'Northeast', 'Pennsylvania', '7000.00', '2026-06-15 06:14:15'),
(10, 'Midwest', 'Illinois', '5000.00', '2026-06-15 06:14:15'),
(11, 'Midwest', 'Indiana', '4500.00', '2026-06-15 06:14:15'),
(12, 'Midwest', 'Michigan', '5500.00', '2026-06-15 06:14:15'),
(13, 'Midwest', 'Ohio', '6000.00', '2026-06-15 06:14:15'),
(14, 'Midwest', 'Wisconsin', '4500.00', '2026-06-15 06:14:15'),
(15, 'Midwest', 'Iowa', '4000.00', '2026-06-15 06:14:15'),
(16, 'Midwest', 'Kansas', '5000.00', '2026-06-15 06:14:15'),
(17, 'Midwest', 'Minnesota', '5000.00', '2026-06-15 06:14:15'),
(18, 'Midwest', 'Missouri', '5500.00', '2026-06-15 06:14:15'),
(19, 'Midwest', 'Nebraska', '4000.00', '2026-06-15 06:14:15'),
(20, 'Midwest', 'North Dakota', '8500.00', '2026-06-15 06:14:15'),
(21, 'Midwest', 'South Dakota', '5000.00', '2026-06-15 06:14:15'),
(22, 'South', 'Delaware', '3500.00', '2026-06-15 06:14:15'),
(23, 'South', 'Florida', '4500.00', '2026-06-15 06:14:15'),
(24, 'South', 'Georgia', '5000.00', '2026-06-15 06:14:15'),
(25, 'South', 'Maryland', '4500.00', '2026-06-15 06:14:15'),
(26, 'South', 'North Carolina', '5500.00', '2026-06-15 06:14:15'),
(27, 'South', 'South Carolina', '5000.00', '2026-06-15 06:14:15'),
(28, 'South', 'Virginia', '6000.00', '2026-06-15 06:14:15'),
(29, 'South', 'West Virginia', '8000.00', '2026-06-15 06:14:15'),
(30, 'South', 'Alabama', '6000.00', '2026-06-15 06:14:15'),
(31, 'South', 'Kentucky', '6500.00', '2026-06-15 06:14:15'),
(32, 'South', 'Mississippi', '7000.00', '2026-06-15 06:14:15'),
(33, 'South', 'Tennessee', '5500.00', '2026-06-15 06:14:15'),
(34, 'South', 'Arkansas', '7000.00', '2026-06-15 06:14:15'),
(35, 'South', 'Louisiana', '9000.00', '2026-06-15 06:14:15'),
(36, 'South', 'Oklahoma', '8500.00', '2026-06-15 06:14:15'),
(37, 'South', 'Texas', '10000.00', '2026-06-15 06:14:15'),
(38, 'West', 'Arizona', '6500.00', '2026-06-15 06:14:15'),
(39, 'West', 'Colorado', '7500.00', '2026-06-15 06:14:15'),
(40, 'West', 'Idaho', '5000.00', '2026-06-15 06:14:15'),
(41, 'West', 'Montana', '7000.00', '2026-06-15 06:14:15'),
(42, 'West', 'Nevada', '9000.00', '2026-06-15 06:14:15'),
(43, 'West', 'New Mexico', '8500.00', '2026-06-15 06:14:15'),
(44, 'West', 'Utah', '6500.00', '2026-06-15 06:14:15'),
(45, 'West', 'Wyoming', '9500.00', '2026-06-15 06:14:15'),
(46, 'West', 'Alaska', '10000.00', '2026-06-15 06:14:15'),
(47, 'West', 'California', '8000.00', '2026-06-15 06:14:15'),
(48, 'West', 'Hawaii', '3000.00', '2026-06-15 06:14:15'),
(49, 'West', 'Oregon', '5000.00', '2026-06-15 06:14:15'),
(50, 'West', 'Washington', '5500.00', '2026-06-15 06:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `state_claims`
--

CREATE TABLE `state_claims` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `region` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `claimed_at` timestamp NULL DEFAULT current_timestamp(),
  `code` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `is_verified` tinyint(1) DEFAULT 0 COMMENT '0=Not Verified, 1=Pending, 2=Verified',
  `status` enum('active','suspended') DEFAULT 'active',
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `state_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `verified_at` datetime DEFAULT NULL,
  `allowance_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `region` varchar(100) NOT NULL,
  `verified_method` varchar(100) DEFAULT NULL,
  `verified_account_name` varchar(255) DEFAULT NULL,
  `verified_account_id` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `method_name` varchar(255) DEFAULT NULL,
  `method_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receive_currency` varchar(20) DEFAULT NULL,
  `exchange_rate` decimal(10,4) DEFAULT NULL,
  `receive_amount` decimal(15,2) DEFAULT NULL,
  `method` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_id` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `region_settings`
--
ALTER TABLE `region_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country` (`country`),
  ADD KEY `idx_country` (`country`);

--
-- Indexes for table `state_allowances`
--
ALTER TABLE `state_allowances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `state` (`state`);

--
-- Indexes for table `state_claims`
--
ALTER TABLE `state_claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `region_settings`
--
ALTER TABLE `region_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `state_allowances`
--
ALTER TABLE `state_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `state_claims`
--
ALTER TABLE `state_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
