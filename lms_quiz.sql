-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 09:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(4, 'ademola', 'ded029ad9ca9bedbb6f149ccfe32c4a1', 'your_email@example.com', '2025-08-09 04:30:37'),
(6, 'admin', '$2y$10$2NNznNsWiw1uzZhJiu8TOexK6f/Cmo0paN8FvMueOIbHS9lQQbR2W', 'admin@example.com', '2025-08-10 09:05:28');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` char(1) DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL COMMENT 'Time limit for this question in seconds (NULL = use quiz default)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `time_limit`) VALUES
(6, 3, 'During a Sprint, the Product Owner discovers that a major change in market conditions has rendered the current Sprint Goal completely obsolete. What is the *most appropriate* action according to the Scrum Guide?', 'The Product Owner must immediately cancel the Sprint, and any incomplete Product Backlog Items are discarded.', 'The Product Owner can cancel the Sprint, and completed \"Done\" Product Backlog items are reviewed, while incomplete ones are re-estimated and returned to the Product Backlog.', 'The Scrum Master should facilitate a meeting between the Product Owner and Development Team to renegotiate the Sprint Goal, avoiding cancellation if possible.', 'The Development Team, upon realizing the Sprint Goal is obsolete, should inform the Product Owner and collectively decide to cancel the Sprint.', 'b', NULL),
(7, 3, 'Which of the following statements about the Sprint Retrospective is TRUE according to the Scrum Guide?', 'It is an informal meeting where the Increment is demonstrated to gather feedback for the Product Backlog.', 'It is primarily for the Development Team to plan work for the next 24 hours, focusing on progress towards the Sprint Goal.', 'It occurs immediately after the Daily Scrum and before the Sprint Review to ensure continuous improvement of the process.', 'It is an opportunity for the Scrum Team to inspect itself and create a plan for improvements to be enacted in the next Sprint.', 'd', NULL),
(8, 3, 'According to the Scrum Guide, which of the following is the Development Team *solely* responsible for during Sprint Planning?', 'Crafting the Sprint Goal collaboratively with the Product Owner.', 'Negotiating the selected Product Backlog items with the Product Owner if work volume needs adjustment.', 'Determining the number of items selected from the Product Backlog for the Sprint.', 'Inviting other people to attend the Sprint Planning to provide technical or domain advice.', 'c', NULL),
(9, 3, 'A Scrum Team is working on a product where an organizational convention dictates a specific \"Definition of Done.\" During a Sprint Retrospective, the Development Team identifies an improvement that would lead to higher product quality but requires changes to this organizational \"Definition of Done.\" What is the appropriate action based on the Scrum Guide?', 'The Development Team should unilaterally adapt their \"Definition of Done\" to include the higher quality criteria.', 'The Scrum Master should work with the organization to adjust the organizational \"Definition of Done\" to accommodate the Development Team\'s identified improvement.', 'The Development Team must follow the organizational \"Definition of Done\" as a minimum, and may expand upon it, but should not conflict with it.', 'The Scrum Team plans ways to increase product quality by improving work processes or adapting the \"Definition of Done\" *if appropriate and not in conflict with product or organizational standards*.', 'd', NULL),
(10, 3, 'The Product Owner is primarily responsible for maximizing the value of the product. Which of the following activities is the *sole responsibility* of the Product Owner regarding Product Backlog management?', 'Ensuring the Development Team understands items in the Product Backlog to the level needed.', 'Optimizing the value of the work the Development Team performs.', 'Clearly expressing Product Backlog items.', 'Ordering the items in the Product Backlog to best achieve goals and missions.', 'd', NULL),
(11, 3, 'When is a Sprint time-box fixed and unable to be shortened or lengthened?', 'Once Sprint Planning begins.', 'Once the Sprint Goal has been established.', 'Once a new Sprint starts.', 'Once the Daily Scrum begins.', 'c', NULL),
(12, 3, 'Which statement best reflects the Scrum Master\'s role in facilitating Scrum events according to the Scrum Guide?', 'The Scrum Master is responsible for running all Scrum events to ensure they are productive and stay within time-boxes.', 'The Scrum Master facilitates Scrum events only \"as requested or needed\" by the Product Owner or Development Team.', 'The Scrum Master ensures that events take place and that attendees understand their purpose and keep them within time-boxes, but the Development Team primarily conducts their own events.', 'The Scrum Master actively leads the discussion and decision-making during all Scrum events to maximize value creation.', 'c', NULL),
(13, 3, 'A Development Team is struggling to track its progress towards the Sprint Goal. According to the Scrum Guide, which artifact or event is specifically designed to provide a highly visible, real-time picture of the work the Development Team plans to accomplish during the Sprint, enabling them to manage their progress?', 'The Product Backlog, which is a living artifact that changes to identify what the product needs.', 'The Increment, which is the sum of all \"Done\" Product Backlog items and supports empiricism.', 'The Sprint Backlog, which belongs solely to the Development Team and is modified throughout the Sprint.', 'The Daily Scrum, where the Development Team inspects progress and forecasts upcoming work.', 'c', NULL),
(14, 3, 'Which of the following describes an immutable aspect of Scrum as presented in the Scrum Guide?', 'The specific tactics for using the Scrum framework, such as particular estimation techniques.', 'The roles, events, artifacts, and rules that bind them together.', 'The tools and techniques used by the Development Team to build the Increment.', 'The structure of the Daily Scrum meeting, including specific questions asked.', 'b', NULL),
(15, 3, 'What is the primary objective of limiting Sprints to one calendar month or less?', 'To minimize the need for other meetings not defined in Scrum.', 'To ensure predictability by allowing for inspection and adaptation of progress towards a Sprint Goal at least monthly and to limit financial risk.', 'To prevent the Product Owner from making changes that endanger the Sprint Goal.', 'To provide ample time for Product Backlog refinement between Sprints.', 'b', NULL),
(16, 3, 'The Scrum Guide defines three pillars of empirical process control: Transparency, Inspection, and Adaptation. Which Scrum event is *not* listed as one of the four formal events specifically for inspection and adaptation?', 'Sprint Planning', 'Daily Scrum', 'Sprint Review', 'Product Backlog Refinement', 'd', NULL),
(17, 3, 'During the Sprint, who has the authority to change the Sprint Backlog?', 'The Product Owner, in consultation with the Development Team.', 'The Scrum Master, to remove identified impediments.', 'Only the Development Team.', 'The entire Scrum Team collaboratively, to ensure the Sprint Goal is met.', 'c', NULL),
(18, 3, 'The Sprint Review is an informal meeting, not a status meeting. What is its primary intent concerning the Increment and feedback?', 'To formally approve the Increment as \"Done\" and ready for release.', 'To demonstrate the Increment to elicit feedback and foster collaboration.', 'To provide a formal status report on the Sprint\'s progress to stakeholders.', 'To allow the Development Team to present solutions to problems encountered during the Sprint without external interruption.', 'b', NULL),
(19, 3, 'What is the minimum size for a Development Team, and why is this size important according to the Scrum Guide?', 'At least 3 members, to prevent skill constraints and ensure a potentially releasable Increment.', 'At least 3 members, to ensure optimal productivity gains and sufficient interaction.', 'At least 5 members, to be large enough to complete significant work within a Sprint.', 'No less than 7 members, to provide enough coordination for complex work.', 'a', NULL),
(20, 3, 'The Scrum Master serves the Product Owner in various ways. Which of the following is *not* a specific service mentioned in the Scrum Guide for the Scrum Master to the Product Owner?', 'Helping the Scrum Team understand the need for clear and concise Product Backlog items.', 'Ensuring the Product Owner knows how to arrange the Product Backlog to maximize value.', 'Causing change that increases the productivity of the Scrum Team.', 'Finding techniques for effective Product Backlog management.', 'c', NULL),
(21, 3, 'According to the Scrum Guide, what happens to the Sprint Backlog when new work is discovered during the Sprint?', 'The new work must be added to the Product Backlog and addressed in the next Sprint.', 'The Development Team may add new work to the Sprint Backlog as they learn more about the work needed to achieve the Sprint Goal.', 'The Scrum Master must approve any additions to the Sprint Backlog to prevent scope creep.', 'The Product Owner must prioritize the new work before it can be added to the Sprint Backlog.', 'b', NULL),
(22, 3, 'Which statement about the Definition of Done is most accurate according to the Scrum Guide?', 'It is created by the Development Team at the start of each Sprint and can vary between Sprints.', 'It is a shared understanding of what work has been completed when a Product Backlog item or Increment is described as Done.', 'It is primarily the responsibility of the Product Owner to define and maintain throughout the project.', 'It only applies to the final Increment at the end of each Sprint, not individual Product Backlog items.', 'b', NULL),
(23, 3, 'During Sprint Planning, the Development Team realizes they cannot complete all the Product Backlog items initially discussed. What should happen according to the Scrum Guide?', 'The Sprint Planning should be extended until a complete plan is agreed upon.', 'The Development Team and Product Owner renegotiate the selected Product Backlog items.', 'The Scrum Master should facilitate a compromise between the competing priorities.', 'The Sprint should be shortened to match the Development Team\'s capacity.', 'b', NULL),
(24, 3, 'What is the maximum recommended duration for a Daily Scrum for a one-month Sprint?', '10 minutes', '15 minutes', '30 minutes', 'It should be proportional to the Sprint length, so 8 minutes for a one-month Sprint.', 'b', NULL),
(25, 3, 'According to the Scrum Guide, who is responsible for monitoring the total work remaining in the Sprint Backlog?', 'The Scrum Master tracks this daily and reports to stakeholders.', 'The Product Owner monitors this to ensure the Sprint Goal will be met.', 'The Development Team tracks the total work remaining at least every Daily Scrum.', 'The entire Scrum Team collectively monitors this during Sprint Review.', 'c', NULL),
(26, 3, 'Which of the following best describes when Product Backlog refinement activities take place?', 'Only during designated refinement meetings scheduled between Sprints.', 'Throughout the Sprint as an ongoing process, consuming no more than 10% of Development Team capacity.', 'Primarily during Sprint Planning when items are being selected for the Sprint.', 'Only when requested by the Product Owner when new requirements emerge.', 'b', NULL),
(27, 3, 'A Scrum Team has been working together for several Sprints. The Development Team consistently delivers high-quality Increments but stakeholders complain about lack of visibility into progress. According to the Scrum Guide, what is the most appropriate solution?', 'Schedule additional status meetings between the Development Team and stakeholders.', 'Ensure the Sprint Review is being conducted properly to demonstrate the Increment and gather feedback.', 'Have the Scrum Master provide weekly progress reports to stakeholders.', 'Create detailed documentation of all work completed during each Sprint.', 'b', NULL),
(28, 3, 'What is the primary purpose of the Sprint Goal according to the Scrum Guide?', 'To provide a detailed specification of all features to be delivered in the Sprint.', 'To give the Development Team some flexibility regarding the functionality implemented within the Sprint.', 'To serve as a commitment that the Development Team makes to stakeholders.', 'To ensure all Product Backlog items selected for the Sprint are completed.', 'b', NULL),
(29, 3, 'According to the Scrum Guide, when should a potentially releasable Increment be available?', 'Only at the end of the Sprint during the Sprint Review.', 'At the end of every Sprint, regardless of whether the Product Owner decides to release it.', 'Only when all items in the Product Backlog have been completed.', 'When the Product Owner determines there is sufficient business value to warrant a release.', 'b', NULL),
(30, 3, 'Which statement about Scrum Team composition is correct according to the Scrum Guide?', 'The Scrum Team should remain unchanged throughout the entire product development.', 'Changes to Development Team membership should only occur between Sprints to minimize disruption.', 'The Scrum Team consists of a Product Owner, Development Team, and Scrum Master, with no sub-teams or hierarchies.', 'Additional specialists should be added to the Scrum Team when technical complexity increases.', 'c', NULL),
(31, 3, 'What is the recommended maximum size for a Development Team according to the Scrum Guide?', '7 members', '9 members', '12 members', '15 members', 'b', NULL),
(32, 3, 'During a Sprint Review, stakeholders request several changes to the demonstrated functionality. What is the most appropriate action according to the Scrum Guide?', 'The Product Owner should immediately update the current Sprint Backlog to incorporate the feedback.', 'The feedback should be considered for inclusion in the Product Backlog for future Sprints.', 'The Development Team should implement the changes in the remaining time of the current Sprint.', 'The changes should be documented and addressed in a separate change control process.', 'b', NULL),
(33, 3, 'According to the Scrum Guide, what is the primary accountability of the Development Team?', 'Following the technical direction provided by the Scrum Master.', 'Delivering potentially releasable Increments of Done product at the end of each Sprint.', 'Implementing all Product Backlog items exactly as specified by the Product Owner.', 'Managing and reporting on the progress of individual team members.', 'b', NULL),
(34, 3, 'Which Scrum event is specifically time-boxed to a maximum of 3 hours for a one-month Sprint?', 'Sprint Planning', 'Daily Scrum', 'Sprint Review', 'Sprint Retrospective', 'd', NULL),
(35, 3, 'What happens if the Development Team cannot complete all Sprint Backlog items during a Sprint?', 'The Sprint is automatically extended until all items are completed.', 'Incomplete items are returned to the Product Backlog and re-estimated.', 'The Development Team must work overtime to complete all committed items.', 'The Scrum Master escalates the issue to management for resource allocation.', 'b', NULL),
(36, 3, 'According to the Scrum Guide, who has the final decision on the order of items in the Product Backlog?', 'The Development Team based on technical dependencies', 'The Scrum Master to optimize team productivity', 'The Product Owner to maximize product value', 'The stakeholders based on business priorities', 'c', NULL),
(37, 3, 'What is the main difference between the Product Backlog and Sprint Backlog according to the Scrum Guide?', 'Product Backlog contains features, Sprint Backlog contains tasks', 'Product Backlog is managed by the Product Owner, Sprint Backlog by the Development Team', 'Product Backlog is high-level, Sprint Backlog is detailed', 'All of the above are correct', 'd', NULL),
(38, 3, 'During which Scrum event does the Development Team commit to delivering specific Product Backlog items?', 'Sprint Planning', 'Daily Scrum', 'Sprint Review', 'The Scrum Guide does not use the term commit in this context', 'd', NULL),
(39, 3, 'What is the purpose of having cross-functional Development Teams according to the Scrum Guide?', 'To reduce the need for external dependencies and have all skills necessary to create a product Increment', 'To ensure each team member has multiple skills and can work on any task', 'To eliminate the need for specialized roles within the organization', 'To create competition between different functional areas', 'a', NULL),
(40, 3, 'According to the Scrum Guide, what should happen if impediments cannot be resolved by the Development Team?', 'The Sprint should be cancelled immediately', 'The Scrum Master should work to remove the impediments', 'The impediments should be escalated to management', 'Work should continue on other Sprint Backlog items', 'b', NULL),
(41, 3, 'Which statement about empiricism in Scrum is most accurate according to the Scrum Guide?', 'It requires complete transparency of all organizational processes', 'It is based on the three pillars of transparency, inspection, and adaptation', 'It eliminates the need for planning since decisions are made based on observation', 'It only applies to technical aspects of product development', 'b', NULL),
(42, 3, 'What is the primary focus of the Daily Scrum according to the Scrum Guide?', 'Reporting status to the Scrum Master', 'Planning work for the next 24 hours and inspecting progress toward the Sprint Goal', 'Discussing impediments with the entire Scrum Team', 'Reviewing completed work from the previous day', 'b', NULL),
(43, 3, 'According to the Scrum Guide, when can the composition of the Development Team be changed?', 'Only at the start of a new project', 'Only between Sprints to avoid disrupting the current Sprint', 'Anytime, but membership changes may affect team productivity', 'Only with approval from the Product Owner', 'c', NULL),
(44, 3, 'What is the maximum time-box for Sprint Planning in a one-month Sprint according to the Scrum Guide?', '4 hours', '6 hours', '8 hours', '12 hours', 'c', NULL),
(45, 3, 'According to the Scrum Guide, what is the primary way the Scrum Master serves the Development Team?', 'By assigning tasks to individual team members', 'By removing impediments to the Development Team\'s progress', 'By making technical decisions for the team', 'By reporting team progress to management', 'b', NULL),
(46, 3, 'Which statement about the Increment is correct according to the Scrum Guide?', 'It must be released to production at the end of every Sprint', 'It is the sum of all Product Backlog items completed during a Sprint plus previous Increments', 'It only needs to meet the Definition of Done if it will be released', 'It can only be created by the Development Team working together as a whole', 'b', NULL),
(47, 3, 'What should the Development Team do if they identify a way to improve their productivity during a Sprint?', 'Wait until the Sprint Retrospective to discuss the improvement', 'Implement the improvement immediately if it doesn\'t affect the Sprint Goal', 'Get approval from the Scrum Master before implementing any changes', 'Document the improvement for the next Sprint Planning', 'b', NULL),
(48, 3, 'According to the Scrum Guide, what is the relationship between the Definition of Done and acceptance criteria?', 'They are the same thing with different names', 'The Definition of Done is broader and applies to all work, while acceptance criteria are specific to individual items', 'Acceptance criteria replace the need for a Definition of Done', 'The Scrum Guide does not mention acceptance criteria', 'd', NULL),
(49, 3, 'What is the primary purpose of Sprint time-boxing according to the Scrum Guide?', 'To create pressure for faster delivery', 'To enable predictability and limit risk', 'To provide equal time for all planned activities', 'To align with organizational reporting cycles', 'b', NULL),
(50, 3, 'According to the Scrum Guide, who is responsible for ensuring Scrum is understood and enacted?', 'The Product Owner', 'The Development Team', 'The Scrum Master', 'The entire Scrum Team', 'c', NULL),
(51, 3, 'According to the Scrum Guide, what is the primary characteristic that distinguishes Scrum from other agile methodologies?', 'Its focus on iterative development', 'Its empirical approach based on transparency, inspection, and adaptation', 'Its emphasis on customer collaboration', 'Its use of cross-functional teams', 'b', NULL),
(52, 3, 'When multiple Scrum Teams work on the same product, what must they share according to the Scrum Guide?', 'The same Sprint length', 'The same Definition of Done', 'The same Sprint Goal', 'The same Scrum Master', 'b', NULL),
(53, 3, 'What is the Scrum Master\'s primary responsibility when organizational impediments affect the Scrum Team?', 'Escalate to senior management immediately', 'Work with the organization to remove these impediments', 'Shield the team from organizational issues', 'Document impediments for future reference', 'b', NULL),
(54, 3, 'According to the Scrum Guide, what happens to undone work when a Sprint is cancelled?', 'It is discarded and must be re-estimated', 'It is automatically moved to the next Sprint', 'It is reviewed and any releasable work is identified', 'It becomes the responsibility of the Product Owner to complete', 'c', NULL),
(55, 3, 'Which statement about self-organization in Scrum Teams is most accurate?', 'Teams can choose which Scrum events to follow based on their needs', 'Teams organize around how to best accomplish their work rather than being directed by others outside the team', 'Teams can modify Scrum roles to fit their organizational structure', 'Teams are completely autonomous and require no external input', 'b', NULL),
(56, 3, 'According to the Scrum Guide, what is the main purpose of maintaining transparency in Scrum?', 'To provide complete visibility to all stakeholders', 'To enable proper inspection of Scrum artifacts', 'To eliminate the need for documentation', 'To ensure accountability of team members', 'b', NULL),
(57, 3, 'What does the Scrum Guide say about the size of Product Backlog items selected for a Sprint?', 'They should all be of similar size for easier estimation', 'They should be small enough to be completed within one Sprint', 'They can be of any size as long as the Sprint Goal is achievable', 'They should be broken down to no more than one day of work', 'b', NULL),
(58, 3, 'According to the Scrum Guide, who determines the technical practices used by the Development Team?', 'The Scrum Master based on industry best practices', 'The Product Owner based on product requirements', 'The Development Team chooses their own technical practices', 'The organization\'s technical leadership', 'c', NULL),
(59, 3, 'What is the primary purpose of the three Scrum roles according to the Scrum Guide?', 'To provide clear reporting hierarchies', 'To ensure accountability for different aspects of the product development', 'To separate technical work from business work', 'To distribute work evenly among team members', 'b', NULL),
(60, 3, 'According to the Scrum Guide, when should the Sprint Backlog be updated?', 'Only during Daily Scrum meetings', 'Only during Sprint Planning', 'As new work is discovered throughout the Sprint', 'Only at the end of the Sprint during Sprint Review', 'c', NULL),
(61, 3, 'What does the Scrum Guide say about dependencies between Scrum Teams working on the same product?', 'Dependencies should be eliminated entirely', 'Dependencies should be managed through a formal dependency board', 'Dependencies should be minimized and managed by the teams themselves', 'Dependencies should be escalated to the Product Owner for resolution', 'c', NULL),
(62, 3, 'According to the Scrum Guide, what is the maximum duration for a Sprint Review in a four-week Sprint?', '2 hours', '3 hours', '4 hours', '6 hours', 'c', NULL),
(63, 3, 'What should happen if the Definition of Done for an Increment is not a convention of the development organization?', 'The Scrum Team must create their own Definition of Done', 'The Product Owner defines it for the team', 'The Scrum Master facilitates its creation', 'The Development Team must adopt industry standards', 'a', NULL),
(64, 3, 'According to the Scrum Guide, what is the primary benefit of having potentially releasable Increments?', 'It reduces development costs', 'It provides flexibility for the Product Owner to release when appropriate', 'It eliminates the need for separate testing phases', 'It ensures faster time to market', 'b', NULL),
(65, 3, 'What does the Scrum Guide say about technical debt?', 'It should be avoided at all costs', 'It should be tracked separately from the Product Backlog', 'It is addressed through the Definition of Done and Product Backlog items', 'It is the sole responsibility of the Development Team to manage', 'c', NULL),
(66, 3, 'According to the Scrum Guide, what happens if a Development Team member is consistently unavailable for Daily Scrums?', 'The team should meet individually with that member', 'The Scrum Master should address this with the team member', 'This threatens the benefits of the Daily Scrum to the Development Team', 'The team should adjust the Daily Scrum time to accommodate everyone', 'c', NULL),
(67, 3, 'What is the Scrum Master\'s role when conflicts arise within the Development Team?', 'Make decisions to resolve conflicts quickly', 'Facilitate resolution but let the team work it out', 'Escalate to management for resolution', 'Document conflicts for performance reviews', 'b', NULL),
(68, 3, 'According to the Scrum Guide, what is the relationship between the Sprint Goal and Product Backlog items in the Sprint?', 'All Sprint Backlog items must directly contribute to the Sprint Goal', 'The Sprint Goal is derived from the selected Product Backlog items', 'Product Backlog items are more important than the Sprint Goal', 'There is no required relationship between them', 'b', NULL),
(69, 3, 'What does the Scrum Guide say about estimating Product Backlog items?', 'Estimates must be provided in story points', 'The Development Team provides estimates when requested by the Product Owner', 'Estimates are mandatory for all Product Backlog items', 'Only the Product Owner can estimate Product Backlog items', 'b', NULL),
(70, 3, 'According to the Scrum Guide, what should guide the Development Team\'s decisions about their work processes?', 'Industry best practices', 'Organizational standards', 'What works best for them to achieve the Sprint Goal', 'Direction from the Scrum Master', 'c', NULL),
(71, 3, 'What is the primary purpose of the Sprint according to the Scrum Guide?', 'To provide a fixed time period for planning', 'To create a potentially releasable product Increment', 'To allow for regular inspection of team performance', 'To establish a rhythm for stakeholder communication', 'b', NULL),
(72, 3, 'According to the Scrum Guide, what should happen if the Development Team realizes during a Sprint that they have selected too much work?', 'Continue working and deliver whatever is possible', 'Extend the Sprint to complete all selected work', 'Collaborate with the Product Owner to adjust the Sprint scope', 'Remove the least important items unilaterally', 'c', NULL),
(73, 3, 'What does the Scrum Guide say about the format of the Daily Scrum?', 'It must follow the three-question format', 'The format is set by the Development Team', 'The Scrum Master determines the most effective format', 'It should rotate between different formats', 'b', NULL),
(74, 3, 'According to the Scrum Guide, what is the primary focus of Sprint Planning?', 'Estimating all Product Backlog items', 'Creating a plan for delivering the product Increment', 'Assigning tasks to individual team members', 'Reviewing the previous Sprint\'s performance', 'b', NULL),
(75, 3, 'What should the Scrum Master do if the Development Team asks for help with technical practices?', 'Provide specific technical guidance', 'Help them find appropriate techniques and practices', 'Direct them to use organizational standards', 'This is outside the Scrum Master\'s responsibilities', 'b', NULL),
(76, 3, 'According to the Scrum Guide, what is the primary purpose of Sprint Planning\'s first part?', 'To estimate Product Backlog items', 'To select Product Backlog items for the Sprint and craft the Sprint Goal', 'To assign work to team members', 'To review the Definition of Done', 'b', NULL),
(77, 3, 'What does the Scrum Guide say about quality in the context of the Definition of Done?', 'Quality requirements should be tracked separately', 'Quality is ensured through the Definition of Done', 'Quality is primarily the Product Owner\'s responsibility', 'Quality standards should be defined by the organization', 'b', NULL),
(78, 3, 'According to the Scrum Guide, when should Product Backlog items be considered ready for Sprint Planning?', 'When they have been estimated by the Development Team', 'When they are refined to an appropriate level of detail', 'When they have been approved by all stakeholders', 'When they have been prioritized by the Product Owner', 'b', NULL),
(79, 3, 'What is the Scrum Master\'s primary accountability regarding Scrum adoption in the organization?', 'Training all employees on Scrum', 'Ensuring all teams follow Scrum perfectly', 'Leading the organization in Scrum adoption and helping employees understand Scrum', 'Auditing teams for Scrum compliance', 'c', NULL),
(80, 3, 'According to the Scrum Guide, what should happen if stakeholders want to change priorities during a Sprint?', 'Changes should be implemented immediately', 'Changes should wait until the next Sprint', 'The Product Owner makes the final decision about mid-Sprint changes', 'The Scrum Master should facilitate a discussion about the changes', 'b', NULL),
(81, 3, 'What does the Scrum Guide say about the Development Team\'s interaction with stakeholders?', 'All interaction must go through the Product Owner', 'The Development Team can interact directly with stakeholders during the Sprint Review', 'The Development Team should have no direct stakeholder contact', 'Stakeholder interaction should be managed by the Scrum Master', 'b', NULL),
(82, 3, 'According to the Scrum Guide, what is the primary benefit of cross-functional Development Teams?', 'Reduced personnel costs', 'Minimized dependencies and faster delivery', 'Better specialization of skills', 'Improved individual performance', 'b', NULL),
(83, 3, 'What should happen according to the Scrum Guide if the Product Owner is consistently unavailable to the Development Team?', 'The Scrum Master should take over Product Owner responsibilities', 'The Development Team should make product decisions independently', 'This threatens the success of the Scrum implementation', 'The team should continue with their best understanding', 'c', NULL),
(84, 3, 'According to the Scrum Guide, what is the primary purpose of inspection in Scrum?', 'To evaluate team performance', 'To detect undesirable variances from goals', 'To ensure compliance with processes', 'To identify individual accountability', 'b', NULL),
(85, 3, 'What does the Scrum Guide say about the Development Team\'s authority over their internal processes?', 'They must follow organizational development standards', 'They have complete autonomy over how they organize their work', 'Their processes must be approved by the Scrum Master', 'They should use industry best practices', 'b', NULL),
(86, 3, 'According to the Scrum Guide, what is the relationship between Scrum values and Scrum success?', 'Values are optional guidelines for better teamwork', 'Success is possible without embodying Scrum values but is more difficult', 'Scrum values must be embodied and lived by the team for Scrum to be successful', 'Values are only important for the Scrum Master role', 'c', NULL),
(87, 3, 'What should the Development Team do if they discover that the Definition of Done is inadequate during a Sprint?', 'Continue with the current Definition of Done until the Sprint ends', 'Update the Definition of Done immediately', 'Discuss improvements in the next Sprint Retrospective', 'Escalate to the Scrum Master for guidance', 'c', NULL),
(88, 3, 'According to the Scrum Guide, what is the primary purpose of adaptation in empirical process control?', 'To respond to changing requirements', 'To adjust when inspection reveals aspects outside acceptable limits', 'To continuously improve team performance', 'To align with organizational changes', 'b', NULL),
(89, 3, 'What does the Scrum Guide say about the duration consistency of Sprints?', 'Sprints should have consistent duration throughout a project', 'Sprint duration can vary based on the amount of work', 'Each Sprint can have a different duration based on complexity', 'Sprint duration should be determined by stakeholders', 'a', NULL),
(90, 3, 'According to the Scrum Guide, what should guide the Product Owner\'s decisions about Product Backlog ordering?', 'Technical dependencies identified by the Development Team', 'Stakeholder requests and business priorities', 'Value maximization and goal achievement', 'Risk mitigation and compliance requirements', 'c', NULL),
(91, 3, 'What is the Scrum Master\'s role regarding team conflicts according to the Scrum Guide?', 'Resolve conflicts by making final decisions', 'Coach the team in conflict resolution and self-organization', 'Report conflicts to management', 'Ignore conflicts as they will resolve naturally', 'b', NULL),
(92, 3, 'According to the Scrum Guide, what should happen if the Sprint Goal becomes unclear during the Sprint?', 'The Sprint should be cancelled immediately', 'The Development Team should clarify the goal with the Product Owner', 'Continue the Sprint and clarify in the Sprint Review', 'The Scrum Master should redefine the Sprint Goal', 'b', NULL),
(93, 3, 'What does the Scrum Guide say about scaling Scrum to multiple teams?', 'It provides detailed scaling frameworks', 'Multiple teams should share certain elements like Definition of Done', 'Scaling is not addressed in the Scrum Guide', 'Each team should operate completely independently', 'b', NULL),
(94, 3, 'According to the Scrum Guide, what is the primary characteristic of a good Sprint Goal?', 'It describes all the work to be done in detail', 'It provides flexibility while giving direction to the Development Team', 'It is easily measurable and quantifiable', 'It aligns with long-term strategic objectives', 'b', NULL),
(95, 3, 'What should the Scrum Master do if organizational policies conflict with Scrum practices?', 'Enforce organizational policies over Scrum', 'Work with the organization to resolve conflicts and improve Scrum adoption', 'Ignore organizational policies', 'Document conflicts for management review', 'b', NULL),
(96, 3, 'According to the Scrum Guide, what is the primary responsibility of the entire Scrum Team?', 'Delivering working software', 'Creating valuable products through collaboration and self-organization', 'Following Scrum processes perfectly', 'Meeting all stakeholder expectations', 'b', NULL),
(97, 3, 'What does the Scrum Guide say about the Product Owner\'s availability to the Development Team?', 'The Product Owner should be available at all times', 'The Product Owner should be available during Sprint Planning and Sprint Review', 'The Product Owner should be available as needed throughout the Sprint', 'Availability requirements are not specified in the Scrum Guide', 'c', NULL),
(98, 3, 'According to the Scrum Guide, what should happen if a Development Team consistently fails to deliver a potentially releasable Increment?', 'The team composition should be changed', 'The Scrum Master should investigate and address underlying issues', 'The Sprint length should be increased', 'The Definition of Done should be relaxed', 'b', NULL),
(99, 3, 'What is the primary purpose of the Scrum framework according to the Scrum Guide?', 'To provide detailed project management processes', 'To support teams in complex product development', 'To ensure predictable delivery schedules', 'To standardize software development practices', 'b', NULL),
(100, 3, 'According to the Scrum Guide, what should guide decisions about when to release the Increment?', 'Technical readiness of the product', 'Market conditions and business value', 'Completion of all planned features', 'Stakeholder approval', 'b', NULL),
(101, 3, 'What does the Scrum Guide say about the Development Team\'s commitment during Sprint Planning?', 'They commit to completing all selected Product Backlog items', 'They forecast what they believe they can deliver', 'They promise to work a certain number of hours', 'They guarantee a specific delivery date', 'b', NULL),
(102, 3, 'According to the Scrum Guide, what is the most important factor in Scrum Team success?', 'Following all Scrum practices perfectly', 'Having experienced team members', 'The team members\' commitment to achieving the goals', 'Using the right tools and technologies', 'c', NULL),
(103, 3, 'What should happen if the Development Team identifies that they need additional skills to complete Sprint work?', 'Wait until the next Sprint to address skill gaps', 'Add specialists to the team immediately', 'Work with available skills and identify learning opportunities', 'Cancel the Sprint due to insufficient capability', 'c', NULL),
(104, 3, 'According to the Scrum Guide, what is the primary benefit of timeboxing Scrum events?', 'It creates urgency for decision making', 'It ensures focus and prevents events from consuming too much time', 'It provides predictable scheduling for stakeholders', 'It allows for better resource allocation', 'b', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `time_per_question` int(11) DEFAULT 60,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allow_skip` tinyint(1) DEFAULT 1,
  `allow_navigation` tinyint(1) DEFAULT 0,
  `shuffle_questions` tinyint(1) DEFAULT 0,
  `show_results` tinyint(1) DEFAULT 1,
  `pass_percentage` decimal(5,2) DEFAULT 60.00,
  `attempts_allowed` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `allow_question_navigation` tinyint(1) DEFAULT 1 COMMENT 'Allow clicking navigation dots',
  `questions_to_show` int(11) DEFAULT NULL COMMENT 'Number of questions to show (NULL = show all)',
  `allow_question_selection` tinyint(1) DEFAULT 0 COMMENT 'Allow users to choose number of questions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `time_per_question`, `created_at`, `allow_skip`, `allow_navigation`, `shuffle_questions`, `show_results`, `pass_percentage`, `attempts_allowed`, `is_active`, `allow_question_navigation`, `questions_to_show`, `allow_question_selection`) VALUES
(3, 'SCRUM master', 'PSM Examination', 60, '2025-08-09 04:40:25', 1, 1, 1, 1, 60.00, 1, 1, 1, NULL, 0),
(4, 'SCRUM 2', '', 120, '2025-08-09 05:43:30', 1, 1, 1, 1, 85.00, 1, 1, 1, NULL, 1),
(5, 'PUBLIC Policy 101 questions', '', 60, '2025-08-10 00:08:46', 1, 0, 1, 1, 60.00, 1, 1, 1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_answer` char(1) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `time_taken` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `attempt_id`, `question_id`, `selected_answer`, `is_correct`, `time_taken`) VALUES
(75, 19, 23, NULL, 0, 30),
(76, 19, 49, NULL, 0, 30),
(77, 19, 92, 'b', 1, 30),
(78, 19, 75, 'b', 1, 30),
(79, 19, 99, 'c', 0, 30),
(80, 19, 13, NULL, 0, 30),
(81, 19, 40, NULL, 0, 30),
(82, 19, 43, NULL, 0, 30),
(83, 19, 100, NULL, 0, 30),
(84, 19, 27, NULL, 0, 30),
(85, 19, 94, NULL, 0, 30),
(86, 19, 78, NULL, 0, 30),
(87, 19, 71, NULL, 0, 30),
(88, 19, 55, NULL, 0, 30),
(89, 19, 42, NULL, 0, 30),
(90, 19, 10, NULL, 0, 30),
(91, 19, 21, NULL, 0, 30),
(92, 19, 39, NULL, 0, 30),
(93, 19, 101, NULL, 0, 30),
(94, 19, 8, NULL, 0, 30),
(95, 19, 90, NULL, 0, 30),
(96, 19, 76, NULL, 0, 30),
(97, 19, 63, NULL, 0, 30),
(98, 19, 95, NULL, 0, 30),
(99, 19, 30, NULL, 0, 30),
(100, 19, 61, NULL, 0, 30),
(101, 19, 38, NULL, 0, 30),
(102, 19, 16, NULL, 0, 30),
(103, 19, 12, NULL, 0, 30),
(104, 19, 60, NULL, 0, 30),
(105, 19, 69, NULL, 0, 30),
(106, 19, 32, NULL, 0, 30),
(107, 19, 82, NULL, 0, 30),
(108, 19, 83, NULL, 0, 30),
(109, 19, 52, NULL, 0, 30),
(110, 19, 9, NULL, 0, 30),
(111, 19, 85, NULL, 0, 30),
(112, 19, 68, NULL, 0, 30),
(113, 19, 104, NULL, 0, 30),
(114, 19, 33, NULL, 0, 30),
(115, 19, 88, NULL, 0, 30),
(116, 19, 59, NULL, 0, 30),
(117, 19, 62, NULL, 0, 30),
(118, 19, 36, NULL, 0, 30),
(119, 19, 31, NULL, 0, 30),
(120, 19, 65, NULL, 0, 30),
(121, 19, 48, NULL, 0, 30),
(122, 19, 70, NULL, 0, 30),
(123, 19, 98, NULL, 0, 30),
(124, 19, 103, NULL, 0, 30),
(125, 19, 35, NULL, 0, 30),
(126, 19, 20, NULL, 0, 30),
(127, 19, 28, NULL, 0, 30),
(128, 19, 79, NULL, 0, 30),
(129, 19, 72, NULL, 0, 30),
(130, 19, 93, NULL, 0, 30),
(131, 19, 17, NULL, 0, 30),
(132, 19, 45, NULL, 0, 30),
(133, 19, 22, NULL, 0, 30),
(134, 19, 14, NULL, 0, 30),
(135, 19, 102, NULL, 0, 30),
(136, 19, 15, NULL, 0, 30),
(137, 19, 24, NULL, 0, 30),
(138, 19, 66, NULL, 0, 30),
(139, 19, 18, NULL, 0, 30),
(140, 19, 46, NULL, 0, 30),
(141, 19, 51, NULL, 0, 30),
(142, 19, 96, NULL, 0, 30),
(143, 19, 19, NULL, 0, 30),
(144, 19, 47, NULL, 0, 30),
(145, 19, 89, NULL, 0, 30),
(146, 19, 54, NULL, 0, 30),
(147, 19, 84, NULL, 0, 30),
(148, 19, 97, NULL, 0, 30),
(149, 19, 56, NULL, 0, 30),
(150, 19, 6, NULL, 0, 30),
(151, 19, 37, NULL, 0, 30),
(152, 19, 74, NULL, 0, 30),
(153, 19, 87, NULL, 0, 30),
(154, 19, 53, NULL, 0, 30),
(155, 19, 77, NULL, 0, 30),
(156, 19, 11, NULL, 0, 30),
(157, 19, 81, NULL, 0, 30),
(158, 19, 86, NULL, 0, 30),
(159, 19, 64, NULL, 0, 30),
(160, 19, 73, NULL, 0, 30),
(161, 19, 80, NULL, 0, 30),
(162, 19, 67, NULL, 0, 30),
(163, 19, 7, NULL, 0, 30),
(164, 19, 58, NULL, 0, 30),
(165, 19, 41, NULL, 0, 30),
(166, 19, 29, NULL, 0, 30),
(167, 19, 34, NULL, 0, 30),
(168, 19, 91, NULL, 0, 30),
(169, 19, 44, NULL, 0, 30),
(170, 19, 50, NULL, 0, 30),
(171, 19, 25, NULL, 0, 30),
(172, 19, 57, NULL, 0, 30),
(173, 19, 26, NULL, 0, 30),
(174, 20, 66, 'c', 1, 30),
(175, 20, 29, 'b', 1, 30),
(176, 20, 84, 'b', 1, 30),
(177, 20, 38, 'a', 0, 30),
(178, 20, 6, 'a', 0, 30),
(179, 20, 62, 'c', 1, 30),
(180, 20, 63, 'a', 1, 30),
(181, 20, 101, 'b', 1, 30),
(182, 20, 46, 'b', 1, 30),
(183, 20, 102, 'c', 1, 30),
(184, 20, 89, 'a', 1, 30),
(185, 20, 97, 'a', 0, 30),
(186, 20, 82, 'b', 1, 30),
(187, 20, 21, 'a', 0, 30),
(188, 20, 95, 'b', 1, 30),
(189, 20, 77, 'b', 1, 30),
(190, 20, 30, NULL, 0, 30),
(191, 20, 91, NULL, 0, 30),
(192, 20, 103, NULL, 0, 30),
(193, 20, 64, NULL, 0, 30),
(194, 20, 92, NULL, 0, 30),
(195, 20, 75, NULL, 0, 30),
(196, 20, 58, NULL, 0, 30),
(197, 20, 59, NULL, 0, 30),
(198, 20, 51, NULL, 0, 30),
(199, 20, 9, NULL, 0, 30),
(200, 20, 28, NULL, 0, 30),
(201, 20, 36, NULL, 0, 30),
(202, 20, 35, NULL, 0, 30),
(203, 20, 76, NULL, 0, 30),
(204, 20, 86, NULL, 0, 30),
(205, 20, 39, NULL, 0, 30),
(206, 20, 61, NULL, 0, 30),
(207, 20, 87, NULL, 0, 30),
(208, 20, 23, NULL, 0, 30),
(209, 20, 72, NULL, 0, 30),
(210, 20, 79, NULL, 0, 30),
(211, 20, 100, NULL, 0, 30),
(212, 20, 44, NULL, 0, 30),
(213, 20, 11, NULL, 0, 30),
(214, 20, 73, NULL, 0, 30),
(215, 20, 80, NULL, 0, 30),
(216, 20, 34, NULL, 0, 30),
(217, 20, 93, NULL, 0, 30),
(218, 20, 13, NULL, 0, 30),
(219, 20, 45, NULL, 0, 30),
(220, 20, 68, NULL, 0, 30),
(221, 20, 85, NULL, 0, 30),
(222, 20, 96, NULL, 0, 30),
(223, 20, 18, NULL, 0, 30),
(224, 20, 48, NULL, 0, 30),
(225, 20, 56, NULL, 0, 30),
(226, 20, 55, NULL, 0, 30),
(227, 20, 12, NULL, 0, 30),
(228, 20, 78, NULL, 0, 30),
(229, 20, 20, NULL, 0, 30),
(230, 20, 37, NULL, 0, 30),
(231, 20, 19, NULL, 0, 30),
(232, 20, 60, NULL, 0, 30),
(233, 20, 31, NULL, 0, 30),
(234, 20, 104, NULL, 0, 30),
(235, 20, 25, NULL, 0, 30),
(236, 20, 42, NULL, 0, 30),
(237, 20, 14, NULL, 0, 30),
(238, 20, 49, NULL, 0, 30),
(239, 20, 99, NULL, 0, 30),
(240, 20, 69, NULL, 0, 30),
(241, 20, 94, NULL, 0, 30),
(242, 20, 54, NULL, 0, 30),
(243, 20, 65, NULL, 0, 30),
(244, 20, 22, NULL, 0, 30),
(245, 20, 52, NULL, 0, 30),
(246, 20, 16, NULL, 0, 30),
(247, 20, 32, NULL, 0, 30),
(248, 20, 24, NULL, 0, 30),
(249, 20, 40, NULL, 0, 30),
(250, 20, 41, NULL, 0, 30),
(251, 20, 27, NULL, 0, 30),
(252, 20, 88, NULL, 0, 30),
(253, 20, 50, NULL, 0, 30),
(254, 20, 10, NULL, 0, 30),
(255, 20, 8, NULL, 0, 30),
(256, 20, 83, NULL, 0, 30),
(257, 20, 71, NULL, 0, 30),
(258, 20, 81, NULL, 0, 30),
(259, 20, 43, NULL, 0, 30),
(260, 20, 90, NULL, 0, 30),
(261, 20, 74, NULL, 0, 30),
(262, 20, 67, NULL, 0, 30),
(263, 20, 47, NULL, 0, 30),
(264, 20, 57, NULL, 0, 30),
(265, 20, 33, NULL, 0, 30),
(266, 20, 15, NULL, 0, 30),
(267, 20, 17, NULL, 0, 30),
(268, 20, 26, NULL, 0, 30),
(269, 20, 70, NULL, 0, 30),
(270, 20, 98, NULL, 0, 30),
(271, 20, 53, NULL, 0, 30),
(272, 20, 7, NULL, 0, 30);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `attempted_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `selected_questions_count` int(11) DEFAULT NULL COMMENT 'Number of questions user chose to attempt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `user_name`, `total_questions`, `attempted_questions`, `correct_answers`, `score`, `started_at`, `completed_at`, `selected_questions_count`) VALUES
(9, 3, 'Ademola', 99, 0, 0, 0.00, '2025-08-09 05:01:19', '2025-08-09 05:13:58', NULL),
(10, 3, 'User', 99, 0, 0, 0.00, '2025-08-09 05:15:43', '2025-08-09 05:15:54', NULL),
(11, 3, 'ademola olaniyi', 99, 0, 0, 0.00, '2025-08-09 05:16:14', '2025-08-09 05:16:54', NULL),
(12, 3, 'Ademola', 99, 0, 0, 0.00, '2025-08-09 05:17:35', NULL, NULL),
(19, 3, 'ademola', 99, 3, 2, 2.02, '2025-08-09 05:32:03', '2025-08-09 05:38:54', NULL),
(20, 3, 'Ademola Abiodun', 99, 16, 12, 12.12, '2025-08-09 05:58:09', '2025-08-09 06:30:31', NULL),
(21, 3, 'ademola olaniyi', 99, 0, 0, 0.00, '2025-08-09 07:50:17', NULL, 99),
(22, 3, 'ademola olaniyi', 99, 0, 0, 0.00, '2025-08-09 07:52:15', NULL, 99),
(23, 3, 'ademola olaniyi', 99, 0, 0, 0.00, '2025-08-09 07:56:24', NULL, 99),
(24, 3, 'ademola', 99, 0, 0, 0.00, '2025-08-09 17:29:47', NULL, 99),
(25, 3, 'ademola', 99, 0, 0, 0.00, '2025-08-13 07:38:56', NULL, 99);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_title', 'LMS Quiz System', 'Website title', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(2, 'default_time_per_question', '60', 'Default time per question in seconds', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(3, 'default_pass_percentage', '60', 'Default pass percentage', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(4, 'allow_retakes', '1', 'Allow quiz retakes by default', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(5, 'max_file_size', '5242880', 'Maximum upload file size in bytes (5MB)', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(6, 'email_notifications', '0', 'Send email notifications', '2025-08-13 07:37:28', '2025-08-13 07:37:28'),
(7, 'admin_email', 'admin@example.com', 'Admin email address', '2025-08-13 07:37:28', '2025-08-13 07:37:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=273;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`),
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
