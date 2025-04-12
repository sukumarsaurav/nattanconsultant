-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 12, 2025 at 04:26 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u911550082_nattan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` enum('admin','consultant','staff') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `profile_image`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$qTSsA9kSXR4oMSP3InPKz.XcPArkpkY2VtXqjUm2wdWQGZT2Xd5Xy', 'Admin', 'User', 'admin@canext.com', NULL, NULL, 'admin', 'active', NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `consultation_type` varchar(50) NOT NULL,
  `appointment_datetime` datetime NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `immigration_purpose` varchar(100) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled','no-show') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `consultation_type`, `appointment_datetime`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `postal_code`, `country`, `immigration_purpose`, `special_requests`, `status`, `payment_status`, `payment_amount`, `additional_notes`, `created_at`, `updated_at`) VALUES
(1, 'Video Consultation', '2023-05-15 10:00:00', 'John', 'Smith', 'john.smith@example.com', '+1 (123) 456-7890', NULL, NULL, NULL, 'USA', 'Express Entry', NULL, 'confirmed', 'paid', 150.00, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(2, 'In-Person Consultation', '2023-05-14 14:00:00', 'Maria', 'Rodriguez', 'maria.r@example.com', '+1 (234) 567-8901', NULL, NULL, NULL, 'Mexico', 'Study Permit', NULL, 'completed', 'paid', 200.00, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(3, 'Phone Consultation', '2023-05-14 11:30:00', 'David', 'Chen', 'david.chen@example.com', '+1 (345) 678-9012', NULL, NULL, NULL, 'China', 'Work Permit', NULL, 'cancelled', 'refunded', 120.00, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(4, 'Video Consultation', '2023-05-13 15:00:00', 'Sarah', 'Johnson', 'sarah.j@example.com', '+1 (456) 789-0123', NULL, NULL, NULL, 'Canada', 'Family Sponsorship', NULL, 'completed', 'paid', 150.00, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(5, 'In-Person Consultation', '2023-05-13 10:00:00', 'Raj', 'Patel', 'raj.patel@example.com', '+1 (567) 890-1234', NULL, NULL, NULL, 'India', 'Business Immigration', NULL, 'confirmed', 'paid', 200.00, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(6, 'Video Consultation', '2025-04-05 10:00:00', 'Sukumar', 'saurav', 'sukumarsaurav@gmail.com', '09991289245', NULL, NULL, NULL, NULL, 'study_permit', 'test', 'pending', 'unpaid', 150.00, NULL, '2025-04-03 04:43:16', '2025-04-03 04:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `availability_schedule`
--

CREATE TABLE `availability_schedule` (
  `id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-newspaper',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `post_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `icon`, `display_order`, `post_count`, `created_at`, `updated_at`) VALUES
(1, 'Immigration News', 'immigration-news', 'fas fa-newspaper', 1, 4, '2025-04-03 07:03:07', '2025-04-09 14:40:06'),
(2, 'Success Stories', 'success-stories', 'fas fa-star', 2, 3, '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(3, 'Provincial Programs', 'provincial-programs', 'fas fa-map-marker-alt', 3, 3, '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(4, 'Study in Canada', 'study-in-canada', 'fas fa-graduation-cap', 4, 3, '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(5, 'Work in Canada', 'work-in-canada', 'fas fa-briefcase', 5, 3, '2025-04-03 07:03:07', '2025-04-03 07:03:07');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'CANEXT Team',
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `author`, `status`, `publish_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Canada to Welcome 500,000 New Immigrants Annually by 2025', 'canada-to-welcome-500000-immigrants-annually', 'The Canadian government has announced plans to significantly increase immigration targets over the next three years.', '<p>The Canadian government has announced a significant increase in immigration targets for the coming years, aiming to welcome 500,000 new permanent residents annually by 2025. This ambitious plan represents one of the largest immigration initiatives in Canadian history.</p><p>Immigration Minister Sean Fraser unveiled the new Immigration Levels Plan, which outlines a strategy to address critical labor shortages and support Canada\'s post-pandemic economic recovery. The plan includes:</p><ul><li>465,000 new permanent residents in 2023</li><li>485,000 new permanent residents in 2024</li><li>500,000 new permanent residents in 2025</li></ul><p>\"Immigration is critical to ensuring Canada remains a growing, prosperous country with a strong healthcare system, well-supported elderly population, and robust workforce across all sectors,\" said Minister Fraser at a press conference.</p><p>The plan focuses on economic immigration programs, including Express Entry and the Provincial Nominee Program, which will account for approximately 60% of new admissions. Family reunification programs will also see increased targets, allowing more Canadians to reunite with their loved ones.</p><p>This announcement comes as Canada faces significant workforce challenges, with nearly one million job vacancies across the country and an aging population creating additional labor market pressures.</p><p>For prospective immigrants, this represents an excellent opportunity to realize their Canadian dreams. However, increased application volumes may also mean more competition for popular immigration programs.</p>', '', 'Maria Rodriguez', 'published', '2025-04-01 07:03:00', '2025-04-03 07:03:07', '2025-04-03 13:01:33'),
(2, 1, 'Express Entry: CRS Score Drops to 475 in Latest Draw', 'express-entry-crs-score-drops', 'The latest Express Entry draw saw the CRS score requirement drop to its lowest point in six months.', '<p>In the latest Express Entry draw held on October 11, 2023, Immigration, Refugees and Citizenship Canada (IRCC) invited 3,500 candidates to apply for permanent residence with Comprehensive Ranking System (CRS) scores as low as 475.</p><p>This represents a significant drop from previous draws, where the CRS score cutoff had been hovering around 500 points. The decrease makes Canadian immigration more accessible to a wider pool of candidates.</p><p>Key details from the latest draw:</p><ul><li>Number of invitations issued: 3,500</li><li>Minimum CRS score requirement: 475</li><li>Tie-breaking rule: September 29, 2023, at 11:42:58 UTC</li></ul><p>The draw included candidates from all Express Entry programs, including the Federal Skilled Worker Program, Federal Skilled Trades Program, and Canadian Experience Class.</p><p>This drop in CRS scores may be attributed to the Canadian government\'s commitment to increasing immigration targets. As announced in the latest Immigration Levels Plan, Canada aims to welcome 500,000 new permanent residents annually by 2025, with a significant portion coming through economic immigration programs like Express Entry.</p><p>For prospective applicants, this trend suggests that now may be an opportune time to submit an Express Entry profile. Even candidates with moderate CRS scores may have improved chances of receiving an invitation to apply in the coming months.</p><p>Keep in mind that there are many ways to improve your CRS score, including obtaining additional education credentials, securing a job offer in Canada, improving language test scores, or gaining additional work experience.</p>', 'images/blog/1744208776_AI-healthcare-medical-analysis.svg', 'David Thompson', 'published', '2025-03-29 07:03:00', '2025-04-03 07:03:07', '2025-04-09 14:26:15'),
(3, 1, 'New Pathways for International Students to Obtain Permanent Residence', 'new-pathways-international-students', 'Canada introduces new immigration pathways designed specifically for international students.', '<p>Immigration, Refugees and Citizenship Canada (IRCC) has announced new immigration pathways designed to help international students transition to permanent residence after completing their studies in Canada.</p><p>The new initiatives recognize the significant contributions international students make to Canada\'s economy, education system, and communities. They also acknowledge that international graduates often have the Canadian education, language proficiency, and work experience that help them integrate quickly into the Canadian labor market.</p><p>Key features of the new pathways include:</p><ol><li><strong>Extended Post-Graduation Work Permits (PGWP)</strong> - Eligible graduates can now apply for work permits valid for up to three years, regardless of the length of their study program.</li><li><strong>Canadian Experience Class adjustments</strong> - Reduced work experience requirements for international graduates applying through Express Entry\'s Canadian Experience Class.</li><li><strong>Provincial Nominee Program options</strong> - New international graduate streams in several Provincial Nominee Programs with simplified requirements.</li></ol><p>To qualify for these new pathways, international students must:</p><ul><li>Have graduated from a Designated Learning Institution (DLI)</li><li>Have completed a program of at least 8 months in duration</li><li>Have maintained valid immigration status during their studies</li></ul><p>International students currently account for approximately 25% of all new permanent residents to Canada, highlighting their importance to Canada\'s immigration strategy and future workforce.</p><p>\"International students bring tremendous economic, cultural and social benefits to communities throughout Canada,\" said Immigration Minister Sean Fraser. \"These new pathways will help more international graduates build their future in Canada, while addressing our labor market needs.\"</p>', 'images/blog/immigration-news-3.jpg', 'Sarah Johnson', 'published', '2025-03-24 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(4, 2, 'From Student to Citizen: Michael&#039;s Journey to Canada', 'from-student-to-citizen-michaels-journey', 'Michael came to Canada as an international student and is now a proud Canadian citizen. Read his inspiring story.', '<p>When Michael Okoye arrived in Toronto as an international student in 2015, he had no idea that eight years later, he would be a successful software engineer and Canadian citizen. His journey is a testament to determination, strategic planning, and making the most of Canada\'s immigration pathways.</p><h3>The Beginning: Studying in Canada</h3><p>Michael came to Canada from Nigeria to pursue a Master\'s degree in Computer Science at the University of Toronto. \"The education system in Canada has an excellent reputation worldwide, and I wanted to experience it firsthand,\" Michael explains. \"Plus, I knew that Canada offered good immigration pathways for international graduates.\"</p><p>While studying, Michael faced the typical challenges of adapting to a new country: adjusting to the cold Canadian winters, making new friends, and managing his finances carefully to make his student budget stretch.</p><h3>Building Canadian Experience</h3><p>After graduating, Michael applied for a Post-Graduation Work Permit (PGWP) and secured a position as a junior developer at a tech startup in Toronto\'s growing tech scene. \"That first job was crucial. It allowed me to gain Canadian work experience, which I knew would be important for my permanent residence application.\"</p><p>Over the next two years, Michael worked diligently, improving his skills and taking on more responsibilities. Eventually, he moved to a larger company where he worked as a software engineer.</p><h3>The Path to Permanent Residence</h3><p>With his Canadian education and work experience, Michael was well-positioned to apply for permanent residence through Express Entry\'s Canadian Experience Class. \"I made sure to maintain high language test scores and continued to gain relevant work experience. When I submitted my Express Entry profile, I had a competitive CRS score of 478.\"</p><p>Just three months after submitting his profile, Michael received an Invitation to Apply for permanent residence. \"That was one of the happiest days of my life,\" he recalls. \"I knew that my dream of building a life in Canada was becoming reality.\"</p><h3>Canadian Citizenship and Beyond</h3><p>After being a permanent resident for the required period, Michael applied for and received his Canadian citizenship earlier this year. He now works as a senior software engineer and has bought his first home in Toronto.</p><p>\"Looking back at my journey, I\'m grateful for the opportunities Canada has provided me. It hasn\'t always been easy, but the pathway from student to citizen was clear. With hard work and planning, it was achievable.\"</p><p>Michael\'s advice to others considering a similar path: \"Do your research, understand the immigration requirements early, and make strategic decisions about your education and career. Canada truly welcomes immigrants who are willing to contribute to society.\"</p>', 'images/blog/1744208764_AI-Powered-Customer-Support.svg', 'Michael Okoye', 'published', '2025-03-31 07:03:00', '2025-04-03 07:03:07', '2025-04-09 14:26:03'),
(5, 2, 'Family\'s Provincial Nominee Success Story', 'family-provincial-nominee-success', 'How the Patel family successfully immigrated to Manitoba through the Provincial Nominee Program.', '<p>For the Patel family, the dream of calling Canada home became a reality through Manitoba\'s Provincial Nominee Program (MPNP). Their story highlights how provincial immigration pathways can provide excellent opportunities for families looking to build a new life in Canada.</p><h3>The Decision to Immigrate</h3><p>Raj and Priya Patel lived in Gujarat, India, where Raj worked as a mechanical engineer and Priya as a healthcare administrator. With two young children, they began considering immigration for better education opportunities and quality of life.</p><p>\"We researched several countries, but Canada stood out for its welcoming attitude toward immigrants and strong social support systems,\" says Raj. \"The challenge was finding the right immigration pathway that would work for our family situation.\"</p><h3>Discovering the Manitoba Provincial Nominee Program</h3><p>While most prospective immigrants focus on Express Entry, the Patels\' immigration consultant suggested they consider the Manitoba Provincial Nominee Program. Manitoba was actively recruiting skilled workers in Raj\'s field, and the province offered an attractive combination of job opportunities, affordable housing, and strong communities.</p><p>\"We hadn\'t originally considered Manitoba,\" admits Priya. \"But when we researched the province, we discovered it offered everything we were looking for, including excellent schools for our children and a growing economy.\"</p><h3>The Application Process</h3><p>The family applied through the MPNP Skilled Worker Overseas stream, which doesn\'t require a job offer but does look for connections to the province. The Patels had a close friend who had lived in Winnipeg for five years, which helped establish their connection to Manitoba.</p><p>The process involved several steps:</p><ol><li>Submitting an Expression of Interest to the MPNP</li><li>Receiving an invitation to apply</li><li>Submitting a complete application with documentation of education, work experience, and language proficiency</li><li>Receiving a provincial nomination</li><li>Applying to IRCC for permanent residence</li></ol><p>\"The process was detailed, but straightforward,\" says Raj. \"We made sure to provide thorough documentation for every claim in our application.\"</p><h3>Moving to Manitoba</h3><p>Nine months after submitting their initial Expression of Interest, the Patels received their confirmation of permanent residence. They arrived in Winnipeg in September 2021.</p><p>\"The community welcome we received was incredible,\" Priya shares. \"From our first day, we had support from settlement organizations, community members, and other immigrants who had been through the same experience.\"</p><p>Raj found employment as a mechanical engineer within three months, while Priya is currently taking certification courses to continue her healthcare career in Canada.</p><p>\"Our children have adapted beautifully to their new schools, and we\'ve already bought a home in a lovely neighborhood,\" says Priya. \"Manitoba has truly become home for us.\"</p><h3>Advice for Other Families</h3><p>The Patels encourage other families to look beyond the most well-known immigration programs and consider provincial nominations. \"Each province has unique opportunities and needs,\" advises Raj. \"Find the one that matches your skills and situation, and you may discover a smoother path to Canadian permanent residence.\"</p>', 'images/blog/success-story-2.jpg', 'Priya Patel', 'published', '2025-03-26 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(6, 2, 'From Temporary Worker to Business Owner: Elena\'s Immigration Success', 'temporary-worker-business-owner-success', 'How Elena used Canada\'s immigration pathways to transition from a temporary foreign worker to a successful business owner.', '<p>Elena Vasquez first came to Canada from the Philippines as a temporary foreign worker in 2018. Today, she owns a thriving small business in Halifax, Nova Scotia, and is a permanent resident on the path to citizenship. Her journey showcases how Canada\'s immigration system can provide opportunities for entrepreneurial immigrants.</p><h3>Starting as a Temporary Worker</h3><p>Elena originally arrived in Canada under the Temporary Foreign Worker Program, hired as a food service supervisor at a restaurant in Halifax. \"It was supposed to be just a two-year contract,\" Elena explains. \"I came to earn money to support my family back home. I never imagined I would end up building a permanent life here.\"</p><p>During her time as a temporary worker, Elena worked diligently, learned about Canadian business practices, and became an essential employee at the restaurant. She also improved her English skills and built a network in the local community.</p><h3>Identifying an Opportunity</h3><p>After two years in Canada, Elena noticed a gap in the local market. \"There were very few authentic Filipino food options in Halifax, despite a growing Filipino community,\" she says. \"I saw an opportunity to introduce the flavors of my homeland to my new community.\"</p><p>With encouragement from her employer and local business advisors, Elena began exploring what it would take to start her own restaurant. She also began researching immigration options that would allow her to stay in Canada permanently.</p><h3>Provincial Nomination and Entrepreneurship</h3><p>Elena learned about Nova Scotia\'s Entrepreneur Stream, which provides a pathway to permanent residence for individuals who establish businesses in the province. The program requires applicants to have business experience, sufficient investment capital, and a viable business plan.</p><p>\"I had saved money during my time as a temporary worker, and my family was willing to help with the initial investment,\" Elena explains. \"I spent months developing a detailed business plan for a Filipino restaurant and catering service.\"</p><p>After submitting her business proposal, Elena received a letter of support from Nova Scotia\'s Office of Immigration, which helped her apply for a work permit extension. This gave her the time she needed to establish her business while her provincial nomination application was being processed.</p><h3>Business Success and Permanent Residence</h3><p>In 2020, Elena opened \"Manila Flavors,\" a restaurant featuring authentic Filipino cuisine. Despite launching during the pandemic, the business found success through takeout and delivery services. Once restrictions eased, the restaurant quickly built a loyal customer base that included both the Filipino community and local Halifax residents eager to try new cuisines.</p><p>After demonstrating that her business was operational and meeting the requirements of her business plan, Elena received her provincial nomination and subsequently applied for permanent residence. In late 2021, her permanent residence was approved.</p><p>\"The day I received my confirmation of permanent residence was incredibly emotional,\" Elena shares. \"I had gone from being a temporary worker to a business owner and permanent resident. It validated all the hard work and risk-taking.\"</p><h3>Community Impact</h3><p>Today, Manila Flavors employs six people, including two recently arrived Filipino immigrants. The restaurant has become a community hub, hosting cultural events and cooking classes.</p><p>\"What makes me proudest is creating a place that feels like home for other immigrants, while also sharing our culture with Canadians,\" says Elena. \"And from a business perspective, we\'re continuing to grow. We\'re now expanding into packaged food products that will be sold in local grocery stores.\"</p><p>Elena\'s story demonstrates how Canada\'s provincial nominee programs can benefit both immigrants and Canadian communities by encouraging entrepreneurship and creating economic growth.</p><p>\"If you\'re a temporary worker with entrepreneurial ambitions, explore the business immigration streams,\" Elena advises. \"Canada truly values immigrants who bring new ideas and create economic opportunities. It\'s been a challenging journey, but building a successful business in my new home country has been the most rewarding experience of my life.\"</p>', 'images/blog/success-story-3.jpg', 'Elena Vasquez', 'published', '2025-03-19 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(7, 3, 'Guide to British Columbia\'s Provincial Nominee Program', 'guide-to-bc-pnp', 'Everything you need to know about immigrating to British Columbia through the BC Provincial Nominee Program.', '<p>British Columbia, with its stunning natural landscapes, thriving tech industry, and high quality of life, is one of Canada\'s most popular immigration destinations. The BC Provincial Nominee Program (BC PNP) offers several pathways for skilled workers, entrepreneurs, and graduates to make this beautiful province their home.</p>\r\n\r\n<h3>BC PNP Streams Overview</h3>\r\n<p>The BC PNP is divided into three main categories, each with its own streams and requirements:</p>\r\n\r\n<h4>1. Skills Immigration</h4>\r\n<p>This category is for skilled workers in high-demand occupations. It includes:</p>\r\n<ul>\r\n    <li><strong>Skilled Worker</strong> - For experienced professionals with a job offer in BC</li>\r\n    <li><strong>Health Authority</strong> - For healthcare professionals with a job offer from a BC health authority</li>\r\n    <li><strong>International Graduate</strong> - For recent graduates from Canadian post-secondary institutions with a job offer in BC</li>\r\n    <li><strong>International Post-Graduate</strong> - For graduates with a master\'s or doctoral degree in sciences who don\'t need a job offer</li>\r\n    <li><strong>Entry Level and Semi-Skilled</strong> - For workers in specific industries like tourism, hospitality, or long-haul trucking</li>\r\n</ul>\r\n\r\n<h4>2. Express Entry BC</h4>\r\n<p>This category aligns with the federal Express Entry system, offering faster processing for:</p>\r\n<ul>\r\n    <li><strong>Skilled Worker</strong></li>\r\n    <li><strong>International Graduate</strong></li>\r\n    <li><strong>Healthcare Professional</strong></li>\r\n</ul>\r\n\r\n<h4>3. Entrepreneur Immigration</h4>\r\n<p>For business owners and managers looking to invest in and actively manage a business in BC:</p>\r\n<ul>\r\n    <li><strong>Base Category</strong> - For experienced entrepreneurs wanting to establish a business in BC</li>\r\n    <li><strong>Regional Pilot</strong> - For entrepreneurs wanting to start a business in participating small communities</li>\r\n    <li><strong>Strategic Projects</strong> - For established international companies setting up operations in BC</li>\r\n</ul>\r\n\r\n<h3>The Skills Immigration Registration System (SIRS)</h3>\r\n<p>Most BC PNP applicants must first register in the Skills Immigration Registration System (SIRS), which assigns points based on:</p>\r\n<ul>\r\n    <li>Salary</li>\r\n    <li>Education</li>\r\n    <li>Work experience</li>\r\n    <li>Location of job (with priority to regional areas)</li>\r\n    <li>In-demand occupation</li>\r\n</ul>\r\n<p>Candidates with the highest scores receive invitations to apply for provincial nomination.</p>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>While requirements vary by stream, most skilled worker applicants need:</p>\r\n<ul>\r\n    <li>A full-time, permanent job offer from a BC employer</li>\r\n    <li>Relevant work experience</li>\r\n    <li>Language proficiency (typically Canadian Language Benchmark 4)</li>\r\n    <li>Education credentials</li>\r\n    <li>Sufficient income to support themselves and dependents</li>\r\n</ul>\r\n\r\n<h3>Application Process</h3>\r\n<p>The application process typically involves these steps:</p>\r\n<ol>\r\n    <li>Determine eligibility and the appropriate stream</li>\r\n    <li>Register in the SIRS (for most streams)</li>\r\n    <li>Receive an invitation to apply (based on ranking)</li>\r\n    <li>Submit a complete BC PNP application</li>\r\n    <li>If approved, receive a provincial nomination</li>\r\n    <li>Apply to IRCC for permanent residence</li>\r\n</ol>\r\n\r\n<h3>Processing Times and Fees</h3>\r\n<p>BC PNP application processing typically takes 2-3 months after submission. Express Entry-aligned applications may be processed more quickly. The application fee is $1,150 CAD for skills immigration and $3,500 CAD for entrepreneur streams.</p>\r\n\r\n<p>If you\'re considering British Columbia as your immigration destination, the BC PNP offers viable pathways for qualifying candidates. As with all immigration programs, requirements and processes may change, so it\'s always advisable to consult with a licensed immigration consultant or refer to the official BC PNP website for the most current information.</p>', 'images/blog/bc-pnp.jpg', 'Jennifer Li', 'published', '2025-03-19 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(8, 3, 'Ontario\'s New Tech Draw: A Gateway for Tech Professionals', 'ontario-tech-draw-gateway', 'How Ontario\'s new Tech Draw is creating opportunities for IT professionals to immigrate to Canada\'s tech hub.', '<p>Ontario, home to Canada\'s largest technology sector, has introduced an innovative pathway for tech professionals seeking to immigrate to the province. The Ontario Immigrant Nominee Program (OINP) Tech Draws provide a streamlined route for skilled tech workers to obtain provincial nomination and, ultimately, permanent residence in Canada.</p>\r\n\r\n<h3>Understanding Ontario\'s Tech Draw</h3>\r\n<p>The Tech Draw is a specialized stream under the OINP\'s Human Capital Priorities Stream. It allows Ontario to search the federal Express Entry pool for candidates with experience in specific tech occupations, regardless of their CRS score. This targeted approach helps Ontario attract the tech talent it needs while giving tech professionals a significant advantage in their immigration journey.</p>\r\n\r\n<h3>Eligible Occupations</h3>\r\n<p>The Tech Draw focuses on six key tech occupations:</p>\r\n<ol>\r\n    <li>Software engineers and designers (NOC 2173)</li>\r\n    <li>Computer programmers and interactive media developers (NOC 2174)</li>\r\n    <li>Database analysts and data administrators (NOC 2172)</li>\r\n    <li>Computer engineers (NOC 2147)</li>\r\n    <li>Web designers and developers (NOC 2175)</li>\r\n    <li>Computer and information systems managers (NOC 0213)</li>\r\n</ol>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To be eligible for the Ontario Tech Draw, candidates must:</p>\r\n<ul>\r\n    <li>Have an active Express Entry profile</li>\r\n    <li>Have at least one year of work experience in one of the eligible tech occupations</li>\r\n    <li>Have a Canadian Language Benchmark (CLB) of 6 or higher in English or French</li>\r\n    <li>Have the education, experience, and language ability to work in the tech occupation</li>\r\n    <li>Have legal status in Canada (if currently in Canada)</li>\r\n    <li>Have sufficient funds to settle in Ontario</li>\r\n    <li>Intend to live and work in Ontario</li>\r\n</ul>\r\n<p>Notably, there is no minimum CRS score requirement for Tech Draws, unlike other OINP streams.</p>\r\n\r\n<h3>The Application Process</h3>\r\n<p>The process works as follows:</p>\r\n<ol>\r\n    <li>Create an Express Entry profile in the federal system</li>\r\n    <li>Be identified by Ontario in a Tech Draw (candidates don\'t apply directly)</li>\r\n    <li>Receive a Notification of Interest (NOI) from Ontario</li>\r\n    <li>Submit an OINP application within 45 days of receiving the NOI</li>\r\n    <li>If approved, receive a provincial nomination that adds 600 points to your CRS score</li>\r\n    <li>Receive an Invitation to Apply (ITA) for permanent residence through Express Entry</li>\r\n    <li>Submit a complete application to IRCC within 60 days</li>\r\n</ol>\r\n\r\n<h3>Recent Draw Results and Trends</h3>\r\n<p>Ontario has been holding regular Tech Draws, with increasing frequency. Recent draws have issued NOIs to between 1,000 and 1,500 candidates per draw. While there\'s no minimum CRS score requirement, selected candidates typically have scores above 350 points in the federal system.</p>\r\n\r\n<h3>Advantages of the Ontario Tech Draw</h3>\r\n<p>The Tech Draw offers several advantages:</p>\r\n<ul>\r\n    <li><strong>No minimum CRS score</strong> - Giving candidates with lower scores a chance at nomination</li>\r\n    <li><strong>No job offer requirement</strong> - Unlike many other provincial pathways</li>\r\n    <li><strong>Access to Canada\'s largest tech hub</strong> - Toronto and the surrounding area host thousands of tech companies</li>\r\n    <li><strong>Faster pathway to permanent residence</strong> - The 600 CRS points boost from a nomination virtually guarantees an Express Entry invitation</li>\r\n</ul>\r\n\r\n<h3>Preparing for the Tech Draw</h3>\r\n<p>If you\'re a tech professional interested in the Ontario Tech Draw, consider these strategies:</p>\r\n<ul>\r\n    <li>Ensure your Express Entry profile accurately reflects your tech experience</li>\r\n    <li>Obtain educational credential assessments for all your degrees</li>\r\n    <li>Achieve the highest possible language test scores</li>\r\n    <li>Keep your Express Entry profile updated with any new qualifications or experience</li>\r\n</ul>\r\n<p>The Ontario Tech Draw represents a golden opportunity for tech professionals who might otherwise struggle to meet the high CRS score requirements of regular Express Entry draws. With Ontario\'s tech sector continuing to grow rapidly, this immigration pathway is expected to remain a key route for international tech talent in the coming years.</p>', 'images/blog/ontario-tech.jpg', 'Raj Mehta', 'published', '2025-03-16 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(9, 3, 'Manitoba\'s In-Demand Occupations: A Path to Prairie Immigration', 'manitoba-in-demand-occupations', 'Discover how Manitoba\'s in-demand occupations list can help skilled workers immigrate to this prairie province.', '<p>Manitoba, with its stable economy, affordable housing, and growing industries, offers excellent opportunities for immigrants seeking a balanced quality of life in Canada. The Manitoba Provincial Nominee Program (MPNP) uses an In-Demand Occupations list to target skilled workers whose expertise matches the province\'s labor market needs.</p>\r\n\r\n<h3>Manitoba Provincial Nominee Program Overview</h3>\r\n<p>The MPNP allows Manitoba to nominate qualified candidates for permanent residence based on the province\'s economic needs and the applicant\'s ability to successfully settle in Manitoba. The program includes multiple pathways:</p>\r\n<ul>\r\n    <li>Skilled Workers in Manitoba</li>\r\n    <li>Skilled Workers Overseas</li>\r\n    <li>Business Investor Stream</li>\r\n    <li>International Education Stream</li>\r\n</ul>\r\n<p>For skilled workers, the In-Demand Occupations list plays a crucial role in determining eligibility and priority processing.</p>\r\n\r\n<h3>The In-Demand Occupations List</h3>\r\n<p>Manitoba regularly updates its In-Demand Occupations list based on labor market research and employer needs. The list is divided into four categories:</p>\r\n\r\n<h4>1. Manitoba Priority Occupations</h4>\r\n<p>These are high-priority occupations with significant labor shortages. Applications in these occupations receive priority processing. Examples include:</p>\r\n<ul>\r\n    <li>Registered nurses</li>\r\n    <li>Software engineers</li>\r\n    <li>Early childhood educators</li>\r\n    <li>Manufacturing supervisors</li>\r\n    <li>Truck drivers</li>\r\n</ul>\r\n\r\n<h4>2. In-Demand Occupations</h4>\r\n<p>These are occupations with moderate labor needs. Current examples include:</p>\r\n<ul>\r\n    <li>Administrative assistants</li>\r\n    <li>Construction managers</li>\r\n    <li>Accountants</li>\r\n    <li>Social workers</li>\r\n    <li>Chefs and cooks</li>\r\n</ul>\r\n\r\n<h4>3. Restricted Occupations</h4>\r\n<p>These occupations are only eligible with a valid job offer from a Manitoba employer and may require additional assessment. Examples include certain retail positions and office support roles.</p>\r\n\r\n<h4>4. Non-Eligible Occupations</h4>\r\n<p>Certain occupations are not eligible under the MPNP, usually because there\'s sufficient local labor supply or because they fall under National Occupational Classification (NOC) skill levels D or E.</p>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To qualify for nomination under the MPNP Skilled Worker pathways, candidates typically need:</p>\r\n<ul>\r\n    <li>Experience in an in-demand occupation (usually 6+ months)</li>\r\n    <li>Educational credentials (minimum high school, with post-secondary education preferred)</li>\r\n    <li>Language proficiency (CLB 6 for NOC 0, A, B occupations; CLB 5 for NOC C occupations)</li>\r\n    <li>Proven settlement funds</li>\r\n    <li>A connection to Manitoba (for overseas applicants) through family, previous education/work, or an invitation through a Strategic Recruitment Initiative</li>\r\n    <li>A valid job offer (required for some pathways, beneficial for others)</li>\r\n</ul>\r\n\r\n<h3>The Expression of Interest System</h3>\r\n<p>Manitoba uses an Expression of Interest (EOI) system to select candidates. Prospective immigrants submit an EOI profile, which is scored based on:</p>\r\n<ul>\r\n    <li>Language proficiency</li>\r\n    <li>Age</li>\r\n    <li>Work experience</li>\r\n    <li>Education</li>\r\n    <li>Manitoba connections</li>\r\n    <li>Adaptability factors</li>\r\n</ul>\r\n<p>Highest-scoring candidates receive Letters of Advice to Apply (LAAs) in regular draws. Candidates in priority occupations may receive LAAs even with lower scores.</p>\r\n\r\n<h3>Regional Immigration Benefits</h3>\r\n<p>Manitoba places special emphasis on immigration to rural and northern regions through its MPNP-B (Morden), MPNP-Russell, and other regional initiatives. Candidates willing to settle outside Winnipeg may receive priority processing and additional support.</p>\r\n\r\n<h3>Application Process</h3>\r\n<ol>\r\n    <li>Check eligibility against current criteria and in-demand occupations list</li>\r\n    <li>Submit an Expression of Interest</li>\r\n    <li>If selected, receive a Letter of Advice to Apply</li>\r\n    <li>Submit a complete MPNP application</li>\r\n    <li>If approved, receive a provincial nomination certificate</li>\r\n    <li>Apply to IRCC for permanent residence</li>\r\n</ol>\r\n\r\n<p>Manitoba\'s In-Demand Occupations list offers a practical pathway for skilled workers whose occupations align with the province\'s labor market needs. With its affordable housing, growing economy, and welcoming communities, Manitoba represents an excellent option for immigrants seeking a high quality of life in Canada\'s heartland.</p>', 'images/blog/manitoba-occupations.jpg', 'Thomas Wilson', 'published', '2025-03-12 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(10, 4, 'Top 5 Canadian Universities for International Students', 'top-5-canadian-universities', 'Explore the best Canadian universities for international students based on rankings, programs, and immigration pathways.', '<p>Canada has become one of the world\'s most popular destinations for international students, offering world-class education, diverse communities, and clear pathways to post-graduation work and permanent residence. Here, we explore the top five Canadian universities that consistently rank highly for international student experience and outcomes.</p>\r\n\r\n<h3>1. University of Toronto</h3>\r\n<p><strong>Location:</strong> Toronto, Ontario</p>\r\n<p><strong>International Student Population:</strong> 25%+ of total enrollment</p>\r\n<p><strong>Notable Programs:</strong> Computer Science, Engineering, Business, Medicine, Arts</p>\r\n<p><strong>Why It Stands Out:</strong> Consistently ranked as Canada\'s top university and among the world\'s top 20, the University of Toronto offers an unparalleled academic experience in Canada\'s largest city. With three distinct campuses and over 700 undergraduate programs, it provides exceptional research opportunities and a vibrant, diverse student community. The university\'s location in Toronto - Canada\'s business and cultural hub - offers excellent internship and employment prospects during and after studies.</p>\r\n<p><strong>Immigration Advantage:</strong> Graduates benefit from Ontario\'s strong Provincial Nominee Program options, including specific streams for international graduates.</p>\r\n\r\n<h3>2. University of British Columbia (UBC)</h3>\r\n<p><strong>Location:</strong> Vancouver, British Columbia</p>\r\n<p><strong>International Student Population:</strong> 28% of total enrollment</p>\r\n<p><strong>Notable Programs:</strong> Environmental Science, Engineering, Film Production, Forestry, International Relations</p>\r\n<p><strong>Why It Stands Out:</strong> UBC offers a stunning campus environment with views of mountains and ocean. The university is particularly strong in sustainability and environmental studies, as well as film and media production. Vancouver\'s mild climate and outdoor lifestyle make it appealing to students from diverse backgrounds. UBC\'s co-op programs provide valuable work experience integrated with academic studies.</p>\r\n<p><strong>Immigration Advantage:</strong> BC\'s PNP offers dedicated streams for international graduates, with special consideration for graduates in STEM fields.</p>\r\n\r\n<h3>3. McGill University</h3>\r\n<p><strong>Location:</strong> Montreal, Quebec</p>\r\n<p><strong>International Student Population:</strong> 30% of total enrollment</p>\r\n<p><strong>Notable Programs:</strong> Medicine, Law, Music, Neuroscience, Agricultural Sciences</p>\r\n<p><strong>Why It Stands Out:</strong> Founded in 1821, McGill combines historic prestige with cutting-edge research. Its downtown Montreal location offers the perfect blend of European charm and North American dynamism. The university is particularly known for its medical education and research, consistently ranking among the top medical-doctoral universities in Canada. The multicultural, bilingual environment of Montreal provides a unique cultural experience.</p>\r\n<p><strong>Immigration Advantage:</strong> Quebec offers specialized immigration pathways for graduates from Quebec institutions, with fewer restrictions for those proficient in French.</p>\r\n\r\n<h3>4. University of Waterloo</h3>\r\n<p><strong>Location:</strong> Waterloo, Ontario</p>\r\n<p><strong>International Student Population:</strong> 20% of total enrollment</p>\r\n<p><strong>Notable Programs:</strong> Computer Science, Engineering, Mathematics, Actuarial Science</p>\r\n<p><strong>Why It Stands Out:</strong> The University of Waterloo is renowned for its co-operative education program - the largest of its kind in the world. Students alternate between academic terms and paid work terms, graduating with up to two years of relevant work experience. The university has exceptional strength in STEM fields and close connections to Canada\'s \"Silicon Valley North\" technology corridor. Waterloo\'s entrepreneurial ecosystem has spawned numerous successful startups.</p>\r\n<p><strong>Immigration Advantage:</strong> Tech experience from co-op placements can position graduates well for Ontario\'s Tech Draw immigration pathway.</p>\r\n\r\n<h3>5. University of Alberta</h3>\r\n<p><strong>Location:</strong> Edmonton, Alberta</p>\r\n<p><strong>International Student Population:</strong> 15% of total enrollment</p>\r\n<p><strong>Notable Programs:</strong> Engineering, Petroleum Engineering, Agricultural Sciences, Health Sciences</p>\r\n<p><strong>Why It Stands Out:</strong> The University of Alberta offers excellent value with relatively lower tuition fees compared to other top Canadian universities, while maintaining high academic standards. It\'s particularly strong in resource-based industries like petroleum engineering and agricultural sciences. The university\'s extensive research facilities and funding make it attractive for graduate studies. Edmonton offers a high quality of life with affordable housing compared to Toronto or Vancouver.</p>\r\n<p><strong>Immigration Advantage:</strong> Alberta\'s PNP offers streams specifically for graduates of Canadian post-secondary institutions, with priority for graduates from Alberta universities.</p>\r\n\r\n<h3>Factors to Consider When Choosing a Canadian University</h3>\r\n<ul>\r\n    <li><strong>Program Availability and Strength:</strong> Research specific departments and programs rather than just overall university rankings</li>\r\n    <li><strong>Location and Climate:</strong> Canada\'s climate varies dramatically by region; consider your preferences</li>\r\n    <li><strong>Cost of Living:</strong> Major cities like Toronto and Vancouver have significantly higher living costs</li>\r\n    <li><strong>Tuition Fees:</strong> These can vary widely between institutions and programs</li>\r\n    <li><strong>Work Opportunities:</strong> Consider co-op programs and local job markets</li>\r\n    <li><strong>Provincial Immigration Pathways:</strong> Different provinces offer different options for post-graduation immigration</li>\r\n</ul>\r\n\r\n<h3>From Student to Permanent Resident</h3>\r\n<p>Canadian education offers clear pathways to permanent residence. After completing eligible programs, international students can apply for Post-Graduation Work Permits, gaining valuable Canadian work experience. This experience, combined with Canadian education, positions graduates strongly for permanent residence through Express Entry or Provincial Nominee Programs.</p>\r\n\r\n<p>Choosing the right Canadian university involves balancing academic goals with long-term immigration plans. Each institution and province offers unique advantages. By considering these factors, international students can make informed decisions that support both their educational and immigration objectives.</p>', 'images/blog/canadian-universities.jpg', 'Maria Chen', 'published', '2025-03-27 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(11, 4, 'Canadian Study Permits: A Complete Application Guide', 'canadian-study-permit-guide', 'Step-by-step instructions for a successful Canadian study permit application.', '<p>Securing a study permit is the crucial first step for international students planning to study in Canada. This comprehensive guide walks you through the entire application process, from determining eligibility to preparing for arrival.</p>\r\n\r\n<h3>Study Permit Basics</h3>\r\n<p>A study permit is an official document issued by Immigration, Refugees and Citizenship Canada (IRCC) that allows foreign nationals to study at designated learning institutions (DLIs) in Canada. Key points to understand:</p>\r\n<ul>\r\n    <li>A study permit is not a visa - you may also need a visitor visa or electronic travel authorization (eTA) to enter Canada</li>\r\n    <li>Study permits are generally valid for the length of your study program, plus an additional 90 days</li>\r\n    <li>With a study permit, you can work part-time (up to 20 hours per week) during regular academic sessions and full-time during scheduled breaks</li>\r\n</ul>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To qualify for a Canadian study permit, you must:</p>\r\n<ol>\r\n    <li>Be accepted by a designated learning institution (DLI) in Canada</li>\r\n    <li>Prove you have enough money to pay for:\r\n        <ul>\r\n            <li>Tuition fees</li>\r\n            <li>Living expenses for yourself and any family members who come with you to Canada</li>\r\n            <li>Return transportation</li>\r\n        </ul>\r\n    </li>\r\n    <li>Be a law-abiding citizen with no criminal record (a police certificate may be required)</li>\r\n    <li>Be in good health (a medical exam may be required)</li>\r\n    <li>Convince an immigration officer that you will leave Canada at the end of your authorized stay</li>\r\n</ol>\r\n\r\n<h3>Financial Requirements</h3>\r\n<p>You must demonstrate that you have sufficient funds to support yourself during your studies. The minimum amount required (not including tuition) is CAD $10,000 per year (or CAD $833 per month) for a single student. Additional funds are required for accompanying family members. Acceptable proof includes:</p>\r\n<ul>\r\n    <li>Bank statements for the past 4-6 months</li>\r\n    <li>Proof of a Canadian bank account in your name if money has been transferred</li>\r\n    <li>Proof of a student/education loan</li>\r\n    <li>Bank drafts that can be converted to Canadian dollars</li>\r\n    <li>Proof of payment of tuition and accommodation fees</li>\r\n    <li>A letter from the person or institution providing financial support</li>\r\n    <li>Proof of funding paid from within Canada, if you have a scholarship or are in a Canadian-funded educational program</li>\r\n</ul>\r\n\r\n<h3>Application Process</h3>\r\n<h4>Step 1: Obtain Acceptance from a DLI</h4>\r\n<p>Apply to and receive an acceptance letter from a designated learning institution in Canada. The acceptance letter should include:</p>\r\n<ul>\r\n    <li>Your full name and date of birth</li>\r\n    <li>The name of the institution and its DLI number</li>\r\n    <li>The program of study and level</li>\r\n    <li>The estimated duration of the program</li>\r\n    <li>The start date and expected completion date</li>\r\n    <li>The deadline for registration</li>\r\n    <li>Tuition fee information, including whether the tuition has been paid</li>\r\n</ul>\r\n\r\n<h4>Step 2: Check Processing Times and Application Methods</h4>\r\n<p>Processing times vary by country. Check the IRCC website for current times. You can apply online or on paper, though online applications are generally processed faster.</p>\r\n\r\n<h4>Step 3: Prepare Your Documents</h4>\r\n<p>Gather the following documents:</p>\r\n<ul>\r\n    <li>Valid passport</li>\r\n    <li>Acceptance letter from a DLI</li>\r\n    <li>Proof of financial support</li>\r\n    <li>Passport-sized photographs (specifications on IRCC website)</li>\r\n    <li>Immigration medical examination results (if required)</li>\r\n    <li>Police certificates (if required)</li>\r\n    <li>Statement of purpose/study plan explaining why you want to study in Canada</li>\r\n    <li>Proof of language proficiency (usually IELTS or TOEFL scores)</li>\r\n    <li>Proof of upfront fee payment (tuition deposit)</li>\r\n    <li>Québec Acceptance Certificate (CAQ) if studying in Québec</li>\r\n</ul>\r\n\r\n<h4>Step 4: Complete and Submit Your Application</h4>\r\n<p>Create an account on the IRCC website to apply online or obtain paper forms if applying by mail. Pay the application fee (CAD $150) and biometrics fee (CAD $85) if applicable. Submit all documents as required.</p>\r\n\r\n<h4>Step 5: Biometrics Appointment</h4>\r\n<p>Most applicants need to provide biometrics (fingerprints and photo). After submitting your application, you\'ll receive a letter instructing you where and when to provide your biometrics.</p>\r\n\r\n<h4>Step 6: Interview (If Required)</h4>\r\n<p>Some applicants may be called for an interview at the local visa office or embassy. This is more common for high-risk countries.</p>\r\n\r\n<h3>After Approval</h3>\r\n<p>If your application is approved, you\'ll receive a letter of introduction and, if required, a temporary resident visa (TRV) or electronic travel authorization (eTA). The actual study permit will be issued at the Canadian port of entry when you arrive.</p>\r\n\r\n<h3>SDS Program for Faster Processing</h3>\r\n<p>The Student Direct Stream (SDS) offers expedited processing (usually within 20 calendar days) for legal residents of certain countries, including India, China, Vietnam, Philippines, Pakistan, Morocco, and Senegal. SDS applicants must meet additional requirements, including:</p>\r\n<ul>\r\n    <li>A higher guaranteed investment certificate (GIC) of CAD $10,000</li>\r\n    <li>Higher language requirements (minimum IELTS score of 6.0 in each skill)</li>\r\n    <li>Tuition payment for the first year</li>\r\n</ul>\r\n\r\n<h3>Common Reasons for Refusal</h3>\r\n<p>Study permit applications are often refused for these reasons:</p>\r\n<ul>\r\n    <li><strong>Insufficient funds</strong> - Inadequate proof of financial resources</li>\r\n    <li><strong>Purpose of visit</strong> - Unconvincing study plan or concerns you won\'t return home</li>\r\n    <li><strong>Inadmissibility</strong> - Health or security concerns</li>\r\n    <li><strong>Missing documents</strong> - Incomplete application</li>\r\n</ul>\r\n\r\n<p>A carefully prepared study permit application is your gateway to a Canadian education. Take time to understand the requirements, prepare thorough documentation, and present a compelling case for your studies in Canada. With proper preparation, you\'ll maximize your chances of approval and be on your way to an enriching educational experience in Canada.</p>', 'images/blog/study-permit.jpg', 'Ahmed Hassan', 'published', '2025-03-22 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07');
INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `author`, `status`, `publish_date`, `created_at`, `updated_at`) VALUES
(12, 4, 'Post-Graduation Work Permit: Your Bridge to Canadian Immigration', 'post-graduation-work-permit', 'How the PGWP can help international graduates transition from student to permanent resident.', '<p>The Post-Graduation Work Permit (PGWP) Program is one of Canada\'s most valuable immigration pathways for international students. This unique program allows graduates from eligible Canadian post-secondary institutions to gain valuable Canadian work experience, which can significantly enhance their prospects for permanent residence.</p>\r\n\r\n<h3>What is the Post-Graduation Work Permit?</h3>\r\n<p>The PGWP is an open work permit that allows international graduates to work for any employer in Canada. Unlike other work permits, it doesn\'t require a job offer or Labour Market Impact Assessment (LMIA). Key features include:</p>\r\n<ul>\r\n    <li>Duration matches the length of your study program (minimum 8 months, maximum 3 years)</li>\r\n    <li>Allows you to work for any employer in any location in Canada</li>\r\n    <li>Permits full-time, part-time, or self-employed work</li>\r\n    <li>Can only be obtained once in a lifetime</li>\r\n</ul>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To qualify for a PGWP, you must:</p>\r\n<ol>\r\n    <li>Have completed a full-time study program at a Designated Learning Institution (DLI) in Canada that was at least 8 months long</li>\r\n    <li>Have graduated from:\r\n        <ul>\r\n            <li>A public post-secondary institution (university, college, or institute)</li>\r\n            <li>A private post-secondary institution that operates under the same rules as public institutions</li>\r\n            <li>A private secondary or post-secondary institution that offers qualifying programs of 900 hours or more leading to a diploma or certificate</li>\r\n        </ul>\r\n    </li>\r\n    <li>Have maintained full-time student status in Canada during each academic session of your program (exceptions for final semester and authorized leaves)</li>\r\n    <li>Have received a transcript and an official letter from your institution confirming you\'ve completed your program</li>\r\n    <li>Apply within 180 days of receiving your final marks or formal written notification of program completion</li>\r\n    <li>Have a valid study permit or have had a study permit when you completed your program</li>\r\n</ol>\r\n\r\n<h3>PGWP Duration</h3>\r\n<p>The length of your PGWP depends on the duration of your completed program:</p>\r\n<ul>\r\n    <li>Programs 8 months to less than 2 years: PGWP valid for the same length as the program</li>\r\n    <li>Programs 2 years or longer: PGWP valid for 3 years</li>\r\n    <li>More than one program: May combine program lengths if each program meets PGWP criteria and was completed within 2 years</li>\r\n</ul>\r\n\r\n<h3>Application Process</h3>\r\n<h4>Step 1: Gather Required Documents</h4>\r\n<ul>\r\n    <li>Valid passport</li>\r\n    <li>Official letter from your institution confirming program completion</li>\r\n    <li>Official transcript</li>\r\n    <li>PGWP application fee (CAD $255)</li>\r\n    <li>Passport-sized photos (if applying on paper)</li>\r\n    <li>Proof you maintained full-time student status during your program (exceptions for final semester)</li>\r\n</ul>\r\n\r\n<h4>Step 2: Submit Your Application</h4>\r\n<p>You can apply online or on paper, though online applications are processed faster. You must apply within 180 days of receiving confirmation that you\'ve completed your program. You can apply:</p>\r\n<ul>\r\n    <li>From within Canada if you have a valid study permit</li>\r\n    <li>From outside Canada if you left Canada after completing your studies (but this is not recommended)</li>\r\n</ul>\r\n\r\n<h4>Step 3: Maintain Status While Waiting</h4>\r\n<p>If you apply before your study permit expires, you can stay in Canada and work full-time while waiting for your PGWP decision under \"implied status.\"</p>\r\n\r\n<h3>From PGWP to Permanent Residence</h3>\r\n<p>The PGWP serves as an excellent bridge to permanent residence, primarily through three pathways:</p>\r\n\r\n<h4>1. Express Entry - Canadian Experience Class (CEC)</h4>\r\n<p>After gaining at least 1 year of skilled work experience (NOC 0, A, or B) during your PGWP period, you can apply through the Canadian Experience Class. Advantages include:</p>\r\n<ul>\r\n    <li>Additional CRS points for Canadian education and work experience</li>\r\n    <li>No need for a job offer</li>\r\n    <li>Faster processing than many other immigration pathways</li>\r\n</ul>\r\n\r\n<h4>2. Provincial Nominee Programs (PNPs)</h4>\r\n<p>Many provinces offer immigration streams specifically for international graduates, including:</p>\r\n<ul>\r\n    <li>Ontario\'s International Graduate streams</li>\r\n    <li>British Columbia\'s International Graduate category</li>\r\n    <li>Alberta\'s International Graduate Entrepreneur Immigration Stream</li>\r\n    <li>Nova Scotia\'s International Graduate Entrepreneur Stream</li>\r\n    <li>Manitoba\'s International Education Stream</li>\r\n</ul>\r\n<p>These streams often have lower requirements than federal programs and can offer a provincial nomination, which adds 600 points to your Express Entry score.</p>\r\n\r\n<h4>3. Quebec Experience Program (PEQ)</h4>\r\n<p>For graduates who studied in Quebec, the PEQ offers a streamlined pathway to permanent residence, requiring:</p>\r\n<ul>\r\n    <li>A Quebec diploma</li>\r\n    <li>French language proficiency (minimum B2 level)</li>\r\n    <li>Intent to live in Quebec</li>\r\n</ul>\r\n\r\n<h3>Recent PGWP Changes and Considerations</h3>\r\n<p>The PGWP program has undergone several recent changes that benefit international graduates:</p>\r\n<ul>\r\n    <li><strong>Online study flexibility:</strong> 100% of online studies completed between March 2020 and August 31, 2023, due to COVID-19 count toward PGWP eligibility</li>\r\n    <li><strong>PGWP extensions:</strong> Occasional PGWP extension opportunities for holders with expiring permits</li>\r\n    <li><strong>Spousal open work permits:</strong> Spouses of PGWP holders in managerial, professional, or technical positions may be eligible for open work permits</li>\r\n</ul>\r\n\r\n<h3>Maximizing Your PGWP Experience</h3>\r\n<p>To make the most of your PGWP period:</p>\r\n<ol>\r\n    <li>Apply as soon as possible after graduation to maximize your work period</li>\r\n    <li>Focus on gaining NOC 0, A, or B level work experience (skilled positions)</li>\r\n    <li>Improve language skills to meet or exceed CLB 7 (for Express Entry)</li>\r\n    <li>Research provincial nomination options specific to your situation</li>\r\n    <li>Begin planning your permanent residence application early, ideally at least 6 months before your PGWP expires</li>\r\n</ol>\r\n\r\n<p>The Post-Graduation Work Permit is more than just a work authorization - it\'s a strategic stepping stone in your Canadian immigration journey. With careful planning and the right approach, the PGWP period can successfully bridge your transition from international student to Canadian permanent resident.</p>', 'images/blog/pgwp.jpg', 'Sophia Lee', 'published', '2025-03-09 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(13, 5, 'Canada\'s Global Talent Stream: Fast-Track Work Permits for Tech Professionals', 'global-talent-stream-tech-professionals', 'How the Global Talent Stream is revolutionizing the hiring of foreign tech workers in Canada.', '<p>Canada\'s Global Talent Stream (GTS) has emerged as a game-changer in the international tech talent landscape, offering a streamlined, expedited pathway for skilled tech professionals to work in Canada. Launched in 2017 as part of the Global Skills Strategy, this innovative program has significantly reduced processing times and administrative burdens for companies hiring foreign tech talent.</p>\r\n\r\n<h3>What is the Global Talent Stream?</h3>\r\n<p>The Global Talent Stream is a specific category of Canada\'s Temporary Foreign Worker Program designed to help Canadian employers access highly-skilled global talent quickly and efficiently. The program offers:</p>\r\n<ul>\r\n    <li>Work permit processing in as little as two weeks (compared to the standard processing time of 2-3 months)</li>\r\n    <li>Exemption from the Labor Market Impact Assessment (LMIA) advertising requirements</li>\r\n    <li>Dedicated account management support from the government</li>\r\n    <li>Access to a simplified work permit application process</li>\r\n</ul>\r\n\r\n<h3>Two Streams of Eligibility</h3>\r\n<p>The Global Talent Stream consists of two distinct categories:</p>\r\n\r\n<h4>Category A: For employers referred by a designated referral partner</h4>\r\n<p>This category is for innovative companies identified by designated referral partners as having the potential to create jobs and drive growth. Employers must be hiring unique and specialized talent. Designated referral partners include:</p>\r\n<ul>\r\n    <li>Global Affairs Canada\'s Trade Commissioner Service</li>\r\n    <li>Provincial or territorial government agencies</li>\r\n    <li>Regional development agencies</li>\r\n    <li>Industry associations like the Council of Canadian Innovators</li>\r\n</ul>\r\n<p>To qualify, the position being filled must offer a high wage (usually above CAD $80,000) and require advanced knowledge and expertise.</p>\r\n\r\n<h4>Category B: For employers seeking to hire workers in occupations on the Global Talent Occupations List</h4>\r\n<p>This category is for employers hiring for specific in-demand tech and STEM positions where there is a demonstrated shortage of Canadian workers. The current Global Talent Occupations List includes:</p>\r\n<ul>\r\n    <li>Software engineers and designers (NOC 2173)</li>\r\n    <li>Computer programmers and interactive media developers (NOC 2174)</li>\r\n    <li>Web designers and developers (NOC 2175)</li>\r\n    <li>Database analysts and data administrators (NOC 2172)</li>\r\n    <li>Digital media designers (NOC 5241)</li>\r\n    <li>And several other technical and scientific roles</li>\r\n</ul>\r\n<p>For these positions, employers must pay the prevailing wage or higher, which varies by location and position.</p>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To be eligible for the Global Talent Stream, the employer must:</p>\r\n<ul>\r\n    <li>Be a Canadian employer</li>\r\n    <li>Have a valid job offer for a position on the Global Talent Occupations List</li>\r\n    <li>Be able to demonstrate a shortage of Canadian workers</li>\r\n</ul>\r\n\r\n<h3>Application Process</h3>\r\n<p>The application process typically involves these steps:</p>\r\n<ol>\r\n    <li>Employer submits a Labour Market Impact Assessment (LMIA) application</li>\r\n    <li>Employer receives approval from Service Canada</li>\r\n    <li>Employer posts a job posting on the Global Talent Stream portal</li>\r\n    <li>Employer submits a Global Talent Stream application</li>\r\n    <li>Candidate applies for a work permit</li>\r\n    <li>Candidate works in Canada</li>\r\n    <li>Candidate applies for permanent residence</li>\r\n</ol>\r\n\r\n<h3>Advantages of the Global Talent Stream</h3>\r\n<p>The Global Talent Stream offers several advantages:</p>\r\n<ul>\r\n    <li><strong>Faster processing</strong> - Processing times are typically 2-3 weeks</li>\r\n    <li><strong>Exemption from LMIA advertising</strong> - No need to advertise the job</li>\r\n    <li><strong>Dedicated support</strong> - Dedicated account management support</li>\r\n    <li><strong>Simplified process</strong> - A streamlined application process</li>\r\n</ul>\r\n\r\n<h3>Why Choose the Global Talent Stream?</h3>\r\n<p>The Global Talent Stream is a valuable pathway for Canadian employers to hire highly-skilled foreign workers. It offers several advantages, including faster processing times, exemption from LMIA advertising, dedicated support, and a simplified application process. If you\'re a Canadian employer looking to hire a highly-skilled foreign worker, the Global Talent Stream is a viable option.</p>', 'images/blog/global-talent.jpg', 'Mark Wilson', 'published', '2025-03-22 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(14, 5, 'Working Holiday in Canada: A Complete Guide to IEC Work Permits', 'working-holiday-canada-guide', 'Everything you need to know about the International Experience Canada (IEC) program and how to work in Canada on a working holiday.', '<p>The International Experience Canada (IEC) program offers a unique opportunity for young adults from over 30 countries to live and work in Canada temporarily. The Working Holiday category is particularly popular, allowing participants to work for any employer in Canada while exploring this vast and diverse country.</p>\r\n\r\n<h3>What is International Experience Canada (IEC)?</h3>\r\n<p>IEC is a program that allows young people from countries with bilateral youth mobility agreements with Canada to work temporarily in Canada. The program has three categories:</p>\r\n<ul>\r\n    <li><strong>Working Holiday</strong>: Allows participants to work for any employer in Canada with an open work permit</li>\r\n    <li><strong>Young Professionals</strong>: For those with a job offer related to their field of expertise or education</li>\r\n    <li><strong>International Co-op/Internship</strong>: For students completing a work placement or internship in Canada as part of their academic curriculum</li>\r\n</ul>\r\n<p>This guide focuses primarily on the Working Holiday category, which offers the most flexibility.</p>\r\n\r\n<h3>Eligibility Requirements</h3>\r\n<p>To participate in the Working Holiday program, you must:</p>\r\n<ul>\r\n    <li>Be a citizen of a country that has a youth mobility agreement with Canada</li>\r\n    <li>Be between 18 and 30 or 35 years old (depending on your country of citizenship)</li>\r\n    <li>Have a valid passport for the duration of your stay</li>\r\n    <li>Have at least CAD $2,500 to help cover initial expenses</li>\r\n    <li>Purchase health insurance for the duration of your stay</li>\r\n    <li>Have a return ticket or sufficient funds to purchase one</li>\r\n    <li>Have no dependent children</li>\r\n    <li>Not have participated in the Working Holiday category before (some countries allow participation twice)</li>\r\n</ul>\r\n\r\n<h3>Participating Countries</h3>\r\n<p>Currently, over 30 countries have Working Holiday agreements with Canada, including:</p>\r\n<ul>\r\n    <li>Australia</li>\r\n    <li>Belgium</li>\r\n    <li>Chile</li>\r\n    <li>Costa Rica</li>\r\n    <li>Czech Republic</li>\r\n    <li>Denmark</li>\r\n    <li>France</li>\r\n    <li>Germany</li>\r\n    <li>Ireland</li>\r\n    <li>Italy</li>\r\n    <li>Japan</li>\r\n    <li>South Korea</li>\r\n    <li>New Zealand</li>\r\n    <li>Spain</li>\r\n    <li>Sweden</li>\r\n    <li>United Kingdom</li>\r\n</ul>\r\n<p>Each country has specific quotas, age limits, and in some cases, slightly different requirements.</p>\r\n\r\n<h3>Application Process</h3>\r\n<p>The application process for a Working Holiday permit involves several steps:</p>\r\n\r\n<h4>1. Create a profile and submit to the pool</h4>\r\n<ul>\r\n    <li>Create an IRCC online account</li>\r\n    <li>Complete the Come to Canada questionnaire to confirm eligibility</li>\r\n    <li>Create an IEC profile and submit it to the pool of candidates</li>\r\n</ul>\r\n\r\n<h4>2. Receive an Invitation to Apply (ITA)</h4>\r\n<ul>\r\n    <li>ITAs are issued through random draws from the pool</li>\r\n    <li>If selected, you\'ll receive an invitation via your account</li>\r\n    <li>You\'ll have 10 days to decide whether to accept or decline</li>\r\n</ul>\r\n\r\n<h4>3. Submit work permit application</h4>\r\n<ul>\r\n    <li>After accepting, you\'ll have 20 days to submit a complete application</li>\r\n    <li>Pay the applicable fees: participation fee ($153 CAD) and work permit fee ($100 CAD)</li>\r\n    <li>Provide biometrics if required ($85 CAD)</li>\r\n</ul>\r\n\r\n<h4>4. Receive approval and travel to Canada</h4>\r\n<ul>\r\n    <li>If approved, you\'ll receive a Port of Entry (POE) Letter</li>\r\n    <li>Travel to Canada with all required documents</li>\r\n    <li>Receive your work permit at the Canadian port of entry</li>\r\n</ul>\r\n\r\n<h3>Making the Most of Your Working Holiday</h3>\r\n<p>Here are some tips to maximize your experience:</p>\r\n\r\n<h4>Before You Leave</h4>\r\n<ul>\r\n    <li>Research job opportunities in different regions of Canada</li>\r\n    <li>Consider seasonal work in tourism or agriculture to experience different areas</li>\r\n    <li>Join online communities of Working Holiday participants for advice</li>\r\n    <li>Prepare your resume according to Canadian standards</li>\r\n    <li>Arrange temporary accommodation for your arrival</li>\r\n</ul>\r\n\r\n<h4>After Arriving in Canada</h4>\r\n<ul>\r\n    <li>Apply for a Social Insurance Number (SIN) immediately</li>\r\n    <li>Open a Canadian bank account</li>\r\n    <li>Obtain a Canadian phone number</li>\r\n    <li>Consider working in different provinces to experience Canada\'s diversity</li>\r\n    <li>Network with locals and other Working Holiday participants</li>\r\n</ul>\r\n\r\n<h3>Extending Your Stay or Transitioning to Permanent Residence</h3>\r\n<p>Many Working Holiday participants fall in love with Canada and seek ways to stay longer. Options include:</p>\r\n<ul>\r\n    <li>Applying for a second Working Holiday (if eligible based on your citizenship)</li>\r\n    <li>Finding an employer to sponsor you for a closed work permit</li>\r\n    <li>Pursuing educational opportunities and applying for a study permit</li>\r\n    <li>Exploring Express Entry or Provincial Nominee Programs if you gain skilled work experience during your stay</li>\r\n</ul>\r\n\r\n<p>The Working Holiday program offers a unique opportunity to experience Canadian life, culture, and work environments with minimal barriers. For many young people, it becomes not just a memorable adventure but also the first step toward a more permanent future in Canada.</p>', 'images/blog/working-holiday.jpg', 'Emily Johnson', 'published', '2025-03-16 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(15, 5, 'LMIA-Based Work Permits: A Comprehensive Guide', 'lmia-based-work-permits-guide', 'Understanding the Labour Market Impact Assessment process and how to obtain an LMIA-based work permit for Canada.', '<p>For many international workers, the Labour Market Impact Assessment (LMIA) represents a crucial step in obtaining a work permit for Canada. While the process can seem complex, understanding its requirements and procedures can significantly increase your chances of success.</p>\r\n\r\n<h3>What is a Labour Market Impact Assessment (LMIA)?</h3>\r\n<p>An LMIA is a document that Canadian employers must usually obtain before hiring a foreign worker. Issued by Employment and Social Development Canada (ESDC), the LMIA verifies that:</p>\r\n<ul>\r\n    <li>There is a genuine need for a foreign worker to fill the job</li>\r\n    <li>No Canadian citizen or permanent resident is available to do the job</li>\r\n    <li>Hiring a foreign worker will not negatively impact the Canadian labor market</li>\r\n</ul>\r\n<p>A positive LMIA (previously called a \"confirmation letter\") shows that there is a need for a foreign worker to fill the position and that no Canadian worker is available to do the job.</p>\r\n\r\n<h3>Types of LMIAs</h3>\r\n<p>There are several types of LMIAs, each with different requirements and processing standards:</p>\r\n\r\n<h4>High-Wage Positions</h4>\r\n<ul>\r\n    <li>For positions offering a wage at or above the median wage for the occupation in the specific region</li>\r\n    <li>Requires employers to conduct extensive recruitment efforts</li>\r\n    <li>Standard processing time: approximately 8-10 weeks</li>\r\n</ul>\r\n\r\n<h4>Low-Wage Positions</h4>\r\n<ul>\r\n    <li>For positions offering a wage below the median wage for the occupation in the specific region</li>\r\n    <li>Stricter requirements, including a cap on the number of low-wage foreign workers an employer can hire</li>\r\n    <li>Employers must provide transportation and affordable housing information</li>\r\n</ul>\r\n\r\n<h4>Global Talent Stream (GTS)</h4>\r\n<ul>\r\n    <li>Expedited LMIA process for high-skilled occupations (primarily in tech)</li>\r\n    <li>Processing time: 10 business days</li>\r\n    <li>Exemption from some recruitment requirements</li>\r\n</ul>\r\n\r\n<h4>Agricultural Stream</h4>\r\n<ul>\r\n    <li>Specific to positions in agriculture, particularly the Seasonal Agricultural Worker Program (SAWP)</li>\r\n    <li>Tailored requirements for the agricultural sector</li>\r\n</ul>\r\n\r\n<h3>The LMIA Application Process for Employers</h3>\r\n<p>Canadian employers must follow these steps to obtain an LMIA:</p>\r\n\r\n<h4>1. Recruitment and Advertising</h4>\r\n<p>Before applying for an LMIA, employers must typically advertise the position for at least 4 consecutive weeks in the preceding 3 months on:</p>\r\n<ul>\r\n    <li>The Job Bank or provincial/territorial job board</li>\r\n    <li>Two or more additional recruitment methods consistent with normal practice for the occupation</li>\r\n</ul>\r\n<p>Some positions may be exempt from advertising requirements, such as those under the Global Talent Stream or positions requiring unique skills.</p>\r\n\r\n<h4>2. Complete LMIA Application</h4>\r\n<p>The employer must submit:</p>\r\n<ul>\r\n    <li>Completed LMIA application forms</li>\r\n    <li>Job offer details</li>\r\n    <li>Proof of recruitment efforts</li>\r\n    <li>Transition plan (for high-wage positions) showing how they will reduce reliance on temporary foreign workers</li>\r\n    <li>Processing fee of $1,000 CAD per position</li>\r\n</ul>\r\n\r\n<h4>3. LMIA Assessment</h4>\r\n<p>ESDC/Service Canada evaluates the application based on:</p>\r\n<ul>\r\n    <li>Genuineness of the job offer</li>\r\n    <li>Whether the wage offered is consistent with the prevailing wage for the occupation</li>\r\n    <li>Working conditions compliance with provincial/territorial standards</li>\r\n    <li>Potential benefits to the Canadian labor market</li>\r\n    <li>Employer compliance with previous LMIA conditions (if applicable)</li>\r\n</ul>\r\n\r\n<h4>4. LMIA Decision</h4>\r\n<p>If successful, the employer receives a positive LMIA (valid for 6 months) to provide to the foreign worker.</p>\r\n\r\n<h3>Work Permit Application Process for Foreign Workers</h3>\r\n<p>After receiving a positive LMIA and job offer from a Canadian employer, foreign workers must apply for a work permit:</p>\r\n\r\n<h4>1. Gather Required Documents</h4>\r\n<ul>\r\n    <li>Positive LMIA and detailed job offer</li>\r\n    <li>Work permit application form</li>\r\n    <li>Proof of qualifications for the job</li>\r\n    <li>Proof of identity and valid passport</li>\r\n    <li>Proof of financial support</li>\r\n    <li>Processing fee ($155 CAD)</li>\r\n    <li>Biometrics fee ($85 CAD) if applicable</li>\r\n</ul>\r\n\r\n<h4>2. Submit Application</h4>\r\n<p>Applications can be submitted online or at a visa application center, depending on your country.</p>\r\n\r\n<h4>3. Biometrics and Medical Exam</h4>\r\n<p>You may need to:</p>\r\n<ul>\r\n    <li>Provide biometrics (fingerprints and photo)</li>\r\n    <li>Complete a medical examination depending on your job and country of residence</li>\r\n</ul>\r\n\r\n<h4>4. Processing and Arrival</h4>\r\n<p>If approved, you\'ll receive:</p>\r\n<ul>\r\n    <li>A letter of introduction (for visa-exempt nationals) or a visa and letter of introduction (for visa-required nationals)</li>\r\n    <li>The actual work permit is issued at a Canadian port of entry upon arrival</li>\r\n</ul>\r\n\r\n<h3>Tips for Success</h3>\r\n<p>For employers:</p>\r\n<ul>\r\n    <li>Ensure the position meets prevailing wage requirements</li>\r\n    <li>Document all recruitment efforts thoroughly</li>\r\n    <li>Be prepared to demonstrate why Canadian candidates were not suitable</li>\r\n    <li>Consider using an immigration consultant for complex applications</li>\r\n</ul>\r\n\r\n<p>For workers:</p>\r\n<ul>\r\n    <li>Ensure your qualifications match the job requirements exactly</li>\r\n    <li>Maintain open communication with your employer during the process</li>\r\n    <li>Prepare for your potential transition to permanent residence if interested</li>\r\n    <li>Understand your rights as a temporary foreign worker in Canada</li>\r\n</ul>\r\n\r\n<h3>Pathway to Permanent Residence</h3>\r\n<p>An LMIA-based work permit can be an excellent stepping stone to permanent residence through:</p>\r\n<ul>\r\n    <li>Canadian Experience Class (after gaining one year of skilled work experience)</li>\r\n    <li>Provincial Nominee Programs (many provinces have streams for workers already employed in the province)</li>\r\n    <li>Federal Skilled Worker Program (work experience and job offers add points)</li>\r\n</ul>\r\n\r\n<p>While the LMIA process requires effort from both employers and workers, it remains one of the most secure pathways to obtaining Canadian work authorization with a clear path to eventual permanent residence.</p>', 'images/blog/lmia-work-permit.jpg', 'Thomas Brown', 'published', '2025-03-12 07:03:07', '2025-04-03 07:03:07', '2025-04-03 07:03:07'),
(16, 1, 'xxx', 'blog-test', 'ROy', 'hi', 'images/blog/1744209607_compressed-image.jpeg', 'CANEXT Team', 'published', '2025-04-09 14:39:00', '2025-04-09 14:40:06', '2025-04-09 14:40:24');

-- --------------------------------------------------------

--
-- Table structure for table `consultants`
--

CREATE TABLE `consultants` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `rcic_number` varchar(20) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `membership_plan` enum('bronze','silver','gold') DEFAULT 'bronze',
  `profile_image` varchar(255) DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `languages` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `video_consultation_available` tinyint(1) DEFAULT 1,
  `phone_consultation_available` tinyint(1) DEFAULT 1,
  `in_person_consultation_available` tinyint(1) DEFAULT 1,
  `video_consultation_fee` decimal(10,2) DEFAULT 150.00,
  `phone_consultation_fee` decimal(10,2) DEFAULT 100.00,
  `in_person_consultation_fee` decimal(10,2) DEFAULT 200.00,
  `years_experience` int(11) DEFAULT 5,
  `successful_cases` int(11) DEFAULT 100,
  `office_address` text DEFAULT NULL,
  `office_hours` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultants`
--

INSERT INTO `consultants` (`id`, `first_name`, `last_name`, `rcic_number`, `status`, `membership_plan`, `profile_image`, `specialization`, `languages`, `bio`, `consultation_fee`, `created_at`, `updated_at`, `video_consultation_available`, `phone_consultation_available`, `in_person_consultation_available`, `video_consultation_fee`, `phone_consultation_fee`, `in_person_consultation_fee`, `years_experience`, `successful_cases`, `office_address`, `office_hours`, `email`, `phone`, `password`) VALUES
(9, 'Ahmed', 'Hassan', 'R901234', 'approved', 'gold', 'ahmed-hassan.jpg', 'Express Entry,Work Permits,Family Sponsorship', 'English,Arabic', 'Comprehensive experience in all immigration streams with a focus on Express Entry and family class applications.', 200.00, '2025-04-12 03:42:30', '2025-04-12 03:51:25', 1, 1, 1, 150.00, 100.00, 200.00, 8, 250, '123 Immigration Street\nToronto, ON M5V 2H1\nCanada', 'Monday - Friday: 9:00 AM - 5:00 PM\nSaturday: By appointment\nSunday: Closed', 'ahmed.hassan@canext.ca', '+1 (416) 555-0123', ''),
(10, 'Emma', 'Brown', 'R012345', 'approved', 'bronze', 'emma-brown.jpg', 'Study Permits,Express Entry', 'English,French', 'Passionate about education and skilled immigration. Helping clients navigate the complex immigration system with confidence.', 125.00, '2025-04-12 03:42:30', '2025-04-12 03:51:25', 1, 1, 1, 150.00, 100.00, 200.00, 8, 250, '123 Immigration Street\nToronto, ON M5V 2H1\nCanada', 'Monday - Friday: 9:00 AM - 5:00 PM\nSaturday: By appointment\nSunday: Closed', 'emma.brown@canext.ca', '+1 (416) 555-0123', ''),
(12, '', '', '', 'approved', 'bronze', NULL, '', '', '', 0.00, '2025-04-12 05:11:17', '2025-04-12 14:19:37', 1, 1, 1, 150.00, 100.00, 200.00, 0, 0, '', '', '', '', '$2y$12$vmw2Nc9BT1sb9SCXy/RBpOxbfizq5JQaVGjAc6W46/4Se8buph8Cu'),
(13, 'saurav', 'suku', '', 'pending', 'bronze', NULL, NULL, NULL, NULL, 0.00, '2025-04-12 07:23:27', '2025-04-12 07:23:24', 1, 1, 1, 150.00, 100.00, 200.00, 5, 100, NULL, NULL, 'info@neowebx.com', '8080808808', '$2y$12$1Os0WzF/k9XQeN9aueaAyupohTyWfZOzDLN1tRToSZR7VJtlWiBM6');

-- --------------------------------------------------------

--
-- Table structure for table `consultant_reviews`
--

CREATE TABLE `consultant_reviews` (
  `id` int(11) NOT NULL,
  `consultant_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_notes`
--

CREATE TABLE `consultation_notes` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `immigration_status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `postal_code`, `country`, `date_of_birth`, `citizenship`, `passport_number`, `immigration_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Smith', 'john.smith@example.com', '+1 (123) 456-7890', NULL, NULL, NULL, 'USA', NULL, 'American', NULL, NULL, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(2, 'Maria', 'Rodriguez', 'maria.r@example.com', '+1 (234) 567-8901', NULL, NULL, NULL, 'Mexico', NULL, 'Mexican', NULL, NULL, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(3, 'David', 'Chen', 'david.chen@example.com', '+1 (345) 678-9012', NULL, NULL, NULL, 'China', NULL, 'Chinese', NULL, NULL, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(4, 'Sarah', 'Johnson', 'sarah.j@example.com', '+1 (456) 789-0123', NULL, NULL, NULL, 'Canada', NULL, 'Canadian', NULL, NULL, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(5, 'Raj', 'Patel', 'raj.patel@example.com', '+1 (567) 890-1234', NULL, NULL, NULL, 'India', NULL, 'Indian', NULL, NULL, NULL, '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
(6, 'Sukumar', 'saurav', 'sukumarsaurav@gmail.com', '09991289245', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pr', NULL, '2025-04-03 04:43:16', '2025-04-03 04:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `day_consultation_availability`
--

CREATE TABLE `day_consultation_availability` (
  `id` int(11) NOT NULL,
  `consultant_id` int(11) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `video_available` tinyint(1) DEFAULT 0,
  `phone_available` tinyint(1) DEFAULT 0,
  `in_person_available` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `day_consultation_availability`
--

INSERT INTO `day_consultation_availability` (`id`, `consultant_id`, `day_of_week`, `video_available`, `phone_available`, `in_person_available`) VALUES
(1, 12, 'saturday', 1, 1, 1),
(2, 12, 'sunday', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `downloadable_resources`
--

CREATE TABLE `downloadable_resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('pdf','doc','docx','xls','xlsx','ppt','pptx','zip','other') NOT NULL DEFAULT 'pdf',
  `file_size` int(11) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-file-pdf',
  `category_id` int(11) DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `downloadable_resources`
--

INSERT INTO `downloadable_resources` (`id`, `title`, `description`, `file_path`, `file_type`, `file_size`, `icon`, `category_id`, `download_count`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Document Checklist', 'Comprehensive checklist of required documents for various visa applications.', 'files/resources/resource_1744212874_1678.svg', 'other', 42061, 'fas fa-file-pdf', NULL, 0, 1, 'published', '2025-04-09 15:32:39', '2025-04-09 15:34:33'),
(2, 'Cost Calculator', 'Excel sheet to calculate immigration costs and living expenses.', 'files/resources/resource_1744213183_1976.svg', 'other', 32755, 'fas fa-file-excel', NULL, 0, 2, 'published', '2025-04-09 15:32:39', '2025-04-09 15:39:42'),
(3, 'Letter Templates', 'Templates for reference letters, statements of purpose, and more.', 'files/resources/resource_1744213195_2780.svg', 'other', 42061, 'fas fa-file-word', NULL, 0, 3, 'published', '2025-04-09 15:32:39', '2025-04-09 15:39:54');

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_categories`
--

INSERT INTO `faq_categories` (`id`, `name`, `icon`, `display_order`, `created_at`, `updated_at`) VALUES
(2, 'General Questions', 'fas fa-question-circle', 1, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(3, 'Immigration Programs', 'fas fa-passport', 2, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(4, 'Application Process', 'fas fa-file-alt', 3, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(5, 'Fees & Costs', 'fas fa-dollar-sign', 4, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(6, 'After Arrival', 'fas fa-plane-arrival', 5, '2025-04-03 07:16:49', '2025-04-03 07:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `faq_items`
--

CREATE TABLE `faq_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_items`
--

INSERT INTO `faq_items` (`id`, `category_id`, `question`, `answer`, `display_order`, `created_at`, `updated_at`) VALUES
(4, 2, 'What is Express Entry?', 'Express Entry is Canada\'s online immigration application management system for skilled workers. It manages applications for three federal economic immigration programs: Federal Skilled Worker Program, Federal Skilled Trades Program, and Canadian Experience Class.', 1, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(5, 2, 'What is the Provincial Nominee Program (PNP)?', 'The Provincial Nominee Program allows Canadian provinces and territories to nominate individuals who wish to immigrate to Canada and who are interested in settling in a particular province. Each province has its own streams and criteria for nomination.', 2, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(6, 2, 'Am I eligible for the Study Permit?', 'To be eligible for a study permit, you must have been accepted by a designated learning institution in Canada, prove you have enough money to pay for tuition fees and living expenses, and be a law-abiding citizen with no criminal record.', 3, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(7, 3, 'How long does the immigration process take?', 'Processing times vary depending on the immigration program and your country of residence. Express Entry applications typically take 6-8 months, while other programs may take longer. We provide estimated timelines based on your specific situation during our consultation.', 1, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(8, 3, 'What documents do I need for my application?', 'Required documents typically include passport, education credentials, language test results, proof of work experience, police certificates, and medical examination results. The specific requirements depend on the immigration program you\'re applying for.', 2, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(9, 3, 'Can CANEXT help with document translation?', 'Yes, we offer document translation services for all immigration-related documents. All translations are done by certified translators as required by Immigration, Refugees and Citizenship Canada (IRCC).', 3, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(10, 4, 'What are your service fees?', 'Our service fees vary depending on the complexity of your case and the services required. We provide transparent fee structures during our initial consultation, with no hidden costs. Payment plans are available to suit your financial situation.', 1, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(11, 4, 'What government fees are required?', 'Government fees vary by immigration program. For Express Entry, the main fees include the application fee ($825 CAD), right of permanent residence fee ($500 CAD), and biometrics fee ($85 CAD). Additional fees may apply for family members.', 2, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(12, 4, 'Do you offer refunds?', 'We offer partial refunds if you cancel our services before we begin working on your case. Once we have started the application process, refunds are provided on a pro-rated basis for work not yet completed. Full details are outlined in our service agreement.', 3, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(13, 5, 'What should I do when I first arrive in Canada?', 'Upon arrival, you should apply for a Social Insurance Number (SIN), health card, open a bank account, find accommodation, and familiarize yourself with local transportation. We provide a comprehensive arrival guide to all our clients.', 1, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(14, 5, 'How can I find housing in Canada?', 'We can help you connect with reliable real estate agents, provide information on rental websites, and offer guidance on neighborhoods based on your preferences and budget.', 2, '2025-04-03 07:16:49', '2025-04-03 07:16:49'),
(15, 5, 'What settlement services are available to newcomers?', 'Canada offers numerous settlement services, including language classes, employment assistance, housing help, and community integration programs. Many of these services are free for permanent residents and refugees.', 3, '2025-04-03 07:16:49', '2025-04-03 07:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'CANEXT Team',
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `author`, `status`, `publish_date`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Express Entry Application Guide', 'express-entry-guide', 'Complete guide to creating and submitting your Express Entry profile.', '<h2>Understanding Express Entry</h2><p>Express Entry is an online system that manages applications for permanent residence from skilled workers. This guide walks you through the process...</p>', 'images/resources/ai-driven-sentiment-feedback-analysis.svg', 'CANEXT Team', 'published', '2025-04-09 13:52:00', '', '', '2025-04-09 13:52:05', '2025-04-09 14:34:31'),
(2, 2, 'Study Permit Application Guide', 'study-permit-guide', 'How to apply for a Canadian study permit successfully.', '<h2>Understanding Study Permits</h2><p>A study permit is a document issued by Immigration, Refugees and Citizenship Canada (IRCC) that allows foreign nationals to study at designated learning institutions (DLIs) in Canada...</p>', 'images/resources/guide2.jpg', 'CANEXT Team', 'published', '2025-04-09 13:52:05', NULL, NULL, '2025-04-09 13:52:05', '2025-04-09 13:52:05'),
(3, 3, 'Family Sponsorship Process Guide', 'family-sponsorship-guide', 'Guide to sponsoring your family members to Canada.', '<h2>Understanding Family Sponsorship</h2><p>The Family Class Sponsorship Program allows Canadian citizens and permanent residents to sponsor eligible family members to come to Canada as permanent residents...</p>', 'images/resources/guide3.jpg', 'CANEXT Team', 'published', '2025-04-09 13:52:05', NULL, NULL, '2025-04-09 13:52:05', '2025-04-09 13:52:05');

-- --------------------------------------------------------

--
-- Table structure for table `guide_categories`
--

CREATE TABLE `guide_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fas fa-book',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `guide_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guide_categories`
--

INSERT INTO `guide_categories` (`id`, `name`, `slug`, `icon`, `display_order`, `guide_count`, `created_at`, `updated_at`) VALUES
(1, 'Immigration Programs', 'immigration-programs', 'fas fa-passport', 1, 1, '2025-04-09 13:52:05', '2025-04-09 14:34:31'),
(2, 'Student Visas', 'student-visas', 'fas fa-graduation-cap', 2, 0, '2025-04-09 13:52:05', '2025-04-09 13:52:05'),
(3, 'Family Sponsorship', 'family-sponsorship', 'fas fa-users', 3, 0, '2025-04-09 13:52:05', '2025-04-09 13:52:05');

-- --------------------------------------------------------

--
-- Table structure for table `guide_downloads`
--

CREATE TABLE `guide_downloads` (
  `id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_articles`
--

CREATE TABLE `news_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `publish_date` datetime NOT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_articles`
--

INSERT INTO `news_articles` (`id`, `title`, `slug`, `image`, `excerpt`, `content`, `publish_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Breaking News: Canada Introduces New Immigration Reforms for 2025', 'breaking-news-canada-introduces-new-immigration-reforms-for-2025', 'breaking-news-canada-introduces-new-immigration-reforms-for-2025-1743658318.png', 'Ottawa, Canada', 'The Canadian government has announced a series of new immigration reforms set to take effect in early 2025. These changes aim to streamline the immigration process, attract skilled workers, and address labor shortages in key industries.\r\n\r\nKey Highlights of the New Reforms\r\n✅ Faster Processing for Express Entry\r\nThe processing time for Express Entry applications will be reduced from six months to four months, allowing skilled workers to receive their permanent residency (PR) status more quickly.\r\n\r\n✅ Expanded Provincial Nominee Program (PNP)\r\nProvinces will receive an increased quota for selecting immigrants under the PNP, giving regions more control over addressing their specific labor market needs.\r\n\r\n✅ New Pathway for International Students\r\nA special PR pathway will be introduced for international students who graduate from Canadian institutions and secure full-time employment in Canada.\r\n\r\n✅ Enhanced Family Sponsorship Program\r\nFamily reunification will be prioritized with faster processing times for spousal and parental sponsorship applications, reducing wait times from two years to just one year.\r\n\r\nGovernment\'s Vision for Immigration\r\nCanada\'s Immigration Minister, [Minister's Name], emphasized the importance of these reforms, stating:\r\n\"Immigration is the backbone of Canada's economic growth. These new policies will ensure that skilled workers, international students, and families can contribute to Canada's success while benefiting from new opportunities.\"', '2025-04-03 00:00:00', 'published', '2025-04-03 05:31:58', '2025-04-03 05:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `payment_records`
--

CREATE TABLE `payment_records` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_group` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `created_at`, `updated_at`) VALUES
('advance_booking_days', '60', 'booking', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('booking_interval', '30', 'booking', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('buffer_time', '15', 'booking', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('business_hours', 'Mon-Fri: 9am-5pm', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('in_person_consultation_price', '200', 'payment', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('phone_consultation_price', '120', 'payment', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('site_address', '2233 Argentina Rd, Mississauga ON L5N 2X7, Canada', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('site_email', 'info@canext.com', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('site_name', 'CANEXT Immigration Consultancy', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('site_phone', '+1 (647) 226-7436', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('timezone', 'America/Toronto', 'general', '2025-04-03 04:30:27', '2025-04-03 04:30:27'),
('video_consultation_price', '150', 'payment', '2025-04-03 04:30:27', '2025-04-03 04:30:27');

-- --------------------------------------------------------

--
-- Table structure for table `time_off`
--

CREATE TABLE `time_off` (
  `id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_tutorials`
--

CREATE TABLE `video_tutorials` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_tutorials`
--

INSERT INTO `video_tutorials` (`id`, `title`, `description`, `video_url`, `thumbnail`, `category_id`, `duration`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CRS Score Calculator Tutorial', 'Learn how to calculate your Comprehensive Ranking System score.', 'https://www.youtube.com/embed/example1', 'images/resources/video_thumbnail_1744212847_6094.png', NULL, '12:45', 1, 'published', '2025-04-09 15:32:39', '2025-04-09 15:34:06'),
(2, 'Document Checklist Review', 'Detailed walkthrough of required documents for immigration.', 'https://www.youtube.com/embed/example2', 'images/resources/video_thumbnail_1744212972_8011.png', NULL, '15:30', 2, 'published', '2025-04-09 15:32:39', '2025-04-09 15:36:11'),
(3, 'Interview Preparation', 'Tips and strategies for immigration interviews.', 'https://www.youtube.com/embed/example3', 'images/resources/video_thumbnail_1744213225_3886.png', NULL, '18:22', 3, 'published', '2025-04-09 15:32:39', '2025-04-09 15:40:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `availability_schedule`
--
ALTER TABLE `availability_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_user_id` (`admin_user_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `consultants`
--
ALTER TABLE `consultants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consultant_reviews`
--
ALTER TABLE `consultant_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultant_id` (`consultant_id`);

--
-- Indexes for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `admin_user_id` (`admin_user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `day_consultation_availability`
--
ALTER TABLE `day_consultation_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consultant_day` (`consultant_id`,`day_of_week`);

--
-- Indexes for table `downloadable_resources`
--
ALTER TABLE `downloadable_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_items`
--
ALTER TABLE `faq_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `guide_categories`
--
ALTER TABLE `guide_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `guide_downloads`
--
ALTER TABLE `guide_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `news_articles`
--
ALTER TABLE `news_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `payment_records`
--
ALTER TABLE `payment_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `time_off`
--
ALTER TABLE `time_off`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_user_id` (`admin_user_id`);

--
-- Indexes for table `video_tutorials`
--
ALTER TABLE `video_tutorials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `availability_schedule`
--
ALTER TABLE `availability_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `consultants`
--
ALTER TABLE `consultants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `consultant_reviews`
--
ALTER TABLE `consultant_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `day_consultation_availability`
--
ALTER TABLE `day_consultation_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `downloadable_resources`
--
ALTER TABLE `downloadable_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faq_items`
--
ALTER TABLE `faq_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guide_categories`
--
ALTER TABLE `guide_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guide_downloads`
--
ALTER TABLE `guide_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_articles`
--
ALTER TABLE `news_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_off`
--
ALTER TABLE `time_off`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_tutorials`
--
ALTER TABLE `video_tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availability_schedule`
--
ALTER TABLE `availability_schedule`
  ADD CONSTRAINT `availability_schedule_ibfk_1` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultant_reviews`
--
ALTER TABLE `consultant_reviews`
  ADD CONSTRAINT `consultant_reviews_ibfk_1` FOREIGN KEY (`consultant_id`) REFERENCES `consultants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultation_notes`
--
ALTER TABLE `consultation_notes`
  ADD CONSTRAINT `consultation_notes_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_notes_ibfk_2` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `day_consultation_availability`
--
ALTER TABLE `day_consultation_availability`
  ADD CONSTRAINT `day_consultation_availability_ibfk_1` FOREIGN KEY (`consultant_id`) REFERENCES `consultants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `downloadable_resources`
--
ALTER TABLE `downloadable_resources`
  ADD CONSTRAINT `downloadable_resources_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faq_items`
--
ALTER TABLE `faq_items`
  ADD CONSTRAINT `faq_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `faq_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guides`
--
ALTER TABLE `guides`
  ADD CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guide_downloads`
--
ALTER TABLE `guide_downloads`
  ADD CONSTRAINT `guide_downloads_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_records`
--
ALTER TABLE `payment_records`
  ADD CONSTRAINT `payment_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_off`
--
ALTER TABLE `time_off`
  ADD CONSTRAINT `time_off_ibfk_1` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_tutorials`
--
ALTER TABLE `video_tutorials`
  ADD CONSTRAINT `video_tutorials_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `guide_categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
