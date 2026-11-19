This is a Data Browser Project for CSCI 130
- it is suppose to be a portfolio in some sense and to have some data in the background
- Covers most if not all the stuff we went over in CSCI 130
- Will update on most of this later on, but will keep it simple for now

11/2/2026
- Skeleton Files of html, js, css, and md files

11/16/2026
- Added multiple files that being phps and json
- Reason for the multiple php files is an software engineering practice called "Separation of Concerns" 
- Easier to maintain, understand, and expand

With the helpful use of ChatGPT, I was able to conduct this in the README portion: 

# Marathon Data Browser

## Project Description

The Marathon Data Browser is a web-based application that allows users to view, search, sort, add, edit, and delete marathon records. The application uses JavaScript and PHP to communicate with a MySQL database, allowing marathon information to be stored and retrieved across sessions.

Each marathon record includes its name, location, year, completion status, race category, and an associated image.

## Features

- **View Marathon Records:** Display marathon information and associated images.
- **Navigation:** Browse records using First, Previous, Next, and Last buttons.
- **Search:** Find a marathon using its index number.
- **Sorting:** Sort marathon records by selected properties in ascending or descending order.
- **Original Order:** Restore records to their original database order.
- **Edit Mode:** Enable or disable editing functionality.
- **Update Records:** Modify existing marathon information.
- **Insert Records:** Add new marathon records to the database.
- **Delete Records:** Remove selected marathon records from the database.
- **Image Uploads:** Upload images when adding a marathon or replacing an existing image.
- **Data Persistence:** Save changes to MySQL so records remain available after refreshing the application.
- **Error Handling:** Provide feedback for invalid input, server errors, and network problems.

## Technologies Used

- HTML5
- JavaScript
- PHP
- MySQL
- XAMPP
- phpMyAdmin
- XMLHttpRequest (XHR)
- FormData for image uploads

## Project Structure

```text
dataBrowser_proj_130/
│
├── index.html
├── script.js
├── db_connect.php
├── fetch_data.php
├── insertMarathon.php
├── updateMarathon.php
├── delMarathon.php
│
└── uploads/
    └── marathon images
```

## Database Setup

The application uses a MySQL database named `marathon_db`.

The database contains a table named `marathons` with the following columns:

| Column | Data Type | Description |
|---|---|---|
| id | INT | Primary key with auto-increment |
| raceName | VARCHAR(255) | Marathon name |
| location | VARCHAR(255) | Marathon location |
| year | INT | Year of the marathon |
| completed | TINYINT(1) | Completion status (0 or 1) |
| raceType | VARCHAR(100) | Marathon category |
| image | VARCHAR(255) | Path to the marathon image |

The database connection settings are located in `db_connect.php`.

## How to Run the Application

1. Install and open XAMPP.
2. Start Apache and MySQL.
3. Place the project folder inside the XAMPP `htdocs` directory.
4. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
5. Import the `marathon_db` database SQL file if setting up the project on a new computer.
6. Verify the database connection settings in `db_connect.php`.
7. Open the application in a web browser:

   `http://localhost/dataBrowser_proj_130/`

## Image Uploads

Uploaded marathon images are stored in the `uploads/` directory on the server. The database stores the relative image path for each marathon record.

The application supports JPG, PNG, GIF, and WEBP image formats, with a maximum upload size of 5 MB.

## Author

Developed as part of CSCI 130.

## Project Status

**Completed**
