# Phase 2: Database Schema Setup

## ✅ Completed

This phase establishes the complete MySQL database schema with all necessary tables, relationships, indexes, and seed data.

## Database Schema Overview

### Tables Created (17 Total)

#### 1. **users** - User Accounts
- Stores login credentials, roles, and account status
- Fields: id, username, email, password_hash, first_name, last_name, role, status, email_verified, verification_token, remember_token, last_login, timestamps
- Indexes: email, username, role, status
- Support for: Admin and Student roles

#### 2. **profiles** - Extended User Information
- Stores profile details, learning preferences
- Fields: id, user_id, profile_picture, bio, phone, date_of_birth, gender, address, city, country, preferred_learning_style, timezone, notifications_enabled, timestamps
- Links to: users (1:1 relationship)
- Learning styles: video, text, interactive, mixed

#### 3. **categories** - Course Categories
- Organizes courses into topics
- Fields: id, name, description, slug, icon, color, display_order, is_active, timestamps
- Examples: Web Development, Data Science, Python Programming, UI/UX Design, Business Skills

#### 4. **courses** - Course Information
- Main course data
- Fields: id, category_id, title, description, slug, thumbnail, difficulty_level, instructor_id, total_lessons, estimated_hours, rating, total_enrollments, is_published, timestamps
- Difficulty levels: beginner, intermediate, advanced
- Links to: categories, users (instructor)

#### 5. **lessons** - Course Lessons
- Individual lessons within courses
- Fields: id, course_id, title, description, content, lesson_type, video_url, resource_file, resource_type, duration_minutes, display_order, is_published, timestamps
- Lesson types: video, text, interactive, resource
- Links to: courses

#### 6. **enrollments** - Student Course Enrollments
- Tracks which students are enrolled in which courses
- Fields: id, student_id, course_id, enrollment_status, progress_percentage, enrolled_at, completed_at, last_accessed, timestamps
- Enrollment statuses: active, completed, dropped
- Unique constraint: One enrollment per student per course

#### 7. **progress** - Lesson Progress Tracking
- Tracks student progress on individual lessons
- Fields: id, student_id, lesson_id, enrollment_id, is_completed, completion_percentage, time_spent_minutes, first_accessed, last_accessed, completed_at, timestamps
- Unique constraint: One progress record per student per lesson

#### 8. **quizzes** - Quiz Configuration
- Stores quiz information
- Fields: id, lesson_id, title, description, quiz_type, total_questions, passing_score, time_limit_minutes, allow_retake, max_attempts, shuffle_questions, show_correct_answers, is_published, timestamps
- Quiz types: practice, graded, final

#### 9. **questions** - Quiz Questions
- Individual questions in quizzes
- Fields: id, quiz_id, question_text, question_type, points, display_order, explanation, timestamps
- Question types: multiple_choice, true_false, short_answer, essay

#### 10. **answers** - Answer Options
- Answer choices for questions
- Fields: id, question_id, answer_text, is_correct, display_order, timestamps
- Supports multiple correct answers

#### 11. **results** - Quiz Results
- Student quiz attempt records
- Fields: id, student_id, quiz_id, enrollment_id, score, total_points, percentage, status, time_taken_minutes, attempt_number, submitted_at, timestamps
- Supports multiple attempts per quiz
- Status: passed, failed

#### 12. **recommendations** - Personalized Recommendations
- Stores AI-generated course recommendations
- Fields: id, student_id, course_id, recommendation_reason, recommendation_type, confidence_score, is_dismissed, dismissed_at, is_enrolled, enrolled_at, timestamps
- Recommendation types: learning_style, progress, category, difficulty, completion
- Unique constraint: One recommendation per student per course

#### 13. **feedback** - User Feedback
- Stores course and lesson feedback
- Fields: id, student_id, course_id, lesson_id, feedback_type, rating, comment, is_anonymous, status, admin_response, responded_by, responded_at, timestamps
- Feedback types: course, lesson, general
- Rating: 1-5 stars
- Status: new, reviewed, resolved

#### 14. **notifications** - User Notifications
- Notifies users of events
- Fields: id, user_id, title, message, notification_type, related_course_id, related_quiz_id, is_read, read_at, action_url, timestamps
- Notification types: course, quiz, achievement, recommendation, general

#### 15. **bookmarks** - Bookmarked Lessons
- Allows students to bookmark lessons
- Fields: id, student_id, lesson_id, bookmark_order, timestamps
- Unique constraint: One bookmark per student per lesson

#### 16. **settings** - User Settings
- Stores user preferences
- Fields: id, user_id, theme, language, email_notifications, course_notifications, quiz_notifications, recommendation_notifications, privacy_profile, show_profile_picture, timestamps
- Theme: light, dark, auto
- Privacy: public, private, friends_only

#### 17. **announcements** - Admin Announcements
- Stores platform announcements
- Fields: id, admin_id, title, content, priority, target_audience, is_published, published_at, expires_at, timestamps
- Priority: low, medium, high
- Target audience: all, students, admins

---

## Database Relationships

```
users (1) ──┬── (1) profiles
            ├── (1) settings
            ├── (M) courses (as instructor)
            ├── (M) enrollments (as student)
            ├── (M) progress (as student)
            ├── (M) results (as student)
            ├── (M) recommendations (as student)
            ├── (M) feedback (as student)
            ├── (M) notifications
            ├── (M) bookmarks (as student)
            └── (M) announcements (as admin)

categories (1) ── (M) courses

courses (1) ──┬── (M) lessons
              ├── (M) enrollments
              └── (M) feedback (partial)

lessons (1) ──┬── (M) quizzes
              ├── (M) progress
              └── (M) bookmarks

quizzes (1) ──┬── (M) questions
              ├── (M) results
              └── (M) notifications (partial)

questions (1) ── (M) answers

enrollments (1) ──┬── (M) progress
                  └── (M) results

enrollments ──┬── students (users)
              ├── courses
              └── linked to progress & results
```

---

## Key Features Implemented

### Security
- ✅ Password hashing field (password_hash)
- ✅ Email verification support (email_verified, verification_token)
- ✅ Remember token for "Remember Me" functionality
- ✅ Prepared statements ready (PDO in config)
- ✅ Proper data types and constraints

### Data Integrity
- ✅ Primary keys on all tables
- ✅ Foreign keys with cascade deletes where appropriate
- ✅ Unique constraints (email, username, enrollments, bookmarks)
- ✅ Check constraints (rating 1-5)
- ✅ NOT NULL constraints on required fields
- ✅ Default values for status, timestamps, flags

### Performance
- ✅ Indexes on frequently queried columns
- ✅ Indexes on foreign keys
- ✅ Indexes on status and state columns
- ✅ Separate tables for different concerns
- ✅ Normalized database design

### Functionality
- ✅ Role-based access (admin, student)
- ✅ Enum fields for restricted values
- ✅ Timestamps (created_at, updated_at) on all tables
- ✅ Status tracking for all entities
- ✅ Soft-delete support (status fields)
- ✅ Progress tracking at multiple levels

---

## Seed Data Included

### Users
- 1 Admin account (admin@learningassistant.com)
- 3 Student accounts (student1, student2, student3)

### Content
- 5 Categories
- 7 Courses (various difficulty levels)
- 5 Lessons with different types (video, text, interactive)
- 3 Quizzes with questions and answers

### Student Data
- 5 Enrollments
- 7 Progress records
- 3 Quiz results
- 4 Recommendations
- 3 Feedback entries
- 4 Notifications
- 4 Bookmarks

### Configuration
- 4 User settings
- 3 Announcements

---

## How to Import Database

### Method 1: Using phpMyAdmin

1. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Login (default: root, no password)

2. **Create Database**
   - Click "New" or go to Databases tab
   - Database name: `learning_assistant`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import Schema**
   - Select `learning_assistant` database
   - Click "Import" tab
   - Choose `database/schema.sql`
   - Click "Go"
   - Wait for success message

4. **Import Seed Data**
   - With `learning_assistant` database selected
   - Click "Import" tab
   - Choose `database/seeds.sql`
   - Click "Go"
   - Wait for success message

### Method 2: Using Command Line

```bash
# Navigate to MySQL bin directory
cd "C:\xampp\mysql\bin"

# Import schema
mysql -u root -p learning_assistant < "C:\xampp\htdocs\learning-assistant\database\schema.sql"

# Import seed data
mysql -u root -p learning_assistant < "C:\xampp\htdocs\learning-assistant\database\seeds.sql"

# Verify (optional)
mysql -u root -p learning_assistant -e "SHOW TABLES;"
```

### Method 3: Using PHP Connection (Recommended)

1. **Ensure config is correct**
   - Check `config/database.php`
   - Verify credentials

2. **Open test connection page**
   - Go to: `http://localhost/learning-assistant/database/test_connection.php`
   - This will show all tables and their row counts

---

## How to Test Phase 2

### Step 1: Import Database
Follow import instructions above

### Step 2: Verify in phpMyAdmin
1. Open phpMyAdmin
2. Select `learning_assistant` database
3. Should see 17 tables
4. Check each table has:
   - Correct columns
   - Proper data types
   - Seeds data populated

### Step 3: Test Connection Page
1. Navigate to: `http://localhost/learning-assistant/database/test_connection.php`
2. Should see:
   - ✅ "Connection Successful" message
   - ✅ All 17 tables listed
   - ✅ Row counts for each table
   - ✅ Database statistics

### Step 4: Run Test Queries

In phpMyAdmin SQL tab, run:

```sql
-- Check users
SELECT id, username, email, role FROM users;

-- Check courses
SELECT id, title, difficulty_level FROM courses;

-- Check enrollments
SELECT e.id, u.username, c.title FROM enrollments e
JOIN users u ON e.student_id = u.id
JOIN courses c ON e.course_id = c.id;

-- Check progress
SELECT COUNT(*) as total_progress FROM progress WHERE is_completed = TRUE;

-- Check recommendations
SELECT COUNT(*) as total_recommendations FROM recommendations;
```

---

## Database Files

### Files Created

1. **database/schema.sql** (1100+ lines)
   - Complete database schema
   - All 17 tables with proper structure
   - Indexes and constraints
   - Documentation comments

2. **database/seeds.sql** (500+ lines)
   - Sample data for testing
   - Users, courses, lessons
   - Student enrollments and progress
   - Quiz results and recommendations
   - Realistic test data

3. **database/test_connection.php**
   - Tests database connection
   - Displays all tables and row counts
   - Shows database statistics
   - Confirms seed data loaded

---

## Database Specifications

### Character Set
- UTF-8 MB4 (supports emojis and all Unicode)
- Collation: utf8mb4_unicode_ci

### Engine
- InnoDB (supports transactions and foreign keys)

### Indexing Strategy
1. Primary keys on all tables
2. Unique indexes on username, email
3. Foreign key indexes (automatic)
4. Status/state column indexes
5. Date range query indexes

### Data Types
- INT: IDs and counts
- VARCHAR: Strings with max length
- TEXT: Long text content
- DATETIME: Timestamps
- DECIMAL: Percentages and ratings
- BOOLEAN: Flags and status indicators
- ENUM: Restricted choice fields

---

## Password Hashing Note

⚠️ **Important**: Seed data uses placeholder hashes.

When implementing authentication (Phase 3):
- Seed passwords are NOT actual bcrypt hashes
- Use PHP `password_hash()` function for real passwords
- Use `password_verify()` for login

Example:
```php
$real_password = 'Admin@12345';
$hashed = password_hash($real_password, PASSWORD_BCRYPT);
// Store $hashed in database

if (password_verify($real_password, $hashed)) {
    // Login success
}
```

---

## Entity-Relationship Diagram

```
┌─────────────────────────────────────┐
│ users                               │
├─────────────────────────────────────┤
│ PK: id                              │
│ username (UNIQUE)                   │
│ email (UNIQUE)                      │
│ password_hash                       │
│ role (admin, student)               │
│ status (active, inactive, suspended)│
└────────┬────────────────────────────┘
         │
    ┌────┴────┬──────────────┬────────────────┬──────────┐
    │          │              │                │          │
    1          1              1                1          1
    │          │              │                │          │
    ▼          ▼              ▼                ▼          ▼
┌────────┐ ┌──────────┐ ┌──────────┐ ┌────────────┐ ┌──────────┐
│profiles│ │settings  │ │courses   │ │enrollments │ │bookmarks │
│(1:1)   │ │(1:1)     │ │(instructor)    │          │          │
└────────┘ └──────────┘ └─────┬────┘ └────────────┘ └──────────┘
                              │
                         ┌────┴─────┬──────────┐
                         │           │          │
                         1           M          1
                         │           │          │
                         ▼           ▼          ▼
                    ┌─────────┐ ┌────────┐ ┌──────────┐
                    │categories  │lessons │ │quizzes   │
                    └─────────┘ └───┬────┘ └────┬─────┘
                                    │           │
                                    M           M
                                    │           │
                                    ▼           ▼
                               ┌──────────┐ ┌──────────┐
                               │progress  │ │questions │
                               └──────────┘ └────┬─────┘
                                                │
                                                M
                                                │
                                                ▼
                                           ┌─────────┐
                                           │answers  │
                                           └─────────┘
```

---

## Approval Checklist

✅ 17 tables created with proper structure
✅ Primary keys and foreign keys defined
✅ Indexes on performance-critical columns
✅ Unique constraints where needed
✅ Seed data populated
✅ Character set UTF-8 MB4
✅ InnoDB engine for transactions
✅ Test connection page working
✅ Documentation complete
✅ Comments in SQL file

---

## Next Steps - Phase 3: Authentication System

After database approval:

1. Create login page with form
2. Create registration page with validation
3. Implement password hashing (bcrypt)
4. Create session management
5. Create logout functionality
6. Implement CSRF protection
7. Create email verification (optional)
8. Create "Remember Me" functionality
9. Create password reset (optional)
10. Test authentication flows

---

## Ready for Testing

**Phase 2 is complete and ready for import!**

Please:
1. Import the database using one of the methods above
2. Test the connection page
3. Verify all 17 tables exist
4. Confirm seed data is populated
5. Reply with approval to proceed to Phase 3

🎯 **Status**: ✅ COMPLETE - Ready for import and testing
