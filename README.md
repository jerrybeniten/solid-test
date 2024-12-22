# XML Sync System

This document provides detailed instructions on how to set up, configure, and use the Mini XML System. The system is designed to process XML files, extract data, and store it in a PostgreSQL database. Below, you'll find everything you need to get started, including Docker setup, SQL import instructions, and an explanation of how the system works.

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Setup Instructions](#setup-instructions)
    - [Starting Docker Containers](#starting-docker-containers)
    - [Importing SQL Files](#importing-sql-files)
3. [System Workflow](#system-workflow)
    - [XML File Processing](#xml-file-processing)
    - [Database Interaction](#database-interaction)
4. [Usage Instructions](#usage-instructions)
5. [Troubleshooting](#troubleshooting)

---

## System Requirements

Before starting, ensure you have the following:

- Docker and Docker Compose installed on your system.
- PostgreSQL installed as part of the Docker setup.
- XML files ready for processing.
- Access to the project directory containing:
  - `docker-compose.yml`
  - `src` folder with the system logic
  - `sql` folder containing the database schema and initial data.

---

## Setup Instructions

### Starting Docker Containers

1. Start the Docker containers using Docker Compose:

   ```bash
   docker-compose up -d
   ```

2. Verify that the containers are running:

   ```bash
   docker ps
   ```

   You should see containers for the application and PostgreSQL running.

### Importing SQL Files

1. Locate the SQL files in the `sql` directory. These files include:
   - `schema.sql`: Defines the database schema.

2. Access the PostgreSQL container:

   ```bash
   docker exec -it <postgres-container-name> bash
   ```

3. Connect to the PostgreSQL database:

   ```bash
   psql -U <username> -d <database_name>
   ```

4. Import the SQL files:

   ```bash
   \\ For schema.sql
   \i /path/to/sql/schema.sql

   \\ For data.sql (if needed)
   \i /path/to/sql/data.sql
   ```

5. Exit the PostgreSQL shell and the container:

   ```bash
   \q
   exit
   ```

---

## System Workflow

### XML File Processing

1. **Input**: The system scans a specified directory for XML files.
2. **Validation**: Each XML file is validated to ensure it conforms to the expected format. Invalid files are logged in the error log.
3. **Processing**: Valid XML files are parsed, and their data is extracted as per the system's logic.
4. **Archiving**: After processing, XML files are moved to a `processed` directory to avoid duplication.

### Database Interaction

1. Extracted data is mapped to the appropriate database tables.
2. Data is inserted into the PostgreSQL database using prepared statements to ensure security and prevent SQL injection.

---

## Usage Instructions

1. **Place XML Files**: Add your XML files to the input directory specified in the configuration (e.g., `/src/Xml/input/`).

2. **Run the Processing Script**: Execute the processing script to start reading and importing data:

   ```bash
   php /src/cron/sync_xml.php
   ```

3. **Monitor Logs**: Check the logs for any errors or validation issues. Logs are stored in the `logs` directory.

4. **Verify Data**: Ensure the data has been imported correctly by querying the database:

   ```bash
   docker exec -it <postgres-container-name> psql -U <username> -d <database_name>
   ```

---

## Troubleshooting

1. **Docker Issues**:
   - If containers fail to start, check the `docker-compose.yml` file and ensure all ports are available.
   - Restart the containers:
     ```bash
     docker-compose down && docker-compose up -d
     ```

2. **Database Issues**:
   - Verify that the database schema is loaded correctly by checking the tables.
   - Reimport the SQL files if needed.

3. **XML File Issues**:
   - Check the error log to identify problematic XML files.
   - Validate the XML structure manually or using an online tool.

4. **General Logs**:
   - Application logs are stored in `/logs` for debugging any runtime errors.

---

This README serves as a quick reference guide. For further details, consult the documentation or reach out to the development team.
