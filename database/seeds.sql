-- =====================================================
-- SEED DATA FOR LEARNING ASSISTANT
-- Version 1.0.0
-- =====================================================

USE learning_assistant;

-- =====================================================
-- SEED: Users (Admin and Test Students)
-- =====================================================

-- Admin user (password: Admin@12345)
INSERT INTO users (username, email, password_hash, first_name, last_name, role, status, email_verified) VALUES
('admin', 'admin@learningassistant.com', '$2y$10$abcdefghijklmnopqrstuvwxyzabcdefghijklmnop', 'System', 'Administrator', 'admin', 'active', TRUE);

-- Student users (password: Student@123)
INSERT INTO users (username, email, password_hash, first_name, last_name, role, status, email_verified) VALUES
('student1', 'student1@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyzabcdefghijklmnop', 'John', 'Doe', 'student', 'active', TRUE),
('student2', 'student2@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyzabcdefghijklmnop', 'Jane', 'Smith', 'student', 'active', TRUE),
('student3', 'student3@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyzabcdefghijklmnop', 'Michael', 'Johnson', 'student', 'active', TRUE);

-- =====================================================
-- SEED: Profiles
-- =====================================================

INSERT INTO profiles (user_id, bio, phone, gender, country, preferred_learning_style) VALUES
(1, 'System Administrator', '+1234567890', 'male', 'USA', 'mixed'),
(2, 'Learning enthusiast passionate about web development', '+1111111111', 'male', 'USA', 'video'),
(3, 'Interested in data science and Python', '+2222222222', 'female', 'Canada', 'text'),
(4, 'UI/UX designer learning full-stack development', '+3333333333', 'other', 'UK', 'interactive');

-- =====================================================
-- SEED: Categories
-- =====================================================

INSERT INTO categories (name, description, slug, icon, color, display_order, is_active) VALUES
('Web Development', 'Learn HTML, CSS, JavaScript, PHP and modern web frameworks', 'web-development', 'globe', '#0056b3', 1, TRUE),
('Data Science', 'Master data analysis, visualization, and machine learning concepts', 'data-science', 'graph-up', '#FF6B6B', 2, TRUE),
('Python Programming', 'Learn Python from basics to advanced programming concepts', 'python-programming', 'code', '#3776AB', 3, TRUE),
('UI/UX Design', 'Create beautiful and user-friendly digital experiences', 'ux-design', 'palette', '#A29BFE', 4, TRUE),
('Business Skills', 'Develop professional and business communication skills', 'business-skills', 'briefcase', '#00B894', 5, TRUE);

-- =====================================================
-- SEED: Courses
-- =====================================================

INSERT INTO courses (category_id, title, description, slug, difficulty_level, instructor_id, total_lessons, estimated_hours, is_published) VALUES
(1, 'Web Development Fundamentals', 'Learn the basics of HTML5, CSS3, and JavaScript for web development', 'web-dev-fundamentals', 'beginner', 1, 12, 24, TRUE),
(1, 'PHP 8 Complete Guide', 'Master PHP 8 with practical examples and projects', 'php-8-guide', 'intermediate', 1, 15, 30, TRUE),
(1, 'Responsive Web Design with Bootstrap 5', 'Create responsive websites using Bootstrap 5 framework', 'bootstrap-5-guide', 'beginner', 1, 10, 20, TRUE),
(2, 'Data Science Essentials', 'Introduction to data science and analytics', 'data-science-essentials', 'beginner', 1, 14, 28, TRUE),
(3, 'Python for Beginners', 'Start your Python journey from scratch', 'python-beginners', 'beginner', 1, 16, 32, TRUE),
(3, 'Advanced Python Programming', 'Master advanced Python concepts and design patterns', 'python-advanced', 'advanced', 1, 18, 36, TRUE),
(4, 'UI Design Principles', 'Learn fundamental principles of user interface design', 'ui-design-principles', 'beginner', 1, 8, 16, TRUE);

-- =====================================================
-- SEED: Lessons (for Web Development Fundamentals course)
-- =====================================================

INSERT INTO lessons (course_id, title, description, content, lesson_type, duration_minutes, display_order, is_published) VALUES
(1, 'Introduction to Web Development', 'Overview of web development and the technologies involved', 'Web development involves creating websites and web applications...', 'text', 15, 1, TRUE),
(1, 'HTML5 Basics', 'Learn the fundamentals of HTML5 markup', 'HTML (HyperText Markup Language) is the standard markup language...', 'video', 25, 2, TRUE),
(1, 'CSS3 Styling Basics', 'Master CSS3 for styling web pages', 'CSS (Cascading Style Sheets) is used to style HTML elements...', 'video', 30, 3, TRUE),
(1, 'JavaScript Fundamentals', 'Introduction to JavaScript programming', 'JavaScript is a powerful programming language for web development...', 'interactive', 40, 4, TRUE),
(1, 'HTML5 Forms and Validation', 'Create forms with HTML5 and client-side validation', 'HTML5 provides new form elements and attributes...', 'text', 20, 5, TRUE);

-- =====================================================
-- SEED: Quizzes (for lessons)
-- =====================================================

INSERT INTO quizzes (lesson_id, title, description, quiz_type, total_questions, passing_score, time_limit_minutes, is_published) VALUES
(1, 'Introduction Quiz', 'Test your understanding of web development basics', 'practice', 5, 60, 10, TRUE),
(2, 'HTML5 Basics Quiz', 'Verify your knowledge of HTML5 fundamentals', 'graded', 10, 70, 15, TRUE),
(3, 'CSS3 Styling Quiz', 'Test your CSS3 knowledge', 'graded', 8, 70, 12, TRUE);

-- =====================================================
-- SEED: Questions and Answers
-- =====================================================

INSERT INTO questions (quiz_id, question_text, question_type, points, display_order) VALUES
(1, 'What does HTML stand for?', 'multiple_choice', 1, 1),
(1, 'Which tag is used for the largest heading in HTML?', 'multiple_choice', 1, 2),
(1, 'What is CSS used for?', 'multiple_choice', 1, 3),
(1, 'JavaScript was originally called?', 'multiple_choice', 1, 4),
(1, 'Which browser engine powers Chrome?', 'multiple_choice', 1, 5);

INSERT INTO answers (question_id, answer_text, is_correct, display_order) VALUES
-- Question 1 answers
(1, 'HyperText Markup Language', TRUE, 1),
(1, 'Home Tool Markup Language', FALSE, 2),
(1, 'Hyperlinks and Text Markup Language', FALSE, 3),
(1, 'High-level Text Markup Language', FALSE, 4),
-- Question 2 answers
(2, '<h1>', TRUE, 1),
(2, '<h6>', FALSE, 2),
(2, '<h9>', FALSE, 3),
(2, '<heading>', FALSE, 4),
-- Question 3 answers
(3, 'To add styles to HTML elements', TRUE, 1),
(3, 'To structure web pages', FALSE, 2),
(3, 'To add interactivity', FALSE, 3),
(3, 'To create databases', FALSE, 4),
-- Question 4 answers
(4, 'LiveScript', TRUE, 1),
(4, 'ActiveScript', FALSE, 2),
(4, 'DynamicScript', FALSE, 3),
(4, 'InternetScript', FALSE, 4),
-- Question 5 answers
(5, 'V8', TRUE, 1),
(5, 'SpiderMonkey', FALSE, 2),
(5, 'JavaScriptCore', FALSE, 3),
(5, 'Gecko', FALSE, 4);

-- =====================================================
-- SEED: Enrollments
-- =====================================================

INSERT INTO enrollments (student_id, course_id, enrollment_status, progress_percentage, last_accessed) VALUES
(2, 1, 'active', 45.50, NOW()),
(2, 4, 'active', 20.00, NOW()),
(3, 1, 'completed', 100.00, NOW()),
(3, 5, 'active', 60.00, NOW()),
(4, 7, 'active', 30.00, NOW());

-- =====================================================
-- SEED: Progress
-- =====================================================

INSERT INTO progress (student_id, lesson_id, enrollment_id, is_completed, completion_percentage, time_spent_minutes) VALUES
(2, 1, 1, TRUE, 100.00, 15),
(2, 2, 1, TRUE, 100.00, 25),
(2, 3, 1, FALSE, 50.00, 15),
(3, 1, 3, TRUE, 100.00, 15),
(3, 2, 3, TRUE, 100.00, 25),
(3, 3, 3, TRUE, 100.00, 30),
(3, 4, 3, TRUE, 100.00, 40);

-- =====================================================
-- SEED: Quiz Results
-- =====================================================

INSERT INTO results (student_id, quiz_id, enrollment_id, score, total_points, percentage, status, time_taken_minutes, attempt_number) VALUES
(2, 1, 1, 4, 5, 80.00, 'passed', 8, 1),
(3, 1, 3, 5, 5, 100.00, 'passed', 6, 1),
(3, 2, 3, 8, 10, 80.00, 'passed', 12, 1);

-- =====================================================
-- SEED: Recommendations
-- =====================================================

INSERT INTO recommendations (student_id, course_id, recommendation_reason, recommendation_type, confidence_score) VALUES
(2, 2, 'Based on your video learning preference and progress', 'learning_style', 0.85),
(2, 3, 'Next course in Web Development path', 'category', 0.75),
(3, 3, 'Bootstrap is commonly used with PHP', 'category', 0.80),
(4, 7, 'Matches your UI/UX design interest', 'learning_style', 0.90);

-- =====================================================
-- SEED: Feedback
-- =====================================================

INSERT INTO feedback (student_id, course_id, feedback_type, rating, comment, status) VALUES
(2, 1, 'course', 4, 'Great course! Very informative and well-structured.', 'resolved'),
(3, 1, 'course', 5, 'Excellent! Highly recommend this course to beginners.', 'reviewed'),
(3, 4, 'course', 3, 'Good content but needs more practical exercises.', 'new');

-- =====================================================
-- SEED: Notifications
-- =====================================================

INSERT INTO notifications (user_id, title, message, notification_type, related_course_id, is_read) VALUES
(2, 'Course Started', 'You have started the Web Development Fundamentals course', 'course', 1, FALSE),
(2, 'Quiz Available', 'A new quiz is available for HTML5 Basics lesson', 'quiz', 1, FALSE),
(3, 'Course Completed', 'Congratulations! You completed Web Development Fundamentals', 'course', 1, TRUE),
(4, 'Course Recommendation', 'We recommend UI Design Principles based on your profile', 'recommendation', 7, FALSE);

-- =====================================================
-- SEED: Bookmarks
-- =====================================================

INSERT INTO bookmarks (student_id, lesson_id, bookmark_order) VALUES
(2, 2, 1),
(2, 4, 2),
(3, 5, 1),
(4, 1, 1);

-- =====================================================
-- SEED: Settings
-- =====================================================

INSERT INTO settings (user_id, theme, language, email_notifications, course_notifications, quiz_notifications) VALUES
(1, 'light', 'en', TRUE, TRUE, TRUE),
(2, 'dark', 'en', TRUE, TRUE, TRUE),
(3, 'light', 'en', TRUE, TRUE, FALSE),
(4, 'auto', 'en', FALSE, TRUE, TRUE);

-- =====================================================
-- SEED: Announcements
-- =====================================================

INSERT INTO announcements (admin_id, title, content, priority, target_audience, is_published, published_at) VALUES
(1, 'Welcome to Learning Assistant', 'Welcome to our new Personalized Learning Assistant platform!', 'high', 'all', TRUE, NOW()),
(1, 'New Courses Available', 'We have added new courses to our platform. Check them out!', 'medium', 'students', TRUE, NOW()),
(1, 'System Maintenance Scheduled', 'System maintenance will occur on Sunday night.', 'high', 'all', TRUE, NOW());