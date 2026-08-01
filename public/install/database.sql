--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` bigint(20) NOT NULL,
  `user_ip` varchar(255) DEFAULT NULL,
  `country_code` varchar(3) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `operating_system` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `analytics`
--

INSERT INTO `analytics` (`id`, `user_ip`, `country_code`, `country`, `operating_system`, `browser`, `date`) VALUES
(1, '192.168.1.125', '', '', '', '', 1676831400),
(2, '192.168.1.125', '', '', '', '', 1677004200),
(3, '192.168.1.114', '', '', '', '', 1677090600),
(4, '82.157.123.54', 'CN', 'China', '', '', 1677263400),
(5, '103.240.208.227', 'IN', 'India', '', '', 1677263400),
(6, '49.34.80.173', 'IN', 'India', '', '', 1677349800),
(7, '150.129.104.93', 'IN', 'India', '', '', 1677522600),
(8, '103.240.208.216', 'IN', 'India', '', '', 1677695400),
(9, '82.157.123.54', 'CN', 'China', '', '', 1677695400),
(10, '103.240.208.240', 'IN', 'India', '', '', 1677781800),
(11, '103.240.208.117', 'IN', 'India', '', '', 1678041000),
(12, '103.240.208.26', 'IN', 'India', '', '', 1678127400),
(13, '150.129.104.9', 'IN', 'India', '', '', 1678300200),
(14, '49.34.171.91', 'IN', 'India', '', '', 1678300200),
(15, '49.34.166.52', 'IN', 'India', '', '', 1678300200),
(16, '150.129.104.116', 'IN', 'India', '', '', 1678386600),
(17, '103.240.208.96', 'IN', 'India', '', '', 1678473000),
(18, '150.129.104.32', 'IN', 'India', '', '', 1678473000),
(19, '43.250.156.249', 'IN', 'India', '', '', 1678645800),
(20, '150.129.104.159', 'IN', 'India', '', '', 1678732200),
(21, '43.250.156.198', 'IN', 'India', '', '', 1678818600),
(22, '150.129.104.171', 'IN', 'India', '', '', 1678905000),
(23, '49.34.205.234', 'IN', 'India', '', '', 1678905000),
(24, '49.34.253.84', 'IN', 'India', '', '', 1679250600),
(25, '103.240.208.193', 'IN', 'India', '', '', 1679250600),
(26, '150.129.104.125', 'IN', 'India', '', '', 1679250600),
(27, '150.129.104.202', 'IN', 'India', '', '', 1679337000),
(28, '150.129.104.124', 'IN', 'India', '', '', 1679423400),
(29, '49.34.200.36', 'IN', 'India', '', '', 1679423400),
(30, '103.240.208.54', 'IN', 'India', '', '', 1679509800),
(31, '150.129.104.67', 'IN', 'India', '', '', 1679596200),
(32, '49.34.96.223', 'IN', 'India', '', '', 1679596200),
(33, '103.240.208.201', 'IN', 'India', '', '', 1679682600),
(34, '43.250.156.199', 'IN', 'India', '', '', 1679855400),
(35, '150.129.104.78', 'IN', 'India', '', '', 1679941800),
(36, '150.129.104.111', 'IN', 'India', '', '', 1680028200),
(37, '150.129.104.68', 'IN', 'India', '', '', 1680114600);

-- --------------------------------------------------------

--
-- Table structure for table `app_ads`
--

CREATE TABLE `app_ads` (
  `id` int(11) NOT NULL,
  `ads_name` varchar(255) DEFAULT NULL,
  `ads_info` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_ads`
--

INSERT INTO `app_ads` (`id`, `ads_name`, `ads_info`, `status`) VALUES
(1, 'Admob', '{\"publisher_id\":\"pub-3940256099942544\",\"banner_on_off\":\"1\",\"banner_id\":\"ca-app-pub-3940256099942544\\/6300978111\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"ca-app-pub-3940256099942544\\/1033173712\",\"interstitial_clicks\":\"5\",\"native_on_off\":\"1\",\"native_id\":\"ca-app-pub-3940256099942544\\/2247696110\",\"native_position\":\"7\"}', 0),
(2, 'StartApp', '{\"publisher_id\":\"203601319\",\"banner_on_off\":\"1\",\"banner_id\":\"\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"\",\"interstitial_clicks\":\"5\",\"native_on_off\":\"1\",\"native_id\":\"\",\"native_position\":\"7\"}', 0),
(3, 'Facebook', '{\"publisher_id\":\"\",\"banner_on_off\":\"1\",\"banner_id\":\"IMG_16_9_APP_INSTALL#288347782353524_288349185686717\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"IMG_16_9_APP_INSTALL#293685261999350_293692201998656\",\"interstitial_clicks\":\"5\",\"native_on_off\":\"1\",\"native_id\":\"IMG_16_9_APP_INSTALL#293685261999350_293692201998656\",\"native_position\":\"7\"}', 0),
(4, 'AppLovins MAX', '{\"publisher_id\":\"\",\"banner_on_off\":\"1\",\"banner_id\":\"3221a2640039c8a8\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"06b9bf27824eb7f6\",\"interstitial_clicks\":\"5\",\"native_on_off\":\"1\",\"native_id\":\"06b9bf27824eb7f6\",\"native_position\":\"7\"}', 0),
(5, 'Wortise', '{\"publisher_id\":\"a4e76baa-c4ce-4672-ba85-f2f7190bd19b\",\"banner_on_off\":\"1\",\"banner_id\":\"a2562302-14ce-476b-94d4-0c6431f1f927\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"ed6fc25c-9855-485e-9513-fed0d3acc528\",\"interstitial_clicks\":\"5\",\"native_on_off\":\"1\",\"native_id\":\"cf65ed35-4765-4955-96fc-a33cf43d5340\",\"native_position\":\"7\"}', 1),
(6, 'Unity Ads', '{\"publisher_id\":\"4613148\",\"banner_on_off\":\"1\",\"banner_id\":\"Banner_Android\",\"interstitial_on_off\":\"1\",\"interstitial_id\":\"Interstitial_Android\",\"interstitial_clicks\":\"5\"}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `info` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `info`, `image`, `facebook_url`, `instagram_url`, `youtube_url`, `website_url`, `status`) VALUES
(1, 'Abdul Kalam', '<p>Avul Pakir Jainulabdeen Abdul Kalam was an Indian scientist who served as the 11th President of India from 2002 to 2007. He was born and raised in Rameswaram, Tamil Nadu and studied physics and aerospace engineering.</p>', 'upload/images/AbdulKalam.jpeg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(2, 'Mahatma Gandhi', '<p>Mohandas Karamchand Gandhi was an Indian activist who was the leader of the Indian independence movement against British rule. Employing nonviolent civil disobedience, Gandhi led India to independence and inspired movements for civil rights and freedom across the world.</p>', 'upload/images/MahatmaGandhi.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(3, 'Jawaharlal Nehru', '<p>Pt. Jawaharlal Nehru was a freedom fighter, the first Prime Minister of India and a central figure in Indian politics before and after independence.</p>', 'upload/aa.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(4, 'Thomas Hardy', '<p>A Victorian realist in the tradition of George Eliot, he was influenced both in his novels and in his poetry by Romanticism, including the poetry of William Wordsworth.</p>', 'upload/bb.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(6, 'Verna Aardema', '<p>Ricardo Barreiro was an Argentine comic book writer.</p>', 'upload/a3.png', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(7, 'China Achebe', '<p>China Achebe was an Argentine comic book writer.</p>', 'upload/a4.png', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(8, 'Patria Aakhus', '<p>Patria Aakhus was an&nbsp; comic book writer.</p>', 'upload/a1.png', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(9, 'Ruskin Bond', '<p>Ruskin Bond was an Argentine comic book writer.</p>', 'upload/a2.png', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(10, 'Bertus Aafjes', '<p>Bertus Aafjes&nbsp; was an Argentine comic book writer.</p>', 'upload/a6.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(11, 'Jess Kidd', '<p>Jess Kidd completed her first degree in Literature with The Open University, and has since taught creative writing and gained a PhD in Creative Writing Studies.</p>', 'upload/a7.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(12, 'Martha McPhee', '<p>Martha McPhee is an American novelist whose work focuses on American social and financial mobility.</p>', 'upload/a8.png', 'https://www.facebook.com/viaviweb', '', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1),
(13, 'Megan Miranda', '<p><strong>Megan </strong>Miranda is the acclaimed author of Fragments of the Lost, The Safest Lies, Fracture, and several other novels for young adults.</p>', 'upload/a9.jpg', 'https://www.facebook.com/viaviweb', 'https://www.instagram.com/viaviwebtech', 'https://www.youtube.com/viaviwebtech', 'https://www.viaviweb.com/', 1);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `sub_cat_id` int(11) DEFAULT NULL,
  `author_ids` varchar(255) DEFAULT NULL,
  `book_access` varchar(255) NOT NULL DEFAULT 'Free',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `url_type` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `download_enable` int(1) NOT NULL DEFAULT 0,
  `book_on_rent` int(1) NOT NULL DEFAULT 0,
  `book_rent_price` float(11,2) DEFAULT NULL,
  `book_rent_time` int(3) DEFAULT NULL,
  `featured` int(1) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `cat_id`, `sub_cat_id`, `author_ids`, `book_access`, `title`, `description`, `image`, `url_type`, `url`, `download_enable`, `book_on_rent`, `book_rent_price`, `book_rent_time`, `featured`, `status`) VALUES
(1, 2, 3, '2', 'Free', 'Grave Mistake', '<p>When you pull up stakes, make sure you don&rsquo;t get stabbed in the back.</p>', 'upload/img_5.png', 'local', 'upload/files/test.epub', 1, 1, 499.00, 30, 1, 1),
(2, 2, 3, '1', 'Free', 'Dark Desire', '<p>This book was amazing and the ending will leave you wanting more.\\\" - Moohnshine\\\'s Corner Blog<br />Forgotten on her birthday.<br />Emily decided that she would ignore her family as punishment.</p>', 'upload/images/books/b11.jpg', 'local', 'upload/files/the_righteous_life.pdf', 1, 0, 5.00, 2, 1, 1),
(3, 3, NULL, '3,2', 'Free', 'The Legend Is Born', '<p>This faraway, pre-contact story begins with Helkena, a young, still untattooed girl enduring a typhoon crashing over the only island she has ever known. Her home is Wōtto, one of the dry, windswept atolls of the northern Marshall Islands.</p>', 'upload/img_4.png', 'server_url', 'https://viaviweb.in/envato/cc/demo/books/51336_Viavi-Top-5-Android-Apps-Bundle.pdf', 0, 0, 15.00, 10, 0, 1),
(4, 3, NULL, '1', 'Free', 'Trading Knives', '<p>Before the Bow of Hart, the dragon existed, waiting to rise, reaching for supremacy, grasping for servants, striking at his enemies. This epic sword and sorcery fantasy prequel reveals how a wizard gains his powers for vengeance, then threatens those opposing his chosen master.</p>', 'upload/img_3.png', 'server_url', 'https://www.viaviweb.in/envato/cc/ebook_app_new_demo/uploads/47453_3436_A-Room-with-a-View-morrison.epub', 0, 0, NULL, NULL, 0, 1),
(5, 3, NULL, '1', 'Free', 'The Black Bag', '<p>A powerless young woman bets her fate against three witches.<br />Alone against magic. Desperation grows in Coryss.<br />The young seamstress is the lone survivor of a curse. Her anger boils against those who killed her family.</p>', 'upload/img_1.png', 'server_url', 'https://www.viaviweb.in/envato/cc/ebook_app_new_demo/uploads/47453_3436_A-Room-with-a-View-morrison.epub', 0, 0, NULL, NULL, 0, 1),
(6, 3, NULL, '1', 'Free', 'The Making of a Matchmaker', '<p>n the fall of 1910, a violent patriarch with many enemies is murdered, freeing his long-suffering wife and four misfit children. Can a secret matchmaking plan find love for the eccentric members of the Tutheridge family.</p>', 'upload/img_2.png', 'server_url', 'https://www.viaviweb.in/envato/cc/ebook_app_new_demo/uploads/47453_3436_A-Room-with-a-View-morrison.epub', 0, 1, 10.00, 15, 1, 1),
(7, 2, 4, '1', 'Free', 'TK Eldridge\\\'s Series Starters', '<p>A series starter collection with the first book in SIX of my series - all under one cover, for free. Twelve-hundred pages of story!</p>', 'upload/images/books/b8.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 1, 5.00, 10, 1, 1),
(8, 1, 2, '3', 'Paid', 'The Count of Monte Cristo', '<p>A classic adventure novel, often considered Dumas\\\' best work, and frequently included on lists of the best novels of all time. Completed in 1844, and released as an 18-part series over the next two years, Dumas collaborated with other authors throughout. The story takes place in France, Italy, and the Mediterranean from the end of the rule of Napoleon I through the reign of Louis-Philippe.</p>', 'upload/images/books/b15.jpg', 'local', 'upload/files/Short Stories.pdf', 0, 0, NULL, NULL, 0, 1),
(11, 6, 7, '10', 'Free', 'Daredevils', '<p>In the prologue to the Hughes Investigations Series, Caela and Ian discover a pattern.<br />Cases that seem solitary and random, may well not be.<br />Still relatively new at the PI job, Caela takes risks she might later regret.</p>', 'upload/images/books/b14.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 0, NULL, NULL, 0, 1),
(12, 6, 7, '10,9', 'Paid', 'When Totems Fall', '<p>He believed his breakthrough AI might someday change the world. It finally has. And now 50,000 Chinese soldiers stand guard over American soil.</p>', 'upload/images/books/b13.jpg', 'server_url', 'http://www.viaviweb.in/envato/cc/ebook_app_new_demo/uploads/47453_3436_A-Room-with-a-View-morrison.epub', 1, 0, NULL, NULL, 0, 1),
(13, 6, 7, '11,4', 'Free', 'Evocatus Inception', '<p>Retired CIA Officer Woody Travis is living on his fishing boat in the Gulf of Mexico and enjoying life. He is recruited by his former CIA mentor to lead a team of mercenaries to execute the most lethal black operation in history.</p>', 'upload/images/books/b5.jpg', 'local', 'upload/files/test.epub', 1, 0, NULL, NULL, 0, 1),
(14, 6, 7, '8,6', 'Free', 'The Trials of the Core', '<p>It is home to Creatures of Legend, all-powerful gods and goddesses, lost Ancients, reclusive First Bloods, and the Guardian of the Core. All life flows in and out of the system. Should it cease, the universe ceases to exist as well. That is why it is the Guardian&rsquo;s duty to protect the system at all costs. However, his time is coming to a close and he needs to hold Trials to select his next apprentice.</p>', 'upload/images/books/b12.jpg', 'local', 'upload/files/the_righteous_life.pdf', 1, 0, NULL, NULL, 0, 1),
(15, 6, 7, '13,6', 'Free', 'No Promises', '<p>I&rsquo;m Kieran Thorne, the world&rsquo;s only living half-fae. The gig comes with a few perks&mdash;immortality, magic I can&rsquo;t control, fae assassins constantly on my heels. And the chance to protect the good and the powerless against those who are neither.</p>', 'upload/images/books/b11.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 0, NULL, NULL, 0, 1),
(16, 6, 7, '7,6', 'Free', 'The Fifth Bride of Pharaoh', '<p>Sara, princess of Nubia, must wed the all-powerful Pharaoh of Egypt. Matthaios, a slave from a distant land, is assigned to serve this foreign beauty. He doesn&rsquo;t understand why Sara should be the fifth wife of any man.</p>', 'upload/images/books/b10.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 0, NULL, NULL, 0, 1),
(17, 6, 7, '10,11', 'Free', 'Healing Her Heart', '<p>Dr. Gabe Allen has a rule about dating colleagues but when he meets ER nurse Larissa Brockman he\\\'s tempted to break his vow.</p>', 'upload/images/books/b9.jpg', 'local', 'upload/files/the_righteous_life.pdf', 1, 0, NULL, NULL, 0, 1),
(18, 6, 7, '1,4', 'Free', 'The Unveiling', '<p>12th century England: Two men vie for the throne: King Stephen the usurper and young Duke Henry the rightful heir. Amid civil and private wars, alliances are forged, loyalties are betrayed, families are divided, and marriages are made.</p>', 'upload/images/books/b8.jpg', 'local', 'upload/files/test.epub', 1, 0, NULL, NULL, 0, 1),
(19, 6, 7, '12,9', 'Free', 'Wuthering Heights', '<p>Emily Bront&euml;\\\'s only novel, this tale portrays Catherine and Heathcliff, their all-encompassing love for one another, and how this unresolved passion eventually destroys them both, leading Heathcliff to shun and abuse society.</p>', 'upload/images/books/b7.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 0, NULL, NULL, 0, 1),
(20, 6, 7, '13,9', 'Free', 'Don Quixote', '<p>Translated by John Ormsby.<br />One of the earliest novels in a modern European language, one which many people consider the finest book in the Spanish language.</p>', 'upload/images/books/b6.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 0, NULL, NULL, 0, 1),
(21, 6, 7, '10', 'Free', 'The Demon Girl', '<p>Rae Wilder has problems. Plunged into a world of dark magic, fierce creatures and ritual sacrifice, she is charged with a guarding a magical amulet.</p>', 'upload/images/books/b2.jpg', 'local', 'upload/files/the_righteous_life.pdf', 1, 0, NULL, NULL, 1, 1),
(22, 6, 6, '4', 'Free', 'Fast as the Wind', '<p>Yet another of Gould\\\'s splendid stories of love, adventure and horse racing.</p>', 'upload/images/books/b1.jpg', 'local', 'upload/files/Short Stories.pdf', 1, 1, 50.00, 10, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `category_image`, `status`) VALUES
(1, 'Biographies', 'upload/images/category/Biographies.jpg', 1),
(2, 'Computers & Tech', 'upload/images/category/Technology.jpg', 1),
(3, 'Thriller', 'upload/images/category/8.png', 1),
(5, 'Adventure', 'upload/images/category/adventure.jpg', 1),
(6, 'Romance', 'upload/images/category/Romance.jpg', 1),
(7, 'Fantasy', 'upload/images/category/Fantasy.jpg', 1),
(8, 'Traveling', 'upload/images/category/Traveling.jpg', 1),
(9, 'Art & Photography', 'upload/images/category/Art_Photography.jpg', 1),
(10, 'Comedy', 'upload/images/category/Comedy.jpg', 1),
(11, 'Mystery', 'upload/images/category/Mystery.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `continue_read`
--

CREATE TABLE `continue_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `page_num` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `continue_read`
--

INSERT INTO `continue_read` (`id`, `user_id`, `post_id`, `page_num`) VALUES
(1, 2, 1, '10'),
(3, 10, 2, '0'),
(4, 10, 1, '{\\\"bookId\\\":\\\"d9d08947-123b-447d-99b1-2a95be54d823\\\",\\\"href\\\":\\\"/titlepage.xhtml\\\",\\\"created\\\":1680082334757,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2/2)\\\"},\\\"title\\\":\\\"\\\"}'),
(5, 10, 3, '0'),
(6, 10, 4, '{\\\"bookId\\\":\\\"xxxxxxxxxxxxx\\\",\\\"href\\\":\\\"/OPS/xhtml/02_Title.xhtml\\\",\\\"created\\\":1679310013610,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2/1:0)\\\"},\\\"title\\\":\\\"\\\"}'),
(7, 10, 6, '{\\\"bookId\\\":\\\"xxxxxxxxxxxxx\\\",\\\"href\\\":\\\"/OPS/xhtml/02_Title.xhtml\\\",\\\"created\\\":1678438389224,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2/1:0)\\\"},\\\"title\\\":\\\"\\\"}'),
(8, 10, 5, '{\\\"bookId\\\":\\\"xxxxxxxxxxxxx\\\",\\\"href\\\":\\\"/OPS/xhtml/02_Title.xhtml\\\",\\\"created\\\":1678438458582,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2/1:0)\\\"},\\\"title\\\":\\\"\\\"}'),
(9, 16, 1, '{\\\"bookId\\\":\\\"d9d08947-123b-447d-99b1-2a95be54d823\\\",\\\"href\\\":\\\"/titlepage.xhtml\\\",\\\"created\\\":1678948035783,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2/2)\\\"},\\\"title\\\":\\\"\\\"}'),
(10, 10, 8, '0'),
(11, 10, 16, '0'),
(12, 17, 16, '0'),
(13, 17, 20, '1'),
(14, 18, 20, '1'),
(15, 10, 20, '0'),
(16, 20, 20, '0'),
(17, 20, 8, '4'),
(18, 20, 3, '0'),
(19, 20, 4, '{\\\"bookId\\\":\\\"xxxxxxxxxxxxx\\\",\\\"href\\\":\\\"/OPS/xhtml/03_Contents.xhtml\\\",\\\"created\\\":1680086625994,\\\"locations\\\":{\\\"cfi\\\":\\\"epubcfi(/0!/4/2/2)\\\"},\\\"title\\\":\\\"\\\"}'),
(20, 20, 19, '0');

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `home_sections`
--

CREATE TABLE `home_sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(500) NOT NULL,
  `post_type` varchar(255) DEFAULT NULL,
  `post_ids` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_sections`
--

INSERT INTO `home_sections` (`id`, `section_name`, `post_type`, `post_ids`, `status`) VALUES
(1, 'Popular Books', 'book', '1,2,3,4,6', 1),
(2, 'Best Authors', 'author', '1,2,3,4,7', 1),
(3, 'Popular Categories', 'category', '1,2,3,5,6,7', 1),
(4, 'Sub Category', 'subcategory', '1,2,3,4,6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_title` varchar(500) NOT NULL,
  `page_slug` varchar(500) NOT NULL,
  `page_content` text NOT NULL,
  `page_order` int(3) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_title`, `page_slug`, `page_content`, `page_order`, `status`) VALUES
(1, 'About Us', 'about-us', '<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \\\"de Finibus Bonorum et Malorum\\\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \\\"Lorem ipsum dolor sit amet..\\\", comes from a line in section 1.10.32.</p>', 1, 1),
(2, 'Privacy Policy', 'privacy-policy', '<h4><strong>Privacy Policy of&nbsp;<span class=\\\"highlight preview_company_name\\\">Company Name</span></strong></h4>\r\n<p><span class=\\\"highlight preview_company_name\\\">Company Name</span>&nbsp;operates the&nbsp;<span class=\\\"highlight preview_website_name\\\">Website Name</span>&nbsp;website, which provides the SERVICE.</p>\r\n<p>This page is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service, the&nbsp;<span class=\\\"highlight preview_website_name\\\">Website Name</span>&nbsp;website.</p>\r\n<p>If you choose to use our Service, then you agree to the collection and use of information in relation with this policy. The Personal Information that we collect are used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</p>\r\n<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at&nbsp;<span class=\\\"highlight preview_website_url\\\">Website URL</span>, unless otherwise defined in this Privacy Policy.</p>\r\n<h4><strong>Information Collection and Use</strong></h4>\r\n<p>For a better experience while using our Service, we may require you to provide us with certain personally identifiable information, including but not limited to your name, phone number, and postal address. The information that we collect will be used to contact or identify you.</p>\r\n<h4><strong>Log Data</strong></h4>\r\n<p>We want to inform you that whenever you visit our Service, we collect information that your browser sends to us that is called Log Data. This Log Data may include information such as your computer\\\'s Internet Protocol (&ldquo;IP&rdquo;) address, browser version, pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.</p>\r\n<h4><strong>Cookies</strong></h4>\r\n<p>Cookies are files with small amount of data that is commonly used an anonymous unique identifier. These are sent to your browser from the website that you visit and are stored on your computer\\\'s hard drive.</p>\r\n<p>Our website uses these &ldquo;cookies&rdquo; to collection information and to improve our Service. You have the option to either accept or refuse these cookies, and know when a cookie is being sent to your computer. If you choose to refuse our cookies, you may not be able to use some portions of our Service.</p>\r\n<h4><strong>Service Providers</strong></h4>\r\n<p>We may employ third-party companies and individuals due to the following reasons:</p>\r\n<ul>\r\n<li>To facilitate our Service</li>\r\n<li>To provide the Service on our behalf</li>\r\n<li>To perform Service-related services or</li>\r\n<li>To assist us in analyzing how our Service is used.</li>\r\n</ul>\r\n<p>We want to inform our Service users that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>\r\n<h4><strong>Security</strong></h4>\r\n<p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</p>\r\n<h4><strong>Links to Other Sites</strong></h4>\r\n<p>Our Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over, and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>\r\n<p>Children\\\'s Privacy</p>\r\n<p>Our Services do not address anyone under the age of 13. We do not knowingly collect personal identifiable information from children under 13. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</p>\r\n<h4><strong>Changes to This Privacy Policy</strong></h4>\r\n<p>We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately, after they are posted on this page.</p>\r\n<h4><strong>Contact Us</strong></h4>\r\n<p>If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us.</p>', 2, 1),
(3, 'Terms of use', 'terms-of-use', '<p><strong>Use of this site is provided by Demos subject to the following Terms and Conditions:</strong><br />1. Your use constitutes acceptance of these Terms and Conditions as at the date of your first use of the site.<br />2. Demos reserves the rights to change these Terms and Conditions at any time by posting changes online. Your continued use of this site after changes are posted constitutes your acceptance of this agreement as modified.<br />3. You agree to use this site only for lawful purposes, and in a manner which does not infringe the rights, or restrict, or inhibit the use and enjoyment of the site by any third party.<br />4. This site and the information, names, images, pictures, logos regarding or relating to Demos are provided &ldquo;as is&rdquo; without any representation or endorsement made and without warranty of any kind whether express or implied. In no event will Demos be liable for any damages including, without limitation, indirect or consequential damages, or any damages whatsoever arising from the use or in connection with such use or loss of use of the site, whether in contract or in negligence.<br />5. Demos does not warrant that the functions contained in the material contained in this site will be uninterrupted or error free, that defects will be corrected, or that this site or the server that makes it available are free of viruses or bugs or represents the full functionality, accuracy and reliability of the materials.<br />6. Copyright restrictions: please refer to our Creative Commons license terms governing the use of material on this site.<br />7. Demos takes no responsibility for the content of external Internet Sites.<br />8. Any communication or material that you transmit to, or post on, any public area of the site including any data, questions, comments, suggestions, or the like, is, and will be treated as, non-confidential and non-proprietary information.<br />9. If there is any conflict between these Terms and Conditions and rules and/or specific terms of use appearing on this site relating to specific material then the latter shall prevail.<br />10. These terms and conditions shall be governed and construed in accordance with the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the Courts of England and Wales.<br />11. If these Terms and Conditions are not accepted in full, the use of this site must be terminated immediately.</p>', 3, 1),
(4, 'Contact us', 'contact-us', '<p>Delete Account</p>\r\n<p>Mail me on= info@gmail.com</p>', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(6, 'kuldip.viaviweb@gmail.com', '$2y$10$NQjCzfikdyJDnCbZV/62C.vozwqfqFZgoxsFQG1wFkbSIuR0lBOzW', '2022-11-22 06:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateway`
--

CREATE TABLE `payment_gateway` (
  `id` int(11) NOT NULL,
  `gateway_name` varchar(255) NOT NULL,
  `gateway_info` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_gateway`
--

INSERT INTO `payment_gateway` (`id`, `gateway_name`, `gateway_info`, `status`) VALUES
(1, 'Paypal', '{\"mode\":\"sandbox\",\"braintree_merchant_id\":null,\"braintree_public_key\":null,\"braintree_private_key\":null,\"braintree_merchant_account_id\":null}', 0),
(2, 'Stripe', '{\"stripe_secret_key\":null,\"stripe_publishable_key\":null}', 0),
(3, 'Razorpay', '{\"razorpay_key\":null,\"razorpay_secret\":null}', 0),
(4, 'Paystack', '{\"paystack_secret_key\":null,\"paystack_public_key\":null}', 0),
(6, 'PayUMoney', '{\"mode\":\"sandbox\",\"payu_merchant_id\":null,\"payu_key\":null,\"payu_salt\":null}', 0),
(8, 'Flutterwave', '{\"flutterwave_public_key\":null,\"flutterwave_secret_key\":null,\"flutterwave_encryption_key\":null}', 0),
(10, 'CinetPay', '{\"cinetpay_api_key\":\"903676224645c928b7f5758.24192977\",\"cinetpay_secret_key\":\"193562810164d367aebab324.18559187\",\"cinetpay_site_id\":\"608738\"}', 0),
(11, 'Bank Transfer', '{\"banktransfer_info\":\"<p><strong>Account<\\/strong>: 2223330032299999<br \\/><strong>IFSC<\\/strong>: SBIN000123456<br \\/><strong>Bank Name<\\/strong>: SBI<br \\/><strong>Beneficiary Name<\\/strong>: John Deo<\\/p>\\r\\n<p><br \\/>Transfer the exact amount for the payment to be successful. Please make payment only in the account number mentioned above.<\\/p>\\r\\n<p>If you have any questions, you can contact customer support at any time.<\\/p>\"}', 0),
(12, 'SSLCOMMERZ', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `post_ratings`
--

CREATE TABLE `post_ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL,
  `rate` int(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_views`
--

CREATE TABLE `post_views` (
  `id` bigint(20) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL,
  `post_views` int(11) NOT NULL DEFAULT 0,
  `post_download` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_views`
--

INSERT INTO `post_views` (`id`, `post_id`, `post_type`, `post_views`, `post_download`, `date`) VALUES
(1, 1, 'Book', 1, 0, 1677090600),
(2, 1, 'Book', 35, 0, 1677695400),
(4, 2, 'Book', 23, 0, 1677695400),
(5, 1, 'Book', 19, 0, 1677781800),
(6, 2, 'Book', 7, 0, 1677781800),
(7, 1, 'Book', 4, 0, 1678041000),
(8, 2, 'Book', 2, 0, 1678041000),
(9, 2, 'Book', 16, 0, 1678127400),
(10, 1, 'Book', 27, 0, 1678127400),
(11, 4, 'Book', 2, 0, 1678127400),
(12, 3, 'Book', 2, 0, 1678127400),
(13, 4, 'Book', 1, 0, 1678300200),
(14, 3, 'Book', 1, 0, 1678300200),
(15, 2, 'Book', 2, 0, 1678300200),
(16, 1, 'Book', 1, 0, 1678300200),
(17, 2, 'Book', 30, 0, 1678386600),
(18, 1, 'Book', 20, 0, 1678386600),
(19, 4, 'Book', 8, 0, 1678386600),
(20, 3, 'Book', 8, 0, 1678386600),
(21, 6, 'Book', 3, 0, 1678386600),
(22, 5, 'Book', 2, 0, 1678386600),
(23, 7, 'Book', 2, 0, 1678386600),
(24, 4, 'Book', 5, 0, 1678473000),
(25, 3, 'Book', 7, 0, 1678473000),
(26, 2, 'Book', 7, 0, 1678473000),
(27, 1, 'Book', 10, 0, 1678473000),
(28, 8, 'Book', 9, 0, 1678473000),
(29, 5, 'Book', 3, 0, 1678473000),
(30, 6, 'Book', 3, 0, 1678473000),
(31, 7, 'Book', 1, 0, 1678473000),
(32, 2, 'Book', 14, 0, 1678645800),
(33, 8, 'Book', 22, 0, 1678645800),
(34, 1, 'Book', 20, 0, 1678645800),
(35, 5, 'Book', 2, 0, 1678645800),
(36, 3, 'Book', 2, 0, 1678645800),
(37, 4, 'Book', 3, 0, 1678645800),
(38, 8, 'Book', 26, 0, 1678732200),
(39, 1, 'Book', 3, 0, 1678732200),
(40, 2, 'Book', 3, 0, 1678732200),
(41, 4, 'Book', 1, 0, 1678732200),
(42, 8, 'Book', 45, 0, 1678818600),
(43, 5, 'Book', 1, 0, 1678818600),
(44, 7, 'Book', 11, 0, 1678818600),
(45, 2, 'Book', 8, 0, 1678818600),
(46, 6, 'Book', 2, 0, 1678818600),
(47, 4, 'Book', 3, 0, 1678818600),
(48, 1, 'Book', 3, 0, 1678818600),
(49, 2, 'Book', 2, 0, 1678905000),
(50, 1, 'Book', 11, 0, 1678905000),
(51, 8, 'Book', 14, 0, 1678905000),
(52, 7, 'Book', 3, 0, 1678905000),
(53, 1, 'Book', 16, 0, 1679250600),
(54, 2, 'Book', 4, 0, 1679250600),
(55, 8, 'Book', 9, 0, 1679250600),
(56, 7, 'Book', 3, 0, 1679250600),
(57, 5, 'Book', 2, 0, 1679250600),
(58, 6, 'Book', 3, 0, 1679250600),
(59, 4, 'Book', 1, 0, 1679250600),
(60, 3, 'Book', 6, 0, 1679337000),
(61, 8, 'Book', 4, 0, 1679337000),
(62, 6, 'Book', 1, 0, 1679337000),
(63, 7, 'Book', 1, 0, 1679337000),
(64, 1, 'Book', 1, 0, 1679337000),
(65, 8, 'Book', 1, 0, 1679423400),
(66, 22, 'Book', 1, 0, 1679423400),
(67, 1, 'Book', 2, 0, 1679509800),
(68, 8, 'Book', 1, 0, 1679509800),
(69, 6, 'Book', 1, 0, 1679509800),
(70, 5, 'Book', 1, 0, 1679509800),
(71, 7, 'Book', 1, 0, 1679509800),
(72, 18, 'Book', 1, 0, 1679509800),
(73, 15, 'Book', 1, 0, 1679509800),
(74, 20, 'Book', 1, 0, 1679509800),
(75, 4, 'Book', 1, 0, 1679509800),
(76, 3, 'Book', 1, 0, 1679509800),
(77, 1, 'Book', 4, 0, 1679596200),
(78, 16, 'Book', 5, 0, 1679596200),
(79, 5, 'Book', 2, 0, 1679596200),
(80, 6, 'Book', 1, 0, 1679596200),
(81, 3, 'Book', 1, 0, 1679596200),
(82, 8, 'Book', 5, 0, 1679596200),
(83, 2, 'Book', 1, 0, 1679596200),
(84, 20, 'Book', 3, 0, 1679596200),
(85, 22, 'Book', 3, 0, 1679596200),
(86, 12, 'Book', 1, 0, 1679596200),
(87, 19, 'Book', 1, 0, 1679596200),
(88, 1, 'Book', 22, 0, 1679682600),
(89, 15, 'Book', 1, 0, 1679682600),
(90, 2, 'Book', 5, 0, 1679682600),
(91, 16, 'Book', 6, 0, 1679682600),
(92, 8, 'Book', 9, 0, 1679682600),
(93, 4, 'Book', 1, 0, 1679682600),
(94, 21, 'Book', 3, 0, 1679682600),
(95, 22, 'Book', 2, 0, 1679682600),
(96, 20, 'Book', 4, 0, 1679682600),
(97, 17, 'Book', 2, 0, 1679682600),
(98, 7, 'Book', 2, 0, 1679682600),
(99, 3, 'Book', 3, 0, 1679682600),
(100, 18, 'Book', 1, 0, 1679682600),
(101, 11, 'Book', 1, 0, 1679682600),
(102, 6, 'Book', 1, 0, 1679682600),
(103, 5, 'Book', 1, 0, 1679682600),
(104, 22, 'Book', 5, 0, 1679855400),
(105, 1, 'Book', 18, 0, 1679855400),
(106, 20, 'Book', 12, 0, 1679855400),
(107, 13, 'Book', 1, 0, 1679855400),
(108, 15, 'Book', 1, 0, 1679855400),
(109, 18, 'Book', 5, 0, 1679855400),
(110, 21, 'Book', 1, 0, 1679855400),
(111, 8, 'Book', 1, 0, 1679855400),
(112, 1, 'Book', 22, 0, 1679941800),
(113, 2, 'Book', 3, 0, 1679941800),
(114, 21, 'Book', 1, 0, 1679941800),
(115, 7, 'Book', 3, 0, 1679941800),
(116, 20, 'Book', 3, 0, 1679941800),
(117, 22, 'Book', 2, 0, 1679941800),
(118, 8, 'Book', 5, 0, 1679941800),
(119, 6, 'Book', 2, 0, 1679941800),
(120, 14, 'Book', 1, 0, 1679941800),
(121, 18, 'Book', 1, 0, 1679941800),
(122, 1, 'Book', 23, 0, 1680028200),
(123, 21, 'Book', 1, 0, 1680028200),
(124, 20, 'Book', 4, 0, 1680028200),
(125, 13, 'Book', 2, 0, 1680028200),
(126, 16, 'Book', 1, 0, 1680028200),
(127, 8, 'Book', 4, 0, 1680028200),
(128, 3, 'Book', 1, 0, 1680028200),
(129, 4, 'Book', 1, 0, 1680028200),
(130, 19, 'Book', 1, 0, 1680028200),
(131, 22, 'Book', 3, 0, 1680028200),
(132, 17, 'Book', 1, 0, 1680028200),
(133, 18, 'Book', 1, 0, 1680028200),
(134, 15, 'Book', 1, 0, 1680028200),
(135, 14, 'Book', 1, 0, 1680028200),
(136, 7, 'Book', 4, 0, 1680028200),
(137, 6, 'Book', 3, 0, 1680028200);

-- --------------------------------------------------------

--
-- Table structure for table `rent_info`
--

CREATE TABLE `rent_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `rent_type` varchar(255) NOT NULL,
  `rent_date` int(11) NOT NULL,
  `rent_exp_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rent_info`
--

INSERT INTO `rent_info` (`id`, `user_id`, `rent_id`, `rent_type`, `rent_date`, `rent_exp_date`) VALUES
(1, 4, 1, 'Book', 1678946067, 1681538067),
(2, 10, 1, 'Book', 1678947441, 1681539441),
(3, 16, 1, 'Book', 1678948001, 1681540001),
(4, 16, 7, 'Book', 1678948507, 1679812507),
(5, 10, 7, 'Book', 1679300497, 1680164497),
(6, 10, 6, 'Book', 1679316244, 1680612244),
(7, 10, 22, 'Book', 1679740564, 1680604564),
(8, 20, 1, 'Book', 1680086749, 1682678749),
(9, 20, 22, 'Book', 1680086789, 1680950789);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `review_id` int(11) DEFAULT NULL,
  `post_type` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `time_zone` varchar(255) NOT NULL DEFAULT 'UTC',
  `default_language` varchar(255) NOT NULL DEFAULT 'en',
  `currency_code` varchar(255) NOT NULL DEFAULT 'USD',
  `admin_logo` varchar(255) DEFAULT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_email` varchar(255) NOT NULL,
  `app_logo` varchar(255) NOT NULL,
  `app_company` varchar(255) DEFAULT NULL,
  `app_website` varchar(255) DEFAULT NULL,
  `app_contact` varchar(255) DEFAULT NULL,
  `app_version` varchar(255) DEFAULT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(255) DEFAULT NULL,
  `smtp_email` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `smtp_encryption` varchar(255) DEFAULT NULL,
  `facebook_link` varchar(255) NOT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `google_play_link` varchar(255) DEFAULT NULL,
  `apple_store_link` varchar(255) DEFAULT NULL,
  `onesignal_app_id` varchar(255) DEFAULT NULL,
  `onesignal_rest_key` varchar(255) DEFAULT NULL,
  `app_update_hide_show` varchar(255) NOT NULL DEFAULT 'true',
  `app_update_version_code` varchar(255) DEFAULT NULL,
  `app_update_desc` varchar(255) DEFAULT NULL,
  `app_update_link` varchar(255) DEFAULT NULL,
  `app_update_cancel_option` varchar(255) NOT NULL DEFAULT 'true',
  `cat_by_name_id` varchar(255) NOT NULL DEFAULT 'category_name',
  `cat_order_by` varchar(255) NOT NULL DEFAULT 'ASC',
  `subcat_by_name_id` varchar(255) NOT NULL DEFAULT 'sub_category_name',
  `subcat_order_by` varchar(255) NOT NULL DEFAULT 'ASC',
  `author_by_name_id` varchar(255) NOT NULL DEFAULT 'name',
  `author_order_by` varchar(255) NOT NULL DEFAULT 'ASC',
  `book_by_name_id` varchar(255) NOT NULL DEFAULT 'title',
  `book_order_by` varchar(255) NOT NULL DEFAULT 'ASC',
  `pagination_limit` int(3) NOT NULL DEFAULT 10,
  `latest_limit` int(3) NOT NULL DEFAULT 10,
  `continue_read_limit` int(3) NOT NULL DEFAULT 5,
  `trending_limit` int(3) NOT NULL DEFAULT 10,
  `envato_buyer_name` varchar(255) DEFAULT NULL,
  `envato_purchase_code` varchar(255) DEFAULT NULL,
  `app_package_name` varchar(255) DEFAULT NULL,
  `netsocks_on_off` varchar(255) NOT NULL DEFAULT 'on',
  `netsocks_publisher_key` varchar(255) DEFAULT NULL,
  `netsocks_consent` varchar(255) NOT NULL DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `time_zone`, `default_language`, `currency_code`, `admin_logo`, `app_name`, `app_email`, `app_logo`, `app_company`, `app_website`, `app_contact`, `app_version`, `smtp_host`, `smtp_port`, `smtp_email`, `smtp_password`, `smtp_encryption`, `facebook_link`, `twitter_link`, `instagram_link`, `youtube_link`, `google_play_link`, `apple_store_link`, `onesignal_app_id`, `onesignal_rest_key`, `app_update_hide_show`, `app_update_version_code`, `app_update_desc`, `app_update_link`, `app_update_cancel_option`, `cat_by_name_id`, `cat_order_by`, `subcat_by_name_id`, `subcat_order_by`, `author_by_name_id`, `author_order_by`, `book_by_name_id`, `book_order_by`, `pagination_limit`, `latest_limit`, `continue_read_limit`, `trending_limit`, `envato_buyer_name`, `envato_purchase_code`, `app_package_name`, `netsocks_on_off`, `netsocks_publisher_key`, `netsocks_consent`) VALUES
(1, 'Asia/Kolkata', 'en', 'INR', 'upload/ebook_app_logo.png', 'Android E Book', 'info@viavilab.com', 'upload/app_icon.png', 'Viavi Webtech', 'www.viaviweb.com', '+91 9227777522', '1.0.0', 'smtp.gmail.com', '465', '', '', 'SSL', 'https://facebook.com', 'https://twitter.com', 'https://instagram.com', 'https://youtube.com', 'https://codecanyon.net/user/viaviwebtech/portfolio', '#ap', NULL, NULL, 'false', '1', 'Please update new app', 'https://google.com', 'true', 'category_name', 'ASC', 'sub_category_name', 'ASC', 'name', 'ASC', 'title', 'ASC', 10, 10, 10, 5, '', '', 'com.example.androidebookapps', 'on', '0E2723F4-A102-42DC-9602-AD8F7312767A', 'off');

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `slider_title` varchar(500) NOT NULL,
  `slider_image` varchar(255) NOT NULL,
  `slider_info` varchar(255) DEFAULT NULL,
  `songs_ids` text DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `slider`
--

INSERT INTO `slider` (`id`, `slider_title`, `slider_image`, `slider_info`, `songs_ids`, `status`) VALUES
(1, 'Romantic Top 40', 'upload/images/slider/charts_RomanticTop40.jpg', 'Best Romantic Songs', '1,4,7,11,8,19', 1),
(2, 'Trending Today', 'upload/images/slider/charts_TrendingToday.jpg', 'Top Trending Song for Today', '1,4,5,8,19', 1),
(3, 'Hindi Chartbusters', 'upload/images/slider/charts_HindiChartbusters.jpg', 'Hindi Chartbusters', '2,26,27,29,28,11', 1),
(4, 'Slow Romantic Hindi', 'upload/images/slider/Slow_Romantic_Hindi.jpg', 'Slow Romantic Hindi Songs', '4,25,20,14,30', 1),
(5, 'New Releases Punjabi', 'upload/images/slider/New_Releases_Panjabi.jpg', 'New Releases Punjabi', '2,27,28,18,16', 1),
(6, 'New Releases Hindi', 'upload/images/slider/New_Releases_Hindi.jpg', 'New Releases Hindi Songs 2022', '1,27,29,28,15,10,3,4,5,2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plan`
--

CREATE TABLE `subscription_plan` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_days` int(11) NOT NULL,
  `plan_duration` varchar(255) NOT NULL,
  `plan_duration_type` varchar(255) NOT NULL,
  `plan_price` decimal(11,2) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `subscription_plan`
--

INSERT INTO `subscription_plan` (`id`, `plan_name`, `plan_days`, `plan_duration`, `plan_duration_type`, `plan_price`, `status`) VALUES
(1, 'Basic Plan', 7, '7', '1', 10.00, 1),
(2, 'Premium Plan', 30, '1', '30', 29.00, 1),
(3, 'Platinum Plan', 365, '1', '365', 99.00, 1),
(4, 'Free Plan', 1, '1', '1', 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `sub_category_name` varchar(255) NOT NULL,
  `sub_category_image` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `cat_id`, `sub_category_name`, `sub_category_image`, `status`) VALUES
(1, 1, 'Historical', 'upload/images/sub_category/Historical_1.jpg', 1),
(2, 1, 'Ethnic & Cultural', 'upload/images/sub_category/Ethnic_Cultural.jpg', 1),
(3, 2, 'Computer Science', 'upload/images/sub_category/Computer_Science.jpg', 1),
(4, 2, 'Databases', 'upload/images/sub_category/Databases.jpg', 1),
(6, 6, 'Love', 'upload/images/sub_category/Love.jpg', 1),
(7, 6, 'Historical', 'upload/images/sub_category/Historical.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `suggestion`
--

CREATE TABLE `suggestion` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `rent_id` int(11) DEFAULT NULL,
  `gateway` varchar(255) NOT NULL,
  `payment_amount` varchar(255) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `usertype` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'User',
  `social_login_type` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `plan_id` int(2) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `exp_date` int(11) DEFAULT NULL,
  `plan_amount` float(11,2) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `confirmation_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `session_id` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `usertype`, `social_login_type`, `google_id`, `facebook_id`, `name`, `email`, `password`, `phone`, `user_image`, `plan_id`, `start_date`, `exp_date`, `plan_amount`, `status`, `confirmation_code`, `remember_token`, `session_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, NULL, NULL, 'Kuldip Viaviweb', 'admin@admin.com', '$2y$10$Iwxg8L0wOvfpjBzuFH1lIuhQmCzFgE10ZktkFrbFsA2Xz7NK8RtiO', '987456111', 'kuldip-viaviweb-f357cb0127763badcc188773c90aadb8-b.jpg', NULL, NULL, NULL, NULL, 1, NULL, 'PaPsOq77Om7L4xrWRgt050Ruk3sXzK2rp8E5CEO9xwJPx1Nkwd7shBr6Ot4g', 'iXL5Efee1FhfiXQzxPTe2PzEfFqU3seM3b6TzvU7', '2020-03-11 00:46:45', '2023-03-29 12:22:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_downloaded`
--

CREATE TABLE `user_downloaded` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_downloaded`
--

INSERT INTO `user_downloaded` (`id`, `user_id`, `post_id`) VALUES
(1, 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_ads`
--
ALTER TABLE `app_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `continue_read`
--
ALTER TABLE `continue_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_sections`
--
ALTER TABLE `home_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `payment_gateway`
--
ALTER TABLE `payment_gateway`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_ratings`
--
ALTER TABLE `post_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_views`
--
ALTER TABLE `post_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent_info`
--
ALTER TABLE `rent_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_plan`
--
ALTER TABLE `subscription_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suggestion`
--
ALTER TABLE `suggestion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_downloaded`
--
ALTER TABLE `user_downloaded`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `app_ads`
--
ALTER TABLE `app_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `continue_read`
--
ALTER TABLE `continue_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `favourite`
--
ALTER TABLE `favourite`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `home_sections`
--
ALTER TABLE `home_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_gateway`
--
ALTER TABLE `payment_gateway`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `post_ratings`
--
ALTER TABLE `post_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `post_views`
--
ALTER TABLE `post_views`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `rent_info`
--
ALTER TABLE `rent_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subscription_plan`
--
ALTER TABLE `subscription_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `suggestion`
--
ALTER TABLE `suggestion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_downloaded`
--
ALTER TABLE `user_downloaded`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
 
-- --------------------------------------------------------
-- Department feature: new tables + user columns
-- --------------------------------------------------------

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `colleges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `book_department` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `departments` ADD PRIMARY KEY (`id`);
ALTER TABLE `departments` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `colleges` ADD PRIMARY KEY (`id`);
ALTER TABLE `colleges` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `book_department` ADD PRIMARY KEY (`id`), ADD KEY `book_id` (`book_id`), ADD KEY `department_id` (`department_id`);
ALTER TABLE `book_department` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

-- New user columns for the registration feature
ALTER TABLE `users`
  ADD `department_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `phone`,
  ADD `college_name` varchar(255) DEFAULT NULL AFTER `department_id`,
  ADD `year` varchar(50) DEFAULT NULL AFTER `college_name`,
  ADD `gender` varchar(20) DEFAULT NULL AFTER `year`;

-- --------------------------------------------------------
-- Media feed: photos & videos (admin + user uploads)
-- --------------------------------------------------------

ALTER TABLE `users`
  ADD `is_verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `status`;

CREATE TABLE `media_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `media_type` enum('photo','video') NOT NULL DEFAULT 'photo',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `file_url` varchar(255) NOT NULL,
  `thumb_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `is_admin_upload` tinyint(1) NOT NULL DEFAULT '0',
  `show_views` tinyint(1) NOT NULL DEFAULT '1',
  `allow_likes` tinyint(1) NOT NULL DEFAULT '1',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `upload_status` varchar(20) NOT NULL DEFAULT 'pending',
  `reject_reason` varchar(500) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `media_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_upload_status` (`status`,`upload_status`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `media_type` (`media_type`);

ALTER TABLE `media_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------
-- Media likes & comments
-- --------------------------------------------------------

CREATE TABLE `media_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `media_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_user` (`post_id`,`user_id`),
  ADD KEY `post_id` (`post_id`);

ALTER TABLE `media_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `media_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `media_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `media_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------
-- Username + media notifications
-- --------------------------------------------------------

ALTER TABLE `users`
  ADD `username` varchar(255) DEFAULT NULL AFTER `name`;

CREATE TABLE `media_notifications` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `media_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `media_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
