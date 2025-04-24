# Quiz Application

This repository contains a Quiz Application developed as part of a college project. The application is built using PHP and provides a platform for users to create, manage, and participate in quizzes.

## Features

- **User Authentication**: Users can register and log in to access the application.
- **Quiz Management**: Create and edit quizzes with multiple questions and options.
- **Result Tracking**: View past quiz attempts and scores.

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/pawankushwah/last-year-college-project.git
   cd last-year-college-project
   ```

2. **Set up the database**:
   - Create a MySQL database named `quiz_app`.
   - Import the SQL file (`database.sql`) provided in the repository to set up the necessary tables.

3. **Configure the application**:
   - Change `config/demo.config.php` file with `config/config.php`
   - Update the `config/config.php` file with your database credentials and base URL of your application.

4. **Run the application**:
   - Ensure your web server (e.g., Apache, Nginx) is running.
   - Access the application via `http://localhost`.

## Usage

- **Register/Login**: Create an account or log in to access features like quiz creation.
- **Create a Quiz**: Navigate to the 'Create Quiz' section to add new quizzes.
- **Take a Quiz**: Browse available on Home Page or Dashboard and select quizzes and attempt them.
- **View Results**: Check your scores and quiz history on the dashboard.

## Technologies Used

- **Backend**: PHP, MySQL
- **Frontend**: HTML, CSS, JavaScript

## May add these features

- **User Profile**: View and update profile information.
- **Question Types**: Supports multiple-choice questions with single correct answers.
- **Real-time Scoring**: Users receive immediate feedback on their quiz performance.
- **Responsive Design**: Accessible on various devices.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

## Acknowledgments

- This project was developed as part of the BCA (Bachelor of Computer Applications) program at Radiant College.