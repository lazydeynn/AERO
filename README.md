## ⚙️ Configuration (.env)

For security reasons, the configuration file containing sensitive data (like email, passwords, api key) is **not included** in this repository. You need to create this file manually to make the other features work.

**Steps to set up:**

1.  Create a new file named `.env` in the root folder of the project.
2.  Copy the following code into that file:

```ini
# MODIFY ONLY THE DB_NAME, GROQ_API_KEY, SMTP_EMAIL, AND SMTP_PASS!! 
GROQ_API_KEY=your_groq_api_key
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=your_db_name
SMTP_HOST=smtp.gmail.com
SMTP_EMAIL=your_email@gmail.com
SMTP_PASS=your_google_app_password
